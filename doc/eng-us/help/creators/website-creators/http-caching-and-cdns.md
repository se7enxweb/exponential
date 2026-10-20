---
title: HTTP caching, proxies and CDNs
audience: website-creator, admin
category: website-creation
order: 640
summary: Let browsers, reverse proxies and CDNs keep pages and static files for anonymous visitors - HTTPHeaderSettings, StaticMaxAge, the role-aware HTTP cache, trusted proxies, and what never to cache.
keywords: HTTP caching, Cache-Control, Expires, CDN, reverse proxy, HTTPHeaderSettings, CustomHeader, StaticMaxAge, httpcache, exphttpcache, TrustedProxies, anonymous visitors, website creator
type: help
---
# HTTP caching, proxies and CDNs

This page is for website creators who put a site behind a CDN or a reverse proxy, or simply want returning visitors'
browsers to keep stylesheets and images. By default every page says "do not cache"; you open it up for anonymous
visitors only ([HTTP caching for anonymous visitors](../../../bc/6.0/http-caching.md)).

## Contents

1. [What is sent by default](#what-is-sent-by-default)
2. [Pages for anonymous visitors](#pages-for-anonymous-visitors)
3. [Static files](#static-files)
4. [The role-aware HTTP cache](#the-role-aware-http-cache)
5. [Behind a proxy or CDN](#behind-a-proxy-or-cdn)
6. [Never cache](#never-cache)

## What is sent by default

Pages: `Cache-Control: no-cache, must-revalidate`, an expired `Expires` and `Pragma: no-cache`. Nothing changes for a
logged-in user in any configuration.

## Pages for anonymous visitors

The page headers are set in `site.ini [HTTPHeaderSettings]` (`CustomHeader=disabled` by default). The BC page shows the
settings to send a short `Cache-Control: public, max-age=...` to anonymous visitors only. Choose a short lifetime (a
few minutes) unless content rarely changes: a browser keeps what it has until it expires.

Check:

```bash
curl -sI https://www.example.com/ | grep -i -E "cache-control|expires|pragma"
```

## Static files

Velocity sends static files with a lifetime set by `StaticMaxAge`; packed stylesheets and scripts have the
modification time in their names, so a long lifetime is safe for them. Lower it while you work on a stylesheet.
Apache and nginx need their own rules for `design/`, `extension/*/design/` and `var/storage/` files.

## The role-aware HTTP cache

`settings/httpcache.ini` switches on the kernel's HTTP cache (`exphttpcache`), which stores responses per set of roles;
it is cleared with the content and template tags. See [The HTTP cache](../../../bc/6.0/httpcache.md).

## Behind a proxy or CDN

- List the proxy's addresses in `[HTTPHeaderSettings] TrustedProxies[]`, so forwarded scheme and host are believed
  only from it ([Trusted proxies](../../../bc/6.0/trusted-proxies.md),
  [8.8 Behind a reverse proxy](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).
- Let the CDN cache only what the site marks as cacheable; logged-in sessions carry a cookie the CDN must pass through.
- Purge the CDN after design changes, or rely on the packer's changing file names.

## Never cache

- Pages for logged-in users, baskets, checkout, account pages.
- Forms with a form token (the token is per session).
- The administration.

## Related pages

- [Static cache and the Velocity response cache](static-cache-and-velocity.md)
- [Performance and caching for websites](performance-and-caching.md)
- [HTTP/2 and cache warming](../../../bc/6.0/http2-and-cache-warming.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
