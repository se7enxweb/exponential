# Firebird driver

This page is for administrators who want to run Exponential on Firebird, and for contributors who work on the driver.
From Exponential 6.0.15 the kernel has a Firebird driver, `expFirebirdDB` in `lib/ezdb/classes/`, with its schema
handler `expFirebirdSchema`, the kernel schema in `kernel/sql/firebird/kernel_schema.sql`, installer entries and a
data table engine.

**Status.** A whole site runs on Firebird 4.0 and 5.0: `install.php --db=firebird` installs the multisite package
(below), and the kernel schema with its data, upgrades and the portable SQL helpers pass their live tests on both
versions. Not yet: a DFS cluster backend (several web servers on one Firebird database) and read replicas.

## Contents

1. [Supported servers and PHP](#supported-servers-and-php)
2. [Settings](#settings)
3. [Creating the database](#creating-the-database)
4. [Installing a site](#installing-a-site)
5. [How the kernel's SQL becomes Firebird's](#how-the-kernels-sql-becomes-firebirds)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Data table connections](#data-table-connections)
8. [InterBase](#interbase)
9. [Tests](#tests)

## Supported servers and PHP

| | Supported | Why |
|---|---|---|
| Firebird 5.0, 4.0 | yes | tested on every push (GitHub job `firebird`) |
| Firebird 3.0 and older | no | names of at most 31 characters (the kernel has longer index names), no `CRYPT_HASH()`, no time zones |
| PHP 8.4, 8.5 with `pdo_firebird` | yes | |
| PHP 8.3 and older with `pdo_firebird` | no | their `pdo_firebird` keeps an implicit transaction open in autocommit mode: `beginTransaction()` fails ("There is already an active transaction"), a rollback undoes nothing, and other connections' writes stay invisible (measured on 8.3.37) |
| PHP's `interbase`/`fbird_*` extension | no | the driver speaks PDO only |

`pdo_firebird` is part of PHP; it needs Firebird's client library (`libfbclient2`, on Debian and Ubuntu the package
`firebird-dev` to build PHP with it). The 3.0 client library of Debian 12 and Ubuntu 24.04 talks to Firebird 4 and 5.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=firebird
Server=db.example.com
Port=3050
User=EXPONENTIAL
Password=...
Database=exponential

[FirebirdSettings]
Role=
Charset=UTF8
```

`ImplementationAlias[firebird]` (and `fb`) names `expFirebirdDB`. `Database` is an alias of the server's
`databases.conf` (recommended) or the path of the database file on the server; without `Server` the client library
opens the file itself (embedded) or through the local server.

| `[FirebirdSettings]` | Meaning |
|---|---|
| `Role` | the SQL role of the connection; empty: none |
| `Charset` | the connection character set; `UTF8`, which Exponential's text needs |
| `DeadlockRetries` | how often a single statement outside a transaction is sent again after an update conflict or a deadlock (3) |
| `DeadlockRetryWait` | milliseconds before the first retry, doubled, tripled ... for the next (50) |
| `ReconnectOnLoss` | `enabled`: a lost connection outside a transaction is opened again (persistent Velocity workers) |
| `InListChunkSize` | values per `IN` list (1,500, the limit of Firebird 4) |

A failed connection's message never holds the password.

## Creating the database

Firebird creates a database outside of a connection, so neither the installer nor `createDatabase()` can make one.
Create it with `isql` (or the official container's `FIREBIRD_DATABASE`), with pages of 16 KB and UTF8:

```sql
CREATE DATABASE 'exponential' USER 'SYSDBA' PASSWORD '...' PAGE_SIZE 16384 DEFAULT CHARACTER SET UTF8;
```

**The page size matters.** Firebird indexes at most a quarter of a page per key, and counts four bytes per
character of UTF8. With 16 KB the URL alias text, path strings and image paths of the kernel get 811 to 1,021
characters in their indexes; with the default 8 KB only about 400. The schema handler reads the page size of the
database and sizes the text columns of indexes to fit it.

## Installing a site

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=firebird \
    --db-host=db.example.com --db-name=exponential --db-user=EXPONENTIAL
```

The kickstarter takes `Type=firebird` in `[database_choice]`, the setup wizard offers Firebird when PHP has
`pdo_firebird`. `--db-action=remove` (the default) drops the tables, sequences and views of the database first.

## How the kernel's SQL becomes Firebird's

The kernel writes MySQL's words where its portable helpers (`sqlLimit()`, `sqlUpsert()`, `sqlForUpdate()` ...) are
not used; the driver rewrites each statement on a lexer (`expFirebirdDialect::rewrite()`), so a word inside a string
literal or a comment is never touched:

| The kernel writes | Firebird gets |
|---|---|
| `` `name` `` | `"NAME"` (Firebird stores an unquoted name in upper case) |
| a column named like a reserved word: `value`, `count`, `time`, `day`, `position` | `"VALUE"` etc. where the word stands as a name, not where it is a function or a type |
| `LENGTH()`, `IFNULL()`, `NOW()`, `GREATEST()`, `LEAST()` | `CHAR_LENGTH()`, `COALESCE()`, `LOCALTIMESTAMP`, `MAXVALUE()`, `MINVALUE()` |
| `CONCAT(a, b)`, `SUBSTRING(s, f, n)`, `MD5(x)` | `(a \|\| b)`, `SUBSTRING(s FROM f FOR n)`, `LOWER(HEX_ENCODE(CRYPT_HASH(x USING MD5)))` |
| `a & b`, `a \| b`, `~a`, `a >> n`, `a << n` (the language masks) | `BIN_AND(a, b)`, `BIN_OR(a, b)`, `BIN_NOT(a)`, `BIN_SHR(a, n)`, `BIN_SHL(a, n)` |
| `LIKE 'a\_%'` | `LIKE 'a\_%' ESCAPE '\'` (Firebird has no default escape character) |
| `... LIMIT 10 OFFSET 20`, `LIMIT 20, 10` | `OFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY` |
| `SELECT ... FOR UPDATE` | `SELECT ... FOR UPDATE WITH LOCK` (without `WITH LOCK` Firebird locks nothing) |
| `SELECT 1` | `SELECT 1 FROM RDB$DATABASE` |
| `TRUNCATE TABLE t` | `DELETE FROM t` |
| `DROP TABLE IF EXISTS t`, `CREATE ... IF NOT EXISTS` | the catalogue is asked first (Firebird 4 and 5 have neither) |
| a string literal over 16,000 bytes | a bound parameter (Firebird refuses a literal over 64 KB) |
| `CREATE TEMPORARY TABLE` | a global temporary table, `ON COMMIT PRESERVE ROWS` |

**New ids.** An `auto_increment` column is an `INTEGER` with the sequence `S_<table>` and the trigger
`T_<table>_BI`. The trigger numbers a row without id, `NULL` or `0` (MySQL's ways of asking for the next number),
moves the sequence past an id an insert gives itself (the rows of the data files, and the copies the kernel makes for
a new version), and keeps the id in the connection's context variables, where `lastSerialID()` reads it: always the
id of this connection's own insert.

**Temporary tables.** Firebird 4 and 5 have no temporary tables of a session. A kernel `CREATE TEMPORARY TABLE`
(search, node counts, permissions) becomes a global temporary table: its definition is permanent and shared, its rows
belong to the connection. The definition is made once, over a second connection so it is committed at once; later
requests empty their own rows instead. The names are fixed per pattern (`ezsearch_tmp_0`), so no definition is left
behind per request.

**Transactions.** pdo_firebird runs in autocommit mode (read committed); an eZDB transaction is a Firebird
transaction from `begin()` to `commit()`. `lock()` (MySQL's `LOCK TABLES`) opens a transaction and holds the rows it
writes; Firebird reserves whole tables only when a transaction starts (`SET TRANSACTION ... RESERVING`), which PDO
does not offer.

## What works and what does not

Measured on 2026-11-26 with the live tests (below) and a full installation, on Firebird 5.0.4 and 4.0 (the official
`firebirdsql/firebird` images) with PHP 8.4.28 and 8.5.13, pdo_firebird over the 3.0 client library.

| | Firebird 5.0 | Firebird 4.0 |
|---|---|---|
| connect, version, catalogue (`relationList()`, `eZTableList()`) | works | works |
| kernel schema: 153 tables, their indexes, sequences and triggers | works | works |
| kernel data (`share/db_data.dba`, rows with explicit ids), next id after the data | works | works |
| schema read back equal to the `.dba` (upgrade check) | works | works |
| upgrades: add a column with a default to a table with rows, alter, drop; add and drop an index | works | works |
| `lastSerialID()` per table and per connection | works | works |
| paging, transactions (rollback, commit, nested), `FOR UPDATE` row locks | works | works |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlUnixTimestamp()` both ways past 2038, `md5()` | works | works |
| `sqlGroupConcat()` | works, **without order**: `LIST()` has no `ORDER BY` | same |
| text in an index longer than its `VARCHAR` (a URL alias of more than 811 characters) | **does not**: refused (string truncation) | same |
| a whole site: `install.php --db=firebird`, the multisite package | works | works |
| lock waits (`FOR UPDATE`, a row another transaction writes) | work, **without a time limit**: pdo_firebird starts its transactions with `WAIT` and no `LOCK TIMEOUT`, and has no setting for one (`SET TRANSACTION` cannot be sent through it) | same |
| speed | a statement in its own transaction costs about 20 ms on the test server (the commit is written to disk), one inside a transaction about 4 ms; the multisite package (291 objects) installed in 21 and 22 minutes on a busy test server: 16 steps, 0 errors, 9 of 9 checks | same |
| DFS cluster backend (several web servers) | **not yet**: the other backends bound their lock waits (PostgreSQL's `lock_timeout`, SQL Server's `LOCK_TIMEOUT`), which pdo_firebird cannot | not yet |
| read replicas | **no** | no |
| `createDatabase()`, `removeDatabase()` | **no**: Firebird makes and drops databases outside a connection | no |
| PHP 8.3 and older | **no** (see above) | no |

## Data table connections

The data table layer has the engine `firebird` (`datatable.ini [ConnectionSettings] Engines[]`): a named connection
to a Firebird database opens through `expFirebirdDB`, and `expDataTableSchema` makes and upgrades tables on it through
`expFirebirdSchema`. Such a connection is also the way to import from a Firebird database: its tables can be read as
data tables, or copied with the data table tools.

```ini
[Connection_erp]
Engine=firebird
Server=fb.example.com
Port=3050
Database=erp
User=READER
Secret=env:ERP_DB_PASSWORD
```

## InterBase

InterBase is not supported. Its SQL parted from Firebird's long ago: no `IDENTITY` or `OFFSET ... FETCH`, no
`CRYPT_HASH()`, no global temporary tables of this kind, other catalogue columns, and its own client library, which
pdo_firebird is not built against. The driver reports an InterBase server as unsupported.

## Tests

| File | What |
|---|---|
| `tests/tests/lib/ezdb/firebird/expFirebirdDialectTest.php` | the rewrite rules, paging, helpers, temporary tables, errors, DSN, versions (DB-less) |
| `tests/tests/lib/ezdb/firebird/expFirebirdDBTest.php` | the driver over a recording transport: paging, ids, transactions, retries, reconnects, temporary tables (DB-less) |
| `tests/tests/lib/ezdbschema/expFirebirdSchemaTest.php` | types, sequences and triggers, index keys per page size, upgrade statements (DB-less) |
| `tests/tests/lib/ezdb/firebird/live/expFirebirdLiveTest.php` | a live server: ids, paging, transactions, locks, Unicode, long text, temporary tables, reserved names |
| `tests/tests/lib/ezdb/firebird/live/expFirebirdSchemaLiveTest.php` | a live server: the kernel schema and data, the schema read back, upgrades, the helpers |

The DB-less tests run in the testsuite `firebird` (and `lib`), the live ones in `firebird-live`: set
`EXP_TEST_FIREBIRD_HOST`, `_PORT`, `_USER`, `_PASSWORD` (or `_PASSWORD_FILE`) and `_DATABASE` (a database of its own:
the schema test empties it). The GitHub job `firebird` runs them against `firebirdsql/firebird:5` and `:4` as service
containers on PHP 8.4 and 8.5.
