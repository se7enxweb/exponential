# DuckDB driver

This page is for administrators who want to run a site on DuckDB, and for developers who work on the driver. From
Exponential 6.0.15 the kernel has a DuckDB driver, `expDuckDBDB` in `lib/ezdb/classes/`: a whole site (the kernel's
tables, a package's content, the extensions' tables) in one DuckDB file, with no database server.

**What it is for.** DuckDB is an embedded, columnar SQL database: one file, read and written by the PHP process itself,
fast at scans and aggregates. Its rule for that file is the one that decides where it fits: **one process writes at a
time**. A process that has the file open to write has it to itself; any number of processes may read it together, but
never while one writes. So DuckDB suits a site with one editor or a few, a site that is mostly read, a build or demo
site, a site that is made once and then served, and analytics on a site's data. It is not a database for a busy site
with many editors writing at once: their requests queue one behind the other (see
[Concurrency](#concurrency-one-writer-at-a-time)). There is no DFS cluster on DuckDB; a cluster of web servers needs a
database server.

## Contents

1. [Requirements](#requirements)
2. [Installing a site on DuckDB](#installing-a-site-on-duckdb)
3. [Settings](#settings)
4. [Concurrency: one writer at a time](#concurrency-one-writer-at-a-time)
5. [The SQL](#the-sql)
6. [What works and what does not](#what-works-and-what-does-not)
7. [A data table connection to a DuckDB file](#a-data-table-connection-to-a-duckdb-file)
8. [How it is built](#how-it-is-built)
9. [Tests](#tests)

## Requirements

| What | Where it comes from |
|---|---|
| DuckDB's C library, `libduckdb.so` (`libduckdb.dylib` on macOS, `duckdb.dll` on Windows) | DuckDB's releases on GitHub: `duckdb-shared-libs-linux-amd64.tar.gz` (2.0), `libduckdb-linux-amd64.zip` (1.x). Tested: 1.5.6 and 2.0.1 |
| PHP's FFI extension, `ffi` | bundled with PHP (`--with-ffi`); RHEL family and Remi `php-ffi`, Debian and Ubuntu `php<version>-ffi` |
| `ffi.enable=true` in the `php.ini` of PHP-FPM (or Velocity) | PHP's default `ffi.enable=preload` allows FFI on the command line only; a web request then fails with a message that says so |

PHP reaches DuckDB through FFI because it is the most robust of the three ways there are. There is no maintained PHP
extension for DuckDB that a host can install from a package; the `duckdb` command line program would be one process
per statement (or a pipe whose text output has to be parsed back into values). FFI loads DuckDB's own C library into
the PHP process, calls its stable C API, and reads the values in their own types. The driver uses only the part of the
C API DuckDB calls stable (`duckdb_open_ext`, `duckdb_query`, `duckdb_fetch_chunk` and the vectors of a chunk), not the
per-value functions DuckDB deprecated, so the same code runs on DuckDB 1.x and 2.0.

Never put the library or the extension into a shared PHP that other sites use without checking it: FFI lets PHP code
load any C library, which is why PHP switches it off outside the command line by default. Switch it on in the pool of
the site that needs it (`php_admin_value[ffi.enable]=true` in its PHP-FPM pool).

## Installing a site on DuckDB

```
php bin/php/install.php --db=duckdb --apply
php bin/php/install.php --db=duckdb --db-name=/srv/sites/example/data/site.duckdb --apply
```

The default file is `var/storage/duckdb/exponential.duckdb`; a name without a path lives in `var/storage/duckdb/`
(`.duckdb` is added to a name without an extension), a path starting with `/` is used as it is. `var/storage` is sent
to `index.php` by the shipped `.htaccess` and left out of Velocity's static files, so the file is never served. With
`kickstart.ini` the database is `[database_choice] Type=duckdb` and `[database_init] Database=<file>`; server, port,
user and password are ignored. The library is found as `[DuckDBSettings] Library` says, else from the environment's
`DUCKDB_LIBRARY`, else by the name `libduckdb.so` in the system's library path.

The web setup wizard lists DuckDB when PHP's `ffi` extension may be used in the web process (`ffi.enable=true`;
`[database_extensions]` counts `ffi`) and `libduckdb` is found. Its database page asks only for a file name (default
`exponential.duckdb` in `var/storage/duckdb`), with no server, user or password, as for SQLite. With
`ffi.enable=preload`, PHP's default, FFI works on the command line only, so the web wizard does not offer DuckDB: use
`exp:install` or the kickstarter.

The installer creates the kernel's tables from `share/db_schema.dba` through the schema handler `expDuckDBSchema`,
inserts the clean data, moves the sequences on past the inserted ids, installs the package and writes
`DatabaseImplementation=duckdb` into the settings. "Remove the existing data" (`--db-action=remove`, the default)
empties the file first: every view, index, table and sequence in it is dropped.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=duckdb
Database=site.duckdb

[DuckDBSettings]
Library=/usr/local/lib/libduckdb.so
AccessMode=auto
```

`ImplementationAlias[duckdb]` names `expDuckDBDB`. `[DuckDBSettings]` of `site.ini`:

| Setting | Default | Meaning |
|---|---|---|
| `Library` | empty | the C library, an absolute path or a name the loader finds; empty: `DUCKDB_LIBRARY` of the environment, else `libduckdb.so` (`libduckdb.dylib`, `duckdb.dll`) |
| `AccessMode` | `auto` | `auto`: open the file read-only, and to write only for the first write or transaction of the request; `read_write`: always to write; `read_only`: never write (a published copy of a site) |
| `LockWait` | empty | seconds an open waits while another process holds the file; empty: `LockWaitWeb` in a web request, `LockWaitCommandLine` on the command line |
| `LockWaitWeb` | `10` | |
| `LockWaitCommandLine` | `120` | |
| `Threads` | `0` | DuckDB's threads per connection; 0: DuckDB's own choice (the CPUs). On a host with many PHP workers, 1 or 2 |
| `MemoryLimit` | empty | DuckDB's memory limit, e.g. `512MB`; empty: DuckDB's default (80% of the memory) |
| `TempDirectory` | empty | where DuckDB spills a large sort or join; empty: next to the database file |
| `IntegerDivision` | `enabled` | `7/2` is 3, as in PostgreSQL and MySQL; DuckDB's own `/` divides to a DOUBLE |
| `Config[<name>]` | | more DuckDB settings for the open (`Config[preserve_insertion_order]=false`) |

In code: `new expDuckDBDB( array( 'database' => '/path/site.duckdb', 'duckdb' => array( 'AccessMode' => 'read_only' ) ) )`.

## Concurrency: one writer at a time

DuckDB locks the file with the operating system's file lock: a process that opens it to write holds an exclusive
lock, a process that opens it to read a shared one. The driver makes that work for a web site like this:

- **Read first.** With `AccessMode=auto` a request opens the file read-only. Any number of requests read at the same
  time.
- **Write when needed.** The first statement that writes (`INSERT`, `UPDATE`, `DELETE`, DDL, `CREATE TEMPORARY TABLE`)
  or the first `begin()` closes the file and opens it again to write. The connection's `SET` statements are replayed.
  A statement the driver did not recognise as a write and that DuckDB refuses as read-only is sent again after the
  reopen.
- **Wait, then give up.** While another process holds the file to write, the open is tried again (10 ms, doubling up to
  250 ms) for `LockWait` seconds; then it fails with the error number `55P03` (lock not available) and the request
  gets the database error page.
- **Let go at the end.** Every web request closes the file when it ends (a shutdown function), so the next request,
  in another PHP-FPM worker, can have it. A transaction still open is rolled back.

What that means in practice:

| Situation | What happens |
|---|---|
| Visitors reading pages | they read in parallel |
| A request that writes (a login, a form, the session, a counter) | it waits until the readers of the moment have closed the file, then has it alone for the rest of its request; readers arriving meanwhile wait for it |
| Two editors publishing at once | the second waits for the first request to end (up to `LockWait`) |
| A long command line job (an import, a cron job) | it holds the file to write while it runs: web requests that write wait, and fail after `LockWait`. Run such jobs when the site is quiet, or give `LockWaitWeb` more time |
| Velocity's persistent workers | the same rules: each request opens and closes the file |
| Several web servers | not possible: DuckDB's file lock is local to one machine, and a network file system does not carry it reliably |

Sessions written to the database would make most requests writers. The default session handler (`[Session] Handler=`
empty: PHP's own, `ezpSessionHandlerPHP`) keeps them out of it; keep it so on DuckDB, so that pages stay reads.

## The SQL

DuckDB's SQL is PostgreSQL's in almost everything the kernel writes: sequences and `nextval()`, `||`,
`SUBSTRING( ... FROM ... FOR ... )`, `ON CONFLICT`, `STRING_AGG`, `EXTRACT( EPOCH ... )`, `information_schema`,
`UPDATE ... FROM`. `databaseName()` is `duckdb`, and the portable helpers (`sqlLimit()`, `sqlUpsert()`,
`sqlGroupConcat()`, `sqlUnixTimestamp()`, `sqlFromUnixTimestamp()`, `sqlForUpdate()`, `sqlQuoteIdentifier()`) have
a `duckdb` dialect. `expDuckDBDialect` changes a statement on its way only where DuckDB differs:

| The kernel writes | DuckDB gets | Why |
|---|---|---|
| `SELECT ... FOR UPDATE` (also `FOR SHARE`, `LOCK IN SHARE MODE`) | the `SELECT` without it | DuckDB has no row locks: one process writes, and inside it transactions are checked optimistically |
| `LOCK TABLE ...` | nothing | nothing to lock; `lock()` and `unlock()` are a transaction |
| `INSERT IGNORE INTO` | `INSERT OR IGNORE INTO` | MySQL's words |
| `REPLACE INTO` | `INSERT OR REPLACE INTO` | |
| `` `name` `` | `"name"` | |
| `LIMIT 20, 10` | `LIMIT 10 OFFSET 20` | |
| `7 / 2` | 3 | `SET integer_division = true` at every open (`IntegerDivision`) |

String literals and quoted names are never changed. Escaping doubles quotes and drops NUL bytes; a backslash is an
ordinary character. New ids come from the column's sequence `<table>_<column>_seq` (`currval()`), as on PostgreSQL;
error numbers are the SQLSTATE PostgreSQL gives the same error (`23505` duplicate key, `42P01` missing table, `42703`
missing column, `42601` syntax, `55P03` file locked), so code that tells a duplicate key from other errors works
unchanged. Values come back as text, as from MySQL and PostgreSQL (`BOOLEAN` as 1 or 0, `TIMESTAMP` as
`Y-m-d H:i:s`).

The schema handler `expDuckDBSchema` writes PostgreSQL's DDL with three differences: the primary key is part of
`CREATE TABLE` (DuckDB 1.x refuses to add one to a table that has an index, 2.0 has no `ALTER TABLE ONLY`), sequences
have no `CACHE`, indexes no `USING btree`. Index names are unique per database, as in PostgreSQL: an index name another
table has already becomes `<table>__<index>`. `kernel/sql/duckdb/kernel_schema.sql` is the kernel schema written by it.

## What works and what does not

Measured on 2026-11-26 with the tests below on DuckDB 1.5.6 and 2.0.1 (PHP 8.5 with FFI, Linux x86-64), and a whole
site installed with `exp:install --db=duckdb` on DuckDB 2.0.1.

| Area | DuckDB | Notes |
|---|---|---|
| Kernel schema (153 tables) and clean data | works | `kernel/sql/duckdb/kernel_schema.sql`, `share/db_schema.dba` through the schema handler |
| Install with `exp:install` / kickstarter, package and extensions | works | the multisite package with its content: 16 steps, 0 errors, 0 warnings, 9 checks passed; 31 minutes on a busy shared host (about 118 MB). The installer lets the file go before it starts a PHP process of its own (cluster, help manuals, migrations), which would otherwise wait for it |
| Web setup wizard | works | offered with `ffi` and `ffi.enable=true` in the web PHP; with `ffi.enable=preload` not offered, use `exp:install` |
| Reading pages, the admin, editing and publishing | works, slowly | front page, admin login and content views answer 200. **Every statement costs milliseconds** (measured below), where SQLite and MySQL take tens of microseconds for a lookup by key: a page with hundreds of queries takes seconds without the view cache |
| Transactions, nested `begin()`/`commit()`, rollback | works | a failed statement ends DuckDB's transaction; the kernel rolls back as on PostgreSQL |
| New ids (`lastSerialID()`), ids given on insert, sequences moved on | works | `setval()` on 2.0, `nextval()` over `range()` on 1.x |
| Unicode text, long text, BLOB | works | text must be valid UTF-8 (DuckDB refuses anything else) |
| Duplicate keys, missing tables (error numbers) | works | PostgreSQL's SQLSTATE |
| Portable helpers: paging, upsert, group concat, unix time | works | |
| `SELECT ... FOR UPDATE`, `LOCK TABLE` | works | left out: nothing to lock |
| Several readers at once | works | `AccessMode=auto` |
| Several writers at once | **does not** | they queue; a writer waits up to `LockWait`, then fails with `55P03` |
| Several web servers, DFS cluster | **does not** | no DFS backend: the file lock is one machine's |
| Read replica (`UseSlaveServer`) | does not | ignored, as for SQLite |
| Migrations after the install (`exp:migration:migrate`) | works | 49 migrations: 4 applied, 40 not needed, 0 failed. Five that need to read a table's indexes are skipped ("cannot tell"), as the migration checks have no catalog query for DuckDB yet. The installer runs them before the help manuals, whose build is a background worker that holds the file for 20 to 30 minutes |
| Upgrade captions (the database dump before an upgrade or a migration run) | **does not** | no built-in dump for DuckDB: copy the file (or `EXPORT DATABASE`) yourself and run with `--no-backup` |
| Schema upgrades: `ALTER TABLE ... ADD COLUMN` | works | |
| Schema upgrades: changing a column's type, renaming a table, dropping a column, on a table with an index | **does not** | DuckDB refuses (`Cannot alter entry ... because there are entries that depend on it`): drop the table's indexes, alter, create them again. The shipped `update/database/` files are written for MySQL, PostgreSQL and SQLite |
| PostgreSQL's full-text column of the audit index (`tsvector`) | does not | the audit search falls back to `LIKE`, as on SQLite |
| Types the kernel does not use (LIST, STRUCT, MAP, UNION, INTERVAL) | does not | read back only with a `CAST( ... AS VARCHAR )` in the query |
| Data table connection (engine `duckdb`) | works | below |
| Import source | works | the `sqliimport` extension's `expImportDuckDbSource` reads DuckDB files |
| Backup | works | copy the file while no process writes, or `EXPORT DATABASE` from the `duckdb` program |


### Measured

On the database node of the project's cluster (12 CPUs, load 20 to 40 from other work during the run), DuckDB 2.0.1,
PHP 8.5 with FFI, 2 DuckDB threads, through `expDuckDBDB`:

| What | Time |
|---|---|
| a row inserted outside a transaction (a commit each) | 7.4 ms |
| a row inserted inside one transaction | 2.7 ms |
| a row read by its primary key | 2.7 ms |
| a row updated outside a transaction | 7.9 ms |
| open, first statement, close (a small file) | 42 ms |
| open read-only of the installed site (118 MB) | 122 ms |
| front page of the installed site, view cache filled (PHP's built-in server, no OPcache) | 2.3 to 3.5 s |
| the same page with an empty cache | 17 s |

DuckDB is built for scans over many rows, not for many small statements; the kernel sends hundreds per page. Keep the
view cache on, and expect DuckDB to suit sites where that is acceptable.

## A data table connection to a DuckDB file

The data table layer has the engine `duckdb` (`datatable.ini [ConnectionSettings] Engines[]`): a DuckDB file in one of
the `SQLiteDirectories[]`, ending in `.duckdb`, `.ddb` or `.db`:

```ini
[Connection_analytics]
Engine=duckdb
Database=var/storage/datatables/analytics.duckdb
```

It is available where PHP can use FFI. `expDataConnections::test()` opens it, `expDataTableSchema` makes and upgrades
tables on it through `expDuckDBSchema`, and the describer reads its columns from `information_schema.columns`. A site on
another database can keep its analytics in DuckDB this way.

## How it is built

| File | What |
|---|---|
| `lib/ezdb/classes/expduckdbffi.php` | `expDuckDBFFI`: the C declarations of the stable C API, open and close with a configuration, statements, the result read chunk by chunk and vector by vector, values as text |
| `lib/ezdb/classes/expduckdbdb.php` | `expDuckDBDB`: the eZDB driver; access modes, the lock wait, transactions, ids, the catalogue, sequences |
| `lib/ezdb/classes/expduckdbdialect.php` | `expDuckDBDialect`: what is changed in a statement, what counts as a write, escaping, error numbers |
| `lib/ezdbschema/classes/expduckdbschema.php` | `expDuckDBSchema`: DDL from `.dba`, a database read back into `.dba` |
| `kernel/sql/duckdb/kernel_schema.sql` | the kernel schema, written by `expDuckDBSchema` from `share/db_schema.dba` |

## Tests

`tests/tests/lib/ezdb/duckdb/` (DB-less, testsuite `duckdb`) and `.../duckdb/live/` (testsuite `duckdb-live`; skipped
without FFI or without `EXP_TEST_DUCKDB_LIBRARY`, the path of `libduckdb.so`). The GitHub job `duckdb` runs both on
DuckDB 2.0.1 (PHP 8.5) and 1.5.6 (PHP 8.3), and on 2.0.1 installs a whole site with `exp:install --db=duckdb` and
fetches its front page and the admin login.

| File | What |
|---|---|
| `expDuckDBDialectTest` | statement classification, the rewrites, literals left alone, escaping, error numbers, the helpers |
| `expDuckDBDBTest` | the driver on a stand-in for the library: file names, settings, read-only first, the reopen to write, lock wait, replayed settings, paging, errors, ids, catalogue, sequences |
| `expDuckDBSchemaTest` | DDL of a table and of the kernel schema (equal to `kernel/sql/duckdb/kernel_schema.sql`), types and defaults read back, value conversions of the binding |
| `live/expDuckDBLiveTest` | against DuckDB: every type read back, 5,000 rows over several chunks, the kernel schema and clean data, the schema handler's round trip of `share/db_schema.dba`, transactions, ids, errors, the helpers, two processes reading at once, a second process waiting for the lock and failing after `LockWait` |

Regenerate the schema file after a change to `share/db_schema.dba`: the test `expDuckDBSchemaTest::testKernelSchema`
fails until `kernel/sql/duckdb/kernel_schema.sql` equals what `expDuckDBSchema::generateSchemaFile()` writes for it.
