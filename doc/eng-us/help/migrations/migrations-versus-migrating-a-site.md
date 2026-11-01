---
title: "Migrations versus migrating a site"
audience: developer, admin, software-engineer, hosting, dba, adopter, integrator, trainer, project-manager
category: migrations
order: 2
summary: The word migration means two different jobs in the Exponential documentation - migrations (schema and content changes of one site, written as files and applied with exp:migration:migrate) and migrating a site (moving or upgrading a whole installation, or bringing content in from another system). Which one you need, and where each is documented.
keywords: migration, migrations, migrating, migrate a site, move a site, moving a site, upgrade, upgrading, site migration, content migration, schema change, database change, exp:migration, exp:upgrade, import, another host, another system, disambiguation, which migration
featured: true
type: help
---
# Migrations versus migrating a site

The documentation uses the word *migration* for two different jobs. This page tells them apart and sends you to the
right pages.

| | **Migrations** (schema and content changes) | **Migrating a site** (moving or upgrading an installation) |
|---|---|---|
| What changes | one site's database, content classes, content, roles or settings, a little at a time | where a whole installation runs, which release it runs, or which system its content comes from |
| How often | many times, with every change the site's team makes | once per move, per upgrade or per import |
| Written as | a PHP file `YYYYMMDDHHMMSS_name.php` in `migrations/`, under version control | a plan, a checklist, and tools such as dumps, exports and the upgrade |
| Run with | `./console exp:migration:migrate --apply`, or Setup > Migrations | `./console exp:upgrade`, database dumps, package export and import, import tools |
| Recorded in | the tables `exp_migration` and `exp_migration_run`, and a status file in git | the project's own notes; `exp:upgrade` records its steps |
| Applied to | every installation of the site: development, staging, production | the one installation being moved, upgraded or filled |
| Start here | [Migrations](README.md) | [Migrating to and from Exponential](../../install/migration/README.md) |

## Which one do you need?

| You want to ... | It is | Read |
|---|---|---|
| add a table, a column or an index for your site | a migration | [Add a database table](recipes/add-a-database-table.md) |
| add a content class or an attribute, on every installation | a migration | [Create or change a content class](recipes/create-or-change-a-content-class.md) |
| change or fix content on every installation the same way | a migration | [Change content in bulk](recipes/change-content-in-bulk.md), [Fix data](recipes/fix-data.md) |
| give a role new policies on staging and production | a migration | [Add roles and policies](recipes/add-roles-and-policies.md) |
| bring an installation to a new Exponential release | migrating (an upgrade) | [Upgrading](../../guides/upgrading.md), [Upgrading with the console](../../guides/upgrading-with-the-console.md) |
| move a site to another server or hosting company | migrating a site | [Moving a site between hosts](../hosting/migrations/moving-a-site-between-hosts.md) |
| move a site to another database engine | migrating a site | [Moving a site to another engine](../database-administrators/moving-between-engines.md), [Changing a customer site's database engine](../hosting/migrations/changing-the-database-engine.md) |
| bring a site over from WordPress, Drupal or another system | migrating a site | [Migrating to and from Exponential](../../install/migration/README.md), [From another system](../adopters/migrating/from-another-system.md) |
| bring a site over from eZ Platform or Ibexa | migrating a site | [From eZ Platform or Ibexa](../adopters/migrating/from-ez-platform-or-ibexa.md) |
| upgrade from an older Exponential release | migrating a site | [From an older Exponential release](../adopters/migrating/from-an-older-exponential-release.md) |
| move layouts, settings and templates between sites | neither: the Layouts examples | [Migrate examples](../build-two-ways/examples/migrate/README.md) |

## Where the two meet

- **After an upgrade, migrations take over.** `exp:upgrade` brings an installation to a new release with the kernel's
  upgrade steps. Those steps are also listed among the migrations (as migrations of the source `kernel`), and a
  migration run applies the pending ones of the stages `database` and `scripts` before the site's own migrations
  ([The steps among the migrations](../../guides/upgrading-with-the-console.md#the-steps-among-the-migrations)). A
  site migration that needs a step's table names it in `requires:`.
- **After a move or an import, migrations keep the copies alike.** Once a migrated site runs on development, staging
  and production, every further change (a new class, a role, a fix) is a migration, so all three stay the same
  ([Per-environment migrations](recipes/per-environment-migrations.md)).
- **A move of the database engine is not a migration.** Migrations change a site on the engine it runs on; moving
  between engines is a dump and a load ([Moving a site to another engine](../database-administrators/moving-between-engines.md)).
  The migrations themselves keep working on the new engine when they use `$context->db()` and the schema handlers
  ([Engine notes](recipes/engine-notes.md)).
- **Captions serve both.** A migration run and `exp:upgrade` take the same kind of caption (a safe point) and share
  the installation lock, so an upgrade and a migration run never write at the same time
  ([Captions and rollback in practice](recipes/captions-and-rollback-in-practice.md)).

## The pages of each family

**Migrations** (this section of the help):
[Migrations](README.md) · [the guide](../../guides/migrations.md) · [the tutorial](../../guides/migrations-tutorial.md) ·
[the recipes](recipes/README.md) · [In the administration](in-the-administration.md) · [FAQ](faq.md)

**Migrating a site:**

- [Migrating to and from Exponential](../../install/migration/README.md): the hub, with a guide per source system
- [Migrating to Exponential](../adopters/migrating/README.md): for organizations adopting Exponential
- [Migrating customers: overview](../hosting/migrations/README.md): for hosting companies
- [Migrating to Exponential: course overview](../trainers/migration/README.md): the trainers' course
- [Upgrading](../../guides/upgrading.md) and [Upgrading with the console](../../guides/upgrading-with-the-console.md)

## Related pages

- [Migrations: the hub](README.md), [All recipes](recipes/README.md), [Frequently asked questions](faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](in-the-administration.md), [Commands](commands.md), [Settings](settings.md),
  [Policies, views and events](policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [When a migration fails](when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Upgrade step or migration?](recipes/upgrade-step-or-migration.md)
- [Glossary](../../glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
