---
title: Velocity response cache
audience: hosting, admin
category: hosting
order: 50
summary: The response cache in Velocity's parent process for hosted sites: what it stores and never stores, the velocity.ini settings and their trade-offs, conditional requests and stale-while-revalidate, 404s and logs, clearing after a deploy, checking hits, and the browser navigation cache.
keywords: Velocity, response cache, CacheSettings, DefaultTtl, StaleWhileRevalidate, NotFoundSeconds, HonorRequestNoCache, MinifyHtml, SkipCookies, APCu, X-Cache, Age, ETag, 304, exp:velocity cache clear, generation marker, navigation cache, index.js, sw.js
type: help
---
# Velocity response cache

This page is for system administrators and support staff at hosting companies whose customer sites run on Velocity.
It answers: what the response cache stores, which settings decide how fresh pages are, how to check that it works,
how to clear it, and what to tell a customer who sees an old page. The sources are
[Chapter 8, section 8.3.8](../../../install/08-serving-the-site.md#838-the-response-cache),
[Velocity response cache](../../../features/6.0/velocity-response-cache.md) and
[The response cache, and getting out of the network's way](../../../bc/6.0/response-cache-and-navigation.md).

## Contents

1. [What it does](#what-it-does)
2. [What it never stores](#what-it-never-stores)
3. [The settings](#the-settings)
4. [Freshness against speed](#freshness-against-speed)
5. [Checking it](#checking-it)
6. [Clearing it](#clearing-it)
7. [The browser navigation cache](#the-browser-navigation-cache)
8. [Support answers](#support-answers)
9. [Checklist](#checklist)

## What it does

The response cache keeps rendered anonymous pages and answers repeat requests inside the server process, before a
worker is chosen: no fork, no PHP, no database. Measured on a 12-core host, a cached front page over TLS with gzip:
about 2,950 pages a second from one server, 11,985 from four servers on one port
([Velocity response cache](../../../features/6.0/velocity-response-cache.md)). It behaves like a small reverse proxy:

- conditional requests answered `304` with an `ETag` derived from the page, not the clock, so an unchanged page costs
  a returning browser 202 bytes of headers instead of the whole document;
- stale-while-revalidate: one request renders an expired page while the others get the old copy;
- `Date` on every response, `Age` and `X-Cache` on cached ones;
- small entries in APCu (shared by the workers), larger ones on disk in `var/cache/qbix-reverse`.

## What it never stores

From [Chapter 8](../../../install/08-serving-the-site.md#838-the-response-cache) and the feature page:

- signed-in visitors are never served from it; a page carrying a form token is sent `private`;
- a request with a cookie named in `SkipCookies` (empty means the session cookie and `is_logged_in`, which the kernel
  sets for signed-in visitors for exactly this purpose) or an `Authorization` header;
- a response that sets a cookie, or says `no-store` or `private`;
- anything but `GET`, and the server's own `/Q/` and `/.well-known/` paths;
- a status other than 200 (a 404 only when `NotFoundSeconds` is above 0).

> **Warning**
> The response cache needs qbix-webserver 0.0.4.26 or later: older versions matched the session cookie by its exact
> name, never recognized the per-siteaccess session cookie, and served a signed-in visitor's pages to everybody
> (`settings/velocity.ini`). Keep the engine current ([Velocity for hosts](../servers/velocity-for-hosts.md)).

## The settings

`settings/velocity.ini`, `[CacheSettings]` (override in `settings/override/velocity.ini.append.php`, then restart):

| Setting | Shipped | Meaning |
|---|---|---|
| `Enabled` | `enabled` | the cache is on |
| `DefaultTtl` | `30` | seconds an anonymous page is kept when it brings no lifetime of its own |
| `Dir` | `var/cache/qbix-reverse` | disk tier for entries too large for shared memory |
| `APCu`, `APCuMaxSize` | `enabled`, `0` | small entries in APCu; `0` leaves the server's 65536 bytes |
| `SkipCookies` | empty | cookies that mean "personal, do not cache"; a list replaces the default |
| `StaleWhileRevalidate` | `60` | seconds an expired page is still served while one request renders its replacement |
| `NotFoundSeconds` | `0` | seconds a 404 is remembered; `0` sends every 404 to PHP |
| `HonorRequestNoCache` | `enabled` | a request with `Cache-Control: no-cache` reaches PHP and its answer is stored again |
| `SweepEvery`, `SweepMaxAge` | `300`, `86400` | clearing expired entries off the disk |
| `FileMode`, `DirMode` | `0640`, `0750` | nothing else on the machine needs to read a cached page |
| `MinifyHtml` | `enabled` | stored pages without template indentation ("27 % smaller documents") |
| `MiddleOutCompression` | `disabled` | measured and left off |

## Freshness against speed

| Setting | Raise it and | Lower it and |
|---|---|---|
| `DefaultTtl` | more pages from the cache; a visitor may see a page up to this old after an edit | fresher pages, more renders |
| `StaleWhileRevalidate` | an expiry never sends every visitor to PHP at once | an expired busy page can bring a burst of renders |
| `NotFoundSeconds` | a crawler walking dead links does not render each one | every 404 reaches PHP, so `404.log` and the request classifier (`security.log`) see each one |

The shipped `NotFoundSeconds=0` is a deliberate choice: a 404 answered from the cache "was seen once a minute at
most" in the logs. For a host whose security monitoring reads those logs, keep it at 0.

The trade the file names for `DefaultTtl`: "a page that differs between anonymous visitors without a cookie telling
them apart would be shared between them." A customer who personalizes anonymous pages by something other than a
cookie needs `DefaultTtl=0` (only pages that bring their own lifetime are cached).

## Checking it

Request the same anonymous page twice; the second answer carries `X-Cache: HIT` and an `Age` header:

```bash
curl -sI https://example.com/ | grep -iE 'x-cache|age'
curl -sI https://example.com/ | grep -iE 'x-cache|age'
php bin/php/console exp:velocity cache stats     # where it is, how much it holds, when it was cleared
```

`/Q/health` (full figures need admin access) reports `hits`, `misses`, `hitRate`, `stale` and where hits came from.
Mostly `disk` with APCu enabled means APCu is not doing its part: check `apc.enable_cli`, which Velocity sets for the
server process. The control panel's **Cache** tab shows the live hit rate and can purge by URL or pattern.

## Clearing it

```bash
php bin/php/console exp:velocity cache clear --apply     # every cached page is rendered again on its next request
```

- After a template or stylesheet change: clear the template caches, then `exp:velocity cache clear`; no restart is
  needed.
- After a PHP change: `exp:velocity deploy`, which clears the response cache last, after the new code runs.
- Clearing works through a generation marker: every entry stored at or before its time is treated as a miss, and
  every worker and every instance sharing the directory sees it within a second.
- Setup > Caches has a **Clear Velocity's response cache** button in the group "Pages, queries and the server".

## The browser navigation cache

Under Velocity a service worker (`/index.js`; `/sw.js` loads it for older registrations) can answer same-origin page
navigations from the visitor's own browser and refresh them in the background. It never stores a page fetched with a
session cookie, a non-200, or `/admin`, `/user`, `/explayouts_ui` and `/api`. The consequence for support: "a
published change appears on the **second** view, not the first." The kill switch is to serve `index.js` as
`self.registration.unregister()`; every browser drops it within 24 hours
([The response cache, and getting out of the network's way](../../../bc/6.0/response-cache-and-navigation.md#added-a-navigation-cache-in-the-browser-indexjs)).

## Support answers

| The customer says | Check | Answer |
|---|---|---|
| "My edit does not show for visitors" | `X-Cache: HIT` and `Age` | it shows within `DefaultTtl` seconds, or after `exp:velocity cache clear`; with the navigation cache, on the second view |
| "Signed-in users see the old page" | not the response cache | signed-in visitors never get it; look at the view cache or the HTTP cache |
| "A 404 page appears in our logs only sometimes" | `NotFoundSeconds` | above 0, cached 404s do not reach PHP |
| "The site is slow after a deploy" | the cache was cleared | warm it ([Cache clearing and warming](cache-clearing-and-warming.md)) |

## Checklist

- [ ] Engine at 0.0.4.26 or later (in practice the suggested 0.0.4.44 or later).
- [ ] `DefaultTtl` agreed with the customer; `NotFoundSeconds` left at 0 where logs are monitored.
- [ ] Two requests show `X-Cache: HIT`; APCu enabled for the server process.
- [ ] Deploys clear the response cache last (`exp:velocity deploy` does).
- [ ] Support knows the navigation cache's "second view" behavior.

## Related pages

- [Caching layers](caching-layers.md)
- [Cache clearing and warming](cache-clearing-and-warming.md)
- [Velocity for hosts](../servers/velocity-for-hosts.md)
- [Velocity response cache](../../../features/6.0/velocity-response-cache.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
