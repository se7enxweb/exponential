# 11. Upgrading

This chapter brings an existing installation to the current Exponential 6.0 line: from an earlier 6.0.x, from 5.4 or
5.90, or from the 4.x and older 5.x lines. It explains how to find the version you run, the one rule that keeps an
upgrade safe (back up, then never run the installer over an existing site), the database update files in
`update/database/` for each engine and how to apply them, the data repair scripts in `update/common/scripts/`, the
console commands that check the result, and the release notes to read for each period you pass. It summarises and
extends the [Upgrading guide](../guides/upgrading.md), which remains the step-by-step procedure.

[Contents](README.md) · Previous: [10. After installing](10-after-installing.md) · Next: [12. Troubleshooting](12-troubleshooting.md)

## 11.1 Before you start

**Never run an installer over an existing site.** The setup wizard, the kickstarter and `exp:install` create a new
database; with `DatabaseAction=remove` (the default of `exp:install`) they empty the one they are pointed at. An
upgrade replaces the code and updates the existing database in place. `exp:install` refuses to run where
`settings/override/site.ini.append.php` already names a database ("This directory already holds an installation"),
and the kickstarter refuses the step that writes the database without `--force`; do not add `--force` to get past
either on a live site.

**Back up first, every time.** The database step is the one that cannot be undone by putting files back.

```bash
mysqldump -u USER -p DATABASE > backup-before-upgrade.sql          # MySQL or MariaDB
pg_dump -U USER DATABASE > backup-before-upgrade.sql               # PostgreSQL
sqlite3 var/storage/sqlite3/exponential.db ".backup backup-before-upgrade.db"   # SQLite, online backup
tar czf backup-var-and-settings.tgz var settings/override settings/siteaccess extension design
```

For SQLite use the file name your installation has (`[DatabaseSettings] Database` in
`settings/override/site.ini.append.php`); `.backup` is SQLite's online backup and is safe while the site runs, a plain
`cp` of a busy database file is not. Restore the backup on a scratch machine once: it is the only proof that it
works.

**Take the site offline for the database step.**

```bash
php bin/php/maintenance.php on --message="Upgrade in progress" --until=30m
php bin/php/maintenance.php status
php bin/php/maintenance.php off
```

While maintenance mode is on, every page request is answered with the maintenance page (HTTP 503, never cached);
images, styles and scripts are still served. `--allow-ip=` and `--allow-admin` let you in to check. See
[Maintenance mode](../features/6.0/maintenance-mode.md).

## 11.2 Which version do you run?

The code and the database each carry a version, and after a correct upgrade they agree.

| Where | How | Example |
|---|---|---|
| The code | `php bin/php/console --version` (reads `lib/version.php`) | `console (Exponential) 6.0.15stable` |
| The database | the rows `ezpublish-version` and `ezpublish-release` of table `ezsite_data` | `6.0.14`, `1` |
| The admin | **Setup > System information** | |

```bash
mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
psql -U USER DATABASE -c "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
sqlite3 var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
```

The row names keep their historic spelling on purpose; they are identifiers, and every update file writes to them.

| You run | Path | Section |
|---|---|---|
| 3.10 up to 5.3 | the old chain to 5.4, then to 6.0.0, then to today | [11.4](#114-from-310-to-53-the-old-chain), [11.5](#115-from-54-or-590-to-600), [11.6](#116-from-any-60x-to-today) |
| 5.4.0, or 5.90 (2017.08 and later) | to 6.0.0, then to today | [11.5](#115-from-54-or-590-to-600), [11.6](#116-from-any-60x-to-today) |
| 6.0.0 to 6.0.14, or an earlier checkout of the 6.0.15 line | to today | [11.6](#116-from-any-60x-to-today) |

## 11.3 The update files

`update/database/<engine>/<version>/` holds one SQL file per step. Each file takes a database **from** one version
**to** the next, and the chain must be walked in order, every file once.

### By engine

| Engine | Directory | Steps available |
|---|---|---|
| MySQL or MariaDB | `update/database/mysql/` | 3.10 → 4.0 → 4.1 → ... → 5.4, then `6.0/dbupdate-5.4.0-6.0.0.sql`, then `6.0/dbupdate-6.0.0-6.0.15.sql` |
| PostgreSQL | `update/database/postgresql/` | the same chain; the 5.4 step is `6.0/dbupdate-5.4-to-6.0.sql`, then `6.0/dbupdate-6.0.0-6.0.15.sql` |
| SQLite | `update/database/sqlite/` | only `6.0/dbupdate-6.0.0-6.0.15.sql`: SQLite support starts with 6.0.1 |
| Oracle | `extension/ezoracle/update/database/` (in the `ezoracle` extension): `ezpublish/` for the kernel chain, `ezoracle/` for the driver's own tables | the kernel chain up to 5.3 |
| MongoDB | none | the MongoDB driver arrived in 6.0.14; the audit tables of 6.0.15 are created by `createaudittables.php` (below) |

### The 4.0 to 5.4 chain (MySQL file names; PostgreSQL has the same names)

| From | To | File |
|---|---|---|
| 3.10 | 4.0 | `4.0/dbupdate-3.10.0-to-4.0.0.sql` |
| 4.0 | 4.1 | `4.1/dbupdate-4.0.0-to-4.1.0.sql` |
| 4.1 | 4.2 | `4.2/dbupdate-4.1.0-to-4.2.0.sql` |
| 4.2 | 4.3 | `4.3/dbupdate-4.2.0-to-4.3.0.sql` (+ `dbupdate-cluster-4.2.0-to-4.3.0.sql` on MySQL) |
| 4.3 | 4.4 | `4.4/dbupdate-4.3.0-to-4.4.0.sql` |
| 4.4 | 4.5 | `4.5/dbupdate-4.4.0-to-4.5.0.sql` |
| 4.5 | 4.6 | `4.6/dbupdate-4.5.0-to-4.6.0.sql` |
| 4.6 | 4.7 | `4.7/dbupdate-4.6.0-to-4.7.0.sql` (+ `dbupdate-cluster-4.6.0-to-4.7.0.sql` on MySQL) |
| 4.7 | 5.0 | `5.0/dbupdate-4.7.0-to-5.0.0.sql` |
| 5.0 | 5.1 | `5.1/dbupdate-5.0.0-to-5.1.0.sql` |
| 5.1 | 5.2 | `5.2/dbupdate-5.1.0-to-5.2.0.sql` (+ `dbupdate-cluster-5.1.0-to-5.2.0.sql` on MySQL) |
| 5.2 | 5.3 | `5.3/dbupdate-5.2.0-to-5.3.0.sql` |
| 5.3 | 5.4 | `5.4/dbupdate-5.3.0-to-5.4.0.sql` (+ `dbupdate-cluster-5.3.0-to-5.4.0.sql` on MySQL) |

### Rules

- **In order, each once.** If a statement fails because a column or table already exists, the file was applied
  before: stop and compare with the backup.
- **Cluster files.** If the installation stores files in the database cluster (DFS or the database file handler),
  also apply the `dbupdate-cluster-...` file of the same step (MySQL: 4.3, 4.7, 5.2, 5.4).
- **`unstable/` directories** hold the files of pre-releases (alpha, beta, rc). Do not apply them to a site that ran a
  final release.
- **`6.12/`, `7.2/` and `7.3/`** belong to other product lines and are not on this path. The 6.0 files take a 5.4
  database to today.
- **A file that fails half way.** Restore the backup and apply the file again from the start; do not repair by hand
  and re-run.
- **Check the chain itself.** `php bin/php/console exp:checkdbfiles` verifies the update files against the upgrade
  path and reports missing or stray files.

Apply a file with the client of the database:

```bash
mysql -u USER -p DATABASE < update/database/mysql/4.1/dbupdate-4.0.0-to-4.1.0.sql
psql -U USER -d DATABASE -f update/database/postgresql/4.1/dbupdate-4.0.0-to-4.1.0.sql
sqlite3 var/storage/sqlite3/exponential.db < update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

### Per-database notes

| Engine | Note |
|---|---|
| MySQL or MariaDB | `6.0/dbupdate-5.4.0-6.0.0.sql` (like `6.12/` and the old chain's files) starts with `SET storage_engine=InnoDB;`. That variable was removed in MySQL 5.7.6 and is rejected by newer MySQL and by MariaDB: run the two `UPDATE` lines of the file only. `6.0/dbupdate-6.0.0-6.0.15.sql` has no such line. |
| MySQL or MariaDB | Tables must be UTF-8; `php bin/php/ezconvertdbcharset.php` converts an old database. `php bin/php/ezconvertmysqltabletype.php --list` lists the table types and `--newtype=InnoDB` converts them. |
| PostgreSQL | The 5.4 step is `6.0/dbupdate-5.4-to-6.0.sql` (only the two `UPDATE` lines). The `digest` function of `pgcrypto` must exist in the database. |
| SQLite | Each `ALTER TABLE` adds one column, because SQLite takes only one per statement and cannot drop a column again: run the file once. Use SQLite's online backup before it. |
| Oracle | The `ezoracle` extension carries its own update files up to 5.3; apply them with your Oracle client. The 6.0.15 audit tables come from `createaudittables.php`. |
| MongoDB | No SQL files. `createaudittables.php` creates the audit index collections through the driver's schema handler. |

## 11.4 From 3.10 to 5.3: the old chain

1. Put the new code in place (as in [11.6](#116-from-any-60x-to-today), step 1).
2. Apply the update files of the chain in [11.3](#113-the-update-files), from your version up to 5.4, each once.
3. Run the data repair scripts of every version you pass (below), from the installation root.
4. Read the release notes of each version you pass:
   [4.1](../bc/4.1/changes-4.1.0.txt), [4.2](../bc/4.2/changes-4.2.0.txt), [4.3](../bc/4.3/changes-4.3.0.txt),
   [4.4](../bc/4.4/changes-4.4.0.txt), [4.5](../bc/4.5/changes-4.5.0.txt), [4.6](../bc/4.6/changes-4.6.0.txt),
   [4.7](../bc/4.7/changes-4.7.0.txt), [5.0](../bc/5.0/changes-5.0.txt), [5.1](../bc/5.1/changes-5.1.txt),
   [5.2](../bc/5.2/changes-5.2.txt), [5.3](../bc/5.3/changes-5.3.txt), [5.4](../bc/5.4/changes-5.4.txt).
5. Read the notes of the 5.90 line, which prepared the move to PHP 7 and a longer password rule:
   [5.90 overview](../bc/5.90/README.md), [PHP 7](../bc/5.90/php7.md), [password length](../bc/5.90/password_length.md),
   [relation indexing](../bc/5.90/relation_indexing.md).
6. Continue with [11.5](#115-from-54-or-590-to-600).

### The data repair scripts

`update/common/scripts/<version>/` holds PHP scripts that repair or convert data for a step. Run the ones of the
versions you pass, from the installation root, after that version's SQL file, for example:

```bash
php update/common/scripts/4.1/updateimagesystem.php --help
php update/common/scripts/4.1/updateimagesystem.php
```

Read the first lines of a script, or its `--help`, before you run it. The scripts and what they say they do:

| Version | Script | Purpose |
|---|---|---|
| 4.0 | `updatebinaryfile.php`, `updatetipafriendpolicy.php`, `updatevatcountries.php` | binary file attributes, the tip-a-friend policy, VAT countries |
| 4.1 | `addlockstategroup.php`, `correctxmlalign.php`, `fixclassremoteid.php`, `fixezurlobjectlinks.php`, `fixnoderemoteid.php`, `fixobjectremoteid.php`, `initurlaliasmlid.php`, `updateimagesystem.php` | object states, XML alignment, remote ids, URL links, multi-language URL aliases, the image system |
| 4.2 | `fixorphanimages.php` | orphaned image files |
| 4.3 | `updatenodeassignment.php` | node assignments |
| 4.4, 4.5 | `updatesectionidentifier.php` | section identifiers |
| 4.6 | `removetrashedimages.php`, `updateordernumber.php` | images of trashed content, web shop order numbers |
| 5.0 | `deduplicatecontentstategrouplanguage.php`, `disablesuspicioususers.php`, `restorexmlrelations.php` | duplicate state group translations, disabling accounts whose login contains `<` or `>`, XML relations |
| 5.1 | `fiximagesoutsidevardir.php` | references to images outside `VarDir` |
| 5.2 | `cleanupdfscache.php` | cache records in the DFS storage table |
| 5.3 | `recreateimagesreferences.php`, `updatenodeassignmentparentremoteids.php` | missing image references, `parent_remote_id` of node assignments |
| 5.4 | `cleanuntranslatablerelations.php`, `cleanupfieldvaluerelations.php`, `fixremovedezurlobjectlinks.php`, `fixtrashedimagereferences.php` | stale relations, links and image references |
| 6.0 | `createaudittables.php` | the audit index tables of 6.0.15 (see [11.6](#116-from-any-60x-to-today)) |
| any | `cleanup.php`, `updatecontentobjectname.php`, `updatenbxmlcontents.php` | general clean-up, object names, non-breaking space encoding in XML content |

### Coming from a 5.x site on the Symfony stack

A 5.x site that ran the legacy kernel inside a Symfony project kept it in `ezpublish_legacy/`; the installer plugin
still installs it there when the project's `composer.json` sets `extra.symfony-app-dir`. The database and the legacy
settings, extensions and designs are those of the legacy installation, and the path above applies to them. To keep
running Exponential 6 inside a Symfony platform, see [Legacy bridge](../features/6.0/legacy-bridge.md) and the
[legacy bridge bundle specification](../specifications/6.0/legacy-bridge-bundle.md).

## 11.5 From 5.4 (or 5.90) to 6.0.0

1. **New code.** Either a fresh checkout or `composer create-project` of the new version, into which you copy your
   `settings/override/`, `settings/siteaccess/`, your own `extension/` directories and your own `design/` directories;
   or, when the site is a Composer project, change the constraint of `se7enxweb/exponential` in your `composer.json`
   and run `composer update se7enxweb/exponential`. Never let a tool replace your own settings or extensions.
2. **Record the version.** No schema change is needed from 5.4 to 6.0.0:

   ```bash
   mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
   psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
   ```

   On MySQL 5.7.6 and newer and on MariaDB, run only the two `UPDATE` lines of the MySQL file (see the per-database
   notes above). Details: [Changelog 6.0.0](../changelogs/6.0/6.0.0.md).
3. **PHP.** The current line runs on PHP 8.0 to 8.5 (`composer.json`: `^8.0`); Velocity needs 8.1. Releases 6.0.8 to
   6.0.14 required 8.1. A site that must stay on PHP 7.4 stays on 6.0.7. Check your own extensions for PHP 8
   problems: classes that extend `eZPersistentObject` or `eZDataType` are the usual cases. See
   [PHP 8 support](../bc/6.0/php8.md) and [PHP 8.0 support](../bc/6.0/php-8.0-support.md).
4. Continue with [11.6](#116-from-any-60x-to-today).

> **The guides disagree on the PHP minimum.** Step 3 of the [Upgrading guide](../guides/upgrading.md) and the "How to
> check" part of [PHP 8 support](../bc/6.0/php8.md) say 8.1. That was true for 6.0.8 to 6.0.14. From 6.0.15
> `composer.json` accepts 8.0, which Composer enforces.

## 11.6 From any 6.0.x to today

### Step 1: put the new code in place

As in 11.5 step 1: a checkout of the tag or branch (`git fetch --tags`, `git checkout v6.0.<n>` or `main`) followed
by `composer install`, or `composer update se7enxweb/exponential` in a Composer project. Then regenerate the class
maps and clear the caches, in this order:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Do not run `ezpgenerateautoloads.php -k` on an installation that keeps extra working copies of the code inside its
root (for example Git worktree folders) without excluding them (`--exclude='<folder>'`); otherwise kernel classes are
mapped into those copies.

Then reload the PHP that serves the site: the PHP-FPM service of the site (`systemctl reload <service>`; on Plesk
`plesk-php<XY>-fpm`, not `php-fpm`) and, under Velocity, the server:

```bash
php bin/php/console exp:velocity deploy --dry-run
php bin/php/console exp:velocity deploy
```

`exp:velocity deploy` runs every step in the right order: the extension autoloads, the INI cache, the template,
override, translation and design caches, the engine archive when needed, the PHP-FPM reload
(`[DeploySettings] PhpFpmService`), the Velocity restart, the content and HTTP caches and Velocity's response cache. It
prints PASS, FAIL or SKIP with the time of each and stops at the first failure. See
[Velocity engines](../bc/6.0/velocity-engines.md).

### Step 2: apply the database update

The file for the line is `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, for MySQL, PostgreSQL and SQLite.
It records the version (`6.0.15stable`) and adds the tables and columns of the line: the PDF export footer
(`ezpdf_export.show_footer`, `footer_text`), OPML and podcast exports (`ezrss_export.opml_head`, `podcast_head`,
table `ezrss_export_opml_item`), the audit index (`expaudit_cursor`, `expaudit_event`, `expaudit_file`), bookmark
folders (`expbookmark_folder`, `ezcontentbrowsebookmark.folder_id`, `priority`) and the e-mail preferences
(`expmail_category`, `expmail_consent_log`, `expmail_pending`, `expmail_preference`, `expmail_suppression`).

Apply it **once, whole, in order**. If the database already has a column, the statement fails and names it; skip only
that statement.

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
```

Then create the audit index tables and index the audit files written so far (the audit trail is on by default from
6.0.15):

```bash
php update/common/scripts/6.0/createaudittables.php --dry-run
php update/common/scripts/6.0/createaudittables.php
```

The script works on every engine (MySQL/MariaDB, PostgreSQL, SQLite, Oracle and MongoDB, each through its schema
handler) and leaves existing tables alone, so it can be run again. `--dry-run` shows which tables are missing;
`--no-index` creates the tables only. If it stops with "The audit index classes are not in the autoload array", run
`php bin/php/ezpgenerateautoloads.php -k` first. See [Audit](../bc/6.0/audit.md).

Check the version row afterwards (the query in [11.2](#112-which-version-do-you-run)); expected `6.0.15stable`.

### Step 3: read the checklist of each period you pass

Read every release note between your version and today; each opens with what to check.

| From | Read |
|---|---|
| 6.0.0 | [6.0.1](../changelogs/6.0/6.0.1.md): SQLite, replacing kernel modules from an extension |
| 6.0.1 | [6.0.2](../changelogs/6.0/6.0.2.md): links in the online editor |
| 6.0.2 | [6.0.3](../changelogs/6.0/6.0.3.md): datatype extensions joined the distribution |
| 6.0.3 | [6.0.4](../changelogs/6.0/6.0.4.md): the responsive administration design |
| 6.0.4 | [6.0.5](../changelogs/6.0/6.0.5.md): a failed 6.0.4 install, datatypes of extensions |
| 6.0.5 | [6.0.6](../changelogs/6.0/6.0.6.md): `admin3` on phones and tablets |
| 6.0.6 | [6.0.7](../changelogs/6.0/6.0.7.md): the last release for PHP 7.4 |
| 6.0.7 | [6.0.8](../changelogs/6.0/6.0.8.md): **needs PHP 8.1**; and [PHP 8 support](../bc/6.0/php8.md) |
| 6.0.8 | [6.0.9](../changelogs/6.0/6.0.9.md): PostgreSQL, REST access to content |
| 6.0.9 | [6.0.10](../changelogs/6.0/6.0.10.md): the product name in templates, translations and overrides; [The product is called Exponential](../features/6.0/rebranding-to-exponential.md) |
| 6.0.10 | [6.0.11](../changelogs/6.0/6.0.11.md): PHP 8.5, notification mail, page view counting |
| 6.0.11 | [6.0.12](../changelogs/6.0/6.0.12.md): role-based template operators, several sites in one installation |
| 6.0.12 | [6.0.13](../changelogs/6.0/6.0.13.md): **security fixes**, sort column checks; [Security hardening 6.0.13](../specifications/6.0/security-hardening-6.0.13.md), [Hardening](../bc/6.0/hardening.md) |
| 6.0.13 | [6.0.14](../changelogs/6.0/6.0.14.md): MongoDB, the upgrade file check |
| 6.0.14 | [6.0.15](../changelogs/6.0/6.0.15.md), then in order: [July and August 2026](../bc/6.0/behaviour-changes-2026-07-08.md), [16 to 30 September 2026](../bc/6.0/behaviour-changes-2026-09b.md) with [Security defaults](../specifications/6.0/security-defaults-2026-09.md), [1 and 2 October 2026](../bc/6.0/behaviour-changes-2026-10.md), [e-mail preferences and the mail gate](../bc/6.0/mail-preferences.md) |

The changes people meet most often when they reach 6.0.15:

- **Accounts without an `ezuser_setting` row cannot sign in** (16 August 2026): a user with no settings row used to
  count as enabled and now counts as disabled. Accounts made in the admin always have the row; accounts made by SQL,
  packages or migrations may not. The queries that find them are in the
  [August 2026 security specification](../specifications/6.0/security-hardening-2026-08.md#f-06-find-accounts-that-cannot-sign-in).
- **Overrides follow `Priority`** (12 July 2026): override blocks of `override.ini` are sorted by `Priority`, lowest
  first, and a block without it comes last. Review any `override.ini` where only some blocks have a `Priority`.
- **Security headers, `HttpOnly`/`SameSite` session cookies and a stricter upload check are on by default**
  (September and October 2026). `[Session] CookieSecure=auto` marks the session cookie `Secure` whenever the request
  came over HTTPS. If another host frames your admin, set the header deliberately.
- **Legacy extensions** were brought to one standard between 22 September and 2 October 2026:
  [Behaviour changes of the legacy extensions](../bc/6.0/extensions-behaviour-changes.md).
- **Static cache defaults** changed: [Static cache defaults](../bc/6.0/static-cache-defaults.md).
- **Velocity**, if you run it: [Velocity engine upgrade notes](../bc/6.0/velocity-engine-upgrade-notes.md) and
  [on-disk layout](../bc/6.0/velocity-ondisk-layout.md).

For the story by month, see the [chronicle](../history/2026/); for every single change, the
[ledger of the root repository](../history/ledger/exponential-root.md).

### Step 4: check your own extensions

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:checkclasses
```

`exp:checkclasses` loads every declared class and reports those PHP refuses to load, which is how a PHP 8
incompatibility in an extension shows. Extensions carry their own version in `ezinfo.php` and `extension.xml`, and the
admin's **Setup > Extensions** list shows it ([Extension list](../features/6.0/extension-list-and-downloads.md)). The
order in which extensions load changed in 2026: [Extension loading order](../features/6.0/extension-loading-order.md).
Composer-installed extensions are updated with their package: `composer update se7enxweb/<package>`, after reading
the package's notes in the [extensions' release notes](../changelogs/extensions/README.md).

Some classic extensions have their own upgrade scripts in `bin/php/`:

| Script | Upgrades |
|---|---|
| `php bin/php/ezwebinupgrade.php --to-version=<version>` | an existing `ezwebin` installation to the current package version |
| `php bin/php/ezflowupgrade.php --to-version=<version>` | the installed `ezflow` packages (`--package`, `--url`) |

Run them with `--help` first.

## 11.7 Verify the upgrade

```bash
php bin/php/ezcache.php --clear-all                 # ends without an error
php bin/php/console exp:checkdbfiles                # no file of your line marked as missing
php bin/php/console exp:checkclasses                # no class PHP refuses to load
```

Then in the browser: the front page loads; you can log in to the admin; **Setup > System information** shows the new
version; a test item can be published and appears on the site. **Setup > Upgrade check** with **Check file
consistency** compares the installed files with the file manifest `share/filelist.md5` and lists the ones that differ
([File consistency check](../features/6.0/file-consistency-check.md)). Files you changed on purpose are expected
there.

Other tools that help after an upgrade:

| Command | When |
|---|---|
| `php bin/php/ezsqldiff.php --type=mysql --user=USER <database> <database2>` | compare two database schemas; the exit code tells whether they differ (`--type` is `mysql` or `postgresql`) |
| `php bin/php/updateniceurls.php` | rebuild the URL aliases (`--update-nodes`) |
| `php bin/php/updatesearchindex.php` | reindex all content in the search engine |
| `php bin/php/cleanupversions.php` | remove archived versions beyond the `VersionManagement` limits |

Last, clear the content cache and, under Velocity, its response cache, **after** the PHP-FPM reload or Velocity
restart, so that a page rendered by the old code is not served again:

```bash
php bin/php/ezcache.php --clear-tag=content
php bin/php/console exp:velocity cache clear
```

## 11.8 If something goes wrong

| Symptom | What to do |
|---|---|
| A white page or HTTP 500 after the code change | Read `var/log/error.log`; run step 1 of 11.6 again; `vendor/` must match the new `composer.json`. |
| "The site is missing the software libraries it needs" (HTTP 503) | `vendor/` is missing: `composer install`; see [Repairing an installation](../bc/6.0/repair.md). |
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches. |
| An SQL file failed half way | Restore the backup and apply the file again from the start. |
| `SET storage_engine=InnoDB;` fails | MySQL 5.7.6+ or MariaDB: skip that line, run the `UPDATE` lines. |
| Someone cannot sign in | The `ezuser_setting` change of August 2026 (11.6, step 3). |
| A change does not show under Velocity | `exp:velocity restart` (or `deploy`), then `exp:velocity cache clear`. |

More in [chapter 12](12-troubleshooting.md).

A published release never changes; a fix always comes as the next version. To see what a version holds, open its
changelog: [6.0.0 to 6.0.15](../changelogs/6.0/6.0.15.md).

## References

In this repository:

- [Upgrading](../guides/upgrading.md): the step-by-step guide this chapter extends.
- Release notes: [changelogs of 6.0](../changelogs/6.0/6.0.15.md) ([6.0.0](../changelogs/6.0/6.0.0.md) to
  [6.0.14](../changelogs/6.0/6.0.14.md)), the [extensions' release notes](../changelogs/extensions/README.md),
  [all changelogs](../changelogs/README.md).
- Behaviour changes: [July and August 2026](../bc/6.0/behaviour-changes-2026-07-08.md),
  [16 to 30 September 2026](../bc/6.0/behaviour-changes-2026-09b.md),
  [1 and 2 October 2026](../bc/6.0/behaviour-changes-2026-10.md),
  [legacy extensions](../bc/6.0/extensions-behaviour-changes.md),
  [e-mail preferences](../bc/6.0/mail-preferences.md), [static cache defaults](../bc/6.0/static-cache-defaults.md),
  [Velocity engine upgrade notes](../bc/6.0/velocity-engine-upgrade-notes.md).
- PHP: [PHP 8 support](../bc/6.0/php8.md), [PHP 8.0 support](../bc/6.0/php-8.0-support.md),
  [5.90 and PHP 7](../bc/5.90/php7.md).
- Security: [Hardening of 6.0.13](../bc/6.0/hardening.md),
  [Security hardening 6.0.13](../specifications/6.0/security-hardening-6.0.13.md),
  [Security hardening August 2026](../specifications/6.0/security-hardening-2026-08.md),
  [Security defaults](../specifications/6.0/security-defaults-2026-09.md).
- [Audit](../bc/6.0/audit.md), [Maintenance mode](../features/6.0/maintenance-mode.md),
  [File consistency check](../features/6.0/file-consistency-check.md),
  [Repairing an installation](../bc/6.0/repair.md), [Velocity engines](../bc/6.0/velocity-engines.md).
- [Legacy bridge](../features/6.0/legacy-bridge.md).
- [Operating a site](../guides/operating-a-site.md): backups, caches and repairs.
- [History](../history/README.md): the [2026 chronicle](../history/2026/) and the
  [ledger](../history/ledger/exponential-root.md).
- Code: `update/database/`, `update/common/scripts/`, `bin/php/checkdbfiles.php`, `bin/php/checkclasses.php`,
  `bin/php/ezsqldiff.php`, `lib/version.php`.

External:

- PHP migration guides: [8.0](https://www.php.net/manual/en/migration80.php),
  [8.1](https://www.php.net/manual/en/migration81.php), [8.2](https://www.php.net/manual/en/migration82.php),
  [8.3](https://www.php.net/manual/en/migration83.php), [8.4](https://www.php.net/manual/en/migration84.php),
  [8.5](https://www.php.net/manual/en/migration85.php).
- Database backups: [mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html),
  [pg_dump](https://www.postgresql.org/docs/current/app-pgdump.html),
  [SQLite backup command](https://sqlite.org/cli.html),
  [PostgreSQL pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html).
- Composer: [update](https://getcomposer.org/doc/03-cli.md#update-u-upgrade).
- GitHub: [se7enxweb/exponential releases](https://github.com/se7enxweb/exponential/releases).

[Contents](README.md) · Previous: [10. After installing](10-after-installing.md) · Next: [12. Troubleshooting](12-troubleshooting.md)
