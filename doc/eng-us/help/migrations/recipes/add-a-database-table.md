---
title: "Add a database table"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 1
summary: "A migration that creates a table of the site's own on every engine from a share/db_schema.dba definition, writes a first row, and drops it again in down() - with the dry run, the run, the checks and the rollback."
keywords: migration, migrations, schema change, database change, add table, create table, new table, db_schema.dba, expKernelTableSchema, install, exp:migration:generate, exp:migration:migrate, exp:migration:rollback, MySQL, PostgreSQL, SQLite
featured: true
type: help
---
# Add a database table

This recipe is for developers who need a table of their own for a site: a list of news sources, a log, a mapping
of old to new addresses. The table is created by a migration, so every installation of the site (development,
staging, production) gets the same table in the same order, and the record says where it already exists. The
example creates `exp_news_source` and writes its first row.

## What you'll have at the end

- a migration `migrations/20261101090000_news_source_table.php` in the site's git repository;
- the table `exp_news_source` with five columns and a unique index, on whatever engine the installation runs
  (MySQL/MariaDB, PostgreSQL, SQLite, Oracle; a collection on MongoDB);
- the migration recorded as `done`, a caption taken before the run, and a `down()` that drops the table again.

## Before you start

- Exponential 6.0.15 with the record tables: `./console exp:upgrade:status` lists `6.0.15-migration-tables` as
  `done` (if not, run `./console exp:upgrade --apply`).
- A development installation and a git checkout of the site. On a first try add `--no-commit` to the run, so
  nothing is committed or pushed.
- Run every command from the installation root as the user the web server runs as (as root, add
  `--allow-root-user`).

## Steps

### 1. Generate the skeleton

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

The file name starts with the time of now; your timestamp differs.

### 2. Describe the table and fill in the methods

Replace the body of the class with the migration below. The table is described in the format of
`share/db_schema.dba`, the same format the kernel's own tables use, and `expKernelTableSchema::install()` writes
the SQL of the engine the installation runs. Everything goes through `$context->db()`, so the run's transaction is
the one used.

### 3. Check the file

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

### 4. Look at the dry run

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

On PostgreSQL and SQLite you can also run it for real in a transaction that is rolled back:
`./console exp:migration:dry-run --simulate --diff` shows `+ table exp_news_source (5 columns, 1 row)`.

### 5. Run it

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

Commit the migration file with your change. On staging and production the same file runs with
`./console exp:migration:migrate --apply`, and there the run's record is committed, tagged and pushed.

## The migration

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

- `detect()` makes the migration count as done on an installation where someone created the table by hand, so it is
  never run twice there.
- Name new tables with the `exp` prefix or your site's own prefix, never `ez`.
- On MySQL/MariaDB and Oracle a `CREATE TABLE` commits at once, outside the migration's transaction; if the `INSERT`
  failed, the table would stay. The caption taken before the run is restored after a failure and covers that.
- On MongoDB `install()` creates a collection; `DROP TABLE` in `down()` does not apply there, so take a MongoDB run
  back with its caption.

## Check it

```bash
./console exp:migration:status
```

```text
  done        20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              recorded done by run 20261101-090631-a90c3e on 2026-11-01T09:06:31-07:00
```

Look at the table with the database tool you use, or in Setup > Migrations (the status page and the page of the
run). `./console exp:migration:migrate` now lists the migration as `done` and changes nothing.

## Roll it back

Take this one migration back with its `down()`:

```bash
./console exp:migration:rollback 20261101090000_news_source_table          # dry run
./console exp:migration:rollback 20261101090000_news_source_table --apply
```

```text
  undone      20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              dropped exp_news_source
```

The record of the migration is removed and it is pending again. To return the whole installation to the state
before the run instead, restore the run's pre caption: `./console exp:migration:rollback 20261101-090631-a90c3e --apply`.

## Next step

Give the table a new column and an index in a second migration: [Add a column or an index](add-a-column-or-an-index.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Schema changes and migrations](../../software-engineers/schema-changes-and-migrations.md), [Engine notes](engine-notes.md), [Writing a reversible down()](writing-a-reversible-down.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
