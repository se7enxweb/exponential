---
title: Profiling and performance
audience: app-creator, developer
category: app-creation
order: 630
summary: Find out where an application spends its time - exp:benchmark in HTTP, kernel and micro modes, comparing against a baseline, the debug bar's timings and queries, OPcache and Velocity, and the usual causes of slow pages.
keywords: profiling, performance, exp:benchmark, kernel mode, micro mode, baseline, debug bar, SQL queries, OPcache, Velocity, slow pages, app creator
type: help
---
# Profiling and performance

This page is for application creators who make code faster. Measure, change one thing, measure again
([Benchmarking](../../../guides/benchmarking.md), [Benchmark reference](../../../features/6.0/benchmark.md)).

## Contents

1. [Measuring pages](#measuring-pages)
2. [Inside a request](#inside-a-request)
3. [Comparing fairly](#comparing-fairly)
4. [The usual causes](#the-usual-causes)

## Measuring pages

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
# change something
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
php bin/php/console exp:benchmark --cold --requests=50 --allow-root-user   # past the response caches
```

It requests the front page, menu pages, search and the admin login, 100 times each after warm-up, and prints median,
p90, p99 and more.

## Inside a request

- `exp:benchmark kernel --repeat=30` times boot, render (cold and warm), INI load, a content fetch, a database round trip
  and cache writes and reads in-process.
- `exp:benchmark micro` times pure-PHP hot paths without HTTP or a database.
- The debug bar shows each request's SQL queries, templates and timings ([Exp debug bar](../../../features/6.0/exp-debug-bar.md)).
- Under Velocity, the opcode cache profile shows why a page costs what it does
  ([Velocity and the opcode cache](../../../features/6.0/velocity-opcode-cache-and-profile.md)).

## Comparing fairly

Same machine, nothing else running, OPcache as in production, Xdebug off, and judge two servers only by runs made back to
back. Every run records its environment, and a comparison with a baseline made under another setup says so.

## The usual causes

| Symptom | Cause | Fix |
|---|---|---|
| Many queries per page | fetches in loops, `load_data_map` on lists | fetch once, limit, cache block |
| Slow first view after deploy | caches cleared | warm (`php bin/php/warm.php`) |
| Slow under Velocity after a change | workers not restarted or OPcache not revalidating | `exp:velocity deploy` |
| Slow publish | heavy work in workflows or listeners | move it to a queue |
| Slow admin lists | unbounded counts | paged queries |

## Related pages

- [Scaling applications](scaling-applications.md)
- [Caching in applications](caching-in-applications.md)
- [Performance and caching for websites](../website-creators/performance-and-caching.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
