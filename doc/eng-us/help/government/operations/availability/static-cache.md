---
title: The static cache
audience: government, hosting, admin
category: public-sector
order: 70
summary: For public-sector IT teams asked whether to turn on Exponential's static cache. What it is, why it ships off and the installation book says to leave it off, the rare cases where it still fits (for example an emergency information site), and how to generate, serve and remove it.
keywords: static cache, StaticCache, makestaticcache.php, static pages, var/site/static, publish cost, emergency site, fallback, Setup > Cache, staticcache_cleanup, cache status
type: help
---
# The static cache

This page is for IT teams in public bodies who are asked to "turn on the static cache", often because an older guide
recommended it. It answers what the static cache is, why it ships off, when it can still make sense for a public
body, and how to generate, serve and remove it. The sources are
[Static cache generator](../../../../features/6.0/static-cache-generator.md) and the hosting page
[Static cache](../../../hosting/performance/static-cache.md).

## Contents

1. [What it is](#what-it-is)
2. [Why it ships off](#why-it-ships-off)
3. [When it may still fit](#when-it-may-still-fit)
4. [Generating and checking it](#generating-and-checking-it)
5. [Serving it and switching it off](#serving-it-and-switching-it-off)
6. [Checklist](#checklist)

## What it is

The static cache turns public pages into plain files under `var/<site>/static` that the web server sends without
starting Exponential. The rewrite rules answer from a file only for a plain `GET` or `HEAD` without a query string
and without a session; forms, searches and signed-in users always get the live page.

## Why it ships off

`site.ini [ContentSettings] StaticCache=disabled` is the shipped value, and the installer writes it so. The
installation book says: "Leave the static cache off." With it on, every publish regenerated the affected pages on every
public siteaccess over HTTP inside the editor's request, so publishing became slow; Velocity's response cache gives
visitors the same benefit without that cost
([10.12 Performance tuning](../../../../install/10-after-installing.md#1012-performance-tuning)).

| | Static cache | Velocity response cache | Role-aware HTTP cache |
|---|---|---|---|
| Ships | off | on under Velocity | off |
| Served by | the web server, from a file | Velocity's server process | an early exit or Velocity |
| Signed-in visitors | never | never | yes, per permission context |
| Cost on publish | pages refreshed in the editor's request | none; entries expire | purges, no refetch |

From [Static cache](../../../hosting/performance/static-cache.md); the other two are on
[Velocity response cache and HTTP cache](velocity-response-cache-and-http-cache.md).

## When it may still fit

The feature page describes it as suited to "sites with mostly public, rarely changing pages". For a public body that
can mean:

- a small information site served by Apache or nginx where Velocity and the HTTP cache are not available;
- an emergency or crisis information site that must keep answering under a sudden surge, where a few seconds of extra
  publish time are acceptable.

Agree the publish cost with the editors first. With `[CacheSettings] CronjobCacheClear=enabled` the
`staticcache_cleanup` cronjob part does the clearing instead of the publish.

## Generating and checking it

See what it would do first:

```bash
php bin/php/cache.php static status --allow-root-user
```

Expected: a list of the sites that can be generated and a line such as `the static cache is not enabled in site.ini
[ContentSettings] StaticCache; 3 sites can be generated`. Only siteaccesses that need no login are offered. Generate
one site, limited so you can see it work:

```bash
php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
```

The same is in the administration under **Setup > Cache management**, section *Static content cache*, with a *Stop*
button ([Operating a site](../../../../guides/operating-a-site.md)).

## Serving it and switching it off

To make the web server answer from the files, set `StaticCache=enabled` in `settings/override/site.ini.append.php`,
block `[ContentSettings]`, clear the INI cache and check that the web server rules of your installation are active:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

To go back, set it to `disabled`, clear the INI cache again, and remove the generated files:

```bash
php bin/php/cache.php static clear --allow-root-user
```

Pages served from files bypass the application, so they are not in Exponential's request logs. Keep that in mind for
statistics and investigations.

## Checklist

- [ ] The default (off) is kept unless a documented reason applies.
- [ ] If on: editors accept the publish cost, or `CronjobCacheClear` is enabled.
- [ ] `cache.php static status` shows the expected sites and page counts.
- [ ] The procedure to switch it off and clear the files is in your runbook.

## Related pages

- [Availability](README.md) index of this folder
- [Cache warming after changes](cache-warming-after-changes.md)
- [Caching layers](../../../hosting/performance/caching-layers.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
