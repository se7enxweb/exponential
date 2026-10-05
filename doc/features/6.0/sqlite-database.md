# SQLite database support

This page is for anyone who wants to try Exponential, develop on a laptop, run automated tests, or host a small site
on a plan that offers no MySQL. With SQLite, a complete Exponential site runs from one file on disk, with no database
server to install, configure or secure.

SQLite support arrived in January 2024 (releases 6.0.1 to 6.0.3, see the
[January 2024 chronicle](../../history/2024/2024-01a.md)) and has been refined
since. Everything below describes the code as it is now; a note says where a
setting came in a later release than the first driver.

## What you get

- A kernel database driver, `eZSQLite3DB`
  (`lib/ezdb/classes/ezsqlite3db.php`), selected with the implementation
  alias `sqlite3`.
- A schema handler, `eZSQLiteSchema`
  (`lib/ezdbschema/classes/ezsqliteschema.php`), so the setup wizard, the
  package installer and schema tools such as `bin/php/ezsqldumpschema.php` and `bin/php/ezsqlinsertschema.php` can create and
  read tables.
- SQLite-flavoured SQL files in `kernel/sql/sqlite/` (`schema.sql`,
  `cleandata.sql`), used by the setup wizard for a clean install.
- A setup wizard that lists SQLite as a database choice, tests that the PHP
  `sqlite3` extension is loaded and explains what to do if it is not.

## Try it in five minutes

1. Check that PHP has the extension:

   ```bash
   php -m | grep -i sqlite3
   ```

   It must print `sqlite3`. If it prints nothing, install your distribution's
   `php-sqlite3` package (or enable `extension=sqlite3` in `php.ini`) and reload
   PHP-FPM.

2. Open the setup wizard in the browser (`/setup`). On the database page choose
   **SQLite**. The wizard preselects it when the PHP `sqlite3` extension is
   present (setting `[DatabaseSettings] DefaultType=sqlite3` in
   `settings/setup.ini`, see below). If the extension is missing the wizard
   says so and falls back to the first available database system.

3. Give the database a name (the wizard proposes `sqlite.db`, constant `SQLITE_DEFAULT_FILE_NAME`). A name without a path is
   created as a file in `var/storage/sqlite3/`:

   ```bash
   ls -l var/storage/sqlite3/
   ```

4. Finish the wizard as usual. The site now runs from that single file.

## Settings

| File | Block | Key | Default | Scope | What it does |
|---|---|---|---|---|---|
| `settings/site.ini` | `[DatabaseSettings]` | `DatabaseImplementation` | `ezmysqli` | siteaccess | Set to `sqlite3` to use the driver. |
| `settings/site.ini` | `[DatabaseSettings]` | `Database` | (site specific) | siteaccess | The database file. A name without a slash lives in `var/storage/sqlite3/`; an absolute path (starting with `/`) is used as it is; `:memory:` is a throw-away in-memory database. |
| `settings/site.ini` | `[DatabaseSettings]` | `ImplementationAlias[sqlite3]` | `eZSQLite3DB` | global | Maps the alias to the driver class. |
| `settings/dbschema.ini` | `[SchemaSettings]` | `SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | global | Schema handler used by installers and schema tools. |
| `settings/setup.ini` | `[DatabaseSettings]` | `DefaultType` | `sqlite3` | global | Database system the wizard lists first. One of `sqlite3`, `mysqli`, `pgsql`, `mongodb`. |
| `settings/site.ini` | `[DatabaseSettings]` | `SQLitePragmas[]` | empty list | siteaccess | Extra `name=value` PRAGMAs applied on every connection (later releases). |
| `settings/site.ini` | `[DatabaseSettings]` | `SQLiteTransactionWait` | `25` | siteaccess | Seconds a transaction waits at its start for the other writers before giving up with "database is busy" (later releases). Keep it below the request timeout: Velocity ends a request after `Q.webserver.requestTimeout`, 30 s unless changed, hence 25; under PHP-FPM the limit is the pool's `request_terminate_timeout`. Raise both together. |

The driver's built-in PRAGMA defaults (also later releases) are
`synchronous=NORMAL`, `cache_size=-65536` (64 MB), `mmap_size=268435456`,
`temp_store=MEMORY` and `busy_timeout=5000`, and the journal runs in WAL mode.
See [the driver specification](../../specifications/6.0/sqlite3-database-driver.md).

## Using a database file that another application shares

Since April 2026 (`cefe8f13a2`) the driver accepts an absolute path in
`Database`, and registers the `eZSQLite3DB` class in the kernel autoload array.
That lets a legacy bridge point Exponential at the very file a Symfony/Doctrine
application uses, so both stacks see the same data:

```ini
# settings/siteaccess/<name>/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=sqlite3
Database=/absolute/path/to/shared.db
```

The directory of a new database is created recursively (`ae5feaf6a7`), so a
nested path works on first use.

## Limits and good practice

- SQLite allows one writer at a time. Many editors publishing at the same moment
  queue behind each other (see `SQLiteTransactionWait`); for a busy editorial
  team choose MySQL, PostgreSQL or MongoDB.
- Do not put the database file inside a directory the web server serves
  directly. In the shipped `.htaccess` only a short list of `var/` paths (public images, public caches, preview images of packages) is served as files; the rest goes to `index.php`. Verify on your own host that the database file is not downloadable: `curl -sI https://www.example.com/var/storage/sqlite3/sqlite.db`. A
  status other than 200 is what you want.
- Run `ANALYZE` once after a big import so the query planner has statistics.
- Back up with `sqlite3 <file> ".backup <target>"`, not by copying the file
  while the site is busy.

## Related pages

- Specifications: [SQLite3 database driver](../../specifications/6.0/sqlite3-database-driver.md), [database drivers and installers, 16 to 30 September 2026](../../specifications/6.0/database-drivers-2026-09.md), [SQLite and Oracle driver behaviour (October 2026)](../../specifications/6.0/database-drivers-sqlite-oracle.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md)
- [SQLite: transactions queue for the write lock](../../bc/6.0/sqlite-transactions.md)
- [Install in one command](install-in-one-command.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [MongoDB database support](mongodb-database-support.md)
- [PHP 8 support](../../bc/6.0/php8.md) (the countable and `is_countable` fixes that came out of the SQLite test runs)
- Changelogs: [6.0.1](../../changelogs/6.0/6.0.1.md) (the driver), [6.0.13](../../changelogs/6.0/6.0.13.md) (absolute paths, autoload), [6.0.14](../../changelogs/6.0/6.0.14.md) (schema reading)
- History: [January 2024, first half](../../history/2024/2024-01a.md), [January 2024, second half](../../history/2024/2024-01b.md), [April 2026](../../history/2026/2026-04.md), [June 2026, second half](../../history/2026/2026-06b.md) (dropping indexes)
