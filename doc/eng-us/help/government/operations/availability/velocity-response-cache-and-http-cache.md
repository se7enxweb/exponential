---
title: Velocity response cache and HTTP cache
audience: government, hosting, admin
category: public-sector
order: 80
summary: For IT teams that need a public-sector site to stay fast and reachable under load. The two caches that answer before Exponential renders a page, what each stores and never stores, how fresh pages are, and how to check and clear them after a change.
keywords: response cache, Velocity cache, HTTP cache, role-aware cache, httpcache.ini, DefaultTtl, StaleWhileRevalidate, X-Cache, X-Exp-Cache, cache clear, freshness, traffic surge, availability under load
type: help
---
# Velocity response cache and HTTP cache

This page is for IT teams in public bodies whose sites see traffic peaks: an election night, a storm warning, a
deadline for applications. It answers which caches answer before Exponential renders a page, what they never store, how
fresh the pages they serve are, and how to check and clear them. The references are
[Velocity response cache](../../../../features/6.0/velocity-response-cache.md), [The role-aware HTTP cache](../../../../bc/6.0/httpcache.md)
and the hosting pages [Velocity response cache](../../../hosting/performance/velocity-response-cache.md) and
[Caching layers](../../../hosting/performance/caching-layers.md).

## Contents

1. [Two caches in front of the application](#two-caches-in-front-of-the-application)
2. [What is never cached](#what-is-never-cached)
3. [Freshness and urgent corrections](#freshness-and-urgent-corrections)
4. [Checking them](#checking-them)
5. [Clearing them in the right order](#clearing-them-in-the-right-order)
6. [Checklist](#checklist)

## Two caches in front of the application

| | Velocity response cache | Role-aware HTTP cache |
|---|---|---|
| Ships | on under Velocity (`velocity.ini [CacheSettings] Enabled=enabled`) | off (`httpcache.ini Enabled=disabled`) |
| Serves | anonymous visitors | anonymous visitors and signed-in users, per permission context |
| Answered by | Velocity's server process, before a worker | an early exit under Apache or PHP-FPM, or Velocity's server process |
| Header to look for | `X-Cache`, `Age` | `X-Exp-Cache` |

The response cache "keeps rendered anonymous pages and answers repeat requests inside the server process, before a
worker is chosen: no fork, no PHP, no database"
([Velocity response cache](../../../hosting/performance/velocity-response-cache.md)). The HTTP cache keeps whole pages
per permission context, so that users with the same roles share one copy ([The role-aware HTTP cache](../../../../bc/6.0/httpcache.md)).

For availability this matters: while the cache answers, a page can still be served when PHP workers are busy, and the
database is spared the load of a surge. It does not keep a site up when the database or the server is down for pages
nobody requested recently.

## What is never cached

From the response cache's documentation:

- signed-in visitors are never served from it; a page carrying a form token is sent `private`;
- a request with the session cookie or `is_logged_in`, or an `Authorization` header;
- a response that sets a cookie or says `no-store` or `private`;
- anything but `GET`, and statuses other than 200 (a 404 only when `NotFoundSeconds` is above 0).

The response cache needs engine 0.0.4.26 or later: older versions could serve a signed-in visitor's pages to everybody.
Keep the engine current.

## Freshness and urgent corrections

`DefaultTtl=30` seconds is how long an anonymous page is kept when it brings no lifetime of its own;
`StaleWhileRevalidate=60` seconds is how long an expired page may still be served while one request renders the new one.
For a public body, decide whether that delay is acceptable for urgent corrections (a wrong phone number, a changed
deadline). It usually is; if not, clear the cache right after publishing the correction (below), or lower the values in
`settings/override/velocity.ini.append.php` and restart Velocity.

## Checking them

```bash
./console exp:cache status --allow-root-user
php bin/php/cache.php velocity status --allow-root-user
php bin/php/cache.php httpcache status --allow-root-user
curl -sI https://www.example.gov/ | grep -i -E 'x-cache|x-exp-cache|age'
```

`exp:cache status` prints one line per layer. The status commands end in `PASS` with a line about entries and size
([Operating a site](../../../../guides/operating-a-site.md)).

## Clearing them in the right order

Clear the caches in front of PHP **after** PHP-FPM or Velocity runs the new code; otherwise a page rendered by the old
code in between is stored again. After a code or settings change, `exp:velocity deploy` does every step in the right
order. After a template change or an urgent content correction:

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user
./console exp:velocity cache clear --apply --allow-root-user
php bin/php/cache.php httpcache clear --allow-root-user
```

With several web servers, clear the response cache and HTTP cache on every node; they are local
([Several web servers behind a load balancer](several-web-servers-behind-a-load-balancer.md)). Maintenance mode pauses
Velocity's response caches while it is on ([Maintenance mode](../../../../features/6.0/maintenance-mode.md)).

## Checklist

- [ ] The engine version supports the response cache safely (0.0.4.26 or later).
- [ ] The freshness values are accepted by the communications team, or changed.
- [ ] The HTTP cache decision (on or off) is recorded.
- [ ] Runbooks clear caches after the reload, on every node.
- [ ] `exp:cache status` is part of the weekly checks.

## Related pages

- [Availability](README.md) index of this folder
- [Cache warming after changes](cache-warming-after-changes.md)
- [Static cache](static-cache.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
