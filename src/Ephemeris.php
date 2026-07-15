<?php

declare(strict_types=1);

namespace Sweph;

use Sweph\Enums\Planet;
use Sweph\Enums\CalculationFlag;
use Sweph\Enums\Calendar;
use Sweph\Enums\HouseSystem;
use Sweph\DTO\CelestialPosition;
use Sweph\DTO\HouseCalculation;

/**
 * Die Hauptklasse des SDKs.
 * Kapselt die C-Extension "swephp" in ein modernes, objektorientiertes PHP-Interface.
 */
class Ephemeris
{
    private static bool $initialized = false;

    /**
     * Setzt den Pfad zu den Ephemeriden-Dateien (.se1)
     *
     * @param string|null $path Standardmäßig das vom Downloader genutzte Verzeichnis
     */
    public static function setEphePath(?string $path = null): void
    {
        if (!extension_loaded('swephp')) {
            throw new EphemerisException("Die C-Erweiterung 'swephp' ist nicht geladen.");
        }

        if ($path === null) {
            $path = dirname(__DIR__) . '/ephe';
        }

        // Mit führendem Backslash im globalen Namensraum aufrufen!
        \swe_set_ephe_path($path);
        self::$initialized = true;
    }

 /**
     * Berechnet die astronomische Position eines Himmelskörpers.
     *
     * @param float $julianDay Der Julianische Tag (Zeitpunkt der Berechnung)
     * @param Planet $planet Der zu berechnende Himmelskörper (Enum)
     * @param array<CalculationFlag> $flags Berechnungsoptionen (z.B. Geozentrisch, Tropisch)
     * @return CelestialPosition Das strukturierte Datenobjekt mit den Ergebnissen
     */
    public static function calculatePlanetPosition(
        float $julianDay,
        Planet $planet,
        array $flags = []
    ): CelestialPosition {

        if (!self::$initialized) {
            self::setEphePath();
        }

        // Falls keine Flags übergeben wurden, nutzen wir standardmäßig die Geschwindigkeitsberechnung!
        if (empty($flags)) {
            $flags = [CalculationFlag::Speed];
        }

        // Bitmaske aus den übergebenen Enums erstellen
        $bitmask = 0;
        foreach ($flags as $flag) {
            $bitmask |= $flag->value;
        }

        // Mit führendem Backslash im globalen Namensraum aufrufen!
        $result = \swe_calc($julianDay, $planet->value, $bitmask);

        if (!is_array($result)) {
            throw new EphemerisException("Fehler beim Aufruf von swe_calc: Ungültiger Rückgabetyp.");
        }

        $rc = $result['rc'] ?? 0;
        $serr = $result['serr'] ?? '';

        if ($rc < 0 || !empty($serr)) {
            throw new EphemerisException(
                sprintf("Fehler bei der Berechnung für %s am Tag %f: %s", $planet->name, $julianDay, $serr)
            );
        }

        return new CelestialPosition(
            longitude: $result[0] ?? 0.0,
            latitude: $result[1] ?? 0.0,
            distance: $result[2] ?? 0.0,
            longitudeSpeed: $result[3] ?? 0.0,
            latitudeSpeed: $result[4] ?? 0.0,
            distanceSpeed: $result[5] ?? 0.0
        );
    }

    /**
     * Alias für calculatePlanetPosition, der ein DateTimeInterface entgegennimmt
     * und es automatisch in den Julianischen Tag für die Berechnung umwandelt.
     */
    public static function getPlanetPosition(
        Planet $planet,
        \DateTimeInterface $dateTime,
        array $flags = []
    ): CelestialPosition {
        $hour = (int)$dateTime->format('H');
        $minute = (int)$dateTime->format('i');
        $second = (int)$dateTime->format('s');
        $decimalHourUtc = $hour + ($minute / 60.0) + ($second / 3600.0);

        $julianDay = self::getJulianDay(
            (int)$dateTime->format('Y'),
            (int)$dateTime->format('m'),
            (int)$dateTime->format('d'),
            $decimalHourUtc
        );

        // Wir leiten die Flags einfach weiter (wird in calculatePlanetPosition auf Standard Speed gemappt)
        return self::calculatePlanetPosition($julianDay, $planet, $flags);
    }

    /**
     * Hilfsmethode zur Umrechnung eines Datums in einen Julianischen Tag.
     *
     * @param int $year Das Jahr
     * @param int $month Der Monat
     * @param int $day Der Tag
     * @param float $hourUtc Die Dezimalstunde in UTC (z.B. 12.5 für 12:30 Uhr)
     * @param Calendar $calendar Das Kalendersystem (Standard: Gregorianisch)
     */
    public static function getJulianDay(
        int $year,
        int $month,
        int $day,
        float $hourUtc,
        Calendar $calendar = Calendar::GREGORIAN
    ): float {
        return \swe_julday($year, $month, $day, $hourUtc, $calendar->value);
    }


    /**
     * Berechnet die Häuserspitzen und astrologischen Achsen.
     *
     * @param float $julianDay Der Julianische Tag (Zeitpunkt der Berechnung)
     * @param float $latitude Die geografische Breite (z.B. 52.5200 für Berlin)
     * @param float $longitude Die geografische Länge (z.B. 13.4050 für Berlin)
     * @param HouseSystem $system Das zu verwendende Häusersystem (Standard: Placidus)
     * @return HouseCalculation Das strukturierte und validierte Datenobjekt
     */
    public static function calculateHouses(
        float $julianDay,
        float $latitude,
        float $longitude,
        HouseSystem $system = HouseSystem::PLACIDUS
    ): HouseCalculation {
        if (!self::$initialized) {
            self::setEphePath();
        }

        $cusps = [];
        $ascmc = [];

        // hsys erwartet den ASCII-Wert des Systems (als Integer-Wert des ersten Chars)
        $hsysChar = ord($system->value);

        // Native C-Funktion aufrufen:
        $result = \swe_houses($julianDay, $latitude, $longitude, $hsysChar, $cusps, $ascmc);

        if ($result < 0) {
            throw new EphemerisException("Fehler bei der Berechnung der Häuserspitzen.");
        }

        // Wir bereinigen das cusps-Array, sodass wir ein sauberes 1-12 indiziertes Array erhalten
        $formattedCusps = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedCusps[$i] = $cusps[$i] ?? 0.0;
        }

        // Rückgabe unseres brandneuen DTOs
        return new HouseCalculation(
            cusps: $formattedCusps,
            ascendant: $ascmc[0] ?? 0.0,
            mc: $ascmc[1] ?? 0.0,
            armc: $ascmc[2] ?? 0.0,
            vertex: $ascmc[3] ?? 0.0
        );
    }

    /**
     * Aktiviert das Ayanamsa für siderische Berechnungen.
     * * @param int $sidMode Der siderische Modus (z.B. 0 für Lahiri, siehe swisseph-Doku)
     */
    public static function setSiderealMode(int $sidMode = 0): void
    {
        if (!self::$initialized) {
            self::setEphePath();
        }

        // Native C-Funktion aufrufen
        \swe_set_sid_mode($sidMode, 0.0, 0.0);
    }

    /**
     * Berechnet den aktuellen Ayanamsa-Wert (die Verschiebung) für einen Julianischen Tag.
     */
    public static function getAyanamsa(float $julianDay): float
    {
        if (!self::$initialized) {
            self::setEphePath();
        }

        return \swe_get_ayanamsa($julianDay);
    }

    /**
     * Schließt alle geöffneten Ephemeriden-Dateien und gibt Speicher frei.
     */
    public static function close(): void
    {
        \swe_close();
        self::$initialized = false;
    }
}