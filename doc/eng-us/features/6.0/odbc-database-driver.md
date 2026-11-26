# ODBC driver

This page is for administrators who want to reach a database through its ODBC driver, and for developers who work on
the driver. From Exponential 6.0.15 the kernel has a generic ODBC driver, `expODBCDB` in `lib/ezdb/classes/`: one
driver for every database that has an ODBC driver, with the SQL it writes itself (paging, escaping, the new id, the
catalogue, the portable helpers) chosen by a dialect in the settings.

**What it is for.** Named connections of the data table layer ([Data tables](data-tables.md)) to databases the
installation has no native driver for, or that a host only offers through ODBC, and the import of
[MS Access files](ms-access-support.md). A site runs on its native driver (MySQL/MariaDB, PostgreSQL, SQLite,
[SQL Server](sqlserver-database-driver.md)): `DatabaseImplementation=odbc` works for reading and writing data, but the
installers do not offer it and it is not tested as a site's database.

## Contents

1. [Settings](#settings)
2. [Dialects](#dialects)
3. [What works and what does not, per dialect](#what-works-and-what-does-not-per-dialect)
4. [PHP layers](#php-layers)
5. [A data table connection over ODBC](#a-data-table-connection-over-odbc)
6. [Tests](#tests)

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
| `expODBCDBTest` | layers, default drivers, connection strings, engine names, escaping, paging, masked passwords, settings |
| `expMSAccessDialectTest` | the Access dialect (see [MS Access](ms-access-support.md)) |
| `live/expODBCLiveTest` | per target and layer: connect, new ids, paging, transactions, Unicode, 1 MB text, escaping, helpers, duplicate keys, a data table connection |
