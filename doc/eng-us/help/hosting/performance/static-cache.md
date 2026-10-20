---
title: Static cache
audience: hosting, admin
category: hosting
order: 100
summary: The static cache for hosts: pages generated as plain files and served by the web server without PHP, why it ships off and the installation book says to leave it off, when a customer might still want it, how to generate and serve it on Apache, and the settings.
keywords: static cache, StaticCache, staticcache.ini, makestaticcache.php, .htaccess_root_static, staticcache_offload.conf, EZ_VAR_SITE, CachedURLArray, MaxCacheDepth, CachedSiteAccesses, FetchConcurrency, staticcache_cleanup, publish cost
type: help
---
# Static cache

This page is for system administrators at hosting companies who are asked about the static cache, or who see it
mentioned in old hosting guides. It answers: what it is, why it ships off, when it still makes sense, and how to
generate and serve it if a customer needs it. The sources are
[Static cache generator](../../../features/6.0/static-cache-generator.md),
[Static cache defaults](../../../bc/6.0/static-cache-defaults.md) and
[Chapter 10, section 10.12](../../../install/10-after-installing.md#1012-performance-tuning).

## Contents

1. [What it is](#what-it-is)
2. [Why it ships off](#why-it-ships-off)
3. [When a customer might still want it](#when-a-customer-might-still-want-it)
4. [Generating it](#generating-it)
5. [Serving it from the web server](#serving-it-from-the-web-server)
6. [Settings](#settings)
7. [Checklist](#checklist)

## What it is

The static cache turns public pages into plain files under `var/<site>/static` that the web server sends without
starting Exponential at all. Rewrite rules answer a request from the file when it is a plain `GET` or `HEAD`, has no
query string and carries no session; forms, searches and anyone signed in always get the live page.

## Why it ships off

`site.ini [ContentSettings] StaticCache=disabled` is the shipped value, and the installer writes it so. The
installation book is direct: "**Leave the static cache off.** ... With the static cache on, every publish regenerated
every affected page on every public siteaccess over HTTP inside the editor's request, so a publish that changed
nothing took seconds instead of a fraction of one. Velocity's response cache gives the same benefit to visitors
without that cost." ([Chapter 10, section 10.12](../../../install/10-after-installing.md#1012-performance-tuning))

The 6.0 work made the refresh cheaper (each page fetched once, several at a time: a no-change publish that refreshed
24 pages in 4975 ms now refreshes 14 pages in about 0.5 s), but the cost still lands in the editor's request
([Static cache generator](../../../features/6.0/static-cache-generator.md#refreshing-on-publish-once-each-in-parallel-2026-10-01)).

| | Static cache | Velocity response cache | Role-aware HTTP cache |
|---|---|---|---|
| Ships | off | on under Velocity | off |
| Served by | the web server, from a file | Velocity's parent process | an early exit or Velocity, before the kernel |
| Signed-in visitors | never | never | yes, per permission context |
| Cost on publish | pages refreshed over HTTP in the editor's request | none; entries expire | purges, no refetch |

## When a customer might still want it

Where neither Velocity nor the HTTP cache is available and a site is almost entirely public pages that rarely change
(the feature page's own description: "sites with mostly public, rarely changing pages"). Agree the publish cost with
the customer first. With `[CacheSettings] CronjobCacheClear=enabled` the `staticcache_cleanup` cronjob part does the
clearing instead of the publish.

## Generating it

From **Setup > Cache > Static content cache**: choose a public site, a page limit (2500, 1 to 20000) and a link depth
(12, 0 to 30), press *Create new* and watch each page being stored. From the command line:

```bash
php bin/php/makestaticcache.php --help --allow-root-user
php bin/php/makestaticcache.php --site=site --allow-root-user
php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
```

The generator crawls the site from its own root and follows only the links it writes, so it visits exactly the
addresses a visitor can request. A run empties the site's stored pages first unless `--keep` is given. Run it as the
site's user so the files stay writable for the web server.

## Serving it from the web server

| Setup | What to use |
|---|---|
| Apache with `.htaccess` | copy `.htaccess_root_static` to `.htaccess` instead of `.htaccess_root`; it is `.htaccess_root` plus the offload rules |
| Apache virtual host | the rules of [doc/eng-us/examples/staticcache_offload.conf](../../../examples/staticcache_offload.conf) in the `<Directory>` section of the **public** site's vhost |
| other servers | reproduce the same conditions (method, empty query string, no session cookie, file exists) |

Check the line `RewriteRule ^ - [E=EZ_VAR_SITE:site]`: the value is `site.ini [FileSettings] VarDir` without the
leading `var/`; a default installation (`var/site`) needs no change.

> **Warning**
> Never add the offload rules to the administration vhost: "nothing there is cached, and the rules must never be given
> a chance to answer an editor's request from a file generated for an anonymous visitor"
> (`doc/eng-us/examples/staticcache_offload.conf`).

## Settings

`settings/staticcache.ini`, `[CacheSettings]` (override in `settings/override/staticcache.ini.append.php`):

| Key | Default | Meaning |
|---|---|---|
| `HostName` | empty | deprecated; leave empty so each siteaccess is fetched from its own `SiteURL` |
| `SourceProtocol` | `http` | `http` or `https` for the fetch |
| `StaticStorageDir` | `static` | becomes `var/<site>/static`; an absolute path is used as given |
| `MaxCacheDepth` | `12` | deeper URLs are neither cached nor refreshed on publish |
| `CachedURLArray[]` | `/`, `/*` | URLs to cache |
| `CachedSiteAccesses[]` | empty | empty means every public siteaccess |
| `AlwaysUpdateArray[]` | `/` | URLs refreshed on every publish |
| `CronjobCacheClear` | `disabled` | `enabled` lets the `staticcache_cleanup` cronjob part clear instead of the publish |
| `FetchConcurrency` | `8` (in code) | pages fetched at once on refresh |

Installations upgraded from older releases may have overrides that relied on the old defaults; see
[Static cache defaults](../../../bc/6.0/static-cache-defaults.md).

## Checklist

- [ ] `StaticCache=disabled` on every plan unless a customer asked for it and accepted the publish cost.
- [ ] If used: offload rules only on the public vhost; `EZ_VAR_SITE` matches `VarDir`.
- [ ] Generation run as the site's user; the stored pages checked with a plain `curl`.
- [ ] Signed-in requests and requests with a query string still reach PHP.

## Related pages

- [Caching layers](caching-layers.md)
- [Velocity response cache](velocity-response-cache.md)
- [Apache with PHP-FPM](../servers/apache-with-php-fpm.md)
- [Static cache generator](../../../features/6.0/static-cache-generator.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
