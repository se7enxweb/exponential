---
title: Datenbankschema, .dba-Dateien und Upgrades
audience: developer, app-creator
category: extension-development
order: 270
summary: Tabellen ausliefern, die sich auf jeder unterstützten Datenbank installieren lassen - share/db_schema.dba, schema.sql pro Engine, ezsqlinsertschema und ezsqldumpschema, idempotente Installationsbefehle, Upgrades.
keywords: Datenbankschema, Tabellen, Upgrade, idempotent, Datenbank-Engine, database schema, db_schema.dba, db_data.dba, schema.sql, ezsqlinsertschema, ezsqldumpschema, eZDbSchema, sqlite, mysql, postgresql, oracle, mongodb, update scripts, upgrade, install-schema, idempotent
type: help
---
# Datenbankschema, .dba-Dateien und Upgrades

Teil 3, Kapitel 27. Ihr persistentes Objekt braucht seine Tabelle auf jeder Installation, egal welche Datenbank sie
verwendet: Exponential unterstützt SQLite, PostgreSQL, MongoDB, MariaDB, Oracle und MySQL
([Datenbankunterstützung](../../datenbankunterstuetzung.md)). Dieses Kapitel zeigt, wie Erweiterungen Tabellen
ausliefern und später ändern.

## Zwei Formen

| Datei | Form | Verwendet von |
|---|---|---|
| `share/db_schema.dba` | das Schema als PHP-Array, unabhängig von der Engine | dem Paket-Installer, `ezsqlinsertschema`, dem Kickstarter |
| `share/db_data.dba` | einzufügende Zeilen, als PHP-Array | denselben, für Anfangsdaten |
| `sql/<engine>/schema.sql` | reines SQL pro Engine (`mysql`, `postgresql`, `sqlite`, `oracle` ...) | Menschen und Skripten, die SQL bevorzugen |

`eztags` liefert beides aus (`share/db_schema.dba` sowie `sql/mysql/schema.sql`, `sql/postgresql/schema.sql`);
`expqueue` liefert `sql/<engine>/schema.sql` für `mongodb`, `mysql`, `oracle`, `postgresql` und `sqlite`.

## Die .dba schreiben

Der Anfang von `share/db_schema.dba` aus `eztags`:

```php
<?php
// This array contains the database schema
$schema = array (
  'eztags' =>
  array (
    'name' => 'eztags',
    'fields' =>
    array (
      'id' =>
      array (
        'type' => 'auto_increment',
        'default' => false,
      ),
      'parent_id' =>
      array (
        'length' => 11,
        'type' => 'int',
        'not_null' => '1',
        'default' => 0,
      ),
```

Schreiben Sie sie nicht von Hand. Legen Sie die Tabelle in einer Entwicklungsdatenbank an und exportieren Sie sie dann:

```bash
php bin/php/ezsqldumpschema.php --type=mysql --user=<user> --output-array <database> extension/mysite_hello/share/db_schema.dba
php bin/php/ezsqldumpschema.php --type=mysql --user=<user> --output-sql  <database> extension/mysite_hello/sql/mysql/schema.sql
```

(Optionen aus `./console exp:ezsqldumpschema --help`.) Exportieren Sie nur Ihre Tabellen: Kürzen Sie das Array auf
diese, und beginnen Sie die Tabellennamen mit dem Namen Ihrer Erweiterung.

## Installieren

```bash
php bin/php/ezsqlinsertschema.php --type=sqlite extension/mysite_hello/share/db_schema.dba <database>
```

`ezsqlinsertschema` macht aus dem neutralen Schema (über `eZDbSchema`) das SQL der jeweiligen Engine, sodass eine
einzige Datei jeder Datenbank dient. Komfortabler ist es, einen Befehl mitzuliefern, der das für die Datenbank der
Installation erledigt, vorher prüft und gefahrlos zweimal ausgeführt werden kann, wie es `expqueue` tut:
`./console ext:expqueue:install-schema` "Create or upgrade the tables of the job queue (idempotent); `--check`,
`--print=<engine>`, `--write-files`" (legt die Tabellen der Job-Warteschlange an oder aktualisiert sie, idempotent).

## Upgrades

Wenn eine spätere Version eine Spalte hinzufügt:

1. Ändern Sie die Tabelle in der Entwicklung und exportieren Sie die `.dba` und die SQL-Dateien erneut.
2. Liefern Sie ein Upgrade aus: SQL pro Engine unter `update/database/<engine>/<version>/`, wie es der Kernel und
   `eztags` tun, und/oder lassen Sie Ihren Installationsbefehl fehlende Spalten hinzufügen (idempotent).
3. Schreiben Sie im Changelog genau, was auszuführen ist.

Ändern Sie eine Spalte nie so, dass ohne Migrationsskript Daten verloren gehen, und setzen Sie nie MySQL voraus:
SQLite zum Beispiel kann Spalten nicht so entfernen, wie MySQL es kann.

## Eine Änderung an einer .dba prüfen

Eine `.dba` ist ein PHP-Array mit numerischen Schlüsseln für die Zeilen; wird eine Zeile entfernt, werden alle
folgenden neu nummeriert, und ein Diff zeigt für eine kleine Änderung Tausende geänderter Zeilen. Vergleichen Sie die
Arrays Zeile für Zeile (laden Sie beide in einem kleinen Skript mit `include` und vergleichen Sie über den
Primärschlüssel), statt den Diff zu lesen.

## Auf mehr als einer Engine testen

Der schnellste echte Test ist eine frische SQLite-Installation (`./console exp:install`) mit eingeschalteter
Erweiterung und installiertem Schema. Wenn Ihre Erweiterung die Unterstützung weiterer Datenbanken verspricht, testen
Sie auch dort; die CI des Kernels lässt aus diesem Grund MySQL und PostgreSQL laufen
([Kontinuierliche Integration](../../../bc/6.0/ci.md)).

## Verwandte Seiten

- Zurück: [Persistente Objekte und eigene Tabellen](persistente-objekte-und-tabellen.md)
- Weiter: [Ereignisse, Listener und Filter](ereignisse-und-filter.md)
- [Datenbanken](../../../installation/09-datenbanken.md)
- [Pakete (.ezpkg) und der Paket-Ersteller](../teil-7-veroeffentlichen/pakete-ezpkg.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
