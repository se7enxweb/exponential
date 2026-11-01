---
title: "Coming from Doctrine Migrations"
audience: developer, dxp-developer, php-developer, software-engineer
category: migrations
order: 23
summary: For developers who know Doctrine Migrations from Symfony projects - every Doctrine command and class mapped to its Exponential 6 counterpart, what Exponential adds (captions, commit, tag and push, environments, requires, the checksum guard, the administration) and what it does not have.
keywords: migration, migrations, schema change, database change, Doctrine, Doctrine Migrations, doctrine:migrations:migrate, doctrine:migrations:generate, doctrine:migrations:status, AbstractMigration, addSql, Symfony, DXP, concept map
featured: true
type: help
---
# Coming from Doctrine Migrations

If you have written Doctrine Migrations in a Symfony project, Exponential 6 migrations will feel familiar: versioned
PHP classes, `up()` and `down()`, a table that records what ran, a dry run. The differences are in what happens
around a run. This page maps one to the other and rewrites a typical Doctrine migration.

The Exponential Platform DXP keeps Doctrine: on a DXP project you go on with `doctrine:migrations:migrate`
([Doctrine migrations](../../dxp-developers/doctrine-migrations.md)). This page is about Exponential 6 installations.

## What you'll have at the end

- the Exponential command and class for every Doctrine one you use;
- a Doctrine migration rewritten as an Exponential PHP migration;
- a list of what does not exist, so you do not look for it.

## Before you start

Work through [the tutorial](../../../guides/migrations-tutorial.md) once; it takes about 20 minutes.

## Concept map

| Doctrine Migrations | Exponential 6 |
|---|---|
| `doctrine:migrations:generate` | `./console exp:migration:generate --skeleton <name> --apply` |
| `migrations/VersionYYYYMMDDHHMMSS.php` | `migrations/YYYYMMDDHHMMSS_<name>.php`; the id is the file name |
| `final class Version... extends AbstractMigration` | a namespaced class extending `\expMigrationPhp` (`Site\Migrations\<Name><time>`) |
| `getDescription()` | the header line `// description: ...` |
| `up( Schema $schema )` with `$this->addSql()` | `up( $context )`: run the change through `$context->db()` and return what you did, one line each |
| `down( Schema $schema )` | `down( $context )`; leave it out when the change cannot be undone |
| `throwIrreversibleMigrationException()` | no `down()`: the rollback of that migration is refused and points to the caption |
| `isTransactional()` | `transactional()` |
| `skipIf()` / `abortIf()` | `detect()` returns `NOT_NEEDED` or `DONE`; throw an exception to abort |
| table `doctrine_migration_versions` | tables `exp_migration` (one row per migration) and `exp_migration_run` (one row per run) |
| `doctrine:migrations:status` | `./console exp:migration:status` |
| `doctrine:migrations:migrate --dry-run` | `./console exp:migration:migrate` (a dry run is the default) |
| `doctrine:migrations:migrate` | `./console exp:migration:migrate --apply` |
| `doctrine:migrations:execute <v> --up` | `./console exp:migration:migrate --only=<id> --apply` |
| `doctrine:migrations:execute <v> --down` | `./console exp:migration:rollback <id> --apply` |
| `doctrine:migrations:migrate prev` | `./console exp:migration:rollback <run-id> --apply` restores the caption taken before the run |
| `doctrine:migrations:version --add` / `--delete` | no equivalent; a migration is recorded by running it, and a changed file is accepted with `--force --reason=<text>` |
| `doctrine:migrations:diff` | no schema diff generator in 6.0.15; write the change, and see it with `exp:migration:dry-run --simulate --diff` |
| `doctrine:migrations:list` | `./console exp:migration:status` |
| dependencies by order only | `// requires: <id>` (also `extension:<name>` or an upgrade step id) |

## What Exponential adds around a run

- **Captions**: a caption (database, `var/` storage, `settings/override`) before every run, restored by default when a
  migration fails, and one after a run that passed.
- **Commit, tag and push**: the status file and run log of every run are committed, tagged
  `migrations/<environment>/<run-id>` and pushed.
- **Environments and engines**: `// environments: staging, production` and `// engines: mysql, postgresql` in the header.
- **The checksum guard**: a migration whose file changed after it ran is refused.
- **Lock and user**: one run at a time per installation (shared with `exp:upgrade`), run as a named user.
- **The administration**: Setup > Migrations shows, runs and rolls back migrations, with "Verify it's you".

## Steps

### 1. A Doctrine migration

```php
final class Version20261101090000 extends AbstractMigration
{
    public function getDescription(): string { return 'The column priority of news_note'; }
    public function up( Schema $schema ): void
    {
        $this->addSql( 'ALTER TABLE exp_news_note ADD priority INT DEFAULT 0 NOT NULL' );
        $this->addSql( 'UPDATE exp_news_note SET priority = 1 WHERE title LIKE \'Urgent%\'' );
    }
    public function down( Schema $schema ): void
    {
        $this->addSql( 'ALTER TABLE exp_news_note DROP COLUMN priority' );
    }
}
```

### 2. The same as an Exponential migration

```bash
./console exp:migration:generate --skeleton news_note_priority --description="The column priority of exp_news_note" --apply
```

```php
<?php
// description: The column priority of exp_news_note
// engines: mysql, postgresql, sqlite, oracle
namespace Site\Migrations;

final class NewsNotePriority20261101090000 extends \expMigrationPhp
{
    public function plan( $context )
    {
        return array( 'add the column exp_news_note.priority', 'set priority 1 on the urgent notes' );
    }

    public function up( $context )
    {
        $db = $context->db();
        if ( \expUpgradeCatalog::hasColumn( $db, 'exp_news_note', 'priority' ) !== true )
            \expUpgradeCatalog::execute( $db, "ALTER TABLE exp_news_note ADD COLUMN priority INTEGER DEFAULT 0 NOT NULL;\n" );
        $db->query( "UPDATE exp_news_note SET priority = 1 WHERE title LIKE 'Urgent%'" );
        return array( 'added the column exp_news_note.priority', 'set priority 1 on the urgent notes' );
    }

    public function down( $context )
    {
        \expUpgradeCatalog::execute( $context->db(), "ALTER TABLE exp_news_note DROP COLUMN priority;\n" );
        return array( 'dropped the column exp_news_note.priority' );
    }
}
```

`plan()` is what the dry run prints, in place of Doctrine's SQL listing. For SQL the schema handler writes per
engine, see [Migrations on MySQL, PostgreSQL, SQLite, Oracle and MongoDB](engine-notes.md).

### 3. Run it

```bash
./console exp:migration:check
./console exp:migration:migrate
./console exp:migration:migrate --apply
```

## Habits to unlearn

- Do not edit a migration after it ran anywhere; the checksum guard refuses it. Write a new one.
- Do not mark versions by hand; there is no `version --add`.
- Do not count on the order of ids alone when a migration needs another one; write `requires:`.

## Check it

`./console exp:migration:status` shows the migration `done`, and the history the run with its captions and commit.

## Next step

[Testing migrations](testing-migrations.md), then [Deploying with migrations](deploying-with-migrations.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Exponential migrations for Doctrine users](../../dxp-developers/exponential-migrations-for-doctrine-users.md),
  [Doctrine migrations in the DXP](../../dxp-developers/doctrine-migrations.md)
- [Coming from the Kaliop migration bundle](coming-from-kaliop-ezmigrationbundle.md),
  [Coming from SQL update files](coming-from-sql-update-files.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
