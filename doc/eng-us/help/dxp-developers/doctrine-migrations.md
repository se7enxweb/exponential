---
title: Doctrine migrations
audience: dxp-developer
category: dxp-development
order: 36
summary: Database changes on a DXP project with doctrine/doctrine-migrations-bundle - running pending migrations on every update, previewing SQL, writing migrations for your own tables, and why the install command is not for updates.
keywords: Doctrine, migrations, doctrine:migration:migrate, dry-run, status, diff, schema validate, database changes, deploy, update
type: help
---
# Doctrine migrations

This page is for developers who change the database of a DXP project, or deploy changes that do. The DXP requires
`doctrine/doctrine-migrations-bundle`; the skeleton's guide runs migrations on every update and deploy.

## Contents

1. [Install versus migrate](#install-versus-migrate)
2. [The commands](#the-commands)
3. [Your own tables](#your-own-tables)
4. [In a deploy](#in-a-deploy)
5. [Rules](#rules)

## Install versus migrate

| Command | When |
|---|---|
| `exponential:install exponential-oss` | Once, on an empty database |
| `doctrine:migration:migrate --allow-no-migration` | Every update and deploy afterwards |

Never run the install command against a database with content to "update" it.

## The commands

From the guide's CLI reference (section 22.2):

```bash
php bin/console doctrine:migration:status                         # pending and applied
php bin/console doctrine:migration:migrate --dry-run              # preview the SQL
php bin/console doctrine:migration:migrate --allow-no-migration   # apply pending migrations
php bin/console doctrine:migration:diff                           # generate a migration from your entity changes
php bin/console doctrine:migration:execute --up <version>         # one migration up
php bin/console doctrine:migration:execute --down <version>       # roll one back
php bin/console doctrine:schema:validate                          # mapping versus database
```

## Your own tables

If your project stores its own data with Doctrine entities in `src/Entity/`, change the entity, generate a migration
with `doctrine:migration:diff`, read the generated SQL, and commit the migration with the code that needs it. Test it
on every database engine you support: MySQL, PostgreSQL and SQLite differ in types and in what `ALTER TABLE` can do.

Store content in the content repository (content types and fields), not in your own tables, unless it truly is not
content: then it gets the repository's versions, translations, permissions and APIs for free.

## In a deploy

```bash
php bin/console doctrine:migration:migrate --allow-no-migration --env=prod
```

Back up the database before, run migrations before the new code serves traffic, and clear the cache after
([Deploying](deploying-dxp.md)).

## Rules

- A migration that has run anywhere is never edited; write a new one.
- A migration must work on an empty database and on production data.
- `doctrine:database:drop --force` exists; never run it on a server you did not mean to wipe.

## Related pages

- [Updating](updating-dxp.md)
- [Deploying](deploying-dxp.md)
- [Databases](databases-mysql-postgresql-sqlite.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
