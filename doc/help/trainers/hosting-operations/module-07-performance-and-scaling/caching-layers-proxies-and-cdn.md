---
title: "Lesson 7.2: Caching layers, HTTP headers, proxies and CDN"
audience: trainer, hosting, admin
category: training
order: 72
summary: Learners name the caching layers of a site from the browser to the database, read the headers that show which layer answered, switch on cacheable headers for anonymous visitors only, and set the rules for a proxy or CDN in front. 45 minutes.
keywords: hosting and operations, caching layers, HTTP cache headers, Cache-Control, OnlyForAnonymous, X-Cache, role-aware HTTP cache, Varnish, reverse proxy, CDN, TrustedProxies, X-Forwarded-Proto, purge
type: help
---
# Lesson 7.2: Caching layers, HTTP headers, proxies and CDN

An Exponential site has up to twelve caching layers, from the visitor's browser to OPcache. Support staff who know
them can tell where a stale or slow page comes from; hosts who know them decide what a plan switches on, and keep a
proxy or CDN from ever serving a signed-in page to a stranger. This lesson maps the layers and the rules.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 7: Performance and scaling](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate to advanced |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 7.1](benchmarking.md); [Lesson 2.4](../module-02-velocity/response-cache-and-https.md) |

## Learning objectives

After this lesson, learners can:

- list the caching layers, which ship on and which off, and how each is cleared;
- read the default page headers and explain why no shared cache may keep a page by default;
- switch on cacheable headers for anonymous visitors only, and explain why `OnlyForAnonymous` is required;
- name what a proxy in front must send and how it is trusted;
- state the rules for a CDN: static files at the edge, pages only with anonymous cacheable headers, never a signed-in
  response, and no purges sent by Exponential.

## Trainer's notes

### How to present it

Source: [Caching layers](../../../hosting/performance/caching-layers.md),
[HTTP cache headers](../../../hosting/performance/http-cache-headers.md),
[Varnish and reverse proxies](../../../hosting/performance/varnish-and-reverse-proxies.md) and
[CDN](../../../hosting/performance/cdn.md).

1. **The layers** (6 minutes). Show the table of *Caching layers*: browser cache of static files and of pages, CDN or
   proxy, Velocity's response cache, the role-aware HTTP cache, the static cache, the content view cache, template
   blocks, compiled templates and INI, Redis or Valkey (with the extension), the SQL query cache, OPcache and APCu.
   Point out which ship on (view cache, template blocks, response cache under Velocity) and which off.
2. **Default headers** (3 minutes). Pages carry `Cache-Control: no-cache, must-revalidate`; nothing personal can leak
   through a shared cache you did not configure.
3. **Anonymous cacheable pages** (4 minutes). `[HTTPHeaderSettings] CustomHeader=enabled` with
   `OnlyForAnonymous=enabled` and a short `max-age`; a signed-in editor always gets no-cache headers. Never offer it
   without `OnlyForAnonymous`.
4. **Proxies** (4 minutes). Velocity needs no proxy for TLS. A proxy must set `X-Forwarded-Proto` and `X-Forwarded-For`
   and pass the original `Host`; it must be trusted (`TrustedProxies[]` under PHP-FPM, `Q.webserver.proxy.trusted`
   under Velocity). Exponential sends no purge requests.
5. **CDN** (3 minutes). Static files and content images at the edge (image URLs change when the image changes); pages
   only with anonymous cacheable headers; never a response for a request with the session cookie.

### Common questions

**Do I still need Varnish?** The installation book: either drop it (the HTTP cache or Velocity's response cache does
the same job inside Exponential), let it cache anonymous pages for a short time only, or purge it yourself.

**Why do links go to `http://` behind the CDN?** The CDN is not trusted, or sends no `X-Forwarded-Proto`.

**A customer sees an old stylesheet.** Velocity sends a one-year lifetime for static files; a file replaced under the
same name is not fetched again until it expires. Packed files carry their modification time in the name.

### Pitfalls

- **Switching on cacheable headers without restarting Velocity.** Velocity holds settings in memory; clear the INI
  cache and restart, or deploy.
- **Configuring a CDN to ignore `private` or `no-cache`.** Never; a signed-in page cached at the edge is served to
  strangers.
- **Leaving the exercise's header settings in place.** Remove the override at the end.

## Hands-on exercise

Use your own training installation, served by Velocity.

1. Read the headers of the front page as an anonymous visitor:
   `curl -sI http://127.0.0.1:8088/ | grep -iE 'cache-control|expires|x-cache|age'`.
2. Read the headers of a stylesheet from the front page the same way and compare `Cache-Control`.
3. Sign in to the administration in a browser, open the front page with the browser's developer tools and read the
   `Cache-Control` of the page there.
4. Add the anonymous cacheable headers to `settings/override/site.ini.append.php` exactly as in
   [Cacheable pages for anonymous visitors](../../../hosting/performance/http-cache-headers.md#cacheable-pages-for-anonymous-visitors).
5. Clear the INI cache and restart: `php bin/php/ezcache.php --clear-tag=ini` and
   `php bin/php/console exp:velocity restart`.
6. Repeat step 1 and note the new `Cache-Control`.
7. Repeat step 3 while signed in and check that the page still says `no-cache`.
8. Remove the lines you added in step 4, clear the INI cache and restart again.
9. On paper, write the three settings a proxy on another machine needs before it can sit in front of your site: what it
   sends, and where it is trusted.

## Expected result

- Step 1: `no-cache, must-revalidate` on the page; step 2: a long `max-age` on the stylesheet.
- Step 6: `public, max-age=60` for the anonymous visitor.
- Step 7: the signed-in page still carries no-cache headers.
- After step 8 the page is back to the defaults.
- Step 9 names `X-Forwarded-Proto`, `X-Forwarded-For`, the original `Host`, and the trust list for the server in use.
- The trainer checks steps 6 and 7 side by side for one learner.

## Quiz

1. Which caching layers ship switched on? Name two.
2. Why is `OnlyForAnonymous=enabled` required when pages are made cacheable?
3. Does Exponential send purge requests to a proxy or CDN?
   A. yes, on every publish B. yes, with the HTTP cache C. no D. only for images
4. Links behind a load balancer go to `http://`. What is wrong?
5. Why can content images be cached long at the edge?

<details>
<summary>Answers</summary>

1. Any two of: the content view cache, the template-block cache, compiled templates and INI, Velocity's response cache
   under Velocity ([Caching layers](../../../hosting/performance/caching-layers.md#the-layers-at-a-glance)).
2. A signed-in editor then always gets no-cache headers, so a personalized page never reaches a shared cache.
3. **C.** A proxy must be told what to store and for how long, or purged by you.
4. The proxy is not trusted or sends no `X-Forwarded-Proto`.
5. Exponential changes an image's URL when the image is updated.

</details>

## Further reading

- [Caching layers](../../../hosting/performance/caching-layers.md)
- [HTTP cache headers](../../../hosting/performance/http-cache-headers.md)
- [Varnish and reverse proxies](../../../hosting/performance/varnish-and-reverse-proxies.md)
- [CDN](../../../hosting/performance/cdn.md)
- [Static cache](../../../hosting/performance/static-cache.md): why it ships off.

## Related pages

- [Module 7: Performance and scaling](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
