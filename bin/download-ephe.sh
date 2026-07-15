#!/bin/bash

# Fehler sofort melden und Skript abbrechen, falls etwas schiefgeht
set -e

# Zielverzeichnis definieren
EPHE_DIR="./ephe"

echo "========================================================="
echo "  Swiss Ephemeris - Daten-Downloader (Official GitHub)"
echo "========================================================="
echo "Zielverzeichnis: $EPHE_DIR"

# Erstelle das Verzeichnis, falls es noch nicht existiert
mkdir -p "$EPHE_DIR"

# Die 3 Kern-Ephemeridendateien (Sonne/Mond/Hauptplaneten für 1800-2400 AD)
FILES=(
  "seas_18.se1"
  "sepl_18.se1"
  "semo_18.se1"
)

# Download-Schleife über das offizielle Astrodienst-Repository (aloistr/swisseph)
for file in "${FILES[@]}"; do
  TARGET_FILE="$EPHE_DIR/$file"

  if [ -f "$TARGET_FILE" ]; then
    echo " -> [OK] $file existiert bereits. Überspringe..."
  else
    echo " -> [LADE HERUNTER] $file ..."
    # Direkter Download der originalen Binärdaten aus dem master-Zweig
    curl -L -f -o "$TARGET_FILE" "https://raw.githubusercontent.com/aloistr/swisseph/master/ephe/$file"
  fi
done

echo "========================================================="
echo " Download erfolgreich abgeschlossen!"
echo " Die Dateien wurden in '$EPHE_DIR' hinterlegt."
echo "========================================================="