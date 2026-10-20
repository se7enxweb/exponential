---
title: Changing a customer site's database engine
audience: hosting, admin
category: hosting
order: 60
summary: How a host moves a customer site from one of the six supported databases to another (for example from SQLite 3 to PostgreSQL when a site outgrows one machine): when it is worth it, the engine-neutral .dba transfer, Oracle and MongoDB specifics, the settings to change, and the checks before reopening.
keywords: database engine, switch database, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, ezsqldumpschema, ezsqlinsertschema, dba, DatabaseImplementation, maintenance window, row counts, sequences, ezoracle, mysql2oracle
type: help
---
# Changing a customer site's database engine

This page is for system administrators at hosting companies who move a customer site to another database engine.
It answers: when is a switch worth it, how is the data transferred, what changes in the settings, and how do you
check the result?

The supported databases, in the official order: 1. SQLite 3 (default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB ·
5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work at this time, without a driver
in 6.0.x ([Database support](../../database-support.md)).

## Contents

1. [When to switch](#when-to-switch)
2. [Plan](#plan)
3. [Between the SQL engines](#between-the-sql-engines)
4. [Into Oracle or MongoDB](#into-oracle-or-mongodb)
5. [Settings to change](#settings-to-change)
6. [Checks before reopening](#checks-before-reopening)
7. [Later upgrades](#later-upgrades)

## When to switch

Typical reasons a host sees, from [Database support](../../database-support.md#1-sqlite-3-the-default): choose a
server instead of SQLite 3 "when more than one machine must write the database", "when many editors publish large
changes at the same moment all day, or when you need replication or database-level users and grants". The
recommended server is PostgreSQL. Other reasons: the customer's organization standardizes on one engine, or your
platform offers managed backups for one engine only.

> **Note**
> A switch is not a performance fix by itself. Check the caching layers first ([Caching layers](../performance/caching-layers.md));
> most read load never reaches the database.

## Plan

- A maintenance window: "no editor may publish between the dump and the moment the site runs on the new database, or
  that work is lost" ([9.9](../../../install/09-databases.md#99-switching-an-existing-site-to-another-engine)).
- A full backup of the current database and files before you start.
- The new database server, an empty database and a user with the rights to create tables.
- A rehearsal on a copy, timed.

## Between the SQL engines

The schema tools move a database between SQLite 3, PostgreSQL, MariaDB, MySQL and (with `ezoracle`) Oracle through
Exponential's engine-neutral `.dba` format. `--type` takes a schema handler from `settings/dbschema.ini`: `mysql`,
`postgresql`, `sqlite3`, `oracle`.

```bash
# 1. maintenance on
php bin/php/maintenance.php on --message="Database maintenance" --until=1h --allow-root-user

# 2. dump schema and data of the current database (MySQL in this example)
php bin/php/ezsqldumpschema.php --type=mysql --host=localhost --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user

# 3. load them into the new one (PostgreSQL in this example)
php bin/php/ezsqlinsertschema.php --type=postgresql --host=localhost --user=exponential --password='<password>' \
    --insert-types=all --schema-file=var/backup/site.dba var/backup/site.dba exponential --allow-root-user
```

For SQLite 3 the database argument is the file name, as in the `Database` setting. Run both scripts with `--help`
first; the options per engine (host, port, socket, table type, charset) are listed there.

> **Warning**
> `--password=` puts the password on the command line, where other users of the machine can see it in the process
> list. Run the transfer on a machine you control, or with a temporary password you change afterwards
> ([9.9](../../../install/09-databases.md#99-switching-an-existing-site-to-another-engine)).

## Into Oracle or MongoDB

- **Oracle**: the `ezoracle` extension has its own transfer commands, `ext:ezoracle:mysql2oracle-schema` and
  `ext:ezoracle:mysql2oracle-data` ([Oracle for hosts](../basics/oracle-for-hosts.md)).
- **MongoDB**: the scripts in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`,
  `validate_ndjson.sh`). Check the [MongoDB limits](../../database-support.md#mongodb-limits) against the customer's
  extensions before you promise the switch ([MongoDB for hosts](../basics/mongodb-for-hosts.md)).

## Settings to change

In `settings/override/site.ini.append.php`, block `[DatabaseSettings]`: `DatabaseImplementation` and the connection
keys (server, port, user, password, database). "A forgotten `DatabaseImplementation` is the usual reason no page
loads after a switch" (9.9). The keys per engine are on
[The settings: DatabaseSettings in site.ini](../../database-support.md#the-settings-databasesettings-in-siteini).

Then clear every cache and restart the workers:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user    # when Velocity serves the site
```

Under PHP-FPM, reload the site's PHP-FPM service instead.

## Checks before reopening

1. Count the rows of `ezcontentobject`, `ezcontentobject_tree` and `ezcontentobject_attribute` on both sides; the
   numbers must match.
2. On PostgreSQL, correct the sequences if `ezsqlinsertschema.php` reported a problem with them
   ([9.4](../../../install/09-databases.md#94-postgresql)).
3. Sign in to the administration, open a few pages and publish a test object: "a publish exercises the writes that a
   page view does not".
4. Switch maintenance off: `php bin/php/maintenance.php off --allow-root-user`.
5. Change the backup job to the new engine's tool ([Database backups per engine](../operations/database-backups-per-engine.md)).

## Later upgrades

"Schema updates are per engine": update files are chosen by the engine the site runs on now, not the one it was
installed on (`update/database/mysql/`, `update/database/postgresql/`, `update/database/sqlite/`, and for Oracle the
files in `ezoracle`; MongoDB has no SQL update files). Note the new engine in the customer's record.

## Related pages

- [Databases in the official order](../basics/databases-in-the-official-order.md)
- [Moving a site between hosts](moving-a-site-between-hosts.md)
- [Chapter 9: Databases](../../../install/09-databases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
