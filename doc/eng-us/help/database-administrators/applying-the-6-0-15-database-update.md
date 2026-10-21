---
title: Applying the 6.0.15 database update
audience: dba
category: database-administration
order: 31
summary: A step-by-step procedure for the database part of an upgrade from any 6.0.x release to 6.0.15 - backup, maintenance mode, the dbupdate-6.0.0-6.0.15.sql file per engine, what it adds, the audit tables with createaudittables.php, the trash records, the version check and the special cases of SQLite, Oracle, MongoDB and sites that came from 5.x.
keywords: 6.0.15, upgrade, dbupdate-6.0.0-6.0.15.sql, update file, createaudittables.php, movetrashrecords.php, expaudit, expmail, expbookmark_folder, ezrss_export_opml_item, password_hash, trashed, trashed_by, 6.0.15stable, SQLite duplicate column, MongoDB, Oracle
type: help
---
# Applying the 6.0.15 database update

This page is for database administrators who apply the database part of an upgrade from 6.0.0 to 6.0.14 (or an
earlier checkout of the 6.0.15 line) to 6.0.15. It is the database side of
[11.6 From any 6.0.x to today](../../install/11-upgrading.md#116-from-any-60x-to-today): the code is put in place by
whoever deploys the site, the database by you. Follow the steps in order.

## Contents

1. [What the update adds](#what-the-update-adds)
2. [Step 1: prepare](#step-1-prepare)
3. [Step 2: apply the file](#step-2-apply-the-file)
4. [Step 3: the audit tables](#step-3-the-audit-tables)
5. [Step 4: the trash records](#step-4-the-trash-records)
6. [Step 5: verify](#step-5-verify)
7. [Special cases](#special-cases)
8. [Checklist](#checklist)

## What the update adds

`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (MySQL, PostgreSQL, SQLite) records the version
`6.0.15stable` and adds the tables and columns of the line:

| Feature | Tables and columns |
|---|---|
| PDF export footer | `ezpdf_export.show_footer`, `footer_text` |
| OPML and podcast exports | `ezrss_export.opml_head`, `podcast_head`, table `ezrss_export_opml_item` |
| Audit index | `expaudit_cursor`, `expaudit_event`, `expaudit_file` |
| Bookmark folders | `expbookmark_folder`, `ezcontentbrowsebookmark.folder_id`, `priority` |
| E-mail preferences | `expmail_category`, `expmail_consent_log`, `expmail_pending`, `expmail_preference`, `expmail_suppression` |
| Who trashed an object | `ezcontentobject_trash.trashed_by`, `trashed_via` |
| Personal API keys | `expapikey` |

On MySQL and PostgreSQL it also makes, where missing, the three changes of the 5.4 to 6.0.0 file (the wider
`ezuser.password_hash`, `ezcontentobject_trash.trashed`, and on PostgreSQL the `<table>_<column>_seq` sequence names),
leaving a database that has them as it is.

## Step 1: prepare

```bash
php bin/php/maintenance.php on --message="Upgrading" --allow-root-user
```

Take a backup of the database and of `settings/override` ([Backups per engine](backups-per-engine.md)). Read the
version row; it should be a 6.0 version below `6.0.15stable`.

## Step 2: apply the file

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
sqlite3 var/storage/sqlite3/exponential.db < update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

Run the one for your engine, **once, whole, in order**. If the database already has a column, the statement fails and
names it; skip only that statement. The MySQL file has no `SET default_storage_engine` line, because each of its
tables names its engine.

## Step 3: the audit tables

The audit trail is on by default from 6.0.15. Create its index tables and index the audit files written so far:

```bash
php update/common/scripts/6.0/createaudittables.php --dry-run
php update/common/scripts/6.0/createaudittables.php
```

The script works on every engine (MySQL, MariaDB, PostgreSQL, SQLite, Oracle and MongoDB, each through its schema
handler) and leaves existing tables alone, so it can run again. `--dry-run` shows which tables are missing;
`--no-index` creates the tables only. If it stops with "The audit index classes are not in the autoload array", run
`php bin/php/ezpgenerateautoloads.php -k` first.

The same directory holds `createapikeytable.php`, which creates the table of personal API keys (`expapikey`) through
the schema handler of any engine and leaves an existing table alone. The SQL update files of MySQL, PostgreSQL and
SQLite create the same table; on Oracle and MongoDB run the script (`--dry-run` first)
([API keys](../../guides/api-keys.md#10-the-data-model-and-the-upgrade)).

## Step 4: the trash records

Until `trashed_by` and `trashed_via` exist, the kernel writes who trashed an object to `<VarDir>/trash/trashed.json`.
After the update file, move those records into the table:

```bash
php update/common/scripts/6.0/movetrashrecords.php
```

See [the trash view](../../bc/6.0/trash.md#where-trashed-by-is-kept).

## Step 5: verify

```bash
php bin/php/console exp:checkdbfiles --no-verify-branches     # prints nothing
php bin/php/ezcache.php --clear-all --allow-root-user
```

- The version row reads `6.0.15stable`.
- **Setup > Upgrade check**, "Check database consistency", lists no missing table
  ([Checking the schema after an upgrade](checking-the-schema-after-an-upgrade.md)).
- Sign in, publish a test object, move it to the trash and restore it.
- Maintenance off: `php bin/php/maintenance.php off --allow-root-user`.

## Special cases

| Case | What to do |
|---|---|
| SQLite, file already applied | A second run fails with "duplicate column name" on every `ALTER TABLE`; the database is already updated. Never run the file twice on purpose. |
| A site from 5.x that applied an older copy of the file | It may lack the three 5.4 changes. Do not apply the file again: apply the current 5.4 to 6.0.0 file of your engine (it can run again), then set the version row back: `UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';` |
| Oracle | No 6.0.15 SQL file in the kernel; the audit tables come from `createaudittables.php`. Run `ext:ezoracle:schema-diff` afterwards. |
| MongoDB | No SQL file; run `createaudittables.php`, then the upgrade check for missing collections and `create_indexes.js`. |
| `SET storage_engine=InnoDB;` fails | An update file from before October 2026; take the current file. |

## Checklist

- [ ] Maintenance on; backup taken.
- [ ] The engine's 6.0.15 file applied once.
- [ ] `createaudittables.php` run (dry run first).
- [ ] `movetrashrecords.php` run.
- [ ] Version row `6.0.15stable`; upgrade check clean; test publish done.
- [ ] Caches cleared; maintenance off.

## Related pages

- [Upgrades and the SQL update files](upgrades-and-sql-update-files.md)
- [Upgrading](../../guides/upgrading.md)
- [Audit](../../bc/6.0/audit.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
