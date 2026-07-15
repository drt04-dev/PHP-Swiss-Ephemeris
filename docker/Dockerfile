FROM php:8.4-cli-alpine

# 1. System-Abhängigkeiten und Build-Tools installieren
RUN apk add --no-cache \
    git \
    build-base \
    autoconf \
    bash \
    alpine-sdk

# 2. Arbeitsverzeichnis vorbereiten
WORKDIR /usr/src/php-sweph-ext

# 3. Lokalen ext/ Ordner kopieren
COPY ext/ .

# 4. "swephp_arginfo.h" generieren falls nötig
RUN if [ ! -f "swephp_arginfo.h" ] && [ -f "swephp.stub.php" ]; then \
        php /usr/local/lib/php/build/gen_stub.php swephp.stub.php; \
    fi

# 5. SCHRITT 1: Ausschließlich die Core-Dateien der Swiss Ephemeris bauen
RUN cd sweph/src \
    && cc -c -O2 -Wall -fPIC swedate.c swehouse.c swejpl.c swemmoon.c swemplan.c sweph.c swephlib.c swecl.c swehel.c \
    && ar r libswe.a swedate.o swehouse.o swejpl.o swemmoon.o swemplan.o sweph.o swephlib.o swecl.o swehel.o

# 6. SCHRITT 2: PHP-Extension initialisieren, konfigurieren und kompilieren
# Wichtig: Am Ende aktivieren wir das Modul "swephp"
RUN phpize \
    && ./configure --with-sweph=shared,sweph/src \
    && make \
    && make install \
    && docker-php-ext-enable swephp

# 7. Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. finalen App-Ordner vorbereiten
RUN mkdir -p /app /opt/sweph/ephe
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1