---
title: "Migrations on MySQL, PostgreSQL, SQLite, Oracle and MongoDB"
audience: developer, software-engineer, dba, hosting
category: migrations
order: 18
summary: What each database engine means for a migration - transactions, the simulation, captions and the restore, schema changes that commit at once - and how to write one migration that works on MySQL/MariaDB, PostgreSQL, SQLite, Oracle and MongoDB.
keywords: migration, migrations, schema change, database change, engines, MySQL, MariaDB, PostgreSQL, SQLite, Oracle, ezoracle, MongoDB, transactions, DDL, engines header, portable SQL, schema handler
type: help
---
# Migrations on MySQL, PostgreSQL, SQLite, Oracle and MongoDB

Exponential runs on five database engines, and a migration runs on the one the installation uses. The engine decides
how much of a failed migration the runner can undo by itself, whether a run can be simulated, and how a caption is
taken and restored. This page lists the differences and shows a migration that adds a column on every SQL engine.

## What you'll have at the end

- a table you can check before you choose where a migration is first run;
- a migration that adds the column `exp_news_note.priority` on MySQL/MariaDB, PostgreSQL, SQLite and Oracle, and is
  `not-needed` on MongoDB;
- the habits that keep a migration portable.

## Before you start

- Know your engine: `./console exp:migration:status` prints it in its second line
  (`database sqlite (transactions: full)`).
- Read [Add a column or an index](add-a-column-or-an-index.md) if you have not added a column in a migration yet.

## The engines at a glance

| Engine | Transactions (`transactions:` in the output) | `--simulate` | Caption | Restore after a failure |
|---|---|---|---|---|
| SQLite | `full`: schema and data are undone | yes | a consistent copy of the file | the copy written back |
| PostgreSQL | `full` | yes | `pg_dump --clean` | through `psql` |
| MySQL/MariaDB | `data`: a schema change (`CREATE`, `ALTER`, `DROP`) commits at once | no, the plan is listed with a warning | `mysqldump` | through `mysql` |
| Oracle (ezoracle) | `data` | no | none built in: export with `expdp` yourself | by hand; the run prints the steps |
| MongoDB | `none`: every write stays | no | `mongodump` | through the rollback of `exp:upgrade` |

The caption restored after a failure covers what a transaction cannot. On MySQL/MariaDB, Oracle and MongoDB that is
the only automatic way back, so keep the captions on there.

## Steps

### 1. Say which engines a migration is for

The header key `engines:` limits a migration to some engines. On any other engine it is `not-needed` and never runs:

```php
// engines: mysql, postgresql, sqlite, oracle
```

Leave it out when the migration works everywhere (content, roles and settings through the kernel's classes usually
do). Add it when the migration writes SQL or touches columns: MongoDB has collections, no columns and no SQL.

### 2. Ask the engine inside the migration

`$context->engine()` returns `mysql`, `postgresql`, `sqlite`, `oracle` or `mongodb`. Branch on it only where SQL
really differs; let the schema handler of the engine write the SQL wherever it can (step 3 of the migration below).

### 3. Order the work for engines that commit schema changes at once

On MySQL/MariaDB and Oracle a failed migration keeps its schema changes. Write the migration so that a second run
after a failure finds the half-done state and completes it: ask the catalog first (`detect()`, or
`\expUpgradeCatalog::hasColumn()` inside `up()`), and make one schema change per migration where you can.

### 4. Look at the run

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-090102-5d1e7a, environment development, database mysql (transactions: data)

  would-apply 20261101090000_news_note_priority             site               The column priority of exp_news_note
              would add the column exp_news_note.priority on mysql

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

On MongoDB the same migration shows `not-needed` and `Summary: ... 1 not needed`.

## The migration

```php
<?php
// description: The column priority of exp_news_note
// engines: mysql, postgresql, sqlite, oracle
// author: Site team
namespace Site\Migrations;

final class NewsNotePriority20261101090000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_note';
    const FIELD = 'priority';

    /** @return array the column in the format of share/db_schema.dba */
    private static function field()
    {
        return array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 );
    }

    public function detect( $context )
    {
        return \expUpgradeCatalog::hasColumn( $context->db(), self::TABLE, self::FIELD ) === true ? \expMigration::DONE : null;
    }

    public function plan( $context )
    {
        return array( 'add the column ' . self::TABLE . '.' . self::FIELD . ' on ' . $context->engine() );
    }

    public function up( $context )
    {
        $engine = $context->engine();
        $params = array( 'schema' => true, 'data' => false, 'allow_multi_insert' => false, 'diff_friendly' => false,
                         'table_type' => $engine === 'mysql' ? 'innodb' : '' );
        $schema = array( self::TABLE => array( 'name' => self::TABLE, 'fields' => array( self::FIELD => self::field() ),
                                               'indexes' => array() ) );
        $handler = \eZDbSchema::instance( array( 'type' => $engine, 'instance' => \expKernelTableSchema::nullDB(),
                                                 'schema' => $schema ) );
        if ( !$handler )
            throw new \RuntimeException( "no schema handler for $engine" );
        // PostgreSQL: the default and NOT NULL in the same statement, so the rows that exist are filled
        $sql = $engine === 'postgresql'
             ? 'ALTER TABLE ' . self::TABLE . ' ADD COLUMN '
               . $handler->generateFieldDef( self::TABLE, self::FIELD, self::field(), true, $params ) . ";\n"
             : $handler->generateAddFieldSql( self::TABLE, self::FIELD, self::field(), $params );
        $ran = \expUpgradeCatalog::execute( $context->db(), $sql );
        \expKernelTableSchema::reset();
        return array( 'added the column ' . self::TABLE . '.' . self::FIELD . ' (' . count( $ran ) . ' statement(s))' );
    }

    public function down( $context )
    {
        \expUpgradeCatalog::execute( $context->db(), 'ALTER TABLE ' . self::TABLE . ' DROP COLUMN ' . self::FIELD . ";\n" );
        \expKernelTableSchema::reset();
        return array( 'dropped the column ' . self::TABLE . '.' . self::FIELD );
    }
}
```

`\expUpgradeCatalog::execute()` runs the statements one by one and throws at the first that fails, so the runner sees
the failure. `ALTER TABLE ... DROP COLUMN` in `down()` works on MySQL/MariaDB, PostgreSQL, Oracle and SQLite 3.35 and
later.

## Portable habits

- Go through `$context->db()` and the kernel's helpers (`\expKernelTableSchema::install()` for tables, the schema
  handler for columns and indexes, `\expUpgradeCatalog` to ask the catalog). They write the SQL of each engine.
- Keep identifiers lower case and at most 30 characters (Oracle); do not use reserved words as column names.
- Quote values with `$context->db()->escapeString()`, cast numbers with `(int)`.
- Do not use `LIMIT` in `UPDATE` or `DELETE`, engine-only functions, or `SET @variable`; select the ids first and
  write them in batches.
- For MongoDB, write content, roles and settings through the kernel's classes, never SQL.
- Test on the engines you run in production. SQLite is the quickest place to simulate, but it accepts SQL that
  MySQL or PostgreSQL refuse.

## Check it

```bash
./console exp:migration:status
./console exp:upgrade:verify
```

`exp:migration:status` shows the migration `done` (or `not-needed` on MongoDB); `detect()` keeps it `done` even on an
installation where the column was added by hand.

## Roll it back

`./console exp:migration:rollback 20261101090000_news_note_priority --apply` runs `down()`. On Oracle, a failure
during a run is restored by hand: the report lists the steps (an `impdp` of the export you took before the run).

## Next step

[Captions and rollback in practice](captions-and-rollback-in-practice.md): what the captions of each engine hold and
how a run is taken back.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Add a column or an index](add-a-column-or-an-index.md), [Large data migrations](large-data-migrations.md)
- [Portable SQL for extensions](../../database-administrators/portable-sql-for-extensions.md),
  [Backups per engine](../../database-administrators/backups-per-engine.md),
  [Choosing an engine](../../database-administrators/choosing-an-engine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
