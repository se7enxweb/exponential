---
title: "Migration settings"
audience: developer, admin, software-engineer, hosting
category: migrations
order: 21
summary: Every setting that shapes migrations in one place - the five blocks of migration.ini with their defaults and a link to each generated reference page, the settings in site.ini, dashboard.ini, audit.ini and cronjob.ini that migrations use, and how to override them per installation and per environment.
keywords: migration.ini, settings, MigrationSettings, RunSettings, CaptionSettings, RepositorySettings, AdminSettings, DirectoryList, ExtensionDirectory, RunAsUser, StopOnFailure, Before, After, ReuseUnchanged, RestoreOnFailure, RestoreParts, CodeTreeList, BackupDirectory, Mode, Remote, Branch, Commit, Tag, TagPattern, Push, PhpCliPath, JobHistory, LogBytes, AdminEnvironment, Level, EXP_ENV, override, configuration
type: help
---
# Migration settings

The settings of migrations are in `settings/migration.ini`. Never edit that file: an update replaces it. Put what you
change in an override:

- `settings/override/migration.ini.append.php` for the installation,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in an extension (read while the extension is active).

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php on a development installation
[RepositorySettings]
Push=disabled
*/ ?>
```

## Contents

1. [migration.ini](#migrationini)
2. [Settings in other files](#settings-in-other-files)
3. [Typical settings per environment](#typical-settings-per-environment)
4. [Related pages](#related-pages)

## migration.ini

| Block and setting | Default | What it does |
|---|---|---|
| **[MigrationSettings]** ([reference](../../examples/ini-settings/migration/MigrationSettings.md)) | | where migrations are found |
| `Enabled` | `enabled` | `disabled` finds no migrations at all |
| `DirectoryList[]` | `migrations` | the site's migration folders; the first is where `exp:migration:generate` writes |
| `ExtensionDirectory` | `migrations` | the folder inside every active extension and every installed package |
| **[RunSettings]** ([reference](../../examples/ini-settings/migration/RunSettings.md)) | | how a run behaves |
| `RunAsUser` | `admin` | the login a console run runs as (a run in the administration runs as the signed-in user) |
| `StopOnFailure` | `enabled` | stop at the first failure; `disabled` goes on with migrations that do not require the failed one |
| `Throttle` | empty | shipped empty; the 6.0.15 runner does not read it (pause inside a long migration yourself, see [Large data migrations](recipes/large-data-migrations.md)) |
| **[CaptionSettings]** ([reference](../../examples/ini-settings/migration/CaptionSettings.md)) | | the safe points around a run |
| `Before` | `enabled` | a caption (kind `migration-pre`) before every run |
| `After` | `enabled` | a caption (kind `migration-post`) after a run that passed |
| `ReuseUnchanged` | `enabled` | reuse the newest caption while nothing has changed since |
| `ReuseIgnoreTableList[]` | `exp_migration`, `exp_migration_run`, `expupgrade_step`, `ezsession`, `expaudit_*` | tables the reuse check leaves out |
| `RestoreOnFailure` | `enabled` | restore the pre caption after a failure |
| `RestoreParts[]` | `database`, `storage`, `settings` | the parts of a caption that restore puts back |
| `CodeTreeList[]` | `settings`, `extension`, `design` | trees checked for uncommitted changes before `--apply` |
| `BackupDirectory` | empty (`var/backup/upgrade`) | where captions made without git_manager are kept |
| **[RepositorySettings]** ([reference](../../examples/ini-settings/migration/RepositorySettings.md)) | | the record in git |
| `Mode` | `site` | `site`: the installation's own checkout; `separate`: a repository of its own at `Path` |
| `Path` | `migrations` | the folder of the status files and run logs |
| `Remote` | `origin` | the remote to push to; empty: no push |
| `Branch` | empty | the branch to commit on; empty: the current one |
| `Commit`, `Tag`, `Push` | `enabled` | commit the record, tag the commit, push both |
| `TagPattern` | `migrations/<environment>/<run-id>` | the tag name; placeholders `<environment>`, `<run-id>`, `<date>` |
| **[AdminSettings]** ([reference](../../examples/ini-settings/migration/AdminSettings.md)) | | runs started in Setup > Migrations |
| `PhpCliPath` | empty | the PHP command line binary that runs them in the background |
| `JobHistory` | `50` | how many runs keep their files in `var/<site>/migration/jobs/` |
| `LogBytes` | `262144` | how much of the end of a run log a page shows |

Every setting with its full comment: [the blocks of migration.ini](../../examples/ini-settings/migration/README.md).
An extension can point `ExtensionDirectory` elsewhere:
[the extension points of migration.ini](../../examples/extension-points/migration.md).

## Settings in other files

| File and block | Setting | For migrations |
|---|---|---|
| `site.ini [AdminEnvironment]` ([reference](../../examples/ini-settings/site/AdminEnvironment.md)) | `Level` | the environment of the installation (`development`, `test`, `staging`, `production`; `auto` reads `EXP_ENV`), which the header line `environments:` and the status file name use |
| `site.ini [ReauthGroup_system]` ([reference](../../examples/ini-settings/site/ReauthGroup_system.md)) | | "Verify it's you" before a run, a resume, a simulation and a rollback in the administration |
| `dashboard.ini [DashboardBlock_migrations]` ([reference](../../examples/ini-settings/dashboard/DashboardBlock_migrations.md)) | `PolicyList[]` | the dashboard block, for users with `migration/read` |
| `audit.ini [AuditEventSettings]` ([reference](../../examples/ini-settings/audit/AuditEventSettings.md)) | `Enabled[]` | the audit events `system.migration.run`, `.apply`, `.fail`, `.rollback`, `.caption`, `.push` |
| `cronjob.ini [CronjobPart-migrationpush]` ([reference](../../examples/ini-settings/cronjob/CronjobPart-migrationpush.md)) | `Scripts[]` | the part that pushes runs whose push failed ([the cronjob part](../../examples/cronjobs/migrationpush.md)) |
| `upgrade.ini [UpgradeSettings]` ([reference](../../examples/ini-settings/upgrade/UpgradeSettings.md)) | `ExtensionStepList[]` | an extension's upgrade steps, which are listed among the migrations as source `kernel` |

## Typical settings per environment

| | Development | Staging | Production |
|---|---|---|---|
| `[AdminEnvironment] Level` | `development` | `staging` | `production` |
| `[RepositorySettings] Push` | `disabled` (or `--no-push`) | `enabled` | `enabled` |
| `[CaptionSettings] Before`, `After` | `enabled` (cheap on SQLite) | `enabled` | `enabled` |
| `[CaptionSettings] RestoreOnFailure` | `enabled` | `enabled` | `enabled` |
| `[RunSettings] RunAsUser` | `admin` | a dedicated administrator login | a dedicated administrator login |

A setting that differs per environment goes in the environment's own override (for example the variant files of
`EXP_ENV`, `settings/override/site.ini.prod.append.php`), never in a migration: migrations are the same files on every
installation ([Per-environment migrations](recipes/per-environment-migrations.md)).

## Related pages

- [Migrations: the hub](README.md), [All recipes](recipes/README.md), [Frequently asked questions](faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](in-the-administration.md), [Commands](commands.md),
  [Policies, views and events](policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [When a migration fails](when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
