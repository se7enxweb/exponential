---
title: Database support
audience: admin, developer
category: install-upgrade
order: 10
summary: Which databases Exponential 6.0.x supports and in which order (SQLite first, then PostgreSQL, MongoDB, MariaDB, Oracle, MySQL and Microsoft SQL Server with Azure SQL), the databases added in 6.0.15 (Firebird, DuckDB, libSQL and Turso, the MySQL and PostgreSQL protocol families, ODBC and MS Access), with a support matrix and the versions, PHP extensions, settings, installers, clustering, character sets, performance, backup and restore, limits, migration and test coverage of each.
keywords: database, databases, supported databases, support matrix, Firebird, pdo_firebird, DuckDB, FFI, libSQL, sqld, Turso, Hrana, ODBC, pdo_odbc, MS Access, exp:access:import, TiDB, Vitess, PlanetScale, SingleStore, Xpand, Aurora, CockroachDB, YugabyteDB, Spanner, Neon, Supabase, Azure SQL, expSQLServerDB, pdo_sqlsrv, pdo_odbc, SQLite, SQLite3, PostgreSQL, Postgres, MongoDB, MariaDB, MySQL, Oracle, ezoracle, oci8, pgsql, mysqli, sqlite3, mongodb, Microsoft SQL Server, MSSQL, SQL Server, sqlsrv, xrowmssql, xrow, FreeTDS, dblib, DatabaseImplementation, DatabaseSettings, site.ini, backup, restore, migration, character set, utf8mb4, AL32UTF8, CI, requirements, versions, Couchbase, SQL++, N1QL, CouchDB, Mango, exp:couchbase:import, exp:couchdb:import
featured: true
context: setup/info, setup/systemupgrade, dse
type: help
---
# Database support

This page is for administrators who choose, install and run the database of an Exponential site, and for developers
who write code that has to work on every database. It answers four questions in one place: which databases
Exponential 6.0.x supports, in which order they are recommended, what each one needs, and how well each one is
tested. Every fact on this page was checked against the code and the continuous integration of the 6.0.15 line;
where something could not be verified, the page says so.

The step-by-step operator's chapter, with every setting explained, is
[chapter 9 of the installation guide, Databases](../install/09-databases.md). This page is the overview and the
support statement; it links to that chapter wherever the details are. Every database 6.0.15 reaches, in one matrix
with a decision guide per use case, is on [Choosing a database](choosing-a-database.md).

## Contents

1. [The supported databases, in order](#the-supported-databases-in-order)
2. [Support matrix](#support-matrix)
3. [At a glance](#at-a-glance)
4. [Versions, checked against the code](#versions-checked-against-the-code)
5. [PHP extensions](#php-extensions)
6. [The settings: DatabaseSettings in site.ini](#the-settings-databasesettings-in-siteini)
7. [Installer support per path](#installer-support-per-path)
8. [SQLite 3, the default](#1-sqlite-3-the-default)
9. [PostgreSQL](#2-postgresql)
10. [MongoDB](#3-mongodb)
11. [MariaDB](#4-mariadb)
12. [Oracle](#5-oracle)
13. [MySQL](#6-mysql)
14. [Microsoft SQL Server and Azure SQL](#7-microsoft-sql-server-and-azure-sql)
    - [Databases of the PostgreSQL wire protocol](#databases-of-the-postgresql-wire-protocol)
15. [Databases that speak the MySQL protocol: TiDB, Vitess, SingleStore, Xpand, Aurora](#databases-that-speak-the-mysql-protocol)
16. [Character sets and collations](#character-sets-and-collations)
17. [Performance and scaling](#performance-and-scaling)
18. [Backup and restore](#backup-and-restore)
19. [Moving a site to another database](#moving-a-site-to-another-database)
20. [Engine parity, measured](#engine-parity-measured)
21. [Test coverage (CI)](#test-coverage-ci)
22. [Which database is my site using?](#which-database-is-my-site-using)
23. [Related pages](#related-pages)

## The supported databases, in order

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server and Azure SQL.

This is the project's official order for Exponential 6.0.x. It is the order in which the databases are recommended,
documented and listed throughout the documentation. All seven are supported: every one of them has a driver in the
kernel or in a shipped extension, a path in the command-line installers and a DFS cluster backend in the kernel.

What the order means in practice:

1. **SQLite 3** is the default. The setup wizard preselects it, `exp:install` uses it when no `--db` is given, and
   the kickstarter's template names it first. It needs no database server: the whole content repository is one file.
   With the driver Exponential ships it is a full production database for a site served from one machine.
2. **PostgreSQL** is the recommended database server: strict SQL, row-level locking, replication and point-in-time
   recovery of its own, and it is tested in CI on every push.
3. **MongoDB** is supported through an adapter that translates the kernel's SQL-shaped calls into MongoDB
   operations. It is the choice for organizations standardized on MongoDB; read its
   [limits](#mongodb-limits) before you choose it.
4. **MariaDB** runs on the same driver as MySQL (`mysqli`). It is the MySQL-compatible server the project prefers.
5. **Oracle** (19c and later) is supported through the `ezoracle` extension, which `composer.json` requires, so it
   is present in every installation.
6. **MySQL** is supported on the same driver as MariaDB and is tested in CI. It is last in the order, not because
   it works less well, but because the project recommends the others first for new sites.

7. **Microsoft SQL Server and Azure SQL** (SQL Server 2017 to 2025, Azure SQL Database and Managed Instance) run on
   the kernel's own driver, `expSQLServerDB`, new in 6.0.15, on Linux and on Windows. It is the choice for
   organizations standardized on Microsoft's database ([section 7](#7-microsoft-sql-server-and-azure-sql)).

## Support matrix

| Engine | Versions | PHP extension | Installers | Cluster (DFS metadata) | Tested in CI on every push | Status |
|---|---|---|---|---|---|---|
| SQLite 3 | 3.33 and later | `sqlite3` | setup wizard (the default), kickstarter, `exp:install` (the default) | one machine only (`expDFSFileHandlerSQLiteBackend`) | driver tests on PHP 8.1 to 8.5; the DFS backend | supported, the default |
| PostgreSQL | 13 to 19 | `pgsql` | setup wizard, kickstarter, `exp:install` | any number of servers (`expDFSFileHandlerPostgresqlBackend`), read replicas | 13, 15, 18 and 19 for the site, 13 to 19 for the DFS backend | supported, the recommended server |
| MongoDB | 6.0 and later, PHP 8.2 and later | `mongodb` and the library `mongodb/mongodb` | setup wizard, kickstarter, `exp:install` | any number of servers, one server or a replica set (`expDFSFileHandlerMongoDBBackend`, 5.0 and later) | adapter tests; MongoDB 7 and 8.0 in the database and DFS jobs | supported |
| MariaDB | releases your vendor supports | `mysqli` | setup wizard, kickstarter, `exp:install` | any number of servers (MySQLi backend), read replicas | through the MySQL jobs (the same driver) | supported |
| Oracle | 19c and later | `oci8` | setup wizard, kickstarter, `exp:install` (driver from `ezoracle`) | any number of servers (`expDFSFileHandlerOracleBackend`, 12.1 and later), read replicas | the DFS backend on Oracle Free 23 | supported |
| MySQL | releases your vendor supports | `mysqli` | setup wizard, kickstarter, `exp:install` | any number of servers (the default backend), read replicas | MySQL 8.0 | supported |
| Microsoft SQL Server, Azure SQL | 2017, 2019, 2022, 2025; Azure SQL Database and Managed Instance | `sqlsrv`, `pdo_sqlsrv`, `pdo_odbc` or `pdo_dblib` | setup wizard (with `sqlsrv` or `pdo_sqlsrv`), kickstarter, `exp:install` | any number of servers (`expDFSFileHandlerSQLServerBackend`), Always On read replicas | 2017, 2019, 2022 and 2025 on PHP 8.1 to 8.5 across three PHP layers; LocalDB on Windows | supported (new in 6.0.15) |
| TiDB, Vitess / PlanetScale, SingleStore | TiDB 6.5+, Vitess (an unsharded keyspace), SingleStore 8.5+ | `mysqli` | setup wizard (with `mysqli`), kickstarter, `exp:install` | any number of servers (the MySQLi backend) | TiDB 8.5, Vitess and SingleStore in containers (job `mysqlwire`) | supported (new in 6.0.15): [the MySQL protocol](#databases-that-speak-the-mysql-protocol) |
| MariaDB Xpand, Amazon Aurora MySQL | Xpand 6, Aurora MySQL 3 | `mysqli` | setup wizard (with `mysqli`), kickstarter, `exp:install` | the MySQLi backend | no (no image; a managed service) | drivers and profiles, not measured |
| DuckDB | 1.5 and 2.0 (1.5.6 and 2.0.1 tested) | `ffi` with DuckDB's C library `libduckdb` | setup wizard (with `ffi` and `ffi.enable=true`), kickstarter, `exp:install` | none: one process writes at a time | driver and live tests on DuckDB 1.5.6 and 2.0.1, and a whole site installed | supported for small and read-mostly sites (new in 6.0.15); [what works and what does not](../features/6.0/duckdb-database-driver.md#what-works-and-what-does-not) |
| libSQL (sqld), Turso | sqld 0.22 and later (0.24.33 tested, SQLite 3.47); Turso | `curl` (or `allow_url_fopen`); no `sqlite3` needed | kickstarter, `exp:install` (`libsql`, `turso`) | the database: any number of web servers share it; no DFS backend on it | driver tests without a server, live tests against sqld 0.24.33 and a whole site installed (job `libsql`) | full site database (new in 6.0.15); Turso **untested**; [what works and what does not](../features/6.0/libsql-database-driver.md#what-works-and-what-does-not) |
| MonetDB | Dec2023 and later (Dec2023-SP4 and Dec2025-SP5 tested) | none: MAPI in plain PHP | kickstarter, `exp:install` (`monetdb`) | none: no DFS backend; one writer at a time | driver and live tests on Dec2023-SP4 and Dec2025-SP5, and a whole site installed (job `columnar`) | full site database (new in 6.0.15); [what works and what does not](../features/6.0/columnar-databases.md#what-works-and-what-does-not) |
| ClickHouse | 24.8 and later (24.8 and 25.8 tested) | none: HTTP through `curl` or PHP's HTTP stream | none: not a site database | none | driver and live tests on 24.8 and 25.8 (job `columnar`) | data table connection and import source (new in 6.0.15); [why not a site](../features/6.0/columnar-databases.md#why-not-a-site-database) |
| CockroachDB (PostgreSQL wire protocol) | 23.2 and later | `pgsql` | setup wizard, kickstarter, `exp:install` (`cockroachdb`) | any number of servers (`expDFSFileHandlerPostgresqlBackend`, in the site's database; measured) | the live suite `pgwire-live` on CockroachDB 25.2, PHP 8.1 and 8.5 | full site database (new in 6.0.15) |
| YugabyteDB (PostgreSQL wire protocol) | 2.20 and later | `pgsql` | setup wizard, kickstarter, `exp:install` (`yugabytedb`) | any number of servers (`expDFSFileHandlerPostgresqlBackend`, in the site's database; measured, no lock timeout) | the live suite `pgwire-live` on YugabyteDB 2.25, PHP 8.1 and 8.5 | full site database (new in 6.0.15) |
| Google Cloud Spanner, PostgreSQL dialect (PGAdapter) | the Spanner emulator, PGAdapter | `pgsql` | none: not a site database | none | the live suite `pgwire-live` on the emulator behind PGAdapter | data-table connection and import (new in 6.0.15) |
| Neon, Supabase, Amazon Aurora PostgreSQL | the services' PostgreSQL (13 and later) | `pgsql` | setup wizard, kickstarter, `exp:install` (`neon`, `supabase`, `aurorapostgresql`) | the PostgreSQL backend (`expDFSFileHandlerPostgresqlBackend`), **untested** | profiles and unit tests only; **not live-tested** | full site database as PostgreSQL, **untested**; profiles new in 6.0.15 |
| Firebird | 4.0 and 5.0 | `pdo_firebird` of PHP 8.4 or later | setup wizard (with `pdo_firebird`), kickstarter, `exp:install` (`firebird`) | none yet: one web server | 4.0 and 5.0 on PHP 8.4 and 8.5 (job `firebird`) | full site database (new in 6.0.15): [Firebird driver](../features/6.0/firebird-database-driver.md) |
| Any database through ODBC | the database's ODBC driver; measured on SQL Server 2022, PostgreSQL 17, MariaDB 11.4, SQLite 3.45 | `pdo_odbc` or `odbc` | setup wizard, kickstarter, `exp:install` (`--db=odbc --db-dialect=sqlserver`, `postgresql`, `mysql` or `db2`) | the dialect's: SQL Server and PostgreSQL over PDO_ODBC, MariaDB/MySQL through `mysqli` | live tests on four databases and both layers, whole sites installed on SQL Server, MariaDB and PostgreSQL (job `odbc`); LocalDB on Windows | full site database on SQL Server, PostgreSQL, MariaDB/MySQL and Db2, data table connections and imports (new in 6.0.15): [A site over ODBC](../features/6.0/odbc-database-driver.md#a-site-over-odbc) |
| MS Access (`.accdb`, `.mdb`) | Access 97 (JET3) to ACE | Windows: `odbc`, `pdo_odbc` with the Access Database Engine x64; Linux: MDBTools programs | none: `exp:access:import` | none | the import on Linux; driver and import on Windows (job `msaccess-windows`) | import, and on Windows a small driver for data tables (new in 6.0.15): [MS Access](../features/6.0/ms-access-support.md) |
| IBM Db2 | 11.5 and 12.1 (11.5.9 and 12.1.6 tested) | `pdo_ibm` or `ibm_db2` with IBM's Data Server Driver | setup wizard, kickstarter, `exp:install` (`db2`) | none yet: one web server | driver and live tests on Db2 12.1 and 11.5, on both PHP layers, and a whole site installed | full site database (new in 6.0.15); [what works and what does not](../features/6.0/db2-database-driver.md#what-works-and-what-does-not) |
| IBM Informix | 14.10 and 15 (14.10.FC9W1 and 15.0.1 tested) | `ibm_db2` with IBM's Data Server Driver, through the DRDA listener | setup wizard, kickstarter, `exp:install` (`informix`) | none yet: one web server | driver and live tests on Informix 15 and 14.10; a whole site installed on 15 outside CI | full site database (new in 6.0.15); [what works and what does not](../features/6.0/informix-database-driver.md#what-works-and-what-does-not) |
| Apache Cassandra, ScyllaDB (CQL) | Cassandra 4.0 to 5.0, ScyllaDB 2025.1 and later (5.0.9 and 2025.1.17 tested) | PHP 8.1 and the library `mroosz/php-cassandra` | none: not a site database | none | the live suite `cassandra-live` on Cassandra 5.0 and ScyllaDB 2025.1, PHP 8.1 and 8.5 | data-table connection and import (new in 6.0.15); [why no site](../features/6.0/cassandra-scylladb-driver.md#why-no-site-runs-on-cassandra) |
| Couchbase | Server 7.2 and later, Community or Enterprise (7.6.2 and 7.2.4 tested) | `curl` (the Query service's REST API) | none: not a site database | none | driver and live tests on Community 7.6.2 and 7.2.4 (job `couch`) | data table connections and import (new in 6.0.15): [Couchbase driver](../features/6.0/couchbase-driver.md) |
| Apache CouchDB | 3.3 and 3.4 (3.4.3 and 3.3.3 tested) | `curl` and `sqlite3` | none: not a site database | none | driver and live tests on 3.4.3 and 3.3.3 (job `couch`) | data table connections and import (new in 6.0.15): [CouchDB driver](../features/6.0/couchdb-driver.md) |

**ODBC** (new in 6.0.15) carries a site on SQL Server, PostgreSQL, MariaDB/MySQL and Db2: `DatabaseImplementation=odbc`
with the dialect in `[ODBCSettings]`, installed by every installer, clustered by `exp:cluster:enable`; SQL Server and
Db2 run on their own drivers with ODBC as the layer. Any other database with an ODBC driver is reached as a data table
connection. **MS Access** does not carry a site: `exp:access:import` copies the tables of an Access file into the site's database or a data
table connection, and on Windows the small driver `expMSAccessDB` reads and writes Access files in place. See
[ODBC driver](../features/6.0/odbc-database-driver.md) and [MS Access support](../features/6.0/ms-access-support.md).

**Couchbase and Apache CouchDB** (new in 6.0.15) do not carry a site either; both were measured for it. `expCouchbaseDB`
(`couchbase`) turns the kernel's SQL into Couchbase's SQL++ and answers it in a scope of a bucket, through the Query
service's REST API; `expCouchDBDB` (`couchdb`) keeps tables as documents of a CouchDB database and answers SQL on an
in-memory SQLite copy of the documents a statement needs. Both are data table connections and import sources
(`exp:couchbase:import`, `exp:couchdb:import`), with PHP's `curl` (and `sqlite3` for CouchDB). See
[Couchbase driver](../features/6.0/couchbase-driver.md) and [Apache CouchDB driver](../features/6.0/couchdb-driver.md).

**In development.** Drivers for SAP databases, for Cassandra and ScyllaDB and for ClickHouse and MonetDB are
being built; they are not part of a release yet.

**Firebird** (new in 6.0.15, not in the order above): Firebird 4.0 and 5.0 run a whole site on the kernel's
`expFirebirdDB` (`DatabaseImplementation=firebird`, pdo_firebird of PHP 8.4 or later, `exp:install --db=firebird`),
tested on every push against both versions. There is no DFS cluster backend for it yet and no read replicas: a site
on Firebird runs on one web server. Details and limits: [Firebird driver](../features/6.0/firebird-database-driver.md).

**DuckDB** (`DatabaseImplementation=duckdb`, `exp:install --db=duckdb`) keeps a whole site in one file, reached through
PHP's FFI extension. DuckDB lets one process write at a time, so it suits small and read-mostly sites, single editors,
build and demo sites; there is no DFS cluster on it. See [DuckDB driver](../features/6.0/duckdb-database-driver.md).

**libSQL and Turso** (`DatabaseImplementation=libsql` or `turso`, `exp:install --db=libsql`) keep a site's database on a
libSQL server (`sqld`) or in Turso's cloud, reached over the server's HTTP API with PHP's curl extension. libSQL is a fork
of SQLite: the driver is the SQLite driver with an HTTP transport, the SQL and the schema are SQLite's. Unlike a SQLite
file, the database can be shared by several web servers. See [libSQL and Turso driver](../features/6.0/libsql-database-driver.md).

**IBM Db2 and IBM Informix** (new in 6.0.15, not in the order above): the kernel's `expDb2DB`
(`DatabaseImplementation=db2`, Db2 11.5 and 12.1) and `expInformixDB` (`DatabaseImplementation=informix`, Informix
14.10 and 15 through its DRDA listener) reach their servers over IBM's PECL extensions `pdo_ibm` and `ibm_db2`
(`exp:install --db=db2`, `--db=informix`), tested on every push against both versions of each. A whole site installs and runs on both (Db2 12.1 and Informix 15,
measured). There is no DFS
cluster backend for them and no read replicas. Details, the server preparation and limits:
[IBM Db2 driver](../features/6.0/db2-database-driver.md), [IBM Informix driver](../features/6.0/informix-database-driver.md).
**SAP HANA** (new in 6.0.15, not in the order above): SAP HANA 2.0 SPS 05 and later runs a whole site on the
kernel's `expHANADB` (`DatabaseImplementation=hana`, SAP's HANA ODBC driver through PHP's `odbc` extension or
PDO_ODBC, `exp:install --db=hana`; the database is a schema), measured on HANA, express edition 2.00.088 and tested on
every push against it. No DFS cluster backend and no read replicas yet. Details and limits:
[SAP HANA driver](../features/6.0/hana-database-driver.md).

**SAP ASE** (Sybase, new in 6.0.15): the kernel's `expASEDB` (`DatabaseImplementation=ase`, FreeTDS's `pdo_dblib` or
PDO_ODBC) is written for ASE 15.7 and 16.0 and tested without a server only: no ASE server could be had. See
[SAP ASE driver](../features/6.0/ase-database-driver.md).

**Apache Cassandra and ScyllaDB** (`cassandra`, `scylladb`, new in 6.0.15) are data table connections and an import
source (`exp:cassandra:import`), not site databases: CQL has no joins, no sub-selects and no transactions across
rows, and the kernel resolves every URL with a join. See [Cassandra and ScyllaDB driver](../features/6.0/cassandra-scylladb-driver.md).

**MonetDB and ClickHouse** (new in 6.0.15), two columnar databases reached without a PHP extension. MonetDB
(`DatabaseImplementation=monetdb`, `exp:install --db=monetdb`) holds a whole site; its driver lets one writer write at
a time, as MonetDB's optimistic transactions would otherwise fail one of two writers of a table, and every statement
costs milliseconds. ClickHouse (`clickhouse`) is a data table connection and an import source: it enforces no keys and
has no transactions over statements, so a site cannot run on it. See
[Columnar databases: MonetDB and ClickHouse](../features/6.0/columnar-databases.md).

Moving an existing site from one engine to another: [Moving off an old database](moving-off-an-old-database.md).

## At a glance

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL | 7. SQL Server |
|---|---|---|---|---|---|---| --- |
| `DatabaseImplementation` | `sqlite3` | `ezpostgresql` | `mongodb` | `ezmysqli` | `ezoracle` | `ezmysqli` | `sqlserver` |
| Driver class | `eZSQLite3DB` | `eZPostgreSQLDB` | `expMongoDB` | `eZMySQLiDB` | `eZOracleDB` | `eZMySQLiDB` | `expSQLServerDB` |
| PHP extension | `sqlite3` | `pgsql` | `mongodb` + library `mongodb/mongodb` | `mysqli` | `oci8` | `mysqli` | `sqlsrv`, `pdo_sqlsrv`, `pdo_odbc` or `pdo_dblib` |
| Database server | none (one file) | yes | yes | yes | yes | yes | yes |
| Installer default | yes | | | | | |  |
| Setup wizard | yes | yes | yes (slow step) | yes | yes | yes | yes (with `sqlsrv` or `pdo_sqlsrv`) |
| `exp:install --db=` | `sqlite` (default) | `pgsql` | `mongodb` | `mariadb` | `oracle` | `mysql` | `sqlserver` |
| Several web servers on one database | no | yes | yes | yes | yes | yes | yes |
| Character set | UTF-8 | `UTF8` | UTF-8 (BSON) | `utf8mb4` | `AL32UTF8` | `utf8mb4` | UTF-8 collation (`_UTF8`, 2019 and later) or `NVARCHAR` |
| Schema update files | `update/database/sqlite/` | `update/database/postgresql/` | none (no SQL) | `update/database/mysql/` | in `ezoracle` | `update/database/mysql/` | none yet (the schema is `kernel/sql/sqlserver/`) |
| Tested in the project's CI | driver unit tests | yes (PostgreSQL 13, 15, 18 and 19) | adapter unit tests | no | no | yes (MySQL 8.0) | yes (2017, 2019, 2022, 2025; LocalDB on Windows) |
| DFS cluster metadata backend (several web servers) | one machine only: in the kernel (`expDFSFileHandlerSQLiteBackend`) for a one-node cluster and tests | yes, in the kernel (`expDFSFileHandlerPostgresqlBackend`, PostgreSQL 13-19 in CI) | yes, in the kernel (`expDFSFileHandlerMongoDBBackend`, MongoDB 5.0 and later) | yes, in the kernel (MySQLi backend) | yes, in the kernel (`expDFSFileHandlerOracleBackend`, Oracle 12.1 and later) | yes, in the kernel (MySQLi backend) | yes, in the kernel (`expDFSFileHandlerSQLServerBackend`, SQL Server 2017 and later, Azure SQL) |

## Versions, checked against the code

Three different numbers exist for each database, and they are easy to mix up:

- **Wizard floor**: the oldest server version the setup wizard accepts, from `eZSetupDatabaseMap()` in
  `kernel/setup/ezsetupcommon.php`. It is a technical floor kept from earlier releases, not a recommendation.
- **Practical minimum**: the oldest version the current drivers really need, as stated by the driver code and the
  driver documentation.
- **Seen working**: the versions the project runs on its development server and in CI.

| Database | Wizard floor | Practical minimum | Seen working |
|---|---|---|---|
| SQLite 3 | 3.0.1 | **3.33** (the driver rewrites MySQL's `UPDATE ... JOIN` into `UPDATE ... FROM`, which SQLite has from 3.33; `eZSQLite3DB::rewriteJoinedUpdate()`) | library 3.34.1 in PHP 8.5 on the development server; the SQLite library of the CI images (version not pinned) |
| PostgreSQL | 8.0 | **13** (the installer creates `pgcrypto`, a trusted extension from PostgreSQL 13 on); from 15 on the site's user must own the database | 13.23 on the development server; 13, 15, 18 and 19 in CI (every suite), 13 to 19 for the DFS cluster backend; 18.7 and 19.0 also checked for install, upgrade backup, the database stage of the upgrade, the MySQL to PostgreSQL conversion and the read replica |
| MongoDB | 4.0 | **6.0** server, PHP **8.2** or later, PHP `mongodb` extension **1.18** or later ([MongoDB as the database](../features/6.0/mongodb-database-support.md)) | server 8.3.13 with extension 2.3.3 on PHP 8.5 on the development server |
| MariaDB | 4.1.1 (the MySQL number) | a release your vendor still supports; `utf8mb4` and InnoDB | 10.5.29 on the development server |
| Oracle | 19.0 | **19c** (the driver uses `STANDARD_HASH` and `OFFSET`/`FETCH`; 11g and older are not supported) | Oracle AI Database 26ai Free (23.26) with oci8 3.4.1 and Instant Client 23.26 on PHP 8.5 |
| MySQL | 4.1.1 | a release your vendor still supports; `utf8mb4` and InnoDB | 8.0 in CI |
| SQL Server, Azure SQL | 14.0 | **SQL Server 2017** (`STRING_AGG`, `TRIM`, `CONCAT_WS`); 2016 and older connect with a warning, Azure SQL Edge is retired | 2022 CU28 on the development server; 2017, 2019, 2022 and 2025 in CI |
| TiDB | 5.7.25 (what TiDB reports as its MySQL version) | **6.5** | 8.5.1 in CI |
| Vitess, PlanetScale | 5.7.0 | an unsharded keyspace | Vitess 25 `vttestserver` in CI |
| SingleStore | 5.7.0 | **8.5** | 10.10 in CI |
| MariaDB Xpand, Aurora MySQL | 5.0.45, 8.0.0 | Xpand 6, Aurora MySQL 3 (by the vendors' documentation) | none: not measured |
| CockroachDB | 23.2 | **23.2** (`READ COMMITTED`) | 25.2 in CI |
| YugabyteDB | 11.2 (the PostgreSQL it is built on) | **2.20** | 2.25 in CI |
| Neon, Supabase, Aurora PostgreSQL | 14.0, 14.0, 13.0 | as the service's PostgreSQL | none: not measured |
| Firebird | 4.0 | **4.0** (names over 31 characters, `CRYPT_HASH()`); PHP **8.4** for `pdo_firebird` | 4.0 and 5.0.4 in CI |
| DuckDB | 1.5 | **1.5** (the stable C API) | 1.5.6 and 2.0.1 in CI |
| libSQL (sqld), Turso | 3.35 (the server's `sqlite_version()`) | **sqld 0.22** (Hrana 3; older servers in Hrana 2) | sqld 0.24.33 (SQLite 3.47.0) in CI |

Use a server version your vendor still supports with security fixes. The floors exist so that the wizard does not
refuse an old server outright; they are not a promise that a server of that age works with the current drivers.

**PHP.** Exponential 6.0.x runs on PHP 8.0 to 8.5 (PHP 7.4 usable without support) (`composer.json`, `"php": "^7.4 || ... || ^8.8"`). The database
drivers follow that range, with one exception: the MongoDB adapter needs PHP 8.2 or later. Velocity, the recommended
web server, needs PHP 8.0. See [chapter 2](../install/02-requirements.md#22-php).

## PHP extensions

One database extension is required; the setup wizard's critical test `database_extensions` passes when at least one
of `sqlite3`, `mysqli`, `pgsql`, `mongodb`, `oci8`, `sqlsrv`, `pdo_sqlsrv`, `pdo_firebird`, `pdo_ibm`, `ibm_db2` and `ffi` is loaded
(`settings/setup.ini`, `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8;sqlsrv;pdo_sqlsrv;pdo_firebird;pdo_ibm;ibm_db2;ffi`,
`Require=one`). The wizard's database page then offers the databases of every extension it finds: `sqlite3`, `mysqli`
(with the MySQL protocol databases), `pgsql` (with the PostgreSQL protocol databases), `mongodb`, `oci8`, `sqlsrv` or
`pdo_sqlsrv` (SQL Server), `pdo_firebird`, `pdo_ibm` or `ibm_db2` (Db2, Informix) and `ffi` (DuckDB). `ffi` counts only
where FFI may be used in the process: with `ffi.enable=true`. PHP's default `preload` allows FFI on the command line
only, so the web wizard then does not count `ffi` and does not offer DuckDB; DuckDB also needs `libduckdb`
(`[DuckDBSettings] Library`). The system check has an explanation page for each database extension it may report
missing. Install only
the extension your site uses.

| Database | Extension | Where it comes from |
|---|---|---|
| SQLite 3 | `sqlite3` | bundled with PHP; RHEL family `php-pdo` (it also carries `pdo_sqlite`), Debian and Ubuntu `php<version>-sqlite3`. `pdo_sqlite` is not needed by the kernel; the Exponential Platform installers use it. |
| PostgreSQL | `pgsql` | bundled with PHP; `php-pgsql`, `php<version>-pgsql` |
| MongoDB | `mongodb` (PECL) and the Composer library `mongodb/mongodb` | `pecl install mongodb` or `php-pecl-mongodb`; the library with `composer require mongodb/mongodb` (`composer.json` only suggests it). CI installs `mongodb/mongodb` `^1.21 || ^2.0`. |
| MariaDB, MySQL | `mysqli` | bundled with PHP (mysqlnd); `php-mysqlnd`, `php<version>-mysql` |
| SQL Server, Azure SQL | `sqlsrv` or `pdo_sqlsrv` (PECL, Microsoft), or `pdo_odbc` with Microsoft's ODBC Driver 17 or 18, or `pdo_dblib` (FreeTDS) | Microsoft's packages for Linux and its DLLs for Windows; `pdo_odbc` and `pdo_dblib` ship with PHP ([layers](../features/6.0/sqlserver-database-driver.md#supported-servers-and-php-layers)) |
| Oracle | `oci8` (PECL) | PECL oci8 3.4 for PHP 8.4 and 8.5, 3.2 for PHP 8.1 to 8.3, built against an Oracle client (the Instant Client is enough); EPEL, Remi or your vendor |
| TiDB, Vitess, SingleStore, Xpand, Aurora MySQL | `mysqli` | as MySQL |
| CockroachDB, YugabyteDB, Spanner, Neon, Supabase, Aurora PostgreSQL | `pgsql` | as PostgreSQL |
| Firebird | `pdo_firebird` of **PHP 8.4 or later** | bundled with PHP; needs Firebird's client library (`libfbclient2`; `firebird-dev` to build PHP with it). PHP 8.3 and older: its `pdo_firebird` keeps a transaction open in autocommit mode and is refused |
| DuckDB | `ffi` and DuckDB's C library `libduckdb` | `ffi` is bundled with PHP (`php-ffi`, `php<version>-ffi`); `ffi.enable=true` in the web PHP's pool (the default `preload` allows FFI on the command line only); `libduckdb.so` from DuckDB's releases |
| libSQL (sqld), Turso | `curl`, or `allow_url_fopen=On` without it | bundled with PHP (`php-curl`, `php<version>-curl`); PHP's `sqlite3` is not needed for a site on a libSQL server |
| ODBC | `pdo_odbc` (default) or `odbc` | bundled with PHP; unixODBC and the database's ODBC driver on Linux |
| MS Access | Windows: `odbc` and `pdo_odbc` with the Access Database Engine 2016 x64; Linux: the MDBTools programs (`mdb-tables`, `mdb-schema`, `mdb-json`) for the import | Microsoft's redistributable; `mdbtools` (Debian, Ubuntu, EPEL) |
| Couchbase, Apache CouchDB | `curl`; CouchDB also `sqlite3` | bundled with PHP (`php-curl`, `php<version>-curl`); no Couchbase SDK and no CouchDB extension is needed |

Check what a PHP has with `php -m`, and remember that the web server's PHP and the command line PHP are configured
separately: check both. The optional wizard test `database_all_extensions` lists the database extensions that are
missing; it is information only.

## The settings: DatabaseSettings in site.ini

The connection is configured in `settings/site.ini`, block `[DatabaseSettings]`, and overridden (never edited in
place) in `settings/override/site.ini.append.php` or a siteaccess's `site.ini.append.php`. The installers write the
override for you. `DatabaseImplementation` names the driver by an alias that `ImplementationAlias[]` maps to a class:

| Alias in `DatabaseImplementation` | Class |
|---|---|
| `sqlite3` | `eZSQLite3DB` (`lib/ezdb/classes/ezsqlite3db.php`) |
| `ezpostgresql`, `postgresql`, `pgsql` | `eZPostgreSQLDB` (`lib/ezdb/classes/ezpostgresqldb.php`) |
| `mongodb` | `expMongoDB` (`lib/ezdb/classes/expmongodb.php`) |
| `ezmysqli`, `mysqli`, `ezmysql`, `mysql` | `eZMySQLiDB` (`lib/ezdb/classes/ezmysqlidb.php`) |
| `ezoracle`, `oracle` | `eZOracleDB` (in the `ezoracle` extension, registered by its own `site.ini.append.php`) |
| `duckdb` | `expDuckDBDB` (`lib/ezdb/classes/expduckdbdb.php`) |
| `libsql`, `sqld`; `turso` | `expLibSQLDB` (`lib/ezdb/classes/explibsqldb.php`), `expTursoDB` (`lib/ezdb/classes/exptursodb.php`) |
| `sqlserver`, `mssql`, `sqlsrv` | `expSQLServerDB` (`lib/ezdb/classes/expsqlserverdb.php`) |
| `firebird`, `fb` | `expFirebirdDB` (`lib/ezdb/classes/expfirebirddb.php`) |
| `tidb`; `vitess`, `planetscale`; `singlestore`, `memsql`; `xpand`; `aurora`, `auroramysql` | `expTiDBDB`, `expVitessDB`, `expSingleStoreDB`, `expXpandDB`, `expAuroraMySQLDB` (`expMySQLWireDB`) |
| `cockroachdb`, `cockroach`, `crdb`; `yugabytedb`, `yugabyte`, `ysql`; `spanner`, `spannerpg`, `pgadapter`; `neon`, `neondb`; `supabase`; `aurorapostgresql`, `aurora-postgresql` | `expCockroachDB`, `expYugabyteDB`, `expSpannerPGDB`, `expNeonDB`, `expSupabaseDB`, `expAuroraPostgreSQLDB` (`expPostgreSQLWireDB`) |
| `odbc` | `expODBCDB` (`lib/ezdb/classes/expodbcdb.php`) |
| `msaccess`, `access` | `expMSAccessDB` (`lib/ezdb/classes/expmsaccessdb.php`) |
| `db2`, `ibm_db2` | `expDb2DB` (`lib/ezdb/classes/expdb2db.php`) |
| `informix`, `ifx` | `expInformixDB` (`lib/ezdb/classes/expinformixdb.php`) |
| `cassandra`; `scylladb`, `scylla` | `expCassandraDB`; `expScyllaDB` (`lib/ezdb/classes/expcassandradb.php`, `expscylladb.php`; data table connections only) |
| `couchbase` | `expCouchbaseDB` (`lib/ezdb/classes/expcouchbasedb.php`), data table connections only |
| `couchdb` | `expCouchDBDB` (`lib/ezdb/classes/expcouchdbdb.php`), data table connections only |
| `monetdb` | `expMonetDBDB` (`lib/ezdb/classes/expmonetdbdb.php`) |
| `clickhouse` | `expClickHouseDB` (`lib/ezdb/classes/expclickhousedb.php`); data table connections only |
| `hana`, `saphana` | `expHANADB` (`lib/ezdb/classes/exphanadb.php`) |
| `ase`, `sybase` | `expASEDB` (`lib/ezdb/classes/expasedb.php`) |

The examples below use placeholder names and passwords; put your own values in your override file and never commit
that file. Every key is explained in [9.1 How Exponential chooses a database driver](../install/09-databases.md#91-how-exponential-chooses-a-database-driver).

**1. SQLite 3**

```ini
[DatabaseSettings]
DatabaseImplementation=sqlite3
# a plain name lives in var/storage/sqlite3/; a name starting with / is an absolute path
Database=exponential.db
# seconds a transaction waits for the writers ahead of it; keep it below the web server's request timeout
SQLiteTransactionWait=25
# PRAGMAs over the driver's defaults, one name=value per line (empty keeps the defaults)
SQLitePragmas[]
```

**2. PostgreSQL**

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential
UsePersistentConnection=disabled
```

**3. MongoDB**

```ini
[DatabaseSettings]
DatabaseImplementation=mongodb
Server=localhost
Port=27017
# required, also on a server without access control
User=exponential
Password=<the password>
Database=exponential
```

**4. MariaDB and 6. MySQL**

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=exponential
Password=<the password>
Database=exponential
# a Unix socket instead of host and port
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
```

**5. Oracle**

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=exponential
Password=<the password>
# the connect string: Easy Connect host:port/service_name, a TNS alias or a full descriptor;
# Server and Port are not used by this driver
Database=127.0.0.1:1521/FREEPDB1
Charset=utf-8

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

After a change, clear the INI cache so every process reads it, and restart Velocity's workers if Velocity serves the
site:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

Read the effective value with `php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation`. A
mistyped alias does not stop the request at once: the kernel falls back to a null database and logs "Database
implementation not supported" in `var/log/error.log`, and every content page then fails. Read `error.log` first when
a site behaves as if its database were down.

## Installer support per path

Exponential has three installers in the kernel. All three support the first seven databases (SQL Server included),
Firebird, DuckDB and the MySQL and PostgreSQL protocol databases; the setup wizard offers each one when the PHP running
it has the database's extension. libSQL and Turso are installed with the kickstarter and `exp:install`. Over ODBC all three install SQL Server,
PostgreSQL, MariaDB/MySQL and Db2 (`--db=odbc` with `--db-dialect`). MS Access and Spanner are not site databases and
have no installer entry.

| Database | Setup wizard (browser) | Kickstarter (`kickstart.ini`, `[database_choice] Type=`) | `exp:install --db=` |
|---|---|---|---|
| 1. SQLite 3 | yes, listed first and preselected (`settings/setup.ini [DatabaseSettings] DefaultType=sqlite3`); proposes `sqlite.db` | `sqlite3` (the default in `kickstart.ini-dist`) | `sqlite` (the default; also `sqlite3`), file `exponential.db` |
| 2. PostgreSQL | yes; prefills port 5432 and user `postgres`; creates `pgcrypto` or tells you how | `pgsql` (`postgresql` accepted) | `pgsql` (also `postgresql`, `postgres`) |
| 3. MongoDB | yes; the configuration step can take one to three minutes, so raise PHP-FPM's `request_terminate_timeout` or use the console | `mongodb` | `mongodb` (also `mongo`) |
| 4. MariaDB | yes, as "MySQL Improved"; checks that the database is UTF-8 | `mysqli` (`mysql` accepted) | `mariadb` (the same as `mysql`) |
| 5. Oracle | yes, when `oci8` is loaded and `extension/ezoracle` is present; activates the extension for the new site | `oci8` (`oracle` accepted); `Database` is the connect string | `oracle` (also `oci8`, `ezoracle`); `--db-name` is the service name, default `FREEPDB1` |
| 6. MySQL | yes, as "MySQL Improved" | `mysqli` (`mysql` accepted) | `mysql` (also `mysqli`) |
| 7. SQL Server, Azure SQL | yes, when `sqlsrv` or `pdo_sqlsrv` is loaded; prefills port 1433 and user `sa` | `sqlserver` (`mssql`, `sqlsrv` accepted), with `Encrypt` and `TrustServerCertificate` | `sqlserver` (also `mssql`, `sqlsrv`); `--db-encrypt`, `--db-trust-server-certificate`; the database must exist |
| CockroachDB, YugabyteDB, Neon, Supabase, Aurora | yes, listed after PostgreSQL when `pgsql` is loaded; prefills port 26257 (CockroachDB) or 5433 and user `yugabyte` (YugabyteDB) | `cockroachdb`, `yugabytedb`, `neon`, `supabase`, `aurorapostgresql`, with `SSLMode`, `SSLRootCert`, `Pooler`, `Endpoint` | the same names; `--db-sslmode`, `--db-sslrootcert`, `--db-pooler`, `--db-endpoint` |
| TiDB, Vitess / PlanetScale, SingleStore, Xpand, Aurora MySQL | yes, listed after MySQL when `mysqli` is loaded; TiDB prefills port 4000 | `tidb`, `vitess` (`planetscale`), `singlestore` (`memsql`), `xpand`, `aurora` (`aurora-mysql`, `auroramysql`), with `SSL` and `SSLCA` | the same names; `--db-ssl=enabled` or `verify`, `--db-ssl-ca`; on Vitess the keyspace must exist |
| Firebird | yes, when `pdo_firebird` is loaded | `firebird` (`fb`, `pdo_firebird` accepted) | `firebird` (also `fb`); the database must exist (`PAGE_SIZE 16384`, UTF8); default port 3050, user `SYSDBA` |
| DuckDB | yes, when `ffi` may be used (`ffi.enable=true`) and `libduckdb` is found; asks only for a file name (default `exponential.duckdb` in `var/storage/duckdb`) | `duckdb`; `Database` is the file | `duckdb`; `--db-name` is the file (default `var/storage/duckdb/exponential.duckdb`) |
| libSQL (sqld), Turso | | `libsql` (`sqld` accepted), `turso`, with `URL` and `TLS`; `Password` is the auth token | `libsql`, `turso`; `--db-url`, `--db-tls`; port 8080 (443 for turso); the server's database is emptied first |
| Over ODBC: SQL Server, PostgreSQL, MariaDB/MySQL, Db2 | yes, when `pdo_odbc` or `odbc` is loaded: one entry per database, "(ODBC)" | `odbc` with `Dialect=sqlserver`, `postgresql`, `mysql` or `db2` (also `odbc_sqlserver`, `odbc_pgsql`, `odbc_mysql`, `odbc_db2`); `ODBCDriver`, `DataSourceName`, `ConnectionString` | `odbc` with `--db-dialect`; `--db-odbc-driver`, `--db-dsn` (PostgreSQL, MySQL), `--db-connection-string` ([A site over ODBC](../features/6.0/odbc-database-driver.md#a-site-over-odbc)) |

- If the `sqlite3` extension is missing, the wizard chooses the first database whose extension is loaded and says
  so.
- None of the installers creates a database server's database or user. Create them first; the per-database sections
  below show how.
- `exp:install` takes the password from the environment variable `EXP_INSTALL_DB_PASSWORD`, so it stays out of the
  shell history and the process list.
- **Demo installs.** This repository has no separate demo installer; demonstration sites are installed with the same
  commands (`exp:install` or the kickstarter), usually on SQLite.
- **Exponential Platform** (the Symfony-based sister product, not the 6.0.x kernel) has its own SQLite installer
  that uses `pdo_sqlite`; see [SQLite for Exponential Platform](../features/6.0/platform-sqlite-install.md).

The installers are described in [chapter 5, the setup wizard](../install/05-setup-wizard.md),
[chapter 6, the kickstarter](../install/06-kickstarter.md) and [chapter 7, the console install](../install/07-console-install.md).

## 1. SQLite 3, the default

**What it is.** The whole content repository in one file below `var/storage/sqlite3/`, opened by the driver
`eZSQLite3DB`. No server process, no network, no user accounts to manage.

**What the driver adds to SQLite.** Write-ahead logging (WAL), so page views keep reading while an editor publishes;
connection PRAGMAs chosen for a web server (`synchronous=NORMAL`, a 64 MB page cache, a 256 MB memory map, a
5-second busy timeout); the functions SQLite lacks (`md5()`, the `BIT_OR` and `BIT_AND` aggregates the content engine
uses for language masks); a rewrite of MySQL's `UPDATE ... JOIN`; and, since 6.0.15, a **write queue**: every
transaction takes an exclusive lock on `<database>.writer-lock` and then runs `BEGIN IMMEDIATE`, so concurrent
publishes wait their turn (up to `SQLiteTransactionWait` seconds, default 25) instead of failing with "database is
locked". MySQL-only session statements (`SET NAMES`, `SET FOREIGN_KEY_CHECKS` and similar) are accepted and ignored.

**When it fits.** One machine, any number of readers, an editorial team. The project's own load tests had concurrent
readers scale with the number of processes and 16 concurrent writers complete without errors, queued. Choose a
database server instead when more than one machine must write the database (WAL needs shared memory on one host, so
a network file system is not a substitute), when many editors publish large changes at the same moment all day, or
when you need replication or database-level users and grants.

**Files and permissions.** The database is four files: the database, `-wal`, `-shm` and `.writer-lock`. Every process
that uses the site (the web server's PHP, Velocity's workers, cronjobs, console commands) must be able to write the
directory and the files; give them one common group, the directory mode `2775` and a umask of `0002`. Never serve the
directory over HTTP (the shipped rewrite rules do not).

**Limits.** One host; one writer at a time (queued); no replication of its own; access control is the file system's.

The full treatment, with every PRAGMA, the queue's behavior under load, monitoring and maintenance (`ANALYZE`,
`VACUUM`, the WAL file), is [9.2 SQLite 3: a production database in one file](../install/09-databases.md#92-sqlite-3-a-production-database-in-one-file).
The task-oriented introduction is [SQLite database support](../features/6.0/sqlite-database.md), the driver
specification [The SQLite3 database driver](../specifications/6.0/sqlite3-database-driver.md).

**Clustering.** A one-node DFS cluster (the PHP servers of one host on one share) can keep its metadata in SQLite with
the kernel's `expDFSFileHandlerSQLiteBackend`, the tables of `kernel/sql/sqlite/cluster_dfs_schema.sql` and the gateway
`dfssqlite`; `exp:cluster:enable` sets it up on a SQLite site. Several web servers cannot share a SQLite file safely:
[Clustering on SQLite](../features/6.0/sqlite-dfs-cluster.md).

## 2. PostgreSQL

**What it is.** The driver `eZPostgreSQLDB` on the PHP extension `pgsql`.

**Create the database.** Make the site's user the **owner** of the database (from PostgreSQL 15 on, other users may
no longer create tables in its `public` schema):

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

**pgcrypto.** The driver computes MD5 with `digest()` from `pgcrypto`. The installer runs
`CREATE EXTENSION IF NOT EXISTS pgcrypto`; as a trusted extension (PostgreSQL 13 and later) the database owner may
create it. If the server lacks it, install your distribution's PostgreSQL contrib package and create it as the owner
or a superuser. The wizard's message when it is missing reads "The 'digest' function is not available in your
database, and Exponential cannot run without it."

**Sequences.** Auto-increment columns use sequences named `<table>_<column>_seq`. After loading data with explicit
ids, correct them (`SELECT setval('ezcontentobject_id_seq', (SELECT max(id) FROM ezcontentobject));`); the schema
installer does this itself after it loads data.

**Limits.** None specific to Exponential's own code. Extension code written for MySQL (backtick quoting, MySQL-only
`SET` statements) fails here; see [cross-engine notes](../install/09-databases.md#98-cross-engine-notes).

Details: [9.4 PostgreSQL](../install/09-databases.md#94-postgresql).

**Clustering.** A site on several web servers can keep its DFS cluster metadata in PostgreSQL 13 or later with the
kernel's `expDFSFileHandlerPostgresqlBackend`, the tables of `kernel/sql/postgresql/cluster_dfs_schema.sql` and the
gateway `dfspostgresql`, with no extension. It refactors and integrates the ezpostgresqlcluster extension by eZ
Systems (GPL-2.0) and 7x's fork of it: [Clustering on PostgreSQL](../features/6.0/postgresql-dfs-cluster.md), with
example configurations for PostgreSQL and MySQL/MariaDB.

## 3. MongoDB

**What it is.** The adapter `expMongoDB` on the PHP extension `mongodb` and the library's `MongoDB\Client`. It
translates the kernel's SQL-shaped calls into `find`, `aggregate`, insert, upsert and delete operations. MongoDB
arrived with 6.0.14; its schema handler is `expMongoSchema` (`settings/dbschema.ini`, type `mongo`).

**Create the user** in the site's own database (the adapter always puts `<user>:<password>@` into the connection URI,
and MongoDB uses the database in the URI as the authentication database):

```bash
mongosh --eval '
  db.getSiblingDB("exponential").createUser({
    user: "exponential",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "exponential" }, { role: "dbAdmin", db: "exponential" } ]
  })'
```

**After installing,** create the indexes the kernel's queries rely on
(`mongosh "mongodb://exponential@localhost:27017/exponential" --file bin/mongodb/create_indexes.js`), then run the
cronjobs once to build the search index.

**Clustering.** A site on one or several web servers can keep its DFS cluster metadata in MongoDB 5.0 or later with
the kernel's `expDFSFileHandlerMongoDBBackend`, the collections of `kernel/sql/mongodb/cluster_dfs_schema.json` and the
gateway `dfsmongodb`, on a single server or a replica set, with only the PHP extension; `exp:cluster:enable` sets it up
on a MongoDB site: [Clustering on MongoDB](../features/6.0/mongodb-dfs-cluster.md).

### MongoDB limits

From [MongoDB: limits and operations](database-administrators/mongodb-limits-and-operations.md):

- SQL the adapter cannot translate into MongoDB operations (joins, `GROUP BY` with `HAVING`, `UNION`, sub-selects)
  is answered by its **SQL fallback** since 6.0.15: the collections the statement names are copied into an
  in-memory SQLite database and the statement runs there (`site.ini [DatabaseSettings] MongoSQLFallback`, on by
  default; it needs PHP's `sqlite3`). A collection with more documents than `MongoSQLFallbackMaxRows` (200000) is
  not copied, and such a statement, like a write that needs a join, is refused and logged.
- The kernel's features, the admin views, `eztags`, the cronjobs and the installers give the same results as on
  the SQL engines ([Engine parity, measured](#engine-parity-measured)).
- The SQL query cache does not apply.
- There are no SQL update files. The upgrade check compares collections instead of tables and prints the `mongosh`
  commands that create the missing ones.

Details: [9.5 MongoDB](../install/09-databases.md#95-mongodb).

## 4. MariaDB

**What it is.** The same driver as MySQL, `eZMySQLiDB`, on the PHP extension `mysqli`. Everything in
[6. MySQL](#6-mysql) applies; the notes here are what differs.

- Install with `exp:install --db=mariadb` (the same as `--db=mysql`) or choose "MySQL Improved" in the wizard.
- Create the database in `utf8mb4` with an InnoDB default, as for MySQL. In MariaDB, as in MySQL, the connection name
  `utf8` means the three-byte `utf8mb3`; see [Character sets and collations](#character-sets-and-collations).
- Backups with `mariadb-dump` (or the older name `mysqldump`) `--single-transaction`.
- MariaDB is not in the project's CI; the MySQL 8.0 job exercises the same driver. MariaDB 10.5 runs on the
  development server.

## 5. Oracle

**What it is.** The `ezoracle` extension (required by `composer.json` as `~2.3.9`; 2.3.14 at the time of writing) provides the driver
`eZOracleDB`, the schema handler `eZOracleSchema`, console commands (`ext:ezoracle:<name>`) and cronjobs. Because
`composer.json` requires it, it is in `extension/ezoracle` in every installation; it is activated only for sites that
use Oracle.

**Requirements.** Oracle Database 19c or later in the character set `AL32UTF8`; PHP with `oci8` built against an
Oracle client (the Instant Client is enough; `ORACLE_HOME` is not needed with it); a user that may create sessions,
tables, views, triggers, sequences and procedures. `php extension/ezoracle/bin/php/ora-grant.php` prints the grants
for a DBA to run.

**Connections.** `ezoracle.ini [ConnectionSettings]` adds persistent connections, Database Resident Connection
Pooling (DRCP, so hundreds of PHP workers share a pool of server processes), retries, failover, call timeouts and
session tracing. Every feature is off by default. When the settings come from a site extension instead of
`settings/override/`, list that extension **before** `ezoracle` in `ActiveExtensions[]`.

**Oracle's own behavior to know.** Oracle stores `''` as NULL (`OracleEmptyStringForNull=enabled` returns `''` for
text columns); it compares strings case-sensitively (`OracleCaseInsensitive=enabled` with linguistic indexes from
`ext:ezoracle:ci-indexes --create`); and it rejects `AS` before a table alias.

**Since 6.0.15** the driver sends a comparison with the empty string as `IS NULL` / `IS NOT NULL` (Oracle keeps
`''` as NULL, and on a CLOB column the comparison failed with ORA-22848), the search index is written in batches
on Oracle too (an installation of the demo site takes about a third less time), and rows of extension data files
reach the columns Oracle renames (`ezurl_object_link.contentobject_attr_version`).

**Limits.** Exponential 6.0.15 or later is needed (earlier versions do not offer Oracle in the installers). Oracle is
not in the project's CI; Oracle-only tests in the kernel suite skip on other engines; the engine parity check below
is run by hand against Oracle AI Database 26ai Free. The SQL query cache applies. Oracle, like PostgreSQL, sorts
NULL after every value in ascending order where MySQL and SQLite sort it first, so a list sorted by a column that is
often empty can come out in another order.

Details: [9.6 Oracle 19c and later](../install/09-databases.md#96-oracle-19c-and-later-ezoracle), and the extension's
`README.md` and `INSTALL` in `extension/ezoracle/`.

**Clustering.** A site on several web servers can keep its DFS cluster metadata in Oracle 12.1 or later with the
kernel's `expDFSFileHandlerOracleBackend`, the tables of `kernel/sql/oracle/cluster_dfs_schema.sql` and the gateway
`dfsoracle`, with only `oci8`; it replaces the `ezoracle` extension's cluster backend, whose table
`cluster_dfs_schema_upgrade.sql` upgrades, and `exp:cluster:enable` sets it up on an Oracle site:
[Clustering on Oracle](../features/6.0/oracle-dfs-cluster.md).

## 6. MySQL

**What it is.** The driver `eZMySQLiDB` on `mysqli`, the engine most third-party extensions were originally written
against.

**Create the database and the user.** No global privilege is needed:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

**InnoDB.** The installer creates InnoDB tables when the server offers them; transactions need InnoDB.
`php bin/php/ezconvertmysqltabletype.php --list` lists the table types of an older installation, `--newtype=InnoDB`
converts them.

**Read replicas.** `UseSlaveServer=enabled` with `SlaveServerArray[]` and the related keys sends reads to a replica
chosen at random per request; writes go to `Server`. Since 6.0.15 PostgreSQL, MySQL, MariaDB and Oracle (ezoracle)
follow one rule: only plain SELECTs of anonymous GET/HEAD requests that have written nothing, and only from a replica
that is current, so an edit is never read back stale; SQLite has no replica
([Read replicas](../bc/6.0/postgresql-read-replica.md)).

**Limits.** The connection character set `utf-8` maps to MySQL's `utf8` (three bytes), so characters outside the
Basic Multilingual Plane, such as emoji, cannot pass through the connection even when the tables are `utf8mb4`.

Details: [9.3 MySQL and MariaDB](../install/09-databases.md#93-mysql-and-mariadb).

<a id="microsoft-sql-server-possible-with-additional-work"></a>

## 7. Microsoft SQL Server and Azure SQL

Exponential 6.0.15 has a SQL Server driver of its own in the kernel, `expSQLServerDB`
(`DatabaseImplementation=sqlserver`; `mssql` and `sqlsrv` name the same driver), written anew under the GPL, with its
schema handler `expSQLServerSchema`, the kernel schema in `kernel/sql/sqlserver/`, installer entries and a DFS cluster
backend.

- **Servers**: SQL Server 2017, 2019, 2022 and 2025, Azure SQL Database and Azure SQL Managed Instance (transient
  errors are retried). Azure SQL Edge, which Microsoft retired, and SQL Server 2016 and older are not supported.
- **PHP layers**: Microsoft's `sqlsrv` (recommended) or `pdo_sqlsrv`, PHP's `pdo_odbc` with ODBC Driver 17 or 18, or
  `pdo_dblib` over FreeTDS; `[SQLServerSettings] Layer=auto` takes the first one that is loaded. On Linux and on
  Windows.
- **Installing**: create the database first (a UTF-8 collation, `READ_COMMITTED_SNAPSHOT`), then
  `exp:install --apply --db=sqlserver`, the kickstarter with `Type=sqlserver` or the setup wizard (with `sqlsrv` or
  `pdo_sqlsrv`). Measured on SQL Server 2022: 16
  steps, no errors, 323 content objects.
- **Clustering**: `expDFSFileHandlerSQLServerBackend` keeps the DFS metadata on SQL Server or Azure SQL for any
  number of web servers; `exp:cluster:enable --backend=sqlserver` makes a one-node cluster.
- **Read replicas**: Always On availability groups with `ApplicationIntent=ReadOnly`.
- **Tested**: on every push, on SQL Server 2017, 2019, 2022 and 2025 with `sqlsrv`, `pdo_sqlsrv` and `pdo_odbc` and
  PHP 8.1 to 8.5, and on SQL Server LocalDB on Windows.

Details: [Microsoft SQL Server driver](../features/6.0/sqlserver-database-driver.md) and
[Clustering on SQL Server](../features/6.0/sqlserver-dfs-cluster.md).

### Databases of the PostgreSQL wire protocol

Six databases that speak PostgreSQL's protocol run on one driver, `expPostgreSQLWireDB` (new in 6.0.15), a subclass of
the PostgreSQL driver with one profile per database. To the kernel each of them is PostgreSQL (`databaseName()` is
`postgresql`); the profile changes what has to differ: locks, retries, sequences, DDL, poolers, TLS and cold starts.

| Database | `DatabaseImplementation` | Level | Tested live | Works | Does not, or differently |
|---|---|---|---|---|---|
| CockroachDB 23.2+ | `cockroachdb` | full site database | yes, 25.2 | the kernel schema and a site package, content, the admin; `READ COMMITTED`; sequences; data-table connections | no `LOCK TABLE` (row locks in `exp_wire_lock`); advisory locks lock nothing; `SERIALIZABLE` transactions may have to be run again (`40001`); slow DDL |
| YugabyteDB 2.20+ | `yugabytedb` | full site database | yes, 2.25 | the kernel schema and a site package, content, the admin; sequences, temporary tables; data-table connections | no `LOCK TABLE ... ACCESS EXCLUSIVE` (row locks); advisory locks a preview flag; no pgcrypto; ids not in insertion order; slow DDL |
| Google Cloud Spanner (PostgreSQL dialect) | `spanner` | data-table connection and import | yes, the emulator behind PGAdapter | tables, indexes, upserts, 64-bit ids from bit-reversed sequences (`RETURNING`), persistent classes on the connection | no site: no plain sequences, no temporary tables, no `LOCK`, no advisory locks, no `md5()`, no `smallint` |
| Neon | `neon` | full site database (PostgreSQL) | **no** | as PostgreSQL; cold starts retried; TLS | the `-pooler` host is transaction mode: no session state |
| Supabase | `supabase` | full site database (PostgreSQL) | **no** | as PostgreSQL; TLS; Supavisor session mode | port 6543 is transaction mode; the direct host is IPv6 only |
| Amazon Aurora PostgreSQL | `aurorapostgresql` | full site database (PostgreSQL) | **no** | as PostgreSQL; failover reconnects outside transactions; reader endpoint as replica | a transaction in flight during a failover is lost; IAM tokens expire |

**Cluster**: `exp:cluster:enable` gives the five site databases the PostgreSQL DFS backend
(`expDFSFileHandlerPostgresqlBackend`, gateway `dfspostgresql`), with its tables in the site's own database. It passed
every DFS check live on CockroachDB 25.2 and YugabyteDB 2.25 (on YugabyteDB `lock_timeout` is not applied, so a store
waits for a held row lock). Neon, Supabase and Aurora PostgreSQL are mapped the same way, not tested against the
service. Spanner holds no site and gets no cluster.

Details: [Databases of the PostgreSQL wire protocol](../features/6.0/postgresql-wire-databases.md).

## Databases that speak the MySQL protocol

TiDB, Vitess and PlanetScale, SingleStore, MariaDB Xpand and Amazon Aurora MySQL run on drivers of their own from
6.0.15: each is the MySQL driver with a profile of what its database does differently (`expTiDBDB`, `expVitessDB`,
`expSingleStoreDB`, `expXpandDB`, `expAuroraMySQLDB`; `DatabaseImplementation` `tidb`, `vitess`, `singlestore`, `xpand`,
`aurora`). They need `mysqli`, install with `exp:install --db=<name>`, the kickstarter or the setup wizard (listed
after MySQL), and use the MySQL DFS backend.

| Database | Level reached | Works | Does not, or differently |
|---|---|---|---|
| TiDB | full site, measured on 8.5.1 | a whole site, DFS cluster, foreign keys, savepoints | `READ UNCOMMITTED`, `SERIALIZABLE`, `XA`; shared locks become `FOR UPDATE`; `LOCK TABLES` becomes `GET_LOCK()`; no full-text index |
| Vitess, PlanetScale | full site in an unsharded keyspace, measured on Vitess 25 | a whole site, DFS cluster, everything MySQL 8 does | no foreign keys (left out); transactions are killed after the tablet's timeout (PlanetScale 20 s): install with a longer one; sharded keyspaces not for a site |
| SingleStore | full site, measured on 10.10 | a whole site (reference tables), DFS cluster, timestamps past 2038 | no foreign keys, no `FULLTEXT` on rowstore, `SAVEPOINT` does nothing, `READ COMMITTED` only, no `GET_LOCK()` |
| MariaDB Xpand | driver and profile, not measured | expected: as MySQL | no `FULLTEXT`; ids not in insert order |
| Aurora MySQL | driver and profile, not measured | expected: as MySQL 8.0 | after a failover the driver connects again to a writer that turned read-only |

Details, the settings (`[MySQLWireSettings]`) and every rule the drivers apply:
[Databases that speak the MySQL protocol](../features/6.0/mysql-wire-databases.md).

## Character sets and collations

Exponential stores and serves text as UTF-8. Each engine names that differently:

| Database | Create the database with | Notes |
|---|---|---|
| 1. SQLite 3 | nothing to choose | SQLite stores text as UTF-8. `=` compares with case; `LIKE` ignores case for ASCII letters only. |
| 2. PostgreSQL | `--encoding=UTF8 --template=template0` | `template0` lets you choose the encoding regardless of the server's default. Comparisons are case-sensitive. |
| 3. MongoDB | nothing to choose | BSON strings are UTF-8. Comparisons are case-sensitive unless a collation is set on the query or index. |
| 4. MariaDB | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | The wizard refuses a database whose default character set is not UTF-8 ("cannot be used, the setup wizard wants to create the site in ..."). Default collations compare without case. |
| 5. Oracle | database character set `AL32UTF8` | The driver connects in AL32UTF8; `NLS_LANG` is not needed. Case-sensitive unless `OracleCaseInsensitive=enabled`. |
| 6. MySQL | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | As MariaDB. |

**The MySQL and MariaDB connection.** `Charset=utf-8` (the default) is sent to the server as `utf8`, which both
servers treat as the three-byte `utf8mb3`. Keep the database in `utf8mb4` anyway: it costs nothing. If the connection
character set cannot be set, the driver keeps running and logs the warning "could not set the connection charset";
it is logged only when warnings are (`site.ini [DebugSettings] AlwaysLog[]=warning`).

**Case.** A login or identifier lookup written against MySQL's case-insensitive collations may find nothing on
PostgreSQL, Oracle or MongoDB. Compare `lower(...)` on both sides where case must not matter.

**The System information page** (Setup > System information) warns when the connection's character set is not UTF-8.

## Performance and scaling

- **Most requests never reach the database.** Velocity's response cache (anonymous visitors), the content view cache
  and the template-block cache answer first; below them the optional SQL query cache (`settings/querycache.ini`,
  `Mode=off` by default) answers repeated `SELECT` statements on every engine except MongoDB. Measure with
  `php bin/php/console exp:benchmark`.
- **SQLite** scales reads with the number of processes; writes queue. Count up to 64 MB of page cache per process
  that has opened the database (`SQLitePragmas[]=cache_size=-16384` lowers it to 16 MB).
- **PostgreSQL, MariaDB, MySQL** scale writes with row-level locking. `UsePersistentConnection=enabled` keeps one
  connection per PHP process; under Velocity's persistent workers each worker keeps its connection anyway. MySQL,
  MariaDB, PostgreSQL and Oracle can send reads to replicas (`UseSlaveServer`).
- **MongoDB**: performance follows the number of statements per page; create the indexes from
  `bin/mongodb/create_indexes.js`, without which subtree queries and URL alias lookups scan whole collections.
- **Oracle**: a new dedicated session costs tens of milliseconds; use `Persistent=enabled` and DRCP
  (`ezoracle.ini [ConnectionSettings]`), and the `Prefetch` settings of `[PerformanceSettings]`.
- **Slow statements**: `[DatabaseSettings] SlowQueriesOutput=<milliseconds>` with `SQLOutput=enabled` lists the
  slower statements in the debug output on every engine; on MySQL and MariaDB `QueryAnalysisOutput=enabled` adds
  `EXPLAIN`; Oracle logs slow statements to `var/log/oracle-slow.log` when `[LogSettings] SlowQueryThreshold` is set.

The comparison table of [9.7](../install/09-databases.md#97-comparison) lists concurrency, replicas and persistent
connections side by side.

## Backup and restore

A complete backup is the database **and** the files: `var/storage` (uploads and image variations),
`settings/override` and `settings/siteaccess`. Switch on maintenance mode for a restore
(`php bin/php/maintenance.php on --allow-root-user`), and clear every cache afterwards
(`php bin/php/ezcache.php --clear-all --allow-root-user` and, with Velocity, `php bin/php/console exp:velocity cache clear --apply --allow-root-user`).
The full procedure is in [chapter 10, backups and restore](../install/10-after-installing.md#109-backups-and-restore).

| Database | Backup | Restore |
|---|---|---|
| 1. SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential.db'"` (online, consistent) or `"VACUUM INTO '/backup/exponential.db'"`; check with `PRAGMA integrity_check` | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/exponential.db'"`; never copy a database file that is in use |
| 2. PostgreSQL | `pg_dump -U exponential -Fc exponential > exponential.dump` | `pg_restore -U exponential -d exponential --clean exponential.dump` |
| 3. MongoDB | `mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/exponential` | `mongorestore --uri="mongodb://exponential@localhost:27017/exponential" --drop /backup/exponential` |
| 4. MariaDB | `mariadb-dump --single-transaction -u exponential -p exponential > exponential.sql` | `mariadb -u exponential -p exponential < exponential.sql` |
| 5. Oracle | `php bin/php/console ext:ezoracle:datapump` (see its `--help`), or Oracle's `expdp` | Oracle's `impdp` |
| 6. MySQL | `mysqldump --single-transaction -u exponential -p exponential > exponential.sql` | `mysql -u exponential -p exponential < exponential.sql` |
| 7. SQL Server, Azure SQL | `BACKUP DATABASE [site] TO DISK = '/backup/site.bak'`, or the service's automated backups | `RESTORE DATABASE` |
| Firebird | `gbak -b -user SYSDBA exponential /backup/exponential.fbk` | `gbak -c` into a new database |
| DuckDB | copy the file while no process writes, or `EXPORT DATABASE '/backup/site'` in the `duckdb` program | the copy, or `IMPORT DATABASE` |
| libSQL (sqld), Turso | a copy of sqld's data directory while it is stopped; Turso's point-in-time restore | the copy; Turso's restore |
| YugabyteDB | `ysql_dump` (not PostgreSQL 17's `pg_dump`, whose dump YugabyteDB 2.25 refused) | `ysqlsh` |
| TiDB, Vitess, SingleStore, CockroachDB, the cloud services | the database's or the service's own backup | the same |

The safety copy `exp:upgrade` makes before it writes (`exp:upgrade:backup`) has a built-in dump for SQLite,
MySQL/MariaDB, PostgreSQL and MongoDB only; on Oracle, SQL Server, Firebird, DuckDB and libSQL it says "no built-in dump" and
the upgrade runs with `--no-backup` after your own backup ([Choosing a database](choosing-a-database.md#backups-and-upgrade-captions)).

`--single-transaction` gives a consistent dump of InnoDB tables without locking them. Commands that take a password
on the command line show it to other users of the machine; prefer the tools' prompts or option files.

## Moving a site to another database

The content stays the same when a site changes its database; only where it is stored changes. Plan a maintenance
window: nothing may be published between the dump and the switch.

- **Between SQL engines** (SQLite, PostgreSQL, MariaDB, MySQL, and Oracle with `ezoracle`): dump with
  `bin/php/ezsqldumpschema.php --format=generic --output-array --output-types=all` into a `.dba` file and load it with
  `bin/php/ezsqlinsertschema.php --insert-types=all`, choosing the engine with `--type` (`mysql`, `postgresql`,
  `sqlite3`, `oracle`). Then point `DatabaseImplementation` and the connection keys at the new engine, clear all caches
  and restart the workers.
- **Into Oracle**: the extension's own `ext:ezoracle:mysql2oracle-schema` and `ext:ezoracle:mysql2oracle-data`.
- **Into MongoDB**: the scripts in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`,
  `validate_ndjson.sh`) and section 18 of the porting reference.
- **Check before you reopen**: compare the row counts of `ezcontentobject`, `ezcontentobject_tree` and
  `ezcontentobject_attribute` on both sides; on PostgreSQL correct the sequences; sign in and publish a test object.
- **Later upgrades** use the update files of the engine the site runs on now, not the one it was installed on.

The step-by-step version is [9.9 Switching an existing site to another engine](../install/09-databases.md#99-switching-an-existing-site-to-another-engine);
why and when to move, and which engine to move to: [Moving off an old database](moving-off-an-old-database.md).

## Engine parity, measured

On 18 and 19 November 2026 the demo site package (`sevenx_multisite`) was installed with the kickstarter, from the
same kernel, on SQLite 3 (the reference), MongoDB 8.0 and Oracle AI Database 26ai Free, and the same pages were
fetched from each installation and compared: the page's status and title, its headings, the layout blocks, the
table rows, the links and the visible text, with dates, times and the site's own address masked.

| | SQLite 3 | MongoDB | Oracle |
|---|---|---|---|
| Installation (kickstarter, 16 steps) | 0 errors, 54–128 s | 0 errors, 588–916 s | 0 errors, 901–1030 s (1502 s before the search index was batched) |
| Migrations after the installation | 4 applied | 4 applied (the run hung before: see below) | 4 applied |
| Pages compared (151 front end, 299 admin) | reference | 405 of 450 identical | 424 of 450 identical |
| The same pages before the 6.0.15 fixes | | 187 of 380 identical | 115 of 380 identical, with help built during the run |
| Content objects, URL links, relation lists, layouts | 380, 910, 196, 24 | the same | the same |
| Create, publish, new version, move with URL alias and subtree, roles, tags, search, remove (17 checks) | 17 passed | 17 passed | 17 passed |
| Cronjob parts `global`, `frequent`, `infrequent` | no errors | no errors | no errors |

**What the remaining differences are.** None of them is a feature that works on one engine and not on another:

- Lists sorted by a value that several items share (the showcase items have one publication date, the recently
  modified classes and layouts one time) come out in another order: each engine returns ties in its own order.
- Oracle, like PostgreSQL, sorts NULL last in ascending order (a role's policies sorted by limitation).
- Values of the installation itself: audit event ids, migration run ids, the sandbox's path, its crontab, the
  order of the active extensions (`ezoracle` is activated for an Oracle site).
- Setup > System information shows the SQL query cache only where it applies (not on MongoDB).
- On MongoDB, the German URLs of the Bold site take the German path of the parent folders where the SQL engines
  keep the English one, and the search for "running" finds 26 objects where the SQL engines find 29.

**What the comparison found and 6.0.15 fixed.** MongoDB: SQL the adapter could not translate returned nothing (the
SQL fallback answers it now); `LIKE` did not match text over several lines, so the installer left every object
relation list unresolved; object names came from the wrong translation; tag pages listed every node; a moved node
kept its old path and lost its URL alias; a role given twice on different subtrees counted once; unactivated users
listed enabled accounts; the security activity, the user audit, the role figures, the order statuses' order, the
URL list and SQLI Import's tables. Oracle: comparisons with `''` on CLOB columns failed, the default admin layouts
and 495 URL links of the seed data did not reach the database, and the search index was written word by word. Both:
`DatabaseAction=remove` left the tables and collections of extensions with the previous installation's rows, and
a migration run hung on a MongoDB backup (`git_manager`).

The scripts that ran the comparison are kept with the development tools of the project; they need nothing but the
kickstarter, a throwaway database per engine and PHP's built-in web server.

## Test coverage (CI)

What the project's continuous integration (`.github/workflows/`) runs on every push, per database. "Driver unit
tests" means tests that open a real database file or test the driver's SQL without a server; "database tests" means
the legacy toolkit's tests run against a live server with a throw-away database per test.

| Database | In CI | How |
|---|---|---|
| 1. SQLite 3 | yes, driver unit tests | the PHPUnit job on PHP 8.1, 8.2, 8.3, 8.4 and 8.5 loads `sqlite3`; the SQLite tests in `tests/tests/lib/ezdb/` check the driver, partly on an in-memory or temporary database (query counts, the schema mirror of `kernel/sql/sqlite/`) and partly by reading its settings (the transaction wait) |
| 2. PostgreSQL | yes, database tests | the jobs "Database tests (postgresql 13, 15, 18 and 19, PHP 8.3)" with a `postgres:<version>` service, the PGDG client of that version and `pgcrypto`, `tests/runtests.php --dsn postgresql://... --db-per-test` |
| 3. MongoDB | adapter unit tests | the PHPUnit job loads `mongodb` and installs `mongodb/mongodb`; the `mongodb` suite (adapter tests without a server) runs; the `mongodb-live` group needs running MongoDB and MySQL servers and is excluded from CI |
| 4. MariaDB | no | covered indirectly by the MySQL job (same driver) |
| 5. Oracle | the DFS cluster backend | the job "cluster-oracle" runs the Oracle DFS backend's tests against Oracle Free 23 with `oci8`; Oracle-specific kernel tests skip on other engines |
| 6. MySQL | yes, database tests | the job "Database tests (mysql, PHP 8.3)" with a `mysql:8.0` service |
| Firebird | yes, driver, live and kernel schema tests | the job "firebird" with `firebirdsql/firebird` 5 and 4 as service containers and `pdo_firebird`, PHP 8.4 and 8.5 |
| IBM Db2, IBM Informix | yes, driver, live and kernel schema tests, a whole site on Db2 | the job "db2" with Db2 Community Edition 12.1 and 11.5 and Informix Developer Edition 15 and 14.10 as containers, `pdo_ibm` and `ibm_db2`, PHP 8.5 and 8.3 |
| SAP HANA | yes, driver, live and kernel schema tests | the job "hana" with `saplabs/hanaexpress` and SAP's HANA ODBC driver from the image, PHP 8.5 on `odbc` and `pdo_odbc` |
| SAP ASE | DB-less tests only | the job "hana" runs the testsuite `ase`; there is no ASE image |
| 7. SQL Server | yes, driver, live and DFS tests | the job "sqlserver" with `mcr.microsoft.com/mssql/server` 2017, 2019, 2022 and 2025 and ODBC Driver 18, on `sqlsrv`, `pdo_sqlsrv` and `pdo_odbc` and PHP 8.1 to 8.5; the job "SQL Server LocalDB smoke" on Windows with PHP 8.4 |
| MySQL protocol | yes, driver and live tests | the testsuite `mysqlwire`; the job `mysqlwire` runs `mysqlwire-live` against TiDB 8.5, Vitess (`vttestserver`) and SingleStore (dev image) in containers. Xpand and Aurora MySQL: tests without a server only |
| DuckDB | yes, driver, live tests and a whole site | the job `duckdb` on DuckDB 2.0.1 (PHP 8.5) and 1.5.6 (PHP 8.3); on 2.0.1 it installs a site and fetches its front page and admin login |
| libSQL | yes, driver, live tests and a whole site | the job `libsql` with sqld 0.24.33 as a service container (PHP 8.5 and 8.3): the suites `libsql` and `libsql-live`; on PHP 8.5 it installs a site on the server and fetches its front page and admin login |
| ODBC | yes, driver and live tests | the job `odbc` on Linux (SQL Server, PostgreSQL, MariaDB and SQLite, `pdo_odbc` and `odbc`); the Windows job "ODBC on Windows" against LocalDB |
| MS Access | import and driver | the import's live test on Linux with MDBTools; the job `msaccess-windows` (windows-2022, windows-2025, Access Database Engine 2016 x64, PHP 8.4) for the driver and the import |
| PostgreSQL wire protocol | yes, driver and live tests | the testsuite `pgwire` in the PHPUnit job; the job "PostgreSQL wire protocol" runs `pgwire-live` against CockroachDB 25.2, YugabyteDB 2.25 and the Spanner emulator behind PGAdapter, PHP 8.1 and 8.5. Neon, Supabase and Aurora: unit tests only |
| Cassandra and ScyllaDB | yes, driver, data table and import tests | the testsuite `cassandra` in the PHPUnit job; the job "Cassandra and ScyllaDB" runs `cassandra-live` against Cassandra 5.0 and ScyllaDB 2025.1 (with authentication), PHP 8.1 and 8.5 |
| Couchbase, Apache CouchDB | yes, driver, data table and import tests | the job `couch` runs the testsuites `couch` and `couch-live` against Couchbase Server Community 7.6.2 and CouchDB 3.4.3 (PHP 8.5) and 7.2.4 and 3.3.3 (PHP 8.1) in containers |
| MonetDB, ClickHouse | yes, driver and live tests | the testsuite `columnar` and the job "Columnar", which runs `columnar-live` against MonetDB Dec2025-SP5 with ClickHouse 25.8 (PHP 8.5) and MonetDB Dec2023-SP4 with ClickHouse 24.8 (PHP 8.1), and installs a whole site on MonetDB |

PHP 8.0, the oldest supported version, and PHP 7.4 (usable without support) are checked by the Quality workflow with a syntax check of every PHP file and a
boot smoke test without a database. The test suites and how to run them locally are described in
[Continuous integration](../specifications/6.0/continuous-integration.md).

## Which database is my site using?

- In the administration: **Setup > System information**, the **Database** card: engine and server version, driver
  class, the file (SQLite) or server and database name, character set, number of tables and size.
- On the command line:

  ```bash
  php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user
  ```

- **Setup > Upgrade check**, "Check database consistency", compares the tables, fields and indexes with the schema
  of the release for MySQL and MariaDB, PostgreSQL, SQLite and MongoDB ([The upgrade check](../guides/upgrade-check.md)).
- In code: `eZDB::instance()->databaseName()` returns `sqlite`, `postgresql`, `mongo`, `mysql`, `oracle`, `SQLServer`,
  `firebird`, `duckdb`, `MSAccess` or, for the generic ODBC dialect, `odbc`. The MySQL protocol databases answer
  `mysql` and the PostgreSQL protocol databases `postgresql`; on the latter `databaseVariant()` names the database itself.
  libSQL and Turso answer `sqlite` (the driver is the SQLite driver over HTTP); `instanceof expLibSQLDB` tells them apart.

## Related pages

- [Choosing a database](choosing-a-database.md): every database of 6.0.15 in one matrix, with a guide per use case.
- [Chapter 9, Databases](../install/09-databases.md): the operator's reference for every engine.
- [Chapter 2, Requirements](../install/02-requirements.md#26-databases) and [OS support](os-support.md).
- [Moving off an old database](moving-off-an-old-database.md), [Exponential on Windows](exponential-on-windows.md).
- [Microsoft SQL Server driver](../features/6.0/sqlserver-database-driver.md),
  [Databases of the PostgreSQL wire protocol](../features/6.0/postgresql-wire-databases.md),
  [Databases that speak the MySQL protocol](../features/6.0/mysql-wire-databases.md),
  [Firebird driver](../features/6.0/firebird-database-driver.md), [DuckDB driver](../features/6.0/duckdb-database-driver.md),
  [libSQL and Turso driver](../features/6.0/libsql-database-driver.md),
  [ODBC driver](../features/6.0/odbc-database-driver.md), [MS Access support](../features/6.0/ms-access-support.md),
  [Clustering in Exponential 6.0](../features/6.0/clustering-overview.md).
- [SQLite database support](../features/6.0/sqlite-database.md),
  [SQLite: transactions queue for the write lock](../bc/6.0/sqlite-transactions.md),
  [The SQLite3 database driver](../specifications/6.0/sqlite3-database-driver.md).
- [MongoDB as the database](../features/6.0/mongodb-database-support.md) and the
  [MongoDB kernel support porting reference](../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md).
- [SQLite and Oracle drivers](../specifications/6.0/database-drivers-sqlite-oracle.md),
  [Database drivers and installers, September 2026](../specifications/6.0/database-drivers-2026-09.md).
- [SQL query cache](../bc/6.0/sql-query-cache.md), [Installing in one command](../features/6.0/install-in-one-command.md),
  [Kickstarter](../features/6.0/kickstarter-cli.md), [System information](../guides/system-information.md).
- [Free documentation](free-documentation.md): these pages are free to copy, translate and improve.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
