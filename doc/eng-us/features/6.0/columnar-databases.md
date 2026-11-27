# Columnar databases: MonetDB and ClickHouse

This page is for administrators who want to keep a site, or tables of a site, in a columnar database, and for
developers who work on the drivers. Exponential 6.0.15 has a driver for each of two columnar database servers, both in
`lib/ezdb/classes/`, both without a PHP extension of their own:

| Database | `DatabaseImplementation` | Driver | Reached through | Level reached | Tested live |
|---|---|---|---|---|---|
| MonetDB | `monetdb` | `expMonetDBDB` | MonetDB's protocol MAPI, in plain PHP (`expMonetDBMapi`) | **full site database** | yes: Dec2025-SP5 (11.55.11) on the project's database node, Dec2023-SP4 and Dec2025-SP5 in CI |
| ClickHouse | `clickhouse` | `expClickHouseDB` | ClickHouse's HTTP interface (`expClickHouseHttp`, curl or PHP's HTTP stream) | **data table connection and import source** | yes: 25.8.33.6 (LTS) on the project's database node, 24.8 and 25.8 in CI |

The levels mean what they mean for the other drivers: a **full site database** holds the kernel's tables, a site
package's content and the extensions' tables, installed with `exp:install`, and the site runs from it. A **data table
connection** holds the tables of data table classes (`datatable.ini`) beside a site on another database. An **import
source** is a database rows are read from into the site.

Both are columnar: every column is stored on its own, compressed, and read in large blocks. That makes scans and
aggregates over many rows fast and single-row work comparatively slow. The two differ in what else they are: MonetDB is
a full SQL database with transactions, sequences, primary keys and enforced unique constraints; ClickHouse has none of
those, and a site needs all four.

## Contents

1. [MonetDB](#monetdb)
   1. [Requirements and installing a site](#requirements-and-installing-a-site)
   2. [Settings](#settings)
   3. [Concurrency: one writer at a time](#concurrency-one-writer-at-a-time)
   4. [The SQL](#the-sql)
   5. [What works and what does not](#what-works-and-what-does-not)
2. [ClickHouse](#clickhouse)
   1. [Why not a site database](#why-not-a-site-database)
   2. [How PHP reaches ClickHouse](#how-php-reaches-clickhouse)
   3. [A data table connection](#a-data-table-connection)
   4. [What the driver does](#what-the-driver-does)
   5. [Importing from ClickHouse](#importing-from-clickhouse)
   6. [What works and what does not](#what-works-and-what-does-not-1)
3. [Measured](#measured)
4. [How they are built](#how-they-are-built)
5. [Tests](#tests)

## MonetDB

MonetDB (monetdb.org) is a columnar SQL database server with transactions, sequences, primary and foreign keys and
`CREATE SEQUENCE`; its SQL is standard SQL close to PostgreSQL's. The driver `expMonetDBDB` runs a whole site on it.

### Requirements and installing a site

| What | Where it comes from |
|---|---|
| A MonetDB server: `monetdbd` with a database | packages for Debian, Ubuntu, RHEL and Windows from monetdb.org, or the image `monetdb/monetdb`. Tested: Dec2025-SP5 (11.55.11) and Dec2023-SP4 |
| PHP | nothing beyond PHP itself: the driver speaks MAPI over a TCP or Unix socket (`stream_socket_client`) |

Why plain PHP: MonetDB's own PHP client was written for PHP 5 and is no longer maintained, there is no PECL extension,
and its ODBC driver would add a driver manager and `libMonetODBC` to every host. MAPI is a small, documented protocol
(blocks of at most 8190 bytes, a challenge answered with a salted SHA-512 hash, results as text lines), which
`expMonetDBMapi` implements in about 500 lines.

The database is made on the server, not through SQL. Give the site a user with a schema of its own (otherwise the
tables land in the schema `sys` beside MonetDB's own):

```
monetdb create exponential && monetdb release exponential
mclient -u monetdb -d exponential -s "CREATE USER \"exponential\" WITH PASSWORD 'secret' NAME 'Exponential' SCHEMA \"sys\";
  CREATE SCHEMA \"exponential\" AUTHORIZATION \"exponential\"; ALTER USER \"exponential\" SET SCHEMA \"exponential\""
```

Then install:

```
EXP_INSTALL_DB_PASSWORD=secret php bin/php/install.php --db=monetdb --db-host=127.0.0.1 --db-user=exponential \
    --db-name=exponential --apply
```

With `kickstart.ini` the database is `[database_choice] Type=monetdb`. The web setup wizard does not list MonetDB: it
offers the databases whose PHP extension it finds, and MonetDB needs none. "Remove the existing data" (the default)
drops every table, view and sequence of the user's schema. The installer creates the kernel's tables from
`share/db_schema.dba` through `expMonetDBSchema`, inserts the data, restarts the sequences after the inserted ids and
writes `DatabaseImplementation=monetdb`.

### Settings

```ini
[DatabaseSettings]
DatabaseImplementation=monetdb
Server=127.0.0.1
Port=50000
User=exponential
Password=secret
Database=exponential

[MonetDBSettings]
Schema=
WriteLock=enabled
```

| Setting of `[MonetDBSettings]` | Default | Meaning |
|---|---|---|
| `Schema` | empty | the schema of the site's tables; empty: the user's default schema. Set: `SET SCHEMA` at every connection |
| `ConnectTimeout` | `10` | seconds to connect and log in |
| `ReadTimeout` | `300` | seconds a statement may take |
| `WriteLock` | `enabled` | one writer at a time (below); `disabled` leaves conflicts to MonetDB |
| `LockWait` | empty | seconds a writer waits for the write lock; empty: `LockWaitWeb` in a web request, `LockWaitCommandLine` on the command line |
| `LockWaitWeb` | `10` | |
| `LockWaitCommandLine` | `120` | |
| `ConflictRetries` | `3` | how often a single statement (without the write lock) or `retryTransaction()` is tried again after a conflict |

`Server` may be a Unix socket path (`/tmp/.s.monetdb.50000`). A `monetdbd` that proxies or redirects the connection to
the database's server is followed.

### Concurrency: one writer at a time

MonetDB's transactions are optimistic: a transaction reads a snapshot and writes without locks, and a conflict is found
when it happens. MonetDB tells conflicts apart coarsely, which was measured with two connections:

| Two open transactions | MonetDB |
|---|---|
| change the same row of a table | the second `UPDATE` fails at once: "Update failed due to conflict with another transaction" |
| change **different** rows of the same table | the second `UPDATE` fails at once as well |
| insert different rows into a table with a primary key | both inserts succeed; the second `COMMIT` fails: "transaction is aborted because of concurrency conflicts" |
| one writes, the other reads | no conflict: the reader sees its snapshot |

The kernel writes to the same tables from every request (sessions of the database handler, counters, the content
tables), so two editors saving at once would fail one of the two requests. The driver therefore lets one writer write
at a time, as DuckDB's file lock does (`WriteLock=enabled`, the default):

- A transaction starts by changing the row `write` of the table `monetdb_write_lock` (made in the site's schema at the
  first write). While another open transaction has changed it, MonetDB refuses the change at once; the driver rolls
  back, waits (10 ms, doubling up to 250 ms) and tries again, for `LockWait` seconds. Then the statement fails with the
  error number `55P03` (lock not available).
- A write outside a transaction (an `INSERT`, `UPDATE`, `DELETE` or DDL statement sent on its own) is sent in such a
  transaction of its own, so it waits for a writer in progress instead of failing.
- Reads never wait and never take the lock.
- `lock()` and `unlock()` are the write lock; `LOCK TABLE` and `SELECT ... FOR UPDATE` are not sent.
- `retryTransaction( function ( $db ) { ... } )` runs a transaction and runs it again after a conflict (`40001`) or a
  lock wait that ran out (`55P03`), for code that writes while another program writes to the same database without the
  lock.

| Situation | What happens |
|---|---|
| Visitors reading pages | in parallel; MonetDB answers every reader from its snapshot |
| A request that writes | it waits for a writer in progress, up to `LockWait`, then writes |
| Two editors publishing at once | the second waits for the first transaction to end |
| A long command line job (an import) | every write of a web request waits while the job's transaction is open; keep transactions short or run jobs when the site is quiet |
| Several web servers | possible: the lock is a row in the database, not a file |

`WriteLock=disabled` sends every statement as it is (a conflicting single statement is tried again `ConflictRetries`
times). Use it only where one process writes, e.g. a build of a site that is published read-only afterwards.

### The SQL

`databaseName()` is `monetdb`, and the portable helpers have a `monetdb` dialect: `LIMIT n OFFSET m` (an offset alone
without `LIMIT`), `MERGE INTO` for upserts, `GROUP_CONCAT( ... ORDER BY ...)`, `epoch()` for UNIX timestamps,
`SELECT ... FOR UPDATE` left out. `expMonetDBDialect` changes a statement on its way only where MonetDB differs:

| The kernel writes | MonetDB gets | Why |
|---|---|---|
| `` `name` `` | `"name"` | |
| `LIMIT 20, 10` | `LIMIT 10 OFFSET 20` | |
| `SELECT ... FOR UPDATE` (`FOR SHARE`, `LOCK IN SHARE MODE`) | the `SELECT` without it | no row locks; the write lock instead |
| `LOCK TABLE ...`, `UNLOCK TABLES` | nothing | the write lock instead |
| `day` in a statement on `ezgeneral_digest_user_settings`, `records` on `expaudit_file`, `chain` and `snapshot` on `expai_chain_run`, `details` on `expai_probe_result`, `next` on `sqliimport_scheduled` | `"day"`, `"records"`, `"chain"`, `"snapshot"`, `"details"`, `"next"` | the columns of the kernel and its extensions that are reserved words in MonetDB (`sys.keywords` does not list `details` and `snapshot`); `NEXT VALUE FOR` stays as it is |
| `DROP TABLE` | the table, then the sequences of its serial columns that nothing else reads | in MonetDB a serial column's sequence outlives its table, and the table could not be made again |

String literals and quoted names are never changed. MonetDB reads C escapes in string literals (`'a\nb'` is a line
break), so `escapeString()` doubles a backslash as well as a quote. Values come back as text, as from MySQL and
PostgreSQL: `BOOLEAN` as 1 or 0, `TIMESTAMP` as `Y-m-d H:i:s`. New ids come from the column's sequence
`<table>_<column>_seq` (`DEFAULT NEXT VALUE FOR`); MonetDB reports the id with the insert, and `lastSerialID()` returns
the one the last insert into the table took. Error numbers are the SQLSTATE PostgreSQL gives the same error (`23505`
duplicate key, `42P01` missing table, `42703` missing column, `40001` conflict, `55P03` write lock), so code that tells
a duplicate key from other errors works unchanged.

The schema handler `expMonetDBSchema` writes PostgreSQL's types with these differences: the primary key is part of
`CREATE TABLE`; a unique index is a `UNIQUE` constraint (MonetDB accepts `CREATE UNIQUE INDEX` but **does not enforce
it**, measured); defaults are plain literals (no `::` casts); `BLOB` for binary columns; index names are unique per
schema, so a name another table has already becomes `<table>__<index>`. MonetDB cannot change a column's type in place
(no `ALTER ... TYPE`): the handler copies the column into a new one of the new type, drops the old one and renames the
new one. `kernel/sql/monetdb/kernel_schema.sql` is the kernel schema written by it.

### What works and what does not

Measured on 2026-11-26 with the tests below on MonetDB Dec2025-SP5 (11.55.11; PHP 8.5, Linux x86-64, the project's
database node under a load of 35 to 40 from other work), and a whole site installed with `exp:install --db=monetdb`.

| Area | MonetDB | Notes |
|---|---|---|
| Kernel schema (153 tables) and clean data | works | `kernel/sql/monetdb/kernel_schema.sql` and `cleandata.sql`: 17 to 31 s; through the schema handler from `share/db_schema.dba` and `db_data.dba`: 7 to 16 s |
| Install with `exp:install` / kickstarter, package and extensions | works | [the install](#the-install-on-monetdb): 29 minutes on a busy host |
| Reading pages, the admin | works | front page and admin login 200; with the view cache 0.05 to 0.09 s |
| Web setup wizard | does not | not offered (no PHP extension to detect); use `exp:install` |
| Transactions, nested `begin()`/`commit()`, rollback | works | a failed statement ends MonetDB's transaction; the kernel rolls back as on PostgreSQL |
| Several writers at once | works, one after the other | the write lock; without it MonetDB fails the second of two writers of one table |
| Several readers at once, readers during a write | works | snapshots |
| New ids, ids given on insert, sequences restarted | works | `ALTER SEQUENCE ... RESTART WITH` after the data |
| Unicode text, long text, `NULL` versus the text `NULL` | works | |
| Primary keys and unique constraints | works, enforced | `CREATE UNIQUE INDEX` alone would not be enforced: the handler writes constraints |
| Duplicate keys, missing tables (error numbers) | works | PostgreSQL's SQLSTATE |
| Portable helpers: paging, upsert (`MERGE`), group concat, unix time | works | |
| `SELECT ... FOR UPDATE`, `LOCK TABLE` | works | left out: the write lock instead |
| `INSERT INTO t DEFAULT VALUES` | works | the URL alias id counter uses it |
| Schema upgrades: `ADD COLUMN`, `RENAME`, `DROP COLUMN` | works | |
| Schema upgrades: a column's type changed | works, by copying | fails for a column of a key or an index: drop them around it |
| Data table connection (engine `monetdb`) | works | below |
| DFS cluster backend | does not | no `expDFSFileHandler` backend for MonetDB; files on one server, or NFS |
| Read replica (`UseSlaveServer`) | does not | ignored; MonetDB replicates with its own tools |
| Upgrade captions (the database dump before an upgrade) | does not | dump with `msqldump` yourself and run with `--no-backup` |
| PostgreSQL's full-text column of the audit index | does not | the audit search falls back to `LIKE`, as on SQLite |
| Import source | works through a data table connection | a data table synchronisation reads the rows with persistent classes; SQLI Import's SQL source does not list MonetDB |
| Every statement | works, slowly | 4 ms for a lookup by key, where MySQL and PostgreSQL take a fraction of a millisecond (below). Keep the view cache on |

#### The install on MonetDB

Installed on 2026-11-26 with `exp:install --db=monetdb` (package `sevenx_multisite`, eng-US) on MonetDB Dec2025-SP5 in
a container on the project's database node, under a load of 35 to 40 from other jobs:

| What | Result |
|---|---|
| The install | **INSTALLED**: 16 steps, 291 content objects, checks 9 passed and 0 failed, 3 migrations applied; 29 minutes (1,741 s) |
| Errors in the install log | 398, all from extensions the package names that were not in the test tree (`sevenx_themes_media`, `eztags`, `explayouts` and their tables); none from the driver |
| Front page | 200: 2.6 s with every cache empty (templates compiled), 0.05 to 0.09 s with the view cache filled |
| Admin login page | 200 in 0.3 to 0.5 s |

The time is MonetDB's per-statement cost (above), not the write lock: the package is installed in one long
transaction, which takes the lock once. Pages were served by PHP's built-in server without OPcache.

#### A data table connection

```ini
[Connection_reports]
Engine=monetdb
Server=monetdb.example.org
Port=50000
Database=analytics
User=reports
Secret=monetdb_reports
Options[schema]=reports
```

`expDataConnections::test()` opens it, `expDataTableSchema` makes and upgrades tables on it through `expMonetDBSchema`,
the describer reads its columns from `information_schema.columns`.

## ClickHouse

ClickHouse (clickhouse.com) is a columnar database built for analytics: billions of rows, inserted in large batches,
read with aggregates. The driver `expClickHouseDB` makes it a data table connection and an import source.

### Why not a site database

A site cannot run on ClickHouse. This was decided by measuring, not assumed (`expClickHouseLiveTest`, CL-02 to CL-05):

| The kernel relies on | ClickHouse | Measured |
|---|---|---|
| Unique keys: a second row with the same key fails | the primary key only orders the data; nothing is enforced | two inserts of the same key and the same "unique" name: both rows kept |
| Transactions over statements: publishing writes dozens of rows or none | none (`BEGIN` answers "Transactions are not supported"); each `INSERT` is atomic on its own | `begin()`, insert, `rollback()`: the row stays |
| New ids from the database (`AUTO_INCREMENT`, sequences) | none; `generateSerialID()` needs ClickHouse Keeper | the driver takes the maximum plus one itself |
| Cheap single-row `UPDATE` and `DELETE` | rows are changed by rewriting parts of a table: a lightweight `UPDATE` or `DELETE` writes a patch or a mask | 16 to 122 ms per changed row, against 4 to 16 ms on MonetDB |
| Cheap single-row reads | a read by key scans a granule (8,192 rows) | 30 to 48 ms per row read by key |

The kernel's schema and its data do load through the schema handler (153 MergeTree tables in 4.6 to 8 s), so the
handler is not what stops a site: a page writes and reads hundreds of single rows, and a lost unique key or a half
written publish would go unnoticed.

### How PHP reaches ClickHouse

ClickHouse speaks four protocols. The choice was measured against the server:

| Way | Result |
|---|---|
| **HTTP interface (port 8123, 8443 with TLS)** | **chosen**: ClickHouse's own protocol for every client but its C++ one; settings per statement (`mutations_sync`, `max_execution_time`), error numbers in a header, typed results; needs curl or nothing (PHP's HTTP stream) |
| MySQL wire protocol (port 9004), through `mysqli` | works for queries; no transactions, no `LAST_INSERT_ID()`, no settings per statement; would be an `eZMySQLiDB` that is not MySQL |
| PostgreSQL wire protocol (port 9005), through `pgsql` | connects and answers plain queries; bound parameters fail (`pg_query_params()`: "Unknown expression identifier") |
| Native TCP protocol (port 9000) | no maintained PHP extension (SeasClick is unmaintained); a client in PHP would reimplement ClickHouse's column blocks |

`expClickHouseHttp` sends every statement as the body of a POST, the user and password in the headers
`X-ClickHouse-User` and `X-ClickHouse-Key` (never in the address), the database in `X-ClickHouse-Database`, and reads
results in `TabSeparatedWithNamesAndTypes` (`default_format`), so a statement is sent as it is.

### A data table connection

```ini
[Connection_events]
Engine=clickhouse
Server=clickhouse.example.org
Port=8443
Database=analytics
User=exponential
Secret=clickhouse_password
Options[secure]=enabled
```

| Option | Meaning |
|---|---|
| `secure` | `enabled`: HTTPS (port 8443 unless one is given) |
| `verify_tls` | `disabled`: accept any certificate (a test server) |

The defaults are `[ClickHouseSettings]` of `site.ini` (`Secure`, `VerifyTLS`, `ConnectTimeout`, `Timeout`,
`MutationsSync`, `Settings[]` sent with every statement). `expDataTableSchema` makes a class's table through
`expClickHouseSchema`:

- `ENGINE = MergeTree ORDER BY (<primary key>)` (`tuple()` without one), with the block number columns that a
  lightweight `UPDATE` needs.
- A column without `NOT NULL` is `Nullable(...)`; types: `Int32`, `Int64`, `Int16`, `String` (varchar, char and text
  alike), `Float32`, `Float64`, `Decimal(p, s)`, `DateTime`, `Date`.
- A non-unique index is a data-skipping index (`minmax`); a unique index is **left out** with a note in the debug
  output, as ClickHouse would not enforce it.
- A serial column is `Int32` with the comment `auto_increment`; its values come from `nextSerialID()`.

Write data table rows in chunks (`expDataTableWriter`, `chunk`): one `INSERT` of a thousand rows costs about what one
`INSERT` of one row does.

### What the driver does

| The code does | ClickHouse gets |
|---|---|
| `begin()`, `commit()`, `rollback()` | nothing; a rollback after a write logs a warning (nothing can be undone) |
| `UPDATE t SET ... WHERE ...` | a lightweight `UPDATE` (ClickHouse 25.7 and later, a table with block numbers); refused, the mutation `ALTER TABLE t UPDATE ... WHERE ...`, waited for (`MutationsSync`) |
| `DELETE FROM t WHERE ...` | a lightweight `DELETE` |
| `DELETE FROM t` | `TRUNCATE TABLE t` |
| `SELECT ... FOR UPDATE`, `LOCK TABLE` | the `SELECT` without it; nothing |
| an insert into a class with an auto increment key | `nextSerialID()`: the column's maximum plus one, never twice in one connection (one writer of new rows at a time) |
| `sqlUpsert()` | refused (`expDBUnsupportedException`): with no enforced key there is nothing to match on |

Escaping doubles a backslash and writes a quote as `\'`. `databaseName()` is `clickhouse`; error numbers are
PostgreSQL's SQLSTATE where there is one (`42P01` unknown table, `42703` unknown column, `42601` syntax, `28000`
authentication), else `CH` and ClickHouse's own number.

### Importing from ClickHouse

Two ways, both read only:

- **SQLI Import's ClickHouse source** (`expImportClickHouseSource` in the `sqliimport` extension) reads the rows of a
  SELECT through the same HTTP interface with `readonly=2`, page by page, and turns them into content. It refuses
  every statement but `SELECT`/`WITH` and the table functions that reach outside the server. It is part of
  `sqliimport`, not of this driver, and was not changed in this release.
- **A data table connection** (engine `clickhouse`) and a data table class on it: a data table synchronisation
  (`table_to_content`) reads its rows with the class and writes content. The class's reads and writes were tested on
  ClickHouse (CL-07); the synchronisation itself was not run there.

### What works and what does not

| Area | ClickHouse | Notes |
|---|---|---|
| A site database | **does not** | [why](#why-not-a-site-database); `exp:install` does not offer it |
| Data table connection (engine `clickhouse`): test, tables made and planned again, rows written and read, the describer | works | CL-07 |
| Values: every integer and float type, `Decimal`, text with tabs, line breaks, backslashes and Unicode, `NULL`, `Bool`, `Date`, `DateTime`, `DateTime64` | works | CL-01 |
| The kernel schema and data through the schema handler | works | as a proof of the handler, not for a site |
| Unique keys, transactions | do not | measured, CL-03 |
| `UPDATE`, `DELETE` | work, slowly | lightweight where the table has block numbers, else a mutation |
| Auto increment ids | work through the driver | the maximum plus one; one writer of new rows at a time |
| Portable helpers: paging, group concat with an order, unix time | work | upsert refused |
| Import into content | works | SQLI Import's ClickHouse source; a data table synchronisation |
| TLS (HTTPS), a password never in the address | works | |

## Measured

On the project's database node (12 CPUs, a load of 35 to 40 from other work during the runs), PHP 8.5, through the
drivers (from the live tests and a timing run of 50 to 200 statements each):

| What | MonetDB Dec2025-SP5 | ClickHouse 25.8 |
|---|---|---|
| connect (and log in) | 25 ms | 10 ms (first request) |
| a row inserted outside a transaction | 22 ms (write lock and commit); 6.5 ms with `WriteLock=disabled` | 9 to 18 ms |
| a row inserted inside one transaction | 3.9 ms | (no transactions) |
| a row read by its primary key | 3.7 to 4.2 ms | 30 to 48 ms |
| a row updated | 15 ms (outside a transaction) | 16 to 122 ms (lightweight), 26 to 55 ms (mutation) |
| a row deleted | | 17 to 76 ms (lightweight) |
| 1,000,000 rows inserted with `INSERT ... SELECT` | | 214 ms |
| `count()` and `sum()` over 1,000,050 rows | | 90 ms |
| `begin()` and `commit()` with the write lock | 16 ms | |

MonetDB compiles every statement it is sent, and the kernel sends its values inside the statement text, so even a
lookup by key costs milliseconds; a page with hundreds of queries takes a second or more without the view cache.
ClickHouse is built the other way round: a million rows cost little more than one.

## How they are built

| File | What |
|---|---|
| `lib/ezdb/classes/expmonetdbmapi.php` | `expMonetDBMapi`: MAPI in plain PHP; login (challenge, salted hash, proxy and redirect), blocks, results parsed and unescaped |
| `lib/ezdb/classes/expmonetdbdb.php` | `expMonetDBDB`: the eZDB driver; the write lock, transactions, ids, the catalogue, sequences |
| `lib/ezdb/classes/expmonetdbdialect.php` | `expMonetDBDialect`: what is changed in a statement, what counts as a write, escaping, error numbers |
| `lib/ezdbschema/classes/expmonetdbschema.php` | `expMonetDBSchema`: DDL from `.dba`, a schema read back into `.dba` |
| `kernel/sql/monetdb/kernel_schema.sql` | the kernel schema, written by `expMonetDBSchema` from `share/db_schema.dba` |
| `lib/ezdb/classes/expclickhousehttp.php` | `expClickHouseHttp`: the HTTP interface; requests, `TabSeparatedWithNamesAndTypes` read back, errors |
| `lib/ezdb/classes/expclickhousedb.php` | `expClickHouseDB`: the eZDB driver; statements, mutations, ids, the catalogue |
| `lib/ezdb/classes/expclickhousedialect.php` | `expClickHouseDialect`: what is changed in a statement, escaping, error numbers |
| `lib/ezdbschema/classes/expclickhouseschema.php` | `expClickHouseSchema`: MergeTree tables from `.dba`, a database read back |

## Tests

`tests/tests/lib/ezdb/columnar/` (DB-less, testsuite `columnar`) and `.../columnar/live/` (testsuite `columnar-live`;
each test class skips without its server: `EXP_TEST_MONETDB_HOST`, `EXP_TEST_CLICKHOUSE_HOST`, and their `_PORT`,
`_USER`, `_PASSWORD`, `_DATABASE`). The GitHub job `columnar` runs both against MonetDB Dec2025-SP5 with ClickHouse 25.8
(PHP 8.5) and MonetDB Dec2023-SP4 with ClickHouse 24.8 (PHP 8.1), and on the first pair installs a whole site on
MonetDB and fetches its front page and the admin login.

| File | What |
|---|---|
| `expMonetDBMapiTest` | the login answer, results, rows with every escape, errors, no server |
| `expMonetDBDialectTest` | the rewrites, literals left alone, writes, escaping, error numbers |
| `expMonetDBDBTest` | the driver on a stand-in for MAPI: the schema, the write lock around writes and transactions, the lock wait, retries, values, ids, a lost connection |
| `expMonetDBSchemaTest` | DDL of a table and of the kernel schema (equal to `kernel/sql/monetdb/kernel_schema.sql`), types and defaults read back |
| `expClickHouseHttpTest`, `expClickHouseDialectTest`, `expClickHouseDBTest`, `expClickHouseSchemaTest` | the same for ClickHouse: requests and headers, results, errors, rewrites, the mutation fallback, transactions that send nothing, ids, MergeTree DDL |
| `live/expMonetDBLiveTest` | against MonetDB: values, 5,000 rows, the kernel schema and clean data, the schema handler's round trip, transactions, ids, errors, helpers, conflicts without the write lock, the write lock with a second process, reserved columns, schema upgrades, a data table connection |
| `live/expClickHouseLiveTest` | against ClickHouse: values, 5,000 rows, errors, the kernel schema through the handler, what ClickHouse does not do, changing rows with timings, ids, helpers, a data table connection |
