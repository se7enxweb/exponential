---
title: "Schema tools: dump, insert and diff"
audience: dba
category: database-administration
order: 27
summary: The three command-line schema tools of Exponential - ezsqldumpschema.php to dump a schema and data to .dba or SQL, ezsqlinsertschema.php to load them into any SQL engine, and ezsqldiff.php to compare two schemas with an exit code - with their options as their help prints them, safe usage and examples.
keywords: ezsqldumpschema, ezsqlinsertschema, ezsqldiff, exp:ezsqldumpschema, exp:ezsqlinsertschema, exp:ezsqldiff, dba, dump schema, insert schema, schema diff, --output-types, --insert-types, --clean-existing, --check-only, --lint-check, --reverse, exit code
type: help
---
# Schema tools: dump, insert and diff

This page is for database administrators who dump, load or compare Exponential schemas. Three scripts in `bin/php/` do
it, each also available through the console (`exp:ezsqldumpschema`, `exp:ezsqlinsertschema`, `exp:ezsqldiff`). The
options below are copied from their `--help` output in 6.0.15; run `--help` on your release before you rely on one.

## Contents

1. [Before you start](#before-you-start)
2. [ezsqldumpschema.php](#ezsqldumpschemaphp)
3. [ezsqlinsertschema.php](#ezsqlinsertschemaphp)
4. [ezsqldiff.php](#ezsqldiffphp)
5. [Examples](#examples)
6. [Checklist](#checklist)

## Before you start

- The `--password=` option puts the password on the command line, visible in the process list. Run the tools on a
  machine you control or with a temporary password.
- `--type` takes a schema handler type from `settings/dbschema.ini`; for SQLite the database argument is the file
  name, as in `Database`. The scripts' own help lists only some types; `sqlite3` and, with `ezoracle`, `oracle` work as
  well ([9.9](../../install/09-databases.md#99-switching-an-existing-site-to-another-engine)).
- Add `--allow-root-user` when you run them as root.

## ezsqldumpschema.php

`ezsqldumpschema.php [OPTION]... [DATABASE] [FILENAME]` dumps the schema, the data or both to a file or standard
output.

| Option | Meaning |
|---|---|
| `--type`, `--host`, `--port`, `--socket`, `--user`, `--password` | the source database (`--socket` MySQL only) |
| `--output-array` | a file with array structures (human readable): the `.dba` format |
| `--output-serialized` | serialized data (saves space) |
| `--output-sql` | SQL for the source engine |
| `--format=generic` or `local` | `generic` suits all databases (default); `local` only the one it was dumped from |
| `--output-types=schema`, `data`, `all` | what to dump (default: schema only) |
| `--diff-friendly` | SQL output friendlier to `diff` |
| `--meta-data` | extra engine-specific information |
| `--table-type`, `--table-charset` | for SQL output (MySQL table types: bdb, innodb, myisam) |
| `--compatible-sql`, `--no-sort`, `--allow-multi-insert` | output details |
| `--schema-file` | only when dumping from files |

## ezsqlinsertschema.php

`ezsqlinsertschema.php [OPTION]... [FILENAME] [DATABASE]` loads a `.dba` schema and data into a database.

| Option | Meaning |
|---|---|
| `--type`, `--host`, `--port`, `--socket`, `--user`, `--password` | the target database |
| `--insert-types=schema`, `data`, `all`, `none` | what to load (default: schema only); `none` with `--clean-existing` only cleans |
| `--schema-file` | the schema file, required when importing a schema |
| `--table-type`, `--table-charset` | table options for the target |
| `--allow-multi-insert` | multi-row inserts where the engine supports them |
| `--clean-existing` | **removes all database objects** before loading |

`--clean-existing` empties the target database. Check the `DATABASE` argument twice, and never point it at a
production database by accident. On PostgreSQL the installer corrects the sequences after loading data.

## ezsqldiff.php

`ezsqldiff.php [OPTION]... [SOURCE] [MATCH]` shows the differences between two schemas and sets the exit code: 0 when
they match, 1 when they differ.

| Option | Meaning |
|---|---|
| `--type`, `--host`, `--user`, `--password`, `--socket` | one connection for both sides |
| `--source-*`, `--match-*` | separate connections per side |
| `--check-only` | no SQL, only the exit code |
| `--reverse` | reverse the differences |
| `--lint-check` | check the source schema against the naming rules instead of comparing ([The .dba schema format](the-dba-schema-format.md#the-lint-checker)) |

Types: `mysql` and `postgresql`. A side can be a `.dba` file. To compare your database with the release, give the
reference schema **first**; the output is the SQL that would turn your database into the reference. Read it, do not
run it: a `DROP TABLE` line often names a table of your own extension
([11.7](../../install/11-upgrading.md#117-verify-the-upgrade)).

## Examples

Dump schema and data of a MySQL site into one portable file:

```bash
php bin/php/ezsqldumpschema.php --type=mysql --host=localhost --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user
```

Load it into PostgreSQL:

```bash
php bin/php/ezsqlinsertschema.php --type=postgresql --host=localhost --user=exponential --password='<password>' \
    --insert-types=all --schema-file=var/backup/site.dba var/backup/site.dba exponential --allow-root-user
```

Compare a MySQL database with the release's schema:

```bash
php bin/php/ezsqldiff.php --type=mysql --user=exponential --password='<password>' share/db_schema.dba exponential --allow-root-user
echo $?        # 0: the same, 1: differences
```

Keep dumps out of the web root: `var/backup/` is below `var/`, which the shipped rewrite rules do not serve, but check
your server as for the SQLite file ([Securing the database](securing-the-database.md)).

## Checklist

- [ ] `--help` read on your release.
- [ ] Passwords on the command line only on a machine you control.
- [ ] `--clean-existing` used only against a target you meant to empty.
- [ ] Diff output read and reviewed, never piped into the database.

## Related pages

- [Moving a site to another engine](moving-between-engines.md)
- [Checking the schema after an upgrade](checking-the-schema-after-an-upgrade.md)
- [A portable dump between SQL engines](../hosting/operations/database-backups-per-engine.md#a-portable-dump-between-sql-engines)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
