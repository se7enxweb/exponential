---
title: "Move or rename INI settings"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 7
summary: "A PHP migration that moves settings of an installation's override from site.ini to a file of their own and renames one on the way, keeps the value of each installation, writes the new file before it removes the old entry, and moves them back in down()."
keywords: migration, migrations, INI, settings, move setting, rename setting, settings/override, site.ini.append.php, eZINI, setVariable, removeSetting, save, resetAllInstances, configuration change, exp:migration:migrate, Einstellungen
type: help
---
# Move or rename INI settings

This recipe is for developers who reorganize settings: a block moves from `site.ini` into a file of its own, a
setting gets a clearer name. Code reads the new place after the change, but every installation keeps its own
values in `settings/override/`, and each one differs (a page size on staging, another on production). A migration
moves the value that each installation has, in the same release as the code that reads the new place. The example
moves `site.ini [NewsSettings]` to `news.ini [ListSettings]` and renames `SourceLimit` to `PageLimit`.

Migration files of the type `.ini` are found and checked by the engine, but the 6.0.15 runner does not run them yet,
so this change is written in PHP.

## What you'll have at the end

- the migration `20261101150000_move_news_settings.php`;
- on every installation, the values that were in `settings/override/site.ini.append.php [NewsSettings]` in
  `settings/override/news.ini.append.php [ListSettings]`, and no longer in `site.ini.append.php`;
- a `down()` that moves them back.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done.
- The code that reads `news.ini [ListSettings]` is in the same commit, and the extension or site ships a
  `news.ini` with the defaults (a migration moves the installation's own values, never the shipped defaults).
- The installation's settings are not managed by the deployment (`site.ini [ConfigurationSettings]
  ManagedByDeployment`): with it, change the deployment's files instead of running this migration.
- Run as the user who owns `settings/override/` (as root, add `--allow-root-user`).

## Steps

### 1. Generate and fill in

```bash
./console exp:migration:generate --skeleton move_news_settings --description="The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)" --apply
```

### 2. Dry run

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-150144-b8e0d1, environment production, database mysql (transactions: data)

  would-apply 20261101150000_move_news_settings            site               The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
              would move site.ini [NewsSettings/SourceLimit] = 25 to news.ini [ListSettings/PageLimit]
              would move site.ini [NewsSettings/ShowSource] = enabled to news.ini [ListSettings/ShowSource]

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

On an installation that never set `[NewsSettings]`, the plan says `nothing: settings/override/site.ini.append.php
has no [NewsSettings] to move`, and the run records the migration as done without a change.

### 3. Run and deploy

```bash
./console exp:migration:migrate --apply
./console exp:velocity deploy
```

```text
  applied     20261101150000_move_news_settings            site               The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
              moved [NewsSettings/SourceLimit] to news.ini [ListSettings/PageLimit]
              moved [NewsSettings/ShowSource] to news.ini [ListSettings/ShowSource]
```

The settings are cached: the deploy clears the INI cache and reloads PHP-FPM and Velocity, so the web requests read
the new file. Clearing only the INI cache is `php bin/php/ezcache.php --clear-tag=ini`.

## The migration

```php
<?php
// description: The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
// author: Site team
namespace Site\Migrations;

final class MoveNewsSettings20261101150000 extends \expMigrationPhp
{
    const DIRECTORY = 'settings/override';
    /** old block and name => new block and name */
    const MOVES = array(
        'NewsSettings/SourceLimit' => 'ListSettings/PageLimit',
        'NewsSettings/ShowSource' => 'ListSettings/ShowSource',
    );

    /** @return \eZINI an override file, read and written directly (no merged settings, no cache) */
    private static function override( $name )
    {
        return \eZINI::instance( $name, self::DIRECTORY, null, false, null, true );
    }

    /** @return array[] the settings that are still in the old place: array( old, new, value ) */
    private static function pending()
    {
        $site = self::override( 'site.ini.append.php' );
        $pending = array();
        foreach ( self::MOVES as $old => $new )
        {
            list( $block, $name ) = explode( '/', $old );
            if ( $site->hasVariable( $block, $name ) )
                $pending[] = array( $old, $new, $site->variable( $block, $name ) );
        }
        return $pending;
    }

    public function plan( $context )
    {
        $lines = array();
        foreach ( self::pending() as $move )
            $lines[] = "move site.ini [{$move[0]}] = {$move[2]} to news.ini [{$move[1]}]";
        return $lines ? $lines : array( 'nothing: settings/override/site.ini.append.php has no [NewsSettings] to move' );
    }

    public function up( $context )
    {
        $pending = self::pending();
        if ( !$pending )
            return array( 'nothing to move' );
        $site = self::override( 'site.ini.append.php' );
        $news = self::override( 'news.ini.append.php' );
        $done = array();
        foreach ( $pending as $move )
        {
            list( $oldBlock, $oldName ) = explode( '/', $move[0] );
            list( $newBlock, $newName ) = explode( '/', $move[1] );
            $news->setVariable( $newBlock, $newName, $move[2] );
            $site->removeSetting( $oldBlock, $oldName );
            $done[] = "moved [{$move[0]}] to news.ini [{$move[1]}]";
        }
        // the new file first: a failure between the two saves leaves the value in both places, never in none
        if ( !$news->save() || !$site->save() )
            throw new \RuntimeException( 'settings/override could not be written' );
        \eZINI::resetAllInstances();
        return $done;
    }

    public function down( $context )
    {
        $site = self::override( 'site.ini.append.php' );
        $news = self::override( 'news.ini.append.php' );
        $done = array();
        foreach ( self::MOVES as $old => $new )
        {
            list( $oldBlock, $oldName ) = explode( '/', $old );
            list( $newBlock, $newName ) = explode( '/', $new );
            if ( !$news->hasVariable( $newBlock, $newName ) )
                continue;
            $site->setVariable( $oldBlock, $oldName, $news->variable( $newBlock, $newName ) );
            $news->removeSetting( $newBlock, $newName );
            $done[] = "moved news.ini [$new] back to site.ini [$old]";
        }
        if ( !$site->save() || !$news->save() )
            throw new \RuntimeException( 'settings/override could not be written' );
        \eZINI::resetAllInstances();
        return $done ? $done : array( 'nothing to move back' );
    }
}
```

- `eZINI::instance( <file>, 'settings/override', null, false, null, true )` reads one file directly: not the merged
  settings of every directory, and not the cache. That is what the migration must move.
- The new file is saved before the old entry is removed, so a failure leaves the value in both places, never in
  none.
- `settings/override/` is part of every caption (`[CaptionSettings] RestoreParts[]=settings`), so a failed run
  restores the files as they were.
- Uncommitted changes in `settings/` stop a run (`settings` is in `[CaptionSettings] CodeTreeList[]`), but
  `settings/override/` is usually not in git; the files the migration writes there are not a reason to refuse the
  next run.
- A setting that holds a secret (a password, a token) does not belong in an INI file at all; use the secret store.

## Check it

- `cat settings/override/news.ini.append.php` shows `[ListSettings]` with the moved values; `site.ini.append.php`
  has no `[NewsSettings]` left.
- Setup > Ini settings shows `news.ini` with the merged values from the new place.
- The news list on the site uses the page size of this installation.

## Roll it back

```bash
./console exp:migration:rollback 20261101150000_move_news_settings --apply
./console exp:velocity deploy
```

```text
  undone      20261101150000_move_news_settings            site               The news settings move from site.ini [NewsSettings] to news.ini [ListSettings] (SourceLimit -> PageLimit)
              moved news.ini [ListSettings/PageLimit] back to site.ini [NewsSettings/SourceLimit]
              moved news.ini [ListSettings/ShowSource] back to site.ini [NewsSettings/ShowSource]
```

Roll back the code that reads the new place at the same time; otherwise the site falls back to the shipped defaults.

## Next step

Change who may do what in the same way: [Add roles and policies](add-roles-and-policies.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Per-environment migrations](per-environment-migrations.md), [migration.ini [CaptionSettings]](../../../examples/ini-settings/migration/CaptionSettings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
