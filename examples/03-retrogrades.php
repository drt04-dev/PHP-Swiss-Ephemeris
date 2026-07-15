<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Sweph\Ephemeris;
use Sweph\Enums\Planet;

$ephemeris = new Ephemeris();
$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

echo "=== Rückläufigkeits-Check (UTC: {$now->format('Y-m-d H:i:s')}) ===\n\n";

$planetsToCheck = [
    Planet::MERCURY,
    Planet::VENUS,
    Planet::MARS,
    Planet::JUPITER,
    Planet::SATURN,
    Planet::URANUS,
    Planet::NEPTUNE,
    Planet::PLUTO
];

foreach ($planetsToCheck as $planet) {
    try {
        $position = $ephemeris->getPlanetPosition($planet, $now);

        $status = $position->isRetrograde()
            ? "🔴 RÜCKLÄUFIG (Geschwindigkeit: " . round($position->longitudeSpeed, 4) . "°/Tag)"
            : "🟢 Direktläufig (Geschwindigkeit: +" . round($position->longitudeSpeed, 4) . "°/Tag)";

        printf("%-10s : %s\n", $planet->name, $status);
    } catch (\Sweph\EphemerisException $e) {
        echo "Fehler bei {$planet->name}: {$e->getMessage()}\n";
    }
}