---
title: Static and response cache
audience: businesses, admin, website-creator, hosting
category: business-solutions
order: 120
summary: The two caches that answer visitors without running the application, the static cache and Velocity's response cache, what each one costs and gives a business, when to use which, and how to keep them from serving stale or personal pages.
keywords: static cache, response cache, Velocity response cache, makestaticcache, staticcache.ini, stale-while-revalidate, negative caching, X-Cache, Age, anonymous visitors, offload
type: help
---
# Static and response cache

This page is for managers deciding how to keep public pages fast and available under load, and for the operator who
switches the caches on. Both caches described here answer a visitor without starting the application, so they are
the strongest protection against a traffic spike. They work differently, and they have different costs. The general
picture is on [Caching for availability](caching-for-availability.md).

## Contents

1. [Two caches compared](#two-caches-compared)
2. [The Velocity response cache](#the-velocity-response-cache)
3. [The static cache](#the-static-cache)
4. [Which one for which business](#which-one-for-which-business)
5. [Keeping personal pages out](#keeping-personal-pages-out)
6. [After a change](#after-a-change)
7. [Checklist](#checklist)

## Two caches compared

| | Velocity response cache | Static cache |
|---|---|---|
| Where it lives | Inside the Velocity server | Plain files under `var/<site>/static`, sent by the web server |
| Needs | Velocity serving the site | Web server rules that answer from the files |
| Shipped state | On in Exponential's `settings/velocity.ini` (`[CacheSettings] Enabled=enabled`) | Off (`[ContentSettings] StaticCache=disabled`) |
| Who is served from it | Anonymous visitors only | Anonymous visitors only: no query string, no session |
| How pages get in | The first request for a page stores it | A crawl of the site, then refreshes on publish |
| How long a page lives | `[CacheSettings] DefaultTtl`, 30 seconds shipped, unless the page states its own | Until a publish refreshes it |
| Cleared by | `exp:velocity cache clear` | `php bin/php/cache.php static clear` |

Sources: `settings/velocity.ini`, [Caching layers](../../hosting/performance/caching-layers.md#the-layers-at-a-glance),
[Static cache generator](../../../features/6.0/static-cache-generator.md),
[Operating a site, turn on the static cache](../../../guides/operating-a-site.md#4-turn-on-the-static-cache).

## The Velocity response cache

It "keeps rendered public pages and answers repeat requests inside the server process, before a worker is chosen, so
a hit costs no worker at all" ([Velocity response cache](../../../features/6.0/velocity-response-cache.md)). It
behaves like a small reverse proxy. The features that protect availability:

| Feature | What it does for you |
|---|---|
| Stale-while-revalidate | An expired page is still served while one request renders a new copy, so a busy page never sends every visitor to PHP at once |
| Negative caching | "Not found" answers can be kept for a while, so repeated requests for a missing page cost little |
| Conditional requests (`304`) and `ETag` | A returning browser gets a short "not modified" answer instead of the whole page |
| A reload does not bypass it | "A visitor's own `Cache-Control: no-cache` does not bypass the cache, so pressing reload cannot make the server render every page again" |
| Maintenance aware | While maintenance mode is on, the cache does not answer, so the maintenance page is shown ([Maintenance mode guide](../../../guides/maintenance-mode.md#9-apache-velocity-and-the-caches)) |

The feature page documents its own measurement: "A cached public page is served in about 0.2 ms of CPU (measured on
a 12-core host: a front page over TLS with gzip, about 2,950 pages a second from one server, 11,985 from four servers
on one port)". As with every benchmark, your pages and hardware decide your result; see
[Velocity for businesses](velocity-for-businesses.md#the-documented-figures-read-honestly).

The trade-off is written into the shipped settings: with `DefaultTtl=30` "a visitor may see a page up to this old"
(`settings/velocity.ini`, `[CacheSettings]`). For most public pages half a minute is invisible; for a stock counter or
a live score it may not be. Ask your developer which pages must never be cached and how they say so.

## The static cache

"The static cache stores public pages as plain files that the web server can send without starting Exponential"
([Operating a site](../../../guides/operating-a-site.md#4-turn-on-the-static-cache)). It suits sites with mostly
public, rarely changing pages ([Static cache generator](../../../features/6.0/static-cache-generator.md)).

How it is switched on, in outline:

1. Generate the pages: **Setup > Cache > Static content cache**, or
   `php bin/php/makestaticcache.php --site=site --allow-root-user` (defaults: up to 2,500 pages, link depth 12).
2. Switch on `StaticCache=enabled` in `[ContentSettings]` and clear the INI cache.
3. Make the web server answer from the files: on Apache with `.htaccess`, the shipped `.htaccess_root_static`
   adds the rules.

Only siteaccesses that need no login are offered, because their pages are the same for everybody; forms, searches and
signed-in visitors always get the live page.

> **Note**
> The website creators' guide recommends leaving the static cache off unless you need it: with it on, a publish
> regenerates the affected pages over HTTP inside the editor's request, which made publishing slower
> ([Static cache and the Velocity response cache](../../creators/website-creators/static-cache-and-velocity.md#leave-the-static-cache-off)).
> Since 1 October 2026 a publish fetches each page once and several at a time
> ([Static cache generator](../../../features/6.0/static-cache-generator.md#refreshing-on-publish-once-each-in-parallel-2026-10-01)),
> but the cost still grows with the number of pages a publish touches. Measure on a copy of your site.

## Which one for which business

| Your situation | Suggestion |
|---|---|
| Velocity serves the site | Rely on the response cache; it is on in the shipped settings |
| Shared hosting with Apache, mostly public pages that rarely change | Consider the static cache, after measuring publish times |
| Many editors publishing all day | Avoid the static cache; let the view cache and the response cache work |
| Most traffic signed in (intranet, member area, checkout) | Neither helps much; plan capacity for rendered pages ([Peak traffic planning](peak-traffic-planning.md)) |
| A CDN in front | It can cache public pages too; it does not store the maintenance page, but may hold older pages after a change ([Maintenance mode guide](../../../guides/maintenance-mode.md#9-apache-velocity-and-the-caches)) |

## Keeping personal pages out

Both caches are for anonymous pages only, and both protect personal pages by design:

- The response cache skips any request with an `Authorization` header or a session cookie, and any response that sets
  a cookie or says `private` or `no-store` ([Velocity response cache](../../../features/6.0/velocity-response-cache.md#what-is-kept)).
- The static cache answers from a file only for a plain `GET` or `HEAD` with no query string and no session.

The response cache page warns: "Do not cache personalised pages." If your developer builds pages that differ between
anonymous visitors without a cookie to tell them apart (for example by location), they must mark them so.

## After a change

- After a template, stylesheet or settings change: `php bin/php/console exp:velocity cache clear --allow-root-user`.
  `exp:velocity deploy` clears it as its last step
  ([Static cache and the Velocity response cache](../../creators/website-creators/static-cache-and-velocity.md#after-a-change)).
- Static cache files are refreshed on publish; remove all with `php bin/php/cache.php static clear --allow-root-user`.
- Check the response cache works: a second anonymous request for the same page carries an `Age` header and
  `X-Cache: HIT`.

## Checklist

- [ ] You know which of the two caches your site uses, and why.
- [ ] The response cache lifetime fits your content (no page shown older than the business accepts).
- [ ] Pages that must never be cached are marked so by your developer.
- [ ] If the static cache is on, publish times were measured and are acceptable to editors.
- [ ] Caches are cleared after deploys in the right order ([Graceful deploys](graceful-deploys.md)).

## Related pages

- [Caching for availability](caching-for-availability.md)
- [Velocity response cache (hosting)](../../hosting/performance/velocity-response-cache.md)
- [Static cache (hosting)](../../hosting/performance/static-cache.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
