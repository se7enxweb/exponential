---
title: Database schema, .dba files and upgrades
audience: developer, app-creator
category: extension-development
order: 270
summary: Ship tables that install on every supported database: share/db_schema.dba, per-engine schema.sql, ezsqlinsertschema and ezsqldumpschema, idempotent install commands, upgrades.
keywords: database schema, db_schema.dba, db_data.dba, schema.sql, ezsqlinsertschema, ezsqldumpschema, eZDbSchema, sqlite, mysql, postgresql, oracle, mongodb, update scripts, upgrade, install-schema, idempotent
type: help
---
# Database schema, .dba files and upgrades

Part 3, chapter 27. Your persistent object needs its table on every installation, whatever database it runs:
Exponential supports SQLite, PostgreSQL, MongoDB, MariaDB, Oracle and MySQL ([Database support](../../database-support.md)).
This chapter shows how extensions ship tables and change them later.

## Two forms

| File | Form | Used by |
|---|---|---|
| `share/db_schema.dba` | the schema as a PHP array, engine neutral | the package installer, `ezsqlinsertschema`, the kickstarter |
| `share/db_data.dba` | rows to insert, as a PHP array | the same, for initial data |
| `sql/<engine>/schema.sql` | plain SQL per engine (`mysql`, `postgresql`, `sqlite`, `oracle` ...) | people and scripts that prefer SQL |

`eztags` ships both (`share/db_schema.dba` and `sql/mysql/schema.sql`, `sql/postgresql/schema.sql`); `expqueue` ships
`sql/<engine>/schema.sql` for `mongodb`, `mysql`, `oracle`, `postgresql` and `sqlite`.

## Writing the .dba

The start of `eztags`' `share/db_schema.dba`:

```php
<?php
// This array contains the database schema
$schema = array (
  'eztags' =>
  array (
    'name' => 'eztags',
    'fields' =>
    array (
      'id' =>
      array (
        'type' => 'auto_increment',
        'default' => false,
      ),
      'parent_id' =>
      array (
        'length' => 11,
        'type' => 'int',
        'not_null' => '1',
        'default' => 0,
      ),
```

Do not write it by hand. Create the table in a development database, then dump it:

```bash
php bin/php/ezsqldumpschema.php --type=mysql --user=<user> --output-array <database> extension/mysite_hello/share/db_schema.dba
php bin/php/ezsqldumpschema.php --type=mysql --user=<user> --output-sql  <database> extension/mysite_hello/sql/mysql/schema.sql
```

(Options from `./console exp:ezsqldumpschema --help`.) Dump only your tables: edit the array down to them, and keep
the table names prefixed with your extension's name.

## Installing it

```bash
php bin/php/ezsqlinsertschema.php --type=sqlite extension/mysite_hello/share/db_schema.dba <database>
```

`ezsqlinsertschema` turns the neutral schema into each engine's SQL (through `eZDbSchema`), so one file serves every
database. For a better experience, ship a command that does it for the installation's own database, checks first, and
can be run twice safely, as `expqueue` does: `./console ext:expqueue:install-schema` "Create or upgrade the tables of the
job queue (idempotent); `--check`, `--print=<engine>`, `--write-files`".

## Upgrades

When a later version adds a column:

1. Change the table in development, dump the `.dba` and the SQL files again.
2. Ship an upgrade: SQL per engine under `update/database/<engine>/<version>/`, as the kernel and `eztags` do, and/or
   make your install command add missing columns (idempotent).
3. Say in the changelog exactly what to run.

Never change a column in a way that loses data without a migration script, and never assume MySQL: SQLite, for
example, cannot drop columns the way MySQL does.

## Reviewing a .dba change

A `.dba` is a PHP array with numeric keys for rows, so removing one row renumbers all that follow, and a diff shows
thousands of changed lines for a small change. Compare the arrays row by row (load both with `include` in a small
script and compare by primary key) instead of reading the diff.

## Testing on more than one engine

The quickest real test is a fresh SQLite installation (`./console exp:install`) with your extension switched on and its
schema installed. If your extension claims support for other databases, test there too; the kernel's CI runs MySQL and
PostgreSQL for this reason ([Continuous integration](../../../bc/6.0/ci.md)).

## Related pages

- Previous: [Persistent objects and your own tables](persistent-objects-and-tables.md)
- Next: [Events, listeners and filters](events-and-filters.md)
- [Databases](../../../install/09-databases.md)
- Packages (.ezpkg) and the package creator
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
