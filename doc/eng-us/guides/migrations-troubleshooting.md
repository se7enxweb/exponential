# Migrations: troubleshooting

This page lists the messages the migration commands print, what each means and what to do. Messages that start with
`FAIL:` are errors (the exit code is not 0); `WARN` lines are warnings, the command still passed. Every command can
also print its report as JSON (`--format=json`) with the same texts in `errors` and `warnings`.

Looking by what you see rather than by message (a run that failed, a migration that does not run, a push that waits,
a run in the administration that does not start)? [When a migration fails: troubleshooting by symptom](../help/migrations/when-a-migration-fails.md).

[Guides](README.md) · [Migrations: the hub](../help/migrations/README.md) · [Migrations](migrations.md) · Related: [Rollback and captions](migrations-rollback-and-captions.md),
[The migrations repository](migrations-repositories.md), [PHP migrations](migrations-php.md)

## Exit codes

| Code | Means |
|---|---|
| 0 | done, or nothing to do |
| 1 | a migration failed, a check found a problem, the lock was held, a changed file was refused, or something is pending with `exp:migration:status --fail-on-pending` |
| 2 | usage: an option is wrong or missing (`--apply` with `--dry-run`, `--force` without `--reason`, an unknown target), or uncommitted code refused a run |
| 3 | the database is not reachable |
| 4 | internal error |

## Before a run

**`the table exp_migration does not exist yet: run ./console exp:upgrade --apply first (step 6.0.15-migration-tables)`**
The record tables are made by an upgrade step. Run `./console exp:upgrade --apply` (or only the database stage,
`./console exp:upgrade:database --apply`). `exp:migration:status` still lists the migrations and warns instead.

**`no migrations found`** (warning)
No file `YYYYMMDDHHMMSS_name.<type>` in the folders searched. Check `exp:migration:check`, which prints the folders it
looked in; `[MigrationSettings] Enabled=disabled` finds none; an extension's migrations are found only while the
extension is active.

**`no migrations directory: [MigrationSettings] DirectoryList in migration.ini names none`**
`exp:migration:generate` has no folder to write to. Set `DirectoryList[]=migrations` in
`settings/override/migration.ini.append.php`.

**`<file> is not a migration file name (YYYYMMDDHHMMSS_name.type, ...)`, `<file> does not start with a valid date and time (YYYYMMDDHHMMSS)`, `<file> has the unknown type ...`, `unknown header key ...`, `the header key ... is given twice`, `maintenance: ... is not yes or no`**
The lint of the file names and headers ([the file format](../specifications/6.0/migration-file-format.md)). Fix the
file; `exp:migration:check` shows every problem at once.

**`the migration <id> requires <dep>, which does not exist`**, **`... which does not run in the environment <env>`**, **`the migration <id> requires the extension <name>, which is not active`**
A requirement in the header names an id no file has, a migration limited to other environments, or an extension
that is not active. Correct the header, or activate the extension.

**`the migrations <ids> wait for each other (a cycle in requires ...)`**, **`the migration <id> is defined twice (...)`**
Two migrations require each other, or one id exists in two sources (the first found is used: site before extensions
before packages). Nothing runs while one is there. Remove the cycle, or rename the second file.

**`... requires ..., which is pending and not selected: add it to --only or --path`**
`--only` or `--path` picked a migration whose requirement has not run. Add the requirement to the selection, or run
without one.

**`no statements for <engine>; missing: skip lets the step pass`**
An SQL migration or `sql` step has nothing for the engine of this installation: add the engine's file
(`<id>.<engine>.sql`, `<id>.mongodb.json`) or key, or `missing: skip` when nothing is to be done there
([SQL and settings](migrations-sql-and-settings.md)).

**INI: `line N: [Block] does not name its file`**
A block of an INI migration names the file it changes: `[site.ini/Block]`, or `[siteaccess:<name> site.ini/Block]`
([SQL and settings](migrations-sql-and-settings.md#settings-ini-migrations-and-the-step-type-settings)).

**PHP: `no class: a PHP migration declares one class that extends expMigrationPhp`, `more than one class extends expMigrationPhp`, `the class ... is declared by ... too`, `PHP syntax: ...`**
Each PHP file declares exactly one non-abstract class that extends `\expMigrationPhp`, with a name no other file
declares; use a namespace ([PHP migrations](migrations-php.md#the-file-and-the-class)).

**`no namespace: two migrations of different sites or extensions could declare the same class`** (warning)
Put the class in a namespace, as the skeleton does.

**`<n> change(s) that are not committed in the code trees (...): commit them first, or run with --include-uncommitted to keep them in the caption`** (exit 2)
`settings/`, `extension/` or `design/` (`[CaptionSettings] CodeTreeList[]`) have changes git does not know. A caption
keeps the commit of the code, not the code. Commit the changes, or run with `--include-uncommitted`.

**`the user to run as does not exist: [RunSettings] RunAsUser=admin (migration.ini)`**
The console runs as `RunAsUser`, found by login. Set it to an existing administrator, or pass `--login=<login>`.

## The lock

**`another run holds the lock: migration run <run-id> by <user> (pid <pid> on <host>) since <time>`**
An `exp:upgrade` or another migration run is writing. Wait for it; `./console exp:migration:status` shows the holder.

**A stale lock: `..., stale: its process has ended`**
The lock is a file lock held by the process, so a run that dies frees it at once. What can stay behind is the holder
file `var/<site>/upgrade.lock.holder`; the status then reports the holder as stale and the next run takes over and
writes its own. Nothing has to be removed by hand.

## During a run

**`<id> changed after it was applied (checksum ... -> ...); a run with --force --reason=<text> accepts the change`**
The file of a migration that ran was edited since. Undo the edit and put the change in a new migration, or accept it
when it does not change what the migration did (a comment, a typo):
`./console exp:migration:migrate --apply --force --reason="a comment only"`. The migration is not run again; the new
checksum is recorded and the acceptance audited.

**`--force needs a --reason=<text>: why the changed migrations are accepted`**, **`--reason goes with --force`** (exit 2)
The two options go together.

**A migration `failed` with a message**
The exception `up()` threw. The run stopped, the migration's transaction was rolled back and the caption taken before
the run was restored (`Restore: restored`). Fix the cause and run again, or `./console exp:migration:resume --apply`.

**`<id>: skipped, its state cannot be told: ...`** (warning, state `unknown`)
`detect()` threw. The migration is not run; fix `detect()` (it must not need anything the migration creates).

**`blocked` / `waits for <id>`**
With `[RunSettings] StopOnFailure=disabled`, a migration that requires a failed one is not run.

**Partial changes on MySQL/MariaDB, Oracle and MongoDB**
A failed migration's schema changes stay on MySQL/MariaDB and Oracle (they commit at once), and on MongoDB every
change stays: there the restore of the caption is the way back. With `--no-restore`, or without a caption, undo the
changes by hand or restore the caption: `./console exp:migration:rollback <run-id> --apply`.

## Captions and restore

**`no caption could be taken, the run goes on without a way back: ...`** (warning)
The caption failed (no dump tool for the engine, a full disk, git_manager failing). The run went on. Fix the cause
(`./console exp:migration:caption --apply` takes one to test), or stop and take a backup yourself.

**`no caption before the run (--no-caption or [CaptionSettings] Before=disabled): a failure cannot be restored automatically`** (warning)
As it says. On a failure the report then lists the steps to restore by hand.

**`no caption was taken before the run: nothing was restored; restore by hand:`** followed by numbered steps
A failure without a caption. Follow the steps: maintenance mode on, restore the database from your own dump, put back
`settings/override` and `var/`, `./console exp:velocity deploy`, `./console exp:upgrade:verify --after`, maintenance
mode off.

**`no built-in restore for oracle: restore it by hand`**
Oracle captions have no built-in restore. The report lists the steps (an `impdp` of your own export).

**`the restore of the caption <id> failed: ...`**
The automatic restore did not go through. The failed state is in the `migration-failure` caption. Restore by hand:
`./console exp:migration:rollback <run-id> --apply` (or `exp:upgrade:rollback <caption> --apply`), then
`./console exp:upgrade:finish --apply`.

**`the caption after the run could not be taken: ...`** (warning)
The run passed; only the re-caption failed. The next run takes a new pre caption.

## The repository

These are warnings: the run itself passed or failed on its own.

**`git_manager is not active: the record of the run is not committed`**
Commits go through git_manager. Install it, or set `[RepositorySettings] Commit=disabled`.

**`<path> is not a git repository ([RepositorySettings] Mode=...)`**
With `Mode=site` the installation is not a git checkout; with `Mode=separate` the folder `Path` is not a repository.
Run `git init` there, or set `Commit=disabled`.

**`<path> is on the branch <x>, not <y> ([RepositorySettings] Branch)`**, **`... is on no branch`**
Check out the branch, or empty `Branch`.

**`git add failed for ... (ignored by .gitignore?)`**
`migrations/status/` or `migrations/runs/` are ignored. Remove them from `.gitignore`.

**`the tag <name> exists already: it is never moved, the commit stays untagged`**
A run id was reused or the `TagPattern` gives the same name twice (for example only `<date>`). Include `<run-id>`.

**`the push to origin failed (exit 128): the commit stays local and is pushed again by the cronjob part migrationpush`**
Fix the remote or its credentials, then `./console exp:migration:push --apply`, or let the cronjob part
`migrationpush` do it.

**`not pushed: the repository has no remote origin`**
Add the remote, or set `[RepositorySettings] Remote` to the right name (empty for no push).

## After a run

**Velocity or PHP-FPM do not see a change**
A migration that adds a class, a setting or a template needs a deploy like any code change:
`./console exp:velocity deploy` (caches, PHP-FPM, Velocity), or `./console exp:upgrade:finish --apply` after a restore.

**`<id> (site) is recorded as done, but its file is not found any more`** (warning, `orphan`)
The record has a migration whose file was deleted or renamed. Rename it back; a migration that ran should not be
renamed. The row stays in the record and harms nothing.

**A run whose row still says `running`**
The process was killed. `./console exp:migration:resume` finds it (no process holds the lock any more) and, with
`--apply`, closes it as failed and runs what is still pending in a new run.

## Related pages

- [Migrations: the hub](../help/migrations/README.md), [All recipes](../help/migrations/recipes/README.md),
  [Frequently asked questions](../help/migrations/faq.md)
- [The guide](migrations.md), [the tutorial](migrations-tutorial.md), [PHP migrations](migrations-php.md),
  [Rollback and captions](migrations-rollback-and-captions.md),
  [The migrations repository](migrations-repositories.md)
- [In the administration](../help/migrations/in-the-administration.md), [Commands](../help/migrations/commands.md),
  [Settings](../help/migrations/settings.md),
  [Policies, views and events](../help/migrations/policies-views-and-events.md)
- [The file format](../specifications/6.0/migration-file-format.md), [The feature](../features/6.0/migrations.md)
- [When a migration fails](../help/migrations/when-a-migration-fails.md)
- [Migrations versus migrating a site](../help/migrations/migrations-versus-migrating-a-site.md)
