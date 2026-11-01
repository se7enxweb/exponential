---
title: "Per-environment migrations: development, staging and production"
audience: developer, software-engineer, admin
category: migrations
order: 10
summary: Limit a migration to some environments with the header line environments, set the environment of each installation, and follow one change from development through staging to production.
keywords: migration, migrations, environment, environments, development, test, staging, production, EXP_ENV, AdminEnvironment, Level, not-needed, schema change, database change, deploy, exp:migration:status, exp:migration:migrate, --environment
type: help
---
# Per-environment migrations: development, staging and production

Most migrations belong on every installation of a site: the table, the class or the role must be the same on a
developer's laptop, on staging and on production. Some do not: sample content that makes a development copy usable,
a test account for the staging team, a cleanup that only production needs. This recipe limits migrations to
environments and walks one change through all three.

## What you'll have at the end

- every installation of the site naming its own environment;
- a migration with sample data that runs on development and test only, and is `not-needed` elsewhere;
- a status file per environment in the migrations repository, so you can see what each one has applied.

## Before you start

- Exponential 6.0.15 with the record tables (`./console exp:upgrade:status` shows `6.0.15-migration-tables` as
  `done`).
- The table `exp_news_note` of the [table recipe](add-a-database-table.md) (migration
  `20261101090000_news_note_table`).

## Steps

### 1. Give every installation its environment

The environment is `site.ini [AdminEnvironment] Level`: `development`, `test`, `staging` or `production`. It is also
the environment badge of the administration. Set it in each installation's override:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Level=staging
*/ ?>
```

With `Level=auto` the environment comes from the variable `EXP_ENV` (`dev` and `development`, `test`, `stage` and
`staging`, `prod`, `production` and `live`), which suits containers and hosts that already set it. An installation
with an empty `Level` has no environment: migrations limited to environments do not run there, and its status file is
`status/default.json`.

### 2. Write a migration for some environments only

```bash
./console exp:migration:generate --skeleton sample_news_notes --description="Sample news notes for development and test installations" --apply
```

Add the header line `environments:` and fill in the class:

```php
<?php
// description: Sample news notes for development and test installations
// requires: 20261101090000_news_note_table
// environments: development, test
// author: Site team
namespace Site\Migrations;

final class SampleNewsNotes20261101093000 extends \expMigrationPhp
{
    const FIRST_ID = 9001;
    const COUNT = 3;

    public function plan( $context )
    {
        return array( 'write ' . self::COUNT . ' sample notes (ids ' . self::FIRST_ID . ' and up) into exp_news_note' );
    }

    public function up( $context )
    {
        $db = $context->db();
        for ( $i = 0; $i < self::COUNT; $i++ )
        {
            $id = self::FIRST_ID + $i;
            $db->query( "INSERT INTO exp_news_note ( id, title ) VALUES ( $id, 'Sample note $id' )" );
        }
        return array( 'wrote ' . self::COUNT . ' sample notes' );
    }

    public function down( $context )
    {
        $context->db()->query( 'DELETE FROM exp_news_note WHERE id >= ' . self::FIRST_ID
                               . ' AND id < ' . ( self::FIRST_ID + self::COUNT ) );
        return array( 'removed the sample notes' );
    }
}
```

The opposite form leaves environments out: `environments: !production` runs everywhere but on production. Use either
names or `!names` in one header, not both.

### 3. See the difference per environment

On a development installation:

```text
Exponential migrations: status
Run 20261101-093512-a41c07, environment development, database sqlite (transactions: full)

  done        20261101090000_news_note_table               site               A table for news notes, with a first note
  pending     20261101093000_sample_news_notes             site               Sample news notes for development and test installations

Summary: 2 migration(s): 1 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

On staging, the same files:

```text
Exponential migrations: status
Run 20261101-101500-3f9a1c, environment staging, database mysql (transactions: data)

  done        20261101090000_news_note_table               site               A table for news notes, with a first note
  not-needed  20261101093000_sample_news_notes             site               Sample news notes for development and test installations

Summary: 2 migration(s): 1 done, 0 pending, 1 not needed, 0 failed, 0 unknown
PASS
```

`--environment=<name>` asks as another environment for one command, for example to see on staging what production
would run: `./console exp:migration:status --environment=production`. It does not change the installation.

### 4. Walk a change through the environments

1. A developer writes and runs the migration on development (`exp:migration:migrate --apply`), commits the file and
   opens a pull request; CI runs `./console exp:migration:check`, which checks every environment's order.
2. After the merge, staging pulls the code and runs `./console exp:migration:migrate --apply`. The run commits
   `migrations/status/staging.json` and tags `migrations/staging/<run-id>`.
3. Production does the same; its run tags `migrations/production/<run-id>`.

Each environment pushes its own status file, so a clone of the repository shows what each one applied:

```bash
git show origin/main:migrations/status/staging.json | head -20
git tag -l 'migrations/production/*' --sort=version:refname | tail -3
```

## Check it

- `./console exp:migration:status` on each installation names the environment on its second line.
- A migration that must not run somewhere is `not-needed` there, never `pending`.

## Roll it back

The sample data is reversible: `./console exp:migration:rollback 20261101093000_sample_news_notes --apply` on a
development installation runs `down()`. On staging and production there is nothing to roll back.

## Next step

Ship migrations with an extension: [Migrations in an extension or package](migrations-in-an-extension-or-package.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Deploying with migrations](deploying-with-migrations.md), [The migrations repository](../../../guides/migrations-repositories.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
