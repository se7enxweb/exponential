# SAP ASE driver

This page is for administrators who want to run Exponential on SAP Adaptive Server Enterprise (ASE, formerly Sybase
ASE), and for contributors who work on the driver. From Exponential 6.0.15 the kernel has an ASE driver, `expASEDB` in
`lib/ezdb/classes/`, with its dialect `expASEDialect`, the schema handler `expASESchema`, installer entries and a data
table engine.

**Status: written, not tested against a server.** No SAP ASE server could be had for this driver's tests: SAP
publishes no container image of ASE, the ASE developer edition is a download behind SAP's registration, and its
license does not allow redistributing it as an image (the community images of ASE on Docker Hub are such
redistributions, so they were not used). Everything below follows SAP's ASE 16.0 reference manuals and is covered by
tests without a server (the dialect, the driver over a recording transport, the schema handler); none of it has run
against ASE. Treat the driver as a starting point for a site that has ASE, and report what you find.

## Contents

1. [Supported servers and PHP](#supported-servers-and-php)
2. [Settings](#settings)
3. [Preparing the server](#preparing-the-server)
4. [Installing a site](#installing-a-site)
5. [How the kernel's SQL becomes ASE's](#how-the-kernels-sql-becomes-ases)
6. [What is expected to work and what does not](#what-is-expected-to-work-and-what-does-not)
7. [Data table connections](#data-table-connections)
8. [Tests](#tests)

## Supported servers and PHP

| | Supported | Why |
|---|---|---|
| ASE 16.0, 15.7 | written for, untested | `MERGE`, `int` identity columns and `hash()` need 15.7 |
| ASE 15.5 and older | no | no `MERGE`; the driver reports them as unsupported |
| PHP 8.0 to 8.5 with `pdo_dblib` (FreeTDS) | written for | TDS 5.0, UTF-8 |
| PHP with `PDO_ODBC` and FreeTDS's ODBC driver, or SAP's ASE ODBC driver | written for | `[ASESettings] Layer=pdo_odbc` |

ASE and Microsoft SQL Server share their origin and the TDS protocol, so `expASEDB` extends the
[SQL Server driver](sqlserver-database-driver.md) (`expSQLServerDB`): the retries after a deadlock, the reconnect of a
lost connection, `#temporary` tables, `IDENTITY_INSERT` around an insert that gives its own id, and the transport over
PDO are the SQL Server driver's. What differs is the SQL, the catalogue and the connection.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=ase
Server=ase.example.com
Port=5000
User=exponential
Password=...
Database=exponential

[ASESettings]
Layer=auto
```

`ImplementationAlias[ase]` (and `sybase`) names `expASEDB`.

| `[ASESettings]` | Meaning |
|---|---|
| `Layer` | `auto` (pdo_dblib, then PDO_ODBC), `pdo_dblib` or `pdo_odbc` |
| `ODBCDriver` | the ODBC driver of PDO_ODBC: `FreeTDS` (sent with `TDS_Version=5.0`) or SAP's `Adaptive Server Enterprise` |
| `ApplicationName` | the program name the server shows (`sp_who`) |
| `LoginTimeout` | seconds to wait for the login (15) |
| `LockTimeout` | milliseconds to wait for a lock, sent as `set lock wait` in whole seconds; -1: the server's setting |
| `DeadlockRetries`, `DeadlockRetryWait` | a single statement outside a transaction is sent again after a deadlock (1205) |
| `ReconnectOnLoss` | `enabled`: a lost connection outside a transaction is opened again |
| `InListChunkSize` | values per `IN` list (1,000) |
| `EmptyStringSpace` | `enabled`: a value of exactly one space is read back as `''` (see below) |

Every connection first runs `set quoted_identifier on`, `set chained off` (autocommit; a transaction is
`BEGIN TRANSACTION` to `COMMIT`), `set textsize 2147483647`, `set ansinull on` and `set string_rtruncation off`.

## Preparing the server

- **Character set utf8, a logical page of 16 KB.** The schema handler writes text as `UNIVARCHAR`/`UNITEXT` (Unicode
  whatever the server's character set), but the server should still have `utf8` as its default character set so
  FreeTDS's UTF-8 converts cleanly, and 16 KB pages: a text column in an index is `UNIVARCHAR(1000)` (2,000 bytes),
  which a server with 2 KB pages cannot index.
- **The database must exist.** `createDatabase()` sends `CREATE DATABASE <name>` (on the default device), which an
  administrator usually wants to place on devices of their own: create it first, with a log of its own.
- **Allow nulls by default is not needed**: every column the schema handler writes says `NULL` or `NOT NULL`.
- **Locking scheme.** `SELECT ... FOR UPDATE` locks rows only on tables with `datarows` locking (ASE 15.7 and later);
  `sp_configure 'lock scheme', 0, datarows` before the install makes it the default for new tables.

## Installing a site

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=ase \
    --db-host=ase.example.com --db-name=exponential --db-user=exponential
```

The kickstarter takes `Type=ase` in `[database_choice]` (`sybase` too). The setup wizard does not list ASE: it has no
PHP extension of its own to detect (pdo_dblib also serves SQL Server). `--db-action=remove` (the default) drops the
tables of the database first.

## How the kernel's SQL becomes ASE's

The kernel writes MySQL's words where its portable helpers are not used; `expASEDialect::rewrite()` reads each
statement with the SQL Server driver's lexer, so a word inside a string literal or a comment is never touched:

| The kernel writes | ASE gets |
|---|---|
| `` `name` `` | `[name]` |
| a column named like a reserved word of ASE (`count`, `level`, `role`, `key` ...) | `[count]` where it stands as a name, not as a call (`COUNT(*)`) |
| `CONCAT(a, b)`, `LENGTH()`, `SUBSTR()`, `SUBSTRING(s, f)`, `IFNULL()` | `(a + b)`, `char_length()`, `substring()`, `substring(s, f, char_length(s))`, `isnull()` |
| `NOW()`, `CURDATE()`, `MD5(x)` | `getdate()`, `current_date()`, `lower(hash(x, 'md5'))` |
| `UNIX_TIMESTAMP()`, `FROM_UNIXTIME(t)` | `datediff(ss, '19700101', getutcdate())`, `dateadd(ss, t, '19700101')` |
| `LIKE 'a\_%'`, `LIKE '[x]%'` | `LIKE 'a\_%' ESCAPE '\'`, `LIKE '\[x]%' ESCAPE '\'` (ASE reads `[...]` as a set of characters) |
| `SELECT ... LIMIT 10 OFFSET 20` (and arrayQuery()'s limit and offset) | `SELECT TOP 30 ...`, the first 20 rows dropped by the driver (no `OFFSET` before ASE 16.0 SP02) |
| `INSERT ... VALUES (...), (...)` | one `INSERT` per row in one batch |
| `TRUNCATE TABLE t` | `DELETE FROM t` (stays inside the transaction) |
| `CREATE TEMPORARY TABLE t (...)` | `CREATE TABLE #t (...)` with ASE's types, `NULL` written out |
| `GROUP_CONCAT`, `sqlGroupConcat()` | refused (`expDBUnsupportedException`): ASE has no string aggregate |
| `sqlUpsert()` | `MERGE INTO ... USING (SELECT ...) AS s ON ...` |

**New ids.** An `auto_increment` column is `INT IDENTITY`. An insert that gives it `NULL` or `0` leaves the column
out, one with an id of its own runs between `SET IDENTITY_INSERT t ON` and `OFF`; `SELECT @@identity` goes in the same
batch, and `lastSerialID()` returns it.

**Empty strings.** ASE stores `''` in a variable length text column as one space. With `EmptyStringSpace=enabled`
the driver reads a value of exactly one space back as `''`, which is what the kernel wrote; a real single space reads
as `''` too.

## What is expected to work and what does not

Nothing in this table was measured on a server; it is what the driver is written to do.

| | Expected |
|---|---|
| connect (pdo_dblib at TDS 5.0, PDO_ODBC), `@@version`, the catalogue (`sysobjects`, `syscolumns`, `sysindexes`) | written, covered by DB-less tests |
| kernel schema (153 tables, `INT IDENTITY`, `UNIVARCHAR`/`UNITEXT`) and data | written, DDL covered by DB-less tests |
| paging, `lastSerialID()`, `IDENTITY_INSERT`, multi-row inserts, `#temporary` tables, locks (`LOCK TABLE ... IN EXCLUSIVE MODE`) | written, covered by DB-less tests |
| `sqlUpsert()` (MERGE), `sqlLimit()` without an offset, timestamps, `md5()` | written, covered by DB-less tests |
| `sqlLimit()` with an offset, `sqlGroupConcat()` | **no**: refused with `expDBUnsupportedException` |
| sequences | **no**: ASE has none (identity columns instead) |
| application locks (`getAppLock()`) | **no**: returns false |
| read replicas, DFS cluster backend | **no** |
| case-insensitive comparisons | depend on the server's sort order (MySQL's default collation is case-insensitive; ASE's default binary order is not) |
| a whole site | **untested** |

## Data table connections

The data table layer has the engine `ase` (`datatable.ini [ConnectionSettings] Engines[]`), marked untested: a named
connection opens through `expASEDB` (options `layer` and `odbc_driver`), and `expDataTableSchema` makes tables on it
through `expASESchema`. Such a connection is also the way to read from an existing ASE database.

```ini
[Connection_legacy]
Engine=ase
Server=ase.example.com
Port=5000
Database=legacy
User=reader
Secret=env:LEGACY_DB_PASSWORD
```

## Tests

| File | What |
|---|---|
| `tests/tests/lib/ezdb/ase/expASEDialectTest.php` | names, functions, LIKE, paging, statements, temporary tables, MERGE, versions, errors, DSNs |
| `tests/tests/lib/ezdb/ase/expASEDBTest.php` | the driver over a recording transport: session setup, paging, ids, locks, temporary tables, relations |
| `tests/tests/lib/ezdbschema/expASESchemaTest.php` | types, `NULL` written out, identity, index keys, upgrade statements |

They run in the testsuite `ase` (and the schema test in `lib`); the GitHub job `hana` runs them too. There are no live
tests: there was no server to run them against.
