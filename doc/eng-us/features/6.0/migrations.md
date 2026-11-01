# Migrations in the kernel

Exponential 6.0.15 adds migrations to the kernel: changes to an installation (a table, content, a class, a role, a
setting) written once in files under version control and applied to every installation of a site in the same
order, with a record of what ran where, a caption (backup) before every run, an automatic restore after a failure,
and a commit, tag and push of every run's record. Before 6.0.15 such changes were applied by hand or by update
scripts, and nothing recorded which ran.

The guide that teaches it is [Migrations](../../guides/migrations.md); start with
[the tutorial](../../guides/migrations-tutorial.md).

Quick start:

```bash
./console exp:migration:generate --skeleton add_news_class --apply
./console exp:migration:check
./console exp:migration:migrate              # dry run
./console exp:migration:migrate --apply
./console exp:migration:status
```

## What 6.0.15 adds

| Part | What it is |
|---|---|
| Migration files | `YYYYMMDDHHMMSS_name.php` in the site's `migrations/`, in `extension/<name>/migrations/` of active extensions and in installed packages; a header of `key: value` comment lines (description, requires, environments, engines, maintenance, author); ordered by id and by `requires` |
| PHP migrations | a namespaced class that extends `expMigrationPhp` with `plan()`, `up()`, optionally `down()` and `detect()` |
| The record | tables `exp_migration` (one row per migration; also the record of `exp:upgrade` since 6.0.15) and `exp_migration_run` (one row per run), made by the upgrade step `6.0.15-migration-tables` |
| The runner | the installation lock shared with `exp:upgrade`, one transaction per migration where the engine has one, the run's user, maintenance mode per migration, a checksum guard against changed files, `ezpEvent` events and audit events |
| Captions | a caption before every run (reused while nothing changed), one after a run that passed, one of the failed state, and the restore of the pre caption after a failure; the captions are those of `exp:upgrade` |
| Rollback | a run (its pre caption), one migration (its `down()`), or, for experts, any caption |
| The migrations repository | the status file `status/<environment>.json` and the run log `runs/<run-id>.log`, committed, tagged and pushed after every run; failed pushes retried by the cronjob part `migrationpush` |
| Commands | `exp:migration:status`, `:migrate`, `:dry-run` (with `--simulate --diff`), `:rollback`, `:resume`, `:caption`, `:push`, `:check`, `:generate` |
| Settings | `settings/migration.ini`: `[MigrationSettings]`, `[RunSettings]`, `[CaptionSettings]`, `[RepositorySettings]` |

## The building blocks

| Class (`kernel/classes/migration/`) | Role |
|---|---|
| `expMigration` | the interface of a migration (id, source, state, plan, up, down) |
| `expMigrationDefinition` | a file on disk: name, header, checksum |
| `expMigrationLocator` | finds the files of the site, the extensions and the packages |
| `expMigrationGraph` | the order, and the errors of the order (duplicates, missing requirements, cycles) |
| `expMigrationPhp` | the base class of a PHP migration |
| `expMigrationContext` | what a run works on: database, user, environment, references, record, lock, log |
| `expMigrationRunner` | runs migrations, and takes one back with `down()` |
| `expMigrationRunFlow` | captions, restore, commit, tag and push around a run |
| `expMigrationCaption`, `expMigrationRollback`, `expMigrationResume` | captions, the three rollbacks, resuming a run |
| `expMigrationRecord`, `expMigrationStatusMirror`, `expMigrationRepository` | the tables, the files in git, git |
| `expMigrationLock` | the installation lock with a holder file (also the lock of `exp:upgrade`) |
| `expMigrationStatus`, `expMigrationCheck`, `expMigrationSimulation`, `expMigrationGenerator` | the status, the lint, the simulation, the skeleton |

The commands are `bin/php/expmigration<name>.php` (alias `exp:migration:<name>`), their code in
`kernel/private/classes/commands/`. Their machine output is `exp-migration/1` (`--format=json`); the exit codes are
those of `exp:upgrade` (0, 1 failed, 2 usage, 3 database unreachable, 4 internal).

## Engines

| Database | Transactions | Simulation | Caption and restore |
|---|---|---|---|
| SQLite | full | yes | yes |
| PostgreSQL | full | yes | yes |
| MySQL/MariaDB | data only (schema changes commit at once) | no | yes |
| MongoDB | none | no | yes |
| Oracle (ezoracle) | data only | no | a caption needs an export of your own; the restore is by hand (the steps are printed) |

## Limits in 6.0.15

- Migrations are written in PHP. Files of the types `yml`, `sql`, `<engine>.sql` and `ini` are found, ordered and
  checked, and refused by the runner until their steps arrive.
- The administration has no migrations view yet; everything runs from the console.
- `exp:upgrade` keeps its own commands; its kernel steps are recorded in `exp_migration` (source `kernel`) but are
  not listed by `exp:migration:status` yet.
- Commits, tags and pushes need git_manager; without it a run says so and commits nothing.

## Settings

Every setting with its default: [migration.ini](../../examples/ini-settings/migration/README.md). The cronjob part:
[migrationpush](../../examples/cronjobs/migrationpush.md). Audit events: `system.migration.run`, `.apply`, `.fail`,
`.rollback`, `.caption`, `.push` in `settings/audit.ini`.

## Read on

- [Migrations](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md)
- [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md),
  [Troubleshooting](../../guides/migrations-troubleshooting.md)
- [The migration file format](../../specifications/6.0/migration-file-format.md)
- [Upgrading with the console](../../guides/upgrading-with-the-console.md)
