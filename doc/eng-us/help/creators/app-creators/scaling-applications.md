---
title: Scaling applications
audience: app-creator, developer, admin
category: app-creation
order: 600
summary: Grow an Exponential application from one server to many - measure first, caches and Velocity workers, the database, background work, search on Solr, shared storage and clustering, and what each step costs.
keywords: scaling, performance, Velocity workers, caching, database, read load, background jobs, Solr, cluster, DFS, load balancer, Valkey, Redis, app creator
type: help
---
# Scaling applications

This page is for application creators whose application must serve more users or more data. Scale in steps, measuring
before each one: most sites never need more than the first three.

## Contents

1. [Measure first](#measure-first)
2. [The steps, in order](#the-steps-in-order)
3. [Several web servers](#several-web-servers)
4. [Application design that scales](#application-design-that-scales)

## Measure first

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
php bin/php/console exp:benchmark kernel --repeat=30 --allow-root-user
```

`exp:benchmark` gives median, p90 and p99 per page; `kernel` mode times boot, render, INI load, fetches and database
round trips ([Benchmarking](../../../guides/benchmarking.md), [Profiling and performance](profiling-and-performance.md)).

## The steps, in order

| Step | Effect | Read |
|---|---|---|
| 1. Caches on and keyed right | most requests never render | [Caching in applications](caching-in-applications.md) |
| 2. Velocity with its response cache | anonymous repeat requests answered without PHP; persistent workers keep the application warm | [Velocity response cache](../../../features/6.0/velocity-response-cache.md) |
| 3. Slow work out of requests | requests stay fast under load | [Background work](background-work-patterns.md) |
| 4. The database tuned; query cache | fewer and cheaper queries | [Databases](../../../install/09-databases.md), [SQL query cache](../../../bc/6.0/sql-query-cache.md) |
| 5. Search on Solr | search load off the database | [Search with ezfind and Solr](../website-creators/search-with-ezfind-and-solr.md) |
| 6. Shared cache backend | content and INI caches in Redis or Valkey | [sevenx_valkey_cache](../../../features/6.0/extensions/sevenx_valkey_cache.md) |
| 7. Several web servers | horizontal capacity | [Clustering and shared storage](clustering-and-shared-storage.md) |

Velocity's worker pool and its dynamic sizing are set in `velocity.ini`
([8.3.6 Workers](../../../install/08-serving-the-site.md#836-workers-pool-size-the-dynamic-pool-and-the-zygote)).

## Several web servers

Multiple web servers need: one database (or a cluster of it), shared file storage through the cluster file handler,
the same code and settings on every server, sessions every server can read (the database session handler), a load
balancer listed in `TrustedProxies[]`, and cron and queue workers on one designated server (or locked).

## Application design that scales

- Every list paged and limited; counts with count queries.
- No per-request work proportional to the size of the site (whole-tree fetches in the pagelayout).
- Writes batched, transactions short.
- Idempotent background jobs, so more workers do not mean duplicates.
- Caches keyed by what changes, cleared by events rather than by time where possible.

## Related pages

- [Performance and caching for websites](../website-creators/performance-and-caching.md)
- [Clustering and shared storage](clustering-and-shared-storage.md)
- [10.12 Performance tuning](../../../install/10-after-installing.md#1012-performance-tuning)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
