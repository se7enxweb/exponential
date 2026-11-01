---
title: "Writing a reversible down()"
audience: developer, software-engineer, php-developer
category: migrations
order: 12
summary: How to write a down() that really undoes up() on every installation - idempotent, found by remote id or identifier, safe on every engine - when to leave it out, and how to prove it with up, down and up again.
keywords: migration, migrations, down, down(), reversible, rollback, undo, idempotent, remote id, detect, schema change, database change, deploy, exp:migration:rollback, caption
type: help
---
# Writing a reversible down()

`exp:migration:rollback <migration-id>` takes one migration back by running its `down()`. A good `down()` lets a
developer or an administrator undo one change without restoring a whole caption, which would also take back every
other change since. This recipe shows what makes a `down()` trustworthy and when to leave it out on purpose.

## What you'll have at the end

- a migration whose `down()` undoes exactly what `up()` did, on every installation and every supported engine;
- a test that runs `up()`, `down()` and `up()` again;
- a clear rule for migrations that are not reversible.

## Before you start

Read how the runner treats `down()` in [PHP migrations](../../../guides/migrations-php.md#down-and-reversible). In
short: a class that declares `down()` is reversible; the rollback refuses a migration that has no `down()` or is not
`done`; after a successful `down()` the record is removed and the migration is `pending` again.

## Steps

### 1. Undo what up() did, nothing more

`down()` mirrors `up()` in reverse order. If `up()` creates a table and writes a row, `down()` drops the table; if
`up()` adds a column, `down()` drops that column, not the table.

### 2. Find what you created by something stable

Object ids, node ids and attribute ids differ between installations. Give what you create a **remote id** or an
**identifier** in `up()` and look it up by that in `down()`: `eZContentObject::fetchByRemoteID()`,
`eZContentClass::fetchByIdentifier()`, `eZRole::fetchByName()`.

### 3. Make it safe to run twice

A `down()` that finds nothing to undo returns a message instead of failing. That keeps a rollback working after an
administrator removed something by hand.

### 4. Ask the database, not the record

`expUpgradeCatalog::hasColumn()`, `hasIndex()` and `expKernelTableSchema::exists()` tell what the database holds now.

## The migration

```php
<?php
// description: News notes get a priority (default 0)
// requires: 20261101090000_news_note_table
// engines: mysql, postgresql, sqlite
namespace Site\Migrations;

final class NewsNotePriority20261101110000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_note';
    const COLUMN = 'priority';

    public function plan( $context )
    {
        return array( 'add the column ' . self::TABLE . '.' . self::COLUMN . ' (integer, default 0)' );
    }

    public function up( $context )
    {
        $has = \expUpgradeCatalog::hasColumn( $context->db(), self::TABLE, self::COLUMN );
        if ( $has === null )
            throw new \RuntimeException( 'the table ' . self::TABLE . ' does not exist' );
        if ( $has )
            return array( self::TABLE . '.' . self::COLUMN . ' exists already' );
        \expUpgradeCatalog::execute( $context->db(),
            'ALTER TABLE ' . self::TABLE . ' ADD COLUMN ' . self::COLUMN . " INTEGER DEFAULT 0 NOT NULL;\n" );
        return array( 'added ' . self::TABLE . '.' . self::COLUMN );
    }

    public function down( $context )
    {
        if ( \expUpgradeCatalog::hasColumn( $context->db(), self::TABLE, self::COLUMN ) !== true )
            return array( self::TABLE . '.' . self::COLUMN . ' is not there: nothing to undo' );
        \expUpgradeCatalog::execute( $context->db(),
            'ALTER TABLE ' . self::TABLE . ' DROP COLUMN ' . self::COLUMN . ";\n" );
        return array( 'dropped ' . self::TABLE . '.' . self::COLUMN );
    }
}
```

`engines:` limits it to the engines whose SQL it writes; on Oracle and MongoDB it is `not-needed`. `DROP COLUMN`
needs SQLite 3.35 or later. `expUpgradeCatalog::execute()` runs each statement with errors raised as exceptions, so a
failing statement fails the migration instead of passing silently.

## When down() must not exist

Leave `down()` out when undoing would lose data the migration cannot bring back: content removed, values
overwritten, two fields merged into one. Then the rollback says so:

```text
Exponential migrations: rollback with down()
Run 20261101-111845-0c5d2e, environment development, database sqlite (transactions: full), as admin

  done        20261101113000_merge_news_teasers            site               The teaser text becomes part of the summary

Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown
FAIL: 20261101113000_merge_news_teasers cannot be undone by itself (no down()); restore the caption taken before it
FAIL (exit 1)
```

The way back is the caption taken before its run: `./console exp:migration:rollback <run-id> --apply`
([Captions and rollback in practice](captions-and-rollback-in-practice.md)). An empty `down()` that only returns a
message is worse than none: it removes the record and leaves the change in place.

## Check it

Run the three steps on a development installation:

```bash
./console exp:migration:migrate --apply --no-commit
./console exp:migration:rollback 20261101110000_news_note_priority --apply
./console exp:migration:migrate --apply --no-commit
```

```text
Exponential migrations: rollback with down()
Run 20261101-112010-9e3a41, environment development, database sqlite (transactions: full), as admin

  undone      20261101110000_news_note_priority            site               News notes get a priority (default 0)
              dropped exp_news_note.priority

Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 undone
PASS
```

The second `migrate` applies it again. Put the same sequence in a PHPUnit test
([Testing migrations](testing-migrations.md)).

## Roll it back

This page is about rolling back. Mind the order: roll back the migrations that require this one first. The
rollback does not check that for you; it only refuses a migration that is not `done` or has no `down()`. The command takes one
migration at a time, so take a chain back with one command per migration, the newest first.

## Next step

[Testing migrations](testing-migrations.md): the check, the simulation and a PHPUnit test.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Rollback and captions](../../../guides/migrations-rollback-and-captions.md), [exp:migration:rollback](../../../examples/commands/expmigrationrollback.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
