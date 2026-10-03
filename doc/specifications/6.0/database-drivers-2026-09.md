# Database drivers and installers: SQLite, PostgreSQL, MySQL and Oracle, 16 to 30 September 2026

What changed in the database drivers, the schema handlers and the setup wizard in
the second half of September 2026, why it matters, and how to check it on your
installation. MongoDB has its own addendum in
[MongoDB kernel support](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)
(section "Addendum: driver and kernel fixes of 18 to 20 September 2026"). The
transaction work of the following days is in
[SQLite and Oracle driver behaviour (October 2026)](database-drivers-sqlite-oracle.md).
The installers themselves are described in
[Installer logs and seed data](installer-logs-and-seed-data.md) and
[Installing in one command](../../features/6.0/install-in-one-command.md).

## SQLite

| Change | What it fixes | Where to look |
|---|---|---|
| **Index names are made unique across the database.** SQLite index names are database-wide, not per table as in MySQL, and schema files reuse names such as `contentobject_id` across tables. An index is created as `<table>__<name>` unless its name already starts with the table's; the schema comparison uses the same names. An index made earlier keeps its plain name and is dropped under it. | The second table using a name failed and the schema insert stopped: `cjw_newsletter` lost four tables on a default SQLite installation. | `lib/ezdbschema/classes/ezsqliteschema.php` |
| **A composite key keeps its whole key.** SQLite allows `AUTOINCREMENT` only on a one-column primary key, so the 13 tables keyed `(id, version)` (`ezcontentclass`, `ezcontentclass_attribute`, `ezcontentobject_attribute`, `ezworkflow` among them) were created with `id` alone as key, and storing version N+1 replaced version N (the kernel writes with `INSERT OR REPLACE` on SQLite). The auto-increment column of a composite key is now a plain `INTEGER`; the driver numbers new rows as before (`MAX + 1`). | A class or attribute lost its earlier versions. | same file |
| **The database check** called three schema methods that did not exist and answered 500. | Setup > System upgrade > database check on SQLite. | |
| **Connection settings for a web site.** Every connection gets `synchronous=NORMAL` (still crash-safe with WAL), a 64 MB page cache, a 256 MB memory map, temporary tables in memory and a 5 second busy timeout, instead of SQLite's defaults with a busy timeout of 0 (a request that met another's write lock failed at once). A failed query's log line carries SQLite's own message. Page views did not get faster in the measurement (the 12 MB database already sat in the file cache); the settings are for writes, concurrent access and lock errors under load. | "database is locked" under load. | `settings/site.ini` `[DatabaseSettings] SQLitePragmas[]` |
| **A database file that cannot be opened is a failed connection.** A directory SQLite could not create, a directory in the file's place or no write access used to give a warning and an uncaught exception; now `eZDBNoConnectionException` is thrown, as the MySQL driver does, so the setup wizard and `index.php` report a database that cannot be reached. `availableDatabases()` lists databases only, not the `-wal`, `-shm` and `-journal` files. `eZSQLite3DB::filePath()` and `STORAGE_DIRECTORY` (`var/storage/sqlite3`) give the file a `Database` value names. | | `lib/ezdb/classes/ezsqlite3db.php` |
| `escapeString( null )` returns an empty string, as the MySQL driver does (a PHP 8.1 deprecation). | The setup wizard raised a deprecation. | |
| **The REST API looks up OAuth tokens on a SQLite site.** The lazy database set-up of the REST layer knew only the MySQL, PostgreSQL and Oracle implementations and threw for `sqlite3`; it now opens the same file the site's driver opens (or an in-memory database for `:memory:`). | An unknown OAuth token answered a server error instead of 401. | |
| **Search indexing takes the batched path** (a multi-row `INSERT`, an `IN` list and an arithmetic `UPDATE`; SQLite has accepted a multi-row `INSERT` since 3.7.11, PostgreSQL since 8.2) as well as on MySQL and MongoDB. | Measured on 20 objects with 12,546 word links on SQLite: 1.62 s before, 0.88 s after (81 ms and 44 ms per object); the index is identical. | search plugin `ezsearchengine` |
| **The kernel's own SQLite schema and clean data load.** `kernel/sql/sqlite/schema.sql` created `sqlite_sequence`, which SQLite keeps for itself; `kernel/sql/sqlite/cleandata.sql` used MySQL string escapes. The literals use SQLite's quoting now; all 2,022 rows load and every serialized value reads back. The unused demo data files (`workingdata.sql`, `workingdataandschema.sql`, `workingexample.db`) were removed. | A SQLite installation from the SQL files. | `kernel/sql/sqlite/` |

Settings (file `settings/site.ini`, block `[DatabaseSettings]`, scope: the
installation, SQLite only):

| Key | Default | Meaning |
|---|---|---|
| `SQLitePragmas[]` | empty (the driver's defaults apply: `synchronous=NORMAL`, `cache_size=-65536`, `mmap_size=268435456`, `temp_store=MEMORY`, `busy_timeout=5000`) | One `name=value` per line over the driver's defaults. After an install run `ANALYZE` once so the query planner has statistics. |
| `SQLiteTransactionWait` | `60` | See [SQLite and Oracle driver behaviour](database-drivers-sqlite-oracle.md). |

Check the values your site uses:

```bash
./console exp:ini get site.ini/DatabaseSettings/SQLitePragmas --allow-root-user
grep -n 'SQLitePragmas' settings/site.ini
```

## The setup wizard

| Change | Setting |
|---|---|
| **SQLite is listed first and preselected** and the `sqlite3` extension is checked first. When the PHP extension is missing the page preselects the first available engine and says why SQLite is missing and how to enable it. A submit without a choice takes the preselected default; a kickstart `Type` of `mysql` or `sqlite` is mapped to its driver as `postgresql` already was. | `settings/setup.ini [DatabaseSettings] DefaultType=sqlite3` |
| **The SQLite file is checked before anything opens it**: a plain name ending in `.db`, `.db3`, `.sqlite` or `.sqlite3`, kept in `var/storage/sqlite3` (an absolute path only from `kickstart.ini`); the directory writable or creatable; an existing file writable and a SQLite database. Each refusal names the file, the directory and the web server's user. A file that already holds tables is asked about on the Site details page with the choices a non-empty MySQL database gets (keep and add, remove, keep and skip, choose another). The chosen name is kept between pages. | |
| The SQLite form posts no server, port, user or password; they are kept as empty values, so a web install no longer logs 14 "Undefined array key" warnings. The time zone check fails only when `php.ini` sets no zone and `index.php` fell back to UTC (it failed on every UTC server before). | |
| **PostgreSQL defaults**: the database page prefills port 5432 and user `postgres`. A `Default<Setting>_<type>` replaces `Default<Setting>` for that system. | `setup.ini [DatabaseSettings] DefaultPort_pgsql=5432`, `DefaultUser_pgsql=postgres` |

## PostgreSQL

- **`pgcrypto` is created when needed.** The installer needs `digest()`, which
  comes from `pgcrypto` (a trusted extension since PostgreSQL 13 that the
  database owner may create). It creates the extension when the server ships it
  (`CREATE EXTENSION IF NOT EXISTS pgcrypto`) and refuses only when that is not
  possible. The messages name `pgcrypto` and today's causes (server, port, user,
  password, database, `listen_addresses`, `pg_hba.conf`) with links to the
  PostgreSQL documentation; the old advice about `postmaster -i` and PostgreSQL
  7.2 is gone.
- **The wizard installs into the database named on the database page.** It used
  to connect to `template1`, list every database on the server and preselect the
  first, which on a shared server is somebody else's database. The login is now
  tested against the typed database (so a user who owns one database and may
  connect to nothing else gets through), and only without a name is `template1`
  opened and a list offered (databases the user may connect to, without
  templates, sorted by name).
- **The schema handler** creates `tinyint`, `smallint` and `mediumint` columns
  (used by `cjw_newsletter`) as `smallint` and `integer` without a length (they
  ended the request with `die()`); an unknown column type logs an error and
  throws. An index whose name already exists on another table is created as
  `<table>__<name>`, so the kernel's own index names stay.
- A PostgreSQL SQL dump made with `ezsqldumpschema.php` on a site that runs
  another database no longer queries the PostgreSQL catalogues (`pg_class`,
  `pg_index`) on it.
- `expInfo::kernelInfo()` read `ezsite_data.value` with the column in backticks,
  which MySQL and SQLite accept and PostgreSQL and Oracle do not, so the install
  version came back empty on those two. The column is named plainly now.

## MySQL

Since PHP 8.1 `mysqli` throws `mysqli_sql_exception` by default. The driver
caught only `ErrorException`, so a wrong password or an unknown database ended
the request with an uncaught exception; it now throws
`eZDBNoConnectionException`, which the setup wizard and `index.php` report as a
database that cannot be reached.

## SQL dumps from schema and data files

`php bin/php/ezsqldumpschema.php` turns `.dba` files into SQL without a database
connection, and each schema class fell back to its own escaping, all broken for
values with an apostrophe (the ISBN group names "China, People's Republic" and
"Lao People's Democratic Republic"):

| Engine | Before | Now |
|---|---|---|
| MySQL | `mysqli_real_escape_string()` without a link: a fatal error on the first value, so no MySQL data dump could be written. | Escapes the same characters that function does. |
| SQLite | Returned the value unchanged, so the quote ended the literal. | Doubles the quote. |
| PostgreSQL | `pg_escape_string()` without a connection (deprecated since PHP 8.1). | Uses the connected database when there is one, otherwise doubles the quote. |

`kernel/sql/common/cleandata.sql` was changed in the same way (standard SQL
quoting) so PostgreSQL loads it too.

Try it (read-only: it writes to standard output):

```bash
php bin/php/ezsqldumpschema.php --help --allow-root-user
```

## Oracle

Two queries wrote a table alias with `AS` (the per-location child count of
`eZContentObject::assignedNodes()` when locations are sorted by their number of
children, and the attribute join of the built-in search engine when results are
sorted by a class attribute). Oracle allows `AS` for column aliases only
(`ORA-00933`), so both failed there; the aliases are written without `AS`, which
is the same on every database. Install on Oracle with
`./console exp:install --db=oracle` (see
[Installing in one command](../../features/6.0/install-in-one-command.md)).

## How to check

- SQLite install: `./console exp:install --print --allow-root-user` shows
  `Type=sqlite3` in `[database_choice]` by default (it installs nothing).
- Index names on an existing SQLite database: a table created by an extension
  has indexes named `<table>__<name>`; list them with
  `sqlite3 var/storage/sqlite3/<file> ".indexes"` if the `sqlite3` tool is
  installed.
- MySQL login errors: point `Database` at a wrong password on a test siteaccess;
  expect the "database cannot be reached" page, not a PHP exception.

## Related pages

- [SQLite database](../../features/6.0/sqlite-database.md)
- [SQLite and Oracle driver behaviour (October 2026)](database-drivers-sqlite-oracle.md)
- [Installer logs and seed data](installer-logs-and-seed-data.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
