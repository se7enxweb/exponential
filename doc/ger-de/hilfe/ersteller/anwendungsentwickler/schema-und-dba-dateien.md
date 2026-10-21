---
title: Schema und .dba-Dateien
audience: app-creator, developer
category: app-creation
order: 230
summary: Die Tabellen Ihrer Anwendung für jede Datenbank-Engine ausliefern - das Schemaformat .dba, Export mit ezsqldumpschema, Installation mit ezsqlinsertschema, SQL-Dateien je Engine, ein Installationsbefehl und Upgrades.
keywords: Schema, Datenbankschema, Schema installieren, Upgrade, Tabellen, Anwendungsentwickler, schema, dba, db_schema.dba, db_data.dba, ezsqldumpschema, ezsqlinsertschema, ezsqldiff, ezimportdbafile, eZDbSchema, install schema, upgrade, tables, app creator
type: help
---
# Schema und .dba-Dateien

Diese Seite richtet sich an Anwendungsentwickler, deren Erweiterung eigene Tabellen hat. Exponential beschreibt Schemas
in einem datenbankneutralen Format, `.dba`, und erzeugt daraus das SQL für jede Engine, sodass eine einzige Datei für
SQLite, PostgreSQL, MySQL/MariaDB und Oracle genügt.

## Inhalt

1. [Das Format .dba](#das-format-dba)
2. [Die Datei erstellen](#die-datei-erstellen)
3. [Die Datei installieren](#die-datei-installieren)
4. [Ein Installationsbefehl](#ein-installationsbefehl)
5. [Upgrades](#upgrades)
6. [Änderungen prüfen](#änderungen-prüfen)

## Das Format .dba

Eine `.dba`-Datei ist ein PHP-Array von Tabellen, jede mit Feldern (Typ, Länge, Standardwert, not null), Indizes und
dem Primärschlüssel. Das Schema des Kernels ist `share/db_schema.dba` und seine Daten sind `share/db_data.dba`;
Erweiterungen legen ihre in `extension/<ext>/share/db_schema.dba` ab (und `db_data.dba` für Startzeilen). Die Klasse,
die sie liest und schreibt, ist `eZDbSchema` (`lib/ezdbschema/`).

## Die Datei erstellen

Legen Sie die Tabelle in einer Entwicklungsdatenbank an und exportieren Sie dann nur Ihre Tabellen:

```bash
php bin/php/ezsqldumpschema.php --type=sqlite --table-include=expdemo_item --output-array \
  var/storage/sqlite3/exponential.db extension/expdemo/share/db_schema.dba --allow-root-user
```

Prüfen Sie die Optionen Ihrer Version mit `php bin/php/ezsqldumpschema.php --help`; der Export kann statt des Arrays
auch SQL für eine einzelne Engine schreiben.

## Die Datei installieren

```bash
php bin/php/ezsqlinsertschema.php --type=mysql --user=<user> extension/expdemo/share/db_schema.dba <database>
```

`ezsqlinsertschema.php` („Insert database schema and data to specified database“) legt die Tabellen für die mit
`--type` genannte Engine an. `ezimportdbafile.php` importiert eine `.dba` in die konfigurierte Datenbank.

## Ein Installationsbefehl

Geben Sie Ihrer Erweiterung einen idempotenten Befehl, der ihre Tabellen anlegt, falls sie fehlen, so wie expqueue es
mit `./console ext:expqueue:install-schema` tut ([expqueue](../../../funktionen/6.0/erweiterungen/expqueue.md)):

```text
extension/expdemo/bin/php/install-schema.php   ->   ./console ext:expdemo:install-schema
```

Er liest die `.dba`, prüft, welche Tabellen existieren, und legt nur die fehlenden an; zweimal ausgeführt, ändert er
nichts.

## Upgrades

- Ändern Sie eine veröffentlichte Tabelle niemals direkt ohne einen Upgrade-Schritt.
- Liefern Sie Upgrade-Schritte als nummerierte Skripte oder SQL-Dateien je Engine aus (`extension/<ext>/update/`), und
  lassen Sie den Installationsbefehl die fehlenden anwenden.
- Fügen Sie Spalten mit Standardwerten hinzu, damit alter Code während des Deployments weiter funktioniert.

## Änderungen prüfen

Eine `.dba` ist ein PHP-Array; das Entfernen einer Zeile nummeriert alles Folgende neu, sodass ein Text-Diff bei
Datendateien unlesbar ist. Vergleichen Sie Schemas mit `php bin/php/ezsqldiff.php` und Daten Zeile für Zeile mit einem
Skript, nicht per Diff.

## Verwandte Seiten

- [Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md)
- [Datenbankzugriff](datenbankzugriff.md)
- [Datenbanken](../../../installation/09-datenbanken.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
