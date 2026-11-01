---
title: Schemaänderungen und Migrationen
audience: software-engineer, developer
category: software-engineering
order: 210
summary: Wie in Exponential eine Änderung des Datenbankschemas ausgeliefert wird - die .dba-Schemadateien und Schema-Handler, Upgrade-Schritte, die exp:upgrade anwendet und festhält, Migrationen für die eigenen Änderungen einer Site, die Upgrade-Prüfung, die eine Website mit dem Schema des Releases vergleicht, und eine Checkliste für eine Änderung, die jede Installation anwenden kann.
keywords: Schema, Migration, dba, db_schema.dba, Update-SQL, dbupdate, ezsqldumpschema, ezsqlinsertschema, Upgrade-Prüfung, Datenbankkonsistenz, Schema-Handler, Upgrade-Schritt, exp:upgrade, exp:migration, Softwareingenieur, migration, update SQL, upgrade check, database consistency, schema handler, upgrade step
type: help
---
# Schemaänderungen und Migrationen

Diese Seite richtet sich an Ingenieure, die im Kernel oder in einer Erweiterung eine Tabelle, eine Spalte oder einen
Index hinzufügen oder ändern. Seit 6.0.15 wird eine Schemaänderung als **Upgrade-Schritt** ausgeliefert, den
`./console exp:upgrade` auf jeder Engine anwendet und festhält, und die eigenen Änderungen einer Site als
**Migrationen**, die `./console exp:migration:migrate` der Reihe nach anwendet, mit einer Caption vor dem Lauf und
einer Aufzeichnung dessen, was gelaufen ist. Die SQL-Updatedateien bleiben als Referenz für Installationen, die von
Hand aktualisiert werden.

## Inhalt

1. [Die beteiligten Dateien](#die-beteiligten-dateien)
2. [Wie eine Installation eine Änderung anwendet](#wie-eine-installation-eine-änderung-anwendet)
3. [Die Werkzeuge](#die-werkzeuge)
4. [Checkliste für eine Schemaänderung](#checkliste-für-eine-schemaänderung)
5. [Bekannte Lücken](#bekannte-lücken)

## Die beteiligten Dateien

| Datei | Rolle |
|---|---|
| `share/db_schema.dba`, `share/db_data.dba` | Schema und Ausgangsdaten des Kernels als PHP-Arrays; Erweiterungen liefern ihre eigenen in ihrem `share/` |
| `settings/dbschema.ini [SchemaSettings]` | ordnet eine Engine ihrem Schema-Handler zu (`eZMysqlSchema`, `eZPgsqlSchema`, `eZSQLiteSchema`, `expMongoSchema`; `eZOracleSchema` mit `ezoracle`) |
| `kernel/sql/<engine>/` | das je Engine erzeugte SQL |
| `update/database/<driver>/6.0/` | das Upgrade-SQL je Treiber, zum Beispiel `dbupdate-6.0.0-6.0.15.sql` (Dateien für MySQL, PostgreSQL und SQLite existieren) |
| `update/common/scripts/6.0/` | PHP-Skripte, die ein Upgrade ausführt, etwa `createaudittables.php` |
| `kernel/classes/upgrade/steps/`, `settings/upgrade.ini` | die Upgrade-Schritte des Kernels (`expUpgradeStep`) und ihre Liste; eine Erweiterung fügt ihre eigenen mit `ExtensionStepList[]` hinzu |
| `migrations/`, `extension/<name>/migrations/` | die Migrationen einer Site und einer Erweiterung, `YYYYMMDDHHMMSS_name.php` |

Die Update-Dateien für Oracle liegen in `ezoracle`; MongoDB hat keine, weil es kein SQL-Schema hat.

## Wie eine Installation eine Änderung anwendet

Aus [Kompatibilität, Releases und Bereitstellung](../../standards/architektur/kompatibilitaet-releases-deployment.md#eine-installation-aktualisieren):

1. Sichern Sie Dateien und Datenbank.
2. Bringen Sie den neuen Code an seinen Platz, erzeugen Sie die Klassenzuordnungen neu und leeren Sie alle Caches; laden
   Sie PHP-FPM neu und starten Sie Velocity neu.
3. Wenden Sie die Datenbankaktualisierung **einmal, vollständig, der Reihe nach** an: die Datei in
   `update/database/<driver>/6.0/`, dann die Skripte in `update/common/scripts/6.0/`, die die Upgrade-Anleitung nennt.
4. Führen Sie die Upgrade-Prüfung aus: Setup > System Upgrade, **Überprüfe Datenbankkonsistenz** vergleicht die Tabellen
   mit den Schemadateien ([Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md)).

Die Update-Datei muss sagen, wie auf einer Datenbank vorzugehen ist, die bereits einen Teil davon hat; bei der Datei
für 6.0.15 schlägt eine Anweisung für eine vorhandene Spalte mit deren Namen fehl, und nur diese Anweisung wird
übersprungen.

Die Schritte 1 bis 3 sind seit 6.0.15 ein Befehl: `./console exp:upgrade --apply` nimmt eine Caption, wendet jeden
ausstehenden Upgrade-Schritt auf jeder Engine an, hält jeden in `exp_migration` fest und stellt bereit
([Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)). Eine Erweiterung liefert ihre
Schemaänderung als eigenen Upgrade-Schritt aus ([Upgrade-Schritte für Erweiterungen](../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)).
Eine Änderung einer einzelnen Site (ihre eigene Tabelle, Klassen, Inhalte, Rollen) ist eine Migration:
`./console exp:migration:migrate --apply` führt die ausstehenden der Reihe nach aus, in einer Transaktion, wo die
Engine eine hat, stellt nach einem Fehler die Caption wieder her und committet die Aufzeichnung des Laufs
([Migrationen](../../anleitungen/migrationen.md), [PHP-Migrationen](../../anleitungen/migrationen-php.md)).
Die Upgrade-Schritte stehen dort ebenfalls, als Migrationen der Quelle `kernel`, und ein Migrationslauf führt die
ausstehenden Schritte der Stufen `database` und `scripts` vor den Migrationen der Site aus, unter demselben
Sicherungspunkt; eine Migration der Site kann daher einen Schritt verlangen (`requires: 6.0.15-migration-tables`).

## Die Werkzeuge

```bash
./console exp:upgrade:status               # every upgrade step and its state
./console exp:migration:status             # every migration and its state
./console exp:migration:check              # the lint of migration files, for CI
php bin/php/ezsqldumpschema.php --help     # dump a database schema to a .dba file
php bin/php/ezsqlinsertschema.php --help   # load a .dba schema into an engine (--type)
php bin/php/ezsqldiff.php --help           # compare two schemas
```

Eine `.dba`-Änderung anhand ihres Diffs zu prüfen ist irreführend: Zeilen liegen in einem numerisch geschlüsselten Array,
das Entfernen einer Zeile nummeriert also jede Zeile danach neu. Vergleichen Sie stattdessen das Schema, das die Dateien
ergeben, zum Beispiel mit `ezsqldiff.php` oder einem Test, der die Datei in eine Wegwerf-SQLite-Datenbank lädt
([Schema und dba-Dateien](../ersteller/anwendungsentwickler/schema-und-dba-dateien.md)).

## Checkliste für eine Schemaänderung

- [ ] Die Änderung steht in `share/db_schema.dba` (und `db_data.dba` für Ausgangszeilen) des Kernels oder der
      Erweiterung.
- [ ] Ein Upgrade-Schritt wendet sie auf jeder Engine an und erkennt sie (`detect()`), sodass `exp:upgrade` sie
      festhält und ein zweiter Lauf nichts ändert; die eigene Änderung einer Site ist stattdessen eine Migration.
- [ ] Für jeden Treiber mit SQL existiert eine Update-Datei: MySQL/MariaDB, PostgreSQL, SQLite und Oracle in `ezoracle`.
- [ ] MongoDB ist abgedeckt: Der Adapter oder der Installer legt an, was der Code braucht.
- [ ] Die Aktualisierung ist auf einer Datenbank sicher, die bereits einen Teil davon hat.
- [ ] Eine Persistenzklasse (`eZPersistentObject`) beschreibt eine neue Tabelle.
- [ ] Neue Tabellen verwenden das Präfix `exp`; Spalten, die `ez`-Tabellen hinzugefügt werden, behalten deren Stil
      ([Regeln zu Schichtung und Benennung](../../standards/architektur/schichten-und-benennung.md)).
- [ ] Das Changelog führt sie unter **Added** oder **Updated**, und die Upgrade-Hinweise in `doc/eng-us/bc/6.0/` sagen,
      was auszuführen ist ([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).
- [ ] Die CI-Datenbankjobs (MySQL 8.0 und PostgreSQL 15) bestehen.

## Bekannte Lücken

- Eine Installation, die von Hand mit den SQL-Updatedateien aktualisiert wurde, hat keine Aufzeichnung, welche
  Anweisungen gelaufen sind; `exp:upgrade` hält jeden Schritt fest, den es anwendet, und `exp:upgrade:verify` findet
  Abweichungen.
- Migrationen werden in 6.0.15 in PHP geschrieben; YAML-, SQL- und INI-Migrationen werden gefunden und geprüft, aber
  noch nicht ausgeführt.
- MariaDB und Oracle haben im Kernel-Repository keinen CI-Job.

## Verwandte Seiten

- [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md)
- [Datenbankschema und dba-Dateien](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md)
- [Aktualisieren](../../anleitungen/aktualisieren.md), [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)
- [Migrationen](../../anleitungen/migrationen.md), [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
