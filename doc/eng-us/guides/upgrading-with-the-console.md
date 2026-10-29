# Upgrading with the console: exp:upgrade

This guide teaches `./console exp:upgrade`: the one command that brings the database and the settings of an
Exponential 6.0 installation up to the code it runs, shows first what it would change, makes a backup, applies the
steps in order, records what it did and checks that the site still answers. It replaces the manual list of SQL
files, update scripts and setup commands of a release's "Upgrading" section; that list stays in the changelog as a
reference, and every item of it names the step that does it.

It is for administrators who upgrade an installation, and for anyone who runs upgrades from a deployment pipeline.
Everything below was run on SQLite, MariaDB and PostgreSQL databases on 29 October 2026.

[Guides](README.md) · Related: [Upgrading](upgrading.md), [The upgrade check](upgrade-check.md),
[Upgrade steps for extensions](upgrade-steps-for-extensions.md), [Deploying](deploying.md),
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
  pending     6.0.15-upgrade-record-table          missing: table expupgrade_step
  not-needed  6.0.15-user-password-hash-width      not for sqlite
  not-needed  6.0.15-trash-columns                 in place: ezcontentobject_trash.trashed, ...
  ...
Stage scripts      not-needed
  not-needed  6.0.15-editor-policies               in place
  ...
Summary: 1 pending, 0 done, 28 not needed, 0 failed, 0 unknown
PASS
```

This is what an installation that was upgraded by hand shows: everything is `not-needed` except the record table,
which is new with `exp:upgrade` itself.

## The dry run

`./console exp:upgrade` (or a stage's own command) without `--apply` lists for every pending step what it would
change: the SQL it would run on this engine, the policies it would add, the rows it would create. It writes nothing
and takes no lock. `--dry-run` is accepted as the same thing; `--apply` together with `--dry-run` is a usage error
(exit 2).

```text
Stage database     pending (1 pending)
  pending     6.0.15-upgrade-record-table          missing: table expupgrade_step
              would create table expupgrade_step: CREATE TABLE expupgrade_step ( ... PRIMARY KEY ( step_id ) );
```

## Applying

```bash
./console exp:upgrade --apply
```

1. The lock `var/<site>/upgrade.lock` is taken: a second run at the same time stops with exit 1.
2. Before the first write the backup is made (below); if it fails, nothing is changed.
3. The pending steps run in order. After each one the step looks at the installation again; only when its effect
   is there now is it recorded as `done`.
4. The first step that fails stops the run (exit 1). The steps after it are listed as not run. Fix the cause and
   run the command again: what is done is not done twice, and a half-applied step is completed.
5. The finish stage deploys the changes and requests the front page of `[SiteSettings] SiteURL` (or every `--url`).

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
refs only, never the working tree, and only as the owner. Before the first move, git_manager makes its own backup on
MySQL and MariaDB (on the other engines the upgrade's backup holds the database), and the commits every checkout was
on are written to `code.json` in the upgrade's backup. Without git_manager, or when the installation is not a git
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

## Rolling back

```bash
./console exp:upgrade:rollback                                      # the backups
./console exp:upgrade:rollback 20261029-101500                      # what a rollback to it restores
./console exp:upgrade:rollback 20261029-101500 --apply
./console exp:upgrade:rollback 20261029-101500 --apply --parts=database,settings
./console exp:upgrade:finish --apply                                # make it live
```

A rollback is never automatic and never runs without `--apply`. It restores the parts the backup has, in this order:

| Part | Restores |
|---|---|
| database | SQLite: the copy written back through SQLite's backup API; MySQL and MariaDB: `database.sql` through `mysql`; PostgreSQL: `database.sql` through `psql`. Tables made after the backup are dropped. MongoDB and Oracle: by hand (`mongorestore --drop`, `impdp`) |
| settings | `settings/override` copied back over the installation's (files added since stay) |
| libraries | `composer.json` and `composer.lock` copied back, then `composer install` |
| code | every checkout of `code.json` moved back to its commit (`git reset --keep`, refused over local changes in the files it would move) |

## The backup

`./console exp:upgrade:backup` makes the same backup on its own. It goes into `var/backup/upgrade/<time>/`
(`--backup-dir=` for another place):

| Engine | Backup |
|---|---|
| SQLite | `database.sqlite`, a consistent copy made with `VACUUM INTO` while the site runs |
| MySQL, MariaDB | `database.sql` from `mysqldump --single-transaction` |
| PostgreSQL | `database.sql` from `pg_dump` |
| MongoDB | `mongodb/` from `mongodump` |
| Oracle | none: make one with `expdp` and run with `--no-backup` |

`settings/override`, `composer.json`, `composer.lock` and `vendor/composer/installed.json` are copied next to it, and
`manifest.json` says what is there, with the tables the database had (a rollback drops the ones made later). On an
installation whose settings call it production (`site.ini [AdminEnvironment] Level=production`, or `Level=auto` with
`EXP_ENV` set to `prod`, `production` or `live`), `--no-backup` is refused with exit 2 unless `--force-no-backup` is
given too. The password of the connection
reaches the dump tool in a file only its owner can read (MySQL, MongoDB) or in its environment (PostgreSQL), never
on a command line. The files are readable by their owner only. The backup is the way back: no step can be
undone on its own.

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

`exp:upgrade` records every step it applies in the table `expupgrade_step`: the step, its stage and version, done or
failed, when, how long, the system user that ran it, the engine, the checksum of the step's code and the last
message. The table is created by the first step, `6.0.15-upgrade-record-table`.

`./console exp:upgrade:verify` looks at every step again and reports drift: a step the record calls done whose
effect is missing now (a table dropped, a policy removed by hand). It exits 1 when it finds any. Run it after a
restore, or from monitoring.

Every applied step is also written to `var/log/upgrade.log`, and to the audit log: `system.upgrade.step` for each
step applied or failed, `system.upgrade.backup` for the backup, `system.upgrade.run` at the end of a run with
`--apply`, `system.upgrade.rollback` for a rollback ([the audit](../bc/6.0/audit.md)).

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
| 1 | a step failed, the lock or the backup was refused, a stage was out of order, or steps are pending with `--fail-on-pending`; `exp:upgrade:verify`: drift |
| 2 | usage error (an unknown option value, stage, step, backup or part; `--apply` with `--dry-run`; `--no-backup` on production without `--force-no-backup`) |
| 3 | the database is not reachable |
| 4 | internal error |

## The steps of 6.0.15

| Step | Stage | Replaces |
|---|---|---|
| `6.0.15-code-update` | code | `git pull` by hand, through git_manager, fast-forward only |
| `6.0.15-libraries-update` | libraries | `composer update <package>` by hand (only with `--update`) |
| `6.0.15-libraries-install` | libraries | `composer install` by hand, with `composer audit` |
| `6.0.15-upgrade-record-table` | database | (new) the record table `expupgrade_step` |
| `6.0.15-user-password-hash-width` | database | `ezuser.password_hash` widened to 255 in the SQL update file |
| `6.0.15-trash-columns` | database | `ezcontentobject_trash.trashed`, `trashed_by`, `trashed_via` and their index |
| `6.0.15-pdf-export-footer` | database | `ezpdf_export.show_footer`, `footer_text` |
| `6.0.15-rss-opml-podcast` | database | `ezrss_export.opml_head`, `podcast_head`, table `ezrss_export_opml_item` |
| `6.0.15-order-statuses` | database | the order statuses 4 to 17 |
| `6.0.15-audit-index-tables` | database | `update/common/scripts/6.0/createaudittables.php` (tables) |
| `6.0.15-bookmark-folders` | database | bookmark folders: columns, index, table `expbookmark_folder` |
| `6.0.15-bookmark-view-table` | database | `createbookmarkviewtable.php` |
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
| `6.0.15-site-version` | database | `ezsite_data` `ezpublish-version` set to 6.0.15 |
| `6.0.15-missing-user-settings` | scripts | `createmissingusersettings.php` |
| `6.0.15-trash-records-to-columns` | scripts | `movetrashrecords.php` (the file is kept) |
| `6.0.15-anonymous-public-policies` | scripts | `bin/php/addanonymouspublicpolicies.php` |
| `6.0.15-editor-policies` | scripts | `bin/php/addeditorpolicies.php --apply` |
| `6.0.15-member-management` | scripts | `bin/php/addmembermanagement.php --apply` |
| `6.0.15-user-approval` | scripts | `bin/php/adduserapproval.php` |
| `6.0.15-help-read-policy` | scripts | `bin/php/addexphelpreadpolicy.php` |
| `6.0.15-oauth-authorize-policy` | scripts | `bin/php/addoauthauthorizepolicy.php` |
| `6.0.15-order-siteaccess-backfill` | scripts | `bin/php/expbackfillordersiteaccess.php --apply` |
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

## The promise

The command names (`exp:upgrade` and `exp:upgrade:*`), their options, the exit codes, the step states and the
`exp-upgrade/1` output are kept for good: options and output keys may be added, nothing is renamed or removed. A
test in the kernel compares them against a snapshot.
