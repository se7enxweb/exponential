---
title: Schema changes and migrations
audience: software-engineer, developer
category: software-engineering
order: 210
summary: How a database schema change is shipped in Exponential - the .dba schema files and schema handlers, update SQL per driver, update scripts, the upgrade check that compares a site with the release schema, the absence of a migration runner, and a checklist for a change that every installation can apply.
keywords: schema, migration, dba, db_schema.dba, update SQL, dbupdate, ezsqldumpschema, ezsqlinsertschema, upgrade check, database consistency, schema handler, software engineer
type: help
---
# Schema changes and migrations

This page is for engineers who add or change a table, a column or an index, in the kernel or in an extension.
Exponential has no migration runner: schema changes are shipped as files and applied by the operator. That makes the
discipline of the change itself the safety net.

## Contents

1. [The files involved](#the-files-involved)
2. [How an installation applies a change](#how-an-installation-applies-a-change)
3. [The tools](#the-tools)
4. [Checklist for a schema change](#checklist-for-a-schema-change)
5. [Known gaps](#known-gaps)

## The files involved

| File | Role |
|---|---|
| `share/db_schema.dba`, `share/db_data.dba` | the kernel's schema and seed data as PHP arrays; extensions ship their own in their `share/` |
| `settings/dbschema.ini [SchemaSettings]` | maps an engine to its schema handler (`eZMysqlSchema`, `eZPgsqlSchema`, `eZSQLiteSchema`, `expMongoSchema`; `eZOracleSchema` with `ezoracle`) |
| `kernel/sql/<engine>/` | the SQL generated per engine |
| `update/database/<driver>/6.0/` | the upgrade SQL per driver, for example `dbupdate-6.0.0-6.0.15.sql` (MySQL, PostgreSQL and SQLite files exist) |
| `update/common/scripts/6.0/` | PHP scripts an upgrade runs, such as `createaudittables.php` |

Oracle's update files are in `ezoracle`; MongoDB has none, because it has no SQL schema.

## How an installation applies a change

From [Compatibility, releases and deployment](../../standards/architecture/compatibility-releases-deployment.md#upgrading-an-installation):

1. Back up files and database.
2. Put the new code in place, regenerate the class maps and clear all caches; reload PHP-FPM and restart Velocity.
3. Apply the database update **once, whole, in order**: the file in `update/database/<driver>/6.0/`, then the scripts
   in `update/common/scripts/6.0/` that the upgrade guide names.
4. Run the upgrade check: Setup > System upgrade, **Check database consistency** compares the tables with the schema
   files ([The upgrade check](../../guides/upgrade-check.md)).

The update file must say how to proceed on a database that already has part of it; for the 6.0.15 file, a statement
for an existing column fails with its name, and only that statement is skipped.

## The tools

```bash
php bin/php/ezsqldumpschema.php --help     # dump a database schema to a .dba file
php bin/php/ezsqlinsertschema.php --help   # load a .dba schema into an engine (--type)
php bin/php/ezsqldiff.php --help           # compare two schemas
```

Reviewing a `.dba` change by its diff is misleading: rows live in a numerically keyed array, so removing one renumbers
every row after it. Compare the schema the files produce instead, for example with `ezsqldiff.php` or a test that
loads the file into a throwaway SQLite database ([Schema and dba files](../creators/app-creators/schema-and-dba-files.md)).

## Checklist for a schema change

- [ ] The change is in `share/db_schema.dba` (and `db_data.dba` for seed rows) of the kernel or the extension.
- [ ] An update file exists for every driver with SQL: MySQL/MariaDB, PostgreSQL, SQLite, and Oracle in `ezoracle`.
- [ ] MongoDB is covered: the adapter or the installer creates what the code needs.
- [ ] The update is safe on a database that already has part of it.
- [ ] A persistent class (`eZPersistentObject`) describes a new table.
- [ ] New tables use the `exp` prefix; columns added to `ez` tables keep the table's style
      ([Layering and naming rules](../../standards/architecture/layering-and-naming.md)).
- [ ] The changelog lists it under **Added** or **Updated**, and the upgrade notes in `doc/eng-us/bc/6.0/` say what
      to run ([Changelogs and upgrade notes](changelogs-and-bc-notes.md)).
- [ ] The CI database jobs (MySQL 8.0 and PostgreSQL 15) pass.

## Known gaps

- The update files are applied by hand; nothing records which statements ran.
- MariaDB and Oracle have no CI job in the kernel repository.

## Related pages

- [Persistence and the database layer](persistence-and-the-database-layer.md)
- [Database schema and dba files](../extension-development/part-3-extension-developer/database-schema-and-dba-files.md)
- [Upgrading](../../guides/upgrading.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
