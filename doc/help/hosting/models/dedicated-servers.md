---
title: Dedicated servers
audience: hosting, admin
category: hosting
order: 50
summary: How to use a dedicated server for Exponential: one large site or many isolated ones, sizing the Velocity worker pool and instances from the measured figures the sources give, the database on the same machine or beside it, and what to measure before you commit to hardware.
keywords: dedicated server, bare metal, sizing, Velocity, workers, instances, SO_REUSEPORT, dynamic pool, PSS, memory, PostgreSQL, SQLite, several sites, isolation, benchmark
type: help
---
# Dedicated servers

This page is for hosting companies that put Exponential on a dedicated (physical) server: either one large customer
site on a machine of its own, or many customer sites on one big machine. It answers how the project's sources say
to use the resources of such a machine, which figures they give, and what you must measure yourself.

## Contents

1. [Two ways to use a dedicated server](#two-ways-to-use-a-dedicated-server)
2. [Using the cores: workers and instances](#using-the-cores-workers-and-instances)
3. [Memory: read PSS, not RSS](#memory-read-pss-not-rss)
4. [The database on a dedicated server](#the-database-on-a-dedicated-server)
5. [Many sites on one machine](#many-sites-on-one-machine)
6. [Measuring before you buy](#measuring-before-you-buy)
7. [Checklist](#checklist)

## Two ways to use a dedicated server

| Use | Shape | Main concern |
|---|---|---|
| One large site | Velocity facing visitors, a large worker pool, possibly several instances; the database on the same machine or a second one | using all cores; database writes |
| Many customer sites | one PHP-FPM pool or one Velocity instance per site, each as its own user | isolation; fair sharing of memory and CPU |

A dedicated server is tier 1, 2 or 3 by the distribution you install, exactly as a virtual machine is
([Operating system support](../../os-support.md#the-four-tiers)). The project's own development server is an
AlmaLinux 9 machine with Plesk, running Apache with PHP-FPM 8.5 and Exponential Velocity on the same installation
([Tier 1](../../os-support.md#tier-1-tested-here)).

## Using the cores: workers and instances

Velocity's capacity is set in `settings/override/velocity.ini.append.php`, `[ServerSettings]`
([8.3.6](../../../install/08-serving-the-site.md#836-workers-pool-size-the-dynamic-pool-and-the-zygote)):

| Setting | Shipped | Meaning |
|---|---|---|
| `Workers` | `4` | the size of the pool; with `SpareWorkers`, the most it grows to |
| `SpareWorkers` | `0` | above 0: a dynamic pool that keeps this many idle workers |
| `IdleWorkerTimeout` | `60` | seconds an extra worker may stay idle before it is retired |
| `Instances` | `1` | servers sharing the same ports with `SO_REUSEPORT` (Linux and the BSDs); `Workers` and `SpareWorkers` are per instance |

Chapter 8 suggests "a good start for a production site" as a dynamic pool:

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=64
SpareWorkers=8
IdleWorkerTimeout=60
```

What the sources measured: "One server answers cached pages in its own process and is bounded by one core
(measured: about 2,950 cached pages a second over TLS; four instances, about 12,000). Rendered pages are bounded by
the workers, not by the number of instances." On a machine with many cores, `Instances` above 1 spreads the cached
traffic; the worker count decides the rendered traffic.

> **Note**
> Those figures are the project's own measurement on its own machine, quoted so you know the shape of the scaling.
> They are not a promise for your hardware. Measure your own (see [Measuring before you buy](#measuring-before-you-buy)).

With Apache or nginx and PHP-FPM, the PHP-FPM pool's process settings play the role of `Workers`; see
[PHP-FPM tuning](../performance/php-fpm-tuning.md).

## Memory: read PSS, not RSS

"A worker of a full Exponential costs about 10 to 11 MB of its own memory (measured as PSS on 12 cores and PHP 8.5);
the shared part, about 47 MB, is paid once. Judge memory by PSS or private memory, never by RSS, which counts every
shared page once per worker and overstates a pool many times."
([8.3.6](../../../install/08-serving-the-site.md#836-workers-pool-size-the-dynamic-pool-and-the-zygote))

For a dedicated server this means a large pool is cheap in memory; the limits come from CPU, the database and the
caches. Velocity's control panel shows memory as PSS
([Velocity control panel](../../../features/6.0/velocity-control-panel.md)).

Workers are also replaced on their own after `maxRequests` (1000), when the heap passes `workerMemoryCeiling`, or
when a request runs past `requestTimeout` (30 seconds, the client gets 504). These are engine settings
([Velocity worker pool](../../../specifications/6.0/velocity-worker-pool.md)).

## The database on a dedicated server

In the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server is known to be possible with additional work at this time.

- **1. SQLite 3** on the same machine "scales reads with the number of processes; writes queue". It is a full
  production database for one machine. Count "up to 64 MB of page cache per process that has opened the database"
  when you size memory for a large worker pool (`SQLitePragmas[]=cache_size=-16384` lowers it to 16 MB)
  ([Performance and scaling](../../database-support.md#performance-and-scaling)).
- **2. PostgreSQL** (and 4. MariaDB, 6. MySQL) "scale writes with row-level locking"; under Velocity each worker
  keeps its connection, so the database server must accept as many connections as there are workers across all
  instances.
- A second machine for the database is the step towards [several web nodes](../operations/several-web-nodes.md).

Most requests never reach the database: Velocity's response cache, the view cache and the template-block cache
answer first ([Caching layers](../performance/caching-layers.md)).

## Many sites on one machine

For many customers on one dedicated server, follow the isolation rule of the root `README.md`: "run one Velocity
instance per site behind a proxy, each as its own operating-system user", or use a traditional server with one
PHP-FPM pool per site ([8.1](../../../install/08-serving-the-site.md#81-the-choice-in-one-table)). One Exponential
installation with several siteaccesses is not a boundary between customers
([Multi-tenant isolation](../security/multi-tenant-isolation.md)).

Each site then has its own `var/`, its own cron lines in its own user's crontab and its own backups.

## Measuring before you buy

The installation book "gives few hard numbers, and this book does not invent others"
([2.10 Memory, CPU and disk](../../../install/02-requirements.md#210-memory-cpu-and-disk)). Measure a representative
site on candidate hardware:

```bash
php bin/php/console exp:benchmark
php bin/php/console exp:benchmark --compare=https://example.com,https://example.com:8080
```

The second form compares two addresses, for example Apache and Velocity on the same installation
([Benchmark](../../../features/6.0/benchmark.md), [Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md)).
Record the hardware, PHP version, engine, worker settings and cache state with each result.

## Checklist

- [ ] One large site or many sites decided; isolation per site for many customers.
- [ ] `Workers`, `SpareWorkers` and `Instances` set explicitly and measured.
- [ ] Memory judged by PSS; database connection limits match the worker count.
- [ ] Database in the official order, on this machine or a second one.
- [ ] Benchmarks recorded for the machine type before it is sold as a plan.
- [ ] Boot service for Velocity, monitoring and backups in place.

## Related pages

- [Choosing a hosting model](choosing-a-hosting-model.md), [VPS hosting](vps-hosting.md)
- [Sizing memory, CPU and disk](../basics/sizing-memory-cpu-and-disk.md)
- [Velocity workers and pools](../servers/velocity-workers-and-pools.md)
- [Velocity web server](../../../features/6.0/velocity-web-server.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
