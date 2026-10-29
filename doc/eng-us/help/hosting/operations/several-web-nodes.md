---
title: Several web nodes
audience: hosting, admin
category: hosting
order: 40
summary: What changes when more than one web server serves one Exponential site: the database, shared files, sessions, the caches on each node, trusted proxies, cron, deploys, maintenance mode and logs, with a checklist.
keywords: load balancer, several servers, web nodes, scaling, horizontal scaling, sessions, sticky sessions, TrustedProxies, X-Forwarded-For, replicas, UseSlaveServer, Valkey, Redis, cluster
type: help
---
# Several web nodes

This page is for system administrators and product managers planning an offer in which several web servers ("nodes")
serve one Exponential site behind a load balancer. It answers: which parts of a site must be shared between the nodes,
which stay local to each one, and what an operator has to do differently for cron, deploys, maintenance and logs.

A single machine with enough CPU and memory is the simpler choice for most sites; read
[Sizing memory, CPU and disk](../basics/sizing-memory-cpu-and-disk.md) and the caching pages first. Use several nodes
when one machine is not enough or when the offer promises that a node can fail without taking the site down.

## Contents

1. [What must be shared, what stays local](#what-must-be-shared-what-stays-local)
2. [The database](#the-database)
3. [Files](#files)
4. [Sessions](#sessions)
5. [Caches on each node](#caches-on-each-node)
6. [The load balancer and trusted proxies](#the-load-balancer-and-trusted-proxies)
7. [Cron, deploys, maintenance and logs](#cron-deploys-maintenance-and-logs)
8. [Checklist](#checklist)

## What must be shared, what stays local

| Part of the site | Shared or local | How |
|---|---|---|
| Database | shared | a database server all nodes reach |
| Uploaded files (`var/storage`) | shared | the DFS cluster file handler and an NFS mount |
| Settings, code, extensions, designs | identical on every node | the same deployment on each node |
| Sessions | shared, or the user kept on one node | the database session handler, or sticky sessions |
| Caches (`var/cache`, view cache, HTTP cache) | local per node, kept in step | the cluster handler, or a shared cache backend |
| Logs (`var/log`) | local per node | collect them centrally if you need one view |
| Cronjobs | one node | run the crontab on one node only |

## The database

A multi-node site needs a database server. From the supported databases, in the official order (1. SQLite 3, the
default · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server is known to be
possible with additional work at this time, with no driver in 6.0.x), every engine except SQLite supports several web
servers on one database ([Database support, at a glance](../../database-support.md#at-a-glance)). SQLite's WAL mode
uses shared memory, so all processes must be on one machine; "several web servers sharing one database file over NFS
are not" supported ([chapter 9](../../../install/09-databases.md#wal-readers-and-the-writer-side-by-side)).

For read-heavy sites on MySQL or MariaDB, `site.ini [DatabaseSettings] UseSlaveServer` sends read queries to replicas
(`SlaveServerArray[]` and the related keys); the shipped value is `disabled`
([Performance and scaling](../../database-support.md#performance-and-scaling)).

## Files

Uploaded images and files must be visible to every node. The kernel's way is the DFS cluster file handler: the files
on a shared NFS export, their metadata in a database table, cache files copied to each node. Each node keeps its own
`var/` directory; only the mount is shared. Everything about it is on
[Clustering and shared storage](clustering-and-shared-storage.md).

> **Warning**
> Do not simply put the whole `var/` directory on a network share for all nodes. The eZDFS documentation says the var
> directories "should in NO CASE be shared among instances"; the cluster handler keeps them in step itself.

## Sessions

`site.ini [Session] Handler` decides where sessions are kept ([Sessions](../../../guides/sessions.md)):

| Handler | Where | Several nodes |
|---|---|---|
| empty (default): `ezpSessionHandlerPHP` | PHP's own session storage, usually files on the node | each node knows only its own sessions |
| `ezpSessionHandlerDB` | the `ezsession` table of the site's database | every node sees every session |

Two ways to make sign-in work across nodes:

- **Sessions in the database.** Set `Handler=ezpSessionHandlerDB`; every node reads the same table. The administration
  page **Setup > Sessions** can then also list and remove sessions.
- **Keep each user on one node.** A load balancer that routes by the session cookie keeps a user on the node that
  holds the session. `[Session] CookieAlwaysAddToHttpResponse=enabled` sends the session cookie with every response
  once a session runs, for such a balancer; those responses go out with `Cache-Control: private`. A node that fails
  then signs its users out.

## Caches on each node

Caches answer most requests, so each node has its own. What a host has to plan for is that a cache clear on one node
reaches the others:

- **With the DFS cluster**, cache files and `expiry.php` (the timestamps that invalidate caches) are cluster files, so
  a clear on one node is seen by all ([Cluster (DFS)](../../../features/6.0/multi-site-log-and-cache-paths.md#cluster-dfs)).
- **The caches in front of PHP** are local: the HTTP cache in `var/<site>/cache/exphttpcache` and Velocity's response
  cache. Clear them on every node after a change (`./console exp:velocity cache clear --apply` for Velocity;
  [Caches](../../../guides/caches.md)).
- **A shared cache backend.** The extension `sevenx_valkey_cache` keeps template blocks, the content view cache and the
  compiled INI cache in Redis or Valkey, which "makes INI caches safe for clusters"
  ([sevenx_valkey_cache](../../../features/6.0/extensions/sevenx_valkey_cache.md)). See
  [Valkey and Redis](../performance/valkey-and-redis.md).

A reverse proxy or CDN in front of all nodes is a further, shared cache layer; see
[Varnish and reverse proxies](../performance/varnish-and-reverse-proxies.md).

## The load balancer and trusted proxies

Behind a load balancer every request arrives from the balancer's address. Exponential believes forwarded headers only
from the proxies it is told to trust ([8.8 Behind a reverse proxy](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy),
[Trusted proxies](../../../bc/6.0/trusted-proxies.md)):

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# replace the shipped list (127.0.0.1 and ::1) with the balancer's addresses
TrustedProxies[]
TrustedProxies[]=192.0.2.10
# read the visitor's address from X-Forwarded-For, from a trusted proxy only
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

- The balancer must set `X-Forwarded-Proto: https` when it terminates TLS, so generated links and the `Secure` flag of
  the session cookie are right.
- It should pass the original `Host` (or `X-Forwarded-Host`).
- Velocity has its own list of trusted proxies, `Q.webserver.proxy.trusted` in the engine configuration, because it
  decides before the kernel runs; set it as well when Velocity serves the nodes.

The address `192.0.2.10` above is a documentation address; use your balancer's.

## Cron, deploys, maintenance and logs

- **Cron from one node.** Install the crontab on one node only, or several nodes run the same jobs. Each script takes a
  lock (`eZMutex`), but that lock is a locked file in the node's own cache directory, so it does not stop another node. To pause parts for all nodes, point `cronjob.ini [PauseSettings]
  Directory` at a shared directory ([Pausing cronjob parts](../../../features/6.0/cronjob-pause.md)).
- **Deploys on every node.** Run the deploy (`./console exp:velocity deploy`, see [Deploying changes](deploying-changes.md))
  on each node, so all of them run the new code and rebuild their caches. Take one node out of the balancer while it
  is deployed if the change must not be served half way.
- **Maintenance on every node.** The maintenance state is the file `var/maintenance.json` of an installation. With a
  `var/` directory per node, switch it on and off on every node ([Maintenance mode](maintenance-mode.md)).
- **Logs per node.** Each node writes its own `var/log`. An error reference from a visitor may be in the log of
  another node than the one you look at ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).
  Collect logs centrally, or forward the audit trail with a sink (see [Security log](security-log.md)).
- **Monitoring per node.** Check each node directly as well as the balanced address
  ([Health checks and uptime](health-checks-and-uptime.md)).

## Checklist

- [ ] The site's database is a server (not SQLite) reachable from every node.
- [ ] Uploaded files are shared through the DFS cluster; each node has its own `var/`.
- [ ] Sessions are in the database, or the balancer keeps each user on one node.
- [ ] `TrustedProxies[]` lists the balancer, and `ClientIpByCustomHTTPHeader` is set when the visitor's address matters.
- [ ] The crontab runs on one node; the pause directory is shared.
- [ ] Deploy, cache clearing and maintenance runbooks name every node.
- [ ] Logs are collected or the support team knows how to search every node.

## Related pages

- [Clustering and shared storage](clustering-and-shared-storage.md)
- [Choosing a hosting model](../models/choosing-a-hosting-model.md), [Kubernetes](../models/kubernetes.md)
- [Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md)
- [Caching layers](../performance/caching-layers.md)
- [Database support](../../database-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
