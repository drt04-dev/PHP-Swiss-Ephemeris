<?php

declare(strict_types=1);

namespace Sweph\DTO;

/**
 * Repräsentiert die berechnete Position und Geschwindigkeit eines Himmelskörpers.
 */
class CelestialPosition
{
    /**
     * @param float $longitude Ekliptikale Länge (0° bis 360°, z.B. 0° Widder bis 359.9° Fische)
     * @param float $latitude Ekliptikale Breite (Abweichung von der Ekliptik nach Norden(+) oder Süden(-))
     * @param float $distance Entfernung zur Erde (oder zur Sonne bei heliozentrischer Berechnung) in Astronomischen Einheiten (AE)
     * @param float $longitudeSpeed Tägliche Bewegung in der Länge (Grad pro Tag). Negativ bedeutet Rückläufigkeit!
     * @param float $latitudeSpeed Tägliche Bewegung in der Breite (Grad pro Tag)
     * @param float $distanceSpeed Tägliche Änderung der Entfernung (AE pro Tag)
     */
    public function __construct(
        public private(set) float $longitude,
        public private(set) float $latitude,
        public private(set) float $distance,
        public private(set) float $longitudeSpeed,
        public private(set) float $latitudeSpeed,
        public private(set) float $distanceSpeed,
    ) {}

    /**
     * Hilfsmethode, um schnell zu prüfen, ob der Planet gerade rückläufig ist.
     */
    public function isRetrograde(): bool
    {
        return $this->longitudeSpeed < 0.0;
    }
}