---
title: Caching for availability
audience: businesses, admin, website-creator, hosting
category: business-solutions
order: 110
summary: Why caches are a reliability tool and not only a speed tool, which cache layers Exponential has and which are on by default, how conditional requests and stale-while-revalidate protect a busy site, and the rules for clearing caches safely.
keywords: cache, caching, availability, view cache, HTTP cache, response cache, static cache, conditional requests, 304, ETag, stale-while-revalidate, cache clear, warming
type: help
---
# Caching for availability

This page is for managers who want the site to stay up when many customers arrive at once, and for the operator who
configures the caches. It explains why caching is a reliability measure, which layers Exponential offers, what is on
out of the box, and the few rules that keep caches from causing problems of their own. The full technical map is
[Caching layers](../../hosting/performance/caching-layers.md) and [Caches](../../../guides/caches.md).

## Contents

1. [Why caching is a reliability measure](#why-caching-is-a-reliability-measure)
2. [The layers in plain words](#the-layers-in-plain-words)
3. [What is on out of the box](#what-is-on-out-of-the-box)
4. [Protection built into the cache](#protection-built-into-the-cache)
5. [Signed-in customers](#signed-in-customers)
6. [Clearing caches safely](#clearing-caches-safely)
7. [Warming after a clear](#warming-after-a-clear)
8. [Checklist](#checklist)

## Why caching is a reliability measure

Every page that is answered from a cache is a page that does not need the database, the template engine or a PHP
worker. That has two effects a business cares about:

- **Headroom under load.** The same server can serve far more visitors when most pages come from a cache, so a
  traffic spike is less likely to turn into an outage.
- **Fewer moving parts per request.** The database documentation says it plainly: "Most requests never reach the
  database" because the response cache, the view cache and the template-block cache answer first
  ([Database support, performance and scaling](../../database-support.md#performance-and-scaling)).

There is a limit to remember: a cached front page can stay green while the database is down. That is why the
monitoring advice is to check the administration login page as well
([Health checks and monitoring](health-checks-and-monitoring.md#checks-from-outside)).

## The layers in plain words

From the outside in ([Caching layers, the layers at a glance](../../hosting/performance/caching-layers.md#the-layers-at-a-glance)):

| Layer | In plain words |
|---|---|
| Browser cache | The visitor's browser keeps images, styles and scripts |
| CDN or caching proxy | An optional service in front of your site; not part of Exponential |
| Velocity response cache | Velocity answers repeat requests for public pages itself, before any worker |
| Role-aware HTTP cache | Stores whole pages per permission context, for anonymous and signed-in visitors |
| Static cache | Public pages stored as plain files the web server sends directly |
| Content view cache | The rendered content of each page, per language and role |
| Template-block cache | Parts of the page layout, such as menus |
| SQL query cache | Answers repeated database reads |
| OPcache and APCu | PHP's own caches of compiled code and small values |

The two layers that answer without running the application at all, the static cache and Velocity's response cache,
have a page of their own: [Static and response cache](static-and-response-cache.md).

## What is on out of the box

From the shipped settings ([Caching layers](../../hosting/performance/caching-layers.md#the-layers-at-a-glance),
checked against `settings/site.ini`, `settings/velocity.ini` and `settings/querycache.ini`):

| Layer | Shipped |
|---|---|
| Content view cache | On (`[ContentSettings] ViewCaching=enabled`) |
| Template-block cache | On |
| Velocity response cache | On when Velocity serves the site (`[CacheSettings] Enabled=enabled`) |
| Role-aware HTTP cache | Off until switched on |
| Static cache | Off (`[ContentSettings] StaticCache=disabled`) |
| SQL query cache | Off (`Mode=off`) |

**Setup > System information** warns when view caching, template compilation, the template cache or the override cache
is switched off, and says to "enable them on production"
([System information, health checks](../../../guides/system-information.md#3-health-checks)).

## Protection built into the cache

The README lists fixes in Exponential 6.0.15 that "apply wherever Exponential runs", on Apache and Nginx as well as
Velocity ([README, "The part that matters even if you never run Velocity"](https://github.com/se7enxweb/exponential/blob/main/README.md#the-part-that-matters-even-if-you-never-run-velocity)):

- **Conditional requests**: "a returning visitor's reload is answered with 202 bytes instead of the whole page,
  because cached pages now answer conditional requests properly".
- **Content-based identity**: rebuilding a cache entry that produced identical output "no longer forces every visitor
  to download it again".
- **One rebuild, not a stampede**: "when a cached page expires, one request rebuilds it while everyone else is served
  the existing copy — instead of every visitor present rebuilding it at once". This is what keeps a popular page from
  overloading the server the moment its cache expires.
- **Remembered "not found"**: "a 'not found' is remembered instead of being rebuilt from scratch every time".

For a business, the third point matters most: the moment a cache expires on a busy page is a classic cause of
overload, and Exponential handles it for you.

## Signed-in customers

Customers who are signed in (members, shop customers with an account, editors) see pages that depend on who they are.
The Velocity response cache never serves them: "Signed-in visitors are never served from the cache"
(`settings/velocity.ini`, `[CacheSettings]`). The content view cache still helps them, because it is kept per role
and limitations ([View cache and cache blocks](../../creators/website-creators/view-cache-and-cache-blocks.md#the-content-view-cache)),
and the role-aware HTTP cache can serve signed-in visitors per permission context when it is switched on.

If most of your traffic is signed in (an intranet, a member site, a shop at checkout), plan capacity for rendered
pages, not cached ones ([Peak traffic planning](peak-traffic-planning.md)).

## Clearing caches safely

Caches cause trouble only when they are cleared carelessly. Three rules:

1. **Clear the smallest thing that fixes the problem.** A settings change needs the INI cache, a template the
   template cache. "Everything, when unsure" exists, but costs the next visitors a slow page each
   ([Operating a site, clear the right cache](../../../guides/operating-a-site.md#2-clear-the-right-cache)).
2. **Look first.** `--dry-run` shows what a clear would do and changes nothing.
3. **Clear page caches last.** "Clear the HTTP and response caches after PHP-FPM or Velocity runs the new code,
   otherwise a page rendered by the old code in between is stored again" (same page). The deploy command does this in
   the right order ([Graceful deploys](graceful-deploys.md)).

Large caches are moved aside and deleted afterwards, "so a clear does not make requests wait"
([Cache clears that move directories aside](../../../features/6.0/cache-clear-rename-aside.md)).

## Warming after a clear

After a clear or a deploy "the first visitor pays for the render". Warm the pages yourself
([Operating a site, warm the site](../../../guides/operating-a-site.md#5-warm-the-site-preload)):

```bash
php bin/php/preload.php --siteaccess=site --allow-root-user
```

Use the name of your own public siteaccess. The same is available in the administration as **Preload Sites**
([Preloading caches](../../../guides/preloading-caches.md)).

## Checklist

- [ ] View caching, template compilation and the template cache are on in production (no warning on **Setup > System information**).
- [ ] The team knows which cache to clear for which change, and uses `--dry-run` first.
- [ ] Page caches are cleared after the code reload, not before (the deploy command does it).
- [ ] The site is warmed after a full clear or a deploy.
- [ ] Capacity is planned for signed-in traffic, which is rendered rather than served from the response cache.

## Related pages

- [Static and response cache](static-and-response-cache.md)
- [Peak traffic planning](peak-traffic-planning.md)
- [Performance and caching for websites](../../creators/website-creators/performance-and-caching.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
