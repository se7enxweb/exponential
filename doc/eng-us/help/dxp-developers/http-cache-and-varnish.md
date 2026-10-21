---
title: HTTP cache and Varnish
audience: dxp-developer
category: dxp-development
order: 29
summary: How the DXP caches whole responses with ibexa/http-cache - the built-in Symfony proxy by default, Varnish as an option, the purge settings, the time to live, purging by path and tag, and what to check when a page stays stale.
keywords: HTTP cache, ibexa/http-cache, Varnish, Symfony HttpCache, APP_HTTP_CACHE, HTTPCACHE_PURGE_TYPE, purge, invalidate, fos:httpcache, tags, TTL, TRUSTED_PROXIES
type: help
---
# HTTP cache and Varnish

This page is for developers and administrators who make a DXP site fast. The DXP ships `ibexa/http-cache`: whole
responses are cached, tagged with the content they show, and purged when that content changes. Without Varnish, the
Symfony HTTP cache proxy inside the application does the job.

## Contents

1. [The two modes](#the-two-modes)
2. [Settings](#settings)
3. [Switching to Varnish](#switching-to-varnish)
4. [Purging by hand](#purging-by-hand)
5. [When a page stays stale](#when-a-page-stays-stale)

## The two modes

| Mode | Set | Purge type |
|---|---|---|
| Symfony HTTP cache (default) | `APP_HTTP_CACHE=1` in the web server | `HTTPCACHE_PURGE_TYPE=local` |
| Varnish in front | `APP_HTTP_CACHE=0` (Varnish caches instead) | `HTTPCACHE_PURGE_TYPE=varnish` |

An installed project's `config/packages/ibexa.yaml` explains why a long time to live is safe: the HTTP cache bundle
"is designed to expire all affected cache on changes".

## Settings

| Variable | Guide's default | Meaning |
|---|---|---|
| `HTTPCACHE_PURGE_TYPE` | `local` | `local` or `varnish` |
| `HTTPCACHE_DEFAULT_TTL` | `86400` | Seconds a response may be cached |
| `HTTPCACHE_PURGE_SERVER` | `http://localhost:80` | Where purge requests go (Varnish address) |
| `HTTPCACHE_VARNISH_INVALIDATE_TOKEN` | unset | A shared secret for purge requests where an IP allow list is not possible |
| `TRUSTED_PROXIES` | unset | The proxy's address, so Symfony trusts its headers |

## Switching to Varnish

From the skeleton's guide (section 19):

1. Set in `.env.local`:

   ```bash
   HTTPCACHE_PURGE_TYPE=varnish
   HTTPCACHE_PURGE_SERVER=http://127.0.0.1:6081
   HTTPCACHE_VARNISH_INVALIDATE_TOKEN=<your secret>
   TRUSTED_PROXIES=127.0.0.1
   ```

2. Set `APP_HTTP_CACHE=0` in the web server configuration.
3. Load a VCL that understands the bundle's tags and purge requests. The guide points to a `doc/varnish/` directory
   "if present" or to the upstream documentation; no VCL ships in the skeleton itself.
4. Clear the cache and purge everything once:

   ```bash
   php bin/console cache:clear
   php bin/console fos:httpcache:invalidate:path / --all
   ```

Varnish 6.0 or 7.1 and newer are the versions the guide names.

## Purging by hand

```bash
php bin/console fos:httpcache:invalidate:path / --all     # every path
php bin/console fos:httpcache:invalidate:tag <tag>        # one tag
```

Normal publishing purges by itself; purge by hand only after a template or configuration deploy.

## When a page stays stale

1. Is it the browser? Reload without cache, or test with `curl -I`.
2. Is it the HTTP cache? Look at the `Age` and cache headers in the response.
3. Is it the application cache? Clear it ([Application cache and Redis](application-cache-and-redis.md)).
4. With Varnish: does the purge reach it? Check `HTTPCACHE_PURGE_SERVER` and the token, and Varnish's log.
5. After a deploy: did you warm the cache after clearing it ([Deploying](deploying-dxp.md))?

## Related pages

- [Application cache and Redis](application-cache-and-redis.md)
- [Environment variables](environment-variables.md#search-cache-mail-and-the-rest)
- [Web server setup](web-server-setup.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
