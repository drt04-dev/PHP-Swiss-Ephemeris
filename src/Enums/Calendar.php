<?php

declare(strict_types=1);

namespace Sweph\Enums;

/**
 * Kalendersysteme für die julianische Tagesberechnung.
 */
enum Calendar: int
{
    case JULIAN = 0;    // SE_JUL_CAL
    case GREGORIAN = 1; // SE_GREG_CAL
}