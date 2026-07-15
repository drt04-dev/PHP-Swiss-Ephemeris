<?php

declare(strict_types=1);

// Sicherstellen, dass der Autoloader existiert
require_once __DIR__ . '/../vendor/autoload.php';

use Sweph\Ephemeris;
use Sweph\Enums\Planet;

// Initialisierung (Optionaler Pfad zu deinen se1-Dateien im System)
$ephemeris = new Ephemeris('/opt/sweph/ephe');

// Aktueller Zeitpunkt (UTC)
$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

echo "=== Aktuelle Planetenpositionen ({$now->format('Y-m-d H:i:s')} UTC) ===\n\n";

try {
    foreach ([Planet::SUN, Planet::MOON, Planet::MERCURY, Planet::VENUS, Planet::MARS] as $planet) {
        $position = $ephemeris->getPlanetPosition($planet, $now);

        printf(
            "%-10s | Länge: %6.2f° | Breite: %5.2f° | Distanz: %6.4f AE\n",
            $planet->name,
            $position->longitude,
            $position->latitude,
            $position->distance
        );
    }
} catch (\Sweph\EphemerisException $e) {
    echo "Fehler bei der Berechnung: " . $e->getMessage() . "\n";
}