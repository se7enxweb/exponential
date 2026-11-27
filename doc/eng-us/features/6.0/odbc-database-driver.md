# ODBC driver

This page is for administrators who want to reach a database through its ODBC driver, and for developers who work on
the driver. From Exponential 6.0.15 the kernel has a generic ODBC driver, `expODBCDB` in `lib/ezdb/classes/`: one
driver for every database that has an ODBC driver, with the SQL it writes itself (paging, escaping, the new id, the
catalogue, the portable helpers) chosen by a dialect in the settings.

**What it is for.** A site's own database over ODBC (SQL Server, PostgreSQL, MariaDB/MySQL, Db2): the installers,
the setup wizard and the kickstarter install it, `exp:cluster:enable` puts a DFS cluster on it, and the upgrade
stages run over it ([A site over ODBC](#a-site-over-odbc)). Further: named connections of the data table layer
([Data tables](data-tables.md)) to databases the installation has no native driver for, or that a host only offers
through ODBC, and the import of [MS Access files](ms-access-support.md).

**ODBC is a transport.** For SQL Server and Db2, whose own drivers have an ODBC layer, `DatabaseImplementation=odbc`
opens that driver (`expSQLServerDB`, `expDb2DB`) over PDO_ODBC: a site on SQL Server over ODBC behaves exactly as one
on the SQL Server driver. PostgreSQL and MariaDB/MySQL run on `expODBCDB`, with their own schema handlers.

## Contents

1. [Settings](#settings)
2. [Dialects](#dialects)
3. [A site over ODBC](#a-site-over-odbc)
4. [What works and what does not, per dialect](#what-works-and-what-does-not-per-dialect)
5. [PHP layers](#php-layers)
6. [A data table connection over ODBC](#a-data-table-connection-over-odbc)
7. [Tests](#tests)

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=odbc
Server=db.example.com
Port=5432
User=crm
Password=...
Database=crm

[ODBCSettings]
Dialect=postgresql
Driver=PostgreSQL Unicode
```

`ImplementationAlias[odbc]` names `expODBCDB`. `[ODBCSettings]` of `site.ini`:

| Setting | Meaning |
|---|---|
| `Dialect` | `sqlserver`, `postgresql`, `mysql` (MySQL and MariaDB), `sqlite`, `oracle`, `msaccess` or `generic` |
| `Driver` | the ODBC driver's name (`odbcinst.ini` on Linux, the ODBC administrator on Windows); empty: the dialect's usual one |
| `DataSourceName` | a DSN of `odbc.ini` instead of `Driver` and the server settings |
| `ConnectionString` | more `keyword=value` pairs, `;` separated (`Encrypt=yes;TrustServerCertificate=yes`) |
| `Layer` | `auto`, `pdo_odbc` or `odbc` (below) |

User and password go to the connect call, never into the connection string; a failed connection's message has the
password masked. In code, the same settings go in the parameters:
`new expODBCDB( array( 'server' => 'db', 'database' => 'crm', 'user' => 'u', 'password' => $p, 'odbc' => array( 'Dialect' => 'mysql' ) ) )`.

Default drivers (the names the packages register): `ODBC Driver 18 for SQL Server`; `PostgreSQL Unicode` (Debian and
Ubuntu `odbc-postgresql`; `PostgreSQL Unicode(x64)` on Windows); `MariaDB Unicode` (`odbc-mariadb`); `SQLite3`
(`libsqliteodbc`); `Microsoft Access Driver (*.mdb, *.accdb)` on Windows and `MDBTools` elsewhere.

## Dialects

The dialect decides everything the driver writes itself; `databaseName()` is the kernel's name of the engine
(`SQLServer`, `postgresql`, `mysql`, `sqlite`, `oracle`, `MSAccess`, and `odbc` for `generic`), so the schema
handlers and the data table layer treat the connection as that engine.

| Dialect | Paging | New id | Text and escaping | Session |
|---|---|---|---|---|
| `sqlserver` | `OFFSET ... FETCH` (the SQL Server driver's own code) | `SCOPE_IDENTITY()` in the INSERT's own batch | string literals bound as parameters (Unicode), backticks to brackets | `NOCOUNT`, `XACT_ABORT`, ANSI options, `DATEFORMAT ymd` |
| `postgresql` | `LIMIT ... OFFSET` | `currval()` of the column's sequence (`SERIAL` or the kernel's `<table>_<column>_seq`), else `lastval()` | quotes doubled | |
| `mysql` | `LIMIT ... OFFSET` | `LAST_INSERT_ID()` | backslashes and quotes escaped (PDO_ODBC's `quote()` knows no backslash) | `SET NAMES utf8mb4` |
| `sqlite` | `LIMIT ... OFFSET` | `last_insert_rowid()` | quotes doubled | |
| `oracle` | `OFFSET ... FETCH` | the table's sequence (`s_<name>`, as ezoracle) | quotes doubled | |
| `msaccess` | `TOP offset+limit`, the first rows dropped in PHP | `@@IDENTITY` | quotes doubled | see [MS Access](ms-access-support.md) |
| `generic` | `LIMIT ... OFFSET` | the layer's `lastInsertId()` (usually none) | quotes doubled | |

The portable helpers (`sqlLimit()`, `sqlUpsert()`, `sqlGroupConcat()`, `sqlUnixTimestamp()`,
`sqlFromUnixTimestamp()`, `sqlForUpdate()`, `sqlQuoteIdentifier()`) follow the dialect, as on the native drivers.

## A site over ODBC

From 6.0.15 a site can be installed on SQL Server, PostgreSQL, MariaDB/MySQL or Db2 through the database's ODBC
driver. The site gets `DatabaseImplementation=odbc` and `[ODBCSettings]` (in `settings/override` and every
siteaccess); everything else reads the dialect as the engine.

| Dialect | Runs on | Schema handler | DFS cluster backend | xne backup and rollback |
|---|---|---|---|---|
| `sqlserver` | `expSQLServerDB` with `[SQLServerSettings] Layer=pdo_odbc` | `expSQLServerSchema` | SQL Server's, over PDO_ODBC (`Layer=pdo_odbc`, written by the installer) | refused, as for SQL Server |
| `postgresql` | `expODBCDB` | `eZPgsqlSchema` | PostgreSQL's, over PDO_ODBC (`DBODBCDriver`, written by `exp:cluster:enable`); no `pdo_pgsql` needed | as PostgreSQL, through `pdo_pgsql` of the PHP that runs xne |
| `mysql` (MariaDB too) | `expODBCDB` | `eZMysqlSchema` | MySQLi's (needs PHP's `mysqli`) | as MySQL, through `pdo_mysql` of the PHP that runs xne |
| `db2` | `expDb2DB` with `[Db2Settings] Layer=pdo_odbc` | `expDb2Schema` | none (Db2 has none) | refused, as for Db2 |

```bash
# install.php (exp:install) - SQL Server, PostgreSQL, MariaDB through their ODBC drivers
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --db=odbc --db-dialect=sqlserver --db-host=sql.example \
    --db-name=exponential --db-user=exp --db-trust-server-certificate --apply
php bin/php/install.php --db=odbc --db-dialect=postgresql --db-host=pg.example --db-name=exponential --db-user=exp --apply
php bin/php/install.php --db=odbc --db-dialect=mysql --db-dsn=exp-mariadb --db-name=exponential --db-user=exp --apply
```

| Option | Meaning |
|---|---|
| `--db-dialect` | `sqlserver`, `postgresql`, `mysql` (MariaDB too) or `db2`; the port, name and user default to the dialect's |
| `--db-odbc-driver` | the ODBC driver's name; default the dialect's usual one (below) |
| `--db-dsn` | `postgresql` and `mysql`: a data source of `odbc.ini` instead of the driver, host and port |
| `--db-connection-string` | more `keyword=value` pairs |
| `--cluster=one-node` | with `postgresql`, `sqlserver` or `mysql`: a one-node DFS cluster after the installation |

The kickstarter takes `[database_choice] Type=odbc` with `Dialect=`, and `[database_init] ODBCDriver`,
`DataSourceName`, `ConnectionString` (`kickstart.ini-dist`); `odbc_sqlserver`, `odbc_pgsql`, `odbc_mysql` and
`odbc_db2` are accepted as types too. The setup wizard offers these four types when PHP has `pdo_odbc` or `odbc`.

**Measured** on 2026-11-27 (PHP 8.5.13 with only PDO_ODBC and the odbc extension, Ubuntu 24.04, the package
`sevenx_multisite`):

| What | PostgreSQL 17, psqlODBC 16 | MariaDB 11.4, MariaDB Connector/ODBC 3.1 |
|---|---|---|
| installation (`install.php --db=odbc`) | works, 302 s | works, 220 s |
| front page, admin login, search | 200 | 200 |
| schema read back against `share/db_schema.dba` | the same differences as the PostgreSQL driver reading the same database (the tables and the index the 6.0.15 migrations add, bigint defaults read as `'0'`) | the tables and the index the migrations add, nothing else |
| `exp:upgrade:database` (two kernel tables dropped first) | - | works: a caption with `mysqldump`, both tables made again, schema read back as before |
| `exp:cluster:enable` and DFS | works without `pdo_pgsql`: 312 files moved in, a 200 KB file stored, read back, sized and deleted in 34 ms, a stored image served by the gateway | - |

SQL Server 2022 over ODBC Driver 18 and MariaDB are installed by the GitHub job `odbc` (job `pdo_odbc`), PostgreSQL by
the job `odbc` on PHP's odbc extension.

**Upgrades.** The upgrade stages (`exp:upgrade`) run over ODBC with the dialect's schema handler, and the caption
before them uses the dialect's dump tool (`mysqldump`, `pg_dump`; like the SQL Server driver, SQL Server over ODBC
has no caption dump, so make a backup of the database first or run with `--no-backup`). A site installed over ODBC
starts with 6.0.15, so there is no earlier release to upgrade from; the first upgrade over ODBC is 6.0.15 to the next
release.

**Not here.** SQLite, Oracle and MS Access as a site database over ODBC (SQLite and Oracle have native drivers; for
Access see [MS Access](ms-access-support.md#a-minimal-site-on-access)); a DSN for SQL Server and Db2 (their drivers
build the connection string from the host and port).

## What works and what does not, per dialect

Measured on 2026-11-25 with the live tests (below) on Ubuntu 24.04 (unixODBC 2.3.12, PHP 8.5.13), against SQL
Server 2022 (ODBC Driver 18.7), PostgreSQL 17 (psqlODBC 16), MariaDB 11.4 (MariaDB Connector/ODBC 3.1) and SQLite
3.45 (sqliteodbc 0.99991), on both PHP layers; the same tests run in the GitHub job `odbc`.

| Test | SQL Server 2022 | PostgreSQL 17 | MariaDB 11.4 | SQLite 3.45 |
|---|---|---|---|---|
| connect, version, `eZTableList()` | works | works | works | works |
| new id (`lastSerialID()`) | works (`SCOPE_IDENTITY()` in the INSERT's batch) | works (sequence) | works | works |
| paging (limit/offset equals the slice, limit 0, past the end) | works | works | works | works |
| transactions (rollback, commit, nested) | works | works | works | works |
| Unicode (umlauts, emoji, CJK, combining marks; in a `WHERE`) | works (text bound) | works | works | works |
| long text (1.2 MB) | works | works | works | **does not**: sqliteodbc describes `TEXT` as 255 characters; PDO_ODBC refuses the value (`01004 data right truncated`), the odbc extension cuts it to 255 bytes |
| escaping (quote, backslash, NUL dropped, `''` is not `NULL`) | works | works | works | works |
| `sqlUpsert()` | works only with `IDENTITY_INSERT` on when the key is an identity column (MERGE compiles its INSERT branch with the key; `expSQLServerDB` switches it itself) | works | works | works |
| `sqlGroupConcat()` | works | works | works | works |
| unix time stamps there and back | works, past 2038 | works, past 2038 | works before 2038 only (MariaDB before 11.5: `FROM_UNIXTIME()` ends in 2038; a property of MariaDB, not of ODBC) | works, past 2038 |
| a keyword as a name (`sqlQuoteIdentifier()`) | works | works | works | works |
| duplicate key (false, or `eZDBException`) | works | works | works | works |
| data table connection (engine `odbc`): test, make a table, write, plan again: nothing to do | works | works (after reading psqlODBC's `1` as true in the PostgreSQL schema handler) | works | works |
| Access import (MDBTools' 3 files, 4,103 rows) | works; 31 s (a round trip per row) | works, 2.6 s | works, 2.5 s | works, 0.4 s |

Both layers gave the same results; Oracle (`oracle` dialect) is covered by the DB-less tests only.

## PHP layers

| Layer | Extension | Notes |
|---|---|---|
| `pdo_odbc` | PHP's PDO_ODBC | the default (`auto`). Converts bound text to UTF-16 when the driver asks for wide characters |
| `odbc` | PHP's odbc extension | the only layer with the catalogue calls (`SQLTables`, `SQLColumns`, `SQLStatistics`); `auto` picks it first for MS Access. A bound value written `'like this'` (in single quotes) is read by the extension as a file name, so such a statement keeps literals |

Both layers send the statement text in the client's code page: Microsoft's SQL Server driver turned UTF-8 in an
`N'...'` literal into mojibake on both, which is why the `sqlserver` dialect binds string literals (measured: `ä😀`
arrived as `Ã¤ð` as a literal, intact as a parameter). PostgreSQL's, MariaDB's and SQLite's Unicode drivers take
UTF-8 statement text as it is.

## A data table connection over ODBC

The data table layer has the engine `odbc` (`datatable.ini [ConnectionSettings] Engines[]`); its options carry the
dialect and the driver:

```ini
[Connection_crm]
Engine=odbc
Server=db.example.com
Port=5432
Database=crm
User=crm
Secret=env:CRM_DB_PASSWORD
Dialect=postgresql
ODBCDriver=PostgreSQL Unicode
ConnectionString=SSLmode=require
```

`expDataConnections::test()` opens it, reads the version and the tables; `expDataTableSchema` makes and upgrades
tables on it through the dialect's schema handler (MySQL, PostgreSQL, SQLite) or its own statements (SQL Server,
Oracle, MS Access).

## Tests

`tests/tests/lib/ezdb/odbc/` (DB-less, testsuite `odbc`) and `.../odbc/live/` (testsuite `odbc-live`; skipped
without `EXP_TEST_ODBC_SQLSERVER`, `_POSTGRESQL`, `_MYSQL`, `_SQLITE` with `_USER`, `_PASSWORD`, `_DRIVER`, and
`EXP_TEST_ODBC_LAYER`). The GitHub job `odbc` runs them on both layers with every target configured.

| File | What |
|---|---|
| `expODBCDBTest` | layers, default drivers, connection strings, engine names, escaping, paging, masked passwords, settings; the transport to SQL Server's and Db2's drivers (`resolve()`) |
| `expODBCSiteInstallTest` | the setup wizard's ODBC types, the kickstarter's options, the parameters the steps carry, `install.php --db=odbc` |
| `expMSAccessDialectTest` | the Access dialect (see [MS Access](ms-access-support.md)) |
| `live/expODBCLiveTest` | per target and layer: connect, new ids, paging, transactions, Unicode, 1 MB text, escaping, helpers, duplicate keys, a data table connection |
