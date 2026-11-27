# IBM Informix driver

This page is for administrators who want to use IBM Informix with Exponential, and for contributors who work on the
driver. From Exponential 6.0.15 the kernel has an Informix driver, `expInformixDB` in `lib/ezdb/classes/`, with its
schema handler `expInformixSchema`, the kernel schema in `kernel/sql/informix/kernel_schema.sql`, installer entries
and a data table engine. It is the [Db2 driver](db2-database-driver.md) with Informix's SQL and catalogue: both reach
their server through IBM's Data Server Driver, Informix through its DRDA listener.

**Status.** A whole site runs on Informix 15: `install.php --db=informix` installs the multisite package (16 steps,
0 errors, 9 of 9 checks), and its front page, admin login and search answer. The kernel schema with its data, upgrades
and the portable SQL helpers pass their live tests on Informix 15 and 14.10. Not: a schema read back equal to the `.dba`
(Informix keeps no column descriptions), `LIKE` beyond the first 32,739 bytes of a document, `GROUP_CONCAT`, a DFS
cluster backend and read replicas.

## Contents

1. [Supported servers and PHP](#supported-servers-and-php)
2. [Settings](#settings)
3. [Preparing the server and the database](#preparing-the-server-and-the-database)
4. [Installing a site](#installing-a-site)
5. [How the kernel's SQL becomes Informix's](#how-the-kernels-sql-becomes-informixs)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Data table connections](#data-table-connections)
8. [Licences](#licences)
9. [Tests](#tests)

## Supported servers and PHP

| | Supported | Why |
|---|---|---|
| Informix 15 (15.0.1) | yes | tested on every push (GitHub job `db2`) and with a whole site |
| Informix 14.10 | yes | the floor: a `SELECT` without `FROM`, `DBINFO('utc_current')`, `MERGE`; tested on every push (14.10.FC9W1) |
| Informix 12.10 and older | no | out of IBM's support, not tested |
| PHP with `ibm_db2` | yes | IBM's Data Server Driver speaks DRDA, which Informix answers on a `drsoctcp` listener; tested on PHP 8.5 and in CI on 8.3 |
| PHP with `pdo_ibm` only | **no** for a site | `pdo_ibm` 1.7 ends PHP with a segmentation fault when it reads a CLOB or TEXT of Informix (measured with Informix 15); a data table connection without such columns works on it |
| `pdo_informix` (PECL) and Informix's own ODBC driver (CSDK) | not used | `pdo_informix` has not been maintained for PHP 8; the CSDK needs an IBM account to download, while IBM's Data Server Driver is a free download |

`Layer=auto` takes `ibm_db2`, else `pdo_ibm`. Both come from PECL and need IBM's Data Server Driver for ODBC and CLI;
they and PHP's ODBC extensions exclude each other, see [the Db2 page](db2-database-driver.md#supported-servers-and-php).

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=informix
Server=ifx.example.com
Port=9089
User=informix
Password=...
Database=exponential
```

`ImplementationAlias[informix]` (and `ifx`) names `expInformixDB`. `Port` is a listener of the `drsoctcp` protocol in
the server's `sqlhosts` (9089 in IBM's container), not the `onsoctcp` port (9088) of Informix's own clients.

| `[InformixSettings]` | Meaning |
|---|---|
| `Layer` | `auto` (`ibm_db2`, else `pdo_ibm`), `ibm_db2`, `pdo_ibm` or `pdo_odbc` |
| `Security`, `ConnectionOptions`, `StatementConcentrator` | as on Db2; the concentrator is `disabled` by default (Informix gains little from it: 400 statements in 2,107 ms with it, 2,818 ms without) |
| `LockWait` | seconds a statement waits for a lock (`SET LOCK MODE TO WAIT`; Informix does not wait at all by default) (30) |
| `DeadlockRetries`, `DeadlockRetryWait`, `ReconnectOnLoss`, `InListChunkSize` | as on Db2 |

Every connection also reads the last committed version of a row a writer holds (`SET ISOLATION TO COMMITTED READ LAST
COMMITTED`, as InnoDB does) and allows line breaks in string literals (`IFX_ALLOW_NEWLINE`), which the kernel's XML has.

## Preparing the server and the database

**A dbspace of 16 KB pages.** Informix keys at most about a fifth of a page per index; in the root dbspace of 2 KB pages
that is under 400 bytes, too short for the kernel's indexes of text. A dbspace of 16 KB pages needs a buffer pool of
that size, which needs a physical log buffer of at least 256 KB:

```bash
# onconfig: PHYSBUFF 512, and a buffer pool for 16 KB pages; then restart the server
BUFFERPOOL size=16k,buffers=6000,lrus=8,lru_min_dirty=50,lru_max_dirty=60
onspaces -c -d dbs16k -k 16 -p /path/dbs16k.000 -o 0 -s 2000000
```

**A default sbspace.** The columns that hold documents of any length are `CLOB`s, which live in a smart blob space;
comparing one with `=` needs the server's default sbspace (`SBSPACENAME`):

```bash
onspaces -c -S sbs1 -p /path/sbs1.000 -o 0 -s 500000 -Df LOGGING=ON
onmode -wf SBSPACENAME=sbs1
```

**The database** must be logged (DRDA clients need transactions) and UTF-8:

```bash
export DB_LOCALE=en_us.utf8 CLIENT_LOCALE=en_us.utf8
echo "CREATE DATABASE exponential IN dbs16k WITH LOG;" | dbaccess sysmaster -
```

## Installing a site

```bash
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --apply --db=informix \
    --db-host=ifx.example.com --db-port=9089 --db-name=exponential --db-user=informix
```

The kickstarter takes `Type=informix`, the setup wizard offers IBM Informix when PHP has `ibm_db2` or `pdo_ibm`.

## How the kernel's SQL becomes Informix's

The rules Informix shares with Db2 come from `expDb2Dialect` (the lexer, the bit operators as `BITAND()`, `BITOR()`,
`BITNOT()`, `ESCAPE '\'` for a LIKE pattern with a backslash, `CONCAT()` of more than two as `||`, `TRUNCATE` as
`DELETE`). What differs (`expInformixDialect`):

| The kernel writes | Informix gets |
|---|---|
| `` `name` ``, `"name"` | `name`, unquoted, in lower case: Informix reads `"x"` as a string unless the client sets `DELIMIDENT`, so no name is ever quoted |
| `... LIMIT 10 OFFSET 20`, `LIMIT 20, 10` | `SELECT SKIP 20 FIRST 10 ...` (Informix pages right after `SELECT`) |
| `LENGTH(x)`, `SUBSTRING(s, f, n)` | `CHAR_LENGTH(x)`, `SUBSTRING(s FROM f FOR n)` |
| `IFNULL(a, b)`, `NOW()`, `CURDATE()` | `NVL(a, b)`, `CURRENT YEAR TO SECOND`, `TODAY` |
| `MD5('literal')` | the hash computed by PHP (Informix has no MD5; the URL aliases only ask for the MD5 of a literal) |
| `MD5(column)`, `GROUP_CONCAT` | refused with `expDBUnsupportedException` |
| `CREATE TEMPORARY TABLE` | `CREATE TEMP TABLE ... WITH NO LOG` (Informix's own, of the session) |
| `INSERT INTO t (id) VALUES (NULL)` (a counter table) | `... VALUES (0)`: a `SERIAL` refuses `NULL` and numbers `0` |

**Types.** `auto_increment` is a `SERIAL`: Informix numbers a row without id or with `0` and moves its counter past an
id an insert gives itself, so no sequence has to be corrected after a data file. `lastSerialID()` returns what
`DBINFO('sqlca.sqlerrd1')` said right after the connection's insert. `varchar(n)` is `VARCHAR(4n)` up to 255 bytes and
`LVARCHAR(4n)` above (Informix counts bytes; UTF-8 takes up to four per character). `text` and `longtext` are
`LVARCHAR` too, and a row is at most 32,767 bytes, so the text columns of a table share what its other columns leave
(`expInformixSchema::fitRow()`; at least 4,000 bytes each, `ezcollab_item.data_text1` to `3` get 10,565). The columns
that hold documents of any length (`ezcontentobject_attribute.data_text`, `ezsession.data` ...:
`expDb2Schema::LARGE_TEXT_COLUMNS`) are `CLOB`s in the default sbspace: a literal fills one, `=` compares one, `LIKE`
does not (Informix has no `LIKE` for a `CLOB`). Informix's `TEXT` is not used: a literal cannot fill it (`-617`), and
`pdo_ibm` cannot read it. Informix has no column descriptions, so a schema read back has `varchar` where the `.dba` has
`longtext`.

## What works and what does not

Measured on 2026-11-26 with the live tests (below) and a full installation, on Informix Developer Edition 15.0.1.0.3 (2
cores, 3 GB) and 14.10.FC9W1 (1 core) in IBM's containers, with PHP 8.5.13, `ibm_db2` 2.3.1 and `pdo_ibm` 1.7.0 over IBM's
Data Server Driver 12.1.4, on a test server under load (load average 15 to 28 on 12 cores).

| | Informix 15 | Informix 14.10 |
|---|---|---|
| connect over DRDA, version, catalogue | works | works |
| kernel schema: 153 tables and their indexes | works, 2.5 to 3.3 s | works |
| kernel data (`share/db_data.dba`, 3,837 rows with explicit ids), next id after the data | works, 26 to 28 s | works |
| schema read back equal to the `.dba` (upgrade check) | **does not**: no column descriptions, so text and lengths read back as Informix has them | same |
| upgrades: add a column with a default to a table with rows, change its type, drop it; add and drop an index | works | works |
| `lastSerialID()` per table and per connection | works | works |
| paging (`SKIP`/`FIRST`), transactions (rollback, commit, nested), `FOR UPDATE`, temporary tables | works | works |
| `sqlUpsert()` (MERGE), `sqlLimit()`, `sqlUnixTimestamp()`, `md5()` of a literal | works | works |
| `sqlGroupConcat()`, `md5()` of a column | **does not**: refused with `expDBUnsupportedException` | same |
| text: Unicode, 15,000 bytes in an `LVARCHAR`, documents of any length in a `CLOB` (a package page of 69,238 characters) | works | works |
| `LIKE` on a document (`CLOB`) | works within its first 32,739 bytes | same |
| a whole site: `install.php --db=informix`, the multisite package (291 objects) | works: 16 steps, 0 errors, 9 of 9 checks, 2,016 s | not run |
| the site's pages through PHP's built-in server: front page, admin login (sign-in answers 302 to the two-factor setup), search page | 200 each; front page 13.7 s cold, 1.9 s warm, admin login 0.1 to 1.9 s, search page 1.2 to 2.5 s on the loaded test server | not run |
| the kernel's search (`eZSearch::search()`: media, exponential, the) | 15, 7 and 115 hits | not run |
| `pdo_ibm` | a data table connection without `CLOB` or `TEXT` columns; **not** a site: PHP ends with a segmentation fault reading a `CLOB` | same |
| DFS cluster backend, read replicas | **no** | no |
| `createDatabase()`, `removeDatabase()` | **no**: the database is made with `dbaccess` before the install | no |

## Data table connections

The data table layer has the engine `informix`: a named connection opens through `expInformixDB`, and tables are made
and upgraded through `expInformixSchema`. It is also the way to import from Informix: its tables can be read as data
tables, or copied with the data table tools.

```ini
[Connection_legacy]
Engine=informix
Server=ifx.example.com
Port=9089
Database=stores
User=reader
Secret=env:IFX_PASSWORD
```

## Licences

| Part | Licence |
|---|---|
| `expInformixDB`, `expInformixDialect`, `expInformixSchema` | GNU GPL v2.0 or later, as the kernel |
| Informix Developer Edition (the container `icr.io/informix/informix-developer-database`, used by the tests) | free of charge for development and testing only, IBM's licence for the Developer Edition; accepted with `LICENSE=accept`. Production use needs a paid edition |
| IBM Data Server Driver for ODBC and CLI, `ibm_db2`, `pdo_ibm` | as on [the Db2 page](db2-database-driver.md#licences) |

## Tests

The Informix tests are the Db2 tests' (the testsuites `db2` and `db2-live`): the dialect, the driver over a recording
transport and the schema handler without a server, and the live test against an Informix server
(`EXP_TEST_INFORMIX_HOST`, `_PORT`, `_USER`, `_PASSWORD` or `_PASSWORD_FILE`, `_DATABASE`). See
[the Db2 page](db2-database-driver.md#tests).
