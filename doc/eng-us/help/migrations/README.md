---
title: "Migrations"
audience: developer, admin, software-engineer, php-developer, hosting, dba, integrator, dxp-developer, trainer
category: migrations
order: 1
summary: The start of everything about migrations in Exponential 6.0.15 - schema and content changes written once as files under version control and applied to every installation of a site in the same order, with a caption before each run, an automatic restore after a failure and a record in git. Paths for beginners, administrators and teams, 25 recipes, the reference and help when something fails.
keywords: migration, migrations, schema change, database change, content change, data migration, database migration, deploy, deployment, exp:migration, exp:migration:migrate, exp:migration:status, exp:migration:generate, migrate, rollback, caption, upgrade step, Doctrine, Doctrine Migrations, Kaliop, kaliop, ezmigrationbundle, SQL update file, dbupdate, Setup > Migrations, migration.ini, recipes, cookbook, how to
featured: true
type: help
---
# Migrations

A **migration** is a change to an installation (a table, a column, a content class, content, a role, a setting)
written once in a PHP file under version control and applied to every installation of the site (development, staging,
production) in the same order. Exponential 6.0.15 runs migrations in the kernel: it records what ran where, takes a
**caption** (a safe point) before every run, restores it automatically when a migration fails, and commits, tags and
pushes the record of every run.

Looking for something else? Moving a site to another server, upgrading it to a new release or bringing content in
from another system is **migrating a site**, a different job with its own pages:
[Migrations versus migrating a site](migrations-versus-migrating-a-site.md).

## Contents

1. [Start here](#start-here)
2. [Who it is for](#who-it-is-for)
3. [Recipes](#recipes)
4. [In the administration](#in-the-administration)
5. [Reference](#reference)
6. [When something goes wrong](#when-something-goes-wrong)
7. [YAML migrations](#yaml-migrations)
8. [What 6.0.15 has, and what comes later](#what-6015-has-and-what-comes-later)
9. [Related pages](#related-pages)

## Start here

| You want to ... | Start with | Then |
|---|---|---|
| write your first migration (about 20 minutes) | [the tutorial](../../guides/migrations-tutorial.md) | [PHP migrations](../../guides/migrations-php.md), [the recipes](recipes/README.md) |
| understand what migrations are and how a run works | [the guide](../../guides/migrations.md) | [Rollback and captions](../../guides/migrations-rollback-and-captions.md), [the migrations repository](../../guides/migrations-repositories.md) |
| apply migrations other people wrote | [Migrations for administrators](migrations-for-administrators.md) | [In the administration](in-the-administration.md), [Run, dry run and rollback](run-dry-run-and-rollback.md) |
| find your way around Setup > Migrations | [A tour of Setup > Migrations](a-tour-of-setup-migrations.md) | [The status and the history](status-and-history.md) |
| turn changes made by hand into a migration | [Recording changes](recording.md) | [Generating migrations](generating-migrations.md), [The migration editor](the-migration-editor.md) |
| put the migrations on staging and production | [Add a remote step by step](add-a-remote-step-by-step.md) | [Deploy step by step](deploy-step-by-step.md) |
| ship changes as a team, through CI to production | [Team workflow with git](recipes/team-workflow-with-git.md) | [Testing migrations](recipes/testing-migrations.md), [Deploying with migrations](recipes/deploying-with-migrations.md) |
| move from Doctrine Migrations, the Kaliop bundle or SQL update files | [Coming from Doctrine Migrations](recipes/coming-from-doctrine-migrations.md) | [Kaliop](recipes/coming-from-kaliop-ezmigrationbundle.md), [SQL update files](recipes/coming-from-sql-update-files.md) |

The five commands of every day:

```bash
./console exp:migration:generate --skeleton add_news_table --apply   # a new PHP migration in migrations/
./console exp:migration:check                                         # the lint: names, headers, order, PHP (no database)
./console exp:migration:migrate                                       # dry run: what would change
./console exp:migration:migrate --apply                               # caption, run, re-caption, commit, tag, push
./console exp:migration:status                                        # what is done and what is pending
```

Run them from the installation root as the user the web server runs as (as root, add `--allow-root-user`). Every
command that writes is a dry run until it is given `--apply`.

## Who it is for

| You are ... | Your page |
|---|---|
| a software engineer who designs how changes reach production | [Migrations for software engineers](../software-engineers/migrations-for-software-engineers.md) |
| a PHP developer who writes the migration classes | [Writing migrations in PHP](../php-developers/writing-migrations-in-php.md) |
| an administrator who runs them on staging and production | [Migrations for administrators](migrations-for-administrators.md) |
| a hosting company or a database administrator | [Migrations on hosted sites](../hosting/operations/migrations-on-hosted-sites.md) |
| an agency or integrator with many client sites | [Migrations in client projects](../integrators/migrations-in-client-projects.md) |
| a trainer | [Lesson 9.5: Migrations](../trainers/developers/module-09-the-console-and-persistent-objects/migrations.md), with an exercise |
| a DXP developer who knows Doctrine Migrations | [Exponential migrations for Doctrine users](../dxp-developers/exponential-migrations-for-doctrine-users.md) |

## Recipes

Each recipe is one task: what you will have at the end, the steps with their output, the complete migration, how to
check it, how to take it back, and the next step. [All recipes](recipes/README.md).

**The database schema**

- [Add a database table](recipes/add-a-database-table.md)
- [Add a column or an index](recipes/add-a-column-or-an-index.md)
- [Engine notes: MySQL, PostgreSQL, SQLite, Oracle and MongoDB](recipes/engine-notes.md)

**Content classes and content**

- [Create or change a content class](recipes/create-or-change-a-content-class.md)
- [Add a class attribute](recipes/add-a-class-attribute.md)
- [Change content in bulk](recipes/change-content-in-bulk.md)
- [Fix data](recipes/fix-data.md)
- [Seed content for a new site](recipes/seed-content-for-a-new-site.md)
- [Large data migrations](recipes/large-data-migrations.md)

**Settings, roles and policies**

- [Move or rename INI settings](recipes/move-or-rename-ini-settings.md)
- [Add roles and policies](recipes/add-roles-and-policies.md)

**Where migrations live and run**

- [Per-environment migrations](recipes/per-environment-migrations.md)
- [Migrations in an extension or a package](recipes/migrations-in-an-extension-or-package.md)
- [Upgrade step or migration?](recipes/upgrade-step-or-migration.md)

**Quality**

- [Writing a reversible down()](recipes/writing-a-reversible-down.md)
- [Testing migrations](recipes/testing-migrations.md)
- [Reviewing a migration](recipes/reviewing-a-migration.md)

**Teams and delivery**

- [Team workflow with git](recipes/team-workflow-with-git.md)
- [Deploying with migrations](recipes/deploying-with-migrations.md)
- [Pushing tags](recipes/pushing-tags.md)

**Safety and the record**

- [Captions and rollback in practice](recipes/captions-and-rollback-in-practice.md)
- [The audit trail of runs](recipes/the-audit-trail-of-runs.md)

**Coming from another tool**

- [Coming from Doctrine Migrations](recipes/coming-from-doctrine-migrations.md)
- [Coming from the Kaliop migration bundle](recipes/coming-from-kaliop-ezmigrationbundle.md)
- [Coming from SQL update files](recipes/coming-from-sql-update-files.md)

## In the administration

**Setup > Migrations** shows every migration with its state, runs the pending ones in the background, simulates a
run and takes a run or a migration back, records changes, writes migrations in its editor, and deploys them to other
installations. The dashboard has a **Migrations** block, and the website sidebar a panel. Every page below has
step-by-step instructions with screenshots.

- [A tour of Setup > Migrations](a-tour-of-setup-migrations.md): every tab with a screenshot, and which page for which task
- [Migrations in the administration](in-the-administration.md): the pages and who may use them
- [The status and the history](status-and-history.md)
- [Run, dry run and rollback](run-dry-run-and-rollback.md)
- [Migrations in the website sidebar](expwt-panel.md)
- [Recording changes](recording.md): record your own changes, or every change in a site-wide window, as the steps of a migration
- [Remotes and deploys](remotes-and-deploys.md): the other installations this one deploys its migrations to, deploys all at once or stage by stage with a gate, live per remote, history and rollback
- [Add a remote step by step](add-a-remote-step-by-step.md): the five steps of the wizard, Test connection, edit and remove, FTP hosts with the trigger, self-healing
- [Deploy step by step](deploy-step-by-step.md): choose remotes, preflight, deploy, follow it live, approve a gate, roll back, the history and the notice mails
- [The migration editor](the-migration-editor.md): build a YAML migration with forms, check, preview and save it
- [Generating migrations](generating-migrations.md): from a recording, in the editor, from what is there, from a difference, skeletons

## Reference

| Page | What it lists |
|---|---|
| [The guide](../../guides/migrations.md) | the whole model: life cycle, files, order, header, environments, record, transactions, lock, every switch |
| [PHP migrations](../../guides/migrations-php.md) | the class, its methods, the context, transactions, failure, events, testing |
| [The migration file format](../../specifications/6.0/migration-file-format.md) | file names, the header, the order, the checksum, the record tables, the status file, the run log, the machine output |
| [Commands](commands.md) | every `exp:migration:*` command, with a link to its generated page |
| [Settings](settings.md) | every block of `migration.ini`, with a link to its generated page |
| [Policies, views and events](policies-views-and-events.md) | the policies, the views of the module `migration`, the events and the audit events |
| [The feature](../../features/6.0/migrations.md) | what 6.0.15 adds, the classes, the engines, the limits |
| [Upgrading with the console](../../guides/upgrading-with-the-console.md) | `exp:upgrade`, whose steps are listed and run among the migrations |

## When something goes wrong

- [When a migration fails](when-a-migration-fails.md): troubleshooting by symptom
- [Troubleshooting messages](../../guides/migrations-troubleshooting.md): every message the commands print
- [Frequently asked questions](faq.md)
- [Rollback and captions](../../guides/migrations-rollback-and-captions.md)

## YAML migrations

A migration can be a list of YAML steps instead of a PHP class: content, locations and versions, classes and class
groups, users, user groups and roles, sections, languages and object states. The engine, its placeholders,
conditions and loops: [the file format](../../specifications/6.0/migration-file-format.md#the-yaml-migration); the
step types: [YAML migration steps](../../guides/migrations-yaml-steps.md) and
[every step type](../../examples/migration-steps/README.md). The recipes here are written in PHP; each of them can be
written as YAML steps too where a step type covers it.

## What 6.0.15 has, and what comes later

| In 6.0.15 | Not yet |
|---|---|
| PHP, YAML, SQL and INI migrations in the site, in active extensions and in installed packages | |
| the dry run, and the simulation with a diff on PostgreSQL and SQLite | a simulation on MySQL/MariaDB, Oracle and MongoDB (the plan is listed) |
| captions, the automatic restore, rollback of a run, of a migration and of any caption | |
| generators from a recording, from content, classes, roles, sections and settings, from a difference, and skeletons; the editor in the administration | |
| commit, tag and push of every run's record; the cronjob part `migrationpush` | `exp:velocity deploy --migrate` |
| Setup > Migrations, background runs, recording, the editor, remotes and deploys, the dashboard block and the sidebar panel | |
| the kernel's upgrade steps listed and run among the migrations (stages `database` and `scripts`) | the stages `code`, `libraries` and `finish` stay with `exp:upgrade` |

## Related pages

- [Migrations: the guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md)
- [All recipes](recipes/README.md), [Frequently asked questions](faq.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [Migrating to and from Exponential](../../install/migration/README.md) (moving a site from another system or release)
- [Upgrading with the console](../../guides/upgrading-with-the-console.md),
  [Upgrade steps for extensions](../../guides/upgrade-steps-for-extensions.md)
- [Schema changes and migrations](../software-engineers/schema-changes-and-migrations.md)
- [A tour of the administration](../admin-tour.md)
- [Glossary](../../glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
