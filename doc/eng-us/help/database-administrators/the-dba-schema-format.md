---
title: The .dba schema format and the shipped schema files
audience: dba
category: database-administration
order: 26
summary: What the engine-neutral .dba format is, which schema and data files ship with Exponential (share/db_schema.dba, share/db_data.dba, kernel/sql per engine), how schema handlers registered in dbschema.ini turn them into DDL, how extensions add their own schema, and how the lint checker keeps names portable.
keywords: dba, .dba, db_schema.dba, db_data.dba, schema, engine-neutral, generic format, kernel_schema.sql, schema.sql, cleandata.sql, setval.sql, sequence.sql.template, dbschema.ini, SchemaPaths, SchemaHandlerClasses, eZDbSchema, LintChecker, extension share, ezimportdbafile
type: help
---
# The .dba schema format and the shipped schema files

This page is for database administrators who want to know where the definition of an Exponential database comes from
before they compare, dump or move one. It answers: what a `.dba` file is, which schema files ship with the kernel and
which tool reads each, how extensions add tables, and what keeps names valid on every engine.

## Contents

1. [The .dba format](#the-dba-format)
2. [The shipped files](#the-shipped-files)
3. [Schema handlers](#schema-handlers)
4. [Extensions and their schema](#extensions-and-their-schema)
5. [The lint checker](#the-lint-checker)
6. [What you do with these files](#what-you-do-with-these-files)
7. [Checklist](#checklist)

## The .dba format

A `.dba` file is a PHP file that holds an array: for the schema, one entry per table with its fields (type, length,
`not_null`, default) and indexes; for data, the rows. It describes the database without the dialect of any engine,
so one file serves every engine. The start of `share/db_schema.dba` looks like this:

```php
<?php
// This array contains the database schema
$schema = array (
  'expaudit_cursor' =>
  array (
    'name' => 'expaudit_cursor',
    'fields' =>
    array (
      'byte_offset' =>
      array (
        'length' => 20,
        'type' => 'bigint',
        ...
```

`ezsqldumpschema.php --format=generic --output-array` writes this format from a live database
([Schema tools: dump, insert and diff](schema-tools-dump-insert-diff.md)). `--format=local` writes a dump that suits
only the engine it came from.

## The shipped files

| File | What it is | Read by |
|---|---|---|
| `share/db_schema.dba` | the kernel's schema, engine-neutral (about 130 tables) | the installers, the upgrade check, `ezsqldiff.php` |
| `share/db_data.dba` | the kernel's initial data | the installers |
| `kernel/sql/mysql/kernel_schema.sql` | the schema as MySQL DDL (129 `CREATE TABLE`) | reference, hand installs |
| `kernel/sql/mysql/cluster_dfs_schema.sql` | the DFS cluster tables `ezdfsfile`, `ezdfsfile_cache` | cluster setup |
| `kernel/sql/mysql/cluster_db_schema.sql`, `cluster_schema.sql` | tables of the removed database file handler (`ezdbfile`, `ezdbfile_data`) | historical |
| `kernel/sql/postgresql/kernel_schema.sql` | the schema as PostgreSQL DDL (129 `CREATE TABLE`) | reference |
| `kernel/sql/postgresql/setval.sql` | `setval()` for every kernel sequence (85 statements) | after loading data with explicit ids |
| `kernel/sql/postgresql/sequence.sql.template` | the sequence template | schema generation |
| `kernel/sql/sqlite/schema.sql`, `cleandata.sql` | the SQLite schema and clean data | the SQLite tests compare the driver against them |
| `kernel/sql/common/cleandata.sql` | clean initial data | reference |
| `share/legacy_schema.sql` | a MySQL dump kept in `share/`; no code in `kernel/`, `lib/` or `bin/` reads it | nothing; do not apply it |

## Schema handlers

`settings/dbschema.ini [SchemaSettings]` maps a type to a handler class:

| Type | Class | File |
|---|---|---|
| `mysql`, `mysqli` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `postgresql` | `eZPgsqlSchema` | `lib/ezdbschema/classes/ezpgsqlschema.php` |
| `sqlite`, `sqlite3` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |
| `mongo` | `expMongoSchema` | `lib/ezdbschema/classes/expmongoschema.php` |
| `oracle` | `eZOracleSchema` | added by the `ezoracle` extension |

A handler writes DDL from a `.dba` schema and reads a live database back into one. Index names are database-wide on
SQLite and PostgreSQL; the handlers create a clashing name as `<table>__<name>`. On PostgreSQL the handler creates each
sequence before its table, and after loading data the schema installer corrects the sequences
([Operating PostgreSQL](postgresql-operations.md)).

## Extensions and their schema

An extension with tables ships its own `share/db_schema.dba` (and often `sql/<engine>/*.sql`). The upgrade check reads
the `share/db_schema.dba` of every active extension, and the schemas of installed but inactive extensions only to tell
you which extension a table belongs to ([Checking the schema after an upgrade](checking-the-schema-after-an-upgrade.md)).
Some datatypes ship data of their own: `bin/php/ezimportdbafile.php --datatype=<name>` imports it (example from its
help: `--datatype=ezisbn`).

## The lint checker

`dbschema.ini [LintChecker]` limits name lengths so that the schema stays valid on engines with short identifiers:
`TableLimit=26`, `FieldLimit=30`, `IndexLimit=30`, with lists of historical exceptions (`IgnoredTables[]`,
`IgnoredFields[]`, `IgnoredIndexes[]`). `ezsqldiff.php --lint-check` checks a database against these rules instead of
comparing two schemas. Extension developers should keep their names within the limits.

## What you do with these files

- **Never edit** `share/db_schema.dba` or the files in `kernel/sql/`: an upgrade replaces them.
- **Compare** your database with `share/db_schema.dba` after an upgrade.
- **Move** a site between SQL engines by dumping to `.dba` and inserting with another `--type`
  ([Moving a site to another engine](moving-between-engines.md)).
- **Keep** a `.dba` dump of your own database as a portable second backup format, next to the engine's native dump.

## Checklist

- [ ] You know which schema file each tool reads.
- [ ] Your own extensions ship a `share/db_schema.dba` for their tables.
- [ ] Names in your extensions pass `ezsqldiff.php --lint-check`.

## Related pages

- [Schema tools: dump, insert and diff](schema-tools-dump-insert-diff.md)
- [The tables a database administrator should know](tables-you-should-know.md)
- [The database layer in one page](the-database-layer-in-one-page.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
