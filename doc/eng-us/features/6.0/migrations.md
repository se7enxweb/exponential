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
| The kernel's upgrade steps | every step of `exp:upgrade` (the kernel's and the extensions') is a migration of the source `kernel`: listed first by the status and the administration, applied first by a run (stages `database` and `scripts`), and nameable in `requires` |
| Migration files | `YYYYMMDDHHMMSS_name.php` in the site's `migrations/`, in `extension/<name>/migrations/` of active extensions and in installed packages; a header of `key: value` comment lines (description, requires, environments, engines, maintenance, author); ordered by id and by `requires` |
| PHP migrations | a namespaced class that extends `expMigrationPhp` with `plan()`, `up()`, optionally `down()` and `detect()` |
| The record | tables `exp_migration` (one row per migration; also the record of `exp:upgrade` since 6.0.15) and `exp_migration_run` (one row per run), made by the upgrade step `6.0.15-migration-tables` |
| The runner | the installation lock shared with `exp:upgrade`, one transaction per migration where the engine has one, the run's user, maintenance mode per migration, a checksum guard against changed files, `ezpEvent` events and audit events |
| Captions | a caption before every run (reused while nothing changed), one after a run that passed, one of the failed state, and the restore of the pre caption after a failure; the captions are those of `exp:upgrade` |
| Rollback | a run (its pre caption), one migration (its `down()`), or, for experts, any caption |
| The migrations repository | the status file `status/<environment>.json` and the run log `runs/<run-id>.log`, committed, tagged and pushed after every run; failed pushes retried by the cronjob part `migrationpush` |
| Commands | `exp:migration:status`, `:migrate`, `:dry-run` (with `--simulate --diff`), `:rollback`, `:resume`, `:caption`, `:push`, `:check`, `:generate`; `:migrate` and `:resume` take `--run-id` (the id of the run to start) and `--progress` (a file the run appends its progress to, one JSON line per event), `:rollback` takes `--progress` |
| Setup > Migrations | the module `migration` with the views `status`, `history`, `log/<run-id>`, `stream/<run-id>` (the live progress), `dryrun`, `run` and `rollback/<target>`; the policies `migration/read`, `run`, `rollback` and `edit` (Administrator only in a new installation); "Verify it's you" before a run, a resume, a simulation and a rollback |
| Background runs | a run, resume or rollback started in the administration runs as a console process of its own as the signed-in user (`expMigrationJob`), goes on when the page is closed, and is followed live through its progress file; its files are in `var/<site>/migration/jobs/` |
| Dashboard block | `migrations`: pending, failed, orphans, the run going on and the last run (fetch function `migration/summary`) |
| Recording changes | a user's own changes (session) or every change (site-wide window) recorded at the kernel's operation level as the steps of a YAML migration, without personal data; tables `exp_migration_recording` and `exp_migration_recording_event` (upgrade step `6.0.15-migration-recording-tables`); the commands `exp:migration:record:start`, `:stop`, `:status`, `:list`, `:show`, `:discard`; Setup > Migrations > Recording and the Recording bar ([the guide](../../guides/migrations-generators-and-recording.md)) |
| Settings | `settings/migration.ini`: `[MigrationSettings]`, `[RunSettings]`, `[CaptionSettings]`, `[RepositorySettings]`, `[AdminSettings]` (`PhpCliPath`, `JobHistory`, `LogBytes`) |

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
| `expMigrationUpgradeSource`, `expMigrationUpgradeStep` | the kernel's upgrade steps (`exp:upgrade`) as migrations of the source `kernel` |
| `expMigrationAdmin`, `expMigrationJob`, `expMigrationProgress` | the forms and the dashboard summary of Setup > Migrations, the runs started there in the background, their progress file |
| `expMigrationRecording`, `expMigrationRecorder`, `expMigrationRecordingEvent`, `expMigrationRecordingResolver`, `expMigrationRecordingGenerator` | the recordings and their events, the tap in the audit hook points, an audit event as a migration step, the stable names read from the installation, the plan of a recording |

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

- Migrations are written in PHP, in YAML, in SQL (`sql`, `<engine>.sql`, `mongodb.json`) or as INI settings (`ini`)
  ([Migrations: SQL and settings](../../guides/migrations-sql-and-settings.md)).
- A migration run applies the kernel's upgrade steps of the stages `database` and `scripts` only; the stages `code`,
  `libraries` and `finish` stay with `exp:upgrade` (they are listed as `deferred`).
- Setup > Migrations shows, runs and rolls back migrations, records changes and generates a migration of a recording
  (the policy `migration/edit`); writing migrations, the other generators and the settings of the migrations
  repository are done in files and on the console.
- A migration generated from a recording or from what is there leaves out what no step type repeats (a swap of two
  locations, workflow triggers, settings of extensions, secret settings) and names it at the top of the file;
  generating from a diff and the converter of the reference bundle's YAML are not there yet.
- A run started in the administration needs `proc_open` and a PHP command line binary
  (`migration.ini [AdminSettings] PhpCliPath`); without them the page says so and the console runs it.
- Commits, tags and pushes need git_manager; without it a run says so and commits nothing.

## Settings

Every setting with its default: [migration.ini](../../examples/ini-settings/migration/README.md). The cronjob part:
[migrationpush](../../examples/cronjobs/migrationpush.md). Audit events: `system.migration.run`, `.apply`, `.fail`,
`.rollback`, `.caption`, `.push`, `.record` in `settings/audit.ini`.

## Related pages

- [Migrations: the hub](../../help/migrations/README.md), [All recipes](../../help/migrations/recipes/README.md),
  [Frequently asked questions](../../help/migrations/faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](../../help/migrations/in-the-administration.md),
  [Commands](../../help/migrations/commands.md), [Settings](../../help/migrations/settings.md),
  [Policies, views and events](../../help/migrations/policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../../help/migrations/when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../../help/migrations/migrations-versus-migrating-a-site.md)
- [Migrations in the administration](../../help/migrations/in-the-administration.md),
  [the views of the module migration](../../examples/module-views/migration/README.md),
  [its policies](../../examples/policies/migration.md)
- [Upgrading with the console](../../guides/upgrading-with-the-console.md)
