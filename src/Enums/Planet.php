<?php

declare(strict_types=1);

namespace Sweph\Enums;

/**
 * Repräsentiert die Himmelskörper und sensitiven Punkte der Swiss Ephemeris.
 */
enum Planet: int
{
    case SUN = 0;             // SE_SUN
    case MOON = 1;            // SE_MOON
    case MERCURY = 2;         // SE_MERCURY
    case VENUS = 3;           // SE_VENUS
    case MARS = 4;            // SE_MARS
    case JUPITER = 5;         // SE_JUPITER
    case SATURN = 6;          // SE_SATURN
    case URANUS = 7;          // SE_URANUS
    case NEPTUNE = 8;         // SE_NEPTUNE
    case PLUTO = 9;           // SE_PLUTO

    // Mondknoten & Apogäum / Lilith
    case MEAN_NODE = 10;      // Mittlerer Mondknoten (SE_MEAN_NODE)
    case TRUE_NODE = 11;      // Wahrer Mondknoten (SE_TRUE_NODE)
    case MEAN_APOG = 12;      // Lilith / Mittleres Apogäum (SE_MEAN_APOG)
    case OSCU_APOG = 13;      // Oszillierende Lilith (SE_OSCU_APOG)

    // Himmelskörper & Asteroiden
    case EARTH = 14;          // Erde (SE_EARTH)
    case CHIRON = 15;         // Chiron (SE_CHIRON)
    case PHOLUS = 16;         // Pholus (SE_PHOLUS)
    case CERES = 17;          // Ceres (SE_CERES)
    case PALLAS = 18;         // Pallas (SE_PALLAS)
    case JUNO = 19;           // Juno (SE_JUNO)
    case VESTA = 20;          // Vesta (SE_VESTA)

    // Interpolierte Apsiden-Punkte (Natürliche Lilith & Priapus)
    case INTP_APOG = 21;      // Interpoliertes Apogäum / Natürliche Lilith (SE_INTP_APOG)
    case INTP_PERG = 22;      // Interpoliertes Perigäum / Priapus (SE_INTP_PERG)
}