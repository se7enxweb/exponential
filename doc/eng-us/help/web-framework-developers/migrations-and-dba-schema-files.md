---
title: "Migrations and seed data: .dba schema files and packages"
audience: web-framework-developer, developer
category: web-frameworks
order: 160
summary: What replaces migration classes, seeders and fixtures in Exponential - database-neutral .dba schema files, ezsqldumpschema and ezsqlinsertschema, an idempotent install command, upgrade steps you ship, db_data.dba seed rows, content packages for classes and content, and how a deployment applies them.
keywords: migrations, schema, seeders, fixtures, dba, db_schema.dba, db_data.dba, eZDbSchema, ezsqldumpschema, ezsqlinsertschema, ezsqldiff, ezimportdbafile, packages, ezpkg, upgrade scripts, Doctrine migrations, Laravel migrations, Django migrations, web framework developer
type: help
---
# Migrations and seed data: .dba schema files and packages

This page is for developers who run `doctrine:migrations:migrate`, `php artisan migrate`, `manage.py migrate` or
`rails db:migrate`. Exponential has **no migration runner and no migrations table**. Tables are described in
database-neutral `.dba` files, created by commands, and changed by upgrade steps that you write and ship. Content
classes and content are moved with **packages**. Say this early in a project; it changes how a release is planned.

## Contents

1. [The pieces](#the-pieces)
2. [The .dba format](#the-dba-format)
3. [Creating and installing a schema](#creating-and-installing-a-schema)
4. [Upgrades: your own migration steps](#upgrades-your-own-migration-steps)
5. [Seed data and content](#seed-data-and-content)
6. [Compared with migration tools](#compared-with-migration-tools)

## The pieces

| Need | Exponential |
|---|---|
| Describe tables for every database | `extension/<ext>/share/db_schema.dba` |
| Seed rows | `extension/<ext>/share/db_data.dba` |
| Create the tables | `ezsqlinsertschema.php`, or an install command of your extension |
| Compare schemas | `ezsqldiff.php` |
| Content classes and content | packages (`.ezpkg`), installed in **Setup > Packages** or with `ezpm.php` |
| Changes after the first release | upgrade scripts in your extension |

## The .dba format

A `.dba` file is a PHP array of tables, each with fields (type, length, default, not null), indexes and the primary
key. The kernel's own schema is `share/db_schema.dba` and its data `share/db_data.dba`. The class that reads and
writes them is `eZDbSchema` (`lib/ezdbschema/`), which generates the SQL for SQLite, PostgreSQL, MySQL/MariaDB and
Oracle from the same file ([Database schema and .dba files](../creators/app-creators/schema-and-dba-files.md)).

## Creating and installing a schema

Create the table on a development database, then dump only your tables:

```bash
php bin/php/ezsqldumpschema.php --type=sqlite --table-include=expdemo_item --output-array \
  var/storage/sqlite3/exponential.db extension/expdemo/share/db_schema.dba --allow-root-user
```

Install it on a database:

```bash
php bin/php/ezsqlinsertschema.php --type=mysql --user=<user> extension/expdemo/share/db_schema.dba <database>
```

Better: give your extension one idempotent command that creates missing tables, the way expqueue does with
`./console ext:expqueue:install-schema`. A script in `extension/expdemo/bin/php/install-schema.php` becomes
`./console ext:expdemo:install-schema` without registration ([Command line](cli-and-console-commands.md)).

## Upgrades: your own migration steps

1. Never change a released table in place without an upgrade step.
2. Ship upgrade steps as numbered scripts or SQL files per engine (for example `extension/<ext>/update/`), and let
   your install command apply the ones that are missing. Keep the record of what ran in a small table or setting of
   your own; the kernel does not keep one for you.
3. Add columns with defaults, so the old code keeps working during the deploy.
4. Write steps that only add where possible, so a code rollback still runs on the new schema.
5. Review data changes row by row, not by diff: a `.dba` is a PHP array, and removing one row renumbers the rest.

## Seed data and content

| Seed | Use |
|---|---|
| Rows of your own tables | `db_data.dba` next to the schema |
| Content classes your code depends on | a class package installed by your install command; keep identifiers stable ([Content packages](../creators/app-creators/packages-and-ezpkg.md)) |
| Demo or starter content | a content package, or `eZContentFunctions::createAndPublishObject()` with remote IDs, so running it twice updates instead of duplicating |
| Test fixtures | a throwaway database for integration tests ([Testing](testing.md)) |

```bash
php ezpm.php list --allow-root-user
php ezpm.php import var/tmp/<package>.ezpkg --allow-root-user
```

## Compared with migration tools

| Migration tools offer | Exponential |
|---|---|
| Generated migrations from model changes | no: dump the schema and write the upgrade step |
| `up()` and `down()` | upgrade steps only; rollback is a backup restore plus the previous code |
| A table that records what ran | yours to keep, if you need one |
| One command for everything | your extension's install command, run in the deployment |
| Content model changes in migrations | packages and class upgrade steps |

The deployment order that follows from this is on [Servers and deployment](servers-and-deployment.md) and
[A deployment pipeline](../creators/app-creators/deployment-pipeline.md).

## Related pages

- [Database schema and .dba files](../creators/app-creators/schema-and-dba-files.md)
- [Package wizard](../../features/6.0/package-wizard.md)
- [ezpm package manager CLI](../../features/6.0/ezpm-package-manager-cli.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
