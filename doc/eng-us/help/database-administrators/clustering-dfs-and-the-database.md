---
title: "Clustering: DFS and its metadata database"
audience: dba
category: database-administration
order: 41
summary: The database side of an Exponential cluster on several web servers - the DFS file handler and its metadata table ezdfsfile, the backends for MySQL and MariaDB (in the kernel), PostgreSQL (a separate package) and Oracle (ezoracle), the file.ini settings and their unsafe defaults, the cluster update files, maintenance commands and what to monitor.
keywords: cluster, clustering, DFS, eZDFSFileHandler, eZFSFileHandler, ezdfsfile, ezdfsfile_cache, DBBackend, eZDFSFileHandlerMySQLiBackend, eZDFSFileHandlerPostgresqlBackend, eZDFSFileHandlerOracleBackend, file.ini, eZDFSClusteringSettings, config.cluster.php, clusterize, clusterpurge, dfscleanup, cluster_maintenance, NFS
type: help
---
# Clustering: DFS and its metadata database

This page is for database administrators of an Exponential site served by several web servers. Such a site shares
its uploaded files and caches through the DFS cluster file handler, which keeps the files on a shared mount and their
metadata in a database table. This page covers that database: where it lives, which backends exist, the settings, the
maintenance commands and what to watch. The overall design (the NFS mount, `index_cluster.php`) is on
[Clustering and shared storage](../hosting/operations/clustering-and-shared-storage.md).

## Contents

1. [When a cluster is needed](#when-a-cluster-is-needed)
2. [The metadata tables](#the-metadata-tables)
3. [Backends](#backends)
4. [The settings](#the-settings)
5. [Upgrades](#upgrades)
6. [Maintenance commands](#maintenance-commands)
7. [What to watch](#what-to-watch)
8. [Checklist](#checklist)

## When a cluster is needed

`settings/file.ini [ClusteringSettings] FileHandler` decides:

| Handler | Use |
|---|---|
| `eZFSFileHandler` (shipped default) | one web server, plain files |
| `eZDFSFileHandler` | several web servers serving one site: files on an NFS mount, metadata in a database |

A single server needs no cluster, whatever its size. The site's own database and the cluster's metadata database are
separate choices. SQLite cannot be the site database of a multi-server site at all, because WAL needs every process on
one machine.

## The metadata tables

`kernel/sql/mysql/cluster_dfs_schema.sql` creates `ezdfsfile` (one row per clustered file: name, size, modification
time, expiry) and `ezdfsfile_cache`. `file.ini [eZDFSClusteringSettings] MetaDataTableNameCache` can name another table
for caches, for staging setups that share storage entries but not caches. `cluster_db_schema.sql` (`ezdbfile`,
`ezdbfile_data`) belongs to the older database file handler, which was removed; a site that still uses it is moved as
described in [chapter 14, the cluster](../../install/14-migrating-from-4x.md#the-cluster).

The metadata can live in the site's own database server or a separate one. The upstream DFS document notes that a
dedicated server helps large sites; the Oracle extension's `README.cluster` recommends a separate schema for cluster
data, so the two sets of transactions do not mix.

## Backends

| `DBBackend` | Engine | Where it comes from |
|---|---|---|
| `eZDFSFileHandlerMySQLiBackend` | MySQL, MariaDB | the kernel (`kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php`), the shipped default |
| `eZDFSFileHandlerPostgresqlBackend` | PostgreSQL | not in the kernel: the comment in `file.ini` names it, and it comes from the separate package `ezpostgresqlcluster` |
| `eZDFSFileHandlerOracleBackend` | Oracle | the `ezoracle` extension (`clusterfilehandlers/dfsbackends/oracle.php`); only `DBName` is used, not `DBHost`, `DBPort` or `DBSocket` |

There is no SQLite or MongoDB backend.

## The settings

In the global override `settings/override/file.ini.append.php` (not an extension or a siteaccess, because of the
order in which settings are read):

```ini
[ClusteringSettings]
FileHandler=eZDFSFileHandler

[eZDFSClusteringSettings]
MountPointPath=/path/to/nfs
DBBackend=eZDFSFileHandlerMySQLiBackend
DBHost=<cluster database host>
DBPort=3306
DBName=<cluster database>
DBUser=<cluster user>
DBPassword=<the password>
```

The shipped values are `DBHost=dbhost`, `DBName=cluster`, `DBUser=root` and an empty password: placeholders, never for
production. Also shipped: `DBConnectRetries=3`, `DBExecuteRetries=20`, `MaxCopyRetries=5`. `index_cluster.php`, which
serves cluster files over HTTP, reads its own connection from the `CLUSTER_*` constants in `config.cluster.php`
(`CLUSTER_STORAGE_BACKEND`, `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_USER`, `CLUSTER_STORAGE_PASS`,
`CLUSTER_STORAGE_DB` and others). Keep both files out of version control: they hold credentials.

## Upgrades

On an upgrade apply the `dbupdate-cluster-...` file of every step that has one (MySQL: 4.3, 4.7, 5.2, 5.4)
([Upgrades and the SQL update files](upgrades-and-sql-update-files.md#rules)).

## Maintenance commands

| Command | What it does |
|---|---|
| `php bin/php/clusterize.php` (`exp:clusterize`) | moves the files of `var/` into the cluster; `-u` moves them back |
| `php bin/php/dfscleanup.php` (`exp:dfscleanup`) | checks the mount against `ezdfsfile` (`-S`) and the table against the mount (`-B`); `-D` deletes what does not exist |
| `php bin/php/clusterpurge.php --apply` (`exp:clusterpurge`) | physically removes expired cluster files (`--scopes`, `--expiry`, `--dry-run`) |

Run `dfscleanup.php` with `-S` and `-B` first and read the result before you add `-D`. `clusterpurge.php` is the
cronjob part `cluster_maintenance`, which `cronjob.ini [AdminSettings] ForbiddenParts[]` keeps off the browser; run it
from cron. With the Oracle backend that part is not effective; the extension provides
`extension/ezoracle/bin/php/oracleclusterbinarypurge.php` instead.

## What to watch

- the size of `ezdfsfile` and the rate of expired rows;
- connection errors to the cluster database in `var/log/error.log` (every web server connects to it);
- consistency between the mount and the table (`dfscleanup.php -S -B`, read-only);
- the cluster database in your backups, together with the mount.

## Checklist

- [ ] The cluster database has its own account; `DBUser` is not `root`.
- [ ] Settings in `settings/override/file.ini.append.php` and `config.cluster.php`, both outside version control.
- [ ] Cluster update files applied on each upgrade step that has one.
- [ ] `cluster_maintenance` (or the Oracle purge script) scheduled from cron.
- [ ] Mount and table backed up together.

## Related pages

- [Clustering and shared storage](../hosting/operations/clustering-and-shared-storage.md)
- [Several web nodes](../hosting/operations/several-web-nodes.md)
- [Sessions in the database](sessions-in-the-database.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
