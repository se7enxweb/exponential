---
title: "Captions and rollback in practice"
audience: developer, admin, software-engineer, hosting
category: migrations
order: 19
summary: Walk through the ways back after a migration run - the automatic restore after a failure, rolling back a whole run, one migration with its down(), an earlier caption for experts - with the commands, their output and what each one touches.
keywords: migration, migrations, schema change, database change, caption, captions, rollback, roll back, restore, down(), safety caption, exp:migration:rollback, exp:migration:caption, exp:upgrade:captions, --expert, --confirm, --parts, backup
featured: true
type: help
---
# Captions and rollback in practice

Every migration run with `--apply` takes a caption first: a safe point with the database, the `var/` storage and
`settings/override`. This page walks through the four ways back, in the order you will usually need them, on the news
site of the recipes. [Rollback and captions](../../../guides/migrations-rollback-and-captions.md) is the reference.

## What you'll have at the end

- seen a failed run restored automatically, and know where its failed state is kept;
- rolled back a whole run, one migration, and an earlier caption;
- a checklist for what to do after a restore.

## Before you start

- A development installation with captions on (the default) and at least one applied migration.
- With git_manager installed, captions are git_manager's full captions; without it, the database dump of
  `exp:upgrade` in `var/backup/upgrade/` (or `[CaptionSettings] BackupDirectory`) without the storage.

## Steps

### 1. List the captions

```bash
./console exp:migration:caption --list
```

```text
Exponential migrations: the captions, newest first
  20261101-101502          migration-post    2026-11-01 10:15  sqlite     migration run 20261101-101500-3f9a1c in development post: 20261101100000_news_article_class
      ./console exp:migration:rollback 20261101-101502 --expert --confirm=20261101-101502 --apply
  20261101-101500          migration-pre     2026-11-01 10:15  sqlite     migration run 20261101-101500-3f9a1c in development pre: 1 migration(s) 20261101100000_news_article_class
      ./console exp:migration:rollback 20261101-101500-3f9a1c --apply
2 caption(s)
```

It is the same list as `./console exp:upgrade:captions`: upgrades and migrations share their captions. Take one by
hand before a risky change with `./console exp:migration:caption --label="before the news import" --apply`.

### 2. A failure restores the caption by itself

When a migration throws, the runner rolls back its transaction, keeps the failed state in a caption of kind
`migration-failure` and restores the pre caption:

```text
  failed      20261101103000_news_import                    site               Imports the old news
              the import file is missing

Captions: pre 20261101-103001, failure 20261101-103001-2
Restore: restored (caption 20261101-103001)
WARN restored the caption 20261101-103001 taken before the run (database, storage, settings); the failed state is kept in the caption 20261101-103001-2
FAIL (exit 1)
```

Fix the migration and run again. To look at what the failure left, restore the failure caption on a copy of the
site, never on production.

### 3. Roll back a whole run

```bash
./console exp:migration:rollback 20261101-101500-3f9a1c
./console exp:migration:rollback 20261101-101500-3f9a1c --apply
```

Without `--apply` the command lists what it would restore. With it: a `safety` caption of the current state first
(so the rollback can be undone), then the parts of `[CaptionSettings] RestoreParts[]` (`database`, `storage`,
`settings`), then the verification. The run's row gets the state `rolled-back`.

Everything after the caption is gone, also what editors published since. Roll back a run soon after it, or prefer
`down()`.

### 4. Roll back one migration with down()

```bash
./console exp:migration:rollback 20261101100000_news_article_class --apply
```

```text
Exponential migrations: rollback with down()
Run 20261101-104210-a0b1c2, environment development, database sqlite (transactions: full), as admin

  undone      20261101100000_news_article_class             site               The class news_article
              removed the class news_article

Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 undone
PASS
```

Only a `done` migration with a `down()` can be taken back this way; the record is removed and it is pending again.
When two sources have the same id, add `--source=site` or `--source=extension:<name>`.

### 5. Restore an earlier caption (experts)

```bash
./console exp:migration:rollback 20261031-193128 --expert --confirm=20261031-193128 --apply
./console exp:migration:rollback 20261031-193128 --expert --confirm=20261031-193128 --parts=database --apply
```

Any caption of the list can be restored with `--expert` and its id typed again in `--confirm`. `--parts` picks parts
(`database`, `storage`, `settings`, `libraries`, `code`). `--no-safety-caption` skips the safety caption; on
production it is refused unless you add `--force-no-caption`. `--no-verify` skips the verification.

### 6. Make the restore live

```bash
./console exp:upgrade:finish --apply
```

It clears the caches and reloads PHP-FPM and Velocity, so no worker keeps the old classes or pages.

## What each way back touches

| | Restore of a caption (a run, an earlier caption, the automatic restore) | `down()` of one migration |
|---|---|---|
| database | back to the dump; tables made since are dropped | what `down()` undoes |
| `var/` storage | from git_manager's caption; files added since stay | what `down()` undoes |
| `settings/override` | copied back; files added since stay | what `down()` undoes |
| caches, search index | stale until `exp:upgrade:finish --apply` | clear what the migration touched |
| editors' work since | lost | kept |

## Engines

SQLite, PostgreSQL, MySQL/MariaDB and MongoDB are captured and restored by the commands above. Oracle has no built-in
caption: export the schema with `expdp` before the run. On a failure the run stops with `Restore: manual` and prints
the steps: maintenance mode on, `impdp` of your export, put back `settings/override` and `var/`,
`./console exp:velocity deploy`, `./console exp:upgrade:verify --after`, maintenance mode off.

## Check it

```bash
./console exp:migration:status
```

The history lists the run as `rolled-back`; the audit log has `system.migration.rollback` with the verb
`rollback-run`, `rollback-caption` or `rollback`.

## Next step

[The audit trail of runs](the-audit-trail-of-runs.md): where every run, caption and rollback is recorded.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Rollback and captions](../../../guides/migrations-rollback-and-captions.md),
  [Writing a reversible down()](writing-a-reversible-down.md), [Run, dry run and rollback](../run-dry-run-and-rollback.md)
- [`exp:migration:rollback`](../../../examples/commands/expmigrationrollback.md),
  [`exp:migration:caption`](../../../examples/commands/expmigrationcaption.md),
  [`exp:upgrade:captions`](../../../examples/commands/upgradecaptions.md),
  [migration.ini \[CaptionSettings\]](../../../examples/ini-settings/migration/CaptionSettings.md)
- [Restoring a database](../../database-administrators/restoring-a-database.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
