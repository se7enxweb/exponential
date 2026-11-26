---
title: Choosing a database
audience: admin, developer, dba
category: install-upgrade
order: 9
summary: Every database Exponential 6.0.15 reaches, in one matrix (status in 6.0.14 and 6.0.15, site database, DFS cluster, read replicas, installers, data table engine, import source, PHP extension, operating system, CI, tested upgrades), with the measured timings and a decision guide per use case - one server, a cluster, a managed cloud database, embedded and demo sites, analytics and imports, Windows, and moving off MS Access or another old database.
keywords: choosing a database, database matrix, which database, SQLite, PostgreSQL, MongoDB, MariaDB, MySQL, Oracle, Microsoft SQL Server, Azure SQL, TiDB, Vitess, PlanetScale, SingleStore, MariaDB Xpand, Aurora MySQL, CockroachDB, YugabyteDB, Spanner, PGAdapter, Neon, Supabase, Aurora PostgreSQL, Firebird, DuckDB, ODBC, MS Access, exp:access:import, data tables, sqliimport, DFS cluster, read replicas, xne, upgrade, Windows, pdo_firebird, FFI, sqlsrv, pdo_sqlsrv, pdo_odbc, odbc
featured: true
context: setup/info, setup/systemupgrade
type: help
---
# Choosing a database

This page is for administrators, database administrators and developers who decide which database a new Exponential
site runs on, or whether a database they already have can be reached. Exponential 6.0.14 ran on five databases;
6.0.15 reaches more than twenty, at different levels: some carry a whole site, some only hold data tables and feed
imports, some are written from their vendors' documentation and have never run here. This page puts all of them side
by side, says honestly what was tested and what was not, and then answers the question by use case.

Every statement here comes from the code of the 6.0.15 line and the pages of the drivers themselves. The details,
settings and limits of each database are on its own page, linked in the tables.
[Database support](database-support.md) is the support statement with versions and settings; the operator's chapter
is [chapter 9 of the installation guide](../install/09-databases.md).

## Contents

1. [The levels](#the-levels)
2. [The matrix: status and use](#the-matrix-status-and-use)
3. [The matrix: requirements and tests](#the-matrix-requirements-and-tests)
4. [Backups and upgrade captions](#backups-and-upgrade-captions)
5. [Measured timings](#measured-timings)
6. [Which database for which use](#which-database-for-which-use)
7. [In development](#in-development)
8. [Related pages](#related-pages)

## The levels

| Level | Meaning |
|---|---|
| **Site database** | the installers create the kernel schema and a site package in it, and the site runs from it |
| **Site database, not measured** | the driver and its profile exist and pass their tests without a server, but no site was ever installed on the database here (no container image, or a managed service nobody signed up for) |
| **Data tables and import** | a [data table connection](../features/6.0/data-tables.md) can make, read and write tables on it, and data can be imported from it; it does not carry a site |
| **Import only** | data is copied out of it into a database that carries the site |

The official order of the supported databases stays as [Database support](database-support.md) gives it: 1. SQLite 3
(the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server and Azure SQL.
The other databases of 6.0.15 are listed after them, by family.

## The matrix: status and use

"6.0.14" says whether the release before this one had the database at all. "Installers" names the setup wizard in
the browser, the kickstarter (`kickstart.ini`) and `exp:install`
([console install](../install/07-console-install.md)); the value is the `--db=` name. "DFS cluster" means several web
servers on one database, with the file metadata in it (`exp:cluster:enable`,
[Clustering in Exponential 6.0](../features/6.0/clustering-overview.md)).

| Database | 6.0.14 | 6.0.15 | Site database | DFS cluster | Read replicas (`UseSlaveServer`) | Installers |
|---|---|---|---|---|---|---|
| [SQLite 3](../features/6.0/sqlite-database.md) | yes | yes, the default | yes | one machine only (a one-node cluster) | no | wizard, kickstarter, `exp:install` (`sqlite`, the default) |
| [PostgreSQL 13 to 19](../features/6.0/postgresql-18-and-19.md) | yes | yes | yes | yes, in the kernel | yes | wizard, kickstarter, `pgsql` |
| [MongoDB 6.0 and later](../features/6.0/mongodb-database-support.md) | yes | yes | yes, through an adapter | yes, in the kernel | MongoDB's own replica set | wizard, kickstarter, `mongodb` |
| MariaDB | yes | yes | yes | yes (MySQLi backend) | yes | wizard ("MySQL Improved"), kickstarter, `mariadb` |
| Oracle 19c and later (`ezoracle`) | yes | yes | yes | yes, in the kernel | yes | wizard, kickstarter, `oracle` |
| MySQL | yes | yes | yes | yes (MySQLi backend) | yes | wizard ("MySQL Improved"), kickstarter, `mysql` |
| [SQL Server 2017 to 2025, Azure SQL](../features/6.0/sqlserver-database-driver.md) | no | new | yes | yes, in the kernel | yes (Always On, `ApplicationIntent=ReadOnly`) | kickstarter, `sqlserver` |
| [TiDB](../features/6.0/mysql-wire-databases.md#tidb) | no | new | yes | yes (MySQLi backend, measured) | the MySQL mechanism, not tested | kickstarter, `tidb` |
| [Vitess, PlanetScale](../features/6.0/mysql-wire-databases.md#vitess-and-planetscale) | no | new | yes, in an unsharded keyspace | yes (measured on Vitess) | the MySQL mechanism, not tested | kickstarter, `vitess` |
| [SingleStore](../features/6.0/mysql-wire-databases.md#singlestore) | no | new | yes | yes (measured) | the MySQL mechanism, not tested | kickstarter, `singlestore` |
| [MariaDB Xpand](../features/6.0/mysql-wire-databases.md#mariadb-xpand) | no | new, **untested** | not measured | not measured | not measured | kickstarter, `xpand` |
| [Amazon Aurora MySQL](../features/6.0/mysql-wire-databases.md#amazon-aurora-mysql) | no | new, **untested** | not measured | not measured | the reader endpoint, not measured | kickstarter, `aurora` |
| [CockroachDB 23.2 and later](../features/6.0/postgresql-wire-databases.md#cockroachdb) | no | new | yes | no: `exp:cluster:enable` offers no backend for it | not tested | wizard, kickstarter, `cockroachdb` |
| [YugabyteDB 2.20 and later](../features/6.0/postgresql-wire-databases.md#yugabytedb) | no | new | yes | no: `exp:cluster:enable` offers no backend for it | not tested | wizard, kickstarter, `yugabytedb` |
| [Neon](../features/6.0/postgresql-wire-databases.md#neon) | no | new profile, **untested** | as PostgreSQL, not measured | no: `exp:cluster:enable` offers no backend for it | a read replica compute, not measured | wizard, kickstarter, `neon` |
| [Supabase](../features/6.0/postgresql-wire-databases.md#supabase) | no | new profile, **untested** | as PostgreSQL, not measured | no: `exp:cluster:enable` offers no backend for it | not measured | wizard, kickstarter, `supabase` |
| [Amazon Aurora PostgreSQL](../features/6.0/postgresql-wire-databases.md#amazon-aurora-postgresql) | no | new profile, **untested** | as PostgreSQL, not measured | no: `exp:cluster:enable` offers no backend for it | the reader endpoint, not measured | wizard, kickstarter, `aurorapostgresql` |
| [Google Cloud Spanner, PostgreSQL dialect](../features/6.0/postgresql-wire-databases.md#google-cloud-spanner-postgresql-dialect) | no | new | **no**: data tables and import | no | no | none |
| [Firebird 4.0 and 5.0](../features/6.0/firebird-database-driver.md) | no | new | yes | **not yet** | no | wizard (with `pdo_firebird`), kickstarter, `firebird` |
| [DuckDB 1.5 and 2.0](../features/6.0/duckdb-database-driver.md) | no | new | yes, one writer at a time | **no** | no | kickstarter, `duckdb` |
| [Any database through ODBC](../features/6.0/odbc-database-driver.md) | no | new | **no**: data tables and import | no | no | none |
| [MS Access (`.accdb`, `.mdb`)](../features/6.0/ms-access-support.md) | no | new | **no**: import, and data tables on Windows | no | no | none (`exp:access:import`) |

Notes on the table:

- **The setup wizard** lists a database when the PHP running it has the database's extension: `sqlite3`, `mysqli`,
  `pgsql` (with CockroachDB, YugabyteDB, Neon, Supabase and Aurora PostgreSQL listed after PostgreSQL), `mongodb`,
  `oci8` and `pdo_firebird`. SQL Server, the MySQL protocol databases and DuckDB are installed with the kickstarter or
  `exp:install` ([setup wizard](../install/05-setup-wizard.md)).
- **DFS on the MySQL protocol databases**: the backend opens its own `mysqli` connection without TLS. On PlanetScale,
  TiDB Cloud and SingleStore Helios, which require TLS, keep the DFS metadata in another database.
- **DFS on the PostgreSQL protocol databases**: `exp:cluster:enable` maps only `DatabaseImplementation` values of
  MySQL, MariaDB and the MySQL protocol databases, PostgreSQL, SQLite, MongoDB, Oracle and SQL Server to a backend.
  A PostgreSQL backend configured by hand on CockroachDB, YugabyteDB, Neon, Supabase or Aurora has not been tried.

## The matrix: requirements and tests

"Data table engine" is the `Engine=` of a named connection in `datatable.ini`
([Data tables](../features/6.0/data-tables.md)). "Import source" says how data comes out of the database into a
site: the kernel's own commands, a data table connection, or the SQLI Import extension's sources
([SQLI Import](sqli-import/README.md)). "xne upgrade" is the upgrade tool's own integration test, which installs a
real 6.0.14 site, upgrades it to the current code and rolls it back.

| Database | PHP extension | Data table engine | Import source | Operating systems | Tested in CI | xne upgrade tested |
|---|---|---|---|---|---|---|
| SQLite 3 | `sqlite3` | `sqlite` | SQLI Import's SQL source (`pdo_sqlite`) | Linux, Windows (CI), macOS | driver tests, PHP 8.1 to 8.5; a whole site on Windows | **yes**: 6.0.14 to main and back, on Linux and in the Windows job |
| PostgreSQL | `pgsql` | `postgresql` | SQL source (`pdo_pgsql`), named connection | Linux, Windows | database tests on 13, 15, 18, 19; DFS on 13 to 19 | **yes**: 6.0.14 to main and back, and a failure rolled back |
| MongoDB | `mongodb` and the library `mongodb/mongodb`; PHP 8.2 and later | `mongodb` | SQLI Import's MongoDB source | Linux, Windows | adapter tests; MongoDB 8.0 in the DFS job | dump, verify and exact restore only; not yet a whole upgrade |
| MariaDB | `mysqli` | `mysql` | SQL source (`pdo_mysql`), named connection | Linux, Windows | through the MySQL jobs (the same driver) | as MySQL (the same driver), not separately |
| Oracle | `oci8` and the `ezoracle` extension | `oracle` | SQL source (`oci8`), named connection | Linux, Windows | the DFS backend on Oracle Free 23 | export, verify and exact restore only; not yet a whole upgrade |
| MySQL | `mysqli` | `mysql` | SQL source (`pdo_mysql`), named connection | Linux, Windows | database tests on MySQL 8.0 | **yes**: 6.0.14 to main and back, and a failure rolled back |
| SQL Server, Azure SQL | `sqlsrv`, `pdo_sqlsrv`, `pdo_odbc` or `pdo_dblib` | `sqlserver` | SQL source (`pdo_sqlsrv`, `pdo_dblib` or PDO_ODBC), named connection | Linux, Windows (LocalDB in CI) | 2017, 2019, 2022, 2025 on three PHP layers, PHP 8.1 to 8.5; LocalDB on Windows | not yet (new in 6.0.15) |
| TiDB, Vitess, SingleStore | `mysqli` | `tidb`, `vitess`, `singlestore` (or `mysql` with the option `profile`) | named connection of engine `mysql` | Linux | live tests in containers (job `mysqlwire`) | not yet |
| Xpand, Aurora MySQL | `mysqli` | `xpand`, `aurora` | named connection of engine `mysql` | Linux | tests without a server only | not yet |
| CockroachDB, YugabyteDB | `pgsql` | `cockroachdb`, `yugabytedb` | a data table connection | Linux | live tests in containers (job "PostgreSQL wire protocol") | not yet |
| Neon, Supabase, Aurora PostgreSQL | `pgsql` | `neon`, `supabase`, `aurorapostgresql` | a data table connection; Supabase also SQLI Import's Supabase (REST) source | Linux | tests without a server only | not yet |
| Spanner (PGAdapter) | `pgsql` | `spanner` | a data table connection (`table_to_content`) | Linux | live tests against the emulator behind PGAdapter | not applicable |
| Firebird | `pdo_firebird` of **PHP 8.4 or later** and Firebird's client library | `firebird` | a data table connection | Linux | live tests on Firebird 4 and 5, PHP 8.4 and 8.5 (job `firebird`) | not yet |
| DuckDB | `ffi` (with `ffi.enable=true` for the web) and `libduckdb` | `duckdb` | a data table connection; SQLI Import's DuckDB source (Parquet, CSV, JSON and DuckDB files) | Linux (`libduckdb.so`); Windows and macOS not tested | live tests on DuckDB 1.5.6 and 2.0.1, and a whole site installed (job `duckdb`) | not yet; no built-in caption (below) |
| ODBC | `pdo_odbc` or `odbc`, and the database's ODBC driver | `odbc` | a data table connection; `exp:access:import` into it | Linux, Windows | live tests on SQL Server, PostgreSQL, MariaDB and SQLite, both layers (job `odbc`); LocalDB on Windows | not applicable |
| MS Access | Windows: `odbc` and `pdo_odbc` with the Access Database Engine (x64); Linux: the MDBTools programs (import only) | `msaccess` (Windows) | `exp:access:import` | Windows (driver and import), Linux and macOS (import with MDBTools) | the import on Linux; the driver and the import on windows-2022 and windows-2025 (job `msaccess-windows`) | not applicable |

The upgrade tool's tests are described in its own documentation; an upgrade from 6.0.14 exists only for the
databases 6.0.14 had. A site installed on one of the new databases is first upgraded by the release after 6.0.15, and
that path has not been tested yet on any of them.

## Backups and upgrade captions

Before an upgrade or a migration run, the upgrade command makes a safety copy of the database (a "caption",
`exp:upgrade:backup`). It has a built-in dump for four engines only; on every other database make your own backup with
the database's tool and run the upgrade with `--no-backup`.

| Database | Built-in caption | Your own backup |
|---|---|---|
| SQLite 3 | `VACUUM INTO` (a copy of the file when the library is too old) | `sqlite3 ... ".backup ..."` |
| PostgreSQL | `pg_dump` of the server's version | `pg_dump -Fc`, `pg_restore` |
| MongoDB | `mongodump` | `mongodump`, `mongorestore` |
| MariaDB, MySQL | `mysqldump --single-transaction` | `mariadb-dump` or `mysqldump` |
| TiDB, Vitess, SingleStore, Xpand, Aurora MySQL | `mysqldump` is run (the driver reports `mysql`); not measured on these databases | the vendor's own backup |
| CockroachDB, YugabyteDB, Neon, Supabase, Aurora PostgreSQL | `pg_dump` is run (the driver reports `postgresql`) | YugabyteDB: **`ysql_dump`** (a dump by PostgreSQL 17's `pg_dump` sets `transaction_timeout`, which YugabyteDB 2.25 refused on restore); CockroachDB, the cloud services: their own backup |
| Oracle | **none**: run with `--no-backup` | `expdp` / `impdp`, or `ext:ezoracle:datapump` |
| SQL Server, Azure SQL | **none** | `BACKUP DATABASE`, or the service's backups |
| Firebird | **none** | `gbak` or `nbackup` |
| DuckDB | **none** ("no built-in dump for duckdb") | copy the file while no process writes, or `EXPORT DATABASE` from the `duckdb` program |

The full procedure per engine: [Backups per engine](database-administrators/backups-per-engine.md).

## Measured timings

What the driver pages report, each on its own machine and day. Most of these hosts were busy with other work while
they measured, so the numbers say how long something took there, not how two databases compare.

| What | Where | Time |
|---|---|---|
| A site package installed (`sevenx_multisite`, 16 steps) | SQLite, kickstarter | 54 to 128 s |
| | SingleStore 10.10 | 279 s |
| | Vitess 25 (transaction timeout raised to 600 s) | 526 s |
| | MongoDB 8.0, kickstarter | 588 to 916 s |
| | TiDB 8.5.1 | 764 s |
| | Oracle 26ai Free, kickstarter | 901 to 1030 s |
| | Firebird 4.0 and 5.0 | 21 and 22 minutes |
| | DuckDB 2.0.1 | 31 minutes |
| | YugabyteDB 2.25 | 42 minutes (slow DDL) |
| | CockroachDB 25.2 | 47 minutes (slow DDL) |
| One statement | Firebird, in its own transaction / inside a transaction | about 20 ms / about 4 ms |
| | DuckDB, a row read by its key / inserted with a commit / inside a transaction | 2.7 ms / 7.4 ms / 2.7 ms |
| A front page | DuckDB, view cache filled / empty | 2.3 to 3.5 s / 17 s |
| An Access file imported (4,103 rows, `exp:access:import`) | into SQLite, MariaDB, PostgreSQL, SQL Server over ODBC | 0.4 s, 2.5 s, 2.6 s, 31 s |

SQLite and MySQL answer a lookup by key in tens of microseconds; DuckDB and Firebird in milliseconds, which is why a
page with hundreds of statements needs the view cache on those two.

## Which database for which use

### One server

- **SQLite 3**, the default: no database server at all, a whole site in one file, a full production database for a
  site served from one machine. Writers queue.
- **PostgreSQL** when you want a database server from the start, or expect a second web server later.
- **MariaDB or MySQL** when the host offers them and nothing else.
- **Firebird 4 or 5** when Firebird is what your organization runs; it needs PHP 8.4 or later and has no cluster
  backend yet, so it stays on one web server.

### Several web servers (a cluster)

The database must hold the DFS file metadata for all web servers: **PostgreSQL** (recommended), **MariaDB/MySQL**,
**Oracle**, **SQL Server/Azure SQL** or **MongoDB**, each with its backend in the kernel. TiDB, Vitess and SingleStore
ran a one-node DFS cluster through the MySQL backend; for a managed service that requires TLS keep the DFS metadata
elsewhere. Not for a cluster: SQLite (one machine), DuckDB (one writer, a local file lock), Firebird (no backend yet),
the PostgreSQL protocol databases (no backend offered by `exp:cluster:enable`).

### A managed cloud database

| You have | Use | Status |
|---|---|---|
| Azure SQL Database or Managed Instance | `sqlserver` | tested against SQL Server; Azure's transient errors are retried |
| Amazon RDS for PostgreSQL, Google Cloud SQL, any PostgreSQL service | `pgsql` | as PostgreSQL |
| Amazon Aurora PostgreSQL | `aurorapostgresql` | profile, **untested** |
| Amazon Aurora MySQL | `aurora` | profile, **untested** |
| Neon, Supabase | `neon`, `supabase` | profiles, **untested**; install through a direct connection or a session pooler |
| PlanetScale | `vitess` (`planetscale`) | measured on Vitess; transactions longer than 20 s are killed, so install elsewhere and import |
| TiDB Cloud, SingleStore Helios | `tidb`, `singlestore` | measured on the self-hosted servers; TLS with `--db-ssl=verify` |
| CockroachDB, YugabyteDB (self-hosted or cloud) | `cockroachdb`, `yugabytedb` | measured on one node each; slow DDL, no DFS backend |
| Google Cloud Spanner | `spanner` as a data table connection | not a site database |

### Embedded, demo and build sites

- **SQLite 3** is the answer for nearly every demo: the installers default to it and it installs in about a minute.
- **DuckDB** keeps a site in one file too, but one process writes at a time and every statement costs milliseconds:
  for a site with one editor, a site that is built once and then served (`AccessMode=read_only`), or a demo of the
  driver itself.

### Analytics, reporting and imports

- **A DuckDB file as a data table connection** (`Engine=duckdb`) keeps analytics next to a site on any database;
  SQLI Import's DuckDB source reads Parquet, CSV and JSON files through DuckDB.
- **Spanner** (through PGAdapter), **Firebird** and **any ODBC database** reach a site as data table connections:
  tables are made, read and written there, and rows go into content through the data table tools.
- **SQLI Import's SQL source** reads a single `SELECT` from MySQL or MariaDB, PostgreSQL, SQLite, Oracle or SQL Server,
  directly or through a named connection of one of those engines.

### Windows

SQLite (the Windows CI installs a whole site on it) and **SQL Server** are the tested choices on Windows: the SQL
Server driver runs over `sqlsrv` or `pdo_sqlsrv`, LocalDB and Express work for development. PostgreSQL, MySQL,
MariaDB, Oracle and MongoDB have Windows builds of their PHP extensions. The **ODBC** driver is tested on Windows
against LocalDB, and **MS Access** files are read and written there through Microsoft's Access Database Engine.
Firebird and DuckDB have not been tried on Windows. See [Exponential on Windows](exponential-on-windows.md).

### Moving off MS Access or another old database

- **From MS Access**: `exp:access:import` copies the tables of an `.accdb` or `.mdb` file into the site's database or
  a data table connection, with MDBTools on Linux (no Access needed) or the Access ODBC driver on Windows. Do not run
  a site on Access; the [MS Access page](../features/6.0/ms-access-support.md) says why.
- **From a database the site cannot run on** (an ERP on Firebird, a CRM behind an ODBC driver, an intranet on SQL
  Server): make it a data table connection and read it in place, or import it.
- **Moving a whole site to another engine**: [Moving off an old database](moving-off-an-old-database.md) and
  [Moving a site to another engine](database-administrators/moving-between-engines.md).

## In development

Drivers for IBM and SAP databases, for Cassandra and ScyllaDB and for ClickHouse and MonetDB are being built. They
are not part of any release, and nothing on this page describes them.

## Related pages

- [Database support](database-support.md): versions, extensions, settings and the support statement.
- [Chapter 9, Databases](../install/09-databases.md) and [chapter 2, Requirements](../install/02-requirements.md).
- The drivers: [SQL Server](../features/6.0/sqlserver-database-driver.md),
  [MySQL protocol](../features/6.0/mysql-wire-databases.md),
  [PostgreSQL protocol](../features/6.0/postgresql-wire-databases.md),
  [Firebird](../features/6.0/firebird-database-driver.md), [DuckDB](../features/6.0/duckdb-database-driver.md),
  [ODBC](../features/6.0/odbc-database-driver.md), [MS Access](../features/6.0/ms-access-support.md).
- [Clustering in Exponential 6.0](../features/6.0/clustering-overview.md), [Data tables](../features/6.0/data-tables.md).
- [PHP extensions per engine](database-administrators/php-extensions-per-engine.md),
  [Choosing an engine](database-administrators/choosing-an-engine.md) (the DBA's questions).
