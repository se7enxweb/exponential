---
title: Performance and scaling
audience: dba
category: database-administration
order: 40
summary: How the load of an Exponential site reaches its database and how to scale it - the cache layers that keep most requests away, what still reaches the database, persistent connections and Velocity's persistent workers, memory per worker on SQLite, connection counts on server engines, replicas, MongoDB indexes, Oracle pooling - and the order in which to apply the levers.
keywords: performance, scaling, caching layers, Velocity response cache, view cache, template-block cache, query cache, static cache, persistent connection, UsePersistentConnection, Velocity workers, cache_size, max_connections, replicas, UseSlaveServer, DRCP, MongoDB indexes, ANALYZE, statistics
type: help
---
# Performance and scaling

This page is for database administrators who size the database of an Exponential site or look for the reason it is
slow. It answers: how much of the traffic reaches the database at all, what does, and which levers help each engine.
Sources: [Database support, performance and scaling](../database-support.md#performance-and-scaling),
[What carries the read load](../../install/09-databases.md#what-carries-the-read-load-the-caching-layers) and
[10.12 Performance tuning](../../install/10-after-installing.md#1012-performance-tuning).

## Contents

1. [Most requests never reach the database](#most-requests-never-reach-the-database)
2. [What does reach it](#what-does-reach-it)
3. [Connections and workers](#connections-and-workers)
4. [Per engine](#per-engine)
5. [The levers, in order](#the-levers-in-order)
6. [Checklist](#checklist)

## Most requests never reach the database

| Layer | Setting | What a hit costs |
|---|---|---|
| Velocity response cache | `settings/velocity.ini [CacheSettings] Enabled=enabled`, `DefaultTtl=30` | answered by Velocity's parent process: no PHP worker, no database; anonymous visitors only |
| Content view cache | `site.ini [ContentSettings] ViewCaching=enabled` | the main area of a page from a file |
| Template-block cache | `site.ini [TemplateSettings] TemplateCache=enabled` | menus and footers from files |
| SQL query cache | `querycache.ini Mode=off` by default | a repeated `SELECT` from APCu or a file; not on MongoDB ([The SQL query cache](sql-query-cache.md)) |
| Operating system file cache | | database pages from memory after the first read |

The static cache (`[ContentSettings] StaticCache`) is disabled in the shipped installer and should stay off: with it on,
every publish regenerated the affected pages over HTTP inside the editor's request.

## What does reach it

- the first views after a cache clear or a publish;
- signed-in editors and the administration;
- search (the built-in engine reads `ezsearch_*`);
- publishing, workflows, cronjobs, imports.

Size the database for these, not for the total page views. After a deploy that clears caches, expect a short burst
(the warm-up); `exp:preload` warms a site's pages on purpose ([Preloading caches](../../guides/preloading-caches.md)).

## Connections and workers

- Under PHP-FPM each worker opens its own connection when a request needs one. `UsePersistentConnection=enabled`
  keeps it for the life of the worker (MySQL, PostgreSQL, Oracle).
- Under Velocity with persistent workers (`settings/velocity.ini [ServerSettings] ForkPerRequest=disabled`, the
  shipped value) each worker keeps the application, its connection and its caches warm. The pool size is
  `[ServerSettings] Workers` (shipped `4`), optionally dynamic with `SpareWorkers` and `IdleWorkerTimeout`
  ([Velocity workers and pools](../hosting/servers/velocity-workers-and-pools.md)).
- Count the connections: PHP-FPM workers plus Velocity workers plus cron and console processes, per site, against the
  server's limit (`max_connections` on MySQL and PostgreSQL, sessions or a DRCP pool on Oracle).

## Per engine

| Engine | Levers |
|---|---|
| SQLite | reads scale with the number of processes; writes queue. Count up to 64 MB of page cache per process that opened the database (`SQLitePragmas[]=cache_size=-16384` lowers it to 16 MB). `ANALYZE` after imports ([SQLite maintenance](sqlite-maintenance.md)) |
| PostgreSQL | row-level locking; persistent connections; your usual `shared_buffers` and autovacuum tuning; replication is external |
| MariaDB, MySQL | InnoDB row locking; persistent connections; `innodb_buffer_pool_size` sized for the working set; read replicas with `UseSlaveServer` ([Read replicas](mysql-read-replicas.md)) |
| MongoDB | performance follows the number of statements per page; create the indexes from `bin/mongodb/create_indexes.js`, without which subtree queries and URL alias lookups scan whole collections |
| Oracle | a new dedicated session costs tens of milliseconds: `Persistent=enabled` and DRCP; the `Prefetch` settings; statistics with `gather-stats` ([Oracle tuning](oracle-connections-and-tuning.md)) |

Server parameters such as buffer pools are your engine's own; Exponential does not set or require particular values.

## The levers, in order

1. **Caches on**: view cache, template-block cache, Velocity's response cache; check them in **Setup > System
   information**, card Caches.
2. **Statistics current**: `ANALYZE` (SQLite, PostgreSQL), `gather-stats` (Oracle); MongoDB indexes created.
3. **Find the statements**: [Finding slow queries](finding-slow-queries.md); fix repeated queries at their source.
4. **Query cache** `request`, then `shared`.
5. **Persistent connections** or Velocity's persistent workers.
6. **Replicas** for MySQL and MariaDB; a bigger server or another engine when writes queue
   ([Choosing an engine](choosing-an-engine.md)).

Measure each step with `exp:benchmark --save` and `--baseline` ([Benchmark](../../features/6.0/benchmark.md)).

## Checklist

- [ ] Caches enabled; static cache off.
- [ ] Connection count of all workers below the server's limit.
- [ ] SQLite memory sized for `cache_size` per worker.
- [ ] Statistics and indexes current.
- [ ] Every change measured before and after.

## Related pages

- [Caching layers](../hosting/performance/caching-layers.md), [PHP-FPM tuning](../hosting/performance/php-fpm-tuning.md)
- [Sizing memory, CPU and disk](../hosting/basics/sizing-memory-cpu-and-disk.md)
- [Monitoring the database](monitoring-the-database.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
