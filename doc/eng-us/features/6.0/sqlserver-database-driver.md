# Microsoft SQL Server driver

This page is for administrators and developers who want to run Exponential on Microsoft SQL Server, and for
contributors who work on the driver. From Exponential 6.0.15 the kernel has a SQL Server driver of its own,
`expSQLServerDB` in `lib/ezdb/classes/`, written anew under the GPL (version 2 or later). It is not derived from the
old xrowmssql extension; that extension was read for its problems only, and every one of them is now a test.

**Status.** The driver and its tests are done (wave W1); the schema handler `expSQLServerSchema`, the kernel schema in
`kernel/sql/sqlserver/kernel_schema.sql` and the portable SQL helpers of every driver followed. Still to come: the
installer entries, the kernel statements that only MySQL, PostgreSQL and SQLite understand, and the DFS cluster
backend on SQL Server. Until then a site cannot be installed on SQL Server.

## Contents

1. [Supported servers and PHP layers](#supported-servers-and-php-layers)
2. [Settings](#settings)
3. [What the driver does differently](#what-the-driver-does-differently)
4. [Helpers every driver offers](#helpers-every-driver-offers)
5. [Read replicas and failover](#read-replicas-and-failover)
6. [Tested toolchain (W0)](#tested-toolchain-w0)
7. [Tests](#tests)
8. [Traceability: the old extension's problems](#traceability-the-old-extensions-problems)

## Supported servers and PHP layers

| Server | Support |
|---|---|
| SQL Server 2017 (14.x) | supported; the floor (`STRING_AGG`, `TRIM`, `CONCAT_WS`). `md5()` matches PHP only for ASCII text |
| SQL Server 2019 (15.x) | supported; UTF-8 collations |
| SQL Server 2022 (16.x) | supported; the development and main CI target; `Encrypt=strict` (TDS 8.0) |
| SQL Server 2025 (17.x) | supported; in the CI matrix |
| Azure SQL Database, Azure SQL Managed Instance | supported (engine editions 5 and 8); transient errors are retried |
| Azure SQL Edge | **not supported**: Microsoft retired it on 30 September 2025. Use SQL Server 2022 or later on Linux |
| SQL Server 2016 and older | the driver connects and warns; not supported |

`databaseName()` is exactly `SQLServer`. PHP layers, chosen with `Layer=auto` in this order:

| Layer | Extension | Notes |
|---|---|---|
| `sqlsrv` | Microsoft's `sqlsrv` (PECL) | Windows and Linux (over ODBC Driver 17/18). Recommended |
| `pdo_sqlsrv` | Microsoft's `pdo_sqlsrv` (PECL) | the driver switches MARS off (with MARS a `BEGIN TRANSACTION` batch is rolled back at its end) and uses direct execution |
| `pdo_odbc` | PHP's `PDO_ODBC` + an ODBC driver for SQL Server | works with ODBC Driver 18 out of the box (Plesk PHP has it). Statement text travels in the client code page, so text is always bound; batches without rows run through `exec()` |
| `pdo_dblib` | PHP's `pdo_dblib` over FreeTDS | fallback; TDS 7.4, UTF-8. No `MultiSubnetFailover`, no `ApplicationIntent` |

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=sqlserver
Server=sql1.example.com
Port=1433
User=exponential
Password=...
Database=site
```

`ImplementationAlias[sqlserver]`, `[mssql]` and `[sqlsrv]` all name `expSQLServerDB`. The `[SQLServerSettings]` of
`site.ini` (all with defaults): `Layer`, `ODBCDriver`, `Encrypt` (yes, no, strict), `TrustServerCertificate`,
`MultiSubnetFailover`, `ApplicationIntent`, `ReplicaApplicationIntent`, `LoginTimeout`, `ConnectRetryCount` (0: ODBC's
own reconnect is off, the driver reconnects itself), `LockTimeout` (milliseconds, -1 waits), `LiteralMode` (bind,
prefix), `DeadlockRetries`, `DeadlockRetryWait`, `ReconnectOnLoss`, `ApplicationName`, `InListChunkSize`,
`DatabaseCollation`.

**Server side.** Create the database with `ALTER DATABASE [site] SET READ_COMMITTED_SNAPSHOT ON`: readers then never
wait for writers, which is what the kernel expects from MySQL and PostgreSQL. The driver notes it in the debug output
when it is off. A UTF-8 collation (`Latin1_General_100_CI_AS_SC_UTF8`, 2019 and later) is recommended; text columns
should be `NVARCHAR`.

## What the driver does differently

- **Paging** on the server: `ORDER BY ... OFFSET n ROWS FETCH NEXT m ROWS ONLY`. A statement without an outer
  `ORDER BY` gets `ORDER BY (SELECT NULL)`; one with `TOP`, its own `OFFSET`, or `DISTINCT`/`UNION` without `ORDER BY`
  is wrapped. An `ORDER BY` inside a subquery, `OVER()`, a literal or a comment does not count (all found by a
  lexer). `limit` 0 sends no statement (`FETCH NEXT 0` is error 10744).
- **New ids** through `SCOPE_IDENTITY()` in the INSERT's own batch: not `@@IDENTITY` (a trigger's id), and not a later
  batch (a bound insert runs in its own scope). `NULL` or `0` for the identity column leaves the column out; a real
  number switches `IDENTITY_INSERT` on for that statement only, and off again even when it fails.
- **Text** as bound `NVARCHAR` parameters for queries and data changes, as `N'...'` literals in DDL, `GROUP BY`
  statements and statements with more than 2,000 literals (the server takes 2,100 parameters). A backslash before a
  line break, which T-SQL removes, is kept. MySQL backticks become brackets.
- **Session**: `NOCOUNT`, `XACT_ABORT` (an error rolls the whole transaction back), the ANSI options, `DATEFORMAT ymd`,
  `LOCK_TIMEOUT`, `READ COMMITTED`.
- **Errors**: deadlock (1205), lock timeout (1222), duplicate key (2627/2601), FK (547), NOT NULL (515), truncation
  (2628/8152), lost connections and Azure transient errors are told apart. A single statement outside a transaction
  is sent again after a deadlock; a lost connection outside a transaction is opened again (persistent Velocity
  workers survive a server restart); inside a transaction the transaction is invalid. `retryOnDeadlock()` runs a whole
  transaction again.
- **Locks**: `lock()` takes `TABLOCKX, HOLDLOCK` in a transaction; `getAppLock()` / `releaseAppLock()` give named
  session locks (`sp_getapplock`).
- **Temporary tables**: `CREATE TEMPORARY TABLE x` becomes `#x`; the driver drops those left at the next request reset.
- **IN lists** in chunks joined with `OR`; an empty list is `(1=0)`.
- **Catalog** from `sys.*`: tables, sequences, views, triggers, indexes; `tableColumns()` for the schema read-back.

## Helpers every driver offers

Kernel SQL that is MySQL-only today becomes portable through driver helpers, never through per-engine branches:
`sqlLimit()`, `sqlUpsert()` (here `MERGE ... WITH (HOLDLOCK)`), `sqlGroupConcat()` (`STRING_AGG`),
`sqlUnixTimestamp()` / `sqlFromUnixTimestamp()` (`DATEDIFF_BIG`, safe past 2038) and `sqlQuoteIdentifier()`. Every
driver has them: `eZDBInterface` writes MySQL's words, each driver names its dialect (`sqlHelperDialect()`), and the SQL
comes from `expDBSQLHelpers` (mysql, postgresql, sqlite, oracle, sqlserver, generic).

## Schema handler

`expSQLServerSchema` (`dbschema.ini`, `SQLServer` and `sqlserver`) turns `.dba` schemas into SQL Server DDL and reads a
database back: `NVARCHAR` text, `INT IDENTITY` for `auto_increment`, primary keys `PK_<table>`, defaults
`DF_<table>_<column>` (so a column with a default can be dropped). SQL Server indexes at most 1,700 bytes of a key and
no `NVARCHAR(MAX)` column: a text column in an index key is `NVARCHAR(840)` (eight kernel columns, such as the URL alias
text and `path_identification_string`); other MAX columns and columns past 1,700 bytes of a non-unique index are
included columns. The kernel schema and data were created in SQL Server 2022 with it and read back with no difference.

## Read replicas and failover

`UseSlaveServer=enabled` with the Always On listener (or a secondary) as `SlaveServerArray`: plain SELECTs of an
anonymous GET request go there with `ApplicationIntent=ReadOnly` (read-only routing). Writes, locks, `#temp` tables,
sequences, session values, transactions and signed-in users stay on the primary. A replica that is not readable
(`DATABASEPROPERTYEX(..., 'Updateability')`) or further behind than `SlaveServerMaxLag` seconds is not used. For an
availability group across subnets set `MultiSubnetFailover=enabled`.

## Tested toolchain (W0)

Measured on 2026-11-24 on an AlmaLinux 10.3 host, SQL Server 2022 CU28 (16.0.4325.4) in Microsoft's Linux container,
Developer edition. Every build ran in a throwaway container; the host's PHP configuration was not touched.

| PHP | sqlsrv / pdo_sqlsrv | Result |
|---|---|---|
| 8.0.30 | 5.11.1 | both build; `sqlsrv` passes; `pdo_sqlsrv` 5.11 reads `TrustServerCertificate=yes` as false (the driver writes 1/0) |
| 8.1.34, 8.2.36 | 5.12.0 | both build and pass (UTF-8 round trip of emoji) |
| 8.3.37, 8.4.28, 8.5.13 | 5.13.3 | both build and pass |
| 8.5.13 (Plesk) | 5.13.3 | both build against Plesk's headers and pass; `PDO_ODBC` (built in) passes too |

- Microsoft ODBC Driver 18 for SQL Server 18.7.1.1 and `sqlcmd` 18 from Microsoft's RHEL 10 repository run on
  AlmaLinux 10; ODBC Driver 18 encrypts by default, so a self-signed server needs `TrustServerCertificate`.
- `PDO_ODBC` sends the statement text in the client code page: `N'ä'` arrived as `N'Ã¤'`, a bound parameter intact.
  It also prepares every `query()`, so `SET` options of such a call end with it. It reads `VARCHAR` columns in the
  client code page too (emoji and CJK lost even in a UTF-8 collation): keep text in `NVARCHAR`. A bound string in a
  `CAST(? AS DATETIME2)` is typed by ODBC and failed to convert (22018), so the driver writes the epoch with
  `DATETIME2FROMPARTS()`. It sent 2,101 parameters without complaint, where `sqlsrv` refuses them; the driver's
  budget of 2,000 applies on every layer.
- `pdo_sqlsrv` has MARS on by default; under MARS a `BEGIN TRANSACTION` batch was rolled back at the end of the
  batch. The driver's DSN switches MARS off.
- ODBC's idle connection resiliency (`ConnectRetryCount`, on by default) reconnected a killed session behind the
  driver's back; it is off (`ConnectRetryCount=0`) so that the driver, which knows about `#temp` tables and open
  transactions, decides.
- `pdo_dblib`: FreeTDS 1.5 is installed on the host, but none of the PHP builds has `pdo_dblib`; the layer is
  covered by the DB-less tests only.

## Tests

`tests/tests/lib/ezdb/sqlserver/` (DB-less, testsuite `sqlserver`, also part of `lib`) and `.../sqlserver/live/`
(testsuite `sqlserver-live`; skipped without `EXP_TEST_SQLSERVER_HOST`). The GitHub job `sqlserver` runs the live tests
against SQL Server 2017, 2019, 2022 and 2025 containers on all three layers; `sqlserver-windows-localdb` is a smoke run
on Windows LocalDB when the runner has it.

| File | What |
|---|---|
| `expSQLServerLexerTest` | the tokens (lossless on random bytes) |
| `expSQLServerDialectNamesTest` | brackets, 128/116 character names, every reserved word |
| `expSQLServerDialectLiteralsTest` | escaping, N'' literals, bound parameters, the 2,000 literal budget |
| `expSQLServerDialectPagingTest` | OFFSET/FETCH in every statement shape |
| `expSQLServerDialectStatementsTest` | statement kinds, replica routing, INSERT parsing, identity, temp tables |
| `expSQLServerDialectFunctionsTest` | upsert, STRING_AGG, timestamps, SUBSTRING, CONCAT, md5, IN lists |
| `expSQLServerConnectionSettingsTest` | addresses, DSNs, encryption, session setup, versions, replicas, layers |
| `expSQLServerErrorMapTest` | error numbers, SQLSTATEs, retry and transaction rules |
| `expSQLServerDBTest` | the driver over a recording transport: what it sends and how it reacts |
| `expSQLServerBuilderPropertyTest` | property tests over random statements |
| `live/expSQLServerLiveTest` | the same against a server, per layer (126 cases) |

## Traceability: the old extension's problems

Each problem found in xrowmssql's `ezmssqldb.php`, and the tests that keep it from coming back.

| # | Old problem | Now | Tests |
|---|---|---|---|
| 1 | `offset` read from the first row and only renumbered: every page repeated the first rows | OFFSET on the server | `PG-07`, `DB-02` testPagingIsSentToTheServer, live testOldOffsetBugIsGone, testPageEqualsSliceOfOrderedRows |
| 2 | `limit` applied in PHP over a static cursor of the whole result | FETCH NEXT on the server; limit 0 sends nothing | `PG-05`, testLimitZeroSendsNothing, live testLargeResultSet |
| 3 | `lastSerialID()` from `@@identity` (a trigger's id) | `SCOPE_IDENTITY()` in the same batch | testInsertCapturesScopeIdentityInTheSameBatch, live testTriggerDoesNotChangeLastSerialID, testScopeIdentityInALaterBatchIsLostAfterABoundInsert |
| 4 | `IDENTITY_INSERT` switched on and off around every INSERT (three round trips; left on after a failure) | only for explicit ids, off again after a failure; NULL/0 left out | `ST-04`, testIdentityInsertIsSwitchedOffAfterAFailure, live testExplicitIdsAndIdentityInsertSwitchedOffAfterFailure |
| 5 | `N''` added by regular expressions (gave up on odd quote counts and backslash-quote) | lexer; bound parameters | `LI-03`..`LI-05`, `PR-01`..`PR-03`, live testTextRoundTripsBound / AsNLiteral |
| 6 | reads and writes routed by `stristr($sql, 'select')` | lexer-based `isReplicaReadable()` | `ST-02`, `PR-07`, `DB-09` |
| 7 | `LENGTH(` rewritten to `LEN(` by regular expression (inside literals too; `LEN` drops trailing spaces) | no rewriting; `charLength()` | `FN-08`, live testTrailingSpacesAndCase |
| 8 | `CREATE TEMPORARY TABLE` became a permanent table | `#table`, dropped at request reset | `ST-05`, `DB-07`, live testTemporaryTables |
| 9 | `arrayQuery()`'s `column` by name never worked | by name and by position | testColumnByName, testColumnByPosition |
| 10 | the identity column lookup never stored its result (a catalog query per insert) | read once per connection, again after DDL | testIdentityColumnsAreReadOncePerConnection, ...AgainAfterDdl |
| 11 | `md5()` through undocumented `fn_varbintohexstr` over UTF-16; `subString()` cut at 8,000 | `CONVERT(..., 2)` over UTF-8; no cut | `FN-04`, `FN-06`, live testMd5EqualsPhp, testMegabyteOfText |
| 12 | `sqlsrv` only on Windows, `dblib` only elsewhere, MySQL charset names, begin/commit errors ignored | four layers anywhere, UTF-8 only, errors mapped | `CN-08`, `ER-*`, `DB-05` |

| Dialect and theory point | Tests |
|---|---|
| reserved words, `[]` quoting, 128/116 character names | `NM-01`..`NM-03`, live testNameLongerThan128IsRefusedByTheServer |
| 2,100 parameters | testMoreThanTheParameterBudgetFallsBack, live testServerRefusesMoreThan2100Parameters, testManyStringLiteralsStillWork |
| 900/1,700 byte index keys, NVARCHAR(MAX) not a key | live testIndexKeyLimits |
| empty string is not NULL; trailing spaces; case-insensitive default collation | live testEmptyStringIsNotNull, testTrailingSpacesAndCase |
| VARCHAR in a Latin collation loses characters; UTF-8 collation keeps them | live testVarcharInALatinCollation..., testVarcharInAUtf8Collation... |
| deadlock 1205, lock timeout 1222, RCSI readers | live testDeadlockVictimAndRetry, testLockTimeout, testReaderIsNotBlockedByWriterWithRcsi |
| reconnect after a lost session | `DB-06`, live testKilledSessionIsOpenedAgain, testKilledSessionInsideTransactionFails |
| dates past 2038 | `FN-03`, live testUnixTimestampRoundTrip |
| MS Access, generic ODBC | later waves: the ODBC DSN rules (`CN-02`, `CN-04`) are shared |
