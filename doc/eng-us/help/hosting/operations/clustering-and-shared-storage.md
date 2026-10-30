---
title: Clustering and shared storage
audience: hosting, admin
category: hosting
order: 30
summary: How Exponential shares uploaded files and caches between several web servers with the DFS cluster file handler: the NFS mount, the metadata database, index_cluster.php, the maintenance commands and what to watch.
keywords: cluster, clustering, DFS, eZDFSFileHandler, NFS, shared storage, ezdfsfile, index_cluster.php, clusterize, clusterpurge, dfscleanup, file.ini, config.cluster.php
type: help
---
# Clustering and shared storage

This page is for system administrators who design or run an Exponential offer with more than one web server, and
for product managers who need to know what such an offer requires. It answers: how several servers share uploaded
files and caches, what the DFS cluster needs (a shared mount and a metadata database), how files are served, which
commands maintain the cluster, and where the limits are.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../panels/plesk.md), [cPanel](../panels/cpanel.md), [DirectAdmin](../panels/directadmin.md), [ISPConfig](../panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

The facts come from `settings/file.ini`, `config.php-RECOMMENDED`, the cluster documents in `doc/eng-us/features/` and the
migration chapters of the installation book. The other half of a multi-server setup (sessions, caches in front of
PHP, proxies) is on [Several web nodes](several-web-nodes.md).

## Contents

1. [Two file handlers](#two-file-handlers)
2. [How the DFS cluster works](#how-the-dfs-cluster-works)
3. [Setting it up](#setting-it-up)
4. [Serving files: index_cluster.php](#serving-files-index_clusterphp)
5. [Maintenance commands](#maintenance-commands)
6. [What to watch](#what-to-watch)
7. [Checklist](#checklist)

## Two file handlers

`file.ini [ClusteringSettings] FileHandler` decides how Exponential reads and writes the files of `var/`:

| Handler | What it does | Use it for |
|---|---|---|
| `eZFSFileHandler` (shipped default) | plain files on the local disk | one web server |
| `eZDFSFileHandler` | files on a shared NFS mount, their metadata in a database table, caches copied to each server | several web servers serving one site |

The older handlers `eZDBFileHandler` and `eZFS2FileHandler` were removed; a site that still uses them is moved as
described in [chapter 14, the cluster](../../../install/14-migrating-from-4x.md#the-cluster).

> **Note**
> A single server does not need a cluster file handler, whatever its size. Clustering is for a site that several
> machines serve at once. It adds a mount and a database to run, back up and monitor.

## How the DFS cluster works

From [How-to configure eZDFS cluster](../../../features/4.2/eZDFS-INSTALL.txt):

- The reference copy of every clustered file is on an **NFS export**, mounted at the same path on every web server.
- Its metadata (name, size, modification time, expiry) is kept in a **database table**, `ezdfsfile`.
- **Cache files** are copied to the local disk of the server that uses them.
- **Images and binary files** requested by a browser are streamed from the mount.
- The **var directories must not be shared** between the servers: each keeps its own, and the cluster handler keeps
  them in step with the central copy.

The metadata database can be the site's own database server or a separate one; the document notes that "for large
scale websites, a dedicated MySQL server can really improve performances".

### Database backends

| Backend (`[eZDFSClusteringSettings] DBBackend`) | Where it comes from |
|---|---|
| `eZDFSFileHandlerMySQLiBackend` | the kernel (the shipped default), for MySQL and MariaDB |
| `eZDFSFileHandlerPostgresqlBackend` | the package `ezpostgresqlcluster` |
| `eZDFSFileHandlerOracleBackend` | the `ezoracle` extension |

Source: [15.17 Cluster and DFS](../../../install/15-migrating-from-5x-legacy.md#1517-cluster-and-dfs) and the backends listed in
`config.php-RECOMMENDED` (`dfsmysqli`, `dfspostgres`, `dfsoracle`). The storage side can be replaced as well:
`[eZDFSClusteringSettings] DFSBackend` names a class that implements `eZDFSFileHandlerDFSBackendInterface`
([Configurable DFS cluster backend](../../../features/5.4/configurable_dfs_cluster_backend.md)).

> **Note**
> The site's own database and the cluster's metadata database are separate choices. A site on PostgreSQL can keep
> its content in PostgreSQL; the cluster table needs one of the backends above. SQLite cannot be the site database
> of a multi-server setup at all: its WAL mode needs every process on one machine
> ([chapter 9](../../../install/09-databases.md#wal-readers-and-the-writer-side-by-side)).

## Setting it up

1. **Create the table.** Its definition is in the MySQLi backend class and printed in the eZDFS document
   (`CREATE TABLE ezdfsfile ...`, InnoDB).
2. **Mount the export** on every web server at the same path, writable by the site's user.
3. **Configure the handler** in the global override `settings/override/file.ini.append.php`. Cluster settings must be
   in that global file, not in an extension or a siteaccess, because of the order in which settings are read:

   ```ini
   # settings/override/file.ini.append.php
   [ClusteringSettings]
   FileHandler=eZDFSFileHandler

   [eZDFSClusteringSettings]
   MountPointPath=/path/to/nfs
   DBBackend=eZDFSFileHandlerMySQLiBackend
   DBHost=<cluster database host>
   DBPort=3306
   DBName=<cluster database>
   DBUser=<user>
   DBPassword=<password>
   ```

   The shipped file also has `DBConnectRetries=3`, `DBExecuteRetries=20` and `MaxCopyRetries=5`.
4. **Move the existing files into the cluster** once: `php bin/php/clusterize.php` (see below).
5. **Point the rewrite rules for files at `index_cluster.php`** and give it its constants (next section).

On an upgrade, apply the `dbupdate-cluster-...` file of every step that has one
([11.3 The update files](../../../install/11-upgrading.md#113-the-update-files)).

## Serving files: index_cluster.php

Since 4.7 one file in the document root, `index_cluster.php`, serves cluster files over HTTP
([Revamped cluster index](../../../features/4.7/cluster_index.md)). It reads `config.php` and, when present,
`config.cluster.php`, and expects the `CLUSTER_*` constants that `config.php-RECOMMENDED` documents:
`CLUSTER_STORAGE_BACKEND` (for example `dfsmysqli`), `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_PORT`,
`CLUSTER_STORAGE_USER`, `CLUSTER_STORAGE_PASS`, `CLUSTER_STORAGE_DB` and `CLUSTER_MOUNT_POINT_PATH`, plus optional
flags such as `CLUSTER_ENABLE_HTTP_CACHE` and `CLUSTER_EXPIRY_TIMEOUT`.

Set them from the console on every web server, with the same values as `[eZDFSClusteringSettings]`. Each command
shows the change as a diff, writes only with `--apply`, backs `config.php` up to `var/backup/config/<time>/` first and
checks the new file with `php -l`:

```bash
./console exp:setup:config --apply                           # once, when there is no config.php yet
./console exp:config:set CLUSTER_STORAGE_BACKEND dfsmysqli --apply
./console exp:config:set CLUSTER_STORAGE_HOST db1.example.com --apply
./console exp:config:set CLUSTER_STORAGE_PORT 3306 --apply
./console exp:config:set CLUSTER_STORAGE_USER <user> --apply
./console exp:config:set CLUSTER_STORAGE_PASS --value="$CLUSTER_DB_PASS" --apply
./console exp:config:set CLUSTER_STORAGE_DB <cluster database> --apply
./console exp:config:set CLUSTER_MOUNT_POINT_PATH /path/to/nfs --apply
./console exp:config:list --only-set                         # the password shows as ********
chmod 0640 config.php                                        # with the web server's group
```

The password is masked in every output; the command warns when it lands in a `config.php` every user can read.
`./console exp:config:list --only-set --format=json`, run on each server, gives documents you can compare to see
that all web nodes agree. See [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md).

The web server sends it only the paths its rewrite rules name. With per-site caches in `CacheVarDir`, add their public
caches as well ([Cluster (DFS)](../../../features/6.0/multi-site-log-and-cache-paths.md#cluster-dfs)):

```apache
RewriteRule ^/var_cache/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [L]
```

> **Warning**
> `config.php`, `config.cluster.php` and `settings/override/file.ini.append.php` hold database credentials. Keep them
> out of version control and out of any file a customer can download; the backups in `var/backup/config/` hold earlier
> copies of `config.php` and need the same care.

## Maintenance commands

| Command | What it does | Options (from the command's own help) |
|---|---|---|
| `php bin/php/clusterize.php` | moves the files of `var/` into the cluster; `-u` moves them back | `-u`, `--skip-binary-files`, `--skip-media-files`, `--skip-images`, `-r` (remove after copying), `-n` (do not wait) |
| `php bin/php/dfscleanup.php` | checks the mount against the `ezdfsfile` table and the other way | `-S` (share against database), `-B` (database against share), `-D` (delete what does not exist), `--path`, `--iteration-limit` |
| `php bin/php/clusterpurge.php --apply` | physically removes cluster files that expired | `--scopes=...`, `--expiry=<days>` (default 30), `--dry-run`, `--iteration-sleep`, `--iteration-limit`, `--memory-monitoring` |

Run `dfscleanup.php` with `-S` and `-B` first and read the result before you add `-D`
([15.17](../../../install/15-migrating-from-5x-legacy.md#1517-cluster-and-dfs)). `clusterpurge.php` is also the cronjob part
`cluster_maintenance`; the shipped `cronjob.ini [AdminSettings] ForbiddenParts[]` keeps it off the buttons of
**Setup > Cronjobs**, because it is destructive and belongs to an administrator on a shell
([Cron jobs](cron-jobs.md)).

## What to watch

- **The mount.** If the NFS mount goes away, every server fails to read files. Monitor the mount and the NFS server
  as part of the site.
- **The metadata database.** It is on the path of every file read; it needs the same backup and monitoring as the
  site's database.
- **Consistency.** Run `dfscleanup.php -S` and `-B` after an incident and before a migration.
- **Pausing cron.** Cronjob pause files are local to each server unless `cronjob.ini [PauseSettings] Directory` points
  at a shared directory ([Pausing cronjob parts](../../../features/6.0/cronjob-pause.md)).
- **Cluster events.** `file.ini [ClusterEventsSettings] ClusterEvents=disabled` ships off; with a listener class they
  let an extension react to cluster operations, for the DFS cluster with the MySQLi backend only
  ([Cluster events](../../../features/4.7/cluster_events.md)).

## Checklist

- [ ] The NFS export is mounted at the same path on every web server, writable by the site's user.
- [ ] `ezdfsfile` exists in the metadata database, which is backed up and monitored.
- [ ] Cluster settings are in the global `settings/override/file.ini.append.php`.
- [ ] Each server has its own `var/` directory; nothing but the mount is shared.
- [ ] The rewrite rules send images and binary files to `index_cluster.php`, which has its `CLUSTER_*` constants.
- [ ] `dfscleanup.php -S` and `-B` report no differences after `clusterize.php`.
- [ ] `clusterpurge.php` runs on a schedule from one server only.

## Related pages

- [Several web nodes](several-web-nodes.md)
- [Storage layout](storage-layout.md)
- [Backups: what to back up](backups-what-to-back-up.md)
- [Databases in the official order](../basics/databases-in-the-official-order.md)
- [Choosing a hosting model](../models/choosing-a-hosting-model.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md): `exp:config:set` for the `CLUSTER_*` constants
- [Database support](../../database-support.md), [Operating system support](../../os-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
