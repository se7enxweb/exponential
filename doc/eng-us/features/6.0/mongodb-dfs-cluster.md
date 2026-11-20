# Clustering on MongoDB: the DFS backend in the kernel

This page is for administrators who run a site on MongoDB and want its files in a DFS cluster, on one server or on
several, and for developers who maintain the cluster backends. From Exponential 6.0.15 the kernel's DFS cluster keeps
its file metadata in MongoDB as well as in MySQL/MariaDB, PostgreSQL and SQLite: the backend, its collections and its
file gateway are part of the kernel, with no extension and no PHP library to install.

## Contents

1. [Credit: where this backend comes from](#credit-where-this-backend-comes-from)
2. [What the kernel ships](#what-the-kernel-ships)
3. [How it locks without transactions](#how-it-locks-without-transactions)
4. [Stable example configuration](#stable-example-configuration)
5. [A one-node cluster with exp:cluster:enable](#a-one-node-cluster-with-expclusterenable)
6. [Several web servers: a replica set](#several-web-servers-a-replica-set)
7. [Tests](#tests)
8. [Limits](#limits)
9. [Related pages](#related-pages)

## Credit: where this backend comes from

The MongoDB backend follows the structure of the kernel's [PostgreSQL backend](postgresql-dfs-cluster.md), which is a
refactoring and integration of **ezpostgresqlcluster** by **eZ Systems AS** (GNU General Public License version 2,
[github.com/ezsystems/ezpostgresqlcluster](https://github.com/ezsystems/ezpostgresqlcluster)) and of 7x's fork of it,
[se7enxweb/ezpostgresqlcluster](https://github.com/se7enxweb/ezpostgresqlcluster). The design it keeps (one record per
clustered file, the `.generating` records that let one process generate a cache file while the others wait, the storage
collection and the cache collection) is the design of the eZ Systems cluster code. The MongoDB class is a new
implementation of the same contract, written method for method against the kernel's SQLite and MySQLi backends, under
the same license (GNU General Public License version 2, or any later version).

## What the kernel ships

| File | What it is |
|---|---|
| `kernel/private/classes/clusterfilehandlers/dfsbackends/mongodb.php` | `expDFSFileHandlerMongoDBBackend`, the metadata backend |
| `kernel/sql/mongodb/cluster_dfs_schema.json` | the collections `ezdfsfile` and `ezdfsfile_cache` and their indexes |
| `kernel/clustering/dfsmongodb.php` | the gateway `index_cluster.php` loads for `CLUSTER_STORAGE_BACKEND` `dfsmongodb` |

MongoDB 5.0 or later is needed (the backend marks files deleted with an update pipeline), with the PHP extension
`mongodb` (`pecl install mongodb`). The backend uses the extension's driver classes (`MongoDB\Driver\Manager`) only: the
library `mongodb/mongodb`, which the site's own database driver uses, is not needed for the cluster. PHP 7.4 is usable;
PHP 8.0 to 8.5 are clean. Tested on MongoDB 7.0 and 8.0 with extension 2.3.

Each clustered file is one document. Its `_id` is `name_hash`, the md5 of the path, as on every engine, so a path has
one document whatever happens; `size` and `mtime` are 64 bit integers (no 2038 limit). A deleted file keeps its
document with `mtime` = -ABS(`mtime`) and `expired` = 1 until it is purged, as on the SQL engines.

## How it locks without transactions

The backend uses no multi-document transactions, so it runs on a single `mongod` as well as on a replica set. Every
change of one file is one atomic write of one document, and the places where the SQL backends need a row lock use
what MongoDB guarantees for a single document instead:

- **One generation per cache file.** `_startCacheGeneration()` inserts the `.generating` document. Its `_id` is unique,
  so exactly one process succeeds; the others get the duplicate key error (11000) and are told to wait or to serve the
  stale file, as on the other engines.
- **Take-overs and timeout checks are compare and set.** A generation that ran out of time is taken over with one
  update conditioned on the `mtime` the process read; only one process can win it. `_checkCacheGenerationTimeout()`
  is the same update.
- **Writes of several documents are ordered** so that a process stopped half way leaves a state the next one recovers
  from. A file is written on the DFS before its document. A rename copies the file, writes the new name's document,
  and only then removes the old one. The end of a generation renames the file, then sets the size and time of the
  file's document in one upsert, then removes the `.generating` document. A purge removes the documents it found by
  `_id`, then their files.
- **Failures are retried, never waited on forever.** A network error, a primary that stepped down, or a write the
  server labels retryable is tried again up to `DBExecuteRetries` times, with a short pause that grows with each try
  (at most 0.2 s). Finding a server waits `DBLockTimeout` seconds at most (default 10).

Name searches use regular expressions instead of `LIKE`. They are case sensitive, as MySQL's `LIKE` on the binary
`name` column is, and a pattern that starts with a literal (`^var/site/cache/`) uses the name index. The `%` of a LIKE
pattern becomes `.*`, an underscore stays a literal (as in the MySQLi backend), and every character of regular
expressions in a file name (`.`, `*`, `?`, `[`, `(`, `+`, `$` ...) is a literal. A shell wildcard with alternatives
(`{a,b}.txt`) becomes one expression with `(?:a|b)`.

## Stable example configuration

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/var/www/vhosts/example.com/dfs
DBBackend=expDFSFileHandlerMongoDBBackend
DBHost=127.0.0.1
DBPort=27017
# the database; the account is authenticated against it, as the site's MongoDB driver does
DBName=example_cluster
DBUser=cluster
DBPassword=secret
# optional: seconds the selection of a server may take before the operation fails
DBLockTimeout=10
DBConnectRetries=3
DBExecuteRetries=20
```

`DBSocket` may name the server's Unix socket (`/tmp/mongodb-27017.sock`) instead of a host and port. The password goes
to the driver as an option, never inside a connection string, so no character of it can break one.

The collections and their indexes are created once. `exp:cluster:enable` does it (below); for a cluster database of
its own, one call does it from any script run with the kernel (`php bin/php/ezexec.php <script> --allow-root-user` or a
command): `expDFSFileHandlerMongoDBBackend::createSchema( $manager, 'example_cluster' )`, where `$manager` is a
`MongoDB\Driver\Manager` connected with an account that may create collections. It reads
`kernel/sql/mongodb/cluster_dfs_schema.json` and leaves existing collections and indexes as they are.

```php
<?php
// config.cluster.php
define( 'CLUSTER_STORAGE_BACKEND', 'dfsmongodb' );
define( 'CLUSTER_STORAGE_HOST', '127.0.0.1' );
define( 'CLUSTER_STORAGE_PORT', 27017 );
define( 'CLUSTER_STORAGE_USER', 'cluster' );
define( 'CLUSTER_STORAGE_PASS', 'secret' );
define( 'CLUSTER_STORAGE_DB', 'example_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/var/www/vhosts/example.com/dfs' );
```

The site's own database and the cluster database are separate choices: a site on MySQL can keep its cluster metadata
in MongoDB, and a site on MongoDB can keep it in its own database (the collections `ezdfsfile` and `ezdfsfile_cache`
sit next to the site's).

## A one-node cluster with exp:cluster:enable

On a site whose database is MongoDB (`DatabaseImplementation=mongodb`), `./console exp:cluster:enable --apply` picks the
MongoDB backend by itself (`--backend=mongodb` names it): it makes the share, creates the two collections and their
indexes in the site's own database, writes the settings and `config.cluster.php` (`dfsmongodb`, the site's server,
account and database) and moves the stored files in. A server without authentication (no `User`) is accepted.
`exp:cluster:disable` takes it back. [A one-node DFS cluster in one command](cluster-commands.md) describes both
commands.

## Several web servers: a replica set

For several web servers, give every server the same share (`MountPointPath`, an NFS export) and the same cluster
database. A production MongoDB for that is a replica set; put its whole connection string in `DBHost` (and in
`CLUSTER_STORAGE_HOST` for the gateway):

```ini
DBHost=mongodb://db1.example,db2.example,db3.example/?replicaSet=rs0&authSource=admin
DBName=example_cluster
DBUser=cluster
DBPassword=secret
```

A string that starts with `mongodb://` or `mongodb+srv://` is used as it is; `DBPort` is then ignored, and the account
of `DBUser`/`DBPassword` is added as driver options unless the string carries one. On a replica set the driver also
retries a write once by itself when the primary changes, and the backend's own retries cover the rest of a failover.

## Tests

| Test | What it checks | Needs |
|---|---|---|
| `expDFSFileHandlerMongoDBBackendTest` | LIKE patterns, prefixes and wildcards as regular expressions (with every regular expression character in names a literal), the connection string and its options, the errors that are retried, name trunks, method parity with the MySQLi backend, no SQL and no library in the code, the schema file, the gateway | nothing (the retry checks need the extension) |
| `expDFSMongoDBBackendIntegrationTest` | the parity checks of every backend (`expDFSBackendParityChecks`), plus the schema's indexes (and that creating it twice changes nothing), 64 bit times and `_id` = `name_hash`, the index use of prefix searches, case sensitive names, regular expression characters in names, the `.generating` document as the lock, a server that does not answer, and the gateway serving metadata and byte ranges | a MongoDB server (`EXP_TEST_DFS_MONGODB_URI`), the extension, and `pcntl`, `posix` for the checks with several processes |

```bash
docker run -d --rm --name dfs-mongo -p 127.0.0.1:27711:27017 mongo:8.0
EXP_TEST_DFS_MONGODB_URI=mongodb://127.0.0.1:27711 php vendor/bin/phpunit \
  tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSMongoDBBackendIntegrationTest.php
```

The integration test makes a database of its own (`dfsparity_<pid>_<random>`) and drops it after the last test. The
parity checks are the ones the other backends pass, among them **four processes generating one cache file at the same
moment** (one generates, three wait, nobody fails). The check of a lock held by another connection is skipped by
design: MongoDB has no lock a client holds between two operations outside a transaction, so there is nothing for a
store to wait on.

Measured on 20 November 2026 on alpha: the integration test 30 of 31 (the lock check skipped by design) on MongoDB 8.0
and on MongoDB 7.0, the unit test 10 of 10, all on PHP 8.5 with extension 2.3.3; and the real `eZDFSFileHandler` on
the backend, end to end: a 2 MB file stored, fetched, copied, moved, deleted and purged, a view cache generated once by
`processCache()` and read by the next request, expired by its name trunk, and the collections step of
`exp:cluster:enable` (created, counted, documents of another origin refused, our own emptied). The backend and the
gateway pass `php -l` on PHP 7.4 to 8.5.

## Limits

- **No multi-document transactions.** A process that stops in the middle of a rename or the end of a generation
  leaves both names or a `.generating` document; the next process takes the generation over after
  `CacheGenerationTimeout`, and `bin/php/dfscleanup.php` finds files on the DFS without a document.
- **No row lock to wait on**, so the bounded lock wait check of the parity suite does not apply; a write that fails
  for a network or failover reason is retried instead.
- **Writes are only as durable as the write concern.** The backend uses the server's default; on a replica set that
  is `majority` from MongoDB 5.0 on. Do not lower it for the cluster database.
- **No `eZDFSFileHandlerTest` on MongoDB yet**: the kernel's database test runner sets up MySQL and PostgreSQL databases
  for it; the MongoDB backend is tested through the parity checks and the end to end run above instead.

## Related pages

- [Clustering on PostgreSQL: the DFS backend in the kernel](postgresql-dfs-cluster.md)
- [Clustering on SQLite: the one-node DFS backend in the kernel](sqlite-dfs-cluster.md)
- [A one-node DFS cluster in one command](cluster-commands.md)
- [MongoDB as the database](mongodb-database-support.md)
- [Clustering: DFS and its metadata database](../../help/database-administrators/clustering-dfs-and-the-database.md)
- [Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
