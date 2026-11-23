# Clustering on Oracle: the DFS backend in the kernel

This page is for administrators who run a site on Oracle Database and want its files in a DFS cluster on several web
servers, and for developers who maintain the cluster backends. From Exponential 6.0.15 the kernel's DFS cluster keeps
its file metadata in Oracle as well as in MySQL/MariaDB, PostgreSQL, MongoDB and SQLite: the backend, its tables and its
file gateway are part of the kernel and need only the PHP extension `oci8`. The `ezoracle` extension is still what a
site needs for Oracle as its own database; its cluster backend `eZDFSFileHandlerOracleBackend` is replaced by the
kernel's, and the tables it made are upgraded with one SQL file.

## Contents

1. [Credit: where this backend comes from](#credit-where-this-backend-comes-from)
2. [What the kernel ships](#what-the-kernel-ships)
3. [How it locks: bounded waits on row locks](#how-it-locks-bounded-waits-on-row-locks)
4. [Stable example configuration](#stable-example-configuration)
5. [Coming from the ezoracle extension's backend](#coming-from-the-ezoracle-extensions-backend)
6. [A one-node cluster with exp:cluster:enable](#a-one-node-cluster-with-expclusterenable)
7. [Tests](#tests)
8. [Limits](#limits)
9. [Related pages](#related-pages)

## Credit: where this backend comes from

The Oracle backend follows the structure of the kernel's [PostgreSQL backend](postgresql-dfs-cluster.md), which is a
refactoring and integration of **ezpostgresqlcluster** by **eZ Systems AS** (GNU General Public License version 2,
[github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) and of 7x's fork of it,
[se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). Its Oracle specifics (the
`filesize` column, the connect string in `DBName`, the purge with `FOR UPDATE`, ORA-00060 as the error to try again)
come from the cluster backend of the **ezoracle** extension by **eZ Systems AS** (GNU General Public License version 2),
which 7x maintains for Exponential. The design it keeps (one row per clustered file, the `.generating` rows that let
one process generate a cache file while the others wait, the storage table and the cache table) is the design of the
eZ Systems cluster code. The kernel class is a new implementation of the same contract, written method for method
against the kernel's MySQLi and SQLite backends, under the same license (GNU General Public License version 2, or any
later version).

## What the kernel ships

| File | What it is |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/oracle.php` | `expDFSFileHandlerOracleBackend`, the metadata backend |
| `kernel/sql/oracle/cluster_dfs_schema.sql` | the tables `ezdfsfile` and `ezdfsfile_cache` |
| `kernel/sql/oracle/cluster_dfs_schema_upgrade.sql` | the upgrade of the table the `ezoracle` extension made |
| `kernel/clustering/dfsoracle.php` | the gateway `index_cluster.php` loads for `CLUSTER_STORAGE_BACKEND` `dfsoracle` |

Oracle Database 12.1 or later is needed (the lists use `OFFSET ... FETCH NEXT`), with the PHP extension `oci8` and an
Oracle client (Instant Client). PHP 7.4 is usable; PHP 8.0 to 8.5 are clean. Tested on Oracle AI Database 26ai Free
(23.26).

Two things of Oracle shape the tables:

- **`SIZE` is a reserved word**, so the size is the column `filesize`, as in the `ezoracle` extension's table. The
  backend returns it as `size`, like every other backend, and never uses a column name as a bind name (`:size` is
  ORA-01745).
- **Oracle stores the empty string as `NULL`.** `datatype` and `scope` may be `NULL` (a `.generating` row has neither)
  and are read back as `''`. A list "every scope but these" keeps a row without scope, as the other engines keep a row
  whose scope is `''`.

Sizes and times are `NUMBER(19)` (no 2038 limit). Names are compared binary and case sensitive (Oracle's default
`NLS_COMP`), so the name index serves both `name = ...` and the prefix searches of the deletes and purges.

## How it locks: bounded waits on row locks

Oracle has row locks, like MySQL and PostgreSQL, but an `UPDATE` or `DELETE` waits for a locked row **without limit**.
The backend therefore never lets a change wait on its own:

- **Every change locks its rows first** with `SELECT ... FOR UPDATE WAIT n`, where n is `[eZDFSClusteringSettings]
  DBLockTimeout` (seconds, default 10). A wait that ran out raises ORA-30006 (ORA-00054 on Oracle 23ai and later),
  which the backend tries again, up to `DBExecuteRetries` tries, with a short pause that grows with each try (at most
  0.2 s). A deadlock (ORA-00060), a serialization failure (ORA-08177) and the duplicate key of two processes inserting
  the same new row at once (ORA-00001) are tried again the same way. After the last try the store fails instead of
  hanging.
- **Stores are `MERGE`**: one statement inserts the row or updates it.
- **One generation per cache file.** `_startCacheGeneration()` inserts the `.generating` row with a plain `INSERT`:
  its `name_hash` is the primary key, so one process gets the row and the others get ORA-00001 and are told to wait or
  to serve the stale file, as on the other engines. A take-over after the timeout is one `UPDATE` conditioned on the
  `mtime` read.
- **Purges skip locked rows**: `_purgeByLike()` selects with `FOR UPDATE SKIP LOCKED`, so two purges never wait for
  each other; the rows another process holds are left for the next run.

Name searches use `LIKE ... ESCAPE '\'`. The `%` of a LIKE pattern stays a wildcard, an underscore is a literal (as in
the MySQLi backend), and `%`, `_` and `\` in a file name are literals. A shell wildcard with alternatives (`{a,b}.txt`)
becomes one `LIKE` pattern per alternative.

## Stable example configuration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerOracleBackend
# the connect string: Easy Connect host:port/service, a TNS alias or a full descriptor
DBName=db.example:1521/EXAMPLEPDB
DBUser=cluster
DBPassword=secret
DBConnectRetries=3
DBExecuteRetries=20
# optional: seconds a change waits for a row lock before its transaction is tried again
DBLockTimeout=10
# optional: oci_pconnect() instead of a new connection per request (enabled|disabled)
DBPersistentConnection=disabled
```

`DBHost`, `DBPort` and `DBSocket` are not used. The client talks AL32UTF8. Create the tables once, in the schema of
`DBUser` (a schema of its own for the cluster is what the `ezoracle` extension recommends too, for backups and so that
the site's transactions and the cluster's never mix):

```bash
sqlplus cluster/secret@db.example:1521/EXAMPLEPDB @kernel/sql/oracle/cluster_dfs_schema.sql
```

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfsoracle' );
define( 'CLUSTER_STORAGE_USER', 'cluster' );
define( 'CLUSTER_STORAGE_PASS', 'secret' );
define( 'CLUSTER_STORAGE_DB', 'db.example:1521/EXAMPLEPDB' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
```

Every web server mounts the same share (`MountPointPath`, an NFS export) and uses the same cluster schema.
`bin/php/clusterpurge.php` (the cronjob part `cluster_maintenance`) works with the kernel backend; the `ezoracle`
extension's `oracleclusterbinarypurge.php` is not needed for it.

## Coming from the ezoracle extension's backend

The `ezoracle` extension's `eZDFSFileHandlerOracleBackend` kept one table, `ezdfsfile`, without `name_trunk` and with
the cache files in it. To move to the kernel backend:

1. Put the site in maintenance (or stop the web servers).
2. Run the upgrade in the cluster schema:
   `sqlplus cluster/secret@db.example:1521/EXAMPLEPDB @kernel/sql/oracle/cluster_dfs_schema_upgrade.sql`. It adds
   `name_trunk` (filled with the name) and its index, drops the rows of cache files (they are generated again), and
   creates `ezdfsfile_cache`. The columns `filesize`, `mtime`, `expired` (`CHAR(1)` there, compared as numbers by the
   kernel backend) and `status` stay as they are.
3. Set `DBBackend=expDFSFileHandlerOracleBackend` and, in `config.cluster.php`, `CLUSTER_STORAGE_BACKEND` `dfsoracle`.
4. Clear the caches, then run `php bin/php/dfscleanup.php -S -B` and, after reading its result, with `-D`, to remove
   the cache files left on the share without a row.

## A one-node cluster with exp:cluster:enable

On a site whose database is Oracle (`DatabaseImplementation=ezoracle`), `./console exp:cluster:enable --apply` picks
the Oracle backend by itself (`--backend=oracle` names it): it makes the share, creates the tables of
`kernel/sql/oracle/cluster_dfs_schema.sql` in the site's own schema through the site's database driver, writes the
settings and `config.cluster.php` (`dfsoracle`, the site's `Database` as the connect string, its account) and moves
the stored files in. `exp:cluster:disable` takes it back.
[A one-node DFS cluster in one command](cluster-commands.md) describes both commands.

## Tests

| Test | What it checks | Needs |
|---|---|---|
| `expDFSFileHandlerOracleBackendTest` | the Oracle errors that are retried, LIKE patterns, prefixes and wildcards with `ESCAPE '\'`, rows as the other backends return them (`filesize` as `size`, `NULL` as `''`), name trunks, method parity with the MySQLi backend, Oracle SQL only and no bind named after a reserved word, the schema and upgrade files, the gateway | nothing |
| `expDFSOracleBackendIntegrationTest` | the parity checks of every backend (`expDFSBackendParityChecks`), plus the column types, case sensitive names, LIKE characters in names, the empty string as `NULL` in scopes, a missing table, a wrong password and the gateway serving metadata and byte ranges | an Oracle account with `CREATE TABLE` (`EXP_TEST_DFS_ORACLE_USER`, `EXP_TEST_DFS_ORACLE_PASSWORD`, `EXP_TEST_DFS_ORACLE_CONNECT`), `oci8`, and `pcntl`, `posix` for the checks with several processes |

```bash
EXP_TEST_DFS_ORACLE_USER=dfstest EXP_TEST_DFS_ORACLE_PASSWORD=secret EXP_TEST_DFS_ORACLE_CONNECT=127.0.0.1:1521/FREEPDB1 \
  php vendor/bin/phpunit tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSOracleBackendIntegrationTest.php
```

The integration test creates the two tables in the account's schema and drops them after the last test; a schema
that has them already is left alone (the class is skipped), so point it at an account of its own. The parity checks
are the ones the other backends pass, among them **four processes generating one cache file at the same moment** (one
generates, three wait, nobody fails) and **a row lock held by another connection**: a second session holds
`SELECT ... FOR UPDATE`, the store waits one second, is retried and succeeds once the lock is gone, or gives up after
`DBExecuteRetries` tries instead of hanging.

Measured on 20 November 2026 on alpha, against Oracle AI Database 26ai Free (23.26) in a throwaway account: the
integration test 30 of 30 (12 runs, one of them with a timing check missed while the server's load was high), the
unit test 10 of 10, on PHP 8.5; the real `eZDFSFileHandler` on the backend, end to end: the ten schema statements as
`exp:cluster:enable` runs them, a 2 MB file stored, fetched, copied, moved, deleted and purged, a view cache generated
once by `processCache()` and read by the next request, kept in `ezdfsfile_cache`, expired by its name trunk; and the
upgrade from the `ezoracle` extension's table (its old storage row read, stores and deletes on the `CHAR(1)` `expired`
column, the old cache row gone). The backend and the gateway pass `php -l` on PHP 7.4 to 8.5.

## Limits

- **Oracle 12.1 or later** (`OFFSET ... FETCH NEXT`). Older releases keep the `ezoracle` extension's backend.
- **A connection per request** unless `DBPersistentConnection=enabled`; with DRCP or many web servers, size the
  database's sessions for it.
- **The cluster schema should be the cluster's own**, as the `ezoracle` extension recommends: the site's transactions
  and the cluster's then never mix.
- **No `eZDFSFileHandlerTest` on Oracle yet**: the kernel's database test runner sets up MySQL and PostgreSQL databases
  for it; the Oracle backend is tested through the parity checks and the end to end run above instead.

## Related pages

- [Clustering in Exponential 6.0](clustering-overview.md)
- [Clustering on PostgreSQL: the DFS backend in the kernel](postgresql-dfs-cluster.md)
- [Clustering on MongoDB: the DFS backend in the kernel](mongodb-dfs-cluster.md)
- [Clustering on SQLite: the one-node DFS backend in the kernel](sqlite-dfs-cluster.md)
- [A one-node DFS cluster in one command](cluster-commands.md)
- [Clustering: DFS and its metadata database](../../help/database-administrators/clustering-dfs-and-the-database.md)
- [Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
