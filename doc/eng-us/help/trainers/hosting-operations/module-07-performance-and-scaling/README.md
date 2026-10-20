---
title: "Module 7: Performance and scaling"
audience: trainer, hosting, admin
category: training
order: 70
summary: The seventh module of Hosting and operations, benchmarking a site, caching layers with HTTP headers, proxies and CDN, a Valkey or Redis cache, and several web nodes with clustering. Four lessons, 2 hours 55 minutes.
keywords: hosting and operations, module 7, performance, scaling, benchmark, exp:benchmark, caching layers, HTTP cache headers, CDN, Varnish, Valkey, Redis, cluster, several web nodes
type: help
---
# Module 7: Performance and scaling

Performance work starts with a number and ends with a decision. This module teaches learners to measure a site with
`exp:benchmark`, to know every caching layer and keep shared caches safe, to add a Valkey or Redis cache, and to design
a site for several web nodes when one machine is no longer enough.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Duration | 2 hours 55 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate to advanced |
| Format | Hands-on (7.1 to 7.3) and a design exercise (7.4) |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [7.1 Benchmarking a site](benchmarking.md) | 45 min | Run warm and cold benchmarks, save a baseline, read the exit code |
| [7.2 Caching layers, HTTP headers, proxies and CDN](caching-layers-proxies-and-cdn.md) | 45 min | Read cache headers, switch on anonymous cacheable headers |
| [7.3 A Valkey or Redis cache](valkey-cache.md) | 40 min | Install the extension, check keys with a unique prefix |
| [7.4 Several web nodes and clustering](several-web-nodes-and-clustering.md) | 45 min | Design a two-node site and its operations |

## Preparing this module

- A training installation per learner, served by Velocity, on a machine quiet enough for benchmarks; agree a small
  `--requests` value if machines are shared.
- For lesson 7.3: a Valkey or Redis server and the PHP `redis` extension on each learner's virtual machine, Composer
  access, and one key prefix per learner. Without them, demonstrate on your own machine.
- Paper or a shared document for the design exercise of lesson 7.4.
- Rehearse lesson 7.2's header change and its removal; leave no override behind.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 7.1 (45 min) |
| 0:45 | Lesson 7.2 (45 min) |
| 1:30 | Break (15 min) |
| 1:45 | Lesson 7.3 (40 min) |
| 2:25 | Lesson 7.4 (45 min) |
| 3:10 | End (2 h 55 min of lessons plus one break) |

## Related pages

- [Hosting and operations: course overview](../README.md)
- [Performance](../../../hosting/performance/README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
