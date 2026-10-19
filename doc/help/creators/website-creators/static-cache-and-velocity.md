---
title: Static cache and the Velocity response cache
audience: website-creator, admin
category: website-creation
order: 650
summary: Why the static cache stays off and the Velocity response cache does its job better - turning the response cache on, what it caches, clearing it after changes, and checking hits.
keywords: static cache, StaticCache, Velocity response cache, exp:velocity cache, anonymous pages, ETag, 304, stale-while-revalidate, cache clear, website creator
type: help
---
# Static cache and the Velocity response cache

This page is for website creators who want public pages served as fast as possible. Two features have done this job:
the old **static cache**, which wrote HTML files to disk, and the **Velocity response cache**, which keeps pages in the
server process. Use the second.

## Contents

1. [Leave the static cache off](#leave-the-static-cache-off)
2. [The Velocity response cache](#the-velocity-response-cache)
3. [Turning it on](#turning-it-on)
4. [After a change](#after-a-change)
5. [Checking it](#checking-it)

## Leave the static cache off

`[ContentSettings] StaticCache=disabled` is the shipped value and the installer writes it. With the static cache on,
every publish regenerated every affected page on every public siteaccess over HTTP inside the editor's request, so a
publish took seconds ([10.12](../../../install/10-after-installing.md#1012-performance-tuning),
[Static cache defaults](../../../bc/6.0/static-cache-defaults.md)).

## The Velocity response cache

It keeps rendered public pages and answers repeat requests inside the server process, before a worker is chosen. It
behaves like a small reverse proxy: conditional requests (`304`), `ETag`, `Age`, stale-while-revalidate, negative
caching, compressed storage, and invalidation by a touched file
([Velocity response cache](../../../features/6.0/velocity-response-cache.md)). It only applies when Velocity serves
the site, and only to anonymous requests.

## Turning it on

The cache is off until a setting says `enabled: true` (since engine release 0.0.4.39). Enable it in a site file or the
`cache` module of the Velocity configuration (`qbixctl enmod cache`), with a cache directory, and restart; the feature
page has the exact settings and the order in which configuration files win.

## After a change

```bash
php bin/php/console exp:velocity cache clear --allow-root-user
```

after template, stylesheet or setting changes, and after the content cache was cleared. `exp:velocity deploy` clears
it as its last step. Clearing it does not need a restart.

## Checking it

```bash
php bin/php/console exp:velocity cache stats --allow-root-user
curl -sI https://www.example.com/ | grep -i -E "age|etag"
```

A second anonymous request for the same page should show an `Age` header.

## Related pages

- [Performance and caching for websites](performance-and-caching.md)
- [Deploying with Velocity](../app-creators/deploying-with-velocity.md)
- [Velocity web server](../../../features/6.0/velocity-web-server.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
