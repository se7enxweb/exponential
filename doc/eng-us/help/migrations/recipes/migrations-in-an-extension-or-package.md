---
title: "Migrations in an extension or package"
audience: developer, software-engineer, php-developer, integrator
category: migrations
order: 11
summary: Ship migrations inside an extension (extension/<name>/migrations/) or an installed package, generate them with --source=extension:<name>, and make a site's migrations wait for them with requires.
keywords: migration, migrations, extension, package, extension:<name>, package:<name>, ExtensionDirectory, requires, source, namespace, schema change, database change, deploy, exp:migration:generate, --source
type: help
---
# Migrations in an extension or package

An extension that needs a table, a class or a role on the sites that use it can bring the change along as
migrations. They are found while the extension is active and run in order with the site's own migrations, under the
same lock, with the same captions and the same record. This recipe adds a migration to the extension `expnews`,
makes a site migration wait for it, and shows where a package's migrations go.

## What you'll have at the end

- `extension/expnews/migrations/20261101100000_news_source_table.php`, listed with the source `extension:expnews`;
- a site migration that names it in `requires:` and therefore always runs after it;
- the rules for packages, whose migrations are found once the package is installed.

## Before you start

- The extension is active (`site.ini [ExtensionSettings] ActiveExtensions[]`). An inactive extension's migrations
  are not found, and a requirement `extension:<name>` fails.
- Decide whether the change is a migration at all: what every installation of the extension needs on every upgrade
  is an [upgrade step](../../../guides/upgrade-steps-for-extensions.md); see
  [Upgrade step or migration?](upgrade-step-or-migration.md).

## Steps

### 1. Generate the migration in the extension

```bash
./console exp:migration:generate --skeleton news_source_table --source=extension:expnews --description="expnews: the table of news sources" --apply
```

```text
Exponential migrations: generate
Wrote extension/expnews/migrations/20261101100000_news_source_table.php
Migration 20261101100000_news_source_table, class \Extension\Expnews\Migrations\NewsSourceTable20261101100000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

The folder is `extension/<name>/` plus `migration.ini [MigrationSettings] ExtensionDirectory` (`migrations`). The
namespace `Extension\Expnews\Migrations` keeps the class apart from every site's `Site\Migrations`.

### 2. Fill it in

```php
<?php
// description: expnews: the table of news sources
// requires: 6.0.15-migration-tables
// author: expnews maintainers
namespace Extension\Expnews\Migrations;

final class NewsSourceTable20261101100000 extends \expMigrationPhp
{
    const TABLE = 'expnews_source';

    /** @return array the table in the format of share/db_schema.dba */
    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'identifier' => array( 'length' => 64, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'url' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
            ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
    }

    public function plan( $context )
    {
        return array( 'create the table ' . self::TABLE );
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

`requires: 6.0.15-migration-tables` names a kernel upgrade step: kernel steps are migrations of the source
`kernel`, so an extension's migration can wait for one. Name its tables with the extension's prefix (`expnews_`).

### 3. Make a site migration wait for it

A site migration that writes into `expnews_source` names the extension's migration by id, or the whole extension:

```php
// requires: 20261101100000_news_source_table
// requires: extension:expnews
```

The first form orders the two migrations; the second checks that `expnews` is active and fails the run otherwise.

### 4. Look at the status

```text
Exponential migrations: status
Run 20261101-100512-7be210, environment development, database sqlite (transactions: full)

  done        6.0.15-migration-tables                      kernel             The migration record (tables exp_migration and exp_migration_run), with the rows of expupgrade_step copied over
  pending     20261101100000_news_source_table             extension:expnews  expnews: the table of news sources
  pending     20261101101000_news_default_sources          site               The default news sources

Summary: 3 migration(s): 1 done, 2 pending, 0 not needed, 0 failed, 0 unknown; 2 would-apply
PASS
```

## Packages

An installed package's migrations are in `var/<storage>/packages/<repository>/<package>/migrations/` and have the
source `package:<name>`. `exp:migration:generate` writes for `site` and `extension:<name>` only: write the file in
the site or extension, then copy it into the package's `migrations/` folder before you export the package. The
6.0.15 installer does not run a package's migrations by itself; after a package is installed, the next
`./console exp:migration:migrate --apply` runs them.

## Ids are unique across sources

An id may exist once. When the same id is found in the site, an extension and a package, the first found counts
(site, then extensions, then packages) and the duplicate is an error that stops every run until one file is renamed.
Generated ids carry the time down to the second, so a clash is rare; rename only a file that has run nowhere.

## Check it

```bash
./console exp:migration:check --path=extension/expnews/migrations
```

Ship the migrations in the extension's repository and in its `-ezpackage` repository alike, so both kinds of
installation get them.

## Roll it back

`./console exp:migration:rollback 20261101100000_news_source_table --apply` runs the extension migration's `down()`.
When two sources have the same id, add `--source=extension:expnews`. A site migration that requires it must be
rolled back first.

## Next step

Make every `down()` trustworthy: [Writing a reversible down()](writing-a-reversible-down.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Upgrade steps for extensions](../../../guides/upgrade-steps-for-extensions.md), [Packages](../../../guides/packages.md),
  [migration.ini \[MigrationSettings\]](../../../examples/ini-settings/migration/MigrationSettings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
