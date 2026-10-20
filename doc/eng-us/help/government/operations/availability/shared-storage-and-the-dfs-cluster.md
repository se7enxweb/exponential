---
title: Shared storage and the DFS cluster
audience: government, hosting, admin
category: public-sector
order: 40
summary: For infrastructure teams in public bodies that let several web servers share the uploaded files of an Exponential site. How the DFS cluster file handler works, what it needs (an NFS export and a metadata database), the maintenance commands, and what to add to your continuity plan.
keywords: DFS, cluster, eZDFSFileHandler, NFS, shared storage, ezdfsfile, metadata database, index_cluster.php, clusterize.php, dfscleanup.php, clusterpurge.php, ezpostgresqlcluster, file.ini, continuity
type: help
---
# Shared storage and the DFS cluster

This page is for infrastructure and storage teams in public bodies who must make the uploaded files of an Exponential
site available to more than one web server. It answers how the DFS cluster file handler works, what it needs from your
storage and database teams, which commands maintain it, and which risks belong in the continuity plan. The complete
setup is on the hosting page [Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md).

## Contents

1. [When you need it](#when-you-need-it)
2. [How the DFS cluster works](#how-the-dfs-cluster-works)
3. [What your teams provide](#what-your-teams-provide)
4. [Maintenance commands](#maintenance-commands)
5. [Risks for the continuity plan](#risks-for-the-continuity-plan)
6. [Checklist](#checklist)

## When you need it

`file.ini [ClusteringSettings] FileHandler` decides how files of `var/` are read and written. The shipped
`eZFSFileHandler` uses plain files on the local disk and is right for one web server. `eZDFSFileHandler` is for
several web servers serving one site. "A single server does not need a cluster file handler, whatever its size"
([Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md)).

## How the DFS cluster works

- The reference copy of every clustered file is on an **NFS export**, mounted at the same path on every web server.
- Its metadata (name, size, modification time, expiry) is kept in a **database table**, `ezdfsfile`.
- **Cache files** are copied to the local disk of the server that uses them.
- **Images and binary files** are streamed from the mount by `index_cluster.php`.
- The **var directories are not shared**; each server keeps its own and the handler keeps them in step.

The metadata database can be the site's database server or a separate one:

| Backend (`[eZDFSClusteringSettings] DBBackend`) | Comes from |
|---|---|
| `eZDFSFileHandlerMySQLiBackend` | the kernel (shipped default), MySQL and MariaDB |
| `eZDFSFileHandlerPostgresqlBackend` | the package `ezpostgresqlcluster` |
| `eZDFSFileHandlerOracleBackend` | the `ezoracle` extension |

A site on SQLite cannot be the site database of a multi-server setup at all.

## What your teams provide

| Team | Provides |
|---|---|
| Storage | an NFS export mounted at the same path on every web server, writable by the site's user, with its own redundancy and backups |
| Database | the `ezdfsfile` table in a metadata database, backed up and monitored like the site's database |
| Web or platform | cluster settings in the global `settings/override/file.ini.append.php`, the `CLUSTER_*` constants for `index_cluster.php`, rewrite rules for files |
| Security | `config.cluster.php` and the cluster settings hold database credentials; keep them out of version control and downloads |

Cluster settings must be in the global override file, not in an extension or a siteaccess, because of the order in
which settings are read.

## Maintenance commands

| Command | What it does |
|---|---|
| `php bin/php/clusterize.php` | moves the files of `var/` into the cluster (once, at setup); `-u` moves them back |
| `php bin/php/dfscleanup.php -S` and `-B` | checks the mount against the table and the other way; `-D` deletes what does not exist |
| `php bin/php/clusterpurge.php --dry-run` | shows the expired cluster files it would remove; without `--dry-run` removes them |

Run `dfscleanup.php` with `-S` and `-B` first and read the result before you add `-D`. `clusterpurge.php` is also the
cronjob part `cluster_maintenance`; the shipped settings keep it off the buttons of **Setup > Cronjobs** because it is
destructive. Run it on a schedule from one server only.

## Risks for the continuity plan

- **The mount.** If the NFS mount goes away, every server fails to read files. Monitor the mount and the NFS server as
  part of the site.
- **The metadata database** is on the path of every file read.
- **Consistency after an incident.** Run `dfscleanup.php -S` and `-B` after an incident and before a migration.
- **Backups.** Back up the NFS export and the metadata database together, in the same window as the site's database
  ([What to back up](../backups-and-recovery/what-to-back-up.md)).
- **Upgrades.** Apply the `dbupdate-cluster-...` file of every upgrade step that has one
  ([11 Upgrading](../../../../install/11-upgrading.md)).

## Checklist

- [ ] One web server only: the cluster handler is not needed.
- [ ] Several: the NFS export is mounted at the same path everywhere and has its own redundancy.
- [ ] `ezdfsfile` exists in a metadata database that is backed up and monitored.
- [ ] Cluster settings are in the global override; credentials are not in version control.
- [ ] `dfscleanup.php -S` and `-B` report no differences after `clusterize.php`.
- [ ] `clusterpurge.php` runs from one server only.

## Related pages

- [Availability](README.md) index of this folder
- [Several web servers behind a load balancer](several-web-servers-behind-a-load-balancer.md)
- [Storage layout](../../../hosting/operations/storage-layout.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
