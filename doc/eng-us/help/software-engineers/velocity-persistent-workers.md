---
title: Velocity persistent workers
audience: software-engineer, admin
category: software-engineering
order: 330
summary: How Exponential Velocity runs PHP - the parent, the warm-up, the zygote and the persistent worker pool, how the pool is sized and when a worker is replaced, what is reset between requests and what is not, which user workers run as, and how to judge memory by PSS - for engineers who size, operate or debug it.
keywords: Velocity, persistent workers, worker pool, parent, fork, zygote, warm-up, reset, maxRequests, workerMemoryCeiling, requestTimeout, forkPerRequest, PSS, RSS, memory, compatibility layer, software engineer
type: help
---
# Velocity persistent workers

This page is for engineers who size, operate or debug Exponential under Velocity. It summarizes
[the worker pool specification](../../specifications/6.0/velocity-worker-pool.md), which applies to Exponential
Velocity 0.0.4.42 and later and holds every setting with its default.

## Contents

1. [The model](#the-model)
2. [Sizing the pool](#sizing-the-pool)
3. [When a worker is replaced](#when-a-worker-is-replaced)
4. [What is reset between requests](#what-is-reset-between-requests)
5. [The zygote and the warm-up](#the-zygote-and-the-warm-up)
6. [The user workers run as](#the-user-workers-run-as)
7. [Operating it](#operating-it)

## The model

- A parent process loads the application once, optionally warms it, then forks workers. Each worker answers one
  request after another and is returned after each to the state it had when it was forked.
- The parent accepts every connection and reads every request. It answers static files, response-cache hits and the
  server's own pages itself; only a request that runs PHP goes to a worker.
- When every worker is busy the request waits in the parent; beyond `maxConnections` the server answers `503`.
- A worker's death never costs a request that could be served: a `GET`, `HEAD` or `OPTIONS` it had not started
  answering runs once more on another worker; a `POST` is never run twice and gets a `502`.

| Mode | Behavior |
|---|---|
| Persistent (default) | each worker serves many requests |
| `forkPerRequest` | one request per worker, then a fresh fork: the isolation of a new process at the cost of a fork per request |

## Sizing the pool

`--workers=N`, or `[ServerSettings] Workers` in `settings/velocity.ini`. Without it the server sizes the pool from
memory and cores (at most 8 per core and 64 in all, never fewer than 4). Static pools fork every worker at start;
a dynamic pool (`SpareWorkers` above 0) forks on demand and retires idle workers beyond the spare count.

**Judge memory by PSS, never RSS.** RSS counts every shared copy-on-write page once per worker and overstates a pool
many times. Measured on 2026-09-26, a full Exponential worker cost about 11 MB PSS, with about 47 MB of shared code
loaded once before the fork.

## When a worker is replaced

| Trigger | Setting (default) |
|---|---|
| it served enough requests | `maxRequests` (1000) |
| its heap is too large | `workerMemoryCeiling` (256 MB, or 3/4 of `memory_limit` if lower) |
| a request ran too long | `requestTimeout` (30 s); the client gets `504`, the request is not run again |
| output buffers were left unbalanced | always |
| the application asked for it | `Q_WebServer_Pool::retireAfterResponse( $reason )` |

The reason is logged, so a worker that keeps being replaced points at a leak.

## What is reset between requests

| State | How |
|---|---|
| Static class properties | snapshot at start, restored after each request |
| `$_GET`, `$_POST`, `$_COOKIE`, `$_FILES`, `$_SERVER`, `$_REQUEST` | re-populated from the request |
| `$_SESSION` | written and emptied |
| Custom globals | snapshot and restore, except the names in `keepGlobals` |
| Shutdown functions, error handlers, autoloaders, `ini_set`, `putenv` | tracked per request and restored |
| Response headers | cleared |

**Not reset, by design:** OPcache, loaded classes, APCu, interned strings, autoloader maps. **The application's job:**
database transaction state, file handles, stream contexts, signal handlers, cURL handles, and registries in globals
filled once with `include_once`. See [Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md).

## The zygote and the warm-up

- **The zygote** is one extra process forked at the end of start-up, before the first connection. Later workers are
  forked from it, so they inherit no visitor's connection.
- **The warm-up** script runs once in the parent before forking (Exponential ships `bin/php/velocity-warmup.php`);
  every worker inherits the warmed memory copy-on-write. Whatever the warm-up leaves becomes every worker's baseline,
  so it must reset request state. Verify a warm-up by behavior: several distinct URLs must return distinct content.

## The user workers run as

A server started as root keeps root only in the server process. The zygote and every worker give it up right after the
fork, before application code runs; the user comes from the command line, the configuration, the environment
(`VC_RUN_USER`, `QBIX_RUN_USER`) or the owner of the document root. Root workers are refused unless explicitly allowed.

## Operating it

```bash
php bin/php/console exp:velocity status
php bin/php/console exp:velocity graceful        # reload workers one at a time, the port keeps answering
php bin/php/console exp:velocity restart
php bin/php/console exp:velocity deploy --allow-root-user
```

After a restart, check that a new parent process is running and that the port answers. The on-disk layout of the
configuration follows Debian's `/etc/apache2` organization ([Velocity on-disk layout](../../bc/6.0/velocity-ondisk-layout.md)).

## Related pages

- [Application servers](application-servers.md)
- [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md)
- [Velocity opcode cache and profile](../../features/6.0/velocity-opcode-cache-and-profile.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
