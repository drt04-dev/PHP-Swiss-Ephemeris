# PHP Swiss Ephemeris SDK (Modern OOP)

[![Run Tests](https://github.com/drt04-dev/PHP-Swiss-Ephemeris/actions/workflows/tests.yml/badge.svg)](https://github.com/drt04-dev/PHP-Swiss-Ephemeris/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.4-8892BF.svg)](https://php.net)

Ein moderner, objektorientierter PHP 8.4+ Wrapper für die Hochpräzisions-Berechnungen der **Swiss Ephemeris** (astronomische und astrologische Berechnungen). 

Dieses Paket wirft alten Ballast ab und nutzt konsequent Features wie **Enums**, **Asymmetric Visibility** (`public private(set)`) und strikte Typisierung.

---

## Voraussetzungen

* **PHP 8.4** oder neuer
* Die installierte C-Erweiterung [swephp](https://github.com/aloistr/swisseph) (im PHP-Core geladen als `ext-swephp`)

---

## Installation

Installiere das Paket unkompliziert via Composer:

```bash
composer require drt04-dev/php-swiss-ephemeris
```

## C-Erweiterungen kompilieren im docker
Um die C-Erweiterung nicht manuell kompilieren zu müssen, kannst du die fertige Docker-Umgebung nutzen:
```bash
# Container bauen und starten
docker compose up -d --build

# Abhängigkeiten installieren
docker compose exec app composer install

# Tests ausführen
docker compose exec app ./vendor/bin/phpunit
```

# Wie man die Beispiele ausführt

Wenn deine Docker-Umgebung läuft, können Sie 
diese Beispiele sofort im Terminal ausführen, ohne eine eigene 
PHP-Datei schreiben zu müssen:

```bash
# Beispiel 1 ausführen
docker compose exec app php examples/01-planet-positions.php

# Beispiel 3 ausführen
docker compose exec app php examples/03-retrogrades.php
```

# Verwendung

```php
use Sweph\Ephemeris;
use Sweph\Enums\Planet;
use Sweph\Enums\CalculationFlag;
use Sweph\Enums\HouseSystem;

// 1. Planetenposition berechnen (liefert ein schreibgeschütztes DTO)
$date = new DateTimeImmutable('2026-07-15 12:00:00', new DateTimeZone('UTC'));
$position = Ephemeris::getPlanetPosition(Planet::SUN, $date, [CalculationFlag::Speed]);

echo "Sonnenposition: {$position->longitude}°\n";
echo "Tägliche Geschwindigkeit: {$position->longitudeSpeed}°/Tag\n";
if ($position->isRetrograde()) {
    echo "Die Sonne läuft rückwärts? (Astronomisch unmöglich, aber das SDK merkt es!)\n";
}

// 2. Häuser und Achsen berechnen
$julianDay = Ephemeris::getJulianDay(2026, 7, 15, 12.0);
$houses = Ephemeris::calculateHouses($julianDay, 52.5200, 13.4050, HouseSystem::PLACIDUS);

echo "Aszendent: {$houses->ascendant}°\n";
echo "Spitze Haus 10 (MC): {$houses->mc}°\n";
echo "Spitze Haus 1: {$houses->getCusp(1)}°\n";
```

# C-Erweiterung & Entwicklung im Docker

Um die C-Erweiterung nicht manuell auf deinem lokalen System 
kompilieren zu müssen, kannst du die fertige Docker-Umgebung nutzen. 
Sie bringt PHP 8.4, die fertig einkompilierte Extension **swephp** 
und alle Werkzeuge mit.

# Schritt 1: Docker-Container bauen und starten
Öffne dein Terminal im Hauptverzeichnis deines geklonten Repositorys und führe folgenden Befehl aus:

```bash
docker compose up -d --build
```

Dieser Befehl baut das Docker-Image (kompiliert die C-Erweiterung für PHP 8.4) und startet den Container im Hintergrund.

# Schritt 2: Composer-Abhängigkeiten installieren
Da wir das Repository-Verzeichnis in den Container spiegeln (via Volume), installieren wir nun PHPUnit und andere Entwicklungswerkzeuge direkt im Container:

```bash
docker compose exec app composer install
```

# Schritt 3: Ephemeriden-Dateien (.se1) herunterladen
Damit die Berechnungen hochpräzise sind, müssen die Ephemeriden-Dateien im **ephe/**-Ordner liegen. 
Mache das Skript einmalig ausführbar und starte den Download:

```bash
# Skript ausführbar machen (nur einmalig nötig)
chmod +x bin/download-ephe.sh

# Download im Docker-Container anstoßen
docker compose exec app ./bin/download-ephe.sh
```

# Schritt 4: Die automatisierten Unit-Tests ausführen
Jetzt kannst du die Testsuite starten. PHPUnit sucht automatisch nach der Konfiguration und führt unseren Test tests/EphemerisTest.php aus:

```bash
docker compose exec app ./vendor/bin/phpunit
```

# Beispiele ausführen: Weitere Examples Unit-Tests
Wenn deine Docker-Umgebung läuft, kannst du die fertigen 
Beispiele im **examples/**-Ordner sofort im Terminal ausführen:

```bash
docker compose exec app php examples/01-planet-positions.php
```

```bash
docker compose exec app php examples/02-birth-chart.php
```

```bash
docker compose exec app php examples/03-retrogrades.php
```


## Repository-Struktur

Das Repository ist in die native C-Erweiterung (`ext/`) und den modernen, 
objektorientierten PHP 8.4 Wrapper (`src/`) unterteilt. 
Das sorgt für maximale Wartbarkeit und Übersichtlichkeit:

```text
drt04-dev/php-swiss-ephemeris/
├── .github/
│   └── workflows/
│       └── tests.yml          # GitHub Action für automatisierte CI/CD-Tests
├── bin/
│   └── download-ephe.sh       # Hilfsskript zum automatischen Laden der Ephemeridendateien
├── docker/
│   └── Dockerfile             # PHP 8.4 Entwicklungs- und Test-Image mit `swephp` Extension
├── ext/                       # Die ursprüngliche C-Erweiterung (swephp)
│   ├── config.m4
│   ├── config.w32
│   ├── php_swephp.h
│   └── swephp.c
├── src/                       # Der moderne PHP 8.4+ OOP-Wrapper (Namespace: Sweph)
│   ├── DTO/                   # Schreibgeschützte, unveränderliche Datenobjekte (Asymmetric Visibility)
│   │   ├── CelestialPosition.php
│   │   └── HouseCalculation.php
│   ├── Enums/                 # Typensichere Enums statt loser Integer- oder String-Konstanten
│   │   ├── Calendar.php
│   │   ├── CalculationFlag.php
│   │   ├── HouseSystem.php
│   │   └── Planet.php
│   │   └── Aspect.php
│   ├── Services/
│   │   ├── AspectFinder.php
│   ├── Ephemeris.php          # Die Hauptklasse (Kapselung der C-Extension-Funktionen)
│   └── EphemerisException.php # Spezifische Exception-Klasse für Berechnungsfehler
├── tests/                     # Automatisierte Qualitätssicherung (PHPUnit 11)
│   ├── bootstrap.php          # Test-Bootstrapper und Autoloading-Konfiguration
│   └── EphemerisTest.php      # Integrationstests für Planeten und Häuser
├── examples/                  # Direkt lauffähige Anwendungsbeispiele
│   ├── 01-planet-positions.php# Basis-Planetenberechnung
│   ├── 02-birth-chart.php     # Astrologisches Geburtschart-Beispiel (Zeichen & Häuser)
│   └── 03-retrogrades.php     # Automatische Erkennung rückläufiger Planeten
├── .gitignore                 # Ignoriert vendor, cache, IDE-Dateien und die großen .se1-Daten
├── composer.json              # Paketdefinition, PHP >= 8.4 Einschränkung & PSR-4 Autoloading
├── docker-compose.yml         # Lokales Docker-Setup für Entwicklung
├── LICENSE                    # MIT Lizenz
└── README.md                  # Diese Dokumentation
```


# Erweiterte Information

## Integration in Symfony 8.1+

Da unser SDK komplett objektorientiert und typsicher aufgebaut ist, lässt es sich nahtlos als Service in moderne Symfony-Projekte integrieren.

### 1. SDK als Service registrieren

Um den Ephemeriden-Pfad beim Bootstrapping der Symfony-Anwendung automatisch zu konfigurieren, registrieren wir die `Ephemeris` im Dependency Injection Container. 

Füge folgende Konfiguration in deine `config/services.yaml` ein:

```yaml
services:
    # ... standardmäßige Konfigurationen ...

    # Das SDK als Service registrieren und Pfad automatisch setzen
    Sweph\Ephemeris:
        public: false
        calls:
            # Setzt das Ephemeriden-Verzeichnis (z.B. im Symfony-Projekt unter %kernel.project_dir%/var/ephe)
            - [setEphePath, ['%kernel.project_dir%/var/ephe']]
```
### 2. Nutzung im Controller (Autowiring)
Jetzt kannst du die **Ephemeris** ganz einfach per Autowiring in jeden Controller, 
Command oder Service injizieren lassen:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Sweph\Ephemeris;
use Sweph\Enums\Planet;
use DateTimeImmutable;

class AstrologyController extends AbstractController
{
    #[Route('/api/planet/{name}', name: 'app_planet_position', methods: ['GET'])]
    public function getPosition(string $name, Ephemeris$ephemeris): JsonResponse
    {
        // Planet aus Pfad-Parameter mappen
        $planetEnum = match (strtolower($name)) {
            'sun' => Planet::SUN,
            'moon' => Planet::MOON,
            'mars' => Planet::MARS,
            default => null,
        };

        if ($planetEnum === null) {
            return $this->json(['error' => 'Planet nicht unterstützt'], 400);
        }

        // Berechnung durchführen
        $position = $ephemeris->getPlanetPosition($planetEnum, new DateTimeImmutable('now'));

        return $this->json([
            'planet' => $planetEnum->name,
            'longitude' => $position->longitude,
            'latitude' => $position->latitude,
            'is_retrograde' => $position->isRetrograde(),
        ]);
    }
}
```

### 3. CLI Command erstellen (Symfony Console)
In Symfony 8.1 kannst du dank der neuen Method-Based Commands oder 
klassischen Commands extrem schnell CLI-Skripte für Berechnungen schreiben:

```php
<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Sweph\Ephemeris;
use Sweph\Enums\Planet;
use DateTimeImmutable;

#[AsCommand(
    name: 'app:calculate-sun',
    description: 'Berechnet die aktuelle Position der Sonne.',
)]
class CalculateSunCommand extends Command
{
    public function __construct(
        private readonly Ephemeris $ephemeris
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface$output): int
    {
        $io = new SymfonyStyle($input,$output);
        
        $position =$this->ephemeris->getPlanetPosition(Planet::SUN, new DateTimeImmutable('now'));
        
        $io->success(sprintf('Die Sonne steht aktuell auf \%.2f° im Tierkreis.',$position->longitude));
        
        return Command::SUCCESS;
    }
}
```