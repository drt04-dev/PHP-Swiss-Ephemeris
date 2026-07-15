<?php

declare(strict_types=1);

namespace Sweph\Enums;

enum Aspect: int
{
    case Conjunction = 0;  // 0°
    case Sextile = 60;     // 60°
    case Square = 90;      // 90°
    case Trine = 120;      // 120°
    case Opposition = 180; // 180°

    /**
     * Gibt den Standard-Orbis (Toleranz) in Grad für den Aspekt zurück.
     */
    public function getDefaultOrbis(): float
    {
        return match($this) {
            self::Conjunction, self::Opposition => 8.0,
            self::Square, self::Trine => 7.0,
            self::Sextile => 5.0,
        };
    }
}