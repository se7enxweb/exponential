# Migrations: rollback and captions

A migration run never leaves an installation without a way back. Before it writes, it takes a **caption**, a safe
point of the database, the storage and the settings; when a migration fails, the run puts the installation back to
it on its own. This page explains the captions of migration runs, the automatic restore, and the three ways to take
migrations back by hand.

[Guides](README.md) · [Migrations: the hub](../help/migrations/README.md) · [Migrations](migrations.md) · Related: [Upgrading with the console: captions](upgrading-with-the-console.md#captions),
[The migrations repository](migrations-repositories.md), [Troubleshooting](migrations-troubleshooting.md)

## In short

```bash
./console exp:migration:caption --list                                   # the captions, newest first
./console exp:migration:caption --label="before the news import" --apply  # a caption now
./console exp:migration:rollback 20261031-120000-a1b2c3                   # what restoring a run's caption would do
./console exp:migration:rollback 20261031-120000-a1b2c3 --apply           # restore it
./console exp:migration:rollback 20261031120000_add_news_class --apply    # one migration's down()
./console exp:upgrade:finish --apply                                      # make a restore live: caches, PHP-FPM, Velocity
```

## What a caption is

A caption is the caption of `exp:upgrade`, so there is one list of captions for upgrades and migrations
(`./console exp:upgrade:captions` and `./console exp:migration:caption --list` show the same ones):

- with git_manager installed: git_manager's full caption (the database dump and the `var/` storage) in git_manager's
  backup folder;
- without it, or for an engine git_manager cannot dump: the database dump of `exp:upgrade` in `var/backup/upgrade/`
  (or `[CaptionSettings] BackupDirectory`), without storage;
- in both: `settings/override`, the commits of the code, and a checksum of every file.

[Upgrading with the console](upgrading-with-the-console.md#captions) lists every file of a caption and how each
engine is dumped. A migration run adds its own folder `migration/` to the caption:

| In the caption | What it is |
|---|---|
| `migration/marker.json` | what the installation looked like when the caption was taken: the database (see reuse below), `var/` storage and `settings/override` as file lists, the commit of the root checkout |
| `migration/uncommitted.json`, `migration/uncommitted/` | with `--include-uncommitted`: the checkouts whose code trees had changes that were not committed, a patch of each and a copy of its new files |

### The kinds

| Kind | Taken | Label |
|---|---|---|
| `migration-pre` | before the first migration of a run with `--apply` | `migration run <run-id> in <environment> pre: 2 migration(s) <ids>` |
| `migration-post` | after a run that passed (the re-caption) | `... post: <the migrations applied>` |
| `migration-failure` | after a migration failed, before the restore: the failed state, for a look later | `... failure: <the failed migration first>` |
| `manual` | `exp:migration:caption --apply` | your `--label`, or `manual caption <time>` |

Each caption is recorded in the run's row (`exp_migration_run.caption_pre`, `caption_post`), in the migrations it
preceded (`exp_migration.caption_id`) and in the audit log (`system.migration.caption`).

### Reuse

A caption takes time and space. With `[CaptionSettings] ReuseUnchanged=enabled` (the default) a run reuses the newest
migration caption instead of taking a new one, while the installation is exactly as it was then: the database, the
`var/` storage, `settings/override` and the commit of the installation. The database is compared exactly on
PostgreSQL (the change counters of every table), MySQL/MariaDB (`CHECKSUM TABLE` of every table) and SQLite (the rows
of every table), leaving out the tables of `ReuseIgnoreTableList[]` (the record of the runs, sessions, the audit
index). MongoDB and Oracle always take a new caption. The output says `pre <id> (reused)`.

### When no caption can be taken

A database without a dump tool, a full disk, git_manager failing: the run goes on with the warning
`no caption could be taken, the run goes on without a way back: ...`. Then there is no automatic restore; a failure
prints the steps to restore by hand instead. `--no-caption` (or `[CaptionSettings] Before=disabled`) runs without a
caption on purpose, with the warning
`no caption before the run (--no-caption or [CaptionSettings] Before=disabled): a failure cannot be restored automatically`.

(This is the rule of migrations. `exp:upgrade` keeps its own: a caption that fails stops an upgrade.)

### Uncommitted code

A caption holds the database, storage and settings, and the commit of the code, not the code itself. So a run with
`--apply` is refused (exit 2) while the code trees of `[CaptionSettings] CodeTreeList[]` (`settings`, `extension`,
`design`) have changes that are not committed:

```text
FAIL: 1 change(s) that are not committed in the code trees (settings/...): commit them first, or run with --include-uncommitted to keep them in the caption
```

Commit them, or give `--include-uncommitted`: the changes then go into the caption as a patch and a copy of the new
files.

## The automatic restore after a failure

When a migration fails (`[CaptionSettings] RestoreOnFailure=enabled`, the default):

1. the failed migration's transaction is rolled back (as far as the database can);
2. a caption of the failed state is taken (kind `migration-failure`);
3. the pre caption is restored: the parts of `[CaptionSettings] RestoreParts[]` (`database`, `storage`, `settings`),
   through the rollback of `exp:upgrade`;
4. the record of the failed migration is written again (the restore took the record back to the time of the
   caption) and the run is committed and pushed with its state `failed`.

```text
Captions: pre 20261031-193128, failure 20261031-193128-2
Restore: restored (caption 20261031-193128)
WARN restored the caption 20261031-193128 taken before the run (database, storage, settings); the failed state is kept in the caption 20261031-193128-2
FAIL (exit 1)
```

`--no-restore` (or `RestoreOnFailure=disabled`) leaves the installation as the failure left it; the report says
`Restore: off`. Oracle has no built-in restore: the run stops and the report lists the steps to restore by hand
(`Restore: manual`). The restore states are `restored`, `failed`, `manual`, `none` (no caption) and `off`.

After a restore, make it live: `./console exp:upgrade:finish --apply` clears the caches and reloads PHP-FPM and
Velocity ([Deploying](deploying.md)).

## Rolling back by hand

`exp:migration:rollback <target>` takes one argument and finds what it names in this order: a run id, a migration
id, a caption id. Without `--apply` it lists what it would do.

### A run: its pre caption

```bash
./console exp:migration:rollback 20261031-120000-a1b2c3 --apply
```

The caption taken before the run (`exp_migration_run.caption_pre`) is restored through the rollback of
`exp:upgrade`: first a `safety` caption of the current state (so the rollback itself can be undone;
`--no-safety-caption` skips it, refused on production without `--force-no-caption`), then the parts
(`--parts=database,storage,settings,libraries,code`, default `[CaptionSettings] RestoreParts[]`), then the
verification (`--no-verify` skips it). The run's row is written again with the state `rolled-back`. What each part
restores is in [Upgrading with the console: rolling back](upgrading-with-the-console.md#rolling-back).

Everything after the caption is gone, also changes made by editors since. Roll a run back soon after it, or use
`down()`.

### One migration: its down()

```bash
./console exp:migration:rollback 20261031120000_add_news_class --apply
```

The migration's `down()` runs (in a transaction where the database has one) and its record is removed, so it is
pending again. Only a migration that is `done` and has a `down()` can be taken back this way; otherwise the command
says so and points to the caption. When two sources have the same id, name one with `--source=site` or
`--source=extension:<name>`.

### Any earlier caption: for experts

```bash
./console exp:migration:caption --list
./console exp:migration:rollback 2026-10-29_10-15-00 --expert --confirm=2026-10-29_10-15-00 --apply
```

Any caption of the list can be restored, also a manual one or one of `exp:upgrade`, with `--expert` and the
caption's id typed again in `--confirm`. The same safety caption and parts as above apply.

### In the administration

**Setup > Migrations > Rollback** (`/migration/rollback`, policy `migration/rollback`) offers the same three:
the runs with a caption before them, the applied migrations that have a `down()`, and the earlier captions (for
experts: the box **I know what this restores** and the caption's id typed again, as `--expert --confirm`). For a run
or a caption it restores the parts database, storage and settings, ticked by default as in
`[CaptionSettings] RestoreParts[]`; libraries and code are restored on the console only. **Roll back** asks
"Verify it's you" and starts `exp:migration:rollback --apply` in the background as the signed-in user, with the
safety caption first; its page shows the progress and the report. **Roll back** in the history and
**Roll back this run** on a run's page lead there too.
[Run, dry run and rollback](../help/migrations/run-dry-run-and-rollback.md#roll-back) shows the page.

### What a rollback touches

| Area | Restore of a caption | `down()` |
|---|---|---|
| database | back to the dump (tables made since are dropped) | what `down()` undoes |
| `var/` storage | from git_manager's caption (files added since stay) | what `down()` undoes |
| `settings/override` | copied back (files added since stay) | what `down()` undoes |
| caches, search index | stale: run `./console exp:upgrade:finish --apply` | as after any change: clear what the migration touched |
| PHP-FPM, Velocity workers | keep the old classes until `exp:upgrade:finish --apply` | the same |

Every rollback is audited: `system.migration.rollback` with the verb `rollback-run`, `rollback-caption` or
`rollback` (one migration).

## Engines

| Database | Caption | Restore |
|---|---|---|
| SQLite | a consistent copy | the copy written back (SQLite backup API) |
| MySQL/MariaDB | `mysqldump` | through `mysql` |
| PostgreSQL | `pg_dump --clean` | through `psql` |
| MongoDB | `mongodump` | through the rollback of `exp:upgrade` |
| Oracle | none built in (export with `expdp`) | by hand: the run prints the steps |

## Related pages

- [Migrations: the hub](../help/migrations/README.md), [All recipes](../help/migrations/recipes/README.md),
  [Frequently asked questions](../help/migrations/faq.md)
- [The guide](migrations.md), [the tutorial](migrations-tutorial.md), [PHP migrations](migrations-php.md),
  [The migrations repository](migrations-repositories.md)
- [In the administration](../help/migrations/in-the-administration.md), [Commands](../help/migrations/commands.md),
  [Settings](../help/migrations/settings.md),
  [Policies, views and events](../help/migrations/policies-views-and-events.md)
- [The file format](../specifications/6.0/migration-file-format.md), [The feature](../features/6.0/migrations.md)
- [When a migration fails](../help/migrations/when-a-migration-fails.md),
  [Troubleshooting messages](migrations-troubleshooting.md)
- [Migrations versus migrating a site](../help/migrations/migrations-versus-migrating-a-site.md)
- [`exp:migration:rollback`](../examples/commands/expmigrationrollback.md), [`exp:migration:caption`](../examples/commands/expmigrationcaption.md)
- [migration.ini \[CaptionSettings\]](../examples/ini-settings/migration/CaptionSettings.md)
