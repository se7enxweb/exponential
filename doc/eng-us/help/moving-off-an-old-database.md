---
title: Moving off an old database
audience: admin, dba, decision-maker
category: install-upgrade
order: 13
summary: Why and how to move an existing Exponential site off a database that holds it back - an old MySQL with MyISAM tables or a three-byte character set, a server out of vendor support, SQLite on a site that has outgrown one machine - onto PostgreSQL 13 to 19, current MariaDB or MySQL, Oracle, MongoDB, or Microsoft SQL Server and Azure SQL, with the commands that exist for each step.
keywords: move database, migrate database, change database, switch engine, old database, legacy database, MySQL 5, MyISAM, InnoDB, utf8mb4, emoji, end of life, out of support, SQLite to PostgreSQL, MySQL to PostgreSQL, MySQL to SQL Server, Azure SQL, Oracle, MongoDB, clustering, read replicas, ezsqldumpschema, ezsqlinsertschema, ezconvertmysqltabletype, ezconvertdbcharset, exp:cluster:enable
featured: true
context: setup/info
type: help
---
# Moving off an old database

This page is for administrators and decision-makers whose Exponential site still runs on the database it was
installed on years ago. Exponential 6.0.x runs on seven database engines, from SQLite to Microsoft SQL Server, on
current versions of each ([Database support](database-support.md#support-matrix)). The content of a site does not
depend on the engine it is stored in: the same objects, versions, translations, URLs and layouts move with it. So the
database a site started on is a choice you can change, not a limit you have to live with.

## Contents

1. [Signs that the database holds the site back](#signs-that-the-database-holds-the-site-back)
2. [What a current engine gives you](#what-a-current-engine-gives-you)
3. [Choosing where to go](#choosing-where-to-go)
4. [Fix in place first: old MySQL and MariaDB](#fix-in-place-first-old-mysql-and-mariadb)
5. [The move, step by step](#the-move-step-by-step)
6. [After the move: clustering and replicas](#after-the-move-clustering-and-replicas)
7. [Related pages](#related-pages)

## Signs that the database holds the site back

| What you see | The cause | Where it leads |
|---|---|---|
| Emoji and some characters of other scripts are lost or rejected | MySQL's old three-byte `utf8` character set | `utf8mb4` on the same server, or another engine |
| A failed publish leaves half-written content; tables are locked during imports | MyISAM tables, which have no transactions and lock whole tables | InnoDB on the same server, or another engine |
| The server no longer gets security fixes | MySQL 5.x, an old MariaDB, PostgreSQL 12 or older, Oracle 18c or older, SQL Server 2016 or older | a current release of the same engine, or another engine |
| The site has to run on a second web server | SQLite keeps the whole database in one file on one machine | PostgreSQL, MariaDB, MySQL, Oracle, MongoDB or SQL Server, which serve any number of web servers |
| IT runs one database platform and yours is not it | the site was installed on what was at hand | the engine your organization already operates, backs up and monitors |
| Reads slow the site down at peak times | one database server does every read | read replicas: PostgreSQL, MariaDB, MySQL, Oracle and SQL Server |

## What a current engine gives you

- **Transactions.** On InnoDB, PostgreSQL, Oracle and SQL Server a publish is committed or rolled back as a whole.
- **Every character.** Full Unicode in content, URLs and search: `utf8mb4` on MariaDB and MySQL, `UTF8` on
  PostgreSQL, `AL32UTF8` on Oracle, UTF-8 collations on SQL Server 2019 and later.
- **Several web servers on one database**, with the DFS cluster file handler keeping its metadata on the same engine:
  MySQL and MariaDB, PostgreSQL 13 to 19, Oracle, SQL Server and Azure SQL, MongoDB
  ([Clustering in Exponential 6.0](../features/6.0/clustering-overview.md)).
- **Read replicas** for anonymous traffic: PostgreSQL streaming standbys, MariaDB and MySQL replicas, Oracle, and SQL
  Server Always On availability groups.
- **Managed databases.** Azure SQL Database and Managed Instance, and the managed PostgreSQL, MySQL and MariaDB
  services of the cloud providers, take backups, patching and failover off your hands.
- **Tested on every push.** PostgreSQL 13, 15, 18 and 19, MySQL 8.0 and SQL Server 2017 to 2025 are exercised by the
  project's continuous integration ([Test coverage](database-support.md#test-coverage-ci)).

## Choosing where to go

| You want | Go to |
|---|---|
| The project's recommended database server | **PostgreSQL** 13 to 19 ([PostgreSQL 18 and 19](../features/6.0/postgresql-18-and-19.md)) |
| To stay with what you know, but current | **MariaDB** or **MySQL** with InnoDB and `utf8mb4` (often no move at all: [below](#fix-in-place-first-old-mysql-and-mariadb)) |
| Microsoft's platform, on premises or in Azure | **SQL Server** 2017 to 2025 or **Azure SQL** ([SQL Server driver](../features/6.0/sqlserver-database-driver.md)) |
| Oracle, where the organization runs it | **Oracle** 19c and later |
| A document store the organization already runs | **MongoDB** 6.0 and later ([MongoDB as the database](../features/6.0/mongodb-database-support.md)) |
| One machine and no database server at all | **SQLite** 3, the default |

A longer comparison: [Choosing an engine](database-administrators/choosing-an-engine.md).

## Fix in place first: old MySQL and MariaDB

Many old MySQL and MariaDB sites do not need a new engine, only a current server and two conversions, which the
kernel ships as scripts:

```bash
# MyISAM to InnoDB: list the table types, then convert
php bin/php/ezconvertmysqltabletype.php --host=localhost --user=exponential --password='<password>' --database=exponential --list
php bin/php/ezconvertmysqltabletype.php --host=localhost --user=exponential --password='<password>' --database=exponential --newtype=innodb
# the site's tables to UTF-8 (run it with --help first)
php bin/php/ezconvertdbcharset.php --help
```

Back the database up first, and run both in a maintenance window. Then upgrade the server itself to a release your
vendor supports, following the vendor's own upgrade path.

## The move, step by step

The full procedure, with every command and option, is
[Moving a site to another engine](database-administrators/moving-between-engines.md). In short:

1. **Plan** a maintenance window and rehearse the whole move on a copy of the site.
2. **Prepare the target**: the database, its account and its character set, as the engine's setup page describes.
3. **Copy schema and data.**
   - Between SQL engines (SQLite, PostgreSQL, MariaDB, MySQL, and Oracle with `ezoracle`): dump into the
     engine-neutral `.dba` format with `bin/php/ezsqldumpschema.php --format=generic --output-array --output-types=all`
     and load it with `bin/php/ezsqlinsertschema.php --insert-types=all --type=<engine>`. The MySQL to PostgreSQL
     conversion is checked byte for byte on PostgreSQL 18 and 19
     ([Moving from MySQL or MariaDB to PostgreSQL 18 or 19](../features/6.0/postgresql-18-and-19.md#moving-from-mysql-or-mariadb-to-postgresql-18-or-19)).
   - Into Oracle: `ext:ezoracle:mysql2oracle-schema` and `ext:ezoracle:mysql2oracle-data`, then
     `ext:ezoracle:sequence-sync`.
   - Into MongoDB: the scripts in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`).
4. **Switch the settings**: `DatabaseImplementation` and the connection keys in
   `settings/override/site.ini.append.php`, then clear every cache and restart the PHP workers.
5. **Check before you reopen**: the row counts of `ezcontentobject`, `ezcontentobject_tree` and
   `ezcontentobject_attribute` match on both sides, the database consistency check (Setup > Upgrade check) is clean,
   and a test object publishes.
6. **Keep the old database read-only** for a while, then retire it with its credentials.

Later upgrades use the update files of the engine the site runs on now.

## After the move: clustering and replicas

On a database server, the site can grow beyond one web server:

```bash
./console exp:cluster:enable --share=/mnt/exp-dfs/example.com             # the plan, nothing changed
./console exp:cluster:enable --share=/mnt/exp-dfs/example.com --apply     # do it
```

`exp:cluster:enable` makes the share, creates the cluster tables in the site's database (`--backend` chooses
`mysqli`, `postgresql`, `oracle`, `sqlserver`, `mongodb` or `sqlite`; `auto` follows the site's engine), writes the
settings and moves the files in ([A one-node DFS cluster in one command](../features/6.0/cluster-commands.md)). Add
the next web servers and a read replica as [Clustering in Exponential 6.0](../features/6.0/clustering-overview.md)
shows.

## Related pages

- [Database support](database-support.md): every engine, with the support matrix
- [Moving a site to another engine](database-administrators/moving-between-engines.md): the full procedure
- [Choosing an engine](database-administrators/choosing-an-engine.md)
- [Clustering in Exponential 6.0](../features/6.0/clustering-overview.md)
- [Exponential on Windows](exponential-on-windows.md)
- [Operating system support](os-support.md)
