---
title: "When a migration fails: troubleshooting by symptom"
audience: developer, admin, software-engineer, hosting, dba
category: migrations
order: 30
summary: Troubleshooting migrations by what you see - a failed run, a refused run, a migration that does not run or runs where it should not, a held lock, nothing restored, a push that waits, a run in the administration that does not start, changes the site does not show - with the cause, what already happened automatically and what to do.
keywords: migration failed, migration error, troubleshooting, FAIL, exit 1, exit 2, refused, lock held, upgrade.lock, checksum, changed after it was applied, --force --reason, not-needed, blocked, deferred, orphan, pending, no migrations found, exp_migration does not exist, restore failed, caption failed, push failed, pending push, proc_open, PhpCliPath, running, resume, cache, Velocity, problem, help
featured: true
type: help
---
# When a migration fails: troubleshooting by symptom

Find what you see in the list below. Each entry says what happened, what Exponential already did by itself, and what
to do. For the exact text of a message, see [Troubleshooting messages](../../guides/migrations-troubleshooting.md);
the two pages link to each other.

First, three commands that answer most questions:

```bash
./console exp:migration:status            # every migration and its state, the latest runs, the lock, pushes that wait
./console exp:migration:migrate           # the dry run: what the next run would do, and why something would not run
./console exp:migration:check             # the files: names, headers, order, PHP, without a database
```

## Contents

1. [A run ended with FAIL](#a-run-ended-with-fail)
2. [A run is refused before it starts](#a-run-is-refused-before-it-starts)
3. [A migration does not run](#a-migration-does-not-run)
4. [A migration ran where it should not](#a-migration-ran-where-it-should-not)
5. [Captions and restores](#captions-and-restores)
6. [The repository](#the-repository)
7. [In the administration](#in-the-administration)
8. [After a run](#after-a-run)
9. [When to stop and ask](#when-to-stop-and-ask)
10. [Related pages](#related-pages)

## A run ended with FAIL

### A migration is `failed` and the run says `Restore: restored`

**What happened.** The migration threw an exception. Its transaction was rolled back (fully on PostgreSQL and SQLite,
the data only on MySQL/MariaDB and Oracle, nothing on MongoDB), the run stopped, the failed state was kept in a
caption of kind `migration-failure`, and the caption taken before the run was restored. The record of the failed run
was committed and pushed. The installation is as it was before the run.

**What to do.** Read the message on the run's page (Setup > Migrations > History) or in
`migrations/runs/<run-id>.log`. Fix the cause: in the migration (a new commit; the failed migration never ran, so it may
be edited), or in the installation (a missing parent, a setting). Then run again, or `./console exp:migration:resume
--apply`. [Captions and rollback in practice](recipes/captions-and-rollback-in-practice.md).

### A migration is `failed` and nothing was restored

**What happened.** The run had no caption (`--no-caption`, `[CaptionSettings] Before=disabled`, or the caption could
not be taken), or it ran with `--no-restore`. On MySQL/MariaDB and Oracle the failed migration's schema changes stay,
on MongoDB everything it wrote stays.

**What to do.** The report lists the steps to restore by hand. Without your own backup, undo the half-done change with
a new migration, or by hand, before you run again. Next time leave the captions on.

### `FAIL: ... changed after it was applied (checksum ... -> ...)`

**What happened.** The file of a migration that already ran was edited. A migration that ran is never run again, so the
run refuses it rather than leave installations that differ.

**What to do.** Undo the edit and put the change in a new migration. When the edit does not change what the migration
did (a comment, a typo): `./console exp:migration:migrate --apply --force --reason="a comment only"`. The new checksum
is recorded and the acceptance audited. [Reviewing a migration](recipes/reviewing-a-migration.md).

### A migration is `unknown`: `skipped, its state cannot be told`

**What happened.** Its `detect()` threw, for example because it reads a table the migration itself creates.

**What to do.** Make `detect()` return `null` when it cannot tell, and never let it need what the migration makes
([PHP migrations](../../guides/migrations-php.md)).

## A run is refused before it starts

### `another run holds the lock: ...`

**What happened.** An `exp:upgrade` or another migration run is writing. Only one may write at a time; the lock is the
file `var/<site>/upgrade.lock`, and its holder file says who holds it.

**What to do.** Wait. `./console exp:migration:status` names the holder and since when. A lock whose process ended is
reported as stale and taken over by the next run; nothing has to be removed by hand.

### `... change(s) that are not committed in the code trees (...)` (exit 2)

**What happened.** `settings/`, `extension/` or `design/` have changes git does not know. A caption keeps the commit
of the code, not the code itself, so a restore could not bring those changes back.

**What to do.** Commit them first. Or run with `--include-uncommitted`, which keeps them in the caption.

### `the table exp_migration does not exist yet`

**What happened.** The record tables come with the upgrade step `6.0.15-migration-tables`, which has not run.

**What to do.** `./console exp:upgrade --apply` (or `./console exp:upgrade:database --apply`).

### `the user to run as does not exist: [RunSettings] RunAsUser=admin`

**What to do.** Set `RunAsUser` to an existing administrator's login in `settings/override/migration.ini.append.php`,
or pass `--login=<login>` ([Settings](settings.md)).

### `the migration <id> requires ..., which does not exist` / `a cycle in requires` / `defined twice`

**What happened.** The order cannot be built: a `requires:` names an id no file has, two migrations wait for each
other, or one id is found in two sources. Nothing runs while one is there.

**What to do.** `./console exp:migration:check` lists every problem. Correct the header, remove the cycle, or rename
the second file (never one that ran) ([Team workflow with git](recipes/team-workflow-with-git.md)).

## A migration does not run

### `no migrations found`

Check the folder and the name: files directly in `migrations/` (not a subfolder), named `YYYYMMDDHHMMSS_name.php`.
An extension's migrations are found only while the extension is active; a package's only once it is installed.
`[MigrationSettings] Enabled=disabled` finds none. `exp:migration:check` prints the folders it searched.

### It is `not-needed`

The header limits it: `environments:` does not include this installation's environment (`site.ini [AdminEnvironment]
Level`, or `EXP_ENV` with `Level=auto`), or `engines:` does not include this database. Or its `detect()` says the
change is already there. Check with `./console exp:migration:status --environment=<name>`
([Per-environment migrations](recipes/per-environment-migrations.md)).

### It is `blocked`, or `waits for <id>`

It requires a migration that failed or has not run. Fix that one first. With `--only` or `--path`, add the requirement
to the selection.

### It is `deferred`

It is one of the kernel's upgrade steps of the stage `code`, `libraries` or `finish`, which a migration run leaves to
`exp:upgrade`; the warning names the command (`./console exp:upgrade:code --apply` ...). A migration that requires it
waits ([Upgrade step or migration?](recipes/upgrade-step-or-migration.md)).

### `no statements for <engine>` (an SQL migration)

The migration has no file for the engine of this installation, and its portable `.sql` file (if any) is not for it
(MongoDB takes a `.mongodb.json`). Add the engine's file, or `missing: skip` on an `sql` step that has nothing to do
there ([Migrations: SQL and settings](../../guides/migrations-sql-and-settings.md)).

### The PHP file is refused: no class, two classes, a class declared twice

Each file declares exactly one non-abstract class that extends `\expMigrationPhp`, in a namespace, with a name no
other file uses. `exp:migration:generate --skeleton` writes it that way ([PHP migrations](../../guides/migrations-php.md)).

### `...::definition() has the name of a method of expMigrationPhp that a migration cannot declare`

The class declares a method PHP would refuse while loading the file: a helper named like one of the base class's own
methods (`definition()`, `file()`, `id()`, `source()`, `checksum()`, `state()` ...), or `plan()`, `up()`, `down()`,
`detect()` declared private, static, with a type on `$context` or with more required parameters. `exp:migration:check`
and every run read the class before loading it and name the method; the other migrations are checked and run as
usual. Rename the helper, or declare the hook as [PHP migrations](../../guides/migrations-php.md#the-methods) shows it.

## A migration ran where it should not

### It ran on production but was meant for development only

Its header had no `environments:` line, or the installation's environment is not set (`Level` empty: every migration
without `environments:` runs). Add the line in a **new** migration that undoes the change where it does not belong,
and set `Level` on every installation. [Per-environment migrations](recipes/per-environment-migrations.md).

### It ran twice, or ran again after a restore

A restore puts the record back with the database, so the migrations after the caption are pending again and run on
the next run. That is intended. A migration must be safe to run on any installation that has not recorded it: create
only what is missing, find things by remote id.

## Captions and restores

### `no caption could be taken, the run goes on without a way back`

The caption failed (no dump tool for the engine, a full disk, git_manager failing) and the run went on. Test with
`./console exp:migration:caption --apply`, fix the cause, or take a backup of your own before the next run.

### `no built-in restore for oracle: restore it by hand`

Oracle captions need an export of your own, and the restore is by hand (the steps are printed)
([Engine notes](recipes/engine-notes.md)).

### `the restore of the caption <id> failed`

The failed state is kept in the `migration-failure` caption. Restore by hand: `./console exp:migration:rollback
<run-id> --apply` (or `./console exp:upgrade:rollback <caption> --apply`), then `./console exp:upgrade:finish --apply`.

## The repository

### `1 run(s) wait for their push`

The push failed (network, expired key, a remote that refused). Commit and tag are local. Fix the remote or its
credentials, then `./console exp:migration:push --apply`, or let the cronjob part `migrationpush` do it
([Pushing tags](recipes/pushing-tags.md)).

### `git_manager is not active` / `is not a git repository` / `is on the branch ...`

The record was not committed. Install and activate git_manager, make the folder a repository, or check out the branch
`[RepositorySettings] Branch` names; on an installation without git, set `Commit=disabled`.

### `the tag <name> exists already`

Tags are never moved. Keep `<run-id>` in `[RepositorySettings] TagPattern`.

## In the administration

### The Run button is refused, or the page says the run cannot start

Another run goes on, or `exp:upgrade` holds the lock; or the server cannot start a background process: the page needs
`proc_open` and a PHP command line binary (`migration.ini [AdminSettings] PhpCliPath`). Run it on the console instead
([Run, dry run and rollback](run-dry-run-and-rollback.md#when-a-run-cannot-start)).

### "Verify it's you" keeps asking

Each run, resume, simulation and rollback asks once; it is the `site.ini [ReauthGroup_system]` group
([Verify it's you](../../guides/verify-its-you.md)).

### A run stays `running` for ever

Its process was killed (a server restart, a time limit of the process manager). `./console exp:migration:resume`
finds it, and with `--apply` closes it as failed and runs what is still pending in a new run.

### The page says "You do not have access"

The role lacks `migration/read` (to see), `migration/run` (to run or simulate) or `migration/rollback`
([Policies, views and events](policies-views-and-events.md)).

## After a run

### The site does not show the change

A new class, setting or template needs a deploy like any code change: `./console exp:velocity deploy` (caches,
PHP-FPM, Velocity). After a run that applied kernel steps, or after a restore: `./console exp:upgrade:finish --apply`.

### A migration is listed as an `orphan`

It is recorded as done but its file is gone (deleted or renamed). Put the file back under its old name; a migration
that ran is never renamed. The row harms nothing.

### Content was created by the wrong user, or a permission is missing

A run runs as the signed-in user in the administration, and as `[RunSettings] RunAsUser` (or `--login`) on the
console. Run as a user with the rights the migration needs.

## When to stop and ask

Stop and ask the migration's author (or your hosting company) before you run again when: the restore failed; the
engine is Oracle or MongoDB and a migration failed halfway; a migration changed content that editors have changed
since; or `exp:upgrade:verify` reports drift after a restore. Send the run id, the run log and the output of
`./console exp:migration:status --format=json`; neither holds secrets or e-mail addresses.

## Related pages

- [Migrations: the hub](README.md), [All recipes](recipes/README.md), [Frequently asked questions](faq.md)
- When a deploy to remotes fails: [Deploy step by step](deploy-step-by-step.md#when-something-goes-wrong); a recording or the editor: [Recording changes](recording.md#when-something-goes-wrong), [The migration editor](the-migration-editor.md#when-something-goes-wrong)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](in-the-administration.md), [Commands](commands.md), [Settings](settings.md),
  [Policies, views and events](policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
