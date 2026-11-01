---
title: "Commit, tag and push the record of a run"
audience: developer, admin, software-engineer, hosting
category: migrations
order: 21
summary: Set up the migrations repository so every run's status file and log are committed, tagged migrations/<environment>/<run-id> and pushed; choose between the site's own repository and a separate one, push what failed again, and read the tags.
keywords: migration, migrations, schema change, database change, git, commit, tag, push, migrations repository, TagPattern, Mode=site, Mode=separate, push_state, exp:migration:push, migrationpush, cronjob, remote, origin
type: help
---
# Commit, tag and push the record of a run

After every run with `--apply`, the failed ones too, the migration engine writes the environment's status file and
the run's log, commits both, tags the commit and pushes commit and tag. This gives every environment a history in
git that anyone with a clone can read. This page sets it up and shows what to do when a push fails.

## What you'll have at the end

- `[RepositorySettings]` set for your installation, in the site's repository or a separate one;
- runs that end with `Repository: commit ..., tag ..., push pushed`;
- a cronjob or a command that pushes what could not be pushed.

## Before you start

- git_manager is installed and active: commits go through it. Without it a run says
  `git_manager is not active: the record of the run is not committed` and commits nothing.
- The system user that runs migrations can push to the remote (an SSH key or a credential helper). Nothing is stored
  by the migrations.

## Steps

### 1. Choose the repository

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Mode=site
Path=migrations
Remote=origin
Branch=
*/ ?>
```

| `Mode` | `Path` | Use it when |
|---|---|---|
| `site` (default) | a folder of the installation's own checkout | the site's repository may carry the run records next to the migration files; only the two files of a run are committed, whatever else is staged stays |
| `separate` | a git repository of its own (relative to the root, or absolute) | the site's history should stay free of run records, or several installations report to one repository |

`Branch` empty means the branch the repository is on; a repository on another branch, or on no branch, is not
committed to, and the report says why.

### 2. Choose the tag names

```ini
[RepositorySettings]
Tag=enabled
TagPattern=migrations/<environment>/<run-id>
```

The placeholders are `<environment>` (`default` when the installation names none), `<run-id>` and `<date>`
(`YYYYMMDD`). Keep `<run-id>` in the pattern: **a tag that exists is never moved or replaced**. A pattern that gives
the same name twice (only `<date>`) leaves the second commit untagged, with the warning
`the tag <name> exists already: it is never moved, the commit stays untagged`.

### 3. Run and read the repository line

```bash
./console exp:migration:migrate --apply
```

```text
Repository: commit 4e1f0a9c2b7d, tag migrations/production/20261101-120000-b2c3d4, push pushed
```

The commit message lists what the run did:

```text
Updated: Migration run 20261101-120000-b2c3d4 in production: 1 applied

- applied site:20261101090000_news_note_priority
```

### 4. Push what failed again

A failed push does not fail the run. The run's `push_state` is `pending`, and `exp:migration:status` warns
`1 run(s) wait for their push: ./console exp:migration:push --apply`.

```bash
./console exp:migration:push
./console exp:migration:push --apply --limit=20
```

```text
Exponential migrations: push
  pending  20261101-120000-b2c3d4
Pushed 1 of 1 tried, 0 still pending
```

| `push_state` | Means |
|---|---|
| `pushed` | commit and tag are on the remote |
| `pending` | the push failed; commit and tag are local and wait |
| `disabled` | `Commit=disabled`, `Push=disabled`, `--no-push` or `--no-commit` |
| `no-remote` | `Remote` is empty or not a remote of the repository |
| `none` | nothing was committed |

### 5. Let cron push

The cronjob part `migrationpush` is in no default part. Add it to one the site runs, or run it on its own:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-migrationpush]
Scripts[]=migrationpush.php
*/ ?>
```

```bash
php runcronjobs.php migrationpush
```

It does nothing while no run waits.

### 6. Read the tags

```bash
git fetch --tags
git tag -l 'migrations/production/*' --sort=version:refname | tail -5
git show migrations/production/20261101-120000-b2c3d4 --stat
```

Sort with `--sort=version:refname` (or `sort -V`), never by name alone.

## Switching parts off

| For one run | For the installation |
|---|---|
| `--no-commit` (no tag, no push either) | `Commit=disabled` |
| `--no-tag` | `Tag=disabled` |
| `--no-push` | `Push=disabled` |

A development installation often runs with `Push=disabled`; staging and production with all three on.

## Check it

`exp:migration:status` shows no run waiting for its push, and `git ls-remote --tags origin 'migrations/*'` lists the
tag of the last run.

## Next step

[Upgrade step or migration?](upgrade-step-or-migration.md): which of the two tools a change belongs in.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [The migrations repository](../../../guides/migrations-repositories.md),
  [migration.ini \[RepositorySettings\]](../../../examples/ini-settings/migration/RepositorySettings.md),
  [`exp:migration:push`](../../../examples/commands/expmigrationpush.md),
  [the cronjob part migrationpush](../../../examples/cronjobs/migrationpush.md)
- [Team workflow with git](team-workflow-with-git.md), [The audit trail of runs](the-audit-trail-of-runs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
