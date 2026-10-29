---
title: Performance
audience: cms-developer
category: cms-development
order: 42
summary: For CMS developers, how to make an Exponential site fast and keep it fast. It covers measuring with exp:benchmark, what the content model and templates decide, fetch rules, caching per layer, the server side (Velocity, OPcache, response cache), images and front-end weight, warming caches, and a short checklist.
keywords: performance, speed, fast site, benchmark, exp:benchmark, measuring, fetch performance, view cache, cache-block, Velocity, OPcache, response cache, HTTP cache, SQL query cache, images, image aliases, packer, preload, warm caches, slow site
type: help
---
# Performance

This page is for developers responsible for how fast a site is. Most of the speed of an Exponential site is decided
by three things you control: how much each page fetches, how much of it can be cached, and how the site is served.
Measure first, change one thing, measure again.

## Contents

1. [Measure](#measure)
2. [The content model and templates](#the-content-model-and-templates)
3. [Fetches](#fetches)
4. [Caching](#caching)
5. [The server](#the-server)
6. [Images and the front end](#images-and-the-front-end)
7. [Warm caches](#warm-caches)
8. [A short checklist](#a-short-checklist)
9. [When it is slow in production](#when-it-is-slow-in-production)

## Measure

```bash
./console exp:benchmark --allow-root-user
./console exp:cache status --allow-root-user
curl -s -o /dev/null -w '%{time_total}s\n' https://www.example.com/
```

`exp:benchmark` requests the front page, menu pages, search and the administration login many times and prints
median, p90 and p99 times; it can save runs and compare against a baseline, and also measures parts of a page inside
the kernel ([Benchmarking Exponential](../../guides/benchmarking.md)). The debug output lists the SQL queries and the
time of a page on a development machine. A single page load proves nothing: the first request fills caches.

## The content model and templates

- A **list page** that draws fifty items, each with relations fetched one by one, makes hundreds of queries. Model so
  that list views need only the item's own attributes; use `load_data_map` when you read attributes of many nodes.
- **Pagelayout fetches** run on every request and are not in the view cache: wrap menus and footers in cache blocks.
- **Uncacheable parts** (the visitor's name, the basket) should be small and kept out of the view cache
  ([Caching for CMS developers](caching-for-cms-developers.md#visitor-specific-output)).

## Fetches

Always pass `limit`; filter by class in the fetch; count with `list_count`; avoid fetches inside loops; keep attribute
filters on large trees to what you measured ([Fetch performance](../designers/templates/fetch-performance.md),
[Fetching content in templates](fetching-content-in-templates.md#rules-for-fast-fetches)). Code in your extensions
follows the same rules ([Performance in extensions](../extension-development/part-3-extension-developer/performance-in-extensions.md)).

## Caching

| Layer | What to do |
|---|---|
| View cache | keep it on (`ViewCaching=enabled`); keep `full` cached; set `cache_ttl` only where needed |
| Cache blocks | around everything in the pagelayout that is the same for many visitors, with correct keys |
| SQL query cache | on by default; `./console exp:cache querycache status` |
| HTTP cache, Velocity response cache | whole pages for anonymous visitors; check with `exp:cache status` |

Every layer: [Caching layers](../hosting/performance/caching-layers.md).

## The server

1. **Velocity** with the `qbix` engine keeps PHP workers loaded between requests and has a response cache; it is the
   recommended engine ([Velocity persistent worker server](../../features/6.0/velocity-persistent-worker-server.md)).
2. **OPcache** on for PHP-FPM; Velocity passes its own opcode cache options
   ([OPcache](../hosting/performance/opcache.md), [Velocity and the opcode cache](../../features/6.0/velocity-opcode-cache-and-profile.md)).
3. **Workers**: `velocity.ini [ServerSettings] Workers`, raised with the CPU count and measured after each change.
4. **The database**: an engine sized for the content; see [Sizing memory, CPU and disk](../hosting/basics/sizing-memory-cpu-and-disk.md).

The order of these steps with commands: [Make it fast](../../guides/deploying.md#8-make-it-fast).

## Images and the front end

- Define image aliases for the sizes you show; print `width` and `height`; lazy-load images below the fold
  ([Images and image aliases](images-and-image-aliases.md), [Image performance](../designers/theme-design/image-performance.md)).
- Load stylesheets and scripts through the packer, which bundles and caches them
  ([The packer](../designers/theme-design/the-packer.md), [Theme performance](../designers/theme-design/theme-performance.md)).
- Measure layout shift and the largest paint on real pages ([Measuring CLS and LCP](../designers/theme-design/measuring-cls-and-lcp.md)).

## Warm caches

After a deployment or a cache clear, the first visitors pay for building caches. `exp:velocity deploy` requests
warm-up pages, and the preloader requests a site's pages as an anonymous visitor would
(**Setup** > **Preload**, `php bin/php/preload.php --apply`), listing broken links on the way
([Preloading caches](../../guides/preloading-caches.md)).

## A short checklist

- [ ] Benchmarked before and after each change, with the same command.
- [ ] No page makes more queries than its content explains; the debug output was checked on the heaviest pages.
- [ ] Every list fetch has a limit; no fetches in loops.
- [ ] Pagelayout menus and footers are in cache blocks with the right keys.
- [ ] The view cache is on; visitor-specific output is outside it.
- [ ] Served by Velocity (or another tuned server) with OPcache.
- [ ] Image aliases for every shown size; assets through the packer.

## When it is slow in production

Follow the runbook [Slow site](../hosting/runbooks/slow-site.md): check the load, the cache layers, the request logs
and the slowest pages before changing anything ([Request logs](../hosting/operations/request-logs.md)).

## Related pages

- [Checklist: performance](../designers/theme-design/checklist-performance.md)
- [Benchmarking a hosted site](../hosting/performance/benchmarking-a-hosted-site.md)
- [Deploying a site](deploying-a-site.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
