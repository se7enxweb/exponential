---
title: "Eine Datenbanktabelle anlegen"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 1
summary: "Eine Migration, die eine eigene Tabelle der Site auf jeder Engine aus einer Definition im Format von share/db_schema.dba anlegt, eine erste Zeile schreibt und sie in down() wieder löscht - mit Probelauf, Lauf, Prüfung und Rücknahme."
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Tabelle anlegen, neue Tabelle, migration, migrations, schema change, database change, add table, create table, db_schema.dba, expKernelTableSchema, install, exp:migration:generate, exp:migration:migrate, exp:migration:rollback, MySQL, PostgreSQL, SQLite
featured: true
type: help
---
# Eine Datenbanktabelle anlegen

Dieses Rezept richtet sich an Entwickler, die für eine Site eine eigene Tabelle brauchen: eine Liste von
Nachrichtenquellen, ein Protokoll, eine Zuordnung alter zu neuen Adressen. Die Tabelle wird von einer Migration
angelegt, sodass jede Installation der Site (Entwicklung, Staging, Produktion) dieselbe Tabelle in derselben
Reihenfolge bekommt und die Aufzeichnung festhält, wo sie schon existiert. Das Beispiel legt `exp_news_source` an
und schreibt ihre erste Zeile.

## Was Sie am Ende haben

- eine Migration `migrations/20261101090000_news_source_table.php` im Git-Repository der Site;
- die Tabelle `exp_news_source` mit fünf Spalten und einem eindeutigen Index, auf der Engine, die die Installation
  nutzt (MySQL/MariaDB, PostgreSQL, SQLite, Oracle; auf MongoDB eine Collection);
- die Migration als `done` aufgezeichnet, einen Sicherungspunkt vor dem Lauf und ein `down()`, das die Tabelle
  wieder löscht.

## Bevor Sie beginnen

- Exponential 6.0.15 mit den Aufzeichnungstabellen: `./console exp:upgrade:status` zeigt
  `6.0.15-migration-tables` als `done` (sonst zuerst `./console exp:upgrade --apply`).
- Eine Entwicklungsinstallation und ein Git-Checkout der Site. Beim ersten Versuch geben Sie dem Lauf `--no-commit`
  mit, damit nichts committet oder gepusht wird.
- Führen Sie jeden Befehl im Wurzelverzeichnis der Installation als der Benutzer aus, unter dem der Webserver läuft
  (als root mit `--allow-root-user`).

## Schritte

### 1. Das Gerüst erzeugen

```bash
./console exp:migration:generate --skeleton news_source_table --description="The table exp_news_source: where the news articles come from" --author="Site team" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261101090000_news_source_table.php
Migration 20261101090000_news_source_table, class \Site\Migrations\NewsSourceTable20261101090000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

Der Dateiname beginnt mit der aktuellen Zeit; Ihr Zeitstempel weicht ab.

### 2. Die Tabelle beschreiben und die Methoden füllen

Ersetzen Sie den Rumpf der Klasse durch die Migration unten. Die Tabelle wird im Format von `share/db_schema.dba`
beschrieben, demselben Format wie die Tabellen des Kernels, und `expKernelTableSchema::install()` schreibt das SQL
der Engine, die die Installation nutzt. Alles läuft über `$context->db()`, damit die Transaktion des Laufs gilt.

### 3. Die Datei prüfen

```bash
./console exp:migration:check
```

```text
Exponential migrations: check
  site                     migrations

  ok    20261101090000_news_source_table             php  migrations/20261101090000_news_source_table.php

1 migration(s) checked
PASS
```

### 4. Den Probelauf ansehen

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-090512-4b7e21, environment development, database sqlite (transactions: full)

  would-apply 20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              would create the table exp_news_source (5 columns, a unique index on identifier)
              would write the source "newsroom"

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

Auf PostgreSQL und SQLite können Sie sie auch wirklich in einer Transaktion ausführen, die danach zurückgerollt wird:
`./console exp:migration:dry-run --simulate --diff` zeigt `+ table exp_news_source (5 columns, 1 row)`.

### 5. Ausführen

```bash
./console exp:migration:migrate --apply --no-commit
```

```text
Exponential migrations: run
Run 20261101-090631-a90c3e, environment development, database sqlite (transactions: full), as admin

  applied     20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              created exp_news_source
              wrote the source newsroom

Captions: pre 20261101-090631, post 20261101-090632
Repository: no commit, push disabled
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 applied
PASS
```

Committen Sie die Migrationsdatei mit Ihrer Änderung. Auf Staging und Produktion läuft dieselbe Datei mit
`./console exp:migration:migrate --apply`, und dort wird die Aufzeichnung des Laufs committet, getaggt und gepusht.

## Die Migration

```php
<?php
// description: The table exp_news_source: where the news articles come from
// author: Site team
namespace Site\Migrations;

final class NewsSourceTable20261101090000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_source';

    /** @return array the table in the format of share/db_schema.dba */
    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'identifier' => array( 'length' => 64, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'name' => array( 'length' => 150, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'url' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'created' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
            ),
            'indexes' => array(
                'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                'exp_news_source_identifier' => array( 'type' => 'unique', 'fields' => array( 'identifier' ) ),
            ),
        ) );
    }

    public function plan( $context )
    {
        return array( 'create the table ' . self::TABLE . ' (5 columns, a unique index on identifier)',
                      'write the source "newsroom"' );
    }

    public function detect( $context )
    {
        // a table made by hand on one installation counts as done there
        return \expKernelTableSchema::exists( self::TABLE, $context->db(), true ) === true ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $messages = array();
        if ( !\expKernelTableSchema::install( array( self::TABLE ), $context->db(), self::schema(), $messages ) )
            throw new \RuntimeException( 'the table could not be created: ' . implode( '; ', $messages ) );
        $context->db()->query( 'INSERT INTO ' . self::TABLE . " ( id, identifier, name, url, created ) VALUES ( 1, 'newsroom', 'Newsroom', '', " . time() . ' )' );
        return array( 'created ' . self::TABLE, 'wrote the source newsroom' );
    }

    public function down( $context )
    {
        $context->db()->query( 'DROP TABLE ' . self::TABLE );
        \expKernelTableSchema::reset();
        return array( 'dropped ' . self::TABLE );
    }
}
```

- `detect()` lässt die Migration auf einer Installation als erledigt gelten, auf der jemand die Tabelle von Hand
  angelegt hat; dort läuft sie nie doppelt.
- Geben Sie neuen Tabellen das Präfix `exp` oder ein eigenes Präfix der Site, nie `ez`.
- Auf MySQL/MariaDB und Oracle wird ein `CREATE TABLE` sofort committet, außerhalb der Transaktion der Migration;
  schlüge das `INSERT` fehl, bliebe die Tabelle stehen. Der vor dem Lauf genommene Sicherungspunkt wird nach einem
  Fehler wiederhergestellt und deckt das ab.
- Auf MongoDB legt `install()` eine Collection an; `DROP TABLE` in `down()` gilt dort nicht, nehmen Sie einen Lauf
  auf MongoDB daher mit seinem Sicherungspunkt zurück.

## Prüfen

```bash
./console exp:migration:status
```

```text
  done        20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              recorded done by run 20261101-090631-a90c3e on 2026-11-01T09:06:31-07:00
```

Sehen Sie sich die Tabelle in Ihrem Datenbankwerkzeug an, oder in Setup > Migrationen (der Status und die Seite des
Laufs). `./console exp:migration:migrate` zeigt die Migration nun als `done` und ändert nichts.

## Zurücknehmen

Nehmen Sie diese eine Migration mit ihrem `down()` zurück:

```bash
./console exp:migration:rollback 20261101090000_news_source_table          # Probelauf
./console exp:migration:rollback 20261101090000_news_source_table --apply
```

```text
  undone      20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              dropped exp_news_source
```

Die Aufzeichnung der Migration wird entfernt, und sie ist wieder ausstehend. Um stattdessen die ganze Installation
auf den Stand vor dem Lauf zu bringen, stellen Sie den Sicherungspunkt des Laufs wieder her:
`./console exp:migration:rollback 20261101-090631-a90c3e --apply`.

## Nächster Schritt

Geben Sie der Tabelle in einer zweiten Migration eine neue Spalte und einen Index:
[Eine Spalte oder einen Index hinzufügen](eine-spalte-oder-einen-index-hinzufuegen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Schemaänderungen und Migrationen](../../softwareingenieure/schemaaenderungen-und-migrationen.md), [Hinweise zu den Datenbanken](hinweise-zu-den-datenbanken.md), [Ein umkehrbares down() schreiben](ein-umkehrbares-down-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
