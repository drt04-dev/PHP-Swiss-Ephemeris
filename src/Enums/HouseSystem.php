<?php

declare(strict_types=1);

namespace Sweph\Enums;

/**
 * Unterstützte Häusersysteme der Swiss Ephemeris.
 */
enum HouseSystem: string
{
    case PLACIDUS = 'P';
    case KOCH = 'K';
    case PORPHYRIUS = 'O';
    case REGIOMONTANUS = 'R';
    case CAMPANUS = 'C';
    case EQUAL = 'E';            // Equal (Aszendent ist Spitze Haus 1)
    case VEHLOw = 'V';           // Vehlow Equal
    case WHOLE_SIGN = 'W';       // Ganzzeichenhäuser (Whole Sign)
    case MERIDIAN = 'X';         // Meridian-System (Axial)
    case MORINUS = 'M';
    case TOPOCENTRIC = 'T';
}