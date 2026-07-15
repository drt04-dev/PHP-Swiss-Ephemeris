<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Sweph\Ephemeris;
use Sweph\Enums\Planet;
use Sweph\Enums\HouseSystem;

$ephemeris = new Ephemeris();

// Geburtsdaten: 15. Juli 2026, 12:30 Uhr in Berlin
$birthTime = new DateTimeImmutable('2026-07-15 12:30:00', new DateTimeZone('Europe/Berlin'));

// Geografische Koordinaten für Berlin
$latitude = 52.5200;
$longitude = 13.4050;

echo "=== Radix-Berechnung für Berlin ===\n";
echo "Datum/Zeit: " . $birthTime->format('d.m.Y H:i:s (T)') . "\n\n";

try {
    // 1. Planeten berechnen
    $sun = $ephemeris->getPlanetPosition(Planet::SUN, $birthTime);
    $moon = $ephemeris->getPlanetPosition(Planet::MOON, $birthTime);

    // Hilfsfunktion zur Umrechnung von Grad (0-360) in das Tierkreiszeichen
    $getZodiacSign = function(float $longitude): string {
        $signs = [
            'Widder', 'Stier', 'Zwillinge', 'Krebs', 'Löwe', 'Jungfrau',
            'Waage', 'Skorpion', 'Schütze', 'Steinbock', 'Wassermann', 'Fische'
        ];
        $signIndex = (int)($longitude / 30);
        $degreeInSign = $longitude - ($signIndex * 30);
        return sprintf("%02d° %s", $degreeInSign, $signs[$signIndex]);
    };

    echo "--- Planeten im Tierkreis ---\n";
    echo "Sonne: " . $getZodiacSign($sun->longitude) . "\n";
    echo "Mond:  " . $getZodiacSign($moon->longitude) . "\n\n";

    // Note: Sobald die Häuser-Methode in der Ephemeris-Klasse implementiert ist,
    // kann das DTO HouseCalculation wie folgt genutzt werden:
    /*
    $houses = $ephemeris->getHouses($birthTime, $latitude, $longitude, HouseSystem::PLACIDUS);
    echo "--- Achsen & Häuser ---\n";
    echo "Aszendent (AC): " . $getZodiacSign($houses->ascendant) . "\n";
    echo "Medium Coeli (MC): " . $getZodiacSign($houses->mc) . "\n";
    echo "Haus 1 (Spitze):  " . $getZodiacSign($houses->getCusp(1)) . "\n";
    */

} catch (\Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
}