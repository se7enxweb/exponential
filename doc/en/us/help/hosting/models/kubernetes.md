---
title: Kubernetes
audience: hosting, admin
category: hosting
order: 70
summary: What the sources say about running Exponential on Kubernetes (tier 3, expected to work) and what several replicas require from the database, the shared file storage, sessions, cron, proxies and code deploys, written as a planning list rather than as manifests.
keywords: Kubernetes, pods, replicas, persistent volume, shared storage, ReadWriteMany, PostgreSQL, SQLite, sessions, ezpSessionHandlerDB, cluster file handler, eZDFSFileHandler, CronJob, ingress, trusted proxies, Velocity
type: help
---
# Kubernetes

This page is for platform teams at hosting companies that want to run Exponential on Kubernetes. It answers what the
project's sources say about Kubernetes, and what changes when a site runs in more than one replica: the database,
shared files, sessions, cron, the proxy in front and code deploys. It does not give manifests: the sources ship none,
and every cluster differs. Treat it as the list of decisions your manifests must encode.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [One replica or several](#one-replica-or-several)
3. [The database](#the-database)
4. [Shared files](#shared-files)
5. [Sessions](#sessions)
6. [Cron](#cron)
7. [Ingress and trusted proxies](#ingress-and-trusted-proxies)
8. [Deploying code](#deploying-code)
9. [Checklist](#checklist)

## What the sources say

- **Kubernetes is tier 3**, expected to work: "it runs the containers above", with the note "one writer for SQLite"
  ([Tier 3](../../os-support.md#tier-3-expected-to-work)). Tier 3 is "a reasoned expectation, not a test result".
- "Several replicas need a database server (PostgreSQL first ...) and shared storage for `var/storage`"
  ([Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels)).
- Everything on [Containers: Docker and Podman](containers-docker-and-podman.md) applies: images, volumes, the entry
  point, secrets.

> **Note**
> If you run Exponential on Kubernetes in production, tell the project on its tracker; the operating system page asks
> for exactly that so that a tier 3 entry can move up ([The four tiers](../../os-support.md#the-four-tiers)).

## One replica or several

| | One replica | Several replicas |
|---|---|---|
| Database | any of the six, 1. SQLite 3 included | a database server |
| `var/` | one persistent volume | shared storage for `var/storage`; caches decided per replica or shared |
| Sessions | PHP's default files work | shared session storage required |
| Cron | in the pod or a CronJob | exactly one scheduler, not one per replica |
| Rolling updates | a short gap or a recreate strategy | possible; mind database schema changes |

A single replica with SQLite is the simplest Kubernetes deployment and stays within the one-writer rule. Plan the
move to several replicas as its own project.

## The database

In the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server is known to be possible with additional work at this time.

With several replicas, SQLite is out: "SQLite's WAL needs shared memory on one host, so several replicas cannot share
one database file over a network volume." The sources recommend **2. PostgreSQL** first; every server database in the
list has "Several web servers on one database: yes" ([At a glance](../../database-support.md#at-a-glance)). Under
Velocity each worker keeps its own connection, so the database must accept workers times replicas connections.

## Shared files

`var/storage` holds uploads and image variations; every replica must see the same files. Two approaches exist in the
sources:

1. **A shared file system mounted in every replica** (a volume that several pods can write), with the default file
   handler `eZFSFileHandler` (`settings/file.ini [ClusteringSettings] FileHandler`).
2. **The cluster file handler** `eZDFSFileHandler`, described in `file.ini` as "distributed filesystem handler with
   a DB overlay. Required for NFS based architectures"; its `[eZDFSClusteringSettings]` name the mount point
   (`MountPointPath`) and a database backend for the file metadata.

Clustering is covered in [Clustering and shared storage](../operations/clustering-and-shared-storage.md) and
[Several web nodes](../operations/several-web-nodes.md).

Caches (`var/cache`, `var/<site>/cache`) can be shared or local per replica; a local cache means a cache clear must
reach every replica. Per-site log and cache paths (`LogVarDir`, `CacheVarDir`) let you put logs and caches on other
file systems than storage ([Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md)).

## Sessions

By default `site.ini [Session] Handler` is empty, which means `ezpSessionHandlerPHP`: "PHP keeps them in its own
storage (files)" ([Sessions](../../../guides/sessions.md)). Files on one pod are invisible to the next, so a visitor
signed in on one replica is a stranger on another. Choose one:

- `Handler=ezpSessionHandlerDB`, which keeps sessions in the `ezsession` table of the shared database; or
- a PHP session save handler that every replica shares (configured in PHP, outside Exponential); or
- sticky sessions at the ingress, which hides the problem until a pod is replaced.

```ini
# settings/override/site.ini.append.php
[Session]
Handler=ezpSessionHandlerDB
```

## Cron

`runcronjobs.php` must run on one schedule for the site, not once per replica: several replicas running the
`frequent` part at the same minute would send notifications and run workflows twice. A Kubernetes CronJob that
starts a container from the same image, with the same volumes, database settings and user, matches the documented
crontab ([Chapter 10, Cronjobs](../../../install/10-after-installing.md#103-cronjobs)):

```text
*/5   * * * *   php runcronjobs.php -q -s site frequent
*/15  * * * *   php runcronjobs.php -q -s site
17    * * * *   php runcronjobs.php -q -s site infrequent
```

Velocity's own scheduler "does not replace the system cron for these parts out of the box".

## Ingress and trusted proxies

Behind an ingress controller or load balancer, Exponential must know which addresses may tell it the visitor's
address and protocol:

- Under PHP-FPM, list the proxies in `site.ini [HTTPHeaderSettings] TrustedProxies[]` (shipped: `127.0.0.1` and `::1`); set `ClientIpByCustomHTTPHeader`
  only together with that list ([13.10 Behind a proxy](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)).
- Under Velocity, configure the trusted proxies in Velocity's own list, `Q.webserver.proxy.trusted`; "Configure
  proxies in front of Velocity **there**" ([Trusted proxies](../../../bc/6.0/trusted-proxies.md)).
- "Make sure the application server cannot be reached around them".

> **Warning**
> A wrong trusted-proxy list either lets visitors forge their address and HTTPS state, or makes every visitor look
> like the ingress. Test with the curl check of
> [8.8 Behind a reverse proxy](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy).

## Deploying code

Velocity's workers keep the application in memory; a code change reaches them only after a restart. In Kubernetes
the natural unit is a new image rolled out to new pods. Two cautions from the sources:

- Schema changes of an upgrade (the update files in `update/database/`, one directory per engine) run once against the shared database, not per pod
  ([Upgrading customer sites](../operations/upgrading-customer-sites.md)).
- After the rollout, clear the caches that the replicas share
  ([Cache clearing and warming](../performance/cache-clearing-and-warming.md)).

## Checklist

- [ ] One replica with SQLite, or several with a database server (PostgreSQL first).
- [ ] `var/storage` shared by every replica, or the cluster file handler configured.
- [ ] Sessions shared (`ezpSessionHandlerDB` or a shared PHP handler).
- [ ] Exactly one scheduler for `runcronjobs.php`.
- [ ] Trusted proxies set for the ingress, in `site.ini` or in Velocity's list.
- [ ] Rollout procedure covers schema updates and cache clearing.
- [ ] A test installation on the cluster before any customer site, because this is tier 3.

## Related pages

- [Containers: Docker and Podman](containers-docker-and-podman.md)
- [Several web nodes](../operations/several-web-nodes.md), [Clustering and shared storage](../operations/clustering-and-shared-storage.md)
- [Varnish and reverse proxies](../performance/varnish-and-reverse-proxies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
