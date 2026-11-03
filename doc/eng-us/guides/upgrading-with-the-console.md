# Upgrading with the console: exp:upgrade

This guide teaches `./console exp:upgrade`: the one command that brings the database and the settings of an
Exponential 6.0 installation up to the code it runs, shows first what it would change, makes a backup, applies the
steps in order, records what it did and checks that the site still answers. It replaces the manual list of SQL
files, update scripts and setup commands of a release's "Upgrading" section; that list stays in the changelog as a
reference, and every item of it names the step that does it.

It is for administrators who upgrade an installation, and for anyone who runs upgrades from a deployment pipeline.
Everything below was run on SQLite, MariaDB and PostgreSQL databases on 29 October 2026.

[Guides](README.md) · Related: [Upgrading](upgrading.md), [The upgrade check](upgrade-check.md),
[Upgrade steps for extensions](upgrade-steps-for-extensions.md), [Migrations](migrations.md),
[Migrations: the hub](../help/migrations/README.md), [Deploying](deploying.md),
[Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

## In short

```bash
./console exp:upgrade:status            # what is done, what is pending, nothing is changed
./console exp:upgrade                   # dry run: what every pending step would change
./console exp:upgrade --apply           # backup, then every pending step, then deploy and check
```

Run the commands from the installation root as the user the web server runs as (as root, add
`--allow-root-user`). Without `--apply` nothing is ever written.

## The stages

An upgrade is made of five stages that always run in this order. Each one is a command of its own, and
`exp:upgrade` runs them all in one call.

| # | Stage | Command | What it does |
|---|---|---|---|
| 1 | code | `exp:upgrade:code` | the installation's git checkout (and each `--repos` checkout) moved to `--to` or to its upstream, fast-forward only, through git_manager |
| 2 | libraries | `exp:upgrade:libraries` | `vendor/` made to hold what `composer.lock` names (`composer install`, or `--update` of named packages first), with a security audit |
| 3 | database | `exp:upgrade:database` | the tables, columns, indexes and rows the release needs, on every engine |
| 4 | scripts | `exp:upgrade:scripts` | the PHP upgrade steps: the update scripts and the setup commands, through their classes |
| 5 | finish | `exp:upgrade:finish` | `exp:velocity deploy --kernel` (autoloads, caches, PHP-FPM, Velocity), then a request to the front page |

A later stage refuses to write while an earlier one has pending steps (`--force-order` runs it anyway): policies
are not added to a database that lacks the tables they need. The finish stage has work only when the run changed
something, or when you run `exp:upgrade:finish` on its own.

## Every step and its state

Each stage is made of steps. `./console exp:upgrade:steps` lists them in the order they run; every step has an id
that never changes, such as `6.0.15-user-audit-table`.

A step always looks at the installation itself, never at a record alone: a table it creates is looked up in the
database's catalog, a policy in the role. What you did by hand with the SQL update file or an old script counts.

| State | Means |
|---|---|
| `done` | in place, and `exp:upgrade` applied it (the record says when and in how long) |
| `not-needed` | in place without `exp:upgrade` (a new installation, or done by hand), or not for this installation (another engine, a feature that is not installed) |
| `pending` | missing: the step has work to do |
| `failed` | missing, and the last attempt failed; the message is in the record |
| `unknown` | it cannot be told; the reason is shown, and a run skips the step and goes on |

```text
$ ./console exp:upgrade:status
Exponential upgrade: status
Code 6.0.15stable, database sqlite, version 6.0.15stable

Stage database     pending (1 pending)
  pending     6.0.15-migration-tables              missing: table exp_migration, table exp_migration_run
  not-needed  6.0.15-upgrade-record-table          in place: no table expupgrade_step
  not-needed  6.0.15-user-password-hash-width      not for sqlite
  not-needed  6.0.15-trash-columns                 in place: ezcontentobject_trash.trashed, ...
  ...
Stage scripts      not-needed
  not-needed  6.0.15-editor-policies               in place
  ...
Summary: 1 pending, 0 done, 29 not needed, 0 failed, 0 unknown
PASS
```

This is what an installation that was upgraded by hand shows: everything is `not-needed` except the record tables,
which are new with `exp:upgrade` itself.

## The dry run

`./console exp:upgrade` (or a stage's own command) without `--apply` lists for every pending step what it would
change: the SQL it would run on this engine, the policies it would add, the rows it would create. It writes nothing
and takes no lock. `--dry-run` is accepted as the same thing; `--apply` together with `--dry-run` is a usage error
(exit 2).

```text
Stage database     pending (1 pending)
  pending     6.0.15-migration-tables              missing: table exp_migration, table exp_migration_run
              would create table exp_migration, exp_migration_run: CREATE TABLE exp_migration ( ... PRIMARY KEY ( source, migration_id ) ); ...
```

## Applying

```bash
./console exp:upgrade --apply
```

1. The lock `var/<site>/upgrade.lock` is taken: a second run at the same time stops with exit 1. It is the same lock
   a [migration](migrations.md) run takes, so an upgrade and a migration run never write at the same time.
2. Before the first write a caption is taken (below), the point to roll back to; if it fails, nothing is changed.
3. The pending steps run in order. After each one the step looks at the installation again; only when its effect
   is there now is it recorded as `done`.
4. The first step that fails stops the run (exit 1). The steps after it are listed as not run. Fix the cause and
   run the command again: what is done is not done twice, and a half-applied step is completed.
5. The finish stage deploys the changes and requests the front page of `[SiteSettings] SiteURL` (or every `--url`).
6. The installation is verified (below). When the run or the verification fails, the command ends with exit 1 and
   lists the captions, newest first, each with the command that rolls back to it; with `--rollback-on-fail` it
   rolls back to the caption of step 2 at once. When it passes, `--post-caption` takes a caption of the verified
   state, the "last known good" for the next upgrade.

Choose what runs with `--stages=database,scripts`, `--only=<step id>` and `--skip=<step id>` (comma lists or
repeated). `--no-deploy` finishes with the extension autoloads and the INI, template and content caches only, when
you deploy yourself later.

When the code stage moved the code, the stages after it run in a new PHP process with the same options: the process
that started has the old classes and the old list of steps loaded, the new one reads the new code. The report of
both is one report, and it says so.

## The code stage

```bash
./console exp:upgrade:code --to=v6.0.16                 # dry run: fetches, lists the commits to come
./console exp:upgrade --apply --to=v6.0.16              # the whole upgrade to that release
./console exp:upgrade:code --apply --repos=extension/myext
```

The installation's git checkout moves to `--to` (a tag, a branch or a commit) or, without it, to the upstream of its
branch, through git_manager (`extension/git_manager`). It moves fast-forward only, and the submodules the
installation records follow it. `--repos` names more checkouts inside the installation, each moved to its own
upstream or `--to`. A checkout is refused when:

- it has local changes in tracked files;
- it has commits the target lacks (not a fast-forward);
- the process is not its owner (run the upgrade as the user that owns the files, `sudo -u <owner>`).

`exp:upgrade:status` reads the remote-tracking refs as the last fetch left them. A dry run and a run fetch first, the
refs only, never the working tree, and only as the owner. The caption taken before the first write holds the
commits every checkout was on (`code.json`). Without git_manager, or when the installation is not a git
checkout, the stage does not apply: update the code as [Upgrading](upgrading.md), part C, says.

## The libraries stage

```bash
./console exp:upgrade:libraries                         # dry run: the packages that differ, the audit
./console exp:upgrade:libraries --apply
./console exp:upgrade:libraries --apply --update=guzzlehttp/guzzle
```

`6.0.15-libraries-install` compares `composer.lock` with what `vendor/composer/installed.json` says is installed
and runs `composer install`, with the dev packages only when `vendor/` had them. `6.0.15-libraries-update` runs
`composer update <packages> --with-dependencies` first, only for the packages named with `--update`; commit the new
`composer.lock` afterwards. `composer audit` runs before and after the install and reports security advisories and
abandoned packages; it never stops the run (`--no-audit` skips it). `--composer=` names the Composer binary or
`composer.phar`.

The install is refused, and nothing is installed, when:

- Composer reports local changes in `vendor/` (`composer status`);
- a package it would change is a git checkout of its own (an extension updated by git, not by Composer: move it
  with the code stage and `--repos=` instead);
- the process is not the owner of the installation.

`--allow-vendor-changes` installs over the first two.

## Captions

A caption is a safe point in time: git_manager's full backup (`extension/git_manager`, the database dump and the
`var/` storage) in git_manager's backup folder, with what `exp:upgrade` adds in its `upgrade/` folder:

| In the caption | What it is |
|---|---|
| `sql_<caption>.tar.gz`, `dump.json` | the database: MySQL and MariaDB `mysqldump`, PostgreSQL `pg_dump --clean`, SQLite a consistent copy (`VACUUM INTO`), MongoDB `mongodump`; Oracle has none (export with `expdp`) |
| `var_<caption>.tar.gz` | the storage under `var/` (without git_manager's `VarExcludeDirs`) |
| `upgrade/settings/override` | the settings overrides |
| `upgrade/libraries/` | `composer.json`, `composer.lock`, `vendor/composer/installed.json` |
| `upgrade/code.json` | the commit of the installation's checkout and of every `--repos` checkout |
| `upgrade/caption.json` | the label, the kind (`pre`, `post`, `safety`, `manual`), the versions (database, code, extensions), the engine, the tables |
| `upgrade/checksums.json` | the SHA-256 of every file of the caption |

`exp:upgrade --apply` takes a `pre` caption, labelled `upgrade <from>→<to> <time> pre`, before it writes anything, and
records it in the report and in the record (`exp_migration`, or `expupgrade_step` before the step
`6.0.15-migration-tables`). Without git_manager, or with a git_manager that cannot dump the
engine (an older one dumps MySQL only), `exp:upgrade` makes the caption itself in `var/backup/upgrade/<time>/` (the
database, no storage) and says why. `--no-caption` (the same as `--no-backup`) writes without one; on an installation
whose settings call it production (`site.ini [AdminEnvironment] Level=production`, or `Level=auto` with `EXP_ENV` set
to `prod`, `production` or `live`) it is refused with exit 2 unless `--force-no-caption` is given too.

```bash
./console exp:upgrade:captions                                  # every caption, newest first, with its rollback command
./console exp:upgrade:captions --verify=2026-10-29_10-15-00     # checksums, and the dump restored into a throwaway database
./console exp:upgrade:captions --prune                          # what the retention settings let go
./console exp:upgrade:captions --prune --apply
```

The list shows git_manager's own captions too. `--verify` checks every checksum and restores the database dump into a
throwaway database (SQLite: a copy and `PRAGMA integrity_check`; MySQL, MariaDB and PostgreSQL: a database
`<name>_capverify_<pid>` made with the installation's account, loaded, counted and dropped; a MySQL dump made with
`--databases` is loaded without its `CREATE DATABASE` and `USE`, so it never reaches the live database). `--prune`
keeps the newest `CaptionKeep` (10), every caption younger than `CaptionKeepDays` (90) and the `pre` and `post`
captions of the last `CaptionKeepUpgrades` (3) upgrades, all in `upgrade.ini [UpgradeSettings]`; it removes captions
of `exp:upgrade` only, git_manager's own keep to its `MaxBackups`.

The password of the connection reaches a dump tool in a file only its owner can read (MySQL, MongoDB) or in its
environment (PostgreSQL), never on a command line, and every file of a caption is readable by its owner only.
`./console exp:upgrade:backup` still makes the database backup of `exp:upgrade` on its own, without a caption.

## The verification after the upgrade

```bash
./console exp:upgrade:verify --after
./console exp:upgrade:verify --after --url=https://www.example.com/,https://www.example.com:8080/ --no-tests
```

At the end of `exp:upgrade --apply` the installation is checked, in a new process so that it is the new code that is
checked (`--no-verify` skips it):

| Check | Passes when |
|---|---|
| steps | no step drifted (recorded as done, effect missing) and none failed |
| urls | every smoke address answers 200 to 399: `upgrade.ini [UpgradeSettings] SmokeURL[]`, else `velocity.ini [DeploySettings] WarmUpURL[]` (the addresses of Apache and of Velocity), else the front page and the sign-in page of `SiteURL`; `--url` overrides |
| tests | the PHPUnit test suite `upgrade-smoke` passes (fast, no database); skipped where PHPUnit is not installed, or with `--no-tests` |
| errorlog | no new fatal line in `var/log/error.log` and the site's own error log since the run started |
| autoload | every registered step class loads, and the autoload arrays are readable |

A check that cannot run says why and does not fail the verification.

## Rolling back

```bash
./console exp:upgrade:rollback                                          # the captions
./console exp:upgrade:rollback 2026-10-29_10-15-00                      # what a rollback to it restores
./console exp:upgrade:rollback 2026-10-29_10-15-00 --apply
./console exp:upgrade:rollback 2026-10-29_10-15-00 --apply --parts=database,settings
./console exp:upgrade:finish --apply                                    # make it live
```

A rollback is never automatic (except with `--rollback-on-fail`) and never runs without `--apply`. With `--apply` it
first takes a `safety` caption of the current state, so the rollback itself can be undone by rolling back to that one
(`--no-caption` skips it, refused on production without `--force-no-caption`). Then it restores the parts the caption
has, in this order, and runs the verification again (`--no-verify` skips it):

| Part | Restores |
|---|---|
| database | SQLite: the copy written back through SQLite's backup API; MySQL and MariaDB: the dump through `mysql`; PostgreSQL: the dump through `psql`. Tables made after the caption are dropped. MongoDB and Oracle: by hand (`mongorestore --drop`, `impdp`) |
| storage | `var/` unpacked from git_manager's archive over the installation's (files added since stay) |
| settings | `settings/override` copied back over the installation's (files added since stay) |
| libraries | `composer.json` and `composer.lock` copied back, then `composer install` (refused over packages kept as git checkouts, as the libraries stage refuses them) |
| code | every checkout of `code.json` moved back to its commit (`git reset --keep`, refused over local changes in the files it would move) |

An encrypted caption (git_manager's GPG option) has to be decrypted with git_manager first.

## Engines

The database steps are written from the kernel schema (`share/db_schema.dba`), the same definitions a new
installation is created from, by each engine's own schema handler: MySQL and MariaDB, PostgreSQL, SQLite, and
Oracle through the ezoracle extension. MongoDB gets the collections; columns and indexes do not apply there. After
the database stage the schema equals that of a new installation (checked on SQLite, MariaDB and PostgreSQL, with
one deliberate extra on MySQL: the full-text index of the audit index).

Two steps are not for every engine: the width of `ezuser.password_hash` (SQLite keeps no length), and the user audit
table (not on MongoDB, where the audit files keep every event).

The unique index on `ezuser.login` is added only when no login belongs to two accounts. While there are duplicates
the step is `unknown` and the run goes on; resolve them with `./console exp:user-login-check`, then run again.

## Verify and the record

`exp:upgrade` records every step it applies: the step, its stage and version, done or failed, when, how long, the
system user that ran it, the engine, the checksum of the step's code and the last message. The record is the record
of the [migrations](migrations.md), `exp_migration` (one row per step, as a migration of the source `kernel`) and
`exp_migration_run`; the first step of the database stage, `6.0.15-migration-tables`, creates both tables. Steps that
run before it (the code and libraries stages of a first run) are not recorded; their state still comes from the
installation itself, and the run says `not recorded` for them.

**The older table `expupgrade_step`.** A pre-release of 6.0.15 kept this record in a table of its own,
`expupgrade_step`. No released version had it and a new installation never gets it. On an installation that still has
it, the step `6.0.15-upgrade-record-table` (right after `6.0.15-migration-tables`) retires it:

1. it copies every row `exp_migration` does not have yet (a row `exp_migration` has already is newer and is kept);
2. it checks that every row of `expupgrade_step` is now in `exp_migration`, and stops with the table kept if one is not;
3. it drops the table (on MongoDB the collection; on Oracle `DROP TABLE ... PURGE`).

The step is pending only while the table exists. The dry run lists each row it would copy, the check and the
`DROP TABLE` statement, and changes nothing; a second run finds nothing to do. `exp:upgrade` never reads
`expupgrade_step`: until the step has run, its rows not yet copied simply do not count as recorded.

```text
$ ./console exp:upgrade:database
  pending     6.0.15-upgrade-record-table          missing: the table expupgrade_step is still there (2 row(s) not in exp_migration yet)
              copy the row 6.0.15-trash-columns of expupgrade_step into exp_migration
              copy the row caption:20261031-120000-pre of expupgrade_step into exp_migration
              check that every row of expupgrade_step is in exp_migration
              drop table expupgrade_step: DROP TABLE expupgrade_step;
```

The SQL update files `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` do the same in plain SQL: they create
`expupgrade_step` empty when it is missing, copy its rows that `exp_migration` does not have and drop it, so they can
run on every installation and again.

The captions of `exp:upgrade` and those of migration runs (kinds `migration-pre`, `migration-post`,
`migration-failure`) are one list: `exp:upgrade:captions` shows both, and either rollback can restore either
([Rollback and captions](migrations-rollback-and-captions.md)).

### The steps among the migrations

Every upgrade step is also a migration of the source `kernel`, with the step id as its migration id. The
[migration commands](migrations.md) and Setup > Migrations therefore show and run the kernel's steps too:

| Where | What it does with the steps |
|---|---|
| `./console exp:migration:status`, Setup > Migrations, the dashboard block | lists every step first, before the site's and the extensions' migrations, with the state `exp:upgrade:status` gives it |
| `./console exp:migration:migrate --apply`, Run in Setup > Migrations | applies the pending steps of the stages `database` and `scripts` first, then the migrations, in one run under one lock and one caption |
| a migration file | may name a step in `requires` (`requires: 6.0.15-migration-tables`); it then runs after it |

A migration run leaves the stages `code`, `libraries` and `finish` to `exp:upgrade`: a pending step of those stages
(or an optional step) is listed as `deferred`, a warning names the command that runs it
(`./console exp:upgrade:code --apply` ...), and a migration that requires it waits. After a migration run that
applied steps, `./console exp:upgrade:finish --apply` makes them live (deploy, caches, smoke check). A step keeps
what makes it a step: its state comes from `detect()`, there is no checksum guard on its class file, no transaction
around it, and it has no `down()` (a caption restores it).

Both commands write the same record, so a step applied by a migration run is `done` in `exp:upgrade:status`, and
the other way round. Each command keeps its own rule for captions: `exp:upgrade` stops when no caption can be taken
and refuses `--no-caption` on production, a migration run warns and goes on; `exp:upgrade` restores only with
`--rollback-on-fail`, a migration run restores by default.

Which to write for a change of your own, an upgrade step or a migration:
[Upgrade step or migration?](../help/migrations/recipes/upgrade-step-or-migration.md). Everything about migrations,
with recipes and help for each audience: [Migrations](../help/migrations/README.md). Not to be confused with moving
a site: [Migrations versus migrating a site](../help/migrations/migrations-versus-migrating-a-site.md).

`./console exp:upgrade:verify` looks at every step again and reports drift: a step the record calls done whose
effect is missing now (a table dropped, a policy removed by hand). It exits 1 when it finds any. Run it after a
restore, or from monitoring.

Every applied step is also written to `var/log/upgrade.log`, and to the audit log: `system.upgrade.step` for each
step applied or failed, `system.upgrade.backup` for the backup, `system.upgrade.run` at the end of a run with
`--apply`, `system.upgrade.rollback` for a rollback, `system.upgrade.caption` for a caption taken, checked or pruned, `system.upgrade.verify` for the verification ([the audit](../bc/6.0/audit.md)).

## In a pipeline

```bash
./console exp:upgrade:status --fail-on-pending        # exit 1 when something is pending
./console exp:upgrade --apply --format=json > upgrade.json
```

`--format=json` writes the machine output `exp-upgrade/1`: the installation (engine, database version, code
version), the backup, every stage with every step (`id`, `stage`, `version`, `state`, `detail`, `action`, `plan`,
`messages`, `duration_ms`, `record`, `drift`), a summary and the exit code.

| Exit code | Means |
|---|---|
| 0 | nothing pending, or everything applied |
| 1 | a step failed, the verification after the upgrade failed, the lock or the caption was refused, a stage was out of order, or steps are pending with `--fail-on-pending`; `exp:upgrade:verify`: drift or a failed check; `exp:upgrade:captions --verify`: a changed file or a dump that does not restore |
| 2 | usage error (an unknown option value, stage, step, caption or part; `--apply` with `--dry-run`; `--no-caption` or `--no-backup` on production without `--force-no-caption`) |
| 3 | the database is not reachable |
| 4 | internal error |

## The steps of 6.0.15

| Step | Stage | Replaces |
|---|---|---|
| `6.0.15-code-update` | code | `git pull` by hand, through git_manager, fast-forward only |
| `6.0.15-libraries-update` | libraries | `composer update <package>` by hand (only with `--update`) |
| `6.0.15-libraries-install` | libraries | `composer install` by hand, with `composer audit` |
| `6.0.15-migration-tables` | database | (new) the record of migrations and upgrades, `exp_migration` and `exp_migration_run` |
| `6.0.15-upgrade-record-table` | database | (new) only on an installation of a pre-release of 6.0.15: the rows of its table `expupgrade_step` copied into `exp_migration`, then the table dropped |
| `6.0.15-sqlite-composite-keys` | database | (new) SQLite only: a table that `share/db_schema.dba` keys on more than one column (`ezcontentobject_attribute` on `id` and `version` ...) but the database keys on `id` alone (an installation made from an older `kernel/sql/sqlite/schema.sql`) is rebuilt with the key of the .dba, rows and indexes kept |
| `6.0.15-user-password-hash-width` | database | `ezuser.password_hash` widened to 255 in the SQL update file |
| `6.0.15-trash-columns` | database | `ezcontentobject_trash.trashed`, `trashed_by`, `trashed_via` and their index |
| `6.0.15-pdf-export-footer` | database | `ezpdf_export.show_footer`, `footer_text` |
| `6.0.15-rss-opml-podcast` | database | `ezrss_export.opml_head`, `podcast_head`, table `ezrss_export_opml_item` |
| `6.0.15-order-statuses` | database | the order statuses 4 to 17 |
| `6.0.15-audit-index-tables` | database | `update/common/scripts/6.0/createaudittables.php` (tables) |
| `6.0.15-bookmark-folders` | database | bookmark folders: columns, index, table `expbookmark_folder` |
| `6.0.15-bookmark-view-table` | database | `createbookmarkviewtable.php` |
| `6.0.15-bookmark-view-columns` | database | `expbookmark_view.module_name`, `view_name`, `view_parameters` and their index (`createbookmarkviewtable.php`) |
| `6.0.15-mail-preference-tables` | database | the five `expmail_*` tables |
| `6.0.15-api-key-table` | database | `createapikeytable.php` |
| `6.0.15-transfer-run-table` | database | `createtransferruntable.php` |
| `6.0.15-data-table-tables` | database | `exp_data_connection`, `exp_secret`, `exp_data_sync*` |
| `6.0.15-user-session-table` | database | `createusersessiontable.php` |
| `6.0.15-oauth-pkce-columns` | database | `ezprest_authcode.code_challenge`, `code_challenge_method` |
| `6.0.15-passkey-table` | database | `createpasskeytable.php` |
| `6.0.15-help-content-tables` | database | `exphelp_help_id`, `exphelp_feedback` |
| `6.0.15-order-siteaccess-column` | database | `ezorder.siteaccess` |
| `6.0.15-user-audit-table` | database | `createuseraudittable.php` |
| `6.0.15-user-login-unique-index` | database | `./console exp:user-login-check --fix-index` |
| `6.0.15-contentobject-modified-index` | database | the index `ezcontentobject_modified` (Dashboard > Recent content) |
| `6.0.15-static-publish-tables` | database | `exp_static_run`, `exp_static_page` (static publishing) |
| `6.0.15-migration-deploy-tables` | database | `exp_migration_deploy`, `exp_migration_deploy_host` (deploys of migrations to remotes) |
| `6.0.15-active-extension-tables` | database | (new) the tables of every active extension that ships a `share/db_schema.dba` (ezstarrating, ezgmaplocation, eztags ...), where missing; no data loaded |
| `6.0.15-site-version` | database | `ezsite_data` `ezpublish-version` set to 6.0.15 |
| `6.0.15-missing-user-settings` | scripts | `createmissingusersettings.php` |
| `6.0.15-trash-records-to-columns` | scripts | `movetrashrecords.php` (the file is kept) |
| `6.0.15-anonymous-public-policies` | scripts | `bin/php/addanonymouspublicpolicies.php` |
| `6.0.15-editor-policies` | scripts | `bin/php/addeditorpolicies.php --apply` |
| `6.0.15-member-management` | scripts | `bin/php/addmembermanagement.php --apply` |
| `6.0.15-user-approval` | scripts | `bin/php/adduserapproval.php` |
| `6.0.15-help-read-policy` | scripts | `bin/php/addexphelpreadpolicy.php` |
| `6.0.15-help-class-metadata` | scripts | `./console exp:help:content classes --apply` (the Meta Data attribute through xrowmetadata 1.4.24 or later; title fields of at least 255 characters) |
| `6.0.15-oauth-authorize-policy` | scripts | `bin/php/addoauthauthorizepolicy.php` |
| `6.0.15-order-siteaccess-backfill` | scripts | `bin/php/expbackfillordersiteaccess.php --apply` |
| `6.0.15-bookmark-view-fill` | scripts | the parsed address of older system page bookmarks (`createbookmarkviewtable.php`) |
| `6.0.15-finish-deploy` | finish | `./console exp:velocity deploy --kernel` |
| `6.0.15-finish-smoke` | finish | a request to the front page |

The setup commands run with their default options. When you need another option (for example
`addeditorpolicies --keep-content-wildcard`, or `addanonymouspublicpolicies --skip=user/register` to keep
registration closed), skip the step (`--skip=6.0.15-editor-policies`) and run the command yourself; the step then
reports what is still missing.

Not steps, because they are not about the database or the settings: the crontab (`bin/php/installcrontab.php`), the
help content commands of a site that runs help as content (`exp:help:content install`, `build-manuals`, `ebooks`),
and a check of the demo mode environment variable. The changelog lists them.

## Extensions

An extension adds its own steps with `ExtensionStepList[]` in its `settings/upgrade.ini.append.php`; they run with
the kernel's, by stage and version. [Upgrade steps for extensions](upgrade-steps-for-extensions.md) shows how.

An extension's tables need no step of their own: `6.0.15-active-extension-tables` creates, for every extension
that is active (globally or in a siteaccess) and ships a `share/db_schema.dba`, the tables of that file the database
lacks, on every engine, as a new installation does after its site package. Nothing is dropped or changed and no
`share/db_data.dba` is loaded. See [Extensions of a new installation](../features/6.0/extensions-of-a-new-installation.md#where-the-tables-come-from).

## The promise

The command names (`exp:upgrade` and `exp:upgrade:*`), their options, the exit codes, the step states and the
`exp-upgrade/1` output are kept for good: options and output keys may be added, nothing is renamed or removed. A
test in the kernel compares them against a snapshot.
