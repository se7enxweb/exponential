---
title: HTTP caching and reverse proxies
audience: web-framework-developer, developer
category: web-frameworks
order: 290
summary: HTTP-level caching in Exponential for developers who know Symfony's HttpCache, Varnish setups, Laravel response caching or Django's cache middleware - page headers for anonymous visitors from settings, the role-aware HTTP cache with its early exit, Velocity's built-in response cache, trusted proxies, CDNs, and what must never be cached.
keywords: HTTP cache, Cache-Control, Expires, HTTPHeaderSettings, CustomHeader, OnlyForAnonymous, httpcache.ini, X-Exp-Cache, early exit, Velocity response cache, reverse proxy, Varnish, CDN, TrustedProxies, ETag, web framework developer
type: help
---
# HTTP caching and reverse proxies

This page is for developers who set `Cache-Control` from a controller, put Varnish in front, or use a framework's
HTTP cache. In Exponential, page headers are **settings**, and there are two built-in page caches that sit in front of
the kernel. Nothing is cached at the HTTP level until you switch it on.

## Contents

1. [What is sent by default](#what-is-sent-by-default)
2. [Headers for anonymous visitors](#headers-for-anonymous-visitors)
3. [The role-aware HTTP cache](#the-role-aware-http-cache)
4. [The Velocity response cache](#the-velocity-response-cache)
5. [Behind a proxy or CDN](#behind-a-proxy-or-cdn)
6. [Never cache](#never-cache)
7. [Compared with other frameworks](#compared-with-other-frameworks)

## What is sent by default

Pages: `Cache-Control: no-cache, must-revalidate`, an expired `Expires` and `Pragma: no-cache`. So no browser, proxy or
CDN keeps a page until you decide otherwise ([HTTP caching for anonymous visitors](../../bc/6.0/http-caching.md)).

## Headers for anonymous visitors

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
CustomHeader=enabled
OnlyForAnonymous=enabled
OnlyForContent=enabled

HeaderList[]
HeaderList[]=Cache-Control
HeaderList[]=Expires

Cache-Control[]
Cache-Control[/]=public, max-age=60
Expires[]
Expires[/]=60
```

`OnlyForAnonymous` is what makes this safe: a signed-in user always receives the no-cache headers. The keys are paths,
so different parts of the tree can get different lifetimes. The trade is staleness: an anonymous visitor may see a page
up to `max-age` seconds old after an edit.

## The role-aware HTTP cache

The kernel's HTTP cache (`settings/httpcache.ini`, `[HttpCacheSettings] Enabled=disabled` shipped) stores rendered
content views keyed by scheme, host, siteaccess, path and the visitor's **permission context**: one copy for anonymous
visitors, one per set of roles and role limitations, and a private copy only where a `content/read` policy depends on
the user. The next request is answered before the kernel starts: by an early exit included from `config.php` under
Apache, PHP-FPM or FrankenPHP, and by the server process itself under Velocity. The `X-Exp-Cache` header says `HIT`,
`STALE`, `MISS` or `BYPASS` with a reason. The visitor's own form token is put back into the stored page on every hit
([The role-aware HTTP cache](../../bc/6.0/httpcache.md)).

This is unusual: most framework HTTP caches only serve anonymous traffic. Here signed-in members share cached pages
safely because the key is their permission context.

## The Velocity response cache

Velocity can answer repeat requests for public pages inside the server process, before a worker is chosen: conditional
requests (`304`), `ETag`, `Age`, stale-while-revalidate and invalidation, like a small reverse proxy built into the
server. It is off until a setting enables it ([Velocity response cache](../../features/6.0/velocity-response-cache.md)).
Clear it last in a deploy: `./console exp:velocity cache clear`.

## Behind a proxy or CDN

- List the proxy's addresses in `site.ini [HTTPHeaderSettings] TrustedProxies[]`, so forwarded scheme and host are
  believed only from them ([Trusted proxies](../../bc/6.0/trusted-proxies.md),
  [Behind a reverse proxy](../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).
- Let the CDN cache only what the site marks as cacheable; signed-in sessions carry a cookie the CDN must pass through.
- For a purging proxy in front, `httpcache.ini [HttpCacheSettings] TagHeader` can send the purge tags of each page
  (its nodes, objects and layouts) in a header such as `xkey` (Varnish xkey vmod), `Surrogate-Key` (Fastly) or
  `Cache-Tag` (Cloudflare); it is `disabled` by default because the tags name internal ids. Sending the purge request
  to the proxy on publish is yours to add, for example in a `content/cache` listener
  ([Events and listeners](events-and-listeners.md)).

## Never cache

From [HTTP caching and CDNs](../creators/website-creators/http-caching-and-cdns.md#never-cache): pages for signed-in
users in a shared cache, baskets, checkout, account pages, forms with a form token (the token is per session), and the
administration.

## Compared with other frameworks

| Framework | Concept | Exponential |
|---|---|---|
| Symfony | `HttpCache` reverse proxy, `#[Cache]`, ESI | settings-driven headers, the role-aware HTTP cache, the Velocity response cache; no ESI |
| Laravel | response cache packages, `Cache-Control` middleware | the same settings and caches |
| Django | `cache_page`, `UpdateCacheMiddleware` | the same |
| Rails | `expires_in`, `fresh_when` | headers per path in settings |

## Related pages

- [Caching: the layers](caching-layers.md)
- [HTTP/2 and cache warming](../../bc/6.0/http2-and-cache-warming.md)
- [Static cache and Velocity](../creators/website-creators/static-cache-and-velocity.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
