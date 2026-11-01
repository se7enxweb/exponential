---
title: "Migration recipes"
audience: developer, software-engineer, php-developer, admin, integrator
category: migrations
order: 40
summary: Twenty-five task pages for migrations in Exponential 6.0.15, each with what you will have at the end, the steps and their output, the complete PHP migration, how to check it, how to take it back and the next step - from a new table to a deploy pipeline, and for teams coming from Doctrine Migrations, the Kaliop bundle or SQL update files.
keywords: migration recipes, cookbook, how to, migration, migrations, schema change, database change, add table, add column, add index, content class, class attribute, bulk content change, fix data, INI settings, roles, policies, seed content, environments, extension, package, down(), testing, code review, git, deploy, CI, GitHub Actions, large data, batches, engines, captions, rollback, audit, tags, upgrade step, Doctrine, Kaliop, SQL update files
type: help
---
# Migration recipes

Each recipe is one task, written the same way: **what you'll have at the end**, the **steps** with the commands and
their output, the complete **migration**, how to **check** it, how to **roll it back**, and the **next step**. They
assume Exponential 6.0.15 with the record tables (`./console exp:upgrade:status` shows `6.0.15-migration-tables` as
`done`) and a development installation in a git checkout. New to migrations? Do [the tutorial](../../../guides/migrations-tutorial.md)
first; it takes about 20 minutes.

## The database schema

| Recipe | You will have |
|---|---|
| [Add a database table](add-a-database-table.md) | a table of your own on every engine, created from a `.dba`-style definition |
| [Add a column or an index](add-a-column-or-an-index.md) | a new column and index on an existing table, written by each engine's schema handler |
| [Engine notes](engine-notes.md) | what differs between MySQL/MariaDB, PostgreSQL, SQLite, Oracle and MongoDB |

## Content classes and content

| Recipe | You will have |
|---|---|
| [Create or change a content class](create-or-change-a-content-class.md) | a content class with its attributes and class group, created or extended on every installation |
| [Add a class attribute](add-a-class-attribute.md) | a new attribute on an existing class, with the objects that exist initialized |
| [Change content in bulk](change-content-in-bulk.md) | many objects updated and published in one migration, found by class and subtree |
| [Fix data](fix-data.md) | a correction of wrong data, written so it is safe on every installation |
| [Seed content for a new site](seed-content-for-a-new-site.md) | the folders and pages a new site starts with, found again by remote id |
| [Large data migrations](large-data-migrations.md) | a long migration in batches that can be resumed after an interruption |

## Settings, roles and policies

| Recipe | You will have |
|---|---|
| [Move or rename INI settings](move-or-rename-ini-settings.md) | a setting moved in `settings/override`, with its old value kept and a way back |
| [Add roles and policies](add-roles-and-policies.md) | a role with its policies, assigned to a user group by remote id |

## Where migrations live and run

| Recipe | You will have |
|---|---|
| [Per-environment migrations](per-environment-migrations.md) | migrations limited to development, staging or production with the header line `environments:` |
| [Migrations in an extension or a package](migrations-in-an-extension-or-package.md) | migrations shipped in an extension or an installed package, ordered with the site's |
| [Upgrade step or migration?](upgrade-step-or-migration.md) | the right tool for each change, and a migration that requires a kernel upgrade step |

## Quality

| Recipe | You will have |
|---|---|
| [Writing a reversible down()](writing-a-reversible-down.md) | a `down()` that really undoes `up()`, and proof that it does |
| [Testing migrations](testing-migrations.md) | the lint in CI, a simulation with a diff, and a PHPUnit test on SQLite |
| [Reviewing a migration](reviewing-a-migration.md) | a checklist for the code review of a migration |

## Teams and delivery

| Recipe | You will have |
|---|---|
| [Team workflow with git](team-workflow-with-git.md) | branches, ordering, `requires:` and merge conflicts handled |
| [Deploying with migrations](deploying-with-migrations.md) | a pipeline (with a GitHub Actions example) that checks and applies migrations |
| [Pushing tags](pushing-tags.md) | every run committed, tagged and pushed, and failed pushes caught up |

## Safety and the record

| Recipe | You will have |
|---|---|
| [Captions and rollback in practice](captions-and-rollback-in-practice.md) | a failed run restored, a run and a migration taken back |
| [The audit trail of runs](the-audit-trail-of-runs.md) | the answer to "what ran where, when and by whom" |

## Coming from another tool

| Recipe | You will have |
|---|---|
| [Coming from Doctrine Migrations](coming-from-doctrine-migrations.md) | the Doctrine commands and classes mapped to Exponential's |
| [Coming from the Kaliop migration bundle](coming-from-kaliop-ezmigrationbundle.md) | Kaliop YAML steps written as PHP migrations |
| [Coming from SQL update files](coming-from-sql-update-files.md) | a hand-written update file turned into a recorded, engine-aware migration |

## YAML migrations

> **Coming with the YAML engine.** Recipes for migrations written as YAML steps will be listed here when the YAML
> engine lands. In 6.0.15 every recipe is written in PHP.

## Related pages

- [Migrations: the hub](../README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md),
  [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Frequently asked questions](../faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
