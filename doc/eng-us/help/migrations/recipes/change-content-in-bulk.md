---
title: "Change content in bulk"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 5
summary: "A migration that walks every object of a class in batches, publishes a new version of each one that needs the change, commits per object, logs its progress and is safe to run again - the pattern for any change to many objects."
keywords: migration, migrations, bulk change, bulk update, many objects, batch, batches, subTreeByNodeID, updateAndPublishObject, transactional, clearCache, new version, content change, exp:migration:migrate, exp:migration:resume
featured: true
type: help
---
# Change content in bulk

This recipe is for developers who must change many objects at once: fill a new field, correct a value an import
got wrong, move a setting from text into an attribute. Done by hand in the administration it is slow and differs
per installation; done in a migration it is written once, reviewed, and applied the same way on staging and
production. The example gives every news article without a source the source "Newsroom".

## What you'll have at the end

- the migration `20261101130000_news_article_default_source.php`;
- a new published version of every news article whose Source was empty, with the earlier version kept in its
  history;
- a migration that commits per object, writes its progress to the run log, and changes nothing on a second run.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done; the class `news_article`
  ([Create or change a content class](create-or-change-a-content-class.md)).
- A copy of production data on a development or staging installation, so you see how long the run takes. Time it
  there before you plan the production run.
- Run as the web server user (as root, add `--allow-root-user`). The objects are published as the run's user.

## Steps

### 1. Generate and fill in

```bash
./console exp:migration:generate --skeleton news_article_default_source --description="Every news article without a source gets the source Newsroom (a new version each)" --apply
```

Add `// requires: 20261101110000_news_article_class` and the methods below.

### 2. Look at the plan

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-130207-91af3c, environment staging, database mysql (transactions: data)

  would-apply 20261101130000_news_article_default_source   site               Every news article without a source gets the source Newsroom (a new version each)
              would look at 2418 news article(s) in batches of 100
              would publish a new version with the source "Newsroom" for each one without a source

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

### 3. Run it

```bash
./console exp:migration:migrate --apply
```

```text
Exponential migrations: run
Run 20261101-131015-2c8e40, environment staging, database mysql (transactions: data), as admin

  applied     20261101130000_news_article_default_source   site               Every news article without a source gets the source Newsroom (a new version each)
              looked at 2418 news article(s)
              gave 312 of them the source Newsroom

Captions: pre 20261101-131015, post 20261101-131322
Repository: commit 0e5a7f1b3c99, tag migrations/staging/20261101-131015-2c8e40, push pushed
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 applied
PASS
```

While it runs, the run log `migrations/runs/20261101-131015-2c8e40.log` gets one line per batch:

```text
2026-11-01T20:10:21Z info batch at offset 0: 100 seen, 14 changed
2026-11-01T20:10:27Z info batch at offset 100: 200 seen, 29 changed
```

Started from Setup > Migrations, the run goes on in the background and its page shows the same progress.

## The migration

```php
<?php
// description: Every news article without a source gets the source Newsroom (a new version each)
// requires: 20261101110000_news_article_class
// maintenance: no
// author: Site team
namespace Site\Migrations;

final class NewsArticleDefaultSource20261101130000 extends \expMigrationPhp
{
    const CLASS_IDENTIFIER = 'news_article';
    const DEFAULT_SOURCE = 'Newsroom';
    const BATCH = 100;
    const ROOT_NODE = 2;

    /** @return array the fetch parameters of one batch */
    private static function batch( $offset )
    {
        return array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::CLASS_IDENTIFIER ),
                      'SortBy' => array( 'node_id', true ), 'Limit' => self::BATCH, 'Offset' => $offset,
                      'MainNodeOnly' => true, 'LoadDataMap' => false, 'IgnoreVisibility' => true );
    }

    /** Whether an object still needs the change: pure, so plan(), up() and a second run agree */
    public static function needsSource( $value )
    {
        return trim( (string)$value ) === '';
    }

    /**
     * Commits per object: a run over thousands of articles must not hold one transaction. Safe to run again,
     * because an article that has a source is left alone.
     */
    public function transactional()
    {
        return false;
    }

    public function plan( $context )
    {
        $count = \eZContentObjectTreeNode::subTreeCountByNodeID( array( 'ClassFilterType' => 'include',
                     'ClassFilterArray' => array( self::CLASS_IDENTIFIER ), 'MainNodeOnly' => true, 'IgnoreVisibility' => true ), self::ROOT_NODE );
        return array( "look at $count news article(s) in batches of " . self::BATCH,
                      'publish a new version with the source "' . self::DEFAULT_SOURCE . '" for each one without a source' );
    }

    public function up( $context )
    {
        $changed = 0;
        $seen = 0;
        for ( $offset = 0; ; $offset += self::BATCH )
        {
            $nodes = \eZContentObjectTreeNode::subTreeByNodeID( self::batch( $offset ), self::ROOT_NODE );
            if ( !$nodes )
                break;
            foreach ( $nodes as $node )
            {
                ++$seen;
                $object = $node->object();
                $dataMap = $object->dataMap();
                if ( !isset( $dataMap['source'] ) || !self::needsSource( $dataMap['source']->toString() ) )
                    continue;
                if ( !\eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => array( 'source' => self::DEFAULT_SOURCE ) ), false ) )
                    throw new \RuntimeException( 'the article ' . $object->attribute( 'remote_id' ) . ' could not be published (' . $changed . ' changed before it)' );
                ++$changed;
            }
            // free the objects of the batch: a long run must not grow without limit
            \eZContentObject::clearCache();
            $context->log( 'info', "batch at offset $offset: $seen seen, $changed changed" );
        }
        return array( "looked at $seen news article(s)", "gave $changed of them the source " . self::DEFAULT_SOURCE );
    }
}
```

Why it is written this way:

- **`transactional()` returns `false`.** Thousands of publishes in one transaction would hold locks for minutes and
  undo everything on the last error. Here every publish commits by itself.
- **It is safe to run again.** `needsSource()` skips articles that have a source, so after a failure halfway the
  next run (or `./console exp:migration:resume --apply`) continues where it stopped, and a second run changes
  nothing.
- **Batches with a fixed order** (`SortBy node_id`) and `eZContentObject::clearCache()` after each batch keep memory
  flat. The filter is on the class, not on the value being changed, so the offsets stay valid while it writes.
- **A new version per object** (`updateAndPublishObject()`) keeps history, updates the search index and clears
  the view caches like a publish in the editor. The third argument `false` sends no notifications to subscribers.
- **No `down()`.** The earlier values are in each object's version history and in the caption; a rollback of this
  migration alone is refused with "cannot be undone by itself", which is the honest answer.

## Check it

- Open a few news articles in the administration: Source says Newsroom, the version history has one more version.
- Run the dry run again: `would look at 2418` and, after `--apply`, `gave 0 of them`.
- Compare the counts with the run log.

## Roll it back

This migration has no `down()`. To go back, restore the run's pre caption (database, storage and settings as they
were before the run):

```bash
./console exp:migration:rollback 20261101-131015-2c8e40            # what it would restore
./console exp:migration:rollback 20261101-131015-2c8e40 --apply
```

On production, plan the run so that nothing else is published meanwhile, or the restore takes those changes back
too; give the header `maintenance: yes` when editors and visitors must wait.

## Next step

Correct rows of a table rather than content objects: [Fix data](fix-data.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Large data migrations](large-data-migrations.md), [Captions and rollback in practice](captions-and-rollback-in-practice.md), [Rollback and captions](../../../guides/migrations-rollback-and-captions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
