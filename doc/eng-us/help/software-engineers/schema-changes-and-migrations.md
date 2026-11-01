---
title: Schema changes and migrations
audience: software-engineer, developer
category: software-engineering
order: 210
summary: How a database schema change is shipped in Exponential - the .dba schema files and schema handlers, upgrade steps that exp:upgrade applies and records, migrations for a site's own changes, the upgrade check that compares a site with the release schema, and a checklist for a change that every installation can apply.
keywords: schema, migration, dba, db_schema.dba, update SQL, dbupdate, ezsqldumpschema, ezsqlinsertschema, upgrade check, database consistency, schema handler, exp:upgrade, exp:migration, upgrade step, software engineer
type: help
---
# Schema changes and migrations

This page is for engineers who add or change a table, a column or an index, in the kernel or in an extension.
Since 6.0.15 a schema change is shipped as an **upgrade step** that `./console exp:upgrade` applies and records on
every engine, and a site's own changes as **migrations** that `./console exp:migration:migrate` applies in order, with
a caption before the run and a record of what ran. The SQL update files remain as a reference for installations that
are upgraded by hand.

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
| `kernel/classes/upgrade/steps/`, `settings/upgrade.ini` | the kernel's upgrade steps (`expUpgradeStep`) and their list; an extension adds its own with `ExtensionStepList[]` |
| `migrations/`, `extension/<name>/migrations/` | a site's and an extension's migrations, `YYYYMMDDHHMMSS_name.php` |

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

Steps 1 to 3 are one command since 6.0.15: `./console exp:upgrade --apply` takes a caption, applies every pending
upgrade step on every engine, records each in `exp_migration` and deploys
([Upgrading with the console](../../guides/upgrading-with-the-console.md)). An extension ships its schema change as an
upgrade step of its own ([Upgrade steps for extensions](../../guides/upgrade-steps-for-extensions.md)). A change of
one site (its own table, classes, content, roles) is a migration: `./console exp:migration:migrate --apply` runs the
pending ones in order, in a transaction where the engine has one, restores the caption after a failure, and commits
the record of the run ([Migrations](../../guides/migrations.md), [PHP migrations](../../guides/migrations-php.md)).

## The tools

```bash
./console exp:upgrade:status               # every upgrade step and its state
./console exp:migration:status             # every migration and its state
./console exp:migration:check              # the lint of migration files, for CI
php bin/php/ezsqldumpschema.php --help     # dump a database schema to a .dba file
php bin/php/ezsqlinsertschema.php --help   # load a .dba schema into an engine (--type)
php bin/php/ezsqldiff.php --help           # compare two schemas
```

Reviewing a `.dba` change by its diff is misleading: rows live in a numerically keyed array, so removing one renumbers
every row after it. Compare the schema the files produce instead, for example with `ezsqldiff.php` or a test that
loads the file into a throwaway SQLite database ([Schema and dba files](../creators/app-creators/schema-and-dba-files.md)).

## Checklist for a schema change

- [ ] The change is in `share/db_schema.dba` (and `db_data.dba` for seed rows) of the kernel or the extension.
- [ ] An upgrade step applies it on every engine and detects it (`detect()`), so `exp:upgrade` records it and a
      second run changes nothing; a site's own change is a migration instead.
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

- An installation upgraded by hand with the SQL update files has no record of which statements ran;
  `exp:upgrade` records every step it applies, and `exp:upgrade:verify` finds drift.
- Migrations are written in PHP in 6.0.15; YAML, SQL and INI migrations are found and checked but not run yet.
- MariaDB and Oracle have no CI job in the kernel repository.

## Related pages

- [Persistence and the database layer](persistence-and-the-database-layer.md)
- [Database schema and dba files](../extension-development/part-3-extension-developer/database-schema-and-dba-files.md)
- [Upgrading](../../guides/upgrading.md), [Upgrading with the console](../../guides/upgrading-with-the-console.md)
- [Migrations](../../guides/migrations.md), [PHP migrations](../../guides/migrations-php.md),
  [The migration file format](../../specifications/6.0/migration-file-format.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
