---
title: Glossar für Datenbankadministratoren
audience: dba
category: database-administration
order: 48
summary: Die Begriffe von Exponential, denen ein Datenbankadministrator begegnet, jeweils in einem Satz mit einem Link erklärt - von .dba, Alias und Cluster bis Siteaccess, Update-Datei, WAL und Schreibsperre.
keywords: Glossar, Begriffe, Definitionen, dba-Format, DatabaseImplementation, Treiber, Schema-Handler, Siteaccess, Override, Update-Datei, ezsite_data, DFS, WAL, Schreibsperre, Abfrage-Cache, Velocity, ezoracle, Adapter, Wartungsmodus, glossary, terms, definitions, driver, schema handler, update file, writer lock, query cache, maintenance mode
type: help
---
# Glossar für Datenbankadministratoren

Diese Seite richtet sich an Datenbankadministratoren, die einem Begriff von Exponential begegnen, den sie nicht kennen. Jeder
Eintrag besteht aus ein oder zwei Sätzen mit einem Link auf die Seite, die ihn erklärt. Die Einträge sind nach dem englischen
Begriff geordnet, der deutsche steht in Klammern.

## Inhalt

1. [A bis D](#a-bis-d)
2. [E bis M](#e-bis-m)
3. [N bis S](#n-bis-s)
4. [T bis Z](#t-bis-z)

## A bis D

**Adapter (MongoDB)**: `expMongoDB`, der Treiber, der die SQL-förmigen Aufrufe des Kernels in MongoDB-Operationen übersetzt;
er führt nie SQL aus ([MongoDB: Grenzen](mongodb-grenzen-und-betrieb.md)).

**Alias (Treiber-Alias)**: der Wert von `DatabaseImplementation`, etwa `sqlite3` oder `ezmysqli`, den `ImplementationAlias[]`
einer Treiberklasse zuordnet ([Referenz zu DatabaseSettings](referenz-databasesettings.md#treiber-aliase)).

**Audit tables (Audit-Tabellen)**: `expaudit_cursor`, `expaudit_event`, `expaudit_file`, der Index des Audit-Trails, angelegt
vom Update auf 6.0.15 oder von `createaudittables.php` ([Update auf 6.0.15](das-datenbank-update-6-0-15-anwenden.md)).

**Cluster (DFS)**: mehrere Webserver, die Dateien auf einem NFS-Mount teilen, mit Metadaten in der Tabelle `ezdfsfile`
([Clustering](clustering-dfs-und-die-datenbank.md)).

**Cronjob part (Cronjob-Teil)**: eine benannte Liste von Skripten in `cronjob.ini`, ausgeführt von `runcronjobs.php <part>`
([Aufräumen](aufraeumen-cronjobs-und-warteschlangen.md)).

**.dba**: das engine-neutrale Schema- und Datenformat von Exponential, ein PHP-Array ([Das Format .dba](das-dba-schemaformat.md)).

**DatabaseSettings**: der Block von `site.ini` mit der Verbindung ([Referenz](referenz-databasesettings.md)).

**Delayed indexing (verzögerte Indizierung)**: Das Veröffentlichen reiht die Suchindizierung für einen Cronjob in
`ezpending_actions` ein ([Tabellen des Suchindex](tabellen-des-suchindex.md)).

**DRCP**: Database Resident Connection Pooling von Oracle, angeboten von `ezoracle` ([Oracle-Tuning](oracle-verbindungen-und-tuning.md#drcp)).

**Driver (Treiber)**: die Klasse, die mit einer Engine spricht und `eZDBInterface` implementiert
([Die Datenbankschicht](die-datenbankschicht-auf-einer-seite.md)).

## E bis M

**eZDB**: der Einstiegspunkt des Kernels zur Datenbank, `eZDB::instance()`; der Name ist ein Bezeichner des Kernels.

**ezoracle**: die Erweiterung, die die Unterstützung für Oracle liefert, von `composer.json` verlangt
([Oracle einrichten](oracle-einrichten.md)).

**ezsite_data**: die Tabelle mit den Versionszeilen `ezpublish-version` und `ezpublish-release` der Datenbank; die Namen behalten
ihre historische Schreibweise ([Upgrades](upgrades-und-sql-update-dateien.md)).

**Kickstarter**: ein von `kickstart.ini` gesteuerter Installer; sein Datenbanktyp ist `[database_choice] Type=`.

**Maintenance mode (Wartungsmodus)**: Besucher erhalten eine 503-Seite, während Sie arbeiten;
`php bin/php/maintenance.php on|off|status` ([Wartungsmodus](../../anleitungen/wartungsmodus.md)).

**MONGO TODO**: die Logmarkierung für SQL, das der MongoDB-Adapter nicht übersetzen konnte.

## N bis S

**Null database (Null-Datenbank, `eZNullDB`)**: was `eZDB::instance()` zurückgibt, wenn der Alias keinen Treiber nennt; jede
Seite scheitert dann.

**Override**: eine Einstellungsdatei in `settings/override/` (oder einem Siteaccess), die einen mitgelieferten Wert ändert, ohne
ihn zu bearbeiten ([Einstellungen lesen und ändern](datenbankeinstellungen-lesen-und-aendern.md)).

**pgcrypto**: die PostgreSQL-Erweiterung, deren `digest()` der Treiber für MD5 verwendet
([PostgreSQL einrichten](postgresql-einrichten.md#pgcrypto)).

**Query cache (Abfrage-Cache)**: der optionale Cache von `SELECT`-Ergebnissen, `querycache.ini` ([Der SQL-Abfrage-Cache](sql-abfrage-cache.md)).

**Schema handler (Schema-Handler)**: die Klasse, die `.dba` in die DDL einer Engine umwandelt und zurück, registriert in
`dbschema.ini`.

**Siteaccess**: ein Zugang zu einer Installation (eine Website, die Administration) mit eigenen Einstellungen, möglicherweise
mit eigener Datenbank.

**Slave server**: der Einstellungsname von Exponential für ein Lesereplikat von MySQL oder MariaDB (`UseSlaveServer`)
([Lesereplikate](mysql-lesereplikate.md)).

## T bis Z

**Update file (Update-Datei)**: eine SQL-Datei in `update/database/<engine>/<version>/`, die eine Datenbank von einer Version
auf die nächste bringt ([Upgrades](upgrades-und-sql-update-dateien.md)).

**Upgrade check (Upgrade-Prüfung)**: **Setup > Upgrade-Prüfung**, die Dateien und das Datenbankschema mit dem Release
vergleicht ([Das Schema prüfen](das-schema-nach-einem-upgrade-pruefen.md)).

**Velocity**: der eigene Anwendungsserver von Exponential mit persistenten Workern; jeder Worker behält seine Verbindung, er muss
also nach einer Änderung der Datenbankeinstellungen neu gestartet werden.

**WAL**: das Write-Ahead-Log von SQLite, die Datei `-wal`; Leser und Schreiber arbeiten nebeneinander
([SQLite für Datenbankadministratoren](sqlite-fuer-datenbankadministratoren.md#wal-leser-und-schreiber-nebeneinander)).

**Writer lock (Schreibsperre)**: die Datei `.writer-lock`, mit der der SQLite-Treiber Schreiber einreiht
([Schreibwarteschlange](sqlite-schreibwarteschlange-und-sperren.md)).

## Verwandte Seiten

- [Glossar für Hoster](../hosting/glossar-fuer-hoster.md)
- [Häufige Fragen zur Datenbank](haeufige-fragen-zur-datenbank.md)
- [Datenbankadministratoren: hier beginnen](datenbankadministratoren-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
