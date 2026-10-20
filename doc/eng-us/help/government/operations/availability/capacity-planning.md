---
title: Capacity planning
audience: government, hosting, admin, project-manager
category: public-sector
order: 120
summary: For public-sector IT teams sizing servers for an Exponential site. The figures the repository actually states (memory limits, Velocity workers, OPcache, disk thresholds, measured cache throughput), what drives load, how to measure your own site with exp:benchmark, and how to plan for peaks.
keywords: capacity planning, sizing, memory, CPU, disk, Velocity workers, OPcache, APCu, exp:benchmark, exp:system:info, peak load, traffic surge, procurement, server specification
type: help
---
# Capacity planning

This page is for IT teams and project managers in public bodies who must specify servers for an Exponential site,
often for a procurement or a budget request. It answers which figures the repository actually gives, what drives
memory, CPU and disk use, and how to measure your own site so the specification rests on evidence. It gives no figures
of its own; the installation book says the same: "The documentation gives few hard numbers, and this book does not
invent others" ([Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md)).

## Contents

1. [The figures the repository gives](#the-figures-the-repository-gives)
2. [What drives load](#what-drives-load)
3. [Measure your own site](#measure-your-own-site)
4. [Planning for peaks](#planning-for-peaks)
5. [Writing a server specification](#writing-a-server-specification)
6. [Checklist](#checklist)

## The figures the repository gives

| Item | Value | Source |
|---|---|---|
| PHP `memory_limit`, installer floor | 64M | `settings/setup.ini` |
| PHP `memory_limit`, health check | below 128M a failure, below 256M a warning | [System information](../../../../guides/system-information.md) |
| One Velocity worker | about 10 MB of private memory (measured) | `settings/velocity.ini` |
| Velocity pool | `Workers=4` shipped | `settings/velocity.ini` |
| OPcache | `memory_consumption=256`, `max_accelerated_files=20000` | `velocity.ini [PHPSettings]` |
| Free disk, health check | under 1 GB or 5 % a failure; under 5 GB or 10 % a warning | [System information](../../../../guides/system-information.md) |
| SQLite `VACUUM` | about twice the database's size in free disk | [9.2](../../../../install/09-databases.md#analyze-vacuum-and-the-wal-file) |
| Cached pages under Velocity | about 0.3 ms of CPU each; about 3,500 a second on a 12-core machine (measured) | `settings/velocity.ini` |

All from [Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md), which also lists the
MongoDB figures and the log rotation limits. Measured figures are from the project's own machines; yours will differ.

## What drives load

- **Cached pages cost little.** Anonymous visitors are answered by the response cache or the HTTP cache without PHP
  ([Velocity response cache and HTTP cache](velocity-response-cache-and-http-cache.md)).
- **Rendered pages** (first views after a clear, signed-in editors, searches, forms) are where CPU goes.
- **PHP processes** use up to their `memory_limit` at peak; imports, package installs and image work need the most.
- **Disk** grows with uploads in `var/storage`, which is usually the main factor; caches and logs are bounded by
  rotation and can be cleared.
- **Cron and image variations** add bursts.

## Measure your own site

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
php bin/php/console exp:system:info --json --allow-root-user
du -sh var/storage var/cache var/log
```

`exp:benchmark` requests the front page, menu pages, the search page and the admin login page and prints median, p90
and p99; it is polite by default ([Benchmarking](../../../../guides/benchmarking.md)). Measure the same request
before and after a change; "a single page load proves nothing" ([Deploying](../../../../guides/deploying.md)).
Run it against a test copy with production content, not against the live site at a busy time.

## Planning for peaks

Public-sector sites have predictable peaks (deadlines, election results, school enrollment) and unpredictable ones
(storms, emergencies). For both:

- make sure the pages that will be visited are cached and warm ([Cache warming after changes](cache-warming-after-changes.md));
- avoid full cache clears and deploys right before a known peak;
- know how to raise Velocity's `Workers` and restart, and measure again after each change;
- consider a lightweight, mostly static information page for emergencies ([Static cache](static-cache.md)).

## Writing a server specification

For a procurement or budget request, write the specification from measurements, and label the rest:

| Item | How to state it |
|---|---|
| CPU cores and memory | measured on a test copy, plus headroom you choose; mark headroom as "example assumption" until measured |
| Disk for `var/storage` | today's size (`du -sh`) and expected growth from your content plans |
| Database size | from **Setup > System information**, Database card |
| PHP workers | Velocity's `Workers` or the PHP-FPM pool size you tested |
| Backups | room off the machine for the generations you decided |

## Checklist

- [ ] Production pools have `memory_limit` 256M or more.
- [ ] Velocity's `Workers` is set explicitly and tested.
- [ ] Disk monitoring uses the System information thresholds.
- [ ] A benchmark of a test copy is saved as the reference.
- [ ] The server specification separates measured figures from assumptions.

## Related pages

- [Availability](README.md) index of this folder
- [Velocity workers and pools](../../../hosting/servers/velocity-workers-and-pools.md)
- [Benchmarking a hosted site](../../../hosting/performance/benchmarking-a-hosted-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
