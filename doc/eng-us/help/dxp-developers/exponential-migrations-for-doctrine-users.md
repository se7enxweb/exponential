---
title: "Exponential migrations for Doctrine users"
audience: dxp-developer, developer
category: migrations
order: 52
summary: For developers who know Doctrine Migrations from the DXP and now work on an Exponential 6 site - what is the same, what differs (content as well as schema, captions, commit, tag and push, environments, the administration), the commands side by side, the same table in both, and when each stack applies.
keywords: migration, migrations, schema change, database change, deploy, exp:migration:migrate, Doctrine, doctrine:migrations:migrate, doctrine:migration:migrate, AbstractMigration, addSql, Version, dxp developer, Symfony, expMigrationPhp
featured: true
type: help
---
# Exponential migrations for Doctrine users

This page is for developers who change the database of DXP projects with Doctrine Migrations
([Doctrine migrations](doctrine-migrations.md)) and now work on an Exponential 6 site, the kernel without Symfony.
Exponential 6.0.15 has migrations of its own. The idea is the one you know; the scope and the safety net are wider.

## What is the same

- One file per change, named by a timestamp, kept in git with the code that needs it.
- Applied in order, recorded per database, never applied twice; a migration that ran is never edited.
- A dry run before the real run, `up()` to apply, `down()` to take one back.
- One command in the deploy that applies what is pending.

## What differs

| Topic | Doctrine Migrations (DXP) | Exponential 6 migrations |
|---|---|---|
| What a migration changes | SQL of the schema and your own tables | anything: tables, content classes, content, roles, settings, through the kernel's APIs |
| The class | `final class Version20261101100000 extends AbstractMigration` | `final class NewsNote20261101100000 extends \expMigrationPhp` in `Site\Migrations` |
| Order | by version | by id, plus `requires:` in the header (other migrations, extensions, kernel upgrade steps) |
| The record | `doctrine_migration_versions` | `exp_migration` and `exp_migration_run`, mirrored to `migrations/status/<environment>.json` in git |
| Changed files | not noticed | refused by a checksum guard unless `--force --reason` accepts them |
| Before a run | your own backup | a caption (dump, storage, settings) taken automatically, reused while nothing changed |
| After a failure | the database as the failure left it (MySQL commits DDL) | the transaction rolled back where the engine can, then the caption restored |
| After a run | nothing | the status file and run log committed, tagged and pushed |
| Environments | `--env` selects the Symfony config | a header `environments: staging, production` limits where a migration runs |
| Schema diff | `doctrine:migrations:diff` generates from entities | no diff generator; `exp:migration:generate --skeleton` writes an empty class |
| Engines | MySQL, PostgreSQL, SQLite | MySQL/MariaDB, PostgreSQL, SQLite, Oracle, MongoDB |
| User interface | console only | console and Setup > Migrations in the administration |

## The commands side by side

| Doctrine (DXP) | Exponential 6 |
|---|---|
| `php bin/console doctrine:migrations:status` | `./console exp:migration:status` |
| `doctrine:migrations:migrate --dry-run` | `./console exp:migration:migrate` (a dry run until `--apply`) |
| `doctrine:migrations:migrate` | `./console exp:migration:migrate --apply` |
| `doctrine:migrations:generate` | `./console exp:migration:generate --skeleton <name> --apply` |
| `doctrine:migrations:execute --down <version>` | `./console exp:migration:rollback <migration-id> --apply` |
| (none) | `./console exp:migration:rollback <run-id> --apply`: the caption taken before a run |
| (none) | `./console exp:migration:dry-run --simulate --diff`: run and roll back, show what changed |
| (none) | `./console exp:migration:check`: the lint for CI, no database |

The DXP's own pages write the commands with the alias `doctrine:migration:*`; both spellings work there.

## The same table in both

Doctrine:

```php
final class Version20261101100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'A table for news notes';
    }

    public function up( Schema $schema ): void
    {
        $this->addSql( 'CREATE TABLE news_note (id INT NOT NULL, title VARCHAR(150) NOT NULL, PRIMARY KEY(id))' );
    }

    public function down( Schema $schema ): void
    {
        $this->addSql( 'DROP TABLE news_note' );
    }
}
```

Exponential 6: the table in the format of `share/db_schema.dba`, created on every engine by the kernel:

```php
<?php
// description: A table for news notes
namespace Site\Migrations;

final class NewsNote20261101100000 extends \expMigrationPhp
{
    const TABLE = 'news_note';

    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'title' => array( 'length' => 150, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
            ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
    }

    public function plan( $context )
    {
        return array( 'create the table ' . self::TABLE );
    }

    public function detect( $context )
    {
        return \expKernelTableSchema::exists( self::TABLE, $context->db(), true ) ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $messages = array();
        if ( !\expKernelTableSchema::install( array( self::TABLE ), $context->db(), self::schema(), $messages ) )
            throw new \RuntimeException( 'the table could not be created: ' . implode( '; ', $messages ) );
        return array( 'created ' . self::TABLE );
    }

    public function down( $context )
    {
        $context->db()->query( 'DROP TABLE ' . self::TABLE );
        \expKernelTableSchema::reset();
        return array( 'dropped ' . self::TABLE );
    }
}
```

The differences in the code: no SQL per engine (the schema handler writes it), `plan()` instead of a description
only, `detect()` so an installation that has the table already counts it as done, and every call goes through
`$context->db()`, the run's connection. Kernel code stays PHP 7.4 compatible: no return types are needed.

## When each stack applies

| You work on | Use |
|---|---|
| a DXP project (Symfony, the content repository) | Doctrine Migrations, as the DXP's skeleton does |
| an Exponential 6 site (the kernel, `./console`) | `exp:migration:*` |
| a project that runs both | each stack's own tool on its own database; never record one stack's change in the other's table |

[Choosing DXP, Legacy, Nexus or Exponential 6](choosing-dxp-legacy-nexus-or-exponential-6.md) explains the stacks.
Moving a site from one stack to the other is a site migration, not a migration file:
[Migrations versus migrating a site](../migrations/migrations-versus-migrating-a-site.md).

## Where to go next

- [Coming from Doctrine Migrations](../migrations/recipes/coming-from-doctrine-migrations.md): the recipe that
  translates a Doctrine migration step by step
- [Add a database table](../migrations/recipes/add-a-database-table.md),
  [add a column or an index](../migrations/recipes/add-a-column-or-an-index.md)
- [Deploying with migrations](../migrations/recipes/deploying-with-migrations.md)
- Start now: [the tutorial](../../guides/migrations-tutorial.md)

## Related pages

- [Migrations: the hub](../migrations/README.md)
- [Migrations: the guide](../../guides/migrations.md)
- [Migrations: the tutorial](../../guides/migrations-tutorial.md)
- [PHP migrations](../../guides/migrations-php.md)
- [Migrations in the administration](../migrations/in-the-administration.md)
- [The migration commands](../migrations/commands.md)
- [The migration settings](../migrations/settings.md)
- [The migration file format](../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../migrations/when-a-migration-fails.md)
- [Troubleshooting migrations](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations/migrations-versus-migrating-a-site.md)
- [DXP Developers: start here](dxp-developers-start-here.md)
- [Doctrine migrations](doctrine-migrations.md), [Coming from Exponential 6](coming-from-exponential-6.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
