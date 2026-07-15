<?php

declare(strict_types=1);

namespace Sweph\DTO;

/**
 * Repräsentiert das Ergebnis einer Häuserberechnung.
 */
class HouseCalculation
{
    /**
     * @param array<int, float> $cusps Die Spitzen der 12 Häuser. Key 1 entspricht Haus 1, Key 12 entspricht Haus 12.
     * @param float $ascendant Der Aszendent (Schnittpunkt des Horizonts mit der Ekliptik im Osten)
     * @param float $mc Das Medium Coeli (Himmelsmitte)
     * @param float $armc Die Rektaszension des Medium Coeli (in Grad)
     * @param float $vertex Der Vertex (Schnittpunkt des Ersten Vertikals mit der Ekliptik im Westen)
     */
    public function __construct(
        public private(set) array $cusps,
        public private(set) float $ascendant,
        public private(set) float $mc,
        public private(set) float $armc,
        public private(set) float $vertex,
    ) {
        // Validierung, dass wir exakt 12 Häuserspitzen haben
        if (count($this->cusps) !== 12) {
            throw new \InvalidArgumentException('Das Cusps-Array muss exakt 12 Einträge (Häuserspitzen 1 bis 12) enthalten.');
        }
    }

    /**
     * Holt die Position einer bestimmten Häuserspitze (1 bis 12).
     */
    public function getCusp(int $houseNumber): float
    {
        if ($houseNumber < 1 || $houseNumber > 12) {
            throw new \OutOfRangeException('Häusernummer muss zwischen 1 und 12 liegen.');
        }

        return $this->cusps[$houseNumber];
    }
}