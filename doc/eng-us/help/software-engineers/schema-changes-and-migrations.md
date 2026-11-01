---
title: Schema changes and migrations
audience: software-engineer, developer
category: migrations
order: 210
summary: How a database schema change reaches every installation of Exponential - the .dba schema files for new installations, upgrade steps that exp:upgrade applies to every installation of the kernel or an extension, and migrations for a site's own changes - which to choose, how each is applied, tested, reviewed and deployed, and a checklist, with links to every guide, recipe and reference page.
keywords: schema, schema change, database change, migration, migrations, dba, db_schema.dba, update SQL, dbupdate, ezsqldumpschema, ezsqlinsertschema, ezsqldiff, upgrade check, database consistency, schema handler, exp:upgrade, exp:migration, exp:migration:migrate, upgrade step, expUpgradeSchemaStep, expMigrationPhp, add table, add column, add index, deploy, CI, software engineer
type: help
---
# Schema changes and migrations

This page is for engineers who add or change a table, a column or an index, in the kernel, in an extension or in
one site, and need that change to reach every installation the same way. It is the overview: it says which of the
three tools to use, how each is applied, and links to the guide, the recipe and the reference of each.

## Contents

1. [Three ways a schema reaches an installation](#three-ways-a-schema-reaches-an-installation)
2. [Which to use](#which-to-use)
3. [The files involved](#the-files-involved)
4. [How an installation applies a change](#how-an-installation-applies-a-change)
5. [Migrations for a site's own changes](#migrations-for-a-sites-own-changes)
6. [Testing, review and delivery](#testing-review-and-delivery)
7. [The tools](#the-tools)
8. [Checklist for a schema change](#checklist-for-a-schema-change)
9. [Known gaps](#known-gaps)
10. [Related pages](#related-pages)

## Three ways a schema reaches an installation

| Tool | For | Applied by | Recorded in | Read more |
|---|---|---|---|---|
| **The `.dba` schema** (`share/db_schema.dba`, an extension's `share/`) | a **new** installation: the tables as they are today | the installer, `ezsqlinsertschema.php` | nothing: it is the starting point | [Database schema and dba files](../extension-development/part-3-extension-developer/database-schema-and-dba-files.md) |
| **An upgrade step** (`expUpgradeStep`, listed in `upgrade.ini`) | every **existing** installation of the kernel or of an extension, on upgrade | `./console exp:upgrade --apply` | `exp_migration` (source `kernel`) | [Upgrading with the console](../../guides/upgrading-with-the-console.md), [Upgrade steps for extensions](../../guides/upgrade-steps-for-extensions.md) |
| **A migration** (`YYYYMMDDHHMMSS_name.php`, `expMigrationPhp`) | **one site**: its own tables, classes, content, roles, settings, on development, staging and production in turn | `./console exp:migration:migrate --apply`, or Setup > Migrations | `exp_migration` and `exp_migration_run`, and a status file in git | [Migrations](../migrations/README.md), [the guide](../../guides/migrations.md) |

A kernel or extension change usually needs both of the first two: the `.dba` change so new installations have it,
and an upgrade step so existing ones get it. A site's change needs only a migration. The upgrade steps are also listed
among the migrations, as migrations of the source `kernel`, and a migration run applies the pending ones of the
stages `database` and `scripts` first; a site migration names a step it needs in `requires:`
([The steps among the migrations](../../guides/upgrading-with-the-console.md#the-steps-among-the-migrations)).

## Which to use

| The change | Use |
|---|---|
| a new kernel table or column, in a release | `.dba` + an upgrade step in `kernel/classes/upgrade/steps/` |
| an extension's table, column, default row or policy that every installation of it needs | `.dba` of the extension + an upgrade step registered in `ExtensionStepList[]` ([Upgrade steps for extensions](../../guides/upgrade-steps-for-extensions.md)) |
| a table or column that only one site has | a migration in the site's `migrations/` ([Add a database table](../migrations/recipes/add-a-database-table.md), [Add a column or an index](../migrations/recipes/add-a-column-or-an-index.md)) |
| a content class, content, a role or a setting of one site | a migration ([the recipes](../migrations/recipes/README.md)) |
| a change one extension's users build on, applied in order with their own migrations | a migration in `extension/<name>/migrations/` ([Migrations in an extension or a package](../migrations/recipes/migrations-in-an-extension-or-package.md)) |
| moving a site to another engine or host | neither: [Migrations versus migrating a site](../migrations/migrations-versus-migrating-a-site.md) |

The longer comparison: [Upgrade step or migration?](../migrations/recipes/upgrade-step-or-migration.md).

## The files involved

| File | Role |
|---|---|
| `share/db_schema.dba`, `share/db_data.dba` | the kernel's schema and seed data as PHP arrays; extensions ship their own in their `share/` |
| `settings/dbschema.ini [SchemaSettings]` | maps an engine to its schema handler (`eZMysqlSchema`, `eZPgsqlSchema`, `eZSQLiteSchema`, `expMongoSchema`; `eZOracleSchema` with `ezoracle`) |
| `kernel/sql/<engine>/` | the SQL generated per engine |
| `update/database/<driver>/6.0/` | the upgrade SQL per driver, for example `dbupdate-6.0.0-6.0.15.sql` (MySQL, PostgreSQL and SQLite files exist), for installations upgraded by hand |
| `update/common/scripts/6.0/` | PHP scripts an upgrade runs, such as `createaudittables.php` |
| `kernel/classes/upgrade/steps/`, `settings/upgrade.ini` | the kernel's upgrade steps (`expUpgradeStep`) and their list; an extension adds its own with `ExtensionStepList[]` |
| `migrations/`, `extension/<name>/migrations/` | a site's and an extension's migrations, `YYYYMMDDHHMMSS_name.php` |
| `migrations/status/<environment>.json`, `migrations/runs/<run-id>.log` | the record of the migration runs, committed to git ([The migrations repository](../../guides/migrations-repositories.md)) |
| `settings/migration.ini` | where migrations are found, how runs behave, captions, the repository ([Settings](../migrations/settings.md)) |

Oracle's update files are in `ezoracle`; MongoDB has none, because it has no SQL schema.

## How an installation applies a change

Since 6.0.15, two commands do the work, both under the same installation lock and with the same captions:

```bash
./console exp:upgrade --apply              # the kernel's and the extensions' upgrade steps, all stages, then deploy
./console exp:migration:migrate --apply    # pending database and scripts steps, then the site's migrations
```

`exp:upgrade` takes a caption, applies every pending upgrade step on every engine, records each and deploys
([Upgrading with the console](../../guides/upgrading-with-the-console.md)). `exp:migration:migrate` takes a caption,
runs the pending migrations in order, each in a transaction where the engine has one, restores the caption after a
failure, and commits, tags and pushes the record of the run ([Migrations](../../guides/migrations.md)).

By hand, as before 6.0.15 ([Compatibility, releases and deployment](../../standards/architecture/compatibility-releases-deployment.md#upgrading-an-installation)):

1. Back up files and database.
2. Put the new code in place, regenerate the class maps and clear all caches; reload PHP-FPM and restart Velocity.
3. Apply the database update **once, whole, in order**: the file in `update/database/<driver>/6.0/`, then the scripts
   in `update/common/scripts/6.0/` that the upgrade guide names.
4. Run the upgrade check: Setup > System upgrade, **Check database consistency** compares the tables with the schema
   files ([The upgrade check](../../guides/upgrade-check.md)).

The update file must say how to proceed on a database that already has part of it; for the 6.0.15 file, a statement
for an existing column fails with its name, and only that statement is skipped. A team moving away from such files
to migrations: [Coming from SQL update files](../migrations/recipes/coming-from-sql-update-files.md).

## Migrations for a site's own changes

A migration is a PHP class with `plan()` (what it would change, for the dry run), `up()` (the change), and where it
can `down()` (the way back) and `detect()` (the state, asked of the installation). Tables are created on every engine
from a definition in the `.dba` format with `expKernelTableSchema::install()`; columns and indexes through the
engine's schema handler, as the kernel's own `expUpgradeSchemaStep` does. Start with
[the tutorial](../../guides/migrations-tutorial.md), then [PHP migrations](../../guides/migrations-php.md).

| Task | Recipe |
|---|---|
| a table of the site | [Add a database table](../migrations/recipes/add-a-database-table.md) |
| a column or an index | [Add a column or an index](../migrations/recipes/add-a-column-or-an-index.md) |
| what differs per engine | [Engine notes](../migrations/recipes/engine-notes.md) |
| data in the new column | [Fix data](../migrations/recipes/fix-data.md), [Large data migrations](../migrations/recipes/large-data-migrations.md) |
| a way back | [Writing a reversible down()](../migrations/recipes/writing-a-reversible-down.md) |
| only on some environments | [Per-environment migrations](../migrations/recipes/per-environment-migrations.md) |

The design rules: one change per migration; find things by name or remote id, never by a numeric id; create only
what is missing, so a migration is safe on any installation that has not recorded it; never edit a migration that
ran anywhere (the checksum guard refuses it); keep secrets out (the run log is committed). More:
[Migrations for software engineers](migrations-for-software-engineers.md).

## Testing, review and delivery

| Stage | What | Page |
|---|---|---|
| CI on every pull request | `./console exp:migration:check` (no database), a PHPUnit test on a throwaway SQLite database | [Testing migrations](../migrations/recipes/testing-migrations.md) |
| before merging | `./console exp:migration:dry-run --simulate --diff` on a PostgreSQL or SQLite copy; the review checklist | [Reviewing a migration](../migrations/recipes/reviewing-a-migration.md) |
| in the team | timestamps, `requires:`, merge conflicts | [Team workflow with git](../migrations/recipes/team-workflow-with-git.md) |
| on deploy | `./console exp:migration:migrate --apply`, then `./console exp:velocity deploy` | [Deploying with migrations](../migrations/recipes/deploying-with-migrations.md) |
| after deploy | the status, the record in git, the audit events | [The audit trail of runs](../migrations/recipes/the-audit-trail-of-runs.md) |
| when it fails | the automatic restore, rollback | [When a migration fails](../migrations/when-a-migration-fails.md) |

## The tools

```bash
./console exp:upgrade:status               # every upgrade step and its state
./console exp:migration:status             # every migration (and step) and its state
./console exp:migration:check              # the lint of migration files, for CI
./console exp:migration:dry-run --simulate --diff   # run them in a transaction that is rolled back (PostgreSQL, SQLite)
php bin/php/ezsqldumpschema.php --help     # dump a database schema to a .dba file
php bin/php/ezsqlinsertschema.php --help   # load a .dba schema into an engine (--type)
php bin/php/ezsqldiff.php --help           # compare two schemas
```

Every migration command: [Commands](../migrations/commands.md). Reviewing a `.dba` change by its diff is misleading:
rows live in a numerically keyed array, so removing one renumbers every row after it. Compare the schema the files
produce instead, for example with `ezsqldiff.php` or a test that loads the file into a throwaway SQLite database
([Schema and dba files](../creators/app-creators/schema-and-dba-files.md)).

## Checklist for a schema change

- [ ] The change is in `share/db_schema.dba` (and `db_data.dba` for seed rows) of the kernel or the extension, so a
      new installation has it; a site-only change needs no `.dba`.
- [ ] Existing installations get it: an upgrade step that applies it on every engine and detects it (`detect()`), so
      `exp:upgrade` records it and a second run changes nothing; or, for one site, a migration.
- [ ] A migration uses `$context->db()` and the schema handlers, works on every engine the site may run on (or names
      them in `engines:`), and creates only what is missing.
- [ ] An update file exists for every driver with SQL when installations are upgraded by hand: MySQL/MariaDB,
      PostgreSQL, SQLite, and Oracle in `ezoracle`.
- [ ] MongoDB is covered: the adapter or the installer creates what the code needs.
- [ ] The update is safe on a database that already has part of it.
- [ ] A persistent class (`eZPersistentObject`) describes a new table.
- [ ] New tables use the `exp` prefix; columns added to `ez` tables keep the table's style
      ([Layering and naming rules](../../standards/architecture/layering-and-naming.md)).
- [ ] `./console exp:migration:check` passes, and the migration was simulated or run on a copy.
- [ ] The changelog lists it under **Added** or **Updated**, and the upgrade notes in `doc/eng-us/bc/6.0/` say what
      to run ([Changelogs and upgrade notes](changelogs-and-bc-notes.md)).
- [ ] The CI database jobs (MySQL 8.0 and PostgreSQL 15) pass.

## Known gaps

- An installation upgraded by hand with the SQL update files has no record of which statements ran;
  `exp:upgrade` records every step it applies, and `exp:upgrade:verify` finds drift.
- Migrations are written in PHP, YAML, SQL or INI; a schema change that must run on every engine is best a `schema`
  step ([Migrations: SQL and settings](../../guides/migrations-sql-and-settings.md)).
- The simulation runs on PostgreSQL and SQLite only; on MySQL/MariaDB, Oracle and MongoDB the plan is listed.
- MariaDB and Oracle have no CI job in the kernel repository.

## Related pages

- [Migrations: the hub](../migrations/README.md), [All recipes](../migrations/recipes/README.md),
  [Frequently asked questions](../migrations/faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](../migrations/in-the-administration.md), [Commands](../migrations/commands.md),
  [Settings](../migrations/settings.md), [Policies, views and events](../migrations/policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [When a migration fails](../migrations/when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations/migrations-versus-migrating-a-site.md)
- [Persistence and the database layer](persistence-and-the-database-layer.md)
- [Database schema and dba files](../extension-development/part-3-extension-developer/database-schema-and-dba-files.md)
- [Upgrading](../../guides/upgrading.md), [Upgrading with the console](../../guides/upgrading-with-the-console.md),
  [Upgrade steps for extensions](../../guides/upgrade-steps-for-extensions.md)
- [Software engineers: start here](software-engineers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
