---
title: Application cache and Redis
audience: dxp-developer
category: dxp-development
order: 30
summary: The DXP's application cache - the Symfony cache, the tag-aware cache pool for content (filesystem by default, Redis or Memcached as options), how to switch, how to clear each part, and sessions in Redis.
keywords: application cache, cache pool, CACHE_POOL, cache.tagaware.filesystem, cache.redis, cache.memcached, Redis, cache:clear, cache:warmup, cache:pool:clear, sessions
type: help
---
# Application cache and Redis

This page is for developers and administrators who tune the caches behind a DXP site. Three caches are easy to mix
up; this page separates them.

## Contents

1. [Three caches](#three-caches)
2. [The content cache pool](#the-content-cache-pool)
3. [Switching to Redis](#switching-to-redis)
4. [Clearing each cache](#clearing-each-cache)
5. [Sessions](#sessions)

## Three caches

| Cache | Holds | Where |
|---|---|---|
| Symfony cache | The compiled container, routes, Twig templates | `var/cache/<env>/` |
| Content cache pool | Repository data (content, locations, types), tag-aware | the pool named by `CACHE_POOL` |
| HTTP cache | Whole responses | the Symfony proxy or Varnish ([HTTP cache](http-cache-and-varnish.md)) |

## The content cache pool

`CACHE_POOL` names the pool; the default is `cache.tagaware.filesystem`. An installed project keeps the predefined
pools in `config/packages/cache_pool/`: `cache.tagaware.filesystem.yaml`, `cache.redis.yaml` and
`cache.memcached.yaml`. Each siteaccess uses the pool set in `ibexa.system.default.cache_service_name`
(`'%cache_pool%'`). A multi-repository installation needs a different pool per repository.

## Switching to Redis

```bash
CACHE_POOL=cache.redis
CACHE_DSN=redis://localhost:6379
```

Then clear the Symfony cache. Redis 6 or newer is the guide's minimum; it recommends Redis for production caching and
sessions. Redis needs the PHP `redis` extension.

## Clearing each cache

```bash
php bin/console cache:clear --env=prod                    # Symfony cache
php bin/console cache:warmup --env=prod                   # build it again before traffic arrives
php bin/console cache:pool:clear cache.tagaware.filesystem   # the content pool (or cache.redis)
php bin/console fos:httpcache:invalidate:path / --all     # the HTTP cache
```

In development only, the guide's "nuclear option" removes `var/cache/dev` and `var/cache/prod` and warms up again. Do
not delete cache directories on a production server under traffic; use the commands.

## Sessions

Sessions default to files under `var/sessions/<env>` (`SESSION_HANDLER_ID=session.handler.native_file`,
`SESSION_SAVE_PATH`). On several web servers, sessions must be shared; Redis is a common choice. Change the handler
in `.env.local` and test sign-in on every server.

## Related pages

- [HTTP cache and Varnish](http-cache-and-varnish.md)
- [Day-to-day commands](day-to-day-commands.md)
- [Troubleshooting](troubleshooting-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
