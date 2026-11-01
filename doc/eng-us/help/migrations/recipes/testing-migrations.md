---
title: "Testing migrations: check, simulate, diff and CI"
audience: developer, software-engineer, php-developer
category: migrations
order: 13
summary: Four layers of tests for migrations - the lint without a database, the dry run, the simulation with a diff on PostgreSQL or SQLite, and a PHPUnit test on a throwaway SQLite database - and how to run them in CI.
keywords: migration, migrations, test, testing, PHPUnit, CI, continuous integration, simulate, simulation, diff, dry run, lint, SQLite, schema change, database change, deploy, exp:migration:check, exp:migration:dry-run, --simulate, --diff, --fail-on-pending
featured: true
type: help
---
# Testing migrations: check, simulate, diff and CI

A migration runs once per installation and is hard to take back once production has it. Test it before it gets
there. This recipe shows four layers, from the cheapest to the most thorough, and how to wire them into CI.

## What you'll have at the end

- `exp:migration:check` in every pull request, with no database;
- a dry run and a simulation with a diff on a development copy;
- a PHPUnit test that runs the site's migrations up, down and up again on a throwaway SQLite database.

## Before you start

- The migrations of the [table](add-a-database-table.md) and [reversible down()](writing-a-reversible-down.md)
  recipes, or your own.
- For the simulation: a development installation on SQLite or PostgreSQL. MySQL/MariaDB, Oracle and MongoDB cannot
  simulate, because a schema change commits at once there.

## Steps

### 1. The lint: exp:migration:check

```bash
./console exp:migration:check
./console exp:migration:check --path=migrations/20261101110000_news_note_priority.php
```

```text
Exponential migrations: check
  site                     migrations
  extension:expnews        extension/expnews/migrations

  ok    20261101090000_news_note_table               php  migrations/20261101090000_news_note_table.php
  ok    20261101100000_news_source_table             php  extension/expnews/migrations/20261101100000_news_source_table.php
  ok    20261101110000_news_note_priority            php  migrations/20261101110000_news_note_priority.php

3 migration(s) checked
PASS
```

It needs no database and runs nothing: file names, header keys, the order for every environment, requirements,
cycles, duplicate ids, the PHP syntax, and that the file declares exactly one class extending `\expMigrationPhp`.
Any problem is exit 1.

### 2. The dry run

```bash
./console exp:migration:migrate
```

lists each pending migration with its `plan()`. It also warns when `settings/`, `extension/` or `design/` have
changes that are not committed, which a run with `--apply` would refuse.

### 3. The simulation with a diff

```bash
./console exp:migration:dry-run --simulate --diff
```

```text
Exponential migrations: simulation, the database is rolled back afterwards
Run 20261101-113402-5d0b7e, environment development, database sqlite (transactions: full), as admin

  simulated   20261101110000_news_note_priority            site               News notes get a priority (default 0)
              would add the column exp_news_note.priority (integer, default 0)
              simulation passed in 4 ms
              added exp_news_note.priority
              ~ table exp_news_note: columns +priority

Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 simulated
WARN simulated in a transaction that was rolled back: the database is unchanged, but what the migrations wrote outside it (files, caches, the search index, mail) is not undone
PASS
```

The migrations run for real inside one transaction that is rolled back. Read the diff: a table you did not expect
to change is a bug. Mind the warning: run simulations on a development copy, never on production.

### 4. A PHPUnit test on a throwaway SQLite database

The kernel tests its runner the same way (`tests/tests/kernel/classes/migration/expMigrationRunnerSqliteTest.php`):
load the kernel schema and the clean data into a new SQLite file, find and load the site's migrations, run them.

```php
<?php
/**
 * The site's migrations on a throwaway SQLite database: forward, back and forward again.
 * Run: php vendor/bin/phpunit tests/site/SiteMigrationsTest.php
 */
class SiteMigrationsTest extends PHPUnit\Framework\TestCase
{
    /** @var eZSQLite3DB */
    private $db;

    /** @var string */
    private $file;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->file = sys_get_temp_dir() . '/site-migrations-' . getmypid() . '.sqlite';
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->file,
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( 'kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
    }

    protected function tearDown(): void
    {
        $this->db->close();
        if ( is_file( $this->file ) )
            unlink( $this->file );
    }

    private function migrations()
    {
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => 'migrations' ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        return $migrations;
    }

    private function context( $apply = true )
    {
        return new expMigrationContext( $this->db, $apply, array( 'environment' => 'testing', 'no-mirror' => true,
                                                                  'user' => eZUser::fetchByName( 'admin' ) ) );
    }

    public function testUpDownUp()
    {
        $report = ( new expMigrationRunner( $this->context() ) )->run( $this->migrations() );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertTrue( expUpgradeCatalog::hasColumn( $this->db, 'exp_news_note', 'priority' ) );

        foreach ( array_reverse( $this->migrations() ) as $migration )
            if ( $migration->reversible() )
                $migration->down( $this->context() );
        $this->assertFalse( expUpgradeCatalog::hasColumn( $this->db, 'exp_news_note', 'priority' ) );
    }

    public function testPlanWritesNothing()
    {
        foreach ( $this->migrations() as $migration )
            $this->assertNotEmpty( $migration->plan( $this->context( false ) ) );
        $this->assertNull( expUpgradeCatalog::hasColumn( $this->db, 'exp_news_note', 'priority' ) );
    }
}
```

`'no-mirror' => true` keeps the test from writing a status file into the repository. Run it from the installation
root. Keep `plan()` pure so it can be tested without a database at all.

### 5. In CI

| Job | Command | Needs |
|---|---|---|
| every pull request | `./console exp:migration:check` | PHP and the code, no database |
| every pull request | `php vendor/bin/phpunit tests/site/SiteMigrationsTest.php` | the SQLite3 extension |
| before a deploy | `./console exp:migration:status --fail-on-pending` on the target | the target's database: exit 1 tells the pipeline that migrations wait |

A complete workflow file is in [Deploying with migrations](deploying-with-migrations.md).

## Check it

All four layers pass, and the PHPUnit test fails when you break `down()` on purpose (remove the `DROP COLUMN`).

## Next step

Let a second pair of eyes look at it: [Reviewing a migration](reviewing-a-migration.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [exp:migration:check](../../../examples/commands/expmigrationcheck.md), [exp:migration:dry-run](../../../examples/commands/expmigrationdryrun.md),
  [Engine notes](engine-notes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
