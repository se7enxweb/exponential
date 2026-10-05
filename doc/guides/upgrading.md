# Upgrading: from 4.x, 5.x or an earlier 6.0.x to today

This guide is for whoever runs an existing installation and wants to bring it to the current Exponential 6.0 line.
That line is 6.0.15, still in development; the newest release tag at the time of writing is `v6.0.14` (see
[Changelog 6.0.15](../changelogs/6.0/6.0.15.md)). Find your version, follow the part for it, and finish with the
checks at the end. They tell you that the result is good.

Plan for twenty minutes for a small site. The risky step is the database. That is why step 1 is a backup.

## Which part do I read?

Find your version: in the admin the **Setup** > **System information** page names it; on the command line:

```bash
mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
```

(Use the equivalent for PostgreSQL or SQLite; the row names `ezpublish-version` and `ezpublish-release` keep their
historic spelling on purpose.) Expected: a value such as `5.4.0` or `6.0.14`.

| You run | Read |
|---|---|
| 3.10 up to 5.3 | Part A, then B |
| 5.4.0 or 5.90 (2017.08 and later) | Part B |
| 6.0.0 up to 6.0.14, or an earlier checkout of the 6.0.15 line | Part C |

## Step 1: back up, always

```bash
mysqldump -u USER -p DATABASE > backup-before-upgrade.sql      # MySQL or MariaDB
pg_dump -U USER DATABASE > backup-before-upgrade.sql           # PostgreSQL
sqlite3 var/storage/sqlite3/exponential.db ".backup 'backup-before-upgrade.db'"  # SQLite: use the path of your database file
tar czf backup-var-and-settings.tgz var settings/override settings/siteaccess extension design
```

Keep `settings/override/` and your own extensions and designs out of any command that replaces files. The one
check that proves the backup is usable: restore it on a scratch machine, never on the live database.

## Part A: from 3.10 up to 5.3 (the old chain)

The database update files form a chain; each takes you one step. Apply them **in order, each once**, for your engine
(`mysql`, `postgresql`, `sqlite` where it exists), from the new code's `update/database/<engine>/` directory:

| From | To | MySQL file |
|---|---|---|
| 3.10 | 4.0 | `4.0/dbupdate-3.10.0-to-4.0.0.sql` |
| 4.0 | 4.1 | `4.1/dbupdate-4.0.0-to-4.1.0.sql` |
| 4.1 | 4.2 | `4.2/dbupdate-4.1.0-to-4.2.0.sql` |
| 4.2 | 4.3 | `4.3/dbupdate-4.2.0-to-4.3.0.sql` |
| 4.3 | 4.4 | `4.4/dbupdate-4.3.0-to-4.4.0.sql` |
| 4.4 | 4.5 | `4.5/dbupdate-4.4.0-to-4.5.0.sql` |
| 4.5 | 4.6 | `4.6/dbupdate-4.5.0-to-4.6.0.sql` |
| 4.6 | 4.7 | `4.7/dbupdate-4.6.0-to-4.7.0.sql` |
| 4.7 | 5.0 | `5.0/dbupdate-4.7.0-to-5.0.0.sql` |
| 5.0 | 5.1 | `5.1/dbupdate-5.0.0-to-5.1.0.sql` |
| 5.1 | 5.2 | `5.2/dbupdate-5.1.0-to-5.2.0.sql` |
| 5.2 | 5.3 | `5.3/dbupdate-5.2.0-to-5.3.0.sql` |
| 5.3 | 5.4 | `5.4/dbupdate-5.3.0-to-5.4.0.sql` |

Apply one with the client of your database, for example:

```bash
mysql -u USER -p DATABASE < update/database/mysql/4.1/dbupdate-4.0.0-to-4.1.0.sql
```

Rules that keep this safe:

- Do not skip a file and do not apply one twice. If a statement fails because a column already exists, the file
  was applied before: stop and compare with your backup.
- If you use the database cluster (DFS or database file handler), also apply the
  `dbupdate-cluster-...` file of the same step where one exists (4.3, 4.7, 5.2, 5.4).
- The directories named `unstable` hold files of pre-releases; do not apply them.
- Directories `6.12` and `7.3` are not part of this path; the 6.0 files in Part B take a 5.4 database to today.
- Several steps ship a PHP script that repairs data; run the ones of the versions you pass through, from the
  project root, for example `php update/common/scripts/4.1/updateimagesystem.php`. List them with
  `ls update/common/scripts/*`, and read the first lines of a script (`--help` where it offers it) before you run it.
  The what and why of each release is in its notes:
  [4.1](../bc/4.1/changes-4.1.0.txt), [4.2](../bc/4.2/changes-4.2.0.txt), [4.3](../bc/4.3/changes-4.3.0.txt),
  [4.4](../bc/4.4/changes-4.4.0.txt), [4.5](../bc/4.5/changes-4.5.0.txt), [4.6](../bc/4.6/changes-4.6.0.txt),
  [4.7](../bc/4.7/changes-4.7.0.txt), [5.0](../bc/5.0/changes-5.0.txt), [5.1](../bc/5.1/changes-5.1.txt),
  [5.2](../bc/5.2/changes-5.2.txt), [5.3](../bc/5.3/changes-5.3.txt), [5.4](../bc/5.4/changes-5.4.txt).
- The update chain is checked by `./console exp:checkdbfiles --allow-root-user`, which lists missing or stray files
  of the upgrade path.

Then read the notes of the 5.90 line, which prepared the move to PHP 7 and a longer password rule:
[5.90 overview](../bc/5.90/README.md), [PHP 7](../bc/5.90/php7.md), [password length](../bc/5.90/password_length.md),
[relation indexing](../bc/5.90/relation_indexing.md).

## Part B: from 5.4 (or 5.90) to Exponential 6.0.0

1. Put the new code in place. Either a new checkout and a copy of your `settings/override/`, `settings/siteaccess/`,
   `extension/` and `design/` into it, or, when you manage the site with Composer, change the constraint of
   `se7enxweb/exponential` in your project's `composer.json` and run `composer update se7enxweb/exponential`.
   Do not let a tool replace your own settings or extensions.
2. Record the new version in the database. The same file brings the schema to what the 6.0 kernel uses: it widens
   `ezuser.password_hash` from the `varchar(50)` of every 5.x schema to 255 (with the default `HashType=php_default`
   and `UpdateHash=true` each user's hash becomes a 60-character bcrypt hash at the first sign-in, which the old
   column cannot hold), adds `ezcontentobject_trash.trashed` (without it moving content to the trash fails) and, on
   PostgreSQL, renames the sequences to `<table>_<column>_seq` (the names the kernel reads new ids from). Each change
   leaves a database that already has it as it is:

   ```bash
   mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
   ```

   PostgreSQL: `update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql`. The MySQL file begins with
   `SET default_storage_engine=InnoDB;`, which MySQL (from 5.5.3) and MariaDB accept, so run the whole file. Copies of
   the file from before October 2026 began with `SET storage_engine=InnoDB;`, which MySQL 5.7.5 and newer and
   MariaDB 12.0 and newer reject; take the current file. Details: [Changelog 6.0.0](../changelogs/6.0/6.0.0.md).
3. PHP: Exponential 6.0 needs PHP 8.0 or newer on the current line (8.1 or newer for Exponential Velocity; see
   [PHP 8.0 support](../bc/6.0/php-8.0-support.md)). A site that must stay on PHP 7.4 stays on 6.0.7; read [PHP 8 support](../bc/6.0/php8.md) for the order of the upgrade steps, and check your own extensions
   for PHP 8 warnings (classes extending `eZPersistentObject` or `eZDataType` are the usual cases).
4. Continue with Part C, which is the same procedure for every 6.0.x step.

## Part C: from any 6.0.x to today

### C1. Put the new code in place

Take the new code as in Part B step 1 (checkout of the tag or branch, or `composer update`). Then regenerate the
class map and clear the caches, in this order:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Expected: the first prints how many classes it found; the second ends with a line saying the caches were cleared.
Do not use `-k` on an installation that keeps extra working copies of the code inside its root (for example git worktree folders) without excluding them, e.g. `--exclude='<folder>'`; otherwise kernel classes are mapped into those copies.
Then reload the PHP-FPM that serves the site (`systemctl reload <your-php-fpm-service>`), and, if you run Velocity,
`./console exp:velocity deploy --dry-run --allow-root-user` shows every step the real command would run.

### C2. Apply the database update

The file for the line is `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (MySQL, PostgreSQL and SQLite
files exist). It records the version, makes the schema changes of the Part B file on MySQL and PostgreSQL (for a site
that came from 5.x with an older copy of that file; a database that already has them is left as it is) and adds the
columns that later changes need (PDF export footer, OPML and podcast fields and more). Apply it **once, whole, in
order**; if your database already has a column, the statement fails and tells you which one, skip only that
statement. A site from 5.x that applied an older copy of this file and still has a 50-character `password_hash`, no
`ezcontentobject_trash.trashed` or (PostgreSQL) `<table>_s` sequences does not apply it again: it applies the current
Part B file and sets the version row back with
`UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';`. Otherwise:

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
```

Then create the audit index tables once (the audit trail is on by default from 6.0.15):

```bash
php update/common/scripts/6.0/createaudittables.php
```

Check the version row (same query as at the top; expected `6.0.15stable` after this file).

### C3. Read the checklist of each period you pass

The upgrade notes are written per period. Read every one between your version and today; each opens with a
quick checklist and a "what to do" column.

| Your version is older than | Read |
|---|---|
| 6.0.8 | [Changelog 6.0.8](../changelogs/6.0/6.0.8.md) and [PHP 8 support](../bc/6.0/php8.md) |
| 6.0.10 | [Changelog 6.0.10](../changelogs/6.0/6.0.10.md), [The product is called Exponential](../features/6.0/rebranding-to-exponential.md) |
| 6.0.13 | [Changelog 6.0.13](../changelogs/6.0/6.0.13.md), [Security hardening 6.0.13](../specifications/6.0/security-hardening-6.0.13.md) |
| July 2026 | [Behaviour changes of July and August 2026](../bc/6.0/behaviour-changes-2026-07-08.md) |
| 16 September 2026 | [Behaviour changes of 16 to 30 September 2026](../bc/6.0/behaviour-changes-2026-09b.md) and [Security defaults](../specifications/6.0/security-defaults-2026-09.md) |
| 1 October 2026 | [Behaviour changes of 1 to 2 October 2026](../bc/6.0/behaviour-changes-2026-10.md) |

The three changes people most often meet:

- Accounts without an `ezuser_setting` row cannot sign in (August 2026): make sure each user has one.
- Overrides follow `Priority` (July 2026): review any `override.ini` where only some blocks have a `Priority`.
- Security headers, `HttpOnly`/`SameSite` session cookies and a stricter upload check are on by default
  (September and October 2026): if a host frames your admin, set the header deliberately.

For what changed by month, the story is in the [chronicle](../history/2026/); every single change is in the
[ledger of the root repository](../history/ledger/exponential-root.md).

### C4. Check your own extensions

```bash
php bin/php/ezpgenerateautoloads.php -e
./console exp:checkclasses --allow-root-user
```

`exp:checkclasses` loads every declared class and reports those PHP refuses to load, which is how a PHP 8
incompatibility in an extension shows. Extensions carry their own version in `ezinfo.php` and `extension.xml`;
the extension list of the admin (**Setup** > **Extensions**) shows each one, see
[Extension list](../features/6.0/extension-list-and-downloads.md). The order in which extensions load changed in
2026: [Extension loading order](../features/6.0/extension-loading-order.md).

## Verify the upgrade

Run these; each states what good looks like.

```bash
php bin/php/ezcache.php --clear-all --allow-root-user         # ends without an error
./console exp:checkdbfiles --allow-root-user                   # no file marked as missing for your line
```

Then in the browser: the front page loads; log in to the admin; open **Setup** > **System information** and read the
version; publish a test item and see it on the site. In the admin, **Setup** > **Upgrade check** and
**Check file consistency** list files of the code that differ from the release
([File consistency check](../features/6.0/file-consistency-check.md)). Files you changed on purpose are expected there.

Last, clear the content cache and, if you use Velocity, its response cache, after the PHP-FPM reload, so a page
rendered by old code is not served again.

## If something goes wrong

| Symptom | What to do |
|---|---|
| A white page after the code change | `var/log/error.log`; run C1 again; `vendor/` must match the new `composer.json` |
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches |
| Libraries missing after a failed Composer run | [Repairing an installation](../bc/6.0/repair.md) |
| A SQL file failed half way | restore the backup of step 1 and apply the file again from the start; never repair by hand and re-run |
| Someone cannot sign in | [Behaviour changes of July and August 2026](../bc/6.0/behaviour-changes-2026-07-08.md), the `ezuser_setting` item |

A published release can not be changed; the way to get a fix is the next version. To see what a version holds, open
its changelog: [6.0.0 to 6.0.15](../changelogs/6.0/6.0.15.md).

## Related pages

- [Getting started](getting-started.md): a clean install of the same version, for a test copy.
- [Operating a site](operating-a-site.md): backups, caches and repairs after the upgrade.
- [Deploying](deploying.md) and [Velocity engines](../bc/6.0/velocity-engines.md): reload the right PHP-FPM, or move to a faster server.
- [Extensions](extensions.md): versions, loading order and releases of your own extensions.
- Release notes: [Exponential 6.0 changelogs](../changelogs/6.0/6.0.15.md) and [the extensions' release notes](../changelogs/extensions/README.md).
