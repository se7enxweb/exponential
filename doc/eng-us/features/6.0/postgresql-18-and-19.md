# PostgreSQL 18 and 19

This page is for administrators and database administrators who run Exponential on PostgreSQL 18 or 19, plan to
upgrade an existing installation to one of them, or run a cluster of web servers on them. From Exponential 6.0.15
PostgreSQL 13 to 19 are supported and tested; 18 and 19, the two newest major releases, are covered by every database
test suite, by the cluster backend tests and by production upgrades. This page says how support is tested, which
client programs a site needs, how to upgrade, how to run a cluster on 18 and 19 with the exact settings, and what the
known limits are.

## Contents

1. [In short](#in-short)
2. [Supported versions and how support is tested](#supported-versions-and-how-support-is-tested)
3. [Client versions: PHP, libpq, pg_dump and psql](#client-versions-php-libpq-pg_dump-and-psql)
4. [A new installation on 18 or 19](#a-new-installation-on-18-or-19)
5. [Upgrading an existing installation](#upgrading-an-existing-installation)
6. [Running a cluster on 18 and 19](#running-a-cluster-on-18-and-19)
7. [Proof from production](#proof-from-production)
8. [Known limits](#known-limits)
9. [Related pages](#related-pages)

## In short

| | |
|---|---|
| Supported | PostgreSQL 13, 14, 15, 16, 17, 18 and 19, for the site's database and for the DFS cluster metadata. 13 is the minimum (`pgcrypto` is a trusted extension from 13 on). |
| Tested on every push | The database test suites on PostgreSQL 13, 15, 18 and 19; the DFS cluster backend on 13 to 19. |
| Checked by hand on 18.7 and 19.0 | Install, the upgrade backup, the database stage of the upgrade, a restore drill, the schema round trip, the MySQL to PostgreSQL conversion, the DFS cluster backend, the read replica with a streaming standby and the SQL query cache. |
| Client rule | `pg_dump` and `psql` must be at least as new as the server. Exponential finds such a client itself; `upgrade.ini [UpgradeSettings] PostgreSQLClientDir` names one. |
| PHP | The extensions `pgsql` (the site's driver `eZPostgreSQLDB`) and `pdo_pgsql` (the DFS cluster backend and its gateway) on every web server. |
| Cluster | The kernel's DFS backend `expDFSFileHandlerPostgresqlBackend`, a primary and a streaming standby, and read replicas through `[DatabaseSettings] UseSlaveServer`. |

## Supported versions and how support is tested

Exponential needs nothing from PostgreSQL that a newer major release takes away: the driver uses plain SQL, sequences
named `<table>_<column>_seq` and `digest()` from `pgcrypto`; the DFS cluster backend uses `INSERT ... ON CONFLICT`,
`FOR UPDATE SKIP LOCKED`, `lock_timeout` and `text_pattern_ops` indexes, all there from 13 on. Support is not assumed
from that, it is tested:

**Continuous integration** (`.github/workflows/phpunit.yml`, on every push):

| Job | PostgreSQL | What runs |
|---|---|---|
| Database tests (postgresql 13, 15, 18 and 19, PHP 8.3) | `postgres:13`, `postgres:15`, `postgres:18`, `postgres:19` | Every database test suite (`tests/runtests.php --dsn ... --db-per-test`) against a fresh database with `pgcrypto`. 13 is the oldest supported release, 15 the one the suites ran on alone before, 18 and 19 the two newest. Each leg installs the PGDG client of its own server, because the caption and backup checks dump the test database and `pg_dump` refuses a newer server. |
| DFS cluster backends (PostgreSQL 13 to 19, PHP 8.3; 18 also on PHP 8.5) | every major from 13 to 19 | The parity checks every cluster backend passes (`expDFSPostgresqlBackendIntegrationTest`), the gateway, the index use of prefix searches and the upgrade of tables made by the old extension. |

**Checked by hand on 18.7 and 19.0**, beyond the suites: a new install, `exp:upgrade:backup`, the database stage of
`exp:upgrade`, a restore drill (dump, restore into a new database, compare), the schema round trip
(`ezsqldumpschema.php` and `ezsqlinsertschema.php`), the conversion of a MySQL database to PostgreSQL (byte for byte
the same output as on 13), the DFS cluster backend with its gateway, the read replica on a streaming standby with
synchronous apply, and the SQL query cache in shared mode.

The rest of the support matrix, for every engine, is in [Database support](../../help/database-support.md).

## Client versions: PHP, libpq, pg_dump and psql

Three kinds of client talk to the server. Only the third has a strict version rule.

| Client | Who uses it | Version rule |
|---|---|---|
| PHP `pgsql` (libpq) | the site's driver `eZPostgreSQLDB` (`pg_connect`, `pg_pconnect`) | Any libpq your PHP build links works with a newer server. libpq 10 or later is needed for `scram-sha-256` passwords, which PostgreSQL 14 and later use by default. |
| PHP `pdo_pgsql` (libpq) | the DFS cluster backend and its gateway `index_cluster.php` | The same as `pgsql`. |
| `pg_dump`, `pg_restore`, `psql` | the upgrade backup, the caption check, the rollback, the migration diff and `ezconvertdbcharset.php` | **At least the server's major version.** `pg_dump` refuses a newer server ("aborting because of server version mismatch"). |

A web server that kept the PostgreSQL 13 client after its database server moved to 18 can still serve the site, but it
cannot make a backup with that client, and `exp:upgrade --apply` would stop at its caption before its first write
("the caption failed, nothing was changed"). From 6.0.15 Exponential picks the client itself
(`expUpgradeBackup::postgresqlTool()`), taking the first of these whose major version is at least the server's:

1. `settings/upgrade.ini [UpgradeSettings] PostgreSQLClientDir`, a `bin` directory, when it is set;
2. the program on `PATH`;
3. the versioned installations, newest major first: `/usr/pgsql-<major>/bin` (PGDG packages for RHEL and its
   rebuilds), `/usr/lib/postgresql/<major>/bin` (Debian and Ubuntu), `/opt/exp-pg<major>/current/bin`,
   `/usr/local/pgsql/bin`.

Without one new enough it uses the newest it found, and `pg_dump` names the mismatch itself. To pin the client:

```ini
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
PostgreSQLClientDir=/usr/pgsql-18/bin
```

Install the client of the server's major version next to the old one; you do not have to remove the old one:

```bash
# RHEL, Rocky, AlmaLinux with the PGDG repository
dnf install postgresql18
# Debian and Ubuntu with the PGDG repository
apt install postgresql-client-18
```

Then check it:

```bash
./console exp:upgrade:backup                 # dumps the database with the client it chose
./console exp:upgrade:backup --format=json   # the same, as machine output
```

## A new installation on 18 or 19

Nothing differs from earlier releases; [PostgreSQL setup](../../help/database-administrators/postgresql-setup.md) and
[9.4 PostgreSQL](../../install/09-databases.md#94-postgresql) have every step. In short:

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
sudo -u postgres psql -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

```ini
# settings/override/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential
UsePersistentConnection=disabled
```

The site's user must **own** the database: from PostgreSQL 15 on, other users may not create tables in its `public`
schema. `pgcrypto` comes with the server's contrib package (`postgresql18-contrib` on PGDG RPM systems; it is part of
the server package on Debian). The installer creates it when it can; the setup wizard says "The 'digest' function is
not available in your database" when it cannot.

`Server` is passed to libpq as `host`, so a path is a socket directory: `Server=/run/postgresql` connects through the
Unix socket when the database runs on the web server itself.

## Upgrading an existing installation

A major upgrade of PostgreSQL (13 to 18, 16 to 19, and so on) changes the server, not Exponential: the site's schema
and data move as they are, and no Exponential upgrade step is needed for it. Plan it like any database change.

### Before you start

1. **Install the new server's client on every web server** (see [Client versions](#client-versions-php-libpq-pg_dump-and-psql))
   and set `PostgreSQLClientDir` if it is not on the standard paths.
2. **Take a backup with the tools you will restore with:** `./console exp:upgrade:backup` (a `pg_dump` into
   `var/backup/upgrade/<time>/`, and a copy of `settings/override`), or your own `pg_dump -Fc` with the new client.
3. **List what the database uses:**

   ```sql
   SELECT extname, extversion FROM pg_extension;   -- pgcrypto, and plpgsql; anything else needs the new server's package too
   SELECT count(*) FROM pg_tables WHERE schemaname = 'public';
   ```

   Keep a row count of every table, so you can compare after the upgrade (a script that loops over `pg_tables` and
   runs `SELECT count(*)` is enough).
4. **Put the site into maintenance**, so no editor writes during the move:
   `php bin/php/maintenance.php on --message="Database upgrade" --allow-root-user`.

### Path 1: pg_upgrade (fast, for large databases)

`pg_upgrade` moves the data files of the old cluster to the new major version in minutes, also for databases of many
gigabytes. Follow PostgreSQL's [pg_upgrade documentation](https://www.postgresql.org/docs/current/pgupgrade.html); the
points that matter for an Exponential site:

- Install the new server **and its contrib package** first: `pg_upgrade` refuses to run when an extension the old
  cluster uses (`pgcrypto`) has no library on the new server.
- **Data checksums.** From PostgreSQL 18 on, `initdb` turns data checksums on by default, and `pg_upgrade` needs both
  clusters to match. When the old cluster has no checksums, initialise the new one with `--no-data-checksums` (or turn
  checksums on in the old cluster first with `pg_checksums --enable` while it is stopped).
- Run `pg_upgrade --check` first; it changes nothing and lists what would stop the upgrade.
- After the upgrade, gather statistics: PostgreSQL 18 and later keep the planner statistics through `pg_upgrade`, but
  not extended statistics, and earlier target versions keep none. `vacuumdb --all --analyze-in-stages` does both.
- Update the extensions to the new server's version: `ALTER EXTENSION pgcrypto UPDATE;` in the site's database.
- **A streaming standby** cannot follow a `pg_upgrade` of its primary on its own. Either rebuild it from the upgraded
  primary with `pg_basebackup`, or use the `rsync` procedure in PostgreSQL's `pg_upgrade` documentation. Exponential
  reads the primary while a replica is missing (see [Read replicas](#read-replicas-on-18-and-19)), so the site runs
  while the standby is rebuilt.

### Path 2: dump and restore (simple, for small and medium databases)

Dump with the **new** client against the old server, restore into the new server:

```bash
/usr/pgsql-18/bin/pg_dump -h old-db -U exponential -Fc exponential > exponential.dump
/usr/pgsql-18/bin/createdb -h new-db -U postgres --owner=exponential --encoding=UTF8 --template=template0 exponential
/usr/pgsql-18/bin/psql -h new-db -U postgres -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
/usr/pgsql-18/bin/pg_restore -h new-db -U exponential -d exponential --no-owner exponential.dump
/usr/pgsql-18/bin/vacuumdb -h new-db -U exponential --analyze-in-stages exponential
```

The dump carries the sequences' current values, so nothing needs correcting after a full restore. If you restored
only some tables, correct their sequences as
[Operating PostgreSQL](../../help/database-administrators/postgresql-operations.md#correcting-sequences) shows.

### Switching the site and checking it

1. Point `[DatabaseSettings] Server` and `Port` (and, in a cluster, `[eZDFSClusteringSettings] DBHost` and `DBPort`
   and the `CLUSTER_STORAGE_HOST`/`CLUSTER_STORAGE_PORT` of `config.cluster.php`) at the new server, unless the new
   server took over the old address.
2. Clear the INI cache and deploy: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` and
   `./console exp:velocity deploy --allow-root-user`.
3. Check:

   | Check | How |
   |---|---|
   | The site answers | `curl -s https://<your-host>/ezinfo/is_alive` prints `Exponential is alive` |
   | The same tables and rows | compare the counts you took before; on 18 and 19 every table must match |
   | Extensions | `SELECT extname, extversion FROM pg_extension;` shows `pgcrypto` at the new server's version |
   | Backups work | `./console exp:upgrade:backup` finishes and names the new client |
   | Upgrade steps | `./console exp:upgrade:status` shows nothing pending that was not pending before |
   | Cluster metadata | `SELECT count(*) FROM ezdfsfile;` in the cluster database matches, and an image of the site loads through `index_cluster.php` |
   | Password hashes | `SELECT rolname FROM pg_authid WHERE rolpassword LIKE 'md5%';` lists no Exponential login (see [Known limits](#known-limits)) |

4. Take the site out of maintenance: `php bin/php/maintenance.php off --allow-root-user`.

The SQL query cache keeps the time of the last write of every table, not the rows themselves; after a restore,
`php bin/php/ezcache.php --clear-id=querycache --allow-root-user` makes sure no result from before the move is used.

### Moving from MySQL or MariaDB to PostgreSQL 18 or 19

The schema tools convert a MySQL or MariaDB database into PostgreSQL through the engine-neutral `.dba` format
([Moving a site to another engine](../../help/database-administrators/moving-between-engines.md)). Three faults that
stopped such a conversion are fixed in 6.0.15:

- `ezsqldumpschema.php --output-types=data` quotes table and column names on MySQL, so a column named like a reserved
  word (`interval`, `group`) no longer stops the dump.
- The PostgreSQL schema handler maps `timestamp`, `datetime`, `date` and `time` columns of extension and third-party
  tables (they failed with "Unhandled column type: timestamp").
- A `NOT NULL` number or date column without a default gets no `DEFAULT ''`, and the `INSERT` column lists for
  PostgreSQL quote reserved words, so tables with such columns are created and filled.

The conversion gives the same output on 13, 18 and 19.

## Running a cluster on 18 and 19

A cluster of Exponential web servers uses PostgreSQL in up to three roles. They are separate choices and can be the
same server or different ones:

| Role | Settings | Needed for |
|---|---|---|
| The site's database (primary) | `site.ini [DatabaseSettings]` | every installation |
| The DFS cluster metadata | `file.ini [eZDFSClusteringSettings]` and `config.cluster.php` | several web servers sharing stored files and caches |
| Read replicas (standbys) | `site.ini [DatabaseSettings] UseSlaveServer` and `SlaveServerArray[]` | reading anonymous pages from a standby close to a web server |

The overview of every part of a cluster is [Clustering in Exponential 6.0](clustering-overview.md).

### The DFS backend on PostgreSQL

The kernel's backend `expDFSFileHandlerPostgresqlBackend` keeps the index of every stored and cache file in the tables
`ezdfsfile` and `ezdfsfile_cache`; the files themselves are on a share every web server mounts. In
`settings/override/file.ini.append.php`:

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/mnt/exp-dfs
DBBackend=expDFSFileHandlerPostgresqlBackend
DBHost=cluster-db.internal
DBPort=5432
# or, on the database server itself, the directory of the Unix socket (it takes the place of DBHost):
# DBSocket=/run/postgresql
DBName=exp_cluster
DBUser=exp_cluster
DBPassword=<the password>
# connection attempts, and tries of a statement that hit a deadlock or a lock wait
DBConnectRetries=3
DBExecuteRetries=20
# seconds a statement waits for a row lock before its transaction is tried again (default 10)
DBLockTimeout=10
# keep one connection per PHP worker between requests (default: off)
DBPersistentConnection=enabled
```

And in `config.cluster.php`, for the gateway `index_cluster.php` that serves stored images and the public cache:

```php
<?php
define( 'CLUSTER_STORAGE_BACKEND', 'dfspostgresql' );
define( 'CLUSTER_STORAGE_HOST', 'cluster-db.internal' );
define( 'CLUSTER_STORAGE_PORT', 5432 );
define( 'CLUSTER_STORAGE_USER', 'exp_cluster' );
define( 'CLUSTER_STORAGE_PASS', '<the password>' );
define( 'CLUSTER_STORAGE_DB', 'exp_cluster' );
define( 'CLUSTER_STORAGE_CHARSET', 'utf8' );
define( 'CLUSTER_MOUNT_POINT_PATH', '/mnt/exp-dfs' );
```

Tables: `psql -v ON_ERROR_STOP=1 -h cluster-db.internal -U exp_cluster -d exp_cluster -f kernel/sql/postgresql/cluster_dfs_schema.sql`.
On a single server, `./console exp:cluster:enable --apply` does all of it, with the tables in the site's own database
([A one-node DFS cluster in one command](cluster-commands.md)).

**One round trip per statement.** The backend binds its values in PHP (PDO emulated prepares; the values are only
integers, strings and nulls), so each statement is one round trip to the server instead of three (prepare, execute
and the `DEALLOCATE`). On a cluster whose database is on another machine this cut the time of every cache file check
to a third. Nothing needs to be set for it.

**Persistent connections.** `DBPersistentConnection=enabled` keeps one connection per PHP worker between requests and
saves the login round trips of a new connection (with `scram-sha-256` these are several). Count first: every PHP-FPM
child and every Velocity worker of every web server holds one connection, to the cluster database **and**, with
`[DatabaseSettings] UsePersistentConnection=enabled`, one to the site's database. The sum must stay below the server's
`max_connections`, with room for cronjobs, the console and your own sessions. PDO rolls back an open transaction when
a request ends.

**Bounded waits.** Every connection sets `lock_timeout` from `DBLockTimeout`, so no statement waits for a row lock
forever; a deadlock (`40P01`), a lock not granted in time (`55P03`) and a serialization failure (`40001`) are retried up
to `DBExecuteRetries` times. A cluster node never keeps or serves a short or failed copy of a file from the share: the
size is checked on the temporary copy before it is renamed into place, and a fetch that fails on every try removes
the old local copy.

Everything else (credit, what was fixed against the old extension, the move from `ezpostgresqlcluster`, the tests) is
on [Clustering on PostgreSQL](postgresql-dfs-cluster.md).

### A primary and a streaming standby

The usual production shape on 18 and 19 is one primary and at least one physical standby fed by streaming
replication. Exponential writes to the primary only; the standby is a hot spare and, with the settings below, a read
replica for anonymous pages.

- **One address for the primary.** Every web server reaches the primary through an address that moves with it on a
  failover (a virtual IP, a DNS name with a short TTL, or a proxy in front of the database). Exponential's settings
  name that address, so a failover changes no setting.
- **The cluster metadata** lives on the primary too. The DFS backend always reads and writes the primary, never a
  replica; a standby that is promoted takes its tables along.
- **Connection poolers.** The DFS backend sets `client_encoding` and `lock_timeout` for its session, and the site's
  driver relies on session state as well. A pooler in front of PostgreSQL must keep sessions (session pooling), not
  hand a session to another client after each transaction.

### Read replicas on 18 and 19

`eZPostgreSQLDB` sends a statement to a standby only when it cannot read stale: a plain `SELECT` of an anonymous `GET`
or `HEAD` web request that has written nothing, outside a transaction, and only while the standby is streaming. Any
error runs the statement on the primary. In `settings/override/site.ini.append.php`:

```ini
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=127.0.0.1
SlaveServerPort[]
SlaveServerPort[]=5433
SlaverServerUser[]
SlaverServerUser[]=exponential
SlaverServerPassword[]
SlaverServerPassword[]=<the password>
SlaverServerDatabase[]
SlaverServerDatabase[]=exponential
```

The key names `SlaverServerUser`, `SlaverServerPassword` and `SlaverServerDatabase` are spelled this way in the kernel
and must be written exactly so. With several `SlaveServerArray[]` entries each request picks one at random.

On the primary, so that a visitor right after an edit sees it on any node:

```sql
ALTER SYSTEM SET synchronous_commit = 'local';   -- only the site's database waits, not every database
ALTER SYSTEM SET synchronous_standby_names = '<standby application_name>';
SELECT pg_reload_conf();
ALTER DATABASE exponential SET synchronous_commit = remote_apply;
GRANT pg_read_all_stats TO exponential;           -- the driver reads pg_stat_wal_receiver on the standby
```

Without `pg_read_all_stats` the driver cannot see that the standby is streaming and never reads it. A node where
nothing listens on the replica's address reads the primary, so one set of settings serves every node of a cluster when
only some have a local standby. The whole rule, and the same feature on MySQL, MariaDB and Oracle:
[Read replicas](../../bc/6.0/postgresql-read-replica.md).

### Shared state of the caches

Two caches keep a small state file that every web server of a cluster must share, and both find the DFS share on their
own:

| Cache | Setting | Default in a cluster |
|---|---|---|
| The HTTP cache: a purge on one server reaches the pages of every server | `httpcache.ini [HttpCacheSettings] SharedStateDir` | the folder `exphttpcache` on the share of `MountPointPath` |
| The SQL query cache in shared mode: a write on one server makes the results of every server stale | `querycache.ini [QueryCacheSettings] SharedStateDir` | the folder `querycache` on the share of `MountPointPath` |

Both take a path instead, or `disabled`. Details: [Clustering in Exponential 6.0](clustering-overview.md#caches-in-a-cluster).

## Proof from production

Production installations were upgraded to PostgreSQL 18.7 with `pg_upgrade`: a primary with a streaming standby,
serving 20 databases with 2,936 tables and 59.5 million rows. The databases were compared after the upgrade and all
20 were identical to what they held before. The kernel itself was proven on 18 and 19 as described in
[Supported versions](#supported-versions-and-how-support-is-tested).

## Known limits

- **MD5 passwords.** PostgreSQL 18 deprecates `md5` password authentication and warns when a role still has an MD5
  hash. Set `password_encryption = 'scram-sha-256'` and set the passwords of the site's and the cluster's logins again
  (`\password exponential` in `psql`); change `md5` to `scram-sha-256` in `pg_hba.conf`. libpq 10 or later is
  needed on the web servers.
- **The client of the backup.** `exp:upgrade:backup`, the caption check, the rollback and `ezconvertdbcharset.php` need
  a `pg_dump`/`psql` at least as new as the server. Install it on every web server that runs them.
- **The gateway connects per request.** `index_cluster.php` opens a new connection to the cluster database for every
  file it serves; `DBPersistentConnection` is a setting of the backend, and the PostgreSQL gateway does not use
  `CLUSTER_PERSISTENT_CONNECTION`. Let the web server cache images (`CLUSTER_ENABLE_HTTP_CACHE`) or put a CDN in front.
- **Replicas are physical standbys.** The read replica check reads `pg_is_in_recovery()` and `pg_stat_wal_receiver`;
  a logical replica is a primary to it and would be read without a lag check. Use streaming standbys.
- **The DFS backend reads the primary only.** File metadata changes with every cache write, so it is never read from a
  replica.
- **Synchronous apply waits for the standby.** With `remote_apply`, commits of the site's database wait while the
  standby is gone. Watch the standby and empty `synchronous_standby_names` automatically when it stops streaming.
- **The setup wizard checks for PostgreSQL 8.0.** Use 13 or later regardless; older releases are not tested and lack
  the trusted `pgcrypto`.

## Related pages

- [Clustering in Exponential 6.0](clustering-overview.md)
- [Clustering on PostgreSQL: the DFS backend in the kernel](postgresql-dfs-cluster.md)
- [Read replicas: PostgreSQL, MySQL and MariaDB, Oracle](../../bc/6.0/postgresql-read-replica.md)
- [A one-node DFS cluster in one command](cluster-commands.md)
- [Database support](../../help/database-support.md)
- [PostgreSQL setup](../../help/database-administrators/postgresql-setup.md)
- [Operating PostgreSQL](../../help/database-administrators/postgresql-operations.md)
- [Moving a site to another engine](../../help/database-administrators/moving-between-engines.md)
- [Upgrading with the console](../../guides/upgrading-with-the-console.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
