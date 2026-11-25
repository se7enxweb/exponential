# Clustering on SQL Server: the DFS backend in the kernel

This page is for administrators who run a site on Microsoft SQL Server or Azure SQL and want its files in a DFS
cluster on several web servers, and for developers who maintain the cluster backends. From Exponential 6.0.15 the
kernel's DFS cluster keeps its file metadata in SQL Server as well as in MySQL/MariaDB, PostgreSQL, Oracle, MongoDB and
SQLite: the backend, its tables and its file gateway are part of the kernel and use the PHP layer of the kernel's
[SQL Server driver](sqlserver-database-driver.md) (`sqlsrv`, `pdo_sqlsrv`, PDO_ODBC or `pdo_dblib`).

## Contents

1. [Credit: where this backend comes from](#credit-where-this-backend-comes-from)
2. [What the kernel ships](#what-the-kernel-ships)
3. [How it locks: bounded waits on row locks](#how-it-locks-bounded-waits-on-row-locks)
4. [Stable example configuration](#stable-example-configuration)
5. [A one-node cluster with exp:cluster:enable](#a-one-node-cluster-with-expclusterenable)
6. [Tests](#tests)
7. [Limits](#limits)
8. [Related pages](#related-pages)

## Credit: where this backend comes from

The SQL Server backend is built after the kernel's [Oracle backend](oracle-dfs-cluster.md), method for method, which
follows the kernel's [PostgreSQL backend](postgresql-dfs-cluster.md): a refactoring and integration of
**ezpostgresqlcluster** by **eZ Systems AS** (GNU General Public License version 2,
[github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) and of 7x's fork of it,
[se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). The design it keeps (one row per
clustered file, the `.generating` rows that let one process generate a cache file while the others wait, the storage
table and the cache table) is the design of the eZ Systems cluster code. The SQL Server class is a new implementation
of the same contract, under the same license (GNU General Public License version 2, or any later version). No code of
the old xrowmssql extension is in it.

## What the kernel ships

| File | What it is |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/sqlserver.php` | `expDFSFileHandlerSQLServerBackend`, the metadata backend |
| `kernel/private/classes/clusterfilehandlers/dfsbackends/sqlserverresult.php` | `expDFSSQLServerResult`, the rows of one statement |
| `kernel/sql/sqlserver/cluster_dfs_schema.sql` | the tables `ezdfsfile` and `ezdfsfile_cache` |
| `kernel/clustering/dfssqlserver.php` | the gateway `index_cluster.php` loads for `CLUSTER_STORAGE_BACKEND` `dfssqlserver` |

SQL Server 2017 or later, or Azure SQL Database or Managed Instance, is needed, with one of the PHP layers of the
driver (on Linux over Microsoft's ODBC Driver 17 or 18). PHP 7.4 is usable; PHP 8.0 to 8.5 are clean. Tested on SQL
Server 2022 (Linux container), and in CI on 2017, 2019, 2022 and 2025.

What SQL Server changes in the tables:

- **Names are `NVARCHAR(830)` in the binary collation `Latin1_General_100_BIN2`.** Case sensitive, as MySQL's binary
  `LIKE` compares them, and short enough that the name index, with the 34 bytes of the clustered `name_hash` a
  non-unique index carries, stays within SQL Server's 1,700 byte key. A file name longer than 830 characters cannot be
  stored (the other engines allow longer ones).
- **`name_hash` is `VARCHAR(34)` in a Windows collation as well.** The driver binds every value as `NVARCHAR`; only a
  Windows collation lets SQL Server seek a `VARCHAR` key with an `NVARCHAR` value. With the server's default English
  collation (`SQL_Latin1_General_CP1_CI_AS`) every lookup would read the whole table.
- **The size column is `filesize`**, as in the Oracle table; the backend returns it as `size`, like every other
  backend. Sizes and times are `BIGINT` (no 2038 limit).

## How it locks: bounded waits on row locks

- **Every session sets `LOCK_TIMEOUT`** to `[eZDFSClusteringSettings] DBLockTimeout` (seconds, default 10). A change
  first locks its rows with `WITH (UPDLOCK, ROWLOCK)`. A wait that ran out (error 1222), a deadlock (1205), a snapshot
  conflict (3960) and the duplicate key of two processes inserting the same new row at once (2627, 2601) are tried
  again by the backend, up to `DBExecuteRetries` tries, with a short pause that grows with each try (at most 0.2 s).
  After the last try the store fails instead of hanging.
- **Stores are `MERGE ... WITH (HOLDLOCK)`**: one statement inserts the row or updates it, without a gap between the
  check and the insert.
- **One generation per cache file.** `_startCacheGeneration()` inserts the `.generating` row with a plain `INSERT`: its
  `name_hash` is the primary key, so one process gets the row and the others get error 2627 and are told to wait or to
  serve the stale file. A take-over after the timeout is one `UPDATE` conditioned on the `mtime` read.
- **Purges skip locked rows** with `READPAST`, so two purges never wait for each other.
- **Row counts come back.** The driver's session setup switches `NOCOUNT` on; the cluster connection switches it off
  again, because the cache generation and the purge decide on the number of rows a statement changed.

Name searches use `LIKE ... ESCAPE '\'`. The `%` of a LIKE pattern stays a wildcard, an underscore is a literal (as in
the MySQLi backend), and `%`, `_`, `[` (which starts a character class in SQL Server) and `\` in a file name are
literals. A shell wildcard with alternatives (`{a,b}.txt`) becomes one `LIKE` pattern per alternative. Lists use `TOP`
and `OFFSET ... FETCH NEXT`.

## Stable example configuration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerSQLServerBackend
# a host and port, or a named instance as host\instance (no port)
DBHost=sql.example
DBPort=1433
DBName=cluster
DBUser=cluster
DBPassword=secret
DBConnectRetries=3
DBExecuteRetries=20
# optional: seconds a change waits for a row lock before its transaction is tried again
DBLockTimeout=10
```

The PHP layer and the encryption of the connection come from `site.ini [SQLServerSettings]`, as for the site's
database (`Layer`, `ODBCDriver`, `Encrypt`, `TrustServerCertificate`, `MultiSubnetFailover`). Put that group in
`settings/override/site.ini.append.php`: the cluster connects before a siteaccess is matched, so a value only in a
siteaccess is not seen by it. The installer writes it there for a site on SQL Server. Create the tables once, in the
cluster's database:

```bash
sqlcmd -S sql.example,1433 -U cluster -d cluster -C -i kernel/sql/sqlserver/cluster_dfs_schema.sql
```

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlserver' );
define( 'CLUSTER_STORAGE_HOST', 'sql.example' );
define( 'CLUSTER_STORAGE_PORT', 1433 );
define( 'CLUSTER_STORAGE_USER', 'cluster' );
define( 'CLUSTER_STORAGE_PASS', 'secret' );
define( 'CLUSTER_STORAGE_DB', 'cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
// optional, as site.ini [SQLServerSettings]: auto, sqlsrv, pdo_sqlsrv, pdo_odbc or pdo_dblib
define( 'CLUSTER_SQLSERVER_LAYER', 'auto' );
define( 'CLUSTER_SQLSERVER_ENCRYPT', 'yes' );
// only for a self-signed certificate on a network you trust
define( 'CLUSTER_SQLSERVER_TRUST_SERVER_CERTIFICATE', false );
```

The gateway loads the driver's transport classes itself (`index_cluster.php` has no autoloader) and masks the password
in its errors. Every web server mounts the same share (`MountPointPath`, an NFS export) and uses the same cluster
database. `bin/php/clusterpurge.php` (the cronjob part `cluster_maintenance`) purges through the backend's
`expiredFilesList()` and `_purge()`, which the parity checks test.

## A one-node cluster with exp:cluster:enable

On a site whose database is SQL Server (`DatabaseImplementation=sqlserver`), `./console exp:cluster:enable --apply`
picks the SQL Server backend by itself (`--backend=sqlserver` names it): it makes the share, creates the tables of
`kernel/sql/sqlserver/cluster_dfs_schema.sql` in the site's own database through the site's database driver, writes
the settings and `config.cluster.php` (`dfssqlserver`, the site's server, port and account, and the layer and
encryption of `[SQLServerSettings]` as the `CLUSTER_SQLSERVER_*` constants) and moves the stored files in.
`exp:cluster:disable` takes it back. [A one-node DFS cluster in one command](cluster-commands.md) describes both
commands.

## Tests

| Test | What it checks | Needs |
|---|---|---|
| `expDFSFileHandlerSQLServerBackendTest` | the SQL Server errors that are retried, LIKE patterns, prefixes and wildcards with `ESCAPE '\'` (`[` a literal), rows as the other backends return them (`filesize` as `size`, `NULL` as `''`), `:name` parameters as `?`, the result class, name trunks, method parity with the MySQLi backend, SQL Server SQL only, the schema file, the gateway and its connection settings | nothing |
| `expDFSSQLServerBackendIntegrationTest` | the parity checks of every backend (`expDFSBackendParityChecks`), plus the column types and collations, case sensitive names, LIKE characters in names, Unicode names, a missing table, a wrong password (masked in the error) and the gateway serving metadata and byte ranges | a SQL Server login that may create a database (`EXP_TEST_SQLSERVER_HOST`, `_PORT`, `_USER`, `_PASSWORD` or `_PASSWORD_FILE`, `_LAYER`), and `pcntl`, `posix` for the checks with several processes |
| `expClusterSetupTest` | the plan of `exp:cluster:enable` on SQL Server: engine, gateway, port 1433, the INI block and the `CLUSTER_SQLSERVER_*` constants | nothing |

```bash
EXP_TEST_SQLSERVER_HOST=127.0.0.1 EXP_TEST_SQLSERVER_PORT=1433 EXP_TEST_SQLSERVER_PASSWORD=secret EXP_TEST_SQLSERVER_LAYER=pdo_odbc \
  php vendor/bin/phpunit tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSSQLServerBackendIntegrationTest.php
```

The integration test makes the database `exp_dfs_test` (`EXP_TEST_DFS_SQLSERVER_DATABASE` names another) on first use,
creates the two tables and drops them after the last test; a database that has them already is left alone (the class
is skipped). The parity checks are the ones the other backends pass, among them **four processes generating one cache
file at the same moment** (one generates, three wait, nobody fails) and **a row lock held by another connection**: a
second session holds `WITH (UPDLOCK, ROWLOCK)` in an open transaction, the store waits one second, is retried and
succeeds once the lock is gone, or gives up after `DBExecuteRetries` tries instead of hanging.

**The two checks with processes in parallel run on PDO_ODBC.** `sqlsrv` and `pdo_sqlsrv` keep their ODBC environment
for the life of the process, and with it the socket of a connection the process has closed (it stays established
after `sqlsrv_close()`). A child forked after such a close gets a driver whose new connections the server resets (TCP
Provider error 0x2746, and 0x20 in the parent afterwards). PDO_ODBC frees its environment with the connection and forks
cleanly. On `sqlsrv` and `pdo_sqlsrv` the two checks are skipped with that reason; CI runs them on its PDO_ODBC job.

Measured on 25 November 2026 on cluster, against SQL Server 2022 in a Linux container: the integration test 32 of 32 on
PDO_ODBC and 30 of 32 with the two skips on `sqlsrv` and `pdo_sqlsrv` (PHP 8.5), the unit test 13 of 13. End to end on
a site installed on SQL Server with `install.php --db=sqlserver`: `exp:cluster:enable --apply` made the tables and the
settings and moved 312 files in; the real `eZDFSFileHandler` on the backend stored, fetched, copied, moved, deleted and
purged a 2 MB file, generated a view cache once with `processCache()` for the next request to read, and expired it by
its name trunk.

## Limits

- **SQL Server 2017 or later** (and Azure SQL). Azure SQL Edge is retired by Microsoft and not supported.
- **Names up to 830 characters**, for the index key (above).
- **A process that forks after closing a connection** cannot use `sqlsrv` or `pdo_sqlsrv` in its children (above). A
  web server's PHP workers do not do that; a script that forks workers after it used the cluster connects in each child
  only, or uses PDO_ODBC.
- **No persistent connection yet** (`DBPersistentConnection` is not read): one connection per request.
- **No `eZDFSFileHandlerTest` on SQL Server yet**: the kernel's database test runner sets up MySQL and PostgreSQL
  databases for it; the backend is tested through the parity checks and the end to end run above instead.

## Related pages

- [Microsoft SQL Server driver](sqlserver-database-driver.md)
- [Clustering in Exponential 6.0](clustering-overview.md)
- [Clustering on Oracle: the DFS backend in the kernel](oracle-dfs-cluster.md)
- [Clustering on PostgreSQL: the DFS backend in the kernel](postgresql-dfs-cluster.md)
- [A one-node DFS cluster in one command](cluster-commands.md)
- [Clustering: DFS and its metadata database](../../help/database-administrators/clustering-dfs-and-the-database.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
