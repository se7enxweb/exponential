---
title: "Add a column or an index"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 2
summary: "A migration that adds a column with a default and an index to an existing table on every SQL engine, through the engine's own schema handler, checks the catalog first so it completes a half-applied change, and drops both in down()."
keywords: migration, migrations, schema change, database change, add column, alter table, add index, create index, expUpgradeCatalog, hasColumn, hasIndex, eZDbSchema, generateAddFieldSql, generateAddIndexSql, PostgreSQL default, exp:migration:migrate
type: help
---
# Add a column or an index

This recipe is for developers who change a table that exists already: a new column, an index for a slow list. The
SQL of an `ALTER TABLE` differs per engine, so the migration asks the engine's schema handler (the same code the
kernel's upgrade steps use) to write it, and asks the database catalog what is there before it changes anything.
The example adds `exp_news_source.priority` and the index `exp_news_source_priority` to the table of
[Add a database table](add-a-database-table.md).

## What you'll have at the end

- a migration `20261101100000_news_source_priority.php` that runs after the table's migration (`requires:`);
- the column `priority` (`int`, `NOT NULL`, default `0`) on every row, old rows included, and a non-unique index on it;
- a `plan()` that tells exactly what is missing, and a `down()` that drops the index and the column.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done (`./console exp:upgrade:status`).
- The table exists: the migration `20261101090000_news_source_table` is `done`, or comes in the same run.
- A development installation and a git checkout; run as the web server user (as root, add `--allow-root-user`).

## Steps

### 1. Generate the skeleton

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

### 2. Fill it in

Add the header lines `requires:` and `engines:` and the methods below. Do not name a helper method
`definition()` or `file()`: `expMigrationPhp` has methods of those names. `exp:migration:check` (and every run)
reports such a method by name instead of loading the class.

### 3. Check and look at the dry run

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

### 4. Simulate it (PostgreSQL and SQLite)

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

On MySQL/MariaDB and Oracle the simulation is not run, because an `ALTER TABLE` commits at once there; the command
lists the plan with a warning.

### 5. Run it

```bash
./console exp:migration:migrate --apply
```

```text
  applied     20261101100000_news_source_priority          site               exp_news_source.priority and an index on it, for the ordered source list
              added the column exp_news_source.priority
              added the index exp_news_source_priority
```

## The migration

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

How it works:

- `expUpgradeCatalog::hasColumn()` and `hasIndex()` read the database catalog. They return `true`, `false`, or
  `null` when the table does not exist; the migration treats anything but `true` as missing.
- Because `up()` adds only what is missing, a run that failed after the column and before the index completes the
  change the next time instead of failing on "column exists".
- PostgreSQL gets the default and `NOT NULL` in the same statement, so the rows that exist are filled; the handler's
  own `generateAddFieldSql()` would set `NOT NULL` afterwards, which fails on a table with rows.
- The header `engines: mysql, postgresql, sqlite, oracle` leaves MongoDB out: it has no columns, and there the
  migration has the state `not-needed`.
- Add the column to your extension's or site's `share/db_schema.dba` too, if a new installation is created from it,
  so a new database and an upgraded one end up the same.

## Check it

```bash
./console exp:migration:status
```

The migration is `done`. Then look at the table in your database tool: `priority` is there with `0` in every row, and the index is listed.
On MySQL/MariaDB a large table is locked while the column is added; run such a migration in a quiet hour and give
the header `maintenance: yes` if visitors must not see the site meanwhile.

## Roll it back

```bash
./console exp:migration:rollback 20261101100000_news_source_priority --apply
```

```text
  undone      20261101100000_news_source_priority          site               exp_news_source.priority and an index on it, for the ordered source list
              dropped the index exp_news_source_priority
              dropped the column exp_news_source.priority
```

SQLite drops a column with `ALTER TABLE ... DROP COLUMN` from version 3.35 on; on an older SQLite take the run back
with its caption instead: `./console exp:migration:rollback <run-id> --apply`.

## Next step

Change the content model in the same way: [Create or change a content class](create-or-change-a-content-class.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Add a database table](add-a-database-table.md), [Engine notes](engine-notes.md), [Upgrade steps for extensions](../../../guides/upgrade-steps-for-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
