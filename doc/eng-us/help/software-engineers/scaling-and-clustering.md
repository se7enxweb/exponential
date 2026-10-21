---
title: Scaling and clustering
audience: software-engineer, admin
category: software-engineering
order: 350
summary: How an Exponential site scales - up on one machine with Velocity and caches, and out to several web nodes - with what must be shared (database, files, sessions), what stays per node (caches, logs, workers), the DFS cluster file handler, read replicas, trusted proxies, cron on one node, deploys across nodes, and the limits of each approach.
keywords: scaling, horizontal scaling, vertical scaling, cluster, several web nodes, load balancer, eZDFSFileHandler, NFS, sessions, read replicas, UseSlaveServer, Valkey, Redis, trusted proxies, cron, software engineer
type: help
---
# Scaling and clustering

This page is for engineers who plan capacity or a high-availability setup. The operator's guide, with a checklist, is
[Several web nodes](../hosting/operations/several-web-nodes.md); the cluster file handler is described in
[Clustering and shared storage](../hosting/operations/clustering-and-shared-storage.md).

## Contents

1. [Scale up first](#scale-up-first)
2. [Scaling out: what must be shared](#scaling-out-what-must-be-shared)
3. [Files: the cluster file handler](#files-the-cluster-file-handler)
4. [Sessions](#sessions)
5. [Database scaling](#database-scaling)
6. [The edge: load balancer and proxies](#the-edge-load-balancer-and-proxies)
7. [Operations across nodes](#operations-across-nodes)
8. [Limits](#limits)

## Scale up first

A single machine with enough CPU and memory is the simpler choice for most sites. Before adding nodes:

1. Serve with Velocity and its response cache for anonymous traffic ([Application servers](application-servers.md)).
2. Switch on the caches that fit the traffic: the role-aware HTTP cache for signed-in pages, the SQL query cache for
   what cannot be stored whole ([Caching layers for engineers](caching-layers-for-engineers.md)).
3. Size the worker pool by PSS, not RSS ([Velocity persistent workers](velocity-persistent-workers.md)).
4. Measure ([Performance measurement and benchmarks](performance-measurement-and-benchmarks.md)).

Scale out when one machine is not enough, or when a node must be able to fail without taking the site down.

## Scaling out: what must be shared

| Part | Shared or local | How |
|---|---|---|
| Database | shared | a database server; every supported engine except SQLite |
| Uploaded files (`var/storage`) | shared | the DFS cluster file handler and an NFS mount |
| Code, settings, extensions, designs | identical on every node | the same deployment on each node |
| Sessions | shared, or the user kept on one node | the database session handler, or sticky sessions |
| Caches | local per node, kept in step | the cluster handler, or Redis or Valkey |
| Logs | local per node | collect centrally if you need one view |
| Cronjobs | one node | run the crontab on one node only |

Do not put the whole `var/` directory on a network share for all nodes; the cluster handler keeps the nodes in step
itself.

## Files: the cluster file handler

Every cache and storage file is written through `eZClusterFileHandler`. The shipped handler is `eZFSFileHandler`
(local files); for several nodes it is `eZDFSFileHandler` (`kernel/private/classes/clusterfilehandlers/`): files on a
shared NFS export, their metadata in a database table, cache files copied to each node. `bin/php/clusterize.php`
moves an installation's files into the cluster. Code that writes files directly instead of through the handler breaks
clustering; reviews should catch it ([Code review checklist](code-review-checklist.md)).

## Sessions

`site.ini [Session] Handler` decides where sessions live: empty (the default, `ezpSessionHandlerPHP`) keeps them in
PHP's storage on each node; `ezpSessionHandlerDB` keeps them in the `ezsession` table, visible to every node
([Sessions](../../guides/sessions.md)).

## Database scaling

- SQLite cannot serve several machines: all processes must be on one machine.
- On MySQL or MariaDB, `site.ini [DatabaseSettings] UseSlaveServer` sends read queries to replicas
  (shipped `disabled`) ([Database support, Performance and scaling](../database-support.md#performance-and-scaling)).
- PostgreSQL brings replication and point-in-time recovery of its own.

## The edge: load balancer and proxies

Behind a load balancer the kernel sees the balancer's address. List it in `site.ini [HTTPHeaderSettings]
TrustedProxies[]`, or forwarded headers (`X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Host`) are ignored and
HTTPS detection, client addresses, the sign-in lockout and the audit log are wrong
([Trusted proxies](../../bc/6.0/trusted-proxies.md)). Exponential sends no purges to a CDN or caching proxy; one that
stores pages must be told what to store and for how long.

## Operations across nodes

| Task | Across nodes |
|---|---|
| Deploy | `exp:velocity deploy` covers PHP-FPM and Velocity on one machine; deploy each node in turn |
| Cache clear | clear on every node, or use a shared cache backend |
| Maintenance mode | the switch is a file in `var/`; set it on every node |
| Logs | collect them, or read them per node in the log viewer |

## Limits

- No built-in purge for external proxies.
- No migration runner; a schema change is applied once, from one node.
- Per-node caches can briefly disagree after a change; plan the order of a deploy.

## Related pages

- [Scaling applications](../creators/app-creators/scaling-applications.md)
- [Clustering and shared storage, for application creators](../creators/app-creators/clustering-and-shared-storage.md)
- [Multi-site installations](multi-site-installations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
