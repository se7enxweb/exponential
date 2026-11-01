---
title: "Coming from SQL update files"
audience: developer, software-engineer, dba, hosting, php-developer
category: migrations
order: 25
summary: For teams that ship database changes as hand-written SQL files and update scripts - how one change becomes one PHP migration that knows every engine, is recorded, captioned and committed, with a worked example of an ALTER TABLE and an UPDATE.
keywords: migration, migrations, schema change, database change, SQL update files, dbupdate, update/database, update/common/scripts, ALTER TABLE, hand-written SQL, db_schema.dba, upgrade check, concept map
type: help
---
# Coming from SQL update files

Before 6.0.15, a change to a site's database was a SQL file run by hand: one file per engine in
`update/database/<driver>/`, a PHP script in `update/common/scripts/` for what SQL could not do, and a note in the
upgrade guide that said which to run. Nothing recorded which statements had run. The kernel still ships such files
for installations upgraded by hand (`update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql` and its PostgreSQL and
SQLite siblings), but its own changes are upgrade steps now, and a site's own changes are migrations. This page turns
a hand-written update into a migration.

## What you'll have at the end

- one PHP migration in place of a SQL file per engine and a note;
- the same change recorded, captioned before it ran, committed and pushed;
- the `.dba` schema kept for new installations.

## Before you start

- The record tables exist: `./console exp:upgrade:status` shows `6.0.15-migration-tables` as `done`.
- Have the SQL file you would have written at hand.

## Concept map

| SQL update files | Migrations |
|---|---|
| `dbupdate-<from>-<to>.sql` per driver | one `YYYYMMDDHHMMSS_<name>.php`, engine-aware inside |
| "run this file once, whole, in order" in a guide | `./console exp:migration:migrate --apply` runs every pending migration in order, once |
| nobody knows which statements ran | `exp_migration` records each migration; `exp:migration:status` lists them |
| a statement fails halfway | the migration's transaction is rolled back where the engine has one, and the caption taken before the run is restored |
| a backup taken by hand, maybe | a caption before every run |
| `update/common/scripts/<x>.php` | the same PHP inside `up()` |
| the upgrade check compares tables with the schema | still true: keep `share/db_schema.dba` (or the extension's) up to date |

## Steps

### 1. The SQL you would have written

```sql
-- update/database/mysql/news/dbupdate-news-1.1.sql
ALTER TABLE exp_news_note ADD COLUMN archived INT NOT NULL DEFAULT 0;
UPDATE exp_news_note SET archived = 1 WHERE id < 100;
```

plus a PostgreSQL file and a SQLite file with the same statements in their dialects.

### 2. Generate the migration

```bash
./console exp:migration:generate --skeleton news_note_archived --description="The column archived of exp_news_note, set on the old notes" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261101160000_news_note_archived.php
Migration 20261101160000_news_note_archived, class \Site\Migrations\NewsNoteArchived20261101160000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

### 3. Fill it in

Replace the generated class with the file below.

## The migration

```php
<?php
// description: The column archived of exp_news_note, set on the old notes
// engines: mysql, postgresql, sqlite, oracle
// author: Site team
namespace Site\Migrations;

final class NewsNoteArchived20261101160000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_note';

    public function plan( $context )
    {
        return array( 'add the column ' . self::TABLE . '.archived', 'mark the notes below id 100 as archived' );
    }

    public function up( $context )
    {
        $db = $context->db();
        $done = array();
        if ( \expUpgradeCatalog::hasColumn( $db, self::TABLE, 'archived' ) !== true )
        {
            $type = $context->engine() === 'oracle' ? 'NUMBER(11)' : 'INTEGER';
            \expUpgradeCatalog::execute( $db, 'ALTER TABLE ' . self::TABLE . " ADD archived $type DEFAULT 0 NOT NULL;\n" );
            \expKernelTableSchema::reset();
            $done[] = 'added the column ' . self::TABLE . '.archived';
        }
        $db->query( 'UPDATE ' . self::TABLE . ' SET archived = 1 WHERE id < 100' );
        $done[] = 'marked the notes below id 100 as archived';
        return $done;
    }

    public function down( $context )
    {
        \expUpgradeCatalog::execute( $context->db(), 'ALTER TABLE ' . self::TABLE . " DROP COLUMN archived;\n" );
        \expKernelTableSchema::reset();
        return array( 'dropped the column ' . self::TABLE . '.archived' );
    }
}
```

The column is added only when it is missing, so an installation where someone already ran the old SQL file by hand
is completed instead of failing. Where the engines need more than a type name, let the schema handler write the SQL
([Migrations on MySQL, PostgreSQL, SQLite, Oracle and MongoDB](engine-notes.md)).

## After the migration

### 4. Keep the schema file

Add the column to the table's definition in `share/db_schema.dba` of the site's extension too, so a new installation
gets it and the upgrade check (Setup > System upgrade, **Check database consistency**) does not report it.

### 5. Check, look, run

```bash
./console exp:migration:check
./console exp:migration:migrate
./console exp:migration:migrate --apply
```

```text
  applied     20261101160000_news_note_archived             site               The column archived of exp_news_note, set on the old notes
              added the column exp_news_note.archived
              marked the notes below id 100 as archived

Captions: pre 20261101-160105, post 20261101-160106
Repository: commit 9a8b7c6d5e4f, tag migrations/staging/20261101-160105-1f2e3d, push pushed
```

## Check it

`./console exp:migration:status` shows the migration `done` on every installation it reached. Remove the SQL files
from the repository once no installation will need them; keep the history in git.

## Roll it back

`./console exp:migration:rollback 20261101160000_news_note_archived --apply` runs `down()`. Note that `down()` drops
the column and with it the values set by the `UPDATE`.

## Next step

[Upgrade step or migration?](upgrade-step-or-migration.md): an extension's own schema for every installation is an
upgrade step, not a migration.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Add a column or an index](add-a-column-or-an-index.md), [Migrations on every engine](engine-notes.md)
- [Upgrades and SQL update files](../../database-administrators/upgrades-and-sql-update-files.md),
  [Schema changes and migrations](../../software-engineers/schema-changes-and-migrations.md),
  [The upgrade check](../../../guides/upgrade-check.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
