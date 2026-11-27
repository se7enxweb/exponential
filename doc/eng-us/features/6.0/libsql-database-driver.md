# libSQL and Turso driver

This page is for administrators who want to keep a site's database on a libSQL server or in Turso's cloud, and for
developers who work on the driver. From Exponential 6.0.15 the kernel reaches a libSQL server over its HTTP API:
`expLibSQLDB` (`DatabaseImplementation=libsql`) for a libSQL server of your own (`sqld`, the `libsql-server`
container) and `expTursoDB` (`turso`) for Turso. No library and no PHP extension of its own: PHP's curl extension, or
`allow_url_fopen` without it.

**What it is.** libSQL is a fork of SQLite: the same SQL, the same file format, the same answers, with a server
(`sqld`) that serves a database over HTTP. The driver is therefore the SQLite driver (`eZSQLite3DB`) with another
transport: the kernel's SQLite schema, the SQLite schema handler, the SQL helpers and every place in the kernel that
treats SQLite in its own way treat a libSQL server the same, and `databaseName()` stays `sqlite`. What changes is where
the database is: on a server, which several web servers can share, instead of a file next to one PHP.

**What it is for.** A site that wants SQLite's simplicity without its one-machine limit (several web servers on one
database), a site whose database runs in Turso's cloud, and a data table connection to a libSQL database. Every
statement is an HTTP request: a read costs about 2 ms instead of SQLite's tens of microseconds, and a write outside a
transaction costs what the server takes to make it durable (measured below). Keep the view cache on.

## Contents

1. [Requirements](#requirements)
2. [Installing a site on a libSQL server](#installing-a-site-on-a-libsql-server)
3. [Settings](#settings)
4. [How a statement reaches the server](#how-a-statement-reaches-the-server)
5. [What differs from SQLite](#what-differs-from-sqlite)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Embedded file mode and embedded replicas](#embedded-file-mode-and-embedded-replicas)
8. [A data table connection to a libSQL database](#a-data-table-connection-to-a-libsql-database)
9. [How it is built](#how-it-is-built)
10. [Tests](#tests)

## Requirements

| What | Where it comes from |
|---|---|
| A libSQL server: `sqld` 0.22 or later (Hrana 3; older servers are spoken to in Hrana 2), or a Turso database | the container `ghcr.io/tursodatabase/libsql-server` (tested: sqld 0.24.33, SQLite 3.47.0); Turso's dashboard or `turso db create` |
| PHP's curl extension (recommended: keep-alive), or `allow_url_fopen=On` | bundled with PHP on every distribution; the stream wrapper is the fallback |
| The SQLite the server is built on: 3.35 or later | the installer checks `sqlite_version()` |

PHP's `sqlite3` extension is **not** needed on the web server. The Packagist clients were looked at and not taken: the
PHP clients of libSQL either load Turso's native library through FFI or a PHP extension written in Rust (neither is
installable from a host's packages), or are HTTP clients that wrap the same three requests in a framework of their
own. The protocol is small (one JSON request per statement), so the driver speaks it itself.

## Installing a site on a libSQL server

Start a server, for example in a container (the data in a volume):

```bash
docker run -d --name sqld -p 127.0.0.1:8080:8080 -v sqld-data:/var/lib/sqld ghcr.io/tursodatabase/libsql-server:latest
```

Then:

```bash
php bin/php/install.php --db=libsql --db-host=127.0.0.1 --db-port=8080 --apply
```

The server holds one database (without namespaces), so `--db-name` is only the name the site's settings keep. With an
auth token (`sqld --auth-jwt-key-file`, or Turso), give the token as the password: `EXP_INSTALL_DB_PASSWORD=<token>`
or `--db-password`. Turso:

```bash
EXP_INSTALL_DB_PASSWORD="$(turso db tokens create exponential)" \
  php bin/php/install.php --db=turso --db-url=libsql://exponential-acme.turso.io --apply
```

`--db-url` replaces `--db-host` and `--db-port` (`http://host:8080`, `https://host`, `libsql://<database>-<org>.turso.io`),
`--db-tls=enabled|disabled|auto` chooses https (auto: Turso, or port 443). The kickstarter takes `Type=libsql` (also
`sqld`) or `Type=turso`, with `URL` and `TLS` in `[database_init]`. The web setup wizard does not list libSQL: there is
no PHP extension of its own to detect; use `exp:install`.

The installer empties the server's database first (`DatabaseAction=remove`), as it empties a SQLite file: every table,
view, index and trigger is dropped.

## Settings

`settings/site.ini` (the installer writes `[LibSQLSettings]` into the siteaccesses and `settings/override`):

```ini
[DatabaseSettings]
DatabaseImplementation=libsql     # turso for Turso; sqld is accepted too
Server=10.0.0.5
Port=8080
Database=exponential
Password=                         # the auth token, when the server wants one

[LibSQLSettings]
URL=                              # http(s)://host:port or libsql://host, replaces Server and Port
TLS=auto                          # enabled, disabled, auto (Turso, or port 443)
AuthToken=                        # instead of Password; else LIBSQL_AUTH_TOKEN (TURSO_AUTH_TOKEN) from the environment
Timeout=30                        # seconds per request (at least the statements' lock wait + 5)
ConnectAttempts=3                 # tries of the first connection
ConnectWait=200                   # ms before the second try, doubled after each failure
KeepStream=enabled                # one server stream for all statements (below)
```

`[DatabaseSettings] SQLiteBusyTimeout`, `SQLiteTransactionWait` and their command line pairs keep their meaning: how
long a statement and a transaction's start may wait. `SQLitePragmas[]` is not sent: the server's settings are its own.
`UseSlaveServer` is ignored (Turso's replicas answer at the same URL).

## How a statement reaches the server

Each statement is one `POST /v3/pipeline` (Hrana 3 over HTTP; `/v2/pipeline` on a server without it) with the
statement and a `get_autocommit` request, on a **stream** of the server, which is the server's connection to the
database. The stream stays open from statement to statement (`KeepStream=enabled`): a new stream is a new connection
of the server to its database, and that cost 18 of a read's 20 ms and 70 of a write's 76 ms (sqld 0.24 on loopback)
before the stream was kept. A transaction runs on one stream from `BEGIN IMMEDIATE` to its end, as on one SQLite
connection. `get_autocommit` tells the driver after every statement whether the server still has the transaction open,
so a transaction the server ended (an error, its transaction timeout) is known at once.

The server lets an idle stream go after 10 seconds. A statement outside a transaction that finds its stream gone (HTTP
400 "invalid baton"), or whose request did not reach the server, is sent once more on a new stream; inside a
transaction the transaction is lost, and the statement fails with that reason. `close()`, at the end of every request,
closes the stream.

## What differs from SQLite

| | SQLite (a file) | libSQL (a server) |
|---|---|---|
| Who waits for the write lock | the writer gate (a lock file next to the database) and SQLite's busy handler | the server; no lock file |
| A long write transaction while another writer waits | the other waits (up to `SQLiteTransactionWait`) | **sqld ends the first after its transaction timeout (about 5 s)**: its next statement fails with "the libSQL server ended the transaction", and the kernel rolls it back |
| `md5()`, `BIT_OR()`, `BIT_AND()`, `MOD()` | functions registered in PHP | `md5()` of a literal is worked out in PHP, of a column it is the server's `md5()` as hex; `BIT_OR()` and `BIT_AND()` are written as `MAX()`/`MIN()` of each bit; `MOD()` is the server's own (math functions built in) |
| Temporary tables | `CREATE TEMPORARY TABLE` | refused by the server: `createTempTable()` makes an ordinary table of the caller's unique name, dropped by `dropTempTable()` or at the end of the connection |
| PRAGMAs (WAL, cache, mmap) | set per connection | the server's own; none sent |
| Several web servers | no (one machine's file) | yes: one database for all |
| Backup | `VACUUM INTO`, a copy of the file | the server's: a copy of sqld's data directory, Turso's point-in-time restore |

## What works and what does not

Measured on 2026-11-27 with the tests below against sqld 0.24.33 (SQLite 3.47.0) in a container, PHP 8.5 with curl,
on the database node of the project's cluster (12 CPUs, load 40 to 50 from other work during the runs), and a whole
site installed with `exp:install --db=libsql`.

| Area | libSQL (sqld) | Notes |
|---|---|---|
| Kernel schema (153 tables) and clean data | works | the kernel's SQLite schema through `eZSQLiteSchema`: 4,205 statements in 22 to 49 s |
| Install with `exp:install` / kickstarter, package and extensions | works | the multisite package with its content and extensions: 16 steps, 0 errors, 0 warnings, 9 checks passed; 1,289 s (22 minutes) on a busy shared host, most of it in writes outside a transaction |
| Web setup wizard | does not | not offered; use `exp:install` |
| Reading pages, the admin, signing in | works | front page, site and admin login answer 200 (PHP's built-in server); signing in as admin works. Every statement is a round trip, so keep the view cache on |
| Transactions, nested `begin()`/`commit()`, rollback, a failed statement inside one | works | |
| New ids (`lastSerialID()`), a key of two columns | works | the server's rowid; MAX + 1 where SQLite numbers no column |
| Unicode text, long text (800 KB), BLOB, 64-bit integers | works | |
| Duplicate keys, missing tables (error numbers) | works | SQLite's result codes: 19, 1 |
| Portable helpers: paging, upsert, group concat, unix time | works | |
| Temporary tables | works | as ordinary tables (above) |
| Several readers and writers at once | works | the server queues the writers; a write transaction held longer than the server's transaction timeout while another waits is ended by the server |
| Several web servers on one database | works | one URL for all; files still need a shared directory or a DFS backend |
| DFS cluster backend on libSQL | does not | no backend; use a shared directory, or a DFS backend on MySQL, PostgreSQL, Oracle, SQL Server or MongoDB |
| Migrations after the install | works | 4 migrations applied, 0 failed (the installer's run) |
| Upgrade captions (the dump before an upgrade) | **does not** | "no built-in dump for a libSQL server": back the server up with its own tools and run with `--no-backup`; a rollback says the same |
| Migration captions (the marker of the database's state) | works | the rows of every table, hashed |
| Stream expired, connection lost, outside a transaction | works | sent again once |
| Without curl (`allow_url_fopen`) | works | the live tests run once over PHP's stream wrapper |
| A token the server refuses | works | the connection fails with "HTTP 401", the token never in the message |
| Turso's cloud | **not tested** | the same protocol over HTTPS with a token; no account was used. `expTursoDB` refuses to connect without a token |
| Embedded replicas | **does not** | see below |
| Data table connection (engines `libsql`, `turso`) | works | below |
| REST API's own SQLite handler (`ezcDbHandlerSqlite`) | does not | it opens a file; the REST API itself runs on the driver |

### Measured

| What | Time |
|---|---|
| connect (first statement on a new stream) | 67 ms |
| a row read by its key (stream kept) | 2.1 ms |
| a row written outside a transaction (the server commits each) | 76 to 101 ms |
| a row written inside one transaction | 2.3 to 6.7 ms |
| the kernel's schema and clean data (4,205 statements) | 22 to 49 s |
| a whole site with `exp:install` | 22 minutes (1,335 s, migrations included) |
| the front page, first request (empty caches, no OPcache, PHP's built-in server) | 26.9 s |
| the same page, view cache filled | 1.9 s |
| the admin login page | 3.3 s |

A read costs a round trip; a write outside a transaction costs the server's commit, which `sqld` makes durable (WAL
and its own replication log) before it answers. Writes inside a transaction cost a round trip each. The numbers come
from a loaded host; an idle server answers faster, a server in another data centre slower by the network's round trip.

## Embedded file mode and embedded replicas

- **A libSQL database file read by the SQLite driver**: a file `sqld` writes (`<data dir>/dbs/default/data`) is an
  SQLite file, and `DatabaseImplementation=sqlite` opens it, as long as no libSQL-only feature (vector columns, random
  rowids) is in it, and never while `sqld` runs on it (its WAL and replication log are its own). Useful to take a
  copy of a server's database to a machine of its own; not a way to share one.
- **Embedded replicas** (a local file that a libSQL client keeps in sync with the primary and writes through to it)
  are made by the libSQL client library, which PHP cannot load without Turso's native library (FFI) or a PHP
  extension of its own. The driver does not offer them.

## A data table connection to a libSQL database

Setup > Data connections, engine `libsql` (or `turso`): the server and port (8080, 443), the database's name, the auth
token as the connection's secret, and the options `url` (instead of the server and port; its host must be in
`datatable.ini [ConnectionSettings] AllowedHosts[]`) and `tls`. Data tables made there are SQLite tables on the server:
the schema planner, the describer and the table lists treat them as SQLite. Both engines are in `Engines[]` by default;
`turso` is marked untested.

## How it is built

| File | What |
|---|---|
| `lib/ezdb/classes/explibsqldb.php` | `expLibSQLDB extends eZSQLite3DB`: the connection, the functions rewritten, temporary tables, no writer gate |
| `lib/ezdb/classes/exptursodb.php` | `expTursoDB`: the profile `turso` |
| `lib/ezdb/classes/explibsqlconnection.php` | `expLibSQLConnection`: Hrana over HTTP with the part of PHP's `SQLite3` class the SQLite driver uses (`exec()`, `query()`, `querySingle()`, `lastErrorCode()`, `lastInsertRowID()` ...); curl or the stream wrapper |
| `lib/ezdb/classes/explibsqlresult.php` | `expLibSQLResult`: the rows, read as an `SQLite3Result` |
| `lib/ezdb/classes/explibsqlprofile.php` | `expLibSQLProfile`: the profiles `libsql` and `turso`, the URL and the token from the settings |

Hrana's values come back as PHP's SQLite3 gives them (integers as `int`, a 64-bit value beyond PHP's range as a
string, floats, text, blobs as bytes, `NULL`); its errors as SQLite's result codes (`SQLITE_CONSTRAINT` 19,
`TRANSACTION_TIMEOUT` 4) with SQLite's message.

## Tests

| Suite | What | Run |
|---|---|---|
| `libsql` | the transport, the profiles, the driver on a stand-in server (PHP's sqlite3 answering Hrana as sqld does), the data connection | `php vendor/bin/phpunit --testsuite libsql` |
| `libsql-live` | against a server: the kernel schema, values, transactions, ids, errors, the functions, the helpers, temporary tables, two connections, an expired stream, a data table, the stream wrapper | `EXP_TEST_LIBSQL_URL=http://127.0.0.1:8080 php vendor/bin/phpunit --testsuite libsql-live` (`EXP_TEST_LIBSQL_TOKEN` for a server with a token) |

The GitHub job `libsql` runs both on sqld 0.24.33 (PHP 8.5 and 8.3) and, on PHP 8.5, installs a whole site on the
server and fetches its front page and admin login.

See also: [Choosing a database](../../help/choosing-a-database.md), [Database support](../../help/database-support.md).
