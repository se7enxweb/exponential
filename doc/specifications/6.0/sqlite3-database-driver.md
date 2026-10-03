# Specification: the SQLite3 database driver

Reference for `eZSQLite3DB` and `eZSQLiteSchema`. The task-oriented
introduction is [SQLite database support](../../features/6.0/sqlite-database.md).

## Classes and files

| Class | File | Role |
|---|---|---|
| `eZSQLite3DB` (extends `eZDBInterface`) | `lib/ezdb/classes/ezsqlite3db.php` | Connection, queries, transactions, escaping, database listing. |
| `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` | Reads a live schema (`fetchTableFields`, `fetchTableIndexes`, uses the SQLite PRAGMA API), generates SQL (`generateDropIndexSql`, create and alter statements). |
| SQL files | `kernel/sql/sqlite/schema.sql`, `kernel/sql/sqlite/cleandata.sql` | Clean installation schema and data. |

Both classes are registered in the autoload arrays; the `eZSQLite3DB` entry was
added to `autoload/ezp_kernel.php` in April 2026 (`cefe8f13a2`). Without that
entry the alias `sqlite3=eZSQLite3DB` ended in a fatal autoload failure and a
null database connection when the kernel was loaded in a bridged setup.

## Registration

| File | Block | Key | Value |
|---|---|---|---|
| `settings/site.ini` | `[DatabaseSettings]` | `ImplementationAlias[sqlite3]` | `eZSQLite3DB` |
| `settings/dbschema.ini` | `[SchemaSettings]` | `SchemaPaths[sqlite]`, `SchemaPaths[sqlite3]` | `lib/ezdbschema/classes/ezsqliteschema.php` |
| `settings/dbschema.ini` | `[SchemaSettings]` | `SchemaHandlerClasses[sqlite]`, `SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` |
| `settings/setup.ini` | `[DatabaseSettings]` | `DefaultType` | `sqlite3` |

The setup wizard's database table (`kernel/setup/ezsetupcommon.php`) lists the
type `sqlite3` with driver `sqlite3`, name "SQLite", required version `3.0.1`,
demo data available, Unicode supported.

## Connection

- The constructor needs the PHP `sqlite3` extension. Without it the handler
  reports `ERROR_MISSING_EXTENSION` through the warning list and stays
  disconnected.
- `connect( $fileName )` resolves the file name like this:
  - `:memory:` stays an in-memory database;
  - a name beginning with `/` is an absolute path and is used as given;
  - any other name is placed in `var/storage/sqlite3/` (constant
    `STORAGE_DIRECTORY`); a missing directory is created recursively.
- An unreachable file throws `eZDBNoConnectionException`, as the MySQL driver
  does, because the setup wizard and `index.php` depend on that exception.
- At every connection the driver applies its PRAGMAs (`applyPragmas()`), then
  switches the journal to WAL mode (`useWAL()`).

### PRAGMA defaults and overrides

| PRAGMA | Default | Effect |
|---|---|---|
| `synchronous` | `NORMAL` | With WAL still crash-safe; `FULL` syncs on every commit. |
| `cache_size` | `-65536` | 64 MB page cache (SQLite default is 2 MB). |
| `mmap_size` | `268435456` | Read through 256 MB of mapped memory. |
| `temp_store` | `MEMORY` | Sorts and temporary tables in memory. |
| `busy_timeout` | `5000` | Wait up to 5 s for a lock instead of failing at once. |

Override or add entries with `[DatabaseSettings] SQLitePragmas[]=name=value` in
`settings/site.ini`.

## Transactions and writers

SQLite has one writer. `begin()` queues transactions behind a writer gate (a
lock file named `<database>.writer-lock` beside the database file) for at most
`[DatabaseSettings] SQLiteTransactionWait` seconds (default `60`). Waiting at
that point is safe because the transaction has written nothing; after the gate
is passed a transaction cannot fail because of a lock. Keep the value below the
web server's request timeout.

## SQL compatibility layer

| Method | Behaviour |
|---|---|
| `subString`, `concatString` | Map to `substr( s, n )` (the rest of the string, counted from 1 like the other engines) and `\|\|`. |
| `md5`, `md5UDF` | An `md5()` function registered with SQLite. |
| `bitAnd`, `bitOr` | SQL fragments for bitwise AND and OR. The aggregates `BIT_OR` and `BIT_AND` are registered as SQLite user functions at connection time, because the content engine folds language masks with `BIT_OR` and SQLite ships none. |
| `rewriteJoinedUpdate` | Rewrites MySQL's `UPDATE ... JOIN` into a form SQLite accepts. |
| `lastSerialID` | Uses the connection's last insert row id and, for tables whose primary key has more than one column, checks that the auto-increment column matches the row id. |
| `escapeString` | SQLite escaping; `null` becomes an empty string, as in the MySQL driver. |
| `createDatabase`, `removeDatabase`, `availableDatabases` | A database is a file that `connect()` creates on demand, so `createDatabase` only makes sure the directory exists; `removeDatabase` empties it so installers can reload a schema; `availableDatabases` lists the files of the storage directory. |
| `eZTableList`, `relationList`, `removeRelation` | Read `sqlite_master`. |

`eZPersistentObject` code and the kernel's SQL were adjusted in January 2024 so
the same statements run on both engines; the test runs also found missing
`is_countable()` guards, fixed in the same period (see
[PHP 8 support](../../bc/6.0/php8.md)).

## Known limits

- One writer at a time (see above).
- No user and password; access control is the file system's.
- No replication or clustering of its own; a shared file system is only
  suitable for read-mostly sites.

## Related

[MongoDB kernel support](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md) for
the other non-MySQL engine, and [SQL query cache](../../bc/6.0/sql-query-cache.md)
for how query results are cached on every engine.

## See also

Changelogs: [6.0.1](../../changelogs/6.0/6.0.1.md), [6.0.13](../../changelogs/6.0/6.0.13.md), [6.0.14](../../changelogs/6.0/6.0.14.md); chronicles: [January 2024, first half](../../history/2024/2024-01a.md), [April 2026](../../history/2026/2026-04.md), [June 2026, second half](../../history/2026/2026-06b.md).
