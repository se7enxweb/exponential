---
title: "Upgrade step or migration?"
audience: developer, software-engineer, php-developer, admin
category: migrations
order: 22
summary: When a change belongs in an upgrade step that exp:upgrade applies to every installation of the kernel or an extension, and when in a migration of one site; how the upgrade steps appear among the migrations, and how a migration requires a step.
keywords: migration, migrations, schema change, database change, upgrade step, exp:upgrade, expUpgradeSchemaStep, expUpgradeScriptStep, ExtensionStepList, upgrade.ini, source kernel, deferred, exp:upgrade:finish, requires, decision
featured: true
type: help
---
# Upgrade step or migration?

Exponential has two tools for changing an installation in code. **Upgrade steps** are what `./console exp:upgrade`
applies when the kernel or an extension is upgraded. **Migrations** are a site's own changes, applied in order with
`./console exp:migration:migrate`. Both are recorded in the table `exp_migration` and run under the same lock, so they
are easy to confuse. This page helps you choose, and shows how the two meet.

## What you'll have at the end

- a decision for your change;
- for a step: where it is registered; for a migration: where its file goes;
- a migration that waits for an upgrade step with `requires:`.

## Before you start

Read [Upgrade steps for extensions](../../../guides/upgrade-steps-for-extensions.md) and the
[PHP migrations](../../../guides/migrations-php.md) guide once; this page compares them.

## The decision

| Question | Upgrade step | Migration |
|---|---|---|
| Who needs the change? | **every installation** of the kernel or the extension | **one site** (development, staging, production of it) |
| When does it run? | on upgrade, with `exp:upgrade --apply` | when the site's code reaches an installation, with `exp:migration:migrate --apply` |
| How is its state known? | `detect()` asks the installation itself (`PRESENT`, `MISSING`, `NOT_APPLICABLE`) | the record (`done` once run), or `detect()` when the class has one |
| Is the file guarded? | no checksum guard on the class file | a changed file of a `done` migration is refused |
| Undo | no `down()`; a caption restores it | `down()` where written; a caption otherwise |
| Caption | `exp:upgrade` stops when no caption can be taken | the run warns and goes on |
| Restore after a failure | only with `--rollback-on-fail` | by default |
| Written as | a class extending `expUpgradeSchemaStep` (tables, columns, indexes of a `.dba`) or `expUpgradeScriptStep` (rows, policies, settings), listed in `upgrade.ini [UpgradeSettings] ExtensionStepList[]` | a file `YYYYMMDDHHMMSS_name.php` with a class extending `expMigrationPhp` in `migrations/` or `extension/<name>/migrations/` |

Rules of thumb:

- A table, column, index or default row that the code of an extension cannot work without: **upgrade step**.
- A class, content, a role, a setting or a table that one site builds on: **migration**.
- A change of one site that needs an extension's table first: a migration with `requires:` on the step.
- Copying a whole site with its content: neither, a [site package](../../../guides/packages.md).

## Steps

### 1. See the steps among the migrations

Every upgrade step is also a migration of the source `kernel`, with the step id as its id:

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-130000-0a1b2c, environment staging, database mysql (transactions: data)

  done        6.0.15-migration-tables                       kernel             The migration record (tables exp_migration and exp_migration_run), with the rows of expupgrade_step copied over
  deferred    6.0.15-code-update                            kernel             The git checkouts moved to the target release, fast-forward only, through git_manager (--to=, --repos=)
  would-apply 20261101090000_news_note_priority             site               The column priority of exp_news_note
              would add the column exp_news_note.priority on mysql
...
WARN 6.0.15-code-update: pending, not run by a migration run: an upgrade step of the stage code: ./console exp:upgrade:code --apply
PASS
```

(Lines left out.) A migration run applies the pending steps of the stages `database` and `scripts` first, then the
site's migrations, in one run under one lock and one caption. Steps of the stages `code`, `libraries` and `finish`
are `deferred`, with a warning that names the `exp:upgrade` command that runs them. `./console exp:migration:status`
lists the steps first too, with the state `exp:upgrade:status` gives them.

### 2. Require a step from a migration

```php
<?php
// description: The news bookmarks of the editors point to the news module view
// requires: 6.0.15-bookmark-view-columns
namespace Site\Migrations;

final class NewsBookmarks20261101140000 extends \expMigrationPhp
{
    public function plan( $context )
    {
        return array( "set module_name 'news' on the bookmarks of /news/list" );
    }

    public function up( $context )
    {
        $db = $context->db();
        $db->query( "UPDATE expbookmark_view SET module_name = 'news', view_name = 'list'"
                    . " WHERE module_name = '' AND module_uri LIKE '/news/list%'" );
        return array( 'set the module and view of the news bookmarks' );
    }
}
```

The migration runs after the step, whatever their ids say. A migration that requires a deferred step waits until
`exp:upgrade` has run it.

### 3. After a run that applied steps

```bash
./console exp:upgrade:finish --apply
```

It makes the applied steps live: deploy, caches, smoke check. Both commands write the same record, so a step applied
by a migration run is `done` in `exp:upgrade:status` and the other way round.

## Check it

`./console exp:upgrade:status` and `./console exp:migration:status` agree on every step. `./console exp:upgrade:verify`
reports drift: a step the record calls done whose effect is gone.

## Next step

Writing a step: [Upgrade steps for extensions](../../../guides/upgrade-steps-for-extensions.md). Writing a migration
in an extension: [Migrations in an extension or a package](migrations-in-an-extension-or-package.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Upgrading with the console](../../../guides/upgrading-with-the-console.md),
  [Upgrade steps for extensions](../../../guides/upgrade-steps-for-extensions.md),
  [The upgrade check](../../../guides/upgrade-check.md)
- [Schema changes and migrations](../../software-engineers/schema-changes-and-migrations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
