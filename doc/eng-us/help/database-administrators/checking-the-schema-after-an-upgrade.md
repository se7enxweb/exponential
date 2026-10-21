---
title: Checking the schema after an upgrade
audience: dba
category: database-administration
order: 32
summary: How to confirm that an Exponential database has the schema of its release - the database consistency check in Setup > Upgrade check and the kinds of finding it reports, the MongoDB collection check, ezsqldiff.php against share/db_schema.dba, exp:checkdbfiles, and the Oracle schema-diff command - and how to act on each finding safely.
keywords: upgrade check, database consistency, schema check, missing table, changed table, table not in the schema, engine notes, ezsqldiff, share/db_schema.dba, exp:checkdbfiles, schema-diff, MongoDB collections, Removes something, system.upgrade.run
type: help
---
# Checking the schema after an upgrade

This page is for database administrators who verify an Exponential database after an upgrade, a restore or an engine
move. It answers: which tools compare the database with the release's schema, what each kind of finding means, and
which findings to act on. The tools only read: none of them changes the database. Source:
[The upgrade check](../../guides/upgrade-check.md#6-run-the-database-consistency-check) and
[11.7 Verify the upgrade](../../install/11-upgrading.md#117-verify-the-upgrade).

## Contents

1. [The tools](#the-tools)
2. [The database consistency check](#the-database-consistency-check)
3. [Each kind of finding](#each-kind-of-finding)
4. [On MongoDB](#on-mongodb)
5. [From the shell: ezsqldiff.php](#from-the-shell-ezsqldiffphp)
6. [On Oracle: schema-diff](#on-oracle-schema-diff)
7. [Checklist](#checklist)

## The tools

| Tool | Compares | Engines |
|---|---|---|
| **Setup > Upgrade check**, "Check database consistency" | the database with `share/db_schema.dba` and the schemas of active extensions | MySQL, MariaDB, PostgreSQL, SQLite, MongoDB (collections), and any engine whose schema handler is registered in `dbschema.ini` |
| `bin/php/ezsqldiff.php` | two schemas, files or databases | `mysql`, `postgresql` |
| `exp:checkdbfiles` | the update files on disk with the upgrade path | all |
| `ext:ezoracle:schema-diff` | the Oracle schema with `share/db_schema.dba` | Oracle |

## The database consistency check

In the administration, **Setup > Upgrade check**, press **Check database consistency**. It reads
`share/db_schema.dba` and the `share/db_schema.dba` of every active extension that has one, reads the schema of the
database, and compares them with the schema handler of the engine (`dbschema.ini [SchemaSettings]`). It also reads the
schema files of installed but inactive extensions, only to say which of them a table belongs to. On the demonstration
server (SQLite, 11 schema files) it takes about 0.1 seconds.

The page shows figures (tables with SQL to review, missing tables, tables not in the schema, changed tables, tables of
inactive extensions, engine notes), then one folding card per table with the differences and the SQL for that table.
**All statements** holds the whole SQL as one block. The page never runs any of it. Each run is written to the audit
trail as `system.upgrade.run`.

## Each kind of finding

| Kind | Meaning | What to do |
|---|---|---|
| **Missing table** | shipped with Exponential or an active extension, not in the database; the feature that uses it fails | run its `CREATE` statement after a backup, or the update file or script that should have made it |
| **Table not in the schema** | in no schema at all: usually a table of your own extension without a schema file, or of a removed extension | leave it unless you know its data is no longer needed; the `DROP` is shown only to be complete, marked **Removes something** |
| **Table of an extension that is not active** | named by the schema of an installed, inactive extension (for example `ezfind_elevate_configuration` of ezfind) | activate the extension or leave the table; no SQL is written for it |
| **Changed table** | fields or indexes differ from the shipped definition | read each statement; run the ones you agree with after a backup; statements marked **Removes something** can remove your data |
| **Engine notes** | the schema handler writes no SQL for the difference (on SQLite, for example, `text` and `longtext` are the same) | nothing |

The rule: missing tables are usually real problems; extra and changed tables are usually your own work. Never run
**All statements** as a whole.

## On MongoDB

The check compares collections: missing collections grouped by feature, collections not in the schema, and the
`mongosh` command that creates the missing ones. Run `bin/mongodb/create_indexes.js` afterwards
([MongoDB: limits and operations](mongodb-limits-and-operations.md#upgrades-without-sql-files)).

## From the shell: ezsqldiff.php

```bash
php bin/php/ezsqldiff.php --type=mysql --user=USER --password='<password>' share/db_schema.dba DATABASE --allow-root-user
echo $?
```

The reference schema goes **first**. The output is the SQL that would turn your database into the reference: a
`CREATE TABLE` or `ADD` line names something an update step should have made; a `DROP TABLE` line a table only your
database has (your own extensions' tables appear that way). Read it, do not run it. Exit code 0 when the schemas
match, 1 when they differ; `--check-only` prints nothing and only sets the exit code, useful in a deploy script
([Schema tools](schema-tools-dump-insert-diff.md#ezsqldiffphp)).

Related shell checks:

```bash
php bin/php/console exp:checkdbfiles --no-verify-branches     # update files complete: no output
php bin/php/console exp:system:info --allow-root-user         # exits 1 when a health check fails
```

## On Oracle: schema-diff

```bash
php bin/php/console ext:ezoracle:schema-diff --allow-root-user
```

It compares the database with `share/db_schema.dba` (`--file=<dba>` for another file) and prints the upgrade SQL;
afterwards `ext:ezoracle:recompile` recompiles invalid objects
([Oracle maintenance commands](oracle-maintenance-commands.md)).

## Checklist

- [ ] The version row matches the code.
- [ ] The consistency check lists no missing table.
- [ ] Changed and extra tables are explained (own extensions, removed extensions).
- [ ] `exp:checkdbfiles` prints nothing.
- [ ] No statement was run without a backup and a reason.

## Related pages

- [Upgrades and the SQL update files](upgrades-and-sql-update-files.md)
- [The .dba schema format](the-dba-schema-format.md)
- [Upgrade check](../../guides/upgrade-check.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
