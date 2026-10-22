---
title: Velocity workers and pools
audience: hosting, admin
category: hosting
order: 90
summary: How to size and run Velocity's worker pool on a hosting server: fixed and dynamic pools, instances on one port, the zygote, worker replacement, persistent workers, streaming limits, memory measured as PSS, and how to find the right number by measuring.
keywords: Velocity, workers, worker pool, Workers, SpareWorkers, IdleWorkerTimeout, Instances, SO_REUSEPORT, zygote, ForkPerRequest, KeepGlobals, StatTtl, maxRequests, requestTimeout, workerMemoryCeiling, maxConnections, PSS, RSS, sizing, MaxStreams
type: help
---
# Velocity workers and pools

This page is for system administrators at hosting companies who size Velocity for customer sites and must know what
a worker costs. It answers: which settings shape the pool, how memory is measured honestly, when workers are
replaced, what the limits are, and how to find the right number for a plan without guessing. The sources are
[Chapter 8, section 8.3.6](../../../install/08-serving-the-site.md#836-workers-pool-size-the-dynamic-pool-and-the-zygote)
and the [worker pool specification](../../../specifications/6.0/velocity-worker-pool.md).

## Contents

1. [The model in short](#the-model-in-short)
2. [The settings](#the-settings)
3. [What a worker costs](#what-a-worker-costs)
4. [Fixed or dynamic pool](#fixed-or-dynamic-pool)
5. [Instances: several servers on one port](#instances-several-servers-on-one-port)
6. [When a worker is replaced](#when-a-worker-is-replaced)
7. [Persistent workers](#persistent-workers)
8. [Streams hold workers](#streams-hold-workers)
9. [Finding the number for a plan](#finding-the-number-for-a-plan)
10. [Checklist](#checklist)

## The model in short

- The parent loads the application once and forks workers; each worker serves many requests and is reset after each.
- Static files, response-cache hits and the server's own pages are answered by the parent, not by a worker.
- When every worker is busy, a request waits in the parent. It is not refused until `maxConnections` (1024) is
  reached; then the server answers `503`.
- Workers never run as root unless you allow it.

## The settings

In `settings/velocity.ini`, `[ServerSettings]` (override in `settings/override/velocity.ini.append.php`, then
`exp:velocity restart`):

| Setting | Shipped | Meaning |
|---|---|---|
| `Workers` | `4` | the size of the pool; with `SpareWorkers` set, the most it grows to. The file says: "Explicit, always" |
| `SpareWorkers` | `0` | `0` is a fixed pool; above 0 a dynamic pool that keeps this many idle workers |
| `IdleWorkerTimeout` | `60` | seconds an extra worker may stay idle before it is retired |
| `Instances` | `1` | servers sharing the same ports with `SO_REUSEPORT` (Linux and the BSDs); `Workers` and `SpareWorkers` are per instance |
| `Zygote` | empty (engine default: on) | fork new workers from a process that never held a client connection |
| `StatTtl` | `0` | seconds (at most 10) a worker may keep file facts across requests |
| `ForkPerRequest` | `disabled` | persistent workers; `enabled` gives one request per worker |
| `Nice` | `5` | the priority of the server and its workers |

Engine settings under `Q.webserver` (set through a snippet in the configuration tree, see
[Velocity for hosts](velocity-for-hosts.md#settings-and-how-to-change-them)) include `maxRequests` (1000),
`requestTimeout` (30 s), `workerMemoryCeiling` (256 MB, or three quarters of `memory_limit` if lower) and
`maxConnections` (1024).

## What a worker costs

Measured on 2026-09-26 as PSS from `/proc/<pid>/smaps_rollup`, 12-core Linux, PHP 8.5
([worker pool specification](../../../specifications/6.0/velocity-worker-pool.md#what-a-worker-costs)):

| | Private per worker | PSS per worker | Workers per GB |
|---|---|---|---|
| A full Exponential (about 600 classes) | about 10 MB | about 11 MB | about 90 |
| The server alone, a one-line PHP page | 1.3 to 1.9 MB | 1.4 to 1.9 MB | about 530 |

The shared part (about 47 MB for Exponential) is paid once. Each worker holds its own memory from the moment it is
forked, idle or not (`settings/velocity.ini`).

> **Warning**
> Judge memory by PSS or private memory, never by RSS. RSS counts every shared copy-on-write page once per worker and
> overstates a pool many times. The control panel's Worker Memory card shows PSS
> ([Velocity control panel](../../../features/6.0/velocity-control-panel.md#the-dashboard)). A monitoring system that
> sums RSS will report memory the machine does not use.

The per-worker figure depends on the site. Another measurement in the sources, on a test installation without
warm-up, found 167.6 MB PSS per worker ([HTTP/2 and cache warming](../../../bc/6.0/http2-and-cache-warming.md#what-it-found-on-the-test-installation)).
Measure your own sites before you fix a plan's limits.

## Fixed or dynamic pool

| | Fixed (`SpareWorkers=0`) | Dynamic (`SpareWorkers` above 0) |
|---|---|---|
| At start | every worker forked | only the spare workers |
| Under load | waits for a free worker | forks more, up to `Workers` |
| When quiet | all workers stay | extra workers retired after `IdleWorkerTimeout` |
| Memory | pays for workers that may never serve | pays for what is used |

Measured on one installation: a fixed pool of 590 held 7,094 MB, the dynamic pool 626 MB
([worker pool specification](../../../specifications/6.0/velocity-worker-pool.md#what-a-worker-costs)). Chapter 8
suggests a dynamic pool as the start for production:

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=64
SpareWorkers=8
IdleWorkerTimeout=60
```

For a plan this is the useful shape: `Workers` is the ceiling you sell, `SpareWorkers` the floor the customer always
has.

## Instances: several servers on one port

One server answers cached pages in its own process and is bounded by one core. `Instances=N` runs N servers on the
same ports; the kernel spreads new connections across them. Measured on a 12-core test installation, cached front
page over TLS: about 2,950 pages a second with one instance, 11,985 with four
([Velocity engines, Instances](../../../bc/6.0/velocity-engines.md#several-qbix-servers-on-one-port-instances)).

- Each instance has its own pool: divide `Workers` and `SpareWorkers` by `Instances` to keep the same memory.
- Each has its own pid file and log (`server.pid`, `server.1.pid`, ...); `graceful` reloads them one at a time.
- Rendered pages are not faster with more instances; they are bounded by the workers.
- Qbix engine only; Linux and the BSDs.

## When a worker is replaced

A worker is replaced by a fresh fork after `maxRequests` requests, when its heap passes `workerMemoryCeiling`, when
its output buffers were left unbalanced, or when the application asks for it. A request still running after
`requestTimeout` seconds is killed and the client gets `504`. The reason is logged. A `GET`, `HEAD` or `OPTIONS` a
dying worker had not started answering runs once more on another worker; a `POST` is never run twice and gets `502`.

**The zygote** is a process forked before the first connection is accepted; later workers are forked from it and
inherit no visitor connections. Without it, workers forked under load kept connections open (up to 50 in
`CLOSE-WAIT` after a 20-second burst, measured). It needs the `sockets` extension; if it fails, workers are forked
from the server and the log says so.

## Persistent workers

`ForkPerRequest=disabled` (shipped) keeps workers alive across requests, with database connections and stat caches.
Measured on a signed-in administration page: 256 to 284 ms with a fresh worker per request, 73 to 80 ms with
persistent workers ([Chapter 8, section 8.3.7](../../../install/08-serving-the-site.md#837-persistent-workers-and-keepglobals)).
Between requests the worker restores static properties, superglobals, globals, buffers and handlers. Four kernel
registries and the worker scope of the kernel service container (`expKernelServicesWorker`: the settings files of
each siteaccess, built once per worker, see [the compiled kernel container](../performance/compiled-kernel-container.md))
are kept on purpose (`expVelocity::DEFAULT_KEEP_GLOBALS`); `[ApplicationSettings] KeepGlobals[]` adds
names only for a global filled once by `include_once`. Keep nothing else: a request-scoped global kept between
requests leaks one visitor's request into the next.

`StatTtl=1` measured 13 percent less CPU per rendered page; a change made by another process (another worker, Apache
on the same files) can then take up to that long to show.

## Streams hold workers

From engine 0.0.4.49 a worker streams Server-Sent Events and unbuffered responses. Each open stream holds one worker
for its whole life. `[ServerSettings] StreamTimeout` (engine default 300 s), `StreamShare` (0.25 of `Workers`) and
`MaxStreams` cap them; past the cap an event stream gets `503` with `Retry-After`, so pages keep the rest of the pool
([Velocity engines, Streaming responses](../../../bc/6.0/velocity-engines.md#streaming-responses)). Keep the cap well
below `Workers`.

## Finding the number for a plan

The sources warn against copying numbers: figures "describe this site's templates on this machine. They are not a
property of the server" ([HTTP/2 and cache warming](../../../bc/6.0/http2-and-cache-warming.md#two-caveats-on-all-of-it)).
Measure instead:

1. Measure a representative customer site rendered (`--cold`) at the concurrency you expect, with
   `exp:benchmark` ([Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md)).
2. Raise `Workers` step by step and repeat. On the test installation of that page throughput peaked at 16 workers
   (about 1.5 times the cores); 32 bought nothing, 64 was worse, and p90 jumped tenfold past the peak.
3. Tune to that **throughput cliff**, not to memory. The other limits come later: the event-loop ceiling (a few
   hundred workers; just below 1,000 with the default event loop) and the memory ceiling.
4. Use a dynamic pool so idle customers do not hold memory.

The engine's own default without `Workers` is what fits in memory after 1 GB is set aside, at most 8 per core and 64
in all, never fewer than 4. The shipped file sets 4 explicitly, because "left to itself the server ... can choose a
number so large it accepts connections and answers none".

## Checklist

- [ ] `Workers` set explicitly per site; `SpareWorkers` above 0 on shared machines.
- [ ] Memory monitored as PSS, not RSS.
- [ ] `Instances` above 1 only where cached throughput needs it, with `Workers` divided accordingly.
- [ ] `MaxStreams` set for sites that use event streams.
- [ ] Worker count chosen from a measurement of the site, written into the plan.

## Related pages

- [Velocity for hosts](velocity-for-hosts.md)
- [Sizing memory, CPU and disk](../basics/sizing-memory-cpu-and-disk.md)
- [Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md)
- [Velocity worker pool specification](../../../specifications/6.0/velocity-worker-pool.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
