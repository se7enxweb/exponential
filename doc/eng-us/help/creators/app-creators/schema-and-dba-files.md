---
title: Database schema and .dba files
audience: app-creator, developer
category: app-creation
order: 230
summary: Ship the tables of your application for every database engine - the .dba schema format, dumping it with ezsqldumpschema, installing it with ezsqlinsertschema, per-engine SQL files, an install command, and upgrades.
keywords: schema, dba, db_schema.dba, db_data.dba, ezsqldumpschema, ezsqlinsertschema, ezsqldiff, ezimportdbafile, eZDbSchema, install schema, upgrade, tables, app creator
type: help
---
# Database schema and .dba files

This page is for application creators whose extension has its own tables. Exponential describes schemas in a
database-neutral format, `.dba`, and generates the SQL for each engine from it, so one file serves SQLite, PostgreSQL,
MySQL/MariaDB and Oracle.

## Contents

1. [The .dba format](#the-dba-format)
2. [Making the file](#making-the-file)
3. [Installing it](#installing-it)
4. [An install command](#an-install-command)
5. [Upgrades](#upgrades)
6. [Reviewing changes](#reviewing-changes)

## The .dba format

A `.dba` file is a PHP array of tables, each with fields (type, length, default, not null), indexes and the primary key.
The kernel's own schema is `share/db_schema.dba` and its data `share/db_data.dba`; extensions keep theirs in
`extension/<ext>/share/db_schema.dba` (and `db_data.dba` for seed rows). The class that reads and writes them is
`eZDbSchema` (`lib/ezdbschema/`).

## Making the file

Create the table on a development database, then dump only your tables:

```bash
php bin/php/ezsqldumpschema.php --type=sqlite --table-include=expdemo_item --output-array \
  var/storage/sqlite3/exponential.db extension/expdemo/share/db_schema.dba --allow-root-user
```

Check the options of your version with `php bin/php/ezsqldumpschema.php --help`; the dump can also write SQL for one
engine instead of the array.

## Installing it

```bash
php bin/php/ezsqlinsertschema.php --type=mysql --user=<user> extension/expdemo/share/db_schema.dba <database>
```

`ezsqlinsertschema.php` ("Insert database schema and data to specified database") creates the tables for the engine
named by `--type`. `ezimportdbafile.php` imports a `.dba` into the configured database.

## An install command

Give your extension one idempotent command that creates its tables if they are missing, the way expqueue does with
`./console ext:expqueue:install-schema` ([expqueue](../../../features/6.0/extensions/expqueue.md)):

```text
extension/expdemo/bin/php/install-schema.php   ->   ./console ext:expdemo:install-schema
```

It reads the `.dba`, checks which tables exist, and creates only the missing ones; running it twice changes nothing.

## Upgrades

- Never change a released table in place without an upgrade step.
- Ship upgrade steps as numbered scripts or SQL files per engine (`extension/<ext>/update/`), and let the install
  command apply the missing ones.
- Add columns with defaults, so old code keeps working during the deploy.

## Reviewing changes

A `.dba` is a PHP array; removing one row renumbers what follows, so a text diff is unreadable for data files. Compare
schemas with `php bin/php/ezsqldiff.php`, and data row by row with a script, not by diff.

## Related pages

- [Persistent objects and custom tables](persistent-objects-and-custom-tables.md)
- [Database access and SQL](database-access.md)
- [Databases](../../../install/09-databases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
