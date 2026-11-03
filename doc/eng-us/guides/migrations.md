# Migrations: changes to an installation, written once and applied everywhere

This guide explains the migrations of the Exponential 6.0.15 kernel: a change to an installation (a table, content,
a role, a setting) written once in a file under version control and applied to every installation of the site, in
the same order, with a record of what ran where. It is the overview; the pages linked at the end teach each part.

It is for developers who change a site's database or content in code, and for administrators who apply those
changes on staging and production. The commands below were run on SQLite on 31 October 2026.

The help center's [Migrations](../help/migrations/README.md) section starts from here: 25
[recipes](../help/migrations/recipes/README.md) (a table, a class, content in bulk, roles, settings, CI and deploy),
pages for each audience, [frequently asked questions](../help/migrations/faq.md) and
[troubleshooting by symptom](../help/migrations/when-a-migration-fails.md). Moving a site to another server or
release is a different job: [Migrations versus migrating a site](../help/migrations/migrations-versus-migrating-a-site.md).

[Guides](README.md) · [Migrations: the hub](../help/migrations/README.md) · Related: [Migrations: a first PHP migration, step by step](migrations-tutorial.md),
[PHP migrations](migrations-php.md), [Rollback and captions](migrations-rollback-and-captions.md),
[The migrations repository](migrations-repositories.md), [Troubleshooting](migrations-troubleshooting.md),
[The feature](../features/6.0/migrations.md), [The file format](../specifications/6.0/migration-file-format.md)

## In short

```bash
./console exp:migration:generate --skeleton add_news_class --apply   # a new PHP migration in migrations/
./console exp:migration:check                                         # the lint: names, headers, order, PHP
./console exp:migration:status                                        # what is done, what is pending
./console exp:migration:migrate                                       # dry run: what would change
./console exp:migration:migrate --apply                               # caption, run, re-caption, commit, tag, push
./console exp:migration:rollback <run-id> --apply                     # back to the caption taken before the run
```

Run the commands from the installation root as the user the web server runs as (as root, add
`--allow-root-user`). Every command that writes is a dry run until it is given `--apply`.

## What a migration is, and when to use one

A migration is a file named `YYYYMMDDHHMMSS_name.<type>`, for example `20261031120000_add_news_class.php`. Its id is
the file name without the type. It holds one change and, where it can, the way back. Once it has run on an
installation, the record says so and it never runs there again.

| You want to ... | Use |
|---|---|
| bring the database and settings of an installation up to a new **kernel release** | [`exp:upgrade`](upgrading-with-the-console.md) (the kernel's upgrade steps) |
| ship a table, column or default row with an **extension**, applied on every upgrade | [an upgrade step of the extension](upgrade-steps-for-extensions.md), or a migration in `extension/<name>/migrations/` |
| change **one site**: a class, content, a role, a setting, a table of the site, on development, staging and production in turn | a migration in the site's `migrations/` |
| copy a whole site with its content to another installation | a [site package](packages.md) |

Migrations and `exp:upgrade` share the record table `exp_migration`, the installation lock and the captions, so an
upgrade and a migration run never write at the same time and `exp:upgrade:captions` lists the captions of both.
The upgrade steps are migrations too, of the source `kernel`: `exp:migration:status` lists them first, and
`exp:migration:migrate --apply` applies the pending ones of the stages `database` and `scripts` before the site's
migrations; the stages `code`, `libraries` and `finish` stay with `exp:upgrade`
([The steps among the migrations](upgrading-with-the-console.md#the-steps-among-the-migrations)). A migration that
needs a step's change names the step in `requires`.

## The life cycle

1. **Write**: `exp:migration:generate --skeleton <name> --apply` writes an empty PHP migration with its header,
   `plan()`, `up()` and `down()`; fill them in ([PHP migrations](migrations-php.md)).
2. **Check**: `exp:migration:check` checks the files without a database: the names, the headers, the order, the PHP
   syntax and the class. It is the lint for CI.
3. **Dry run**: `exp:migration:migrate` (or `exp:migration:dry-run`) lists every pending migration with what its
   `plan()` says it would change. `exp:migration:dry-run --simulate --diff` runs them in a transaction that is rolled
   back and shows what changed in the database, on PostgreSQL and SQLite.
4. **Run**: `exp:migration:migrate --apply`. Under the installation lock, as the run's user:
   - the code trees (`settings/`, `extension/`, `design/`) are checked for changes that are not committed;
   - a **caption** of kind `migration-pre` is taken (or the newest one reused while nothing has changed since);
   - the pending migrations run in order, each in a transaction where the database has one;
   - the run stops at the first failure and the pre caption is restored;
   - after a run that passed, a caption of kind `migration-post` is taken (the re-caption);
   - the status file and the run log are **committed**, the commit is **tagged** and **pushed** in the migrations
     repository.
5. **Look at it**: `exp:migration:status` shows every migration with its state, the latest runs and what waits.
6. **Go back** if needed: `exp:migration:rollback <run-id>` restores the caption taken before a run,
   `exp:migration:rollback <migration-id>` runs one migration's `down()`.

## Where migration files live

| Source | Folder | Setting |
|---|---|---|
| `site` | `migrations/` of the installation | `migration.ini [MigrationSettings] DirectoryList[]` |
| `extension:<name>` | `extension/<name>/migrations/` of every **active** extension | `ExtensionDirectory` |
| `package:<name>` | `migrations/` of every installed package below `var/<storage>/packages/` | `ExtensionDirectory` |

A package's migrations also run when the package is installed, and the kickstarter runs every pending migration
after it created the sites: [Packages, deploys and long runs](migrations-packages-deploy-and-long-runs.md).

Only the files directly in a folder are migrations; subfolders (the status files and run logs) are not searched. A
file whose name does not start with `YYYYMMDDHHMMSS_` (a README, a `.gitkeep`) is skipped.
`[MigrationSettings] Enabled=disabled` finds none at all. Every setting is listed in
[the migration.ini reference](../examples/ini-settings/migration/README.md).

## Types

The type is the file's extension: `php`, `yml`, `sql`, `<engine>.sql` (`mysql`, `pgsql` or `postgresql`, `sqlite`,
`oracle`), `mongodb.json` and `ini`. A migration is written in **PHP**, in **YAML**, in **SQL** (a file per engine, or
one portable file) or as **INI** settings; SQL and INI migrations run as the YAML migration of their steps
([Migrations: SQL and settings](migrations-sql-and-settings.md)). Migrations of every type can require each other
and pass values on to each other (references) in the same run.

## YAML migrations

A YAML migration is a list of steps; each names its step type, and the engine runs them in order, with placeholders,
conditions and loops between them:

```yaml
# description: The languages of the news, checked before the import
# author: Site team
steps:
    - type: reference
      set:
          news_languages: [ eng-US, ger-DE ]
          news_parent: "${idmap.node.news}"
    - foreach: "${ref.news_languages}"
      as: language
      steps:
          - type: assert
            that: { value: "${loop.language}", matches: "/^[a-z]{3}-[A-Z]{2}$/" }
            message: "${loop.language} is not a language code"
    - type: assert
      if: { environment: production }
      that: { value: "${ref.news_parent}", not_equals: 2 }
      message: production names its own news node in its id map
```

- The header is the same `#` comment lines as in every migration. `exp:migration:generate --skeleton <name>
  --type=yml` writes an empty one.
- `type` names a step type of `migration.ini [StepSettings] StepTypeList`. The kernel has `reference` (set
  references), `assert` (fail the migration when a condition is false), `content`, `location` and `content_version`
  (objects, their translations, locations and versions), `content_class` and `content_class_group` (classes with
  their attributes), `user`, `user_group` and `role` (accounts, groups, roles with policies, limitations and
  assignments), `section`, `language`, `object_state_group`, `tag` (eztags), `url_alias` (aliases and
  wildcards), `trash`, `file` and `image` (files of the migrations folder), `cache`, `search` and `autoload`,
  `layout`, `layout_block` and `layout_rule` (Exponential Layouts), `http`, `mail` and `command` (allowlisted console
  commands) ([YAML migration steps](migrations-yaml-steps.md)); an extension adds its own the same way. A step's other
  keys are checked against its type's schema before anything runs.
- `${ref.<name>}` is a reference (set by `references:` of an earlier step, by a `reference` step, or by an earlier
  PHP or YAML migration of the run), `${loop.<name>}` a loop variable, `${idmap.<kind>.<name>}` an id of the id map
  `migrations/idmap/<environment>.json`, `${run.environment}` the run's environment.
- `if:` skips a step, `environments:` and `engines:` limit it; `foreach:` and `while:` (with a maximum) repeat steps.
- `down:` lists the steps that undo the migration for `exp:migration:rollback <id>`.
- YAML is read with symfony/yaml, which composer.json requires; an installation that keeps it elsewhere names the
  folder in `[YamlSettings] LibraryPath`.

[The file format](../specifications/6.0/migration-file-format.md#the-yaml-migration) gives every rule, and
[the tutorial](migrations-tutorial.md#12-a-yaml-migration) runs one.

## Order

Migrations run in the order of their ids, which is the timestamp first and then the name. A migration that names
others in its header line `requires:` runs after them, wherever their timestamps put them. A requirement
`extension:<name>` needs that extension to be active. An id found twice (the first found counts: site before
extensions before packages), a requirement that names no migration, and a cycle are errors that `exp:migration:check`
and every run report; nothing runs while one is there.

## The header

The comment lines at the top of the file are its header, one `key: value` per line:

```php
<?php
// description: The folder News below the content root
// requires: 20261031120000_news_note_table
// environments: staging, production
// maintenance: no
// author: Site team
```

Keys: `id`, `description`, `requires`, `environments`, `engines`, `maintenance`, `author`. An unknown key is an
error, so a misspelled one does not pass silently. [The file format](../specifications/6.0/migration-file-format.md)
gives every rule.

## Environments

An installation's environment is `site.ini [AdminEnvironment] Level` (with `Level=auto`, the environment variable
`EXP_ENV`). A migration whose header names `environments: staging, production` runs only there;
`environments: !development` runs everywhere but on development. `--environment=<name>` takes another one for one
command. A migration that is not for the environment, or not for the database engine (`engines:`), has the state
`not-needed`.

## The record

| Where | What |
|---|---|
| table `exp_migration` | one row per migration (source + id): state, run, checksum of the file, duration, the caption before it, the commit after it; also the record of `exp:upgrade` (source `kernel`) |
| table `exp_migration_run` | one row per run: state, user, environment, the captions before and after, commit, tag, push state |
| `migrations/status/<environment>.json` | the record mirrored into the migrations repository (no users, no messages) |
| `migrations/runs/<run-id>.log` | one line per event of a run, cleaned of secrets and e-mail addresses |

The upgrade step `6.0.15-migration-tables` creates both tables; on an installation of a pre-release of 6.0.15, the step
`6.0.15-upgrade-record-table` then copies the rows of its older table `expupgrade_step` and drops that table;
`./console exp:upgrade --apply` runs both. Until then nothing is read or written and the status says so.

States: `done`, `pending`, `not-needed`, `failed`, `unknown`. A migration recorded as done whose file has changed
since is refused, and the run with it, unless the run is given `--force --reason=<text>`: the new checksum is then
recorded, the change audited, and the migration is not run again.

## Transactions and engines

| Database | A failed migration is undone | Simulation (`--simulate`) |
|---|---|---|
| PostgreSQL, SQLite | completely (`transactions: full`) | yes |
| MySQL/MariaDB, Oracle | its data only; a schema change is committed at once (`transactions: data`) | no: the plan is listed with a warning |
| MongoDB | not at all (`transactions: none`) | no |

The caption restored after a failure covers what a transaction cannot.

## The run lock and the user

A run that writes holds the installation lock `var/<site>/upgrade.lock` (shared with `exp:upgrade`), with a holder
file next to it that says who holds it. A second run stops at once; `exp:migration:status` names the holder. A dry
run takes no lock. A run runs as the signed-in user, or, on the console, as `[RunSettings] RunAsUser` (`admin`), by
login; `--login=<login>` takes another.

## Defaults and every switch

| Default | Switched off for one run | Switched off in `migration.ini` |
|---|---|---|
| caption before the run | `--no-caption` | `[CaptionSettings] Before=disabled` |
| caption after a run that passed | `--no-caption` | `After=disabled` |
| reuse an unchanged caption | | `ReuseUnchanged=disabled` |
| restore the pre caption after a failure | `--no-restore` | `RestoreOnFailure=disabled` |
| refuse uncommitted changes in the code trees | `--include-uncommitted` (kept in the caption) | `CodeTreeList[]` |
| commit the status file and run log | `--no-commit` (no tag and no push either) | `[RepositorySettings] Commit=disabled` |
| tag the commit | `--no-tag` | `Tag=disabled` |
| push commit and tag | `--no-push` | `Push=disabled` |
| stop at the first failure | | `[RunSettings] StopOnFailure=disabled` |
| every pending migration in one run | `--max-migrations=<n>`, `--max-time=<seconds>` (the rest postponed) | `MaxMigrations`, `MaxTime` |
| no pause, the normal priority | `--throttle=<seconds>`, `--nice=<n>` | `Throttle`, `Nice` |
| a package's migrations run when it is installed | | `[InstallSettings] RunOnPackageInstall=disabled` |
| a deploy runs no migration | `exp:velocity deploy --migrate` | `velocity.ini [DeploySettings] Migrate=enabled` |

## Every command

| Command | What it does |
|---|---|
| [`exp:migration:status`](../examples/commands/expmigrationstatus.md) | every migration with its state, the orphans, the latest runs, the pushes that wait, the lock |
| [`exp:migration:migrate`](../examples/commands/expmigrationmigrate.md) | the dry run, and the run with `--apply` |
| [`exp:migration:dry-run`](../examples/commands/expmigrationdryrun.md) | the plan; `--simulate --diff` runs in a transaction that is rolled back |
| [`exp:migration:rollback`](../examples/commands/expmigrationrollback.md) | a run, one migration, or (expert) any caption |
| [`exp:migration:resume`](../examples/commands/expmigrationresume.md) | a new run of what a failed or interrupted run left pending |
| [`exp:migration:caption`](../examples/commands/expmigrationcaption.md) | a caption now, or the list |
| [`exp:migration:push`](../examples/commands/expmigrationpush.md) | the runs whose push failed, pushed again |
| [`exp:migration:check`](../examples/commands/expmigrationcheck.md) | the lint for CI |
| [`exp:migration:generate`](../examples/commands/expmigrationgenerate.md) | a new PHP or (`--type=yml`) YAML migration from the skeleton |

All of them take `--format=json` (the machine output `exp-migration/1`) and end with the exit codes of
`exp:upgrade`: 0 fine, 1 failed (or pending with `exp:migration:status --fail-on-pending`), 2 usage error, 3 the
database is not reachable, 4 internal error.

## In the administration

**Setup > Migrations** (the module `migration`) shows and runs the same migrations:

| Page | What it does | Policy |
|---|---|---|
| Status (`/migration/status`) | every migration with its state, the lock, the latest runs, as `exp:migration:status` | `migration/read` |
| History (`/migration/history`) | every run with its captions, commit, tag and push; resume and roll back | `migration/read` |
| A run's page (`/migration/log/<run-id>`) | its record, its migrations, the end of its run log, and its progress while it goes on | `migration/read` |
| Dry run (`/migration/dryrun`) | the plan, as `exp:migration:dry-run`; **Simulate** as `--simulate --diff` | `migration/read`, `migration/run` to simulate |
| Run (`/migration/run`) | the pending migrations, chosen one by one, with the switches of the table above and `--force --reason` | `migration/run` |
| Rollback (`/migration/rollback`) | a run, one migration, or (expert) any caption | `migration/rollback` |

Only the Administrator role has these policies in a new installation. A run, a resume, a simulation and a rollback
ask "Verify it's you" first (`site.ini [ReauthGroup_system]`). The dashboard has a **Migrations** block for users
with `migration/read`, and the website sidebar (expwt) a read-only Migrations panel.

A run, a resume or a rollback started there runs in the background: the page starts the console command
(`exp:migration:migrate --apply`, `exp:migration:resume --apply` or `exp:migration:rollback --apply`) as a process of
its own, as the signed-in user, and opens the run's page, which follows it. The run goes on when the page is closed.
The page names the run with `--run-id=<run-id>`, so the run, its record and its log have that id, and follows it
through the file of `--progress=<file>`, to which the run appends one JSON line per event. Both options can be used
on the console too. The files of these runs are in `var/<site>/migration/jobs/`; `migration.ini [AdminSettings]`
names the PHP command line binary (`PhpCliPath`), how many runs keep their files (`JobHistory`, 50) and how much of
a run log a page shows (`LogBytes`, 256 KB). One run at a time: while one is going on, or another run or an upgrade
holds the lock, the page refuses a new one.

The pages: [Migrations in the administration](../help/migrations/in-the-administration.md),
[The status and the history](../help/migrations/status-and-history.md),
[Run, dry run and rollback](../help/migrations/run-dry-run-and-rollback.md).

## Not in 6.0.15 yet

The editor and the
generators in the administration (generators other than the skeleton), parallel workers, the steps that change code
and design files, and a site-wide recording window are planned and not part of this release.

## Related pages

- [Migrations: the hub](../help/migrations/README.md), [All recipes](../help/migrations/recipes/README.md),
  [Frequently asked questions](../help/migrations/faq.md)
- In the administration, step by step with screenshots: [A tour of Setup > Migrations](../help/migrations/a-tour-of-setup-migrations.md),
  [The migration editor](../help/migrations/the-migration-editor.md), [Recording changes](../help/migrations/recording.md),
  [Add a remote step by step](../help/migrations/add-a-remote-step-by-step.md), [Deploy step by step](../help/migrations/deploy-step-by-step.md)
- [The tutorial](migrations-tutorial.md), [PHP migrations](migrations-php.md),
  [Packages, deploys and long runs](migrations-packages-deploy-and-long-runs.md),
  [Deploying to remote installations](migrations-remote-deploy.md),
  [Engines and performance](migrations-engines-and-performance.md),
  [Rollback and captions](migrations-rollback-and-captions.md),
  [The migrations repository](migrations-repositories.md)
- [In the administration](../help/migrations/in-the-administration.md), [Commands](../help/migrations/commands.md),
  [Settings](../help/migrations/settings.md),
  [Policies, views and events](../help/migrations/policies-views-and-events.md)
- [The file format](../specifications/6.0/migration-file-format.md), [The feature](../features/6.0/migrations.md)
- [When a migration fails](../help/migrations/when-a-migration-fails.md),
  [Troubleshooting messages](migrations-troubleshooting.md)
- [Migrations versus migrating a site](../help/migrations/migrations-versus-migrating-a-site.md)
- [migration.ini](../examples/ini-settings/migration/README.md), [the cronjob part migrationpush](../examples/cronjobs/migrationpush.md)
