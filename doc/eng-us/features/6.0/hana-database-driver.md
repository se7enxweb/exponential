# SAP HANA driver

This page is for administrators who want to run Exponential on SAP HANA, and for contributors who work on the driver.
From Exponential 6.0.15 the kernel has a SAP HANA driver, `expHANADB` in `lib/ezdb/classes/`, with its dialect
`expHANADialect`, the schema handler `expHANASchema`, installer entries and a data table engine.

**Status.** The driver, the kernel schema with its data (read back equal to the `.dba`), upgrades and the
portable SQL helpers pass their live tests on SAP HANA, express edition 2.00.088 (SPS 08), on both ODBC layers.
`install.php --db=hana` installs the multisite package (see the table below for how far and how fast). Not yet: a DFS
cluster backend and read replicas.

## Contents

1. [Supported servers and PHP](#supported-servers-and-php)
2. [Settings](#settings)
3. [The ODBC driver](#the-odbc-driver)
4. [Installing a site](#installing-a-site)
5. [How the kernel's SQL becomes HANA's](#how-the-kernels-sql-becomes-hanas)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Data table connections](#data-table-connections)
8. [Tests](#tests)

## Supported servers and PHP

| | Supported | Why |
|---|---|---|
| SAP HANA 2.0 SPS 05 to SPS 08, the express edition included | yes | measured on HANA, express edition 2.00.088 (SPS 08) |
| SAP HANA Cloud | written for, untested | same SQL; connect with `Encrypt=enabled` on port 443 |
| SAP HANA 2.0 before SPS 05, HANA 1.0 | no | the driver reports them as unsupported |
| PHP 8.0 to 8.5 with the `odbc` extension | yes | the default layer (`Layer=auto` takes it first) |
| PHP with `PDO_ODBC` only | yes, for everything but the schema | PDO reads `:name` as a parameter, and the triggers of `auto_increment` columns read their row as `:new_row.id`: a statement with a `:` goes over the `odbc` extension, so PDO_ODBC alone cannot make the kernel schema |

PHP has no HANA extension of its own; the driver speaks to SAP's HANA ODBC driver (`libodbcHDB.so` of the SAP HANA
client, HDBODBC) through unixODBC on Linux, or the ODBC driver manager on Windows.

**Licenses.** The SAP HANA client and SAP HANA, express edition come with SAP's own licenses (the express edition:
the SAP HANA express edition developer license, free for development and for production up to 32 GB of memory). Read
them before you download either; Exponential ships neither.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=hana
Server=hana.example.com
Port=39041
User=EXPONENTIAL
Password=...
Database=EXPONENTIAL

[HANASettings]
Driver=HDBODBC
```

`ImplementationAlias[hana]` (and `saphana`) names `expHANADB`. `Port` is the SQL port of the tenant database
(`3<instance>41`: 39041 for HANA express, 30041 for instance 00; 443 for HANA Cloud). `Database` is the **schema**
the site's tables are in, made at the first connection when it does not exist (`CreateSchema`).

| `[HANASettings]` | Meaning |
|---|---|
| `Driver` | the ODBC driver: its name in `odbcinst.ini` (`HDBODBC`) or the path of `libodbcHDB.so` |
| `Layer` | `auto` (the `odbc` extension, then PDO_ODBC), `odbc` or `pdo_odbc` |
| `TenantDatabase` | a tenant to reach through the system database's port (`DATABASENAME`); empty: the port's own |
| `Encrypt`, `ValidateCertificate` | an encrypted connection (`ENCRYPT=TRUE`; HANA Cloud needs it), the server certificate checked |
| `ConnectionString` | more `keyword=value` pairs of the HANA ODBC driver |
| `CreateSchema` | `enabled`: the schema of `Database` is made when it does not exist |
| `BindLiterals` | `enabled`: the literal values of a statement are sent as parameters (see below) |
| `TableType` | `column` (HANA's default) or `row`: the store of the tables the schema handler makes; a row table writes a single row about three times faster (the search index writes word by word) and is kept in memory whole |
| `DeadlockRetries`, `DeadlockRetryWait` | a single statement outside a transaction is sent again after a deadlock (133) |
| `ReconnectOnLoss` | `enabled`: a lost connection outside a transaction is opened again (persistent Velocity workers) |
| `InListChunkSize` | values per `IN` list (5,000) |

The connection string always has `CHAR_AS_UTF8=TRUE` (text as UTF-8) and `CURRENTSCHEMA`. A failed connection's
message never holds the password.

## The ODBC driver

On Linux, install the SAP HANA client and register its driver with unixODBC:

```ini
# /etc/odbcinst.ini (or $ODBCSYSINI/odbcinst.ini)
[HDBODBC]
Description=SAP HANA ODBC driver
Driver=/usr/sap/hdbclient/libodbcHDB.so
```

PHP needs the `odbc` extension (recommended) or `PDO_ODBC`. With PDO_ODBC the driver asks for the HANA driver's own
cursors (unixODBC's cursor library is often missing).

## Installing a site

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=hana \
    --db-host=hana.example.com --db-port=39041 --db-name=EXPONENTIAL --db-user=EXPONENTIAL
```

The user needs `CREATE SCHEMA` (or a schema made for it). The kickstarter takes `Type=hana` in `[database_choice]`.
The setup wizard lists HANA below the other databases when PHP has PDO_ODBC or the odbc extension and SAP's
ODBC driver (`[HANASettings] Driver`, HDBODBC) is a section of unixODBC's `odbcinst.ini` (`$ODBCSYSINI`, `/etc`,
`/etc/unixODBC`, `/usr/local/etc` or `~/.odbcinst.ini`) or a library path that exists; the database page takes the
server, the tenant's SQL port (39041), the user, the password and the schema. `--db-action=remove` (the default)
drops the tables and sequences of the schema first.

## How the kernel's SQL becomes HANA's

The kernel writes MySQL's words where its portable helpers (`sqlLimit()`, `sqlUpsert()` ...) are not used; the driver
rewrites each statement on a lexer (`expHANADialect::rewrite()`), so a word inside a string literal or a comment is
never touched:

| The kernel writes | HANA gets |
|---|---|
| `` `name` `` | `"NAME"` (HANA stores an unquoted name in upper case) |
| an alias that is a reserved word of HANA (`AS order`) | `AS "ORDER"` |
| `CONCAT(a, b, c)`, `CHAR_LENGTH()`, `SUBSTR()`, `CURDATE()` | `(a \|\| b \|\| c)` (HANA's `CONCAT` takes two), `LENGTH()`, `SUBSTRING()`, `CURRENT_DATE` |
| `MD5(x)` | `LOWER(BINTOHEX(HASH_MD5(TO_VARBINARY(TO_NVARCHAR(x)))))` |
| `UNIX_TIMESTAMP()`, `FROM_UNIXTIME(t)` | `SECONDS_BETWEEN(TO_TIMESTAMP('1970-01-01 00:00:00'), CURRENT_UTCTIMESTAMP)`, `ADD_SECONDS(...)` |
| `a & b`, `a \| b`, `~a` (the language masks) | `BITAND(a, b)`, `BITOR(a, b)`, `BITNOT(a)` |
| `a >> n`, `a << n` | `TO_BIGINT(FLOOR((a) / POWER(2, n)))`, `TO_BIGINT((a) * POWER(2, n))` (HANA has no shift) |
| `SELECT DISTINCT` over an NCLOB (`cc.*` of the content classes) | the statement without `DISTINCT`, the duplicate rows dropped by the driver |
| `LIKE 'a\_%'` | `LIKE 'a\_%' ESCAPE '\'` (HANA has no default escape character) |
| `... LIMIT 20, 10` | `LIMIT 10 OFFSET 20` (`LIMIT n OFFSET m` is HANA's own) |
| `SELECT 1` | `SELECT 1 FROM DUMMY` |
| `INSERT ... VALUES (...), (...)` | one `INSERT` per row (HANA takes one row per `VALUES`) |
| `TRUNCATE TABLE t` | `DELETE FROM t` (HANA's `TRUNCATE` commits the transaction it runs in) |
| `DROP TABLE IF EXISTS t`, `CREATE ... IF NOT EXISTS` | the catalogue is asked first |
| a comparison, `ORDER BY` or `GROUP BY` of a text column (`NCLOB`) | `TO_NVARCHAR(column)` (HANA cannot compare or sort an NCLOB) |
| `CREATE TEMPORARY TABLE` | a global temporary table |

**Literals as parameters.** HANA compiles every statement text it has not seen, about 20 ms each on the test server,
and the kernel writes its values into its statements. With `BindLiterals=enabled` the driver sends the values of an
`INSERT` and the values compared in a `WHERE` (`= 5`, `IN (...)`, `LIKE 'x%'`) as parameters, so a statement is
compiled once for all its values (`expHANADialect::parameterize()`). This took the kernel schema and data from 8.5 to
2.5 minutes, and indexing an article for the search from about 150 ms to 6 ms per word.

**Unicode in a statement's text.** HANA's ODBC driver reads the text of a statement (not its parameters) in a
single-byte code page, so an `ä` written into a statement arrives as two characters. The literals left in a statement
after the binding that hold more than ASCII are sent as `TO_NVARCHAR(BINTOSTR(X'...'))` of their bytes in CESU-8,
HANA's own UTF-8 (`expHANADialect::unicodeLiterals()`).

**New ids.** An `auto_increment` column is an `INTEGER` with the comment `exp:auto_increment`, the sequence
`S_<table>` and the trigger `T_<table>_BI`. The trigger numbers a row without id, `NULL` or `0` (MySQL's ways of
asking for the next number) and moves the sequence past an id an insert gives itself; `lastSerialID()` reads the
sequence's `CURRVAL`, which is always this session's own last number. An insert with an id of its own below the
sequence takes a number of it too, so ids may have gaps.

**Text columns.** `text` and `longtext` are `NCLOB`; a text column in an index is `NVARCHAR(5000)` (the comment
`exp:longtext` says it was text), as HANA indexes no NCLOB. A column that is an NCLOB in every table that has it is
compared and sorted as `TO_NVARCHAR(column)`; the driver reads the list of such columns once per process and schema.

**Index names.** HANA's index names are unique in a schema, the `.dba`'s per table: an index is stored as
`<TABLE>$<INDEX>` and read back without the table.

**Temporary tables.** A kernel `CREATE TEMPORARY TABLE` becomes a global temporary table: its definition is permanent
and shared, its rows belong to the session. The definition is made once, over a second connection (DDL commits in
HANA); later requests delete their own rows instead.

## What works and what does not

Measured on 2026-11-26 with the live tests (below) and a full installation, on SAP HANA, express edition
2.00.088.00 (the `saplabs/hanaexpress` image, 12 GB and 4 CPUs for the container, about 4 GB used) on a busy test
server (load 17 to 40 on 12 CPUs), PHP 8.5.13, the HANA ODBC driver of the same release through unixODBC 2.3.12.

| | SAP HANA 2.0 SPS 08 |
|---|---|
| connect (odbc extension and PDO_ODBC), version, catalogue (`relationList()`, `eZTableList()`), a missing schema made | works |
| kernel schema: 153 tables, their indexes, sequences and triggers | works |
| kernel data (`share/db_data.dba`, rows with explicit ids), next id after the data | works (an id an insert gives itself below the sequence takes a number: ids may have gaps) |
| schema read back equal to the `.dba` (upgrade check) | works |
| upgrades: add a column with a default to a table with rows, alter, drop; add and drop an index | works |
| `lastSerialID()` per table and per session | works |
| paging, transactions (rollback, commit, nested), `FOR UPDATE` row locks, `LOCK TABLE` | works |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlGroupConcat()` (ordered), `sqlUnixTimestamp()` both ways past 2038 | works |
| `md5()` | works; for a character outside the Basic Multilingual Plane (an emoji) the digest is of HANA's CESU-8, not of UTF-8 |
| Unicode text in and out (emoji included), 340 KB through a parameter, an NCLOB compared and sorted | works |
| `LENGTH()` of text with emoji | counts each such character as two (HANA keeps it as two UTF-16 units) |
| global temporary tables of the kernel (search) | works |
| the kernel schema and data in one transaction | 2.5 minutes with `BindLiterals` (8.5 without) |
| a statement on the test server | about 3 ms for `SELECT 1`, 6 to 14 ms for a single-row insert into a column table with its trigger |
| a whole site: `install.php --db=hana`, the multisite package | works: 16 steps, 9 of 9 checks, in 109 minutes on the busy test server (291 objects; most of it the search index, which writes word by word at 6 to 14 ms an insert). The front page, the admin login and a search answer 200 (0.2 to 0.7 s warm); the admin dashboard takes 18 to 40 s there (hundreds of statements at 3 ms or more each). The test tree had no packaged extensions (Layouts, Tags ...), so their rows were not loaded; no statement of the kernel failed |
| PDO_ODBC alone | the driver's live tests pass; making the schema's triggers needs the odbc extension next to it |
| DFS cluster backend (several web servers), read replicas | **not yet** |
| `createDatabase()` | makes a schema; `removeDatabase()` drops one with `CASCADE` |

## Data table connections

The data table layer has the engine `hana` (`datatable.ini [ConnectionSettings] Engines[]`): a named connection opens
through `expHANADB` (options `odbc_driver`, `layer`, `tenant`, `encrypt`, `connection_string`; the database is the
schema), and `expDataTableSchema` makes and upgrades tables on it through `expHANASchema`. Such a connection is also
the way to import from HANA: its tables can be read as data tables, or copied with the data table tools.

```ini
[Connection_erp]
Engine=hana
Server=hana.example.com
Port=30041
Database=ERP
User=READER
Secret=env:ERP_HANA_PASSWORD
```

## Tests

| File | What |
|---|---|
| `tests/tests/lib/ezdb/hana/expHANADialectTest.php` | the rewrite rules, paging, binding, Unicode literals, NCLOB columns, helpers, errors, connection string, versions (DB-less) |
| `tests/tests/lib/ezdb/hana/expHANADBTest.php` | the driver over a recording transport: missing schema, paging, ids, binding, transactions, locks, retries, reconnects, temporary tables (DB-less) |
| `tests/tests/lib/ezdbschema/expHANASchemaTest.php` | types, sequences and triggers, index names, the marks of char, text and auto_increment columns, upgrade statements (DB-less) |
| `tests/tests/lib/ezdb/hana/live/expHANALiveTest.php` | a live server: ids per session, paging, the kernel's words, transactions, Unicode and long text, NCLOB comparisons, temporary tables, errors, reconnect |
| `tests/tests/lib/ezdb/hana/live/expHANASchemaLiveTest.php` | a live server: the kernel schema and data, the schema read back, upgrades, the helpers |

The DB-less tests run in the testsuite `hana` (and `lib`), the live ones in `hana-live`: set `EXP_TEST_HANA_HOST`,
`_PORT`, `_USER`, `_PASSWORD` (or `_PASSWORD_FILE`), `_SCHEMA` (a schema of its own: the schema test empties it),
`_DRIVER` and `_LAYER`. The GitHub job `hana` runs them against SAP HANA, express edition (`saplabs/hanaexpress`),
with the HANA ODBC driver taken from the same image, on PHP 8.5 and both layers.
