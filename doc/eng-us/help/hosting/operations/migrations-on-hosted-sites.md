---
title: "Migrations on hosted sites"
audience: hosting, dba, admin
category: migrations
order: 220
summary: What a customer's migration run does to your servers - the installation lock, the captions (database dumps and file copies) and their disk space, maintenance mode, background runs started from the administration, the engines, git pushes with the system user's credentials, logs, audit events and monitoring - and how it differs from moving a site between hosts.
keywords: migration, migrations, schema change, database change, deploy, exp:migration:migrate, hosting, database administrator, DBA, captions, backup, disk space, proc_open, PhpCliPath, migrationpush, cronjob, monitoring, fail-on-pending, migration.log, audit
type: help
---
# Migrations on hosted sites

This page is for hosting companies and database administrators who run Exponential 6.0.15 sites for customers.
The sites' developers ship changes as migrations: files the site applies with `./console exp:migration:migrate
--apply` or from Setup > Migrations. Here is what such a run does on your servers, what it needs from them and what to
watch.

## What a run does

| Step | On the server |
|---|---|
| The lock | the file `var/<site>/upgrade.lock` (shared with `exp:upgrade`) and a holder file next to it; one writing run per installation at a time |
| Code check | git is asked whether `settings/`, `extension/` or `design/` have uncommitted changes; a run is refused when they do |
| Caption before | a database dump and copies of `var/` storage and `settings/override` (see below), reused while nothing changed |
| Migrations | PHP code of the site, run as `[RunSettings] RunAsUser` (`admin`) on the console or as the signed-in user in the administration |
| Maintenance mode | switched on and off around each migration whose header says `maintenance: yes` |
| After a failure | a caption of the failed state, then the restore of the caption taken before |
| Caption after | one more caption after a run that passed |
| Commit, tag, push | the status file and the run log are committed in the site's git checkout, tagged and pushed to `origin` |

## Disk space: the captions

Captions are the backups of runs and upgrades. With git_manager installed they are git_manager's full captions in
its backup folder; without it, the dump of `exp:upgrade` in `var/backup/upgrade/` (or
`migration.ini [CaptionSettings] BackupDirectory`), without storage. A run takes up to three (pre, post and, after a
failure, failure). Reuse (`ReuseUnchanged=enabled`) keeps a quiet site from adding one per run, but a site that runs
often still grows the folder.

- Plan the space like a database backup of the site times the captions you keep.
- List them with `./console exp:upgrade:captions` (or `exp:migration:caption --list`). `exp:upgrade:captions
  --prune` keeps what `upgrade.ini [UpgradeSettings] CaptionKeep` (10), `CaptionKeepDays` (90) and
  `CaptionKeepUpgrades` say; git_manager's own captions keep to its `MaxBackups`.
- Keep the folder on the same backup schedule as the rest of `var/`, or exclude it deliberately: it is a second copy.

## Engines

| Database | A failed migration is undone | Simulation | Caption and restore |
|---|---|---|---|
| PostgreSQL, SQLite | completely | yes | yes |
| MySQL/MariaDB | data only; schema changes commit at once | no | yes |
| MongoDB | not at all | no | yes |
| Oracle (ezoracle) | data only | no | a caption needs an export of your own; the restore is by hand and its steps are printed |

On MySQL/MariaDB, Oracle and MongoDB the restore of the caption is what really takes a failed run back. Make sure the
dump tools of the engine (`mysqldump`, `pg_dump`, `mongodump`) are installed for the PHP command line user.

## Background runs from the administration

A run, a resume or a rollback started in Setup > Migrations runs as a console process of its own, as the signed-in
user, and goes on when the browser is closed. It needs:

- `proc_open` allowed in the PHP that serves the administration (check `disable_functions`);
- a PHP command line binary: `migration.ini [AdminSettings] PhpCliPath` (empty: `cronjob.ini [AdminSettings]
  PhpCliPath`, else the usual places such as `/usr/bin/php`; never the php-fpm binary);
- write access to `var/<site>/migration/jobs/`, where each run keeps its progress file and output
  (`JobHistory`, default 50 runs).

Without them the page says so, and the run has to be started on the console. The process runs as the PHP-FPM user of
the site, like every other write to `var/`.

## Git: credentials and pushes

The record is pushed with the credentials the system user has for the remote (an SSH key, a credential helper);
nothing is stored by the migrations. A push that fails does not fail the run: the commit stays local, the status
warns `1 run(s) wait for their push`, and the cronjob part `migrationpush` pushes it later. Add that part to a cronjob
group the site runs, or schedule it on its own:

```bash
php runcronjobs.php migrationpush
```

## Logs, audit and monitoring

| Where | What |
|---|---|
| `var/log/migration.log` | every line of every run, cleaned of secrets |
| `migrations/runs/<run-id>.log` | the same per run, committed to git |
| the audit log | `system.migration.run`, `.apply`, `.fail`, `.rollback`, `.caption`, `.push` (`settings/audit.ini`) |

For monitoring, ask each installation after a deploy:

```bash
./console exp:migration:status --fail-on-pending --format=json > var/log/migration-status.json
echo $?    # 0 nothing pending, 1 pending or failed, 3 database unreachable
```

The JSON (`exp-migration/1`) carries `summary`, `lock` (who holds it), `pushes` (runs waiting for their push) and
`orphans`. Alert on exit 1 after a deploy window, on pushes that wait for more than a day, and on a lock held for
hours.

## What to watch

- A run that holds the lock for a long time: `exp:migration:status` names the holder; a lock left by a dead process is
  taken over by the next run, nothing needs deleting.
- A failed run: the site was restored automatically; the developer fixes the migration and resumes it.
- Velocity or PHP-FPM not seeing a change a migration made in settings or classes: deploy as for any code change.
- Disk: the caption folder.

## Migrations are not moving a site

A migration changes one installation in place. Moving a customer's site to your servers, between hosts or to another
database engine is a different job, with its own pages:
[Migrations versus migrating a site](../../migrations/migrations-versus-migrating-a-site.md) and
[Migrating customers: overview](../migrations/README.md).

## Where to go next

- [Migrations for administrators](../../migrations/migrations-for-administrators.md)
- [Deploying with migrations](../../migrations/recipes/deploying-with-migrations.md)
- [Engine notes](../../migrations/recipes/engine-notes.md) and
  [captions and rollback in practice](../../migrations/recipes/captions-and-rollback-in-practice.md)
- Start now: [the tutorial](../../../guides/migrations-tutorial.md)

## Related pages

- [Migrations: the hub](../../migrations/README.md)
- [Migrations: the guide](../../../guides/migrations.md)
- [Migrations: the tutorial](../../../guides/migrations-tutorial.md)
- [PHP migrations](../../../guides/migrations-php.md)
- [Migrations in the administration](../../migrations/in-the-administration.md)
- [The migration commands](../../migrations/commands.md)
- [The migration settings](../../migrations/settings.md)
- [The migration file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../../migrations/when-a-migration-fails.md)
- [Troubleshooting migrations](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../../migrations/migrations-versus-migrating-a-site.md)
- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Database administrators: start here](../../database-administrators/database-administrators-start-here.md)
- [Deploying changes](deploying-changes.md), [Database backups per engine](database-backups-per-engine.md),
  [Maintenance mode](maintenance-mode.md), [Cron jobs](cron-jobs.md)
- [The cronjob part migrationpush](../../../examples/cronjobs/migrationpush.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
