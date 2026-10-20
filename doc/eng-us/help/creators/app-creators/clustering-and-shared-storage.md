---
title: Clustering and shared storage
audience: app-creator, developer, admin
category: app-creation
order: 620
summary: Run Exponential on several web servers - the cluster file handlers (eZFSFileHandler, eZDFSFileHandler) in file.ini, the DFS mount and its database, sessions, cron and queue workers, and how application code stays cluster-safe.
keywords: cluster, clustering, eZDFSFileHandler, eZFSFileHandler, file.ini, DFS, NFS, shared storage, load balancer, sessions, cluster-safe code, eZClusterFileHandler, app creator
type: help
---
# Clustering and shared storage

This page is for application creators and administrators whose site runs on more than one web server. All servers must
see the same files and the same database; Exponential's cluster file handlers make the storage shared.

## Contents

1. [The file handlers](#the-file-handlers)
2. [Setting up DFS](#setting-up-dfs)
3. [What else must be shared](#what-else-must-be-shared)
4. [Cluster-safe application code](#cluster-safe-application-code)

## The file handlers

`settings/file.ini [ClusteringSettings] FileHandler`:

| Handler | Use |
|---|---|
| `eZFSFileHandler` (shipped) | one server, files on the local disk |
| `eZDFSFileHandler` | several servers: files on a shared mount (NFS) with a database table that knows each file's state |

## Setting up DFS

`file.ini [eZDFSClusteringSettings]` names the shared mount (`MountPointPath`), the database backend (`DBBackend`, for
example `eZDFSFileHandlerMySQLiBackend`), the DFS backend and the cluster database's host, name and user (retries:
`DBConnectRetries`, `DBExecuteRetries`). Put the password in `settings/override/`. Then move existing files into the
cluster with the kernel's tools (`bin/php/clusterize.php`) and purge with `clusterpurge` in cron
(see `doc/eng-us/examples/commands/` for the commands).

## What else must be shared

| Shared thing | How |
|---|---|
| The database | one server or a database cluster |
| Code and settings | the same release deployed to every server |
| Sessions | the database session handler, so any server can read them ([Sessions](../../../guides/sessions.md)) |
| Content jobs | the job directory shared ([Content jobs](content-jobs.md)) |
| Cron | on one server, or with locks |
| Queue workers | any number of servers; expqueue leases jobs to one worker |
| Velocity response caches | per server; clear on every server after a deploy |
| The proxy | the load balancer's address in `TrustedProxies[]` |

## Cluster-safe application code

- Write and read files through `eZClusterFileHandler::instance( $path )` (`storeContents()`, `fetchContents()`,
  `processCache()`), never with `file_put_contents()` into `var/` ([Caching in applications](caching-in-applications.md)).
- Store uploads through the datatypes, which use the cluster handler.
- Do not keep state on one server's disk or in one process's memory.
- Make background work idempotent and locked, so two servers do not do it twice.

## Related pages

- [Scaling applications](scaling-applications.md)
- [Multi-site log and cache paths](../../../features/6.0/multi-site-log-and-cache-paths.md)
- [Missing files](../../../guides/missing-files.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
