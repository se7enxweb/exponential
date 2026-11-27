# IBM Db2 driver

This page is for administrators who want to run Exponential on IBM Db2, and for contributors who work on the driver.
From Exponential 6.0.15 the kernel has a Db2 driver, `expDb2DB` in `lib/ezdb/classes/`, with its schema handler
`expDb2Schema`, the kernel schema in `kernel/sql/db2/kernel_schema.sql`, installer entries and a data table engine.
Its sibling for IBM Informix is described in [Informix driver](informix-database-driver.md).

**Status.** A whole site runs on Db2 12.1: `install.php --db=db2` installs the multisite package (16 steps,
0 errors, 9 of 9 checks), and its front page, admin login and search answer. The kernel schema with its data, the schema
read back, upgrades and the portable SQL helpers pass their live tests on Db2 12.1 and 11.5, on both PHP layers. Not
yet: a DFS cluster backend (several web servers on one Db2 database) and read replicas.

## Contents

1. [Supported servers and PHP](#supported-servers-and-php)
2. [Settings](#settings)
3. [Creating the database](#creating-the-database)
4. [Installing a site](#installing-a-site)
5. [How the kernel's SQL becomes Db2's](#how-the-kernels-sql-becomes-db2s)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Data table connections](#data-table-connections)
8. [Licences](#licences)
9. [Tests](#tests)

## Supported servers and PHP

| | Supported | Why |
|---|---|---|
| Db2 for Linux, UNIX and Windows 12.1 | yes | tested on every push (GitHub job `db2`, 12.1.6.0) and with a whole site |
| Db2 11.5 | yes | the floor: `LIMIT ... OFFSET`, `DROP ... IF EXISTS`, `HASH()` and `LISTAGG()`; tested on every push (11.5.9.0) |
| Db2 11.1 and older | no | older than the floor, not tested |
| Db2 for z/OS, Db2 for i | no | another SQL and another catalogue |
| PHP with `pdo_ibm` or `ibm_db2` | yes | the PECL extensions, built against IBM's Data Server Driver for ODBC and CLI; tested on PHP 8.5 and in CI on 8.3 |
| PHP's `odbc` or `pdo_odbc` with IBM's ODBC and CLI driver on Linux | **no** | the driver's `libdb2.so` gives unixODBC 32-bit lengths: a NULL comes back as garbage and row counts as `-4294967295` (measured). `Layer=pdo_odbc` is there for an ODBC driver with 64-bit lengths (Windows, or `libdb2o.so` of a full Db2 client) |

`pdo_ibm` and `ibm_db2` come from PECL (`pecl install pdo_ibm ibm_db2`, or `phpize && ./configure
--with-pdo-ibm=<clidriver>`, `--with-IBM_DB2=<clidriver>`); they need IBM's Data Server Driver for ODBC and CLI (the
`clidriver` folder, a free download from IBM: `linuxx64_odbc_cli.tar.gz`). `shivammathur/setup-php` installs them with
`extensions: pdo_ibm, ibm_db2`. `Layer=auto` takes `pdo_ibm`, else `ibm_db2`.

**pdo_ibm and the ODBC extensions exclude each other.** PHP loads extensions with their symbols global, and both
`pdo_ibm` (through `libdb2.so`) and PHP's `odbc`/`pdo_odbc` (through unixODBC) export the ODBC function names: the one
loaded first answers the calls of the other (measured: `pdo_ibm` then fails with "Can't open lib 'IBM DB2 ODBC DRIVER'"
from unixODBC). Microsoft's `sqlsrv` and `pdo_sqlsrv`, which use unixODBC too, end PHP at
start next to `pdo_ibm` (measured on PHP 8.5). A PHP that serves a Db2 or Informix site must not load `odbc`,
`pdo_odbc`, `sqlsrv` or `pdo_sqlsrv`; give it a scan directory of its own (`PHP_INI_SCAN_DIR`) or a pool without them.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=db2
Server=db.example.com
Port=50000
User=db2inst1
Password=...
Database=EXP

[Db2Settings]
Schema=
```

`ImplementationAlias[db2]` (and `ibm_db2`) names `expDb2DB`. Without `Server`, `Database` is a database alias
catalogued in the client (`db2cli.ini` or `db2dsdriver.cfg`).

| `[Db2Settings]` | Meaning |
|---|---|
| `Layer` | `auto` (`pdo_ibm`, else `ibm_db2`), `pdo_ibm`, `ibm_db2` or `pdo_odbc` |
| `Driver` | the ODBC driver's name or path for `Layer=pdo_odbc` |
| `Schema` | the schema of the site's tables (`SET SCHEMA` on every connection); empty: the user's own |
| `Security` | `SSL` for an encrypted connection |
| `ConnectionOptions` | more keywords of the connection string (`KEY=value;KEY=value`) |
| `StatementConcentrator` | `enabled` (the default): the connection asks for Db2's statement concentrator (`StmtConcentrator=WITHLITERALS`), which compiles statements that differ only in their literals once. The kernel writes its values as literals; without it every statement is compiled anew (measured: 400 inserts and selects in 1,822 ms without, 413 ms with). `db2 update db cfg using STMT_CONC LITERALS` does the same for every client |
| `DeadlockRetries` | how often a single statement outside a transaction is sent again after a deadlock (3) |
| `DeadlockRetryWait` | milliseconds before the first retry, doubled, tripled ... for the next (50) |
| `ReconnectOnLoss` | `enabled`: a lost connection outside a transaction is opened again (persistent Velocity workers) |
| `InListChunkSize` | values per `IN` list (1,000) |

A failed connection's message never holds the password.

## Creating the database

Db2 makes a database with a command of its instance, not with SQL, so neither the installer nor `createDatabase()` can
make one. As the instance owner:

```bash
db2 "CREATE DATABASE EXP USING CODESET UTF-8 TERRITORY US PAGESIZE 32 K"
db2 "CONNECT TO EXP"
db2 "CREATE USER TEMPORARY TABLESPACE USERTEMP PAGESIZE 32 K"
```

**The page size matters.** Db2 keys at most a quarter of a page per index (8 KB with pages of 32 KB) and counts a
`VARCHAR(n CODEUNITS32)` as 4n bytes. With 32 KB pages the kernel's URL alias texts, path strings and image paths keep
2,019 to 2,022 characters in their indexes; with the default 4 KB pages the kernel's unique keys do not fit at all.
**The user temporary tablespace** holds the kernel's temporary tables (search, node counts): without it they fail.
`EXTENDED_ROW_SZ` must stay `ENABLE` (the default since 10.5): the kernel's rows of several long texts are longer than a
page.

## Installing a site

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=db2 \
    --db-host=db.example.com --db-name=EXP --db-user=db2inst1
```

The kickstarter takes `Type=db2` in `[database_choice]`, the setup wizard offers IBM Db2 when PHP has `pdo_ibm` or `ibm_db2`.
`--db-action=remove` (the default) drops every table and sequence of the schema first.

## How the kernel's SQL becomes Db2's

Db2 takes much of MySQL's SQL as it is: `LIMIT n OFFSET m` and `LIMIT m, n`, `IFNULL()`, `NOW()`, `GREATEST()`,
`LEAST()`, `CONCAT(a, b)`, `DROP ... IF EXISTS`, `CREATE ... IF NOT EXISTS`, and an integer compared with a string.
The driver rewrites the rest on a lexer (`expDb2Dialect::rewrite()`), so a word inside a string literal or a comment is
never touched:

| The kernel writes | Db2 gets |
|---|---|
| `` `name` `` | `"NAME"` (Db2 stores an unquoted name in upper case) |
| a column named `user` | `"USER"` where it stands as a name (unquoted it is the special register) |
| `LENGTH(x)`, `SUBSTRING(s, f, n)`, `SUBSTR(s, f, n)` | `CHARACTER_LENGTH(x, CODEUNITS32)`, `SUBSTRING(s, f, n, CODEUNITS32)` (characters, not bytes; `SUBSTR()` fails past the end of a text) |
| `CONCAT(a, b, c)` | `(a \|\| b \|\| c)` (Db2's `CONCAT()` takes two) |
| `MD5('literal')`, `MD5(x)` | the hash of the literal computed by PHP; `LOWER(HEX(HASH(x, 0)))` |
| `a & b`, `a \| b`, `~a` (the language masks) | `BITAND(a, b)`, `BITOR(a, b)`, `BITNOT(a)` |
| `LIKE 'a\_%'` | `LIKE 'a\_%' ESCAPE '\'` (Db2 has no default escape character) |
| `SELECT ... FOR UPDATE` | `SELECT ... WITH RS USE AND KEEP UPDATE LOCKS` (Db2 refuses `FOR UPDATE` with `ORDER BY` or a join) |
| `SELECT 1` | `SELECT 1 FROM SYSIBM.SYSDUMMY1` |
| `TRUNCATE TABLE t` | `DELETE FROM t` (Db2's `TRUNCATE` must be the first statement of a unit of work) |
| `INSERT INTO t DEFAULT VALUES` | not written: `supportsDefaultValuesInsertion()` is false, the kernel inserts `(id) VALUES (NULL)` |
| a string literal over 16,000 bytes | a bound parameter (Db2 refuses a literal over 32,672 bytes) |
| `CREATE TEMPORARY TABLE` | a created global temporary table, `ON COMMIT PRESERVE ROWS NOT LOGGED` |

**Types.** `varchar(n)` and `char(n)` are `VARCHAR(n CODEUNITS32)`: n characters, as MySQL counts them. `text` and
`longtext` are `VARCHAR(32672)` (bytes), which compares, sorts and takes `DISTINCT` like any string; the columns that
hold documents of any length (`ezcontentobject_attribute.data_text`, `ezsession.data`, the workflow and audit
payloads: `expDb2Schema::LARGE_TEXT_COLUMNS`) are `CLOB(1G)`, which compares with `=` and `LIKE` but cannot be sorted or
indexed. A text column in an index is `VARCHAR(n CODEUNITS32)` with the n that fits the key. Column descriptions
(`COMMENT ON COLUMN ... IS 'exp:longtext'`, `'exp:char'`, `'exp:tinyint'`) keep the `.dba` type, so the schema reads back
equal to the `.dba`.

**New ids.** An `auto_increment` column is an `INTEGER` with the sequence `S_<table>` (described as `table.column`)
and the trigger `T_<table>_BI`, which numbers a row without id, `NULL` or `0`. `lastSerialID()` reads `PREVIOUS VALUE
FOR` the sequence, which Db2 keeps per connection: always the id of this connection's own insert. After a data file
is loaded with its own ids, `correctSequenceValues()` restarts every sequence past the highest id of its table (the
installer calls it, as on PostgreSQL).

**Temporary tables.** A kernel `CREATE TEMPORARY TABLE` (search, node counts, permissions) becomes a created global
temporary table: its definition is permanent and shared, its rows belong to the connection. The definition is made
once, over a second connection so it is committed at once; later requests empty their own rows instead. The names
are fixed per pattern (`ezsearch_tmp_0`), so no definition is left behind per request.

**Connections.** Opening a connection to Db2 costs about 0.8 s on the test server (Db2's `AUTHENTICATION=SERVER` checks
the password against the operating system for every connection; a connection to Informix takes 17 ms on the same
host). Turn on persistent connections (`[DatabaseSettings] UsePersistentConnection=enabled`): `pdo_ibm` and `ibm_db2`
keep one per PHP-FPM or Velocity worker, and the measured pages got three to forty times faster.

**Transactions and locks.** pdo_ibm runs in autocommit mode; an eZDB transaction is a Db2 unit of work from `begin()`
to `commit()`. `lock()` (MySQL's `LOCK TABLES`) opens a transaction and holds the rows it writes. A deadlock or a
rollback after a lock timeout (`SQL0911N`) of a single statement outside a transaction is sent again.

## What works and what does not

Measured on 2026-11-26 with the live tests (below) and a full installation, on Db2 Community Edition 12.1.6.0 and
11.5.9.0 (the official containers, 3 cores and 6 GB for 12.1) with PHP 8.5.13, `pdo_ibm` 1.7.0 and `ibm_db2` 2.3.1 over
IBM's Data Server Driver 12.1.4, on a test server under load (load average 15 to 28 on 12 cores).

| | Db2 12.1 | Db2 11.5 |
|---|---|---|
| connect, version, catalogue (`relationList()`, `eZTableList()`) | works | works |
| kernel schema: 153 tables, their indexes, sequences and triggers | works, 71 s | works |
| kernel data (`share/db_data.dba`, 3,837 rows with explicit ids), next id after the data (`correctSequenceValues()`) | works, 57 s (before the statement concentrator) | works |
| schema read back equal to the `.dba` (upgrade check) | works | works |
| upgrades: add a column with a default to a table with rows, change its type, drop it; add and drop an index | works (a `REORG` after the `ALTER`s that need one) | works |
| `lastSerialID()` per table and per connection | works | works |
| paging, transactions (rollback, commit, nested), `FOR UPDATE` row locks, temporary tables | works | works |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlGroupConcat()` (LISTAGG, ordered), `sqlUnixTimestamp()` both ways past 2038, `md5()` | works | works |
| text: Unicode, 30,000 bytes in a `VARCHAR(32672)`, documents of any length in a `CLOB` (a package page of 69,238 characters) | works | works |
| a whole site: `install.php --db=db2`, the multisite package (291 objects) | works: 16 steps, 0 errors, 9 of 9 checks; 1,401 s with the statement concentrator (over an installed site, `--db-action=remove`), 3,760 s when the first 100 objects were stored without it | not run |
| the site's pages through PHP's built-in server: front page, admin login (sign-in answers 302 to the two-factor setup), search page | 200 each. With persistent connections: front page 3.8 s cold, 2.0 s warm, admin login 0.05 to 0.25 s, search page 0.5 to 1.6 s; without them every request pays the connection (front page 8.2 to 13.9 s, admin login 2.4 to 3.3 s) | not run |
| the kernel's search (`eZSearch::search()`: media, exponential, the) | 15, 7 and 115 hits | not run |
| both PHP layers, `pdo_ibm` and `ibm_db2` | works | works |
| a text in an index longer than its `VARCHAR` (a URL alias of more than 2,019 characters) | **does not**: refused (value too long) | same |
| database pages of 4 KB (Db2's default) | **does not**: the kernel's unique keys do not fit; pages of 32 KB are needed | same |
| PHP's `odbc`/`pdo_odbc` with the ODBC and CLI driver on Linux | **does not**: 32-bit lengths under unixODBC (see above) | same |
| DFS cluster backend (several web servers) | **not yet** | not yet |
| read replicas | **no** | no |
| `createDatabase()`, `removeDatabase()` | **no**: Db2 makes and drops databases with commands of its instance | no |

## Data table connections

The data table layer has the engine `db2` (`datatable.ini [ConnectionSettings] Engines[]`): a named connection to a Db2
database opens through `expDb2DB`, and `expDataTableSchema` makes and upgrades tables on it through `expDb2Schema`.
Such a connection is also the way to import from Db2: its tables can be read as data tables, or copied with the data
table tools.

```ini
[Connection_erp]
Engine=db2
Server=db2.example.com
Port=50000
Database=ERP
User=reader
Secret=env:ERP_DB_PASSWORD
```

## Licences

| Part | Licence |
|---|---|
| `expDb2DB`, `expDb2Dialect`, `expDb2Schema` and the transports | GNU GPL v2.0 or later, as the kernel |
| Db2 Community Edition (the container `icr.io/db2_community/db2`, used by the tests) | free of charge, IBM's licence for Db2 Community Edition: at most 4 cores and 16 GB of memory per instance, no support; accepted with `LICENSE=accept`. Production use beyond those limits needs a paid edition |
| IBM Data Server Driver for ODBC and CLI (`clidriver`) | free of charge, IBM's licence terms in `clidriver/license`; may be redistributed with an application under those terms |
| `pdo_ibm`, `ibm_db2` (PECL) | Apache License 2.0 |

## Tests

| File | What |
|---|---|
| `tests/tests/lib/ezdb/db2/expDb2DialectTest.php` | the rewrite rules of Db2 and Informix, paging, helpers, temporary tables, errors, DSN, versions (DB-less) |
| `tests/tests/lib/ezdb/db2/expDb2DBTest.php` | both drivers over a recording transport: paging, ids, transactions, retries, reconnects, temporary tables (DB-less) |
| `tests/tests/lib/ezdbschema/expDb2SchemaTest.php` | types, sequences and triggers, index keys, upgrade statements, `kernel/sql/db2` and `kernel/sql/informix` (DB-less) |
| `tests/tests/lib/ezdb/db2/live/expDb2LiveTest.php` | live servers: ids per connection, paging, the kernel's words, transactions, Unicode, long text, temporary tables, the kernel schema and data, the schema read back, upgrades, the helpers |

The DB-less tests run in the testsuite `db2`, the live ones in `db2-live`: set `EXP_TEST_DB2_HOST`, `_PORT`, `_USER`,
`_PASSWORD` (or `_PASSWORD_FILE`), `_DATABASE` (a database of its own: the tests empty it) and `_LAYER` (`pdo_ibm` or
`ibm_db2`), and the same `EXP_TEST_INFORMIX_...` for Informix. The GitHub job `db2` runs them against Db2 12.1 and
Informix 15 on PHP 8.5, and Db2 11.5 and Informix 14.10 on PHP 8.3, Db2 on both layers, and installs a whole site on
Db2.
