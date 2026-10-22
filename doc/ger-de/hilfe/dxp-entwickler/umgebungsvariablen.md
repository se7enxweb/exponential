---
title: Umgebungsvariablen
audience: dxp-developer
category: dxp-development
order: 9
summary: Jede Umgebungsvariable, die die Anleitung des DXP-Skeletons dokumentiert - Anwendung, Datenbank (MySQL, PostgreSQL, SQLite), JWT, Suchmaschine, HTTP-Cache, Anwendungs-Cache, E-Mail, Sessions und CORS - und die Regeln, mit denen Geheimnisse aus Git herausbleiben.
keywords: .env, .env.local, Umgebungsvariablen, APP_ENV, APP_SECRET, DATABASE_URL, JWT_PASSPHRASE, SEARCH_ENGINE, HTTPCACHE_PURGE_TYPE, CACHE_POOL, MAILER_DSN, MESSENGER_TRANSPORT_DSN, Geheimnisse, environment variables, secrets
type: help
---
# Umgebungsvariablen

Diese Seite richtet sich an Entwickler, die ein DXP-Projekt konfigurieren. Ein Symfony-Projekt liest seine Einstellungen
aus Umgebungsvariablen; das Skeleton hält Standardwerte in `.env` und die Werte Ihres Hosts in `.env.local`. Alle
Variablen unten sind die, die die Anleitung des Skeletons (Abschnitt 4) dokumentiert.

## Inhalt

1. [Die Dateien und die Regel](#die-dateien-und-die-regel)
2. [Anwendung](#anwendung)
3. [Datenbank](#datenbank)
4. [JWT](#jwt)
5. [Suche, Cache, E-Mail und der Rest](#suche-cache-e-mail-und-der-rest)
6. [Eine Referenz ohne Geheimnisse aufbewahren](#eine-referenz-ohne-geheimnisse-aufbewahren)
7. [Prüfen, was Symfony sieht](#prüfen-was-symfony-sieht)

## Die Dateien und die Regel

| Datei | Committet | Enthält |
|---|---|---|
| `.env` | ja | Standardwerte ohne Geheimnisse |
| `.env.local` | **nie** | die Werte und Geheimnisse dieses Hosts |
| `.env.local.dist` (optional) | ja | eine Kopie von `.env.local`, in der jedes Geheimnis unkenntlich gemacht ist, als Referenz |

Echte Umgebungsvariablen, die der Webserver oder der Prozessmanager setzt, setzen sich gegen die Dateien durch.

## Anwendung

```bash
APP_ENV=prod                  # or dev
APP_SECRET=<32 random hex characters>
```

`APP_DEBUG` und `APP_HTTP_CACHE` werden in den Beispielen der Anleitung in der Konfiguration des Webservers gesetzt
([Webserver einrichten](webserver-einrichten.md)).

## Datenbank

MySQL oder MariaDB aus Teilen:

```bash
DATABASE_DRIVER=pdo_mysql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=3306
DATABASE_NAME=your_db_name
DATABASE_USER=your_db_user
DATABASE_PASSWORD=your_db_password
DATABASE_CHARSET=utf8mb4
DATABASE_COLLATION=utf8mb4_unicode_520_ci
DATABASE_VERSION=mariadb-10.6.0    # or 8.0 for MySQL
```

PostgreSQL verwendet `DATABASE_DRIVER=pdo_pgsql`, Port `5432`, `DATABASE_CHARSET=utf8` und `DATABASE_VERSION=16`. Ein
vollständiger DSN in `DATABASE_URL` ersetzt die Teile. SQLite verwendet nur den DSN und einen synchronen
Messenger-Transport:

```bash
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
MESSENGER_TRANSPORT_DSN=sync://
```

Details: [Datenbanken](datenbanken-mysql-postgresql-sqlite.md), [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md).

## JWT

```bash
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<64 random hex characters>
```

Die Schlüsseldateien werden je Installation erzeugt und nie committet ([JWT-Authentifizierung](jwt-authentifizierung.md)).

## Suche, Cache, E-Mail und der Rest

| Variable | Standard in der Anleitung | Andere Werte | Seite |
|---|---|---|---|
| `SEARCH_ENGINE` | `legacy` (die Suchmaschine auf der Datenbank) | `solr`, mit `SOLR_DSN` und `SOLR_CORE` | [Suche](suche-legacy-und-solr.md) |
| `HTTPCACHE_PURGE_TYPE` | `local` | `varnish` | [HTTP-Cache](http-cache-und-varnish.md) |
| `HTTPCACHE_DEFAULT_TTL` | `86400` | Sekunden | |
| `HTTPCACHE_PURGE_SERVER` | `http://localhost:80` | die Adresse von Varnish | |
| `HTTPCACHE_VARNISH_INVALIDATE_TOKEN`, `TRUSTED_PROXIES` | nicht gesetzt | mit Varnish | |
| `CACHE_POOL` | `cache.tagaware.filesystem` | `cache.redis` mit `CACHE_DSN` | [Anwendungs-Cache](anwendungs-cache-und-redis.md) |
| `MAILER_DSN` | `null://null` (keine Zustellung) | `smtp://localhost:25` | |
| `IMAGEMAGICK_PATH` | `/usr/bin` | | [Bildvariationen](bildvariationen.md) |
| `CORS_ALLOW_ORIGIN` | Muster für localhost | die Origins Ihrer Clients | [Headless mit der DXP](headless-mit-der-dxp.md) |
| `SESSION_HANDLER_ID`, `SESSION_SAVE_PATH` | native Datei-Sessions unter `var/sessions/` | | |

Für Test-E-Mails in der Entwicklung behalten Sie `MAILER_DSN=null://null` bei und verwenden für Testbenutzer Adressen
unter `example.invalid`, damit keine E-Mail eine echte Person erreicht.

## Eine Referenz ohne Geheimnisse aufbewahren

```bash
cp .env.local .env.local.dist
$EDITOR .env.local.dist          # replace every password, secret and passphrase with a placeholder
git add .env.local.dist
```

Lesen Sie die Datei noch einmal, bevor Sie sie committen.

## Prüfen, was Symfony sieht

```bash
php bin/console debug:dotenv               # which files were loaded and the values they set
php bin/console debug:container --env-vars # environment variables used by the container
```

## Verwandte Seiten

- [Installation mit dem Skeleton](installation-mit-dem-skeleton.md)
- [Bereitstellen](dxp-bereitstellen.md)
- [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md#ein-projekt-anlegen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
