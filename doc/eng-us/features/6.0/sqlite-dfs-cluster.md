# Clustering on SQLite: the one-node DFS backend in the kernel

This page is for administrators who want a site on SQLite to keep its files in a DFS cluster on one server, and for
developers who run the cluster tests without a database server. From Exponential 6.0.15 the kernel's DFS cluster keeps
its file metadata in SQLite as well as in MySQL/MariaDB and PostgreSQL: the backend, its tables and its file gateway
are part of the kernel.

**SQLite clusters one machine, never several.** A SQLite database is a file, and its locks are the file system's. On a
network share (NFS, SMB) those locks are not reliable, and SQLite's own documentation warns against writing one
database from several machines. The SQLite backend is therefore for:

- a **one-node cluster**: the PHP servers of one host (PHP-FPM and Velocity, or two FPM pools) on one share and one
  database file, so that the site already runs in cluster mode and a later move to MySQL/MariaDB or PostgreSQL is a
  settings change;
- **tests**: the parity checks of every cluster backend run on SQLite with nothing to install.

A site on several web servers keeps its cluster metadata in MySQL/MariaDB or
[PostgreSQL](postgresql-dfs-cluster.md).

## Contents

1. [Credit: where this backend comes from](#credit-where-this-backend-comes-from)
2. [What the kernel ships](#what-the-kernel-ships)
3. [How it locks: one writer at a time](#how-it-locks-one-writer-at-a-time)
4. [Stable example configuration](#stable-example-configuration)
5. [A one-node cluster with exp:cluster:enable](#a-one-node-cluster-with-expclusterenable)
6. [Tests](#tests)
7. [Limits](#limits)
8. [Related pages](#related-pages)

## Credit: where this backend comes from

The SQLite backend follows the structure of the kernel's [PostgreSQL backend](postgresql-dfs-cluster.md), which is a
refactoring and integration of **ezpostgresqlcluster** by **eZ Systems AS** (GNU General Public License version 2,
[github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) and of 7x's fork of it,
[se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). The design it keeps (one row per
clustered file, the `.generating` rows that let one process generate a cache file while the others wait, the storage
table and the cache table) is the design of the eZ Systems cluster code. The SQLite class is a new implementation of the
same contract, written method for method against the kernel's MySQLi backend, under the same license (GNU General
Public License version 2, or any later version).

## What the kernel ships

| File | What it is |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/sqlite.php` | `expDFSFileHandlerSQLiteBackend`, the metadata backend |
| `kernel/sql/sqlite/cluster_dfs_schema.sql` | the tables `ezdfsfile` and `ezdfsfile_cache` |
| `kernel/clustering/dfssqlite.php` | the gateway `index_cluster.php` loads for `CLUSTER_STORAGE_BACKEND` `dfssqlite` |

SQLite 3.24 or later is needed (`INSERT ... ON CONFLICT DO UPDATE`), with the PHP extension `pdo_sqlite`. PHP 7.4 is
usable; PHP 8.0 to 8.5 are clean (on PHP 8.4 and later the driver's constants of `Pdo\Sqlite` are used, so PHP 8.5
reports no deprecation).

## How it locks: one writer at a time

SQLite has no row locks: a write takes the lock of the whole database. The backend uses that instead of the
`SELECT ... FOR UPDATE` of the server engines:

- **Every transaction is `BEGIN IMMEDIATE`.** It takes the write lock at its start, so nothing the transaction read
  can change before it writes, and two writers never meet in the middle of a transaction (no deadlock, no
  `SQLITE_BUSY_SNAPSHOT`).
- **The wait is bounded.** A connection waits for the lock up to its busy timeout, `[eZDFSClusteringSettings]
  DBLockTimeout` seconds (default 10). A transaction that did not get the lock in that time (`SQLITE_BUSY`, result code
  5, or `SQLITE_LOCKED`, 6) is tried again by the backend, up to `DBExecuteRetries` tries, with a short pause that grows
  with each try (at most 0.2 s). The callback of a transaction never runs outside it: when the `BEGIN IMMEDIATE` does
  not get the lock, the work waits for the next try.
- **Readers never wait.** The database is switched to WAL journal mode on the first connection (the mode stays in the
  file), so pages and the file gateway read while a writer writes.
- **One generation per cache file.** `_startCacheGeneration()` inserts the `.generating` row with
  `INSERT ... ON CONFLICT (name_hash) DO NOTHING`: one process gets the row, the others are told to wait or to serve the
  stale file, as on the other engines.

Name searches use `GLOB` instead of `LIKE`. SQLite's `LIKE` ignores case and has no escape character by default;
`GLOB` compares case sensitively, as MySQL's `LIKE` on the binary `name` column does, and a prefix (`var/site/cache/*`)
uses the name index. The `%` of a LIKE pattern becomes `*`, an underscore stays a literal (as in the MySQLi backend),
and `*`, `?` and `[` in a file name are literals. A shell wildcard with alternatives (`{a,b}.txt`) becomes one `GLOB`
pattern per alternative.

## Stable example configuration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerSQLiteBackend
# the database file: an absolute path, or a name in var/storage/sqlite3 of the installation
DBName=/var/www/vhosts/example.com/dfs-db/cluster.db
# optional: seconds a write waits for the database's lock before its transaction is tried again
DBLockTimeout=10
DBExecuteRetries=20
```

`DBHost`, `DBPort`, `DBSocket`, `DBUser` and `DBPassword` are not used. The database file is opened without SQLite's
create flag: a wrong `DBName` is a connection error, never a new empty database. Create it once with the tables:

```bash
sqlite3 /var/www/vhosts/example.com/dfs-db/cluster.db < kernel/sql/sqlite/cluster_dfs_schema.sql
```

The folder of the file must be writable by every PHP user of the site (WAL mode keeps `cluster.db-wal` and
`cluster.db-shm` next to it), and must not be served by the web server: below `var/storage` it is not, and a folder
outside the document root is safer still.

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlite' );
define( 'CLUSTER_STORAGE_DB', '/var/www/vhosts/example.com/dfs-db/cluster.db' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
```

The site's own database and the cluster database are separate choices: a site on MySQL can keep its cluster metadata
in a SQLite file on a one-node server, and a site on SQLite can keep it in its own database file.

## A one-node cluster with exp:cluster:enable

On a site whose database is SQLite (`DatabaseImplementation=sqlite3`), `./console exp:cluster:enable --apply` picks the
SQLite backend by itself (`--backend=sqlite` names it): it makes the share, creates the cluster tables in the site's
own database file, writes the settings and `config.cluster.php` (`dfssqlite`, the site's `Database` as
`CLUSTER_STORAGE_DB`) and moves the stored files in. `exp:cluster:disable` takes it back.
[A one-node DFS cluster in one command](cluster-commands.md) describes both commands.

The site and its cluster then share one database file and its one write lock. For a busy site, a separate cluster file
(`DBName` above) keeps the cluster's writes from queuing behind the site's.

## Tests

| Test | What it checks | Needs |
|---|---|---|
| `expDFSFileHandlerSQLiteBackendTest` | result codes and the retry of a busy `BEGIN IMMEDIATE`, the database file of `DBName`, LIKE and wildcard patterns as `GLOB` patterns, name trunks, the connection options, method parity with the MySQLi backend, the schema file, the gateway | nothing |
| `expDFSSQLiteBackendIntegrationTest` | the parity checks of every backend (`expDFSBackendParityChecks`), plus WAL mode, the index use of prefix searches, case sensitive names, `GLOB` characters in names, a missing database file and the gateway serving metadata and byte ranges | `pdo_sqlite` (and `pcntl`, `posix` for the checks with several processes) |

The parity checks are the ones the MySQLi and PostgreSQL backends pass, among them **four processes generating one
cache file at the same moment** (one generates, three wait, nobody fails) and **a lock held by another connection**:
a second process holds `BEGIN IMMEDIATE`, the store waits one second, is retried and succeeds once the lock is gone, or
gives up after `DBExecuteRetries` tries with `SQLITE_BUSY` instead of hanging. All 29 pass. Both test classes need no
server, so they run in the default suite and in the "DFS cluster backends" job of GitHub Actions.

Measured on 19 November 2026 on alpha: the parity suite 29 of 29; a one-node cluster through the real
`eZDFSFileHandler` (store and fetch of a 2 MB file, copy, rename, delete, purge, a view cache made, expired by its
name trunk and made again); the gateway `index_cluster.php` with `dfssqlite` (a whole file, a byte range, a 404);
**two PHP servers of one host** (PHP 8.5 and PHP 8.4, each with its own local `var`, one share, one database file) asking
for one cache key at the same moment, 20 rounds: exactly one generation per round, no error, slowest answer about
1 second; and a runtime check of the backend on PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5 with no notice or deprecation.

## Limits

- **One machine.** Several web servers cannot share a SQLite file safely; for them use MySQL/MariaDB or PostgreSQL.
  The share (`MountPointPath`) of a one-node cluster can still be an NFS export, so that a second server can mount
  it once the metadata moves to a server engine.
- **One writer at a time.** Writes queue on the database's lock; on a site that writes much, the cluster's writes and
  the site's own writes (when they share one file) wait for each other. Readers never wait.
- **No `eZDFSFileHandlerTest` on SQLite yet**: the kernel's database test runner sets up MySQL, PostgreSQL, Oracle and
  MongoDB databases; the SQLite backend is tested through the parity checks above instead.

## Related pages

- [Clustering in Exponential 6.0](clustering-overview.md)
- [Clustering on PostgreSQL: the DFS backend in the kernel](postgresql-dfs-cluster.md)
- [A one-node DFS cluster in one command](cluster-commands.md)
- [SQLite database support](sqlite-database.md)
- [Clustering: DFS and its metadata database](../../help/database-administrators/clustering-dfs-and-the-database.md)
- [Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
