# Clustering in Exponential 6.0

This page is for administrators and architects who run one Exponential site on several web servers, or plan to. It
explains the parts of a cluster in one place (the application servers, the shared file backend, the database primary
and its replicas, the caches, how a purge reaches every server, and how to check that the cluster is healthy) and
links the page that covers each part in depth. Everything here is part of the kernel of Exponential 6.0.15; no
extension is needed unless a row says so.

## Contents

1. [The parts of a cluster](#the-parts-of-a-cluster)
2. [Application servers](#application-servers)
3. [Shared files: the DFS cluster file handler](#shared-files-the-dfs-cluster-file-handler)
4. [Serving files: the cluster gateway](#serving-files-the-cluster-gateway)
5. [The database: primary and replicas](#the-database-primary-and-replicas)
6. [Caches in a cluster](#caches-in-a-cluster)
7. [How a change reaches every server](#how-a-change-reaches-every-server)
8. [Health checks and monitoring](#health-checks-and-monitoring)
9. [Commands and maintenance](#commands-and-maintenance)
10. [Upgrading the database server under a running cluster](#upgrading-the-database-server-under-a-running-cluster)
11. [Setting up a cluster, step by step](#setting-up-a-cluster-step-by-step)
12. [Operating systems](#operating-systems)
13. [Every cluster page](#every-cluster-page)

## The parts of a cluster

A typical cluster: two or more application servers behind a load balancer, one shared disk, one database primary with
a streaming standby, and optionally a shared cache server.

```text
                         visitors and editors
                                  |
                          +---------------+
                          | load balancer |   health check: /ezinfo/is_alive
                          +---------------+
                           /             \
          +----------------------+   +----------------------+
          | application server 1 |   | application server 2 |   PHP-FPM or Velocity workers,
          |  var/ of its own     |   |  var/ of its own     |   local copies of cache files
          +----------------------+   +----------------------+
             |       |       |          |       |       |
             |       |       +----------+-------+------------> shared disk (NFS): stored files,
             |       |                  |       |               cache files, shared cache state
             |       +------------------+-------+------------> cache server (optional): Valkey or Redis
             |                          |
     +-----------------+  streaming  +-----------------+
     | database primary| ----------> | standby         |   writes and editors: primary;
     | site + cluster  |             | (read replica)  |   anonymous pages: standby when current
     +-----------------+             +-----------------+
```

| Part | What it holds | Shared by every server? | Page |
|---|---|---|---|
| Application servers | the code, `settings/`, a `var/` of their own | the same code and settings on each, never the same `var/` | [Application servers](#application-servers) |
| Shared disk | the reference copy of every stored file and cache file | yes, mounted at the same path | [Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md) |
| Cluster metadata | one row per clustered file (`ezdfsfile`, `ezdfsfile_cache`) | yes, one database | [Shared files](#shared-files-the-dfs-cluster-file-handler) |
| Site database | the content, users, settings stored in the database | yes, the primary | [The database](#the-database-primary-and-replicas) |
| Read replicas | a copy of the site database | each server may have its own | [Read replicas](../../bc/6.0/postgresql-read-replica.md) |
| Shared cache state | purge times of the HTTP cache, write times of the SQL query cache | yes, on the shared disk | [Caches in a cluster](#caches-in-a-cluster) |
| Cache server | view cache and compiled INI entries (optional) | yes | [sevenx_valkey_cache](extensions/sevenx_valkey_cache.md) |

A single server does not need any of this, whatever its size. A **one-node cluster** (the same setup on one machine)
is useful when a second server will join later: `exp:cluster:enable` makes it in one command, and the second server
then mounts the same share and uses the same settings ([A one-node DFS cluster in one command](cluster-commands.md)).

## Application servers

Every application server runs the same code and the same `settings/`, and serves the site through PHP-FPM behind
Apache, nginx, LiteSpeed or Caddy, or through Exponential Velocity's persistent workers. What must be the same, and
what must not:

- **The same code and settings.** Deploy them to every server together (`./console exp:velocity deploy` on each). An
  INI change on one server only is a different site.
- **A `var/` of its own.** The DFS handler keeps local copies of cache files in each server's `var/` and keeps them in
  step with the share. Never share `var/` itself between servers.
- **Sessions.** The shipped `site.ini [Session] Handler` is empty, which leaves sessions to PHP (`session.save_handler`,
  files by default). In a cluster a signed-in editor must be recognised on every server: use
  `Handler=ezpSessionHandlerDB` (sessions in the site's database), a PHP session handler on a shared Valkey or Redis
  server, or sticky sessions on the load balancer.
- **Cronjobs** run on one server, or on each server only for the parts that are local to it. Pausing cronjobs across
  servers needs `cronjob.ini [PauseSettings] Directory` on a shared folder ([Pausing cronjob parts](cronjob-pause.md)).
- **Velocity** workers load classes and settings at start: after a change, `exp:velocity deploy` on every server.

## Shared files: the DFS cluster file handler

`file.ini [ClusteringSettings] FileHandler=eZDFSFileHandler` stores every file of `var/` that the cluster manages
(stored images and binary files, view cache, template blocks, packed scripts and stylesheets) on the share named by
`[eZDFSClusteringSettings] MountPointPath`, and indexes it in a metadata database. A server reads a cache file from
its own disk while the index says it is current, and fetches it from the share when it is not. One process generates
an expired cache file while the others wait or serve the stale copy (`.generating` rows), so a cluster does not
stampede.

The metadata backend is chosen by `[eZDFSClusteringSettings] DBBackend`. Every backend is in the kernel and passes the
same parity checks:

| Database | `DBBackend` | Gateway (`CLUSTER_STORAGE_BACKEND`) | Servers | Page |
|---|---|---|---|---|
| MySQL, MariaDB | `eZDFSFileHandlerMySQLiBackend` (default) | `dfsmysqli` | any number | [Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md) |
| PostgreSQL 13 to 19 | `expDFSFileHandlerPostgresqlBackend` | `dfspostgresql` | any number | [Clustering on PostgreSQL](postgresql-dfs-cluster.md), [PostgreSQL 18 and 19](postgresql-18-and-19.md) |
| Oracle 12.1 and later | `expDFSFileHandlerOracleBackend` | `dfsoracle` | any number | [Clustering on Oracle](oracle-dfs-cluster.md) |
| SQL Server 2017 and later, Azure SQL | `expDFSFileHandlerSQLServerBackend` | `dfssqlserver` | any number | [Clustering on SQL Server](sqlserver-dfs-cluster.md) |
| MongoDB 5.0 and later | `expDFSFileHandlerMongoDBBackend` | `dfsmongodb` | any number, one server or a replica set | [Clustering on MongoDB](mongodb-dfs-cluster.md) |
| SQLite 3.24 and later | `expDFSFileHandlerSQLiteBackend` | `dfssqlite` | **one machine only** | [Clustering on SQLite](sqlite-dfs-cluster.md) |

The site's database and the cluster metadata are separate choices: a site on MySQL can keep its cluster metadata in
PostgreSQL, and the other way round. The settings that matter on every SQL backend:

| `[eZDFSClusteringSettings]` | Shipped | Meaning |
|---|---|---|
| `MountPointPath` | empty | the share, the same path on every server |
| `DBBackend` | `eZDFSFileHandlerMySQLiBackend` | the metadata backend (above) |
| `DBHost`, `DBPort`, `DBSocket` | `dbhost`, `3306`, empty | where the metadata database is; `DBSocket` (a socket path, or for PostgreSQL the socket directory) takes the place of the host |
| `DBName`, `DBUser`, `DBPassword` | `cluster`, `root`, empty | the database and its login; Oracle: `DBName` is the connect string; SQLite: the database file |
| `DBConnectRetries` | `3` | connection attempts |
| `DBExecuteRetries` | `20` | tries of a statement that hit a deadlock or a lock wait |
| `DBLockTimeout` | not set (10 s) | PostgreSQL, Oracle, SQL Server, SQLite, MongoDB: how long a statement waits for a lock before it is tried again |
| `DBPersistentConnection` | not set (off) | PostgreSQL and Oracle: keep one connection per PHP worker between requests |
| `MaxCopyRetries` | `5` | tries of a copy from the share |
| `MetaDataTableNameCache` | not set | a cache table of its own, for installations that share the storage rows but not the caches |

A node never keeps or serves a short or failed copy from the share: every backend checks the size on the temporary
copy before it renames it into place, and removes the old local copy when no complete copy can be made.

Every backend sends the cluster events (`file.ini [ClusterEventsSettings] ClusterEvents=enabled` with a `Listener`
class), so a listener that keeps metadata in Memcached or Redis works on each of them, not only on MySQLi
([Cluster events](../4.7/cluster_events.md)).

`exp:cluster:enable` makes the share group-writable, so the other nodes, which reach it over NFS as its group, can
write into it. Mount the share with the same numeric user and group on every server.

## Serving files: the cluster gateway

Stored images and the public cache (packed scripts and stylesheets, text images) are on the share, not on the server
that received the request. The web server sends those addresses to `index_cluster.php`, which looks the file up in the
metadata database and streams it from the share, with byte ranges and HTTP caching. Its settings are constants in
`config.cluster.php`: `CLUSTER_STORAGE_BACKEND`, `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_PORT`, `CLUSTER_STORAGE_USER`,
`CLUSTER_STORAGE_PASS`, `CLUSTER_STORAGE_DB`, `CLUSTER_STORAGE_CHARSET`, `CLUSTER_MOUNT_POINT_PATH`, and optionally
`CLUSTER_ENABLE_HTTP_CACHE`, `CLUSTER_ENABLE_HTTP_RANGE`, `CLUSTER_EXPIRY_TIMEOUT` and `CLUSTER_IMAGE_ALIAS_ON_DEMAND`.
On PostgreSQL, `CLUSTER_PERSISTENT_CONNECTION` (default false) set to true keeps the gateway's database connection
open in the PHP worker between requests, so a worker that serves many images logs in once; count those connections
against `max_connections`.

```apache
# Apache, in the virtual host, before the site's own rules
RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [PT,END]
RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [PT,END]
```

```nginx
location ~ ^/var/([^/]+/)?(storage/images(-versioned)?|cache/(texttoimage|public))/ { rewrite ^ /index_cluster.php last; }
```

Without the public cache rule, a server answers 404 for a packed file another server made. An image alias that has
not been made yet is handed on to `index_image.php`, which makes it in the cluster
(`CLUSTER_IMAGE_ALIAS_ON_DEMAND`, on by default; [Image aliases made on demand](image-aliases-on-demand.md)). The
rules for every web server and `.htaccess` are on [A one-node DFS cluster in one command](cluster-commands.md).

## The database: primary and replicas

Every server writes to one **primary**. Reach it through an address that moves with it on a failover (a virtual IP, a
DNS name with a short TTL, or a database proxy), so a failover changes no setting. The cluster metadata usually lives
on the same primary.

A **standby** fed by streaming replication is a hot spare and, from 6.0.15, a **read replica**:
`site.ini [DatabaseSettings] UseSlaveServer=enabled` with `SlaveServerArray[]`, `SlaveServerPort[]`,
`SlaverServerUser[]`, `SlaverServerPassword[]` and `SlaverServerDatabase[]`. One rule serves PostgreSQL, MySQL,
MariaDB and Oracle: a statement runs on the replica only when it is a plain `SELECT` of an anonymous `GET` or `HEAD`
web request that has written nothing, outside a transaction, and only while the replica is current. Editors,
cronjobs, scripts and forms always read the primary, the first write pins the rest of a request to the primary, and
any error on the replica runs the statement on the primary. An edit is never read back stale:

| Engine | How "current" is decided | Settings |
|---|---|---|
| PostgreSQL | the standby is streaming (`pg_stat_wal_receiver`); the primary applies commits on the standby before they return (`synchronous_commit = remote_apply`) | `UseSlaveServer`, the `SlaveServer*` keys |
| MySQL, MariaDB | both replication threads run, the lag is at most `SlaveServerMaxLag`, the replica has applied the primary's GTID position of the start of the request | `SlaveServerStatusCheck`, `SlaveServerMaxLag`, `SlaveServerCatchUp`, `SlaveServerCatchUpWait` |
| Oracle (ezoracle) | the standby is open read-only with apply and its apply lag is within `SlaveServerMaxLag`; `STANDBY_MAX_DATA_DELAY` | the same |
| SQLite | no replica: `UseSlaveServer` is ignored | none |
| MongoDB | the replica set's own read preferences; `UseSlaveServer` is not read | none |

A server with no replica on the configured address reads the primary, so one set of settings serves every server
even when only some of them have a local standby. The DFS metadata backend always uses the primary. Details:
[Read replicas](../../bc/6.0/postgresql-read-replica.md); on PostgreSQL 18 and 19:
[PostgreSQL 18 and 19](postgresql-18-and-19.md#running-a-cluster-on-18-and-19).

## Caches in a cluster

| Cache | Where it lives in a cluster | What keeps it correct on every server |
|---|---|---|
| View cache, template blocks, other cache files | the share, with local copies on each server | the DFS metadata: an expired file is expired for every server at once |
| Role-aware HTTP cache (`httpcache.ini`) | each server's own `var/<site>/cache/exphttpcache/` | `[HttpCacheSettings] SharedStateDir`: every purge is also written to a state file all servers read |
| SQL query cache, shared mode (`querycache.ini`) | results: each server's APCu or own folder | `[QueryCacheSettings] SharedStateDir`: the time of the last write of every table, in one file all servers read |
| Velocity response cache | each Velocity server | in a cluster it takes the HTTP cache's shared state file as its generation marker |
| Packed scripts and stylesheets | the share | served through the gateway (the public cache rule above) |
| Valkey or Redis (extension `sevenx_valkey_cache`) | one cache server for every application server | one server for all; `InstallationHash` set to a fixed string so every server uses the same keys |

Both `SharedStateDir` settings work the same way:

```ini
# settings/override/httpcache.ini.append.php        (and querycache.ini.append.php, [QueryCacheSettings])
[HttpCacheSettings]
# empty (the default): with eZDFSFileHandler, a folder on its share (exphttpcache, querycache) under MountPointPath;
# on a single server nothing is shared
SharedStateDir=
# or a folder every server mounts, absolute or relative to the installation
#SharedStateDir=/mnt/shared/mysite/exphttpcache
# or never share
#SharedStateDir=disabled
```

The cost is one small file read per request. For the query cache the lock file `state.lock` on the share must be
lockable by every user the site runs as (NFSv4, or NFSv3 with `lockd`); a root process on a share that squashes root
cannot write it. A missing or unreadable shared state purges nothing and breaks nothing. Pages:
[The role-aware HTTP cache](../../bc/6.0/httpcache.md#several-web-servers-a-cluster),
[The SQL query cache](../../bc/6.0/sql-query-cache.md),
[Velocity response cache](velocity-response-cache.md),
[sevenx_valkey_cache](extensions/sevenx_valkey_cache.md),
[Valkey and Redis](../../help/hosting/performance/valkey-and-redis.md).

## How a change reaches every server

An editor publishes on server 1. What the other servers see, and when:

1. **The database.** The new version is in the primary at commit. With synchronous apply (PostgreSQL) or the GTID
   catch-up (MySQL, MariaDB), a read replica returns it too from the next request on.
2. **Cache files.** The view cache of the changed nodes is expired in the cluster metadata; every server sees the
   expiry on its next read and fetches or regenerates the file.
3. **HTTP cache and Velocity.** The purge is written to the shared state; every server treats the purged pages as
   stale from its next request on. Before 6.0.15 an edit stayed invisible on the other servers until their copy
   expired (up to `MaxAge`, an hour by default).
4. **SQL query cache.** The write time of the changed tables is written to the shared state; results of every server
   that read those tables are stale from their next request on.
5. **Valkey or Redis.** The extension's handler purges the view and block entries indexed by the changed node ids, on
   the one shared server.

Clearing caches by hand: `php bin/php/ezcache.php --clear-tag=content --allow-root-user` (or `./console exp:cache`)
expires the cache files in the cluster metadata, so it acts for every server at once; local items such as OPcache, APCu and Velocity's
workers need `exp:velocity deploy` on each server.

## Health checks and monitoring

| What | How |
|---|---|
| A server and its database | `GET /ezinfo/is_alive` answers `Exponential is alive` (200, `text/plain`) when the database is connected, and `No connection` when it is not. Point the load balancer's health check at it; match the text, not only the status code ([ezinfo/is_alive](../../bc/6.0/ezinfo-is-alive.md)). |
| The share | Monitor the mount on every server: when it goes away every server fails to read files. `exp:cluster:enable` leaves a marker file `.exp-cluster.json` in the share; a check that the file is readable is a cheap mount test. |
| The cluster metadata | It is on the path of every file read: the same backups and monitoring as the site's database. |
| Consistency of share and metadata | `php bin/php/dfscleanup.php -S` and `-B` (report only) after an incident and before a migration. |
| PostgreSQL replication | on the primary `pg_stat_replication` (`state = streaming`, `sync_state = sync` with synchronous apply); on the standby `pg_stat_wal_receiver`. |
| MySQL and MariaDB replication | `SHOW REPLICA STATUS` on the replica. |
| Which cache answered | `X-Exp-Cache: HIT`, `STALE` or `MISS (reason)` on every response of the HTTP cache. |

Velocity checks its own workers and its cluster peers on an endpoint of its own; it does not use `ezinfo/is_alive`.

## Commands and maintenance

| Command | What it does |
|---|---|
| `./console exp:cluster:enable` / `exp:cluster:disable` | make an installation a one-node DFS cluster and back (share, tables in the site's database, settings, files moved), a dry run unless `--apply`; `--backend` (`mysqli`, `postgresql`, `sqlite`, `mongodb`, `oracle`, `sqlserver`), `--share`, `--no-move` ([page](cluster-commands.md)) |
| `php bin/php/clusterize.php` (`./console exp:clusterize`) | move the files of `var/` into the cluster; `-u` moves them back ([example](../../examples/commands/clusterize.md)) |
| `php bin/php/clusterpurge.php` (`./console exp:clusterpurge`) | physically remove expired cluster files, a dry run unless `--apply`; also the cronjob part `cluster_maintenance`, which `cronjob.ini [AdminSettings] ForbiddenParts[]` keeps off the admin buttons ([example](../../examples/commands/clusterpurge.md)) |
| `php bin/php/dfscleanup.php` | compare the share with the metadata (`-S`, `-B`), delete what does not exist (`-D`) |
| `./console exp:upgrade:backup` | the database backup the upgrade makes, on its own; on PostgreSQL with a client at least as new as the server ([PostgreSQL 18 and 19](postgresql-18-and-19.md#client-versions-php-libpq-pg_dump-and-psql)) |

Run `clusterpurge.php` on a schedule from **one** server only.

## Upgrading the database server under a running cluster

A major upgrade of the database server (PostgreSQL 18 to 19, for example) touches every server of the cluster at
once, because they all write to the same primary and the DFS metadata lives there too. The order that keeps the
downtime short and the data provably the same (the full procedure for PostgreSQL, with every command:
[From 18 to 19, step by step](postgresql-18-and-19.md#from-18-to-19-step-by-step-pg_upgrade-on-one-server)):

1. **Clients first, on every server.** Install the new major version's client (`pg_dump`, `psql`) on every
   application server and on the database servers, and make it the one on `PATH`, or set
   `upgrade.ini [UpgradeSettings] PostgreSQLClientDir`. A newer client works with the old server; an older one refuses
   the new server, and backups, the upgrade caption and timers that dump databases would fail after the switch.
2. **Prepare without downtime.** Install the new server next to the old one, initialise its data directory with the
   old one's encoding, locale and checksum setting, and run `pg_upgrade --check` against the running server.
3. **Pause what writes on its own:** backup and caption timers, cronjobs on the database server, the replica watch.
4. **Release synchronous replication** (`synchronous_standby_names = ''`): the standby cannot follow the new major
   version, and commits would wait for it.
5. **Close the doors.** Stop the one path every application server uses to reach the database (a database proxy, a
   virtual IP or the firewall rule), so nothing writes during the switch. Sites with the HTTP cache keep serving cached
   pages; requests that need the database get an error page for the duration.
6. **Count, upgrade, count again.** Exact row counts of every table and sequence position before and after; on a
   file system with reflinks `pg_upgrade --clone` takes minutes even for many gigabytes and leaves the old cluster
   startable. Only extension versions may differ.
7. **Open the doors**, then gather statistics (`vacuumdb --all --analyze-in-stages`) while the sites already run.
8. **Rebuild the standby** from the new primary (a new slot, `pg_basebackup`), and arm synchronous replication again
   when it streams. Until then every application server reads the primary: the read replica rule needs a streaming
   standby, so nothing is read stale meanwhile.
9. **Check every server, not only the load balancer:** the front page and an uncached search on each node, an edit on
   one node seen at once on the others, an image uploaded on one node served byte for byte by every node, the load
   balancer's health checks all up.

Keep the old data directory until the cluster has run on the new version for some days: it is the rollback, together
with the standby's old data directory.

## Setting up a cluster, step by step

1. Choose the metadata database (the table above) and the share (NFS, mounted at the same path on every server, with
   the same numeric user and group).
2. On the first server: `./console exp:cluster:enable --share=/mnt/exp-dfs/example.com` to see the plan, then the same
   with `--apply`. It makes the share, the tables in the site's database, the settings in
   `settings/override/file.ini.append.php` and `config.cluster.php`, and moves the files in. For a separate metadata
   database, write the settings yourself as the backend pages show.
3. Add the gateway rules to the web server and reload it.
4. Set up sessions for several servers (`[Session] Handler`), and the read replica if you have one.
5. Add the second server: the same code, the same `settings/override` and `config.cluster.php`, the share mounted, its
   own `var/`, the same gateway rules; `exp:velocity deploy` there.
6. Put both behind the load balancer with the health check on `/ezinfo/is_alive`.
7. Check: publish on one server and load the page on the other (the HTTP cache must show it at once); load an image
   through each server; `dfscleanup.php -S` and `-B` report no differences.

## Operating systems

Cluster mode is plain PHP. The DFS file handler, its six metadata backends, the cluster gateway and the cluster
commands have no code of their own for one operating system: they build paths with `/` and the system's directory
separator, and they turn `\` into `/` in the paths they store. What a cluster needs from the operating system is a
shared folder that every server mounts at the same path, with working file locks, and PHP with the database's
extension.

| System | The share | Status |
|---|---|---|
| Linux (Red Hat Enterprise Linux and its rebuilds, Debian, Ubuntu and the others on [Operating system support](../../help/os-support.md)) | NFSv4, or NFSv3 with `lockd`, mounted at the same path on every server | the reference platform: every metadata backend (MySQL 8.0, PostgreSQL 13 to 19, MongoDB 8.0, SQLite, Oracle Free 23, SQL Server 2017 to 2025) passes its parity and integration tests in the project's CI on every push |
| macOS, FreeBSD and the other BSDs | an NFS mount at the same path on every server | the same code paths as on Linux: absolute `/` paths for the share, a group-writable share |
| Windows | an SMB share, reached by a drive letter or a UNC path | **in progress**: `exp:cluster:enable --share` and `install.php --cluster-share` take an absolute `/` path today; accepting drive letters and UNC paths is being added. The SQL Server driver is tested against SQL Server LocalDB on Windows in CI |

The database servers do not have to run the same system as the web servers: a cluster of Linux web servers keeps its
metadata on SQL Server, PostgreSQL or Oracle wherever they run, and the other way round.

## Every cluster page

**Kernel features (6.0)**

- [PostgreSQL 18 and 19](postgresql-18-and-19.md): versions, clients, upgrading, a cluster on 18 and 19
- [Clustering on PostgreSQL: the DFS backend in the kernel](postgresql-dfs-cluster.md)
- [Clustering on Oracle: the DFS backend in the kernel](oracle-dfs-cluster.md)
- [Clustering on SQL Server: the DFS backend in the kernel](sqlserver-dfs-cluster.md)
- [Clustering on MongoDB: the DFS backend in the kernel](mongodb-dfs-cluster.md)
- [Clustering on SQLite: the one-node DFS backend in the kernel](sqlite-dfs-cluster.md)
- [A one-node DFS cluster in one command](cluster-commands.md): `exp:cluster:enable` and `exp:cluster:disable`
- [Image aliases made on demand](image-aliases-on-demand.md): the gateway hands missing aliases to `index_image.php`
- [Velocity response cache](velocity-response-cache.md)
- [sevenx_valkey_cache: Redis and Valkey cache backend](extensions/sevenx_valkey_cache.md)

**Behaviour and settings (6.0)**

- [Read replicas: PostgreSQL, MySQL and MariaDB, Oracle](../../bc/6.0/postgresql-read-replica.md)
- [The role-aware HTTP cache](../../bc/6.0/httpcache.md): several web servers
- [The SQL query cache](../../bc/6.0/sql-query-cache.md): shared mode in a cluster
- [ezinfo/is_alive](../../bc/6.0/ezinfo-is-alive.md)

**Help and operations**

- [Clustering and shared storage](../../help/hosting/operations/clustering-and-shared-storage.md)
- [Clustering: DFS and its metadata database](../../help/database-administrators/clustering-dfs-and-the-database.md)
- [Read replicas](../../help/government/operations/availability/read-replicas.md)
- [Static publishing on Windows and in clusters](../../guides/static-publishing-on-windows-and-clusters.md)
- [Database support](../../help/database-support.md)

**History**

- [Configurable DFS cluster backend (5.4)](../5.4/configurable_dfs_cluster_backend.md)
- [Cluster events (4.7)](../4.7/cluster_events.md)
