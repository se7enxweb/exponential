---
title: Velocity and performance for integrations
audience: integrator, developer, hosting
category: integration
order: 370
summary: What Velocity, the persistent-worker web server of Exponential, means for integrations - classes loaded at start, statics that survive requests, deploying a changed integration, which API answers the response cache may keep, rate limits per server, and where the load of an integration should run.
keywords: Velocity, persistent worker, qbix engine, FrankenPHP, PHP-FPM, statics, class loading, restart, exp:velocity deploy, response cache, X-Cache, Authorization, APCu rate limit, performance, load, batch, API throughput
type: help
---
# Velocity and performance for integrations

This page is for integrators whose code runs on sites served by **Velocity**, the persistent-worker web server that
is part of Exponential 6.0.15 (`se7enxweb/exponential-velocity`, started with `php bin/php/console exp:velocity`).
Velocity boots the kernel once and keeps workers alive, so it answers far faster than one PHP process per request;
it also changes a few things an integration must respect
([Velocity: running Exponential in a persistent-worker web server](../../features/6.0/velocity-persistent-worker-server.md)).

## Contents

1. [What changes for integration code](#what-changes-for-integration-code)
2. [Deploying a changed integration](#deploying-a-changed-integration)
3. [API answers and the response cache](#api-answers-and-the-response-cache)
4. [Where the load should run](#where-the-load-should-run)
5. [Measuring](#measuring)

## What changes for integration code

| Topic | With PHP-FPM | With Velocity | What to do |
|---|---|---|---|
| Classes | Loaded per request | Loaded when the server starts; workers inherit them | Restart Velocity after adding or changing classes |
| Static properties | Empty at each request | Can survive from one request to the next | Reset request state at the start of each request; never keep the current user or a credential in a static |
| Listeners and registrations | Attached per request | Attached once from settings | Never attach listeners in code that runs per request: they accumulate |
| Settings | Read per request (with the INI cache) | Read at start and cached | Clear the INI cache (`--clear-tag=ini`) and restart after settings changes |
| Long calls | Hold one FPM child | Hold one worker | Keep remote calls out of requests; defer to cronjobs |

The kernel itself shows the pattern: the REST layer resets the API key state at the start of every request
(`expApiKeyRest::reset()`), because a persistent worker would otherwise remember the previous request's key.

## Deploying a changed integration

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do
php bin/php/console exp:velocity deploy --allow-root-user
```

`exp:velocity deploy` regenerates the extension autoloads, clears the INI cache and the template caches, reloads
PHP-FPM, restarts Velocity, and then clears the content and response caches, in that order, stopping at the first
failure ([Deploying](../../guides/deploying.md#7-ship-a-php-change-with-one-command)). Without it, do the same steps by
hand; clearing caches **before** the reload lets pages rendered by the old code be cached again.

A new class that a setting names (a login handler, a workflow event type, a search engine) must exist in the workers
before the setting is active: deploy the code and restart first, then switch the setting on.

## API answers and the response cache

Velocity's response cache is **off until enabled**. When it is on, it keeps a `GET` answer only when the request
carries no `Authorization` header and no session cookie, the response sets no cookie, and `Cache-Control` says
neither `no-store` nor `private` ([Velocity response cache](../../features/6.0/velocity-response-cache.md)).

For integrations this means:

- Requests with an API key, OAuth token or `expservices` token (`Authorization` header) are never answered from the
  cache.
- **Anonymous** `GET` calls (a public RSS feed, an anonymous `ezjscore` call) may be answered from the cache for the
  page's lifetime; the `X-Cache: HIT` and `Age` headers say so. Send `Cache-Control: no-store` from endpoints of your
  own that must always be fresh.
- After an import, clear the response cache (`php bin/php/console exp:velocity cache clear`) if anonymous clients
  must see the change at once ([Caching after imports](caching-after-imports.md)).

## Where the load should run

- **Bulk work on the command line.** Imports, exports and reindexing run as CLI scripts or cronjob parts, not as
  thousands of HTTP requests; they do not take workers away from visitors.
- **Rate limits per server.** The API key rate limit (`RateLimitPerMinute`) is counted in APCu per PHP-FPM pool or
  per Velocity server; with several servers the effective limit is higher. Do not rely on it as a capacity plan.
- **Nice the batch.** Run large imports with a lower priority (`nice -n 15 php ...`) on a shared host.
- **Off-peak.** Schedule full syncs at quiet hours; a large import can clear every view cache
  ([Caching after imports](caching-after-imports.md#what-publishing-clears-on-its-own)).

## Measuring

- `php bin/php/console exp:velocity status` and the Velocity control panel show the workers and their load
  ([Velocity control panel](../../features/6.0/velocity-control-panel.md)).
- `php bin/php/benchmark.php --help` describes the benchmark command for request throughput
  ([Benchmarking](../../guides/benchmarking.md)).
- Time your own runs and log the duration in the summary line; a run that gets slower each week is the first sign of
  a growing table or a missing index.

## Related pages

- [Velocity: running Exponential in a persistent-worker web server](../../features/6.0/velocity-persistent-worker-server.md)
- [Deploying with Velocity for app creators](../creators/app-creators/deploying-with-velocity.md)
- [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md)
- [Caching after imports](caching-after-imports.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
