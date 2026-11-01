---
title: "Large data migrations: batches, time limits and resume"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 17
summary: Change thousands of objects or rows in one migration without running out of memory or locking the site - batches, transactional() returning false, an idempotent up(), pauses, maintenance mode, and resuming a run that was interrupted.
keywords: migration, migrations, large data, bulk, batch, batches, memory, time limit, resume, interrupted, idempotent, transactional, maintenance mode, throttle, schema change, database change, deploy, exp:migration:resume, subTreeByNodeID
type: help
---
# Large data migrations: batches, time limits and resume

Most migrations finish in milliseconds. A migration that touches every news article of a large site does not: it
can take minutes or hours, hold a transaction open for all of it, and use more memory than PHP allows. This recipe
writes such a migration in batches, makes it safe to run again, and resumes it after an interruption.

## What you'll have at the end

- a migration that works through the news articles 200 at a time, committing each batch;
- an `up()` that skips what is already done, so a second run only finishes the rest;
- a run that can be interrupted and resumed with `exp:migration:resume --apply`.

## Before you start

- Estimate the size: `./console exp:migration:migrate` lists the plan, and this migration's `plan()` counts the
  objects.
- Know your engine: on PostgreSQL and SQLite a migration in one transaction is undone completely on failure; a
  batched migration gives that up on purpose ([Engine notes](engine-notes.md)).

## Steps

### 1. Commit batch by batch

The runner wraps each migration in a transaction where the engine has one. For thousands of objects that is one
huge transaction. Return `false` from `transactional()` and the migration commits as it goes, and the kernel
functions it calls (`updateAndPublishObject()`) commit their own work. The price: a failure keeps the batches before
it, so `up()` must be safe to run again.

### 2. Make up() idempotent

Decide per object whether it still needs the change, from the object itself. Here: only an article whose summary is
empty is published again. A second run looks at everything and changes only what the first did not reach.

### 3. Keep memory flat

Load one batch with `Limit` and `Offset`, and empty the object cache after each with `eZContentObject::clearCache()`.
On the command line PHP has no time limit by default (`max_execution_time` is 0 for the CLI), so the length of the
run is not the problem; memory is.

### 4. Give the server air

A pause between batches (`usleep()`) keeps the database and the search index from being swamped while visitors use
the site. `migration.ini [RunSettings] Throttle` is shipped empty and is not read by the 6.0.15 runner, so pause in
your own loop.

## The migration

```php
<?php
// description: Every news article without a summary gets "Read more" (in batches)
// author: Site team
namespace Site\Migrations;

final class FillNewsSummaries20261101120000 extends \expMigrationPhp
{
    const PARENT_NODE = 2;
    const BATCH = 200;
    const PAUSE_MICROSECONDS = 200000;

    /** Commits batch by batch: a failure keeps the batches before it, and a second run skips them */
    public function transactional()
    {
        return false;
    }

    public function plan( $context )
    {
        $count = \eZContentObjectTreeNode::subTreeCountByNodeID( self::filter(), self::PARENT_NODE );
        return array( "look at $count news articles in batches of " . self::BATCH . ' and fill the empty summaries' );
    }

    public function up( $context )
    {
        $offset = 0;
        $seen = 0;
        $filled = 0;
        while ( true )
        {
            $nodes = \eZContentObjectTreeNode::subTreeByNodeID( self::filter() + array(
                'Limit' => self::BATCH, 'Offset' => $offset, 'SortBy' => array( 'node_id', true ) ), self::PARENT_NODE );
            if ( !$nodes )
                break;
            foreach ( $nodes as $node )
            {
                $object = $node->attribute( 'object' );
                $dataMap = $object->dataMap();
                $seen++;
                if ( !isset( $dataMap['summary'] ) || trim( $dataMap['summary']->toString() ) !== '' )
                    continue;
                if ( !\eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => array( 'summary' => 'Read more' ) ), false ) )
                    throw new \RuntimeException( 'object ' . $object->attribute( 'id' ) . ' could not be published' );
                $filled++;
            }
            $context->log( 'info', "offset $offset: $seen looked at, $filled filled" );
            $offset += self::BATCH;
            \eZContentObject::clearCache();
            usleep( self::PAUSE_MICROSECONDS );
        }
        return array( "looked at $seen news articles", "filled $filled summaries" );
    }

    private static function filter()
    {
        return array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'news_article' ),
                      'MainNodeOnly' => true, 'IgnoreVisibility' => true );
    }
}
```

The last argument `false` of `updateAndPublishObject()` publishes without notifications, so subscribers get no mail
for a technical change. `$context->log()` writes each batch to the run log, so a long run can be followed.

### 5. Run it

```text
Exponential migrations: run
Run 20261101-150000-6c2f90, environment production, database mysql (transactions: data), as admin

  applied     20261101120000_fill_news_summaries           site               Every news article without a summary gets "Read more" (in batches)
              looked at 18400 news articles
              filled 3127 summaries

Captions: pre 20261101-145952, post 20261101-153411
Repository: commit 9f0c3b7e21d4, tag migrations/production/20261101-150000-6c2f90, push pushed
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 applied
PASS
```

Started from Setup > Migrations, the same run goes on in the background when the page is closed, and its page shows
the progress.

### 6. Resume after an interruption

When the process is killed (a deploy, a reboot), the run's record still says `running` but no process holds the
lock. `exp:migration:resume` finds it:

```bash
./console exp:migration:resume            # what it would run
./console exp:migration:resume --apply    # close the old run as failed, run what is pending in a new run
```

Because `up()` is idempotent, the new run skips the articles already done.

## Split rather than grow

Several migrations of a moderate size are easier to follow, resume and review than one that does everything. Order
them with `requires:`. Mark one `maintenance: yes` when visitors must not see a half-done state; maintenance mode is
then on while that migration runs ([Maintenance mode](../../../guides/maintenance-mode.md)).

## Check it

- `./console exp:migration:dry-run --simulate --diff` on a SQLite copy with a few hundred articles shows the rows
  that change.
- Run it twice on a development copy: the second run fills 0 summaries.

## Roll it back

A batched migration rarely has a `down()`: the old values are gone. The way back is the caption taken before the
run: `./console exp:migration:rollback 20261101-150000-6c2f90 --apply`. On MySQL/MariaDB, Oracle and MongoDB that
restore is also what undoes a failure halfway, because the committed batches stay.

## Next step

[Engine notes](engine-notes.md): what each database can and cannot undo.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Change content in bulk](change-content-in-bulk.md), [exp:migration:resume](../../../examples/commands/expmigrationresume.md),
  [migration.ini \[RunSettings\]](../../../examples/ini-settings/migration/RunSettings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
