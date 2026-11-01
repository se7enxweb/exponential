---
title: "Eine Spalte oder einen Index hinzufügen"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 2
summary: "Eine Migration, die einer bestehenden Tabelle auf jeder SQL-Engine eine Spalte mit Vorgabewert und einen Index hinzufügt, über den Schema-Handler der Engine, die zuerst den Katalog fragt und so eine halb angewendete Änderung vervollständigt, und die beides in down() wieder entfernt."
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Spalte hinzufügen, Index anlegen, migration, migrations, schema change, database change, add column, alter table, add index, create index, expUpgradeCatalog, hasColumn, hasIndex, eZDbSchema, generateAddFieldSql, generateAddIndexSql, exp:migration:migrate
type: help
---
# Eine Spalte oder einen Index hinzufügen

Dieses Rezept richtet sich an Entwickler, die eine bestehende Tabelle ändern: eine neue Spalte, ein Index für eine
langsame Liste. Das SQL eines `ALTER TABLE` unterscheidet sich je Engine, deshalb lässt die Migration es vom
Schema-Handler der Engine schreiben (demselben Code, den die Upgrade-Schritte des Kernels nutzen) und fragt den
Katalog der Datenbank, was schon da ist, bevor sie etwas ändert. Das Beispiel fügt der Tabelle aus
[Eine Datenbanktabelle anlegen](eine-datenbanktabelle-anlegen.md) die Spalte `exp_news_source.priority` und den
Index `exp_news_source_priority` hinzu.

## Was Sie am Ende haben

- eine Migration `20261101100000_news_source_priority.php`, die nach der Migration der Tabelle läuft (`requires:`);
- die Spalte `priority` (`int`, `NOT NULL`, Vorgabe `0`) in jeder Zeile, auch den alten, und einen nicht eindeutigen
  Index darauf;
- ein `plan()`, das genau sagt, was fehlt, und ein `down()`, das Index und Spalte entfernt.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables` (`./console exp:upgrade:status`).
- Die Tabelle existiert: die Migration `20261101090000_news_source_table` ist `done` oder läuft im selben Lauf.
- Eine Entwicklungsinstallation und ein Git-Checkout; ausführen als Benutzer des Webservers (als root mit
  `--allow-root-user`).

## Schritte

### 1. Das Gerüst erzeugen

```bash
./console exp:migration:generate --skeleton news_source_priority --description="exp_news_source.priority and an index on it, for the ordered source list" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261101100000_news_source_priority.php
Migration 20261101100000_news_source_priority, class \Site\Migrations\NewsSourcePriority20261101100000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

### 2. Füllen

Ergänzen Sie die Kopfzeilen `requires:` und `engines:` und die Methoden unten. Nennen Sie keine Hilfsmethode
`definition()` oder `file()`: `expMigrationPhp` hat Methoden dieser Namen. `exp:migration:check` (und jeder Lauf)
meldet eine solche Methode mit Namen, statt die Klasse zu laden.

### 3. Prüfen und den Probelauf ansehen

```bash
./console exp:migration:check
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-100214-77d0b2, environment development, database postgresql (transactions: full)

  done        20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
  would-apply 20261101100000_news_source_priority          site               exp_news_source.priority and an index on it, for the ordered source list
              would add the column exp_news_source.priority (int, default 0)
              would add the index exp_news_source_priority

Captions: pre would-take, post would-take
Summary: 2 migration(s): 1 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

### 4. Simulieren (PostgreSQL und SQLite)

```bash
./console exp:migration:dry-run --simulate --diff
```

```text
  simulated   20261101100000_news_source_priority          site               exp_news_source.priority and an index on it, for the ordered source list
              simulation passed in 12 ms
              added the column exp_news_source.priority
              added the index exp_news_source_priority
              ~ table exp_news_source: columns 5 -> 6
```

Auf MySQL/MariaDB und Oracle wird nicht simuliert, weil ein `ALTER TABLE` dort sofort committet wird; der Befehl
listet den Plan mit einer Warnung.

### 5. Ausführen

```bash
./console exp:migration:migrate --apply
```

```text
  applied     20261101100000_news_source_priority          site               exp_news_source.priority and an index on it, for the ordered source list
              added the column exp_news_source.priority
              added the index exp_news_source_priority
```

## Die Migration

```php
<?php
// description: exp_news_source.priority and an index on it, for the ordered source list
// requires: 20261101090000_news_source_table
// engines: mysql, postgresql, sqlite, oracle
// author: Site team
namespace Site\Migrations;

final class NewsSourcePriority20261101100000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_source';
    const COLUMN = 'priority';
    const INDEX = 'exp_news_source_priority';

    /** @return array the column and the index in the format of share/db_schema.dba */
    private static function schemaOf()
    {
        return array(
            'name' => self::TABLE,
            'fields' => array(
                self::COLUMN => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
            ),
            'indexes' => array(
                self::INDEX => array( 'type' => 'non-unique', 'fields' => array( self::COLUMN ) ),
            ),
        );
    }

    /** @return \eZDBSchemaInterface a schema handler that writes the SQL of the run's engine */
    private static function handler( $engine )
    {
        $handler = \eZDbSchema::instance( array( 'type' => $engine, 'instance' => \expKernelTableSchema::nullDB(),
                                                 'schema' => array( self::TABLE => self::schemaOf() ) ) );
        if ( !$handler )
            throw new \RuntimeException( "no schema handler for $engine" );
        return $handler;
    }

    private static function params( $engine )
    {
        return array( 'schema' => true, 'data' => false, 'allow_multi_insert' => false, 'diff_friendly' => false,
                      'table_type' => $engine === 'mysql' ? 'innodb' : '' );
    }

    public function plan( $context )
    {
        $plan = array();
        if ( \expUpgradeCatalog::hasColumn( $context->db(), self::TABLE, self::COLUMN ) !== true )
            $plan[] = 'add the column ' . self::TABLE . '.' . self::COLUMN . ' (int, default 0)';
        if ( \expUpgradeCatalog::hasIndex( $context->db(), self::TABLE, array( self::COLUMN ) ) !== true )
            $plan[] = 'add the index ' . self::INDEX;
        return $plan ? $plan : array( 'nothing: the column and the index are there' );
    }

    public function up( $context )
    {
        $db = $context->db();
        $engine = $context->engine();
        $handler = self::handler( $engine );
        $params = self::params( $engine );
        $definition = self::schemaOf();
        $done = array();
        if ( \expUpgradeCatalog::hasColumn( $db, self::TABLE, self::COLUMN ) !== true )
        {
            $field = $definition['fields'][self::COLUMN];
            // PostgreSQL: default and NOT NULL in one statement, so the rows that exist get the default
            $sql = $engine === 'postgresql'
                 ? 'ALTER TABLE ' . self::TABLE . ' ADD COLUMN ' . $handler->generateFieldDef( self::TABLE, self::COLUMN, $field, true, $params ) . ";\n"
                 : $handler->generateAddFieldSql( self::TABLE, self::COLUMN, $field, $params );
            \expUpgradeCatalog::execute( $db, $sql );
            $done[] = 'added the column ' . self::TABLE . '.' . self::COLUMN;
        }
        if ( \expUpgradeCatalog::hasIndex( $db, self::TABLE, array( self::COLUMN ) ) !== true )
        {
            \expUpgradeCatalog::execute( $db, $handler->generateAddIndexSql( self::TABLE, self::INDEX, $definition['indexes'][self::INDEX], $params ) );
            $done[] = 'added the index ' . self::INDEX;
        }
        return $done;
    }

    public function down( $context )
    {
        $db = $context->db();
        $handler = self::handler( $context->engine() );
        $params = self::params( $context->engine() );
        \expUpgradeCatalog::execute( $db, $handler->generateDropIndexSql( self::TABLE, self::INDEX, self::schemaOf()['indexes'][self::INDEX], $params ) );
        \expUpgradeCatalog::execute( $db, $handler->generateDropFieldSql( self::TABLE, self::COLUMN, $params ) );
        return array( 'dropped the index ' . self::INDEX, 'dropped the column ' . self::TABLE . '.' . self::COLUMN );
    }
}
```

So funktioniert sie:

- `expUpgradeCatalog::hasColumn()` und `hasIndex()` lesen den Katalog der Datenbank. Sie liefern `true`, `false`
  oder `null`, wenn die Tabelle fehlt; die Migration behandelt alles außer `true` als fehlend.
- Weil `up()` nur Fehlendes hinzufügt, vervollständigt ein Lauf, der nach der Spalte und vor dem Index scheiterte,
  die Änderung beim nächsten Mal, statt an „Spalte existiert“ zu scheitern.
- PostgreSQL bekommt Vorgabewert und `NOT NULL` in derselben Anweisung, damit bestehende Zeilen gefüllt werden; das
  eigene `generateAddFieldSql()` des Handlers setzte `NOT NULL` erst danach, was auf einer Tabelle mit Zeilen
  scheitert.
- Der Kopf `engines: mysql, postgresql, sqlite, oracle` lässt MongoDB aus: Dort gibt es keine Spalten, und die
  Migration hat den Zustand `not-needed`.
- Tragen Sie die Spalte auch in das `share/db_schema.dba` Ihrer Erweiterung oder Site ein, wenn daraus neue
  Installationen entstehen, damit neue und aktualisierte Datenbanken gleich sind.

## Prüfen

```bash
./console exp:migration:status
```

Die Migration ist `done`. Sehen Sie sich dann die Tabelle in Ihrem Datenbankwerkzeug an: `priority` ist da, mit `0`
in jeder Zeile, und der Index ist gelistet. Auf MySQL/MariaDB wird eine große Tabelle gesperrt, während die Spalte
hinzukommt; führen Sie solch eine Migration in einer ruhigen Stunde aus und geben Sie ihr den Kopf
`maintenance: yes`, wenn Besucher die Site in der Zeit nicht sehen sollen.

## Zurücknehmen

```bash
./console exp:migration:rollback 20261101100000_news_source_priority --apply
```

```text
  undone      20261101100000_news_source_priority          site               exp_news_source.priority and an index on it, for the ordered source list
              dropped the index exp_news_source_priority
              dropped the column exp_news_source.priority
```

SQLite entfernt eine Spalte mit `ALTER TABLE ... DROP COLUMN` ab Version 3.35; auf einem älteren SQLite nehmen Sie
den Lauf mit seinem Sicherungspunkt zurück: `./console exp:migration:rollback <run-id> --apply`.

## Nächster Schritt

Ändern Sie das Inhaltsmodell auf dieselbe Weise:
[Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Eine Datenbanktabelle anlegen](eine-datenbanktabelle-anlegen.md), [Hinweise zu den Datenbanken](hinweise-zu-den-datenbanken.md), [Upgrade-Schritte für Erweiterungen](../../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
