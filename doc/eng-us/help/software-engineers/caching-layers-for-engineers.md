---
title: Caching layers for engineers
audience: software-engineer, admin
category: software-engineering
order: 300
summary: Every cache between a visitor and the database of an Exponential site, seen by an engineer - what each layer stores, what it is keyed on, whether it ships on, who it serves, which servers share it, and the correctness risks each one brings - from browser and Velocity response cache to view cache, template blocks, INI cache, SQL query cache and the Redis or Valkey option.
keywords: caching, cache layers, response cache, HTTP cache, view cache, template block, INI cache, query cache, Valkey, Redis, OPcache, static cache, cache keys, stale content, software engineer
type: help
---
# Caching layers for engineers

This page is for engineers who reason about performance and correctness together. Exponential has many caches; most
performance work is choosing which to switch on, and most "stale page" bugs are one of them answering when it should
not. The operator's view of the same layers is [Caching layers](../hosting/performance/caching-layers.md), and the
administration page is described in [Caches](../../guides/caches.md).

## Contents

1. [The layers in order](#the-layers-in-order)
2. [What each layer is keyed on](#what-each-layer-is-keyed-on)
3. [Shared or per node](#shared-or-per-node)
4. [Correctness risks](#correctness-risks)
5. [Choosing what to switch on](#choosing-what-to-switch-on)

## The layers in order

From the visitor down to the database:

| # | Layer | Ships | Serves | Source |
|---|---|---|---|---|
| 1 | Browser cache of static files | Velocity: `StaticMaxAge` one year | everyone | [HTTP caching](../../bc/6.0/http-caching.md) |
| 2 | Browser cache of pages | off (`[HTTPHeaderSettings]`) | anonymous visitors, when switched on | [HTTP caching](../../bc/6.0/http-caching.md) |
| 3 | CDN or caching proxy | not part of Exponential | as configured | Exponential sends no purges |
| 4 | Velocity response cache | on under Velocity (`velocity.ini [CacheSettings] Enabled=enabled`) | anonymous visitors | [Velocity response cache](../../features/6.0/velocity-response-cache.md) |
| 5 | Role-aware HTTP cache | off (`httpcache.ini`) | anonymous and signed-in, per permission context | [HTTP cache](../../bc/6.0/httpcache.md) |
| 6 | Static cache | off | anonymous visitors, as files | [Static cache defaults](../../bc/6.0/static-cache-defaults.md) |
| 7 | Content view cache | on (`[ContentSettings] ViewCaching=enabled`) | every visitor | [Caches](../../guides/caches.md) |
| 8 | Template block cache | on (`[TemplateSettings] TemplateCache=enabled`) | every visitor | [The template engine and the design chain](template-engine-and-design-chain.md) |
| 9 | Compiled templates, INI and override caches | on | every request | [Caches](../../guides/caches.md) |
| 10 | Redis or Valkey for views, blocks, INI | only with `sevenx_valkey_cache` | every request | [sevenx_valkey_cache](../../features/6.0/extensions/sevenx_valkey_cache.md) |
| 11 | SQL query cache | off (`querycache.ini Mode=off`) | every request it can answer | [The SQL query cache](../../bc/6.0/sql-query-cache.md) |
| 12 | OPcache, APCu | per PHP process | every request | your PHP configuration |

The Velocity response cache exists only under the `qbix` engine: FrankenPHP has none yet, and PHP-FPM behind Apache
or nginx has none of its own ([Application servers](application-servers.md)).

## What each layer is keyed on

| Layer | Key (in short) | Invalidated by |
|---|---|---|
| Velocity response cache | the request of an anonymous visitor; lifetime `[CacheSettings] DefaultTtl` (30 seconds shipped) unless the page's `Cache-Control` says otherwise | lifetime, `./console exp:velocity cache clear --apply` |
| Role-aware HTTP cache | the content view and the permission context (anonymous, a shared role context, or private) | content change purges (`ContentChangePurges=all` by default), `--clear-id=exphttpcache` |
| Content view cache | the node, the view and its parameters, and the user's access context | `eZContentCacheManager` on publish, `--clear-tag=content` |
| Template block cache | the keys written in `{cache-block}`, and its expiry | expiry, `--clear-id=template-block` |
| INI cache | the files of the cascade for a siteaccess (and environment) | `--clear-tag=ini` |
| SQL query cache | the `SELECT` statement | a write to one of the tables it read |

## Shared or per node

The caches in `var/` are files written through the cluster file handler, so the same code can share them on clustered
storage. Velocity's response cache and the in-memory state of its workers are per server process; OPcache and APCu are
per PHP process. With several web nodes, decide for each layer whether it is shared, purged on every node, or allowed
to expire ([Scaling and clustering](scaling-and-clustering.md)).

## Correctness risks

- **Clearing before reloading.** A page rendered by the old code between a cache clear and the reload of PHP-FPM or
  Velocity is cached again; clear page caches last ([Deploying a change](deploying-a-change.md)).
- **Writes from outside.** A restore or a SQL client write bypasses the SQL query cache's invalidation; clear it
  afterwards.
- **Cache blocks without the right keys.** A `{cache-block}` that does not key on what changes the output serves one
  user's or one page's fragment to another.
- **Velocity keeps settings in memory.** After an INI change, clearing the INI cache is not enough under Velocity;
  restart it.
- **Static cache.** With it on, every publish regenerated every affected page over HTTP inside the editor's request;
  the project ships it off and recommends leaving it off.

## Choosing what to switch on

1. Keep the shipped defaults: view cache, template blocks, compiled templates, INI cache.
2. For anonymous traffic, under Velocity, the response cache gives the largest gain per change.
3. For signed-in traffic, the role-aware HTTP cache keeps whole views per permission context.
4. For pages that cannot be stored whole (the administration, private contexts, scripts), the SQL query cache.
5. For several web nodes, consider Redis or Valkey for views, blocks and INI.

Measure each step ([Performance measurement and benchmarks](performance-measurement-and-benchmarks.md)).

## Related pages

- [Cache invalidation and clearing](cache-invalidation-and-clearing.md)
- [Caching in applications](../creators/app-creators/caching-in-applications.md)
- [The response cache, and getting out of the network's way](../../bc/6.0/response-cache-and-navigation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
