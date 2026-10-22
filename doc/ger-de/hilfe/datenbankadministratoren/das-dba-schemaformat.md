---
title: Das .dba-Schemaformat und die mitgelieferten Schemadateien
audience: dba
category: database-administration
order: 26
summary: Was das engine-neutrale Format .dba ist, welche Schema- und Datendateien mit Exponential ausgeliefert werden (share/db_schema.dba, share/db_data.dba, kernel/sql je Engine), wie in dbschema.ini registrierte Schema-Handler daraus DDL machen, wie Erweiterungen ihr eigenes Schema hinzufügen und wie der Lint-Checker Namen portabel hält.
keywords: dba, .dba, db_schema.dba, db_data.dba, Schema, engine-neutral, generisches Format, kernel_schema.sql, schema.sql, cleandata.sql, setval.sql, sequence.sql.template, dbschema.ini, SchemaPaths, SchemaHandlerClasses, eZDbSchema, LintChecker, Erweiterung share, ezimportdbafile, schema, generic format, extension share
type: help
---
# Das .dba-Schemaformat und die mitgelieferten Schemadateien

Diese Seite richtet sich an Datenbankadministratoren, die wissen möchten, woher die Definition einer Exponential-Datenbank
kommt, bevor sie eine vergleichen, dumpen oder umziehen. Sie beantwortet: was eine `.dba`-Datei ist, welche Schemadateien mit
dem Kernel ausgeliefert werden und welches Werkzeug jede liest, wie Erweiterungen Tabellen hinzufügen und was Namen auf jeder
Engine gültig hält.

## Inhalt

1. [Das Format .dba](#das-format-dba)
2. [Die mitgelieferten Dateien](#die-mitgelieferten-dateien)
3. [Schema-Handler](#schema-handler)
4. [Erweiterungen und ihr Schema](#erweiterungen-und-ihr-schema)
5. [Der Lint-Checker](#der-lint-checker)
6. [Was Sie mit diesen Dateien tun](#was-sie-mit-diesen-dateien-tun)
7. [Checkliste](#checkliste)

## Das Format .dba

Eine `.dba`-Datei ist eine PHP-Datei, die ein Array enthält: für das Schema einen Eintrag je Tabelle mit ihren Feldern (Typ,
Länge, `not_null`, Standard) und Indizes; für Daten die Zeilen. Sie beschreibt die Datenbank ohne den Dialekt einer Engine,
eine Datei dient also jeder Engine. Der Anfang von `share/db_schema.dba` sieht so aus:

```php
<?php
// This array contains the database schema
$schema = array (
  'expaudit_cursor' =>
  array (
    'name' => 'expaudit_cursor',
    'fields' =>
    array (
      'byte_offset' =>
      array (
        'length' => 20,
        'type' => 'bigint',
        ...
```

`ezsqldumpschema.php --format=generic --output-array` schreibt dieses Format aus einer laufenden Datenbank
([Schemawerkzeuge: Dump, Insert und Diff](schemawerkzeuge-dump-insert-diff.md)). `--format=local` schreibt einen Dump, der nur zu
der Engine passt, aus der er stammt.

## Die mitgelieferten Dateien

| Datei | Was sie ist | Gelesen von |
|---|---|---|
| `share/db_schema.dba` | das Schema des Kernels, engine-neutral (etwa 130 Tabellen) | den Installern, der Upgrade-Prüfung, `ezsqldiff.php` |
| `share/db_data.dba` | die Anfangsdaten des Kernels | den Installern |
| `kernel/sql/mysql/kernel_schema.sql` | das Schema als MySQL-DDL (129 `CREATE TABLE`) | Referenz, Installationen von Hand |
| `kernel/sql/mysql/cluster_dfs_schema.sql` | die Tabellen des DFS-Clusters `ezdfsfile`, `ezdfsfile_cache` | Cluster-Einrichtung |
| `kernel/sql/mysql/cluster_db_schema.sql`, `cluster_schema.sql` | Tabellen des entfernten Datenbank-Datei-Handlers (`ezdbfile`, `ezdbfile_data`) | historisch |
| `kernel/sql/postgresql/kernel_schema.sql` | das Schema als PostgreSQL-DDL (129 `CREATE TABLE`) | Referenz |
| `kernel/sql/postgresql/setval.sql` | `setval()` für jede Sequenz des Kernels (85 Anweisungen) | nach dem Laden von Daten mit expliziten IDs |
| `kernel/sql/postgresql/sequence.sql.template` | die Vorlage für Sequenzen | Schemaerzeugung |
| `kernel/sql/sqlite/schema.sql`, `cleandata.sql` | das SQLite-Schema und saubere Daten | die SQLite-Tests vergleichen den Treiber damit |
| `kernel/sql/common/cleandata.sql` | saubere Anfangsdaten | Referenz |
| `share/legacy_schema.sql` | ein MySQL-Dump in `share/`; kein Code in `kernel/`, `lib/` oder `bin/` liest ihn | nichts; wenden Sie ihn nicht an |

## Schema-Handler

`settings/dbschema.ini [SchemaSettings]` ordnet einem Typ eine Handler-Klasse zu:

| Typ | Klasse | Datei |
|---|---|---|
| `mysql`, `mysqli` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `postgresql` | `eZPgsqlSchema` | `lib/ezdbschema/classes/ezpgsqlschema.php` |
| `sqlite`, `sqlite3` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |
| `mongo` | `expMongoSchema` | `lib/ezdbschema/classes/expmongoschema.php` |
| `oracle` | `eZOracleSchema` | von der Erweiterung `ezoracle` hinzugefügt |

Ein Handler schreibt DDL aus einem `.dba`-Schema und liest eine laufende Datenbank in eines zurück. Indexnamen gelten auf
SQLite und PostgreSQL datenbankweit; die Handler legen einen kollidierenden Namen als `<table>__<name>` an. Auf PostgreSQL legt
der Handler jede Sequenz vor ihrer Tabelle an, und nach dem Laden von Daten korrigiert der Schema-Installer die Sequenzen
([PostgreSQL betreiben](postgresql-betrieb.md)).

## Erweiterungen und ihr Schema

Eine Erweiterung mit Tabellen liefert ihre eigene `share/db_schema.dba` (und oft `sql/<engine>/*.sql`). Die Upgrade-Prüfung
liest die `share/db_schema.dba` jeder aktiven Erweiterung, und die Schemas installierter, aber inaktiver Erweiterungen nur, um
Ihnen zu sagen, zu welcher Erweiterung eine Tabelle gehört
([Das Schema nach einem Upgrade prüfen](das-schema-nach-einem-upgrade-pruefen.md)). Einige Datentypen liefern eigene Daten:
`bin/php/ezimportdbafile.php --datatype=<name>` importiert sie (Beispiel aus seiner Hilfe: `--datatype=ezisbn`).

## Der Lint-Checker

`dbschema.ini [LintChecker]` begrenzt die Länge von Namen, damit das Schema auf Engines mit kurzen Bezeichnern gültig bleibt:
`TableLimit=26`, `FieldLimit=30`, `IndexLimit=30`, mit Listen historischer Ausnahmen (`IgnoredTables[]`, `IgnoredFields[]`,
`IgnoredIndexes[]`). `ezsqldiff.php --lint-check` prüft eine Datenbank gegen diese Regeln, statt zwei Schemas zu vergleichen.
Entwickler von Erweiterungen sollten ihre Namen innerhalb der Grenzen halten.

## Was Sie mit diesen Dateien tun

- **Bearbeiten Sie nie** `share/db_schema.dba` oder die Dateien in `kernel/sql/`: Ein Upgrade ersetzt sie.
- **Vergleichen Sie** Ihre Datenbank nach einem Upgrade mit `share/db_schema.dba`.
- **Ziehen Sie** eine Website zwischen SQL-Engines **um**, indem Sie nach `.dba` dumpen und mit einem anderen `--type`
  einfügen ([Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md)).
- **Bewahren Sie** einen `.dba`-Dump Ihrer eigenen Datenbank als portables zweites Sicherungsformat neben dem nativen Dump der
  Engine **auf**.

## Checkliste

- [ ] Sie wissen, welche Schemadatei jedes Werkzeug liest.
- [ ] Ihre eigenen Erweiterungen liefern eine `share/db_schema.dba` für ihre Tabellen.
- [ ] Die Namen in Ihren Erweiterungen bestehen `ezsqldiff.php --lint-check`.

## Verwandte Seiten

- [Schemawerkzeuge: Dump, Insert und Diff](schemawerkzeuge-dump-insert-diff.md)
- [Die Tabellen, die ein Datenbankadministrator kennen sollte](tabellen-die-sie-kennen-sollten.md)
- [Die Datenbankschicht auf einer Seite](die-datenbankschicht-auf-einer-seite.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
