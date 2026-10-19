---
title: Performance and caching for websites
audience: website-creator, admin
category: website-creation
order: 620
summary: Make a site fast - the cache layers from browser to database, the levers in order of effect, measuring with exp:benchmark, and the rule for clearing caches in the right order.
keywords: performance, speed, caching, view cache, cache-block, Velocity response cache, HTTP cache, OPcache, query cache, benchmark, exp:benchmark, warm, website creator
type: help
---
# Performance and caching for websites

This page is for website creators who want fast pages. Exponential caches at many levels; most of them are on by
default. This page shows the layers, the levers in order of effect, and how to measure
([10.12 Performance tuning](../../../install/10-after-installing.md#1012-performance-tuning)).

## Contents

1. [The layers](#the-layers)
2. [The levers, in order of effect](#the-levers-in-order-of-effect)
3. [Measure](#measure)
4. [Templates that stay fast](#templates-that-stay-fast)
5. [Clearing caches in the right order](#clearing-caches-in-the-right-order)

## The layers

| Layer | What it keeps | Read |
|---|---|---|
| Browser, proxy, CDN | pages and static files, when headers allow | [HTTP caching, proxies and CDNs](http-caching-and-cdns.md) |
| Velocity response cache | whole public pages, answered without PHP | [Static cache and the Velocity response cache](static-cache-and-velocity.md) |
| Content view cache | the rendered main area of each page | [View cache and cache blocks](view-cache-and-cache-blocks.md) |
| Template block cache | `{cache-block}` output: menus, footers | same |
| Compiled templates | `.tpl` compiled to PHP | |
| OPcache | compiled PHP | [10.12](../../../install/10-after-installing.md#1012-performance-tuning) |
| SQL query cache | query results, optional | [SQL query cache](../../../bc/6.0/sql-query-cache.md) |

## The levers, in order of effect

From the installation book:

1. **Serve with Velocity** and its response cache; persistent workers keep the application warm.
2. **Keep the content view cache and the template block cache on** (`[ContentSettings] ViewCaching=enabled`,
   `[TemplateSettings] TemplateCache=enabled`, both shipped). Warm the site after a full clear with
   `php bin/php/warm.php --allow-root-user`.
3. **OPcache**.
4. **APCu** for the response cache's memory tier and the shared query cache.
5. **The SQL query cache** (`querycache.ini`), measured on your site.
6. **The database** itself ([Databases](../../../install/09-databases.md)).

Leave the static cache off (`StaticCache=disabled`, the shipped value); the response cache gives visitors the same
benefit without slowing down publishing.

## Measure

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
# change something
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
```

It prints median, p90 and p99 per page. Compare runs on the same machine, back to back
([Benchmarking](../../../guides/benchmarking.md)). Measure also in the browser: image sizes, number of requests and
layout shifts often matter more than server time.

## Templates that stay fast

- Fetch only what you show: `limit`, `class_filter_array`, `load_data_map` where offered.
- No fetch of a whole subtree in the pagelayout without a cache block.
- Cache blocks keyed by exactly what the output depends on.
- Images in the right alias size, with `width`, `height` and `loading="lazy"`
  ([Images and image variations](images-and-image-variations.md)).
- Stylesheets and scripts through the packer ([Stylesheets and scripts](stylesheets-and-scripts.md)).

## Clearing caches in the right order

A page rendered by old code between a cache clear and a PHP reload is cached again. So: INI and template caches, then
reload PHP and restart Velocity, and only then the content, template-block and response caches. Under Velocity
`exp:velocity deploy` does this in order ([The order after a code change](../../../install/10-after-installing.md#the-order-after-a-code-change)).

| You changed | Clear |
|---|---|
| a setting | `--clear-tag=ini` |
| a template | `--clear-tag=template` |
| a new template or override rule | `--clear-id=template-override,design_base` |
| a stylesheet or script | `--clear-id=ezjscore-packer,template-block`, then the Velocity cache |
| content looks stale | `--clear-tag=content` |

## Related pages

- [View cache and cache blocks](view-cache-and-cache-blocks.md)
- [Caches](../../../guides/caches.md)
- [Profiling and performance](../app-creators/profiling-and-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
