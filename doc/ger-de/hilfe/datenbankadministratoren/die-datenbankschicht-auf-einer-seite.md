---
title: Die Datenbankschicht auf einer Seite
audience: dba
category: database-administration
order: 2
summary: Wie Exponential seine Datenbank erreicht, erklärt für Datenbankadministratoren - der Einstiegspunkt eZDB, die Treiber-Aliase, die fünf Treiberklassen, die Schema-Handler, was in der Datenbank gespeichert wird und was nicht, und wo jedes Teil im Code liegt.
keywords: eZDB, eZDBInterface, Treiber, Datenbankschicht, DatabaseImplementation, ImplementationAlias, eZNullDB, Schema-Handler, dbschema.ini, lib/ezdb, lib/ezdbschema, Architektur, was in der Datenbank liegt, driver, database layer, schema handler, architecture, what is in the database
type: help
---
# Die Datenbankschicht auf einer Seite

Diese Seite richtet sich an Datenbankadministratoren, die Exponential zum ersten Mal begegnen und wissen möchten, wie die
Anwendung mit der Datenbank spricht, bevor sie einen Server anfassen. Sie beantwortet: welcher Code die Verbindung öffnet,
wie der Treiber gewählt wird, was die Treiber unterschiedlich machen, welche Werkzeuge das engine-neutrale Schema in DDL
umwandeln und was in der Datenbank statt in Dateien liegt. Jede hier genannte Klasse und Datei existiert im Kernel 6.0.x.

## Inhalt

1. [Ein Einstiegspunkt: eZDB](#ein-einstiegspunkt-ezdb)
2. [Treiber und ihre Aliase](#treiber-und-ihre-aliase)
3. [Schema-Handler](#schema-handler)
4. [Was in der Datenbank liegt und was nicht](#was-in-der-datenbank-liegt-und-was-nicht)
5. [Caches zwischen der Website und der Datenbank](#caches-zwischen-der-website-und-der-datenbank)
6. [Wo die Teile liegen](#wo-die-teile-liegen)
7. [Was das für Ihre Arbeit bedeutet](#was-das-für-ihre-arbeit-bedeutet)

## Ein Einstiegspunkt: eZDB

Jeder Teil von Exponential erreicht die Datenbank über `eZDB::instance()` (`lib/ezdb/classes/ezdb.php`). Es liest
`site.ini [DatabaseSettings] DatabaseImplementation`, sucht den Wert in `ImplementationAlias[]`, erzeugt das
Treiberobjekt und behält es für den Rest der Anfrage.

Alle Treiber implementieren eine Schnittstelle, `eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`): Verbinden,
`query()`, `arrayQuery()` mit Offset und Limit, Transaktionen, `lastSerialID()`, `escapeString()` und Hilfsfunktionen für
das, was zwischen Engines abweicht (`subString()`, `concatString()`, `md5()`, `bitAnd()`, `bitOr()`).

Nennt der Alias keine Klasse, bricht die Anfrage nicht sofort ab. `eZDB::instance()` gibt eine Null-Datenbank zurück
(`eZNullDB`, `lib/ezdb/classes/eznulldb.php`) und schreibt "Database implementation not supported" nach
`var/log/error.log`. Jede Seite, die Inhalte braucht, scheitert dann, ein Tippfehler sieht also aus wie eine
ausgefallene Datenbank ([9.1](../../installation/09-datenbanken.md#91-wie-exponential-einen-datenbanktreiber-auswählt)).

## Treiber und ihre Aliase

| Engine | Alias in `DatabaseImplementation` | Klasse | Datei |
|---|---|---|---|
| SQLite 3 | `sqlite3` | `eZSQLite3DB` | `lib/ezdb/classes/ezsqlite3db.php` |
| PostgreSQL | `ezpostgresql`, `postgresql`, `pgsql` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| MongoDB | `mongodb` | `expMongoDB` | `lib/ezdb/classes/expmongodb.php` |
| MariaDB, MySQL | `ezmysqli`, `mysqli`, `ezmysql`, `mysql` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| Oracle | `ezoracle`, `oracle` | `eZOracleDB` | die Erweiterung `ezoracle` |

Die Kernel-Aliase stehen in `settings/site.ini`; die Oracle-Aliase fügt die eigene `site.ini.append.php` der Erweiterung
hinzu, wenn sie aktiv ist. Code kann fragen, auf welcher Engine er läuft: `eZDB::instance()->databaseName()` gibt
`sqlite`, `postgresql`, `mongo`, `mysql` oder `oracle` zurück (und `null` für die Null-Datenbank).

Zwei Treiber tun mehr, als SQL durchzureichen:

- **SQLite** registriert die Funktionen, die SQLite fehlen (`md5()`, die Aggregate `BIT_OR` und `BIT_AND`), schreibt
  MySQLs `UPDATE ... JOIN` um, ignoriert reine MySQL-`SET`-Anweisungen und reiht Schreiber in eine Warteschlange ein
  ([SQLite 3 für Datenbankadministratoren](sqlite-fuer-datenbankadministratoren.md)).
- **MongoDB** führt nie SQL aus: Der Adapter übersetzt die SQL-förmigen Aufrufe des Kernels in MongoDB-Operationen
  ([MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md)).

## Schema-Handler

Das Schema wird in einem engine-neutralen Format geführt, `.dba` (`share/db_schema.dba`, `share/db_data.dba`). Ein
Schema-Handler wandelt es in die DDL einer Engine um und liest eine laufende Datenbank in dasselbe Format zurück. Die
Handler sind in `settings/dbschema.ini [SchemaSettings]` registriert:

| Typ | Klasse | Datei |
|---|---|---|
| `mysql`, `mysqli` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `postgresql` | `eZPgsqlSchema` | `lib/ezdbschema/classes/ezpgsqlschema.php` |
| `sqlite`, `sqlite3` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |
| `mongo` | `expMongoSchema` | `lib/ezdbschema/classes/expmongoschema.php` |
| `oracle` | `eZOracleSchema` | die Erweiterung `ezoracle` |

Die Installer, die Schemawerkzeuge (`ezsqldumpschema.php`, `ezsqlinsertschema.php`) und die Upgrade-Prüfung laufen alle
über diese Handler. Siehe [Das .dba-Schemaformat](das-dba-schemaformat.md).

## Was in der Datenbank liegt und was nicht

| In der Datenbank | Nicht in der Datenbank |
|---|---|
| Inhaltsobjekte, Versionen, Felder, der Knotenbaum, URL-Aliase | Hochgeladene Bilder und Dateien: `var/storage` (und `var/<site>/storage`) |
| Inhaltsklassen, Sektionen, Objektzustände | Einstellungen: INI-Dateien in `settings/` und den Erweiterungen |
| Benutzer, Rollen, Richtlinien, Benutzerbesuche | Caches: Dateien unter `var/cache` und `var/<site>/cache` |
| Der eingebaute Suchindex | Logs: `var/log/` |
| Sessions, wenn `ezpSessionHandlerDB` verwendet wird | Sessions mit dem Standard-Handler von PHP (PHPs eigener Speicher) |
| Warteschlangen (`ezpending_actions`), Workflows, der Shop | Templates und Designs |
| Die installierte Version (`ezsite_data`) | Der Wartungsschalter (`var/maintenance.json`) |

Eine Datenbanksicherung allein ist daher keine Sicherung der Website; siehe [Sicherungen je Engine](sicherungen-je-engine.md).

## Caches zwischen der Website und der Datenbank

Die meisten Seitenaufrufe erreichen die Datenbank nie: Der Antwort-Cache von Velocity, der Cache der Inhaltsansichten und
der Template-Block-Cache antworten zuerst, und der optionale SQL-Abfrage-Cache (`settings/querycache.ini`, standardmäßig
`Mode=off`) beantwortet wiederholte `SELECT`-Anweisungen auf jeder Engine außer MongoDB. Ein Schreibzugriff außerhalb von
Exponential, mit einem SQL-Client oder einer Wiederherstellung, wird von diesen Caches nicht bemerkt; leeren Sie sie danach
([Der SQL-Abfrage-Cache](sql-abfrage-cache.md), [Leistung und Skalierung](leistung-und-skalierung.md)).

## Wo die Teile liegen

| Was | Wo |
|---|---|
| Verbindungseinstellungen | `settings/site.ini [DatabaseSettings]`, überschrieben in `settings/override/site.ini.append.php` |
| Treiber | `lib/ezdb/classes/` |
| Schema-Handler | `lib/ezdbschema/classes/`, registriert in `settings/dbschema.ini` |
| Mitgeliefertes Schema und Daten | `share/db_schema.dba`, `share/db_data.dba`, `kernel/sql/<engine>/` |
| Update-Dateien | `update/database/<engine>/<version>/` |
| Schemawerkzeuge | `bin/php/ezsqldumpschema.php`, `ezsqlinsertschema.php`, `ezsqldiff.php` |
| MongoDB-Hilfsmittel | `bin/mongodb/` |

## Was das für Ihre Arbeit bedeutet

- Das Datenbankkonto ist der einzige Zugang, den die Anwendung braucht; er liegt in einer Override-Datei
  ([Die Datenbank absichern](die-datenbank-absichern.md)).
- Schemaänderungen kommen aus Update-Dateien und den Schemawerkzeugen, nie aus Handbearbeitung
  ([Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md)).
- Leeren Sie nach jedem Schreibzugriff außerhalb der Anwendung die Caches.

## Verwandte Seiten

- [Datenbankadministratoren: hier beginnen](datenbankadministratoren-hier-beginnen.md)
- [Die Referenz zu DatabaseSettings](referenz-databasesettings.md)
- [Unterstützte Engines und Versionen](unterstuetzte-engines-und-versionen.md)
- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
