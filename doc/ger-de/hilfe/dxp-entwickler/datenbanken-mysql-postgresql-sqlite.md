---
title: "Datenbanken: MySQL, MariaDB, PostgreSQL und SQLite"
audience: dxp-developer
category: dxp-development
order: 10
summary: Auf welchen Datenbanken ein DXP-Projekt läuft, wie Sie jede mit dem richtigen Zeichensatz anlegen, welche Sie für Entwicklung und Produktion wählen und wie die Anleitung des Skeletons ein laufendes Projekt von einer Engine auf eine andere umstellt.
keywords: Datenbank, MySQL, MariaDB, PostgreSQL, SQLite, utf8mb4, Kollation, Datenbank anlegen, Umstellung, Konvertierung, pgloader, mysql2sqlite, Doctrine DBAL, Oracle, database, collation, create database, conversion
type: help
---
# Datenbanken: MySQL, MariaDB, PostgreSQL und SQLite

Diese Seite richtet sich an Entwickler, die die Datenbank eines DXP-Projekts wählen und vorbereiten. Die DXP speichert
Inhalte über Doctrine DBAL; die Anleitung des Skeletons dokumentiert vier Engines.

## Inhalt

1. [Welche Engine](#welche-engine)
2. [Die Datenbank anlegen](#die-datenbank-anlegen)
3. [Das Schema installieren](#das-schema-installieren)
4. [Zwischen Engines umstellen](#zwischen-engines-umstellen)
5. [Nach einer Umstellung](#nach-einer-umstellung)

## Welche Engine

| Engine | Mindestens | Verwenden Sie sie für | Hinweise |
|---|---|---|---|
| MySQL | 8.0 | Produktion | `utf8mb4` mit `utf8mb4_unicode_520_ci` |
| MariaDB | 10.3 (10.6 empfohlen) | Produktion | derselbe Zeichensatz |
| PostgreSQL | 14 (16 empfohlen) | Produktion | Kodierung `UTF8` |
| SQLite | 3.35 | Entwicklung, Tests, Demos, Offline-Installationen | kein Server; nicht für gleichzeitige öffentliche Nutzung ([SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)) |

Der Exportweg der Anleitung zu Oracle ist "nur Export"; Oracle ist keine Engine, auf der Sie die DXP installieren.

## Die Datenbank anlegen

MySQL oder MariaDB:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
GRANT ALL PRIVILEGES ON exponential.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
```

PostgreSQL:

```bash
psql -U postgres -c "CREATE DATABASE exponential ENCODING 'UTF8';"
```

Doctrine kann sie auch aus Ihren Einstellungen anlegen: `php bin/console doctrine:database:create`. SQLite braucht
keinen Schritt: Der Installationsbefehl legt die Datei an.

Setzen Sie dann die Datenbankvariablen in `.env.local` ([Umgebungsvariablen](umgebungsvariablen.md#datenbank)).

## Das Schema installieren

```bash
php bin/console exponential:install exponential-oss
```

Spielen Sie bei späteren Aktualisierungen Datenbankänderungen mit Doctrine-Migrationen ein, nicht mit dem
Installationsbefehl ([Doctrine-Migrationen](doctrine-migrationen.md)).

## Zwischen Engines umstellen

Die Anleitung des Skeletons (Abschnitt 21) stellt ein laufendes Projekt nur mit freien Werkzeugen um:

| Von | Nach | Werkzeug in der Anleitung |
|---|---|---|
| MySQL oder MariaDB | SQLite | `mysql2sqlite` (MIT), per Pipe aus `mysqldump` |
| PostgreSQL | SQLite | `pgloader` |
| SQLite | MySQL oder MariaDB | `sqlite3-to-mysql` (Python, MIT) |
| SQLite | PostgreSQL | `pgloader` |
| MySQL oder MariaDB | PostgreSQL | `pgloader` |
| PostgreSQL | MySQL oder MariaDB | CSV-Export je Tabelle, Schema mit `pgloader`, Import |
| Beliebig | Oracle | nur Export |

Beispiel aus der Anleitung, MySQL nach SQLite:

```bash
mysqldump --no-tablespaces --skip-extended-insert --compact \
  -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" -h "$DATABASE_HOST" "$DATABASE_NAME" \
  | ./mysql2sqlite - | sqlite3 var/data_dev.db
```

Sichern Sie Datenbank, `var/` und `.env.local`, bevor Sie beginnen, und arbeiten Sie in einem Wartungsfenster ohne
Verkehr.

## Nach einer Umstellung

Die Checkliste der Anleitung (Abschnitt 21g):

1. Aktualisieren Sie `.env.local` mit den neuen Datenbankeinstellungen.
2. `php bin/console cache:clear` (der Container speichert die Verbindung zwischen).
3. `php bin/console doctrine:schema:validate`.
4. `php bin/console doctrine:migration:migrate --allow-no-migration`.
5. `php bin/console exponential:reindex`.
6. Testen Sie die Website und `/adminui/` kurz durch.
7. Korrigieren Sie bei SQLite als Ziel die Dateiberechtigungen.

| Symptom | Ursache | Abhilfe |
|---|---|---|
| "Base table not found" | eine Tabelle wurde nicht übertragen | Schema prüfen; das Log des Konverters lesen |
| Verstümmelter Text | abweichender Zeichensatz beim Export | mit `--default-character-set=utf8mb4` oder `CLIENT_ENCODING=UTF8` exportieren |
| Bildvariationen liefern 404 | Zeilen in der Feldtabelle fehlen | Zeilenzahlen von Quelle und Ziel vergleichen |

## Verwandte Seiten

- [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)
- [SQLite für Exponential Platform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [Datenbankverwaltung AdminNeo](../../funktionen/6.0/adminneo-datenbankverwaltung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
