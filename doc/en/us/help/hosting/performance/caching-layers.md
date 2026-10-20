---
title: Caching layers
audience: hosting, admin
category: hosting
order: 40
summary: Every cache between a visitor and the database of an Exponential site, in order: what each holds, whether it ships on or off, where it lives, which server uses it, and how it is cleared, so a host knows what a plan includes and support knows where a stale page comes from.
keywords: cache, caching layers, browser cache, StaticMaxAge, CDN, reverse proxy, Velocity response cache, HTTP cache, httpcache.ini, static cache, view cache, ViewCaching, template-block, TemplateCache, compiled templates, INI cache, SQL query cache, querycache.ini, Valkey, Redis, OPcache, APCu, X-Cache, X-Exp-Cache
featured: true
type: help
---
# Caching layers

This page is for system administrators and support staff at hosting companies. It answers: which caches an
Exponential site has, from the visitor's browser down to the database, which of them ship switched on, where each
lives, which server uses it, and how each is cleared. With it, a host can decide what a plan switches on, and support
can tell where a stale or slow page comes from.

## Contents

1. [The layers at a glance](#the-layers-at-a-glance)
2. [In front of the server](#in-front-of-the-server)
3. [In the server, before the kernel](#in-the-server-before-the-kernel)
4. [Inside the kernel](#inside-the-kernel)
5. [Under the kernel](#under-the-kernel)
6. [Which layers are shared between servers](#which-layers-are-shared-between-servers)
7. [Reading the headers](#reading-the-headers)
8. [What a plan can switch on](#what-a-plan-can-switch-on)
9. [Checklist](#checklist)

## The layers at a glance

| # | Layer | Ships | Serves | Cleared by |
|---|---|---|---|---|
| 1 | Browser cache of static files | Velocity: `StaticMaxAge=31536000`; Apache: your `mod_expires` | everyone | file names, or waiting out the lifetime |
| 2 | Browser cache of pages | off (`site.ini [HTTPHeaderSettings] CustomHeader=disabled`) | anonymous visitors, when switched on | lifetime |
| 3 | CDN or caching proxy | not part of Exponential | as you configure it | your proxy; Exponential sends no purges |
| 4 | Velocity response cache | on under Velocity (`velocity.ini [CacheSettings] Enabled=enabled`) | anonymous visitors | `exp:velocity cache clear` |
| 5 | Role-aware HTTP cache | off (`httpcache.ini [HttpCacheSettings] Enabled=disabled`) | anonymous and signed-in, per permission context | purged on content change; `--clear-id=exphttpcache` |
| 6 | Static cache | off (`site.ini [ContentSettings] StaticCache=disabled`) | anonymous visitors, as files | regenerated on publish |
| 7 | Content view cache | on (`[ContentSettings] ViewCaching=enabled`) | every visitor | content change; `--clear-tag=content` |
| 8 | Template-block cache | on (`[TemplateSettings] TemplateCache=enabled`) | every visitor | expiry; `--clear-id=template-block` |
| 9 | Compiled templates, INI and override caches | on | every request | `--clear-tag=template`, `--clear-tag=ini` |
| 10 | Redis or Valkey for views, blocks, INI | only with the `sevenx_valkey_cache` extension | every request | the extension's purges; its clear script |
| 11 | SQL query cache | off (`querycache.ini [QueryCacheSettings] Mode=off`) | every request it can answer | writes to the tables read; `--clear-id=querycache` |
| 12 | OPcache, APCu | per PHP process | every request | reload or restart; Setup > Caches buttons |

Sources: [Caches](../../../guides/caches.md), [Chapter 10, section 10.6](../../../install/10-after-installing.md#106-caches)
and the pages linked below.

## In front of the server

**Browser.** Velocity sends `Cache-Control` with a one-year lifetime for stylesheets, scripts and images
(`[ServerSettings] StaticMaxAge`); a file replaced under the same name is not fetched again until it expires. Pages
are sent `no-cache` unless `[HTTPHeaderSettings]` is switched on for anonymous visitors only. See
[HTTP cache headers](http-cache-headers.md). Under Velocity, an optional navigation cache (a service worker, `/index.js`
and `sw.js`) can answer return visits in the visitor's own browser
([The response cache, and getting out of the network's way](../../../bc/6.0/response-cache-and-navigation.md)).

**CDN or proxy.** Exponential sends no purge or ban requests to a proxy; a proxy that stores pages must be told what
to store and for how long. See [Varnish and reverse proxies](varnish-and-reverse-proxies.md) and [CDN](cdn.md).

## In the server, before the kernel

**Velocity response cache.** Keeps rendered anonymous pages in the parent process and answers repeat requests before
a worker is chosen: no fork, no PHP, no database. Default lifetime 30 seconds, stale-while-revalidate 60 seconds;
signed-in visitors are never served from it. See [Velocity response cache](velocity-response-cache.md).

**Role-aware HTTP cache.** Off by default. When on, it keeps whole content views per permission context (anonymous,
a role context shared by users with the same roles, or a private context) and serves them before the kernel starts:
under Apache or PHP-FPM through an early exit included from `config.php`, under Velocity from the server process. It
purges on content change (`ContentChangePurges=all` by default). Measured on a reference installation, a signed-in
page under Apache: 484 requests a second from the cache against 4.3 rendered
([HTTP cache](../../../bc/6.0/httpcache.md)).

**Static cache.** Off by default, and the installation book says to leave it off: with it on, every publish
regenerated every affected page over HTTP inside the editor's request. See [Static cache](static-cache.md).

## Inside the kernel

| Cache | What it holds | Setting |
|---|---|---|
| Content view cache | the rendered content of each node view | `site.ini [ContentSettings] ViewCaching` |
| Template-block cache | `cache-block` fragments of templates | `[TemplateSettings] TemplateCache` |
| Compiled templates | templates compiled to PHP | `[TemplateSettings] TemplateCompile` |
| INI, override, translation, design caches | merged settings and lookups | always on |
| Image aliases | resized images, made on first view | `image.ini` |

When a change would clear the view caches of `[ContentSettings] CacheThreshold` nodes or more (250 shipped), every
view cache of the site is expired instead, and the log says "Expiring all view cache"
([Caches, Usual questions](../../../guides/caches.md#6-usual-questions)).

With the `sevenx_valkey_cache` extension, the content view cache, template blocks and the compiled INI cache can live
in Redis or Valkey instead of files. See [Valkey and Redis](valkey-and-redis.md).

## Under the kernel

**SQL query cache.** Off by default. `Mode=request` removes repeated identical `SELECT`s within one request;
`Mode=shared` keeps results across requests in APCu or files. Measured on a test installation under Apache with
PHP-FPM, a rendered front page went from about 0.6 s to about 0.17 s with no statement sent to the database
([SQL query cache](../../../bc/6.0/sql-query-cache.md)). It works with the SQL drivers (1. SQLite 3, 2. PostgreSQL,
4. MariaDB, 5. Oracle through `ezoracle`, 6. MySQL); the 3. MongoDB driver does not use it. Writes made outside
Exponential (a restore, a SQL client) are invisible to it: clear it after every restore.

**OPcache and APCu** belong to each PHP process tree. See [OPcache](opcache.md) and
[PHP-FPM tuning](php-fpm-tuning.md#apcu).

## Which layers are shared between servers

The cache files are in `var/`, which Apache with PHP-FPM and Velocity share: clearing a cache in Setup > Caches
reaches both. Two things do not ([Caches](../../../guides/caches.md#in-short)):

- Velocity keeps settings in memory: restart it after clearing the INI caches.
- Velocity's response cache answers pages from its own store: clear it with `exp:velocity cache clear`.

APCu and OPcache are per process tree. On several web nodes, files in a shared `var/` are shared; APCu is per server
(the query cache's `Storage=file` exists for that case). See
[Several web nodes](../operations/several-web-nodes.md).

## Reading the headers

| Header | Set by | Meaning |
|---|---|---|
| `X-Cache: HIT`, `STALE` | Velocity's response cache | answered from the cache, or from an expired copy while a new one renders |
| `Age` | Velocity's response cache | seconds since the entry was stored |
| `X-Exp-Cache: HIT`, `STALE`, `MISS (reason)`, `BYPASS (reason)` | the role-aware HTTP cache | what it did and why |

```bash
curl -sI https://example.com/ | grep -iE 'x-cache|x-exp-cache|age|cache-control'
```

A page answered by Velocity's response cache also carries the `X-Exp-Cache` stored with it, often `MISS` from the
render that filled both caches ([HTTP cache, Seeing what it does](../../../bc/6.0/httpcache.md#seeing-what-it-does)).

## What a plan can switch on

| Layer | Plan option | Watch |
|---|---|---|
| Velocity response cache | on with Velocity plans | `DefaultTtl` is how stale an anonymous page may be |
| Role-aware HTTP cache | an option for sites with many signed-in visitors | sessions must be in files every server can read |
| SQL query cache | `request` everywhere; `shared` with APCu sized | clear after restores |
| Redis or Valkey | an option where you run the server | size it, set an eviction policy |
| Page headers for browsers and CDNs | an option for anonymous-heavy sites | `OnlyForAnonymous=enabled` always |
| Static cache | not recommended | publish cost |

## Checklist

- [ ] The plan states which layers are on and who clears them.
- [ ] Support knows the headers above and the clearing order ([Cache clearing and warming](cache-clearing-and-warming.md)).
- [ ] Caches that hold rendered output are cleared only after new code runs.
- [ ] The query cache is cleared after every database restore.
- [ ] No proxy or CDN stores pages that depend on a session cookie.

## Related pages

- [Velocity response cache](velocity-response-cache.md)
- [HTTP cache headers](http-cache-headers.md)
- [Cache clearing and warming](cache-clearing-and-warming.md)
- [Caches](../../../guides/caches.md)
- [Cache control from the console](../../../bc/6.0/cache-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
