# Clustering on PostgreSQL: the DFS backend in the kernel

This page is for administrators who run, or plan to run, an Exponential site on several web servers, and for
developers who maintain or extend the cluster code. From Exponential 6.0.15 the kernel clusters on PostgreSQL as well
as on MySQL and MariaDB: the PostgreSQL metadata backend, its tables and its file gateway are part of the kernel, and a
default installation needs no extension for it.

## Contents

1. [Credit: where this backend comes from](#credit-where-this-backend-comes-from)
2. [What the kernel ships](#what-the-kernel-ships)
3. [What was refactored, fixed and extended](#what-was-refactored-fixed-and-extended)
4. [Stable example configurations](#stable-example-configurations)
5. [Moving from the ezpostgresqlcluster extension](#moving-from-the-ezpostgresqlcluster-extension)
6. [Tests and the PostgreSQL versions covered](#tests-and-the-postgresql-versions-covered)
7. [More engines: Oracle, MongoDB and SQLite](#more-engines-oracle-mongodb-and-sqlite)
8. [Related pages](#related-pages)

## Credit: where this backend comes from

The PostgreSQL cluster backend of the kernel is a refactoring and integration of earlier work, and we want to name
it plainly:

- **ezpostgresqlcluster**, by **eZ Systems AS** (Copyright 1999-2013), released under the **GNU General Public License
  version 2**. It added PostgreSQL to the DFS cluster of eZ Publish 4.7, the product Exponential grew from, with the
  class `eZDFSFileHandlerPostgresqlBackend`, a gateway for `index_cluster.php` and the PostgreSQL table definitions.
  Its design (one row per clustered file, the `.generating` rows that let one process generate a cache file while the
  others wait, the split between the storage table and the cache table) is the design the kernel keeps.
  Upstream: [github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster).
- **se7enxweb/ezpostgresqlcluster**, 7x's fork of it, which kept the extension installable on Exponential 6 with
  Composer and was the starting point and reference for this work:
  [github.com/se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster).

The kernel's code is a new implementation written method for method against the kernel's MySQLi backend, using the
extension as the source and inspiration for the PostgreSQL side. It is distributed under the same license, the GNU
General Public License version 2 (or any later version). The extension itself was not changed; installations that
still use it keep working (see [Moving from the extension](#moving-from-the-ezpostgresqlcluster-extension)).

## What the kernel ships

| File | What it is |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/postgresql.php` | `expDFSFileHandlerPostgresqlBackend`, the metadata backend |
| `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerpostgresqlbackend.php` | `eZDFSFileHandlerPostgresqlBackend`, the extension's class name, kept for existing settings |
| `kernel/private/classes/clusterfilehandlers/interfaces/expdfsfilehandlerdbbackendinterface.php` | `expDFSFileHandlerDBBackendInterface`, the contract of every metadata backend (MySQLi and PostgreSQL implement it) |
| `kernel/sql/postgresql/cluster_dfs_schema.sql` | the tables `ezdfsfile` and `ezdfsfile_cache` |
| `kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql` | upgrades tables made by the extension |
| `kernel/clustering/dfspostgresql.php` | the gateway `index_cluster.php` loads for `CLUSTER_STORAGE_BACKEND` `dfspostgresql` (`dfspostgres` is the same) |

PostgreSQL 13 or later is needed; everything the backend uses (`ON CONFLICT`, `SKIP LOCKED`, `lock_timeout`,
`text_pattern_ops`) is there from 13 on. PHP 7.4 is usable; PHP 8.0 to 8.5 are clean. The PHP extension `pdo_pgsql` is
needed on every web server.

## What was refactored, fixed and extended

Compared with the extension (its 2013 code and the fork's state in November 2026):

**Fixed**

- **No `pg_*` function is called on a PDO object.** The extension called `pg_escape_string()` and `pg_result_error()`
  with PDO objects, a fatal error on PHP 8. The kernel backend uses PDO only, with every value a bound parameter.
- **Deadlocks are retried.** The extension tested MySQL's error numbers 1205 and 1213, which PostgreSQL never reports,
  so a deadlock was never retried. The kernel backend reads the SQLSTATE and retries a deadlock (`40P01`), a lock not
  granted in time (`55P03`) and a serialization failure (`40001`), up to `DBExecuteRetries` times with a short pause
  that grows with each try (at most 0.2 seconds).
- **No silent rollback.** In PostgreSQL a failed statement aborts its transaction, and the following `COMMIT` answers
  `ROLLBACK` without an error. The backend notices the failed statement, rolls back and reports the failure.
- **No transaction left open:** a rename whose file copy fails now rolls back (the MySQLi code returned without it).
- **Times after 2038:** sizes and times are `bigint` (the extension used `integer`).
- **The cluster events work:** the backend implements `eZClusterEventNotifier`, which the extension did not, so a
  cache listener (`file.ini [ClusterEventsSettings]`) works on PostgreSQL too.
- **The gateway** uses port 5432 by default (the extension's example said 5433), a prepared statement, no path into
  an extension directory, and streams a file in pieces, so a large file or a byte range never sits in memory whole.

**Refactored**

- Stores are one statement, `INSERT ... ON CONFLICT (name_hash) DO UPDATE`, instead of a `SELECT` followed by an
  `INSERT` or `UPDATE`, which two processes could interleave.
- Cache generation starts with `INSERT ... ON CONFLICT DO NOTHING`: one process gets the `.generating` row, the
  others are told how long to wait. Taking over a generation that timed out is one `UPDATE` only one process can win.
- **Bounded locking:** every connection sets `lock_timeout` (`file.ini [eZDFSClusteringSettings] DBLockTimeout`,
  seconds, default 10), so no statement waits for a row lock forever. Purges take their rows with
  `FOR UPDATE SKIP LOCKED`, so two purges never wait on each other, and delete exactly the rows they took.
- The `name` columns have a `text_pattern_ops` index, so the prefix searches of deletes and purges
  (`name LIKE 'var/site/cache/%'`) use an index whatever the database's collation.

**Extended**

- One contract for every engine, `expDFSFileHandlerDBBackendInterface`, and one parity test suite that runs the same
  checks on every backend (see [Tests](#tests-and-the-postgresql-versions-covered)).
- The parity suite found a defect in the kernel's MySQLi backend as well, now fixed: `_deleteByWildcard()` lost the
  escape of a dot, so `a.txt` also matched `aXtxt`.

## Stable example configurations

These are the configurations the tests run. Put the settings in the global override
`settings/override/file.ini.append.php` and the constants in `config.cluster.php`; keep both out of version control.
The shared mount must be the same path on every web server.

### MySQL or MariaDB

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/mnt/exp-dfs
DBBackend=eZDFSFileHandlerMySQLiBackend
DBHost=cluster-db.internal
DBPort=3306
DBName=exp_cluster
DBUser=exp_cluster
DBPassword=<the password>
```

Tables: `mysql -h cluster-db.internal -u exp_cluster -p exp_cluster < kernel/sql/mysql/cluster_dfs_schema.sql`.

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfsmysqli' );
define( 'CLUSTER_STORAGE_HOST', 'cluster-db.internal' );
define( 'CLUSTER_STORAGE_PORT', 3306 );
define( 'CLUSTER_STORAGE_USER', 'exp_cluster' );
define( 'CLUSTER_STORAGE_PASS', '<the password>' );
define( 'CLUSTER_STORAGE_DB', 'exp_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8mb4' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/mnt/exp-dfs' );
```

### PostgreSQL

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/mnt/exp-dfs
DBBackend=expDFSFileHandlerPostgresqlBackend
DBHost=cluster-db.internal
DBPort=5432
# or, on the database server itself, the directory of PostgreSQL's Unix socket:
# DBSocket=/run/postgresql
DBName=exp_cluster
DBUser=exp_cluster
DBPassword=<the password>
# optional: seconds a statement waits for a row lock before its transaction is tried again
DBLockTimeout=10
```

Tables: `psql -v ON_ERROR_STOP=1 -h cluster-db.internal -U exp_cluster -d exp_cluster -f kernel/sql/postgresql/cluster_dfs_schema.sql`.

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfspostgresql' );
define( 'CLUSTER_STORAGE_HOST', 'cluster-db.internal' );
define( 'CLUSTER_STORAGE_PORT', 5432 );
define( 'CLUSTER_STORAGE_USER', 'exp_cluster' );
define( 'CLUSTER_STORAGE_PASS', '<the password>' );
define( 'CLUSTER_STORAGE_DB', 'exp_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/mnt/exp-dfs' );
```

The site's own database and the cluster database are separate choices: a site on MySQL can keep its cluster
metadata in PostgreSQL and the other way round.

### A one-node cluster

A single server can run in cluster mode, with the mount a local directory (or an NFS export on the same machine) and
the cluster tables in a local database. A second web server later mounts the same share and uses the same settings;
the site is not moved. Then move the existing files in once with `php bin/php/clusterize.php` and point the rewrite
rules for files at `index_cluster.php` ([Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md)).

## Moving from the ezpostgresqlcluster extension

1. Run `kernel/sql/postgresql/cluster_dfs_schema_upgrade.sql` on the cluster database while no web server writes to it
   (it changes the column types and adds the indexes; the rows stay).
2. Set `DBBackend=expDFSFileHandlerPostgresqlBackend` and `DBPort` to your server's port.
3. In `config.cluster.php`, set `CLUSTER_STORAGE_BACKEND` to `dfspostgresql` and remove `CLUSTER_STORAGE_GATEWAY_PATH`.
4. Deactivate and remove the extension, regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and clear
   the caches.

Settings that still name `eZDFSFileHandlerPostgresqlBackend` keep working: without the extension that name is the
kernel's class. While the extension is installed, its own class of that name is loaded instead, as extension classes
come first in the autoload.

## Tests and the PostgreSQL versions covered

| Test | What it checks | Needs |
|---|---|---|
| `expDFSFileHandlerPostgresqlBackendTest` | SQLSTATE mapping and the retry of a deadlock, DSN, LIKE and wildcard building, name trunks, method parity with the MySQLi backend, the schema files, the gateway, the old class name | nothing (runs in the default suite) |
| `expDFSPostgresqlBackendIntegrationTest` | the parity checks below, plus the gateway serving metadata and byte ranges, the index use of prefix searches and the upgrade of extension tables | `EXP_TEST_PGCLUSTER_DSN=pgsql://user@host:port/postgres` |
| `expDFSMySQLiBackendParityTest` | the same parity checks on the MySQLi backend | `EXP_TEST_MYCLUSTER_DSN=mysql://user@host:port` |
| `eZDFSFileHandlerTest` | the cluster file handler itself, on the database of the test run (MySQL or PostgreSQL) | the database test suites |

The parity checks (`expDFSBackendParityChecks`): store and fetch, a second store updating the one row, a local file
stored and fetched back, byte ranges, copy, link copy, rename, delete and purge, LIKE deletes (an underscore is a
literal), wildcard deletes, deletes by directory list and by name trunk, the cache table, file lists, the expired list,
cache generation (one winner, onto an expired file, taking over after the timeout, the timeout check, abort), the
cluster events, deleting cache rows from the storage table, **four processes generating one cache file at the same
moment** (one generates, three wait, nobody fails) and, on PostgreSQL, **a lock held by another transaction** (the store
waits one second, is retried and succeeds once the lock is gone, or gives up after `DBExecuteRetries` tries instead of
hanging).

Measured on 19 November 2026: PostgreSQL 13, 14, 15, 16, 17, 18 and 19 (release candidate 1) passed all 27 checks;
MySQL 8.0 passed the 21 that apply (the two left out are times after 2038, which MySQL's `int(11)` column cannot hold,
and the bounded lock wait, which MySQL leaves to its own `innodb_lock_wait_timeout`). GitHub Actions runs the same
on every push (job "DFS cluster backends" of `.github/workflows/phpunit.yml`, PostgreSQL 13 to 19).

## More engines: Oracle, MongoDB and SQLite

The kernel now has what a further engine needs: the contract `expDFSFileHandlerDBBackendInterface` and the parity
checks. A new backend is one class, one schema file, one gateway and one test class:

1. `kernel/private/classes/clusterfilehandlers/dfsbackends/<engine>.php`, a class implementing
   `expDFSFileHandlerDBBackendInterface` and `eZClusterEventNotifier`. The PostgreSQL backend is the template: its
   transaction handling (`_protect()`, a list of retryable error codes, a bounded lock wait) and its one-statement upsert
   carry over directly.
2. `kernel/sql/<engine>/cluster_dfs_schema.sql` with the same columns; `name_hash` stays `md5( name )` on every engine.
3. `kernel/clustering/dfs<engine>.php`, a gateway extending `ezpClusterGateway`.
4. A test class using the trait `expDFSBackendParityChecks`, with its throwaway database, and a leg in the CI job.

The order the owner set: PostgreSQL first (this page), then the others in the kernel:

| Engine | Plan | Notes |
|---|---|---|
| Oracle | a kernel backend after the structure of the PostgreSQL one; until then the `ezoracle` extension's `eZDFSFileHandlerOracleBackend` | `MERGE` for the upsert, `SELECT ... FOR UPDATE SKIP LOCKED` and `WAIT n` for bounded locks, ORA-00060 (deadlock) and ORA-30006 (wait timeout) as the retryable errors |
| MongoDB | **built**: `expDFSFileHandlerMongoDBBackend`, one node or a replica set ([Clustering on MongoDB](mongodb-dfs-cluster.md)) | `_id` = `name_hash`, so the unique `_id` gives the one-winner generation (duplicate key = being generated); upserts for stores, compare and set on `mtime` for take-overs; no multi-document transaction, so it runs on a single server too; only the PHP extension, no library |
| SQLite | **built**: `expDFSFileHandlerSQLiteBackend`, one node and tests only ([Clustering on SQLite](sqlite-dfs-cluster.md)) | one file, so one machine: `BEGIN IMMEDIATE` and a busy timeout in place of row locks; suited to a one-node cluster and to the parity suite on a laptop, never to several web servers |

SQLite and MongoDB are built; Oracle starts with its parity test class.

## Related pages

- [Clustering: DFS and its metadata database](../../help/database-administrators/clustering-dfs-and-the-database.md)
- [Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md)
- [Database support](../../help/database-support.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
