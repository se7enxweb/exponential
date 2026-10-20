---
title: Clear the Velocity response cache
audience: government, admin, hosting
category: public-sector
order: 300
summary: Step by step - make Velocity serve freshly rendered pages after a template change, a correction or a restore, with exp:velocity cache clear, at the right moment, and check with the X-Cache and Age headers.
keywords: Velocity, response cache, cache clear, exp:velocity cache clear, generation marker, X-Cache, Age, stale pages, after deploy
type: help
---
# Clear the Velocity response cache

This task is for administrators of sites served by Exponential Velocity. Velocity's response cache answers repeat
requests for public pages inside the server, before PHP; after a change those answers may be old. Reference:
[Velocity response cache](../../../../features/6.0/velocity-response-cache.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Shell access.
- If the change involved PHP code or settings, deploy first: clearing before the workers run the new code lets old
  pages be stored again ([Deploy a PHP change](deploy-a-php-change.md), which clears this cache as its last steps).

## Steps

```bash
./console exp:velocity cache clear --allow-root-user
php bin/php/cache.php velocity status --allow-root-user
```

The clear touches the cache's generation marker: every entry stored before it is treated as a miss and replaced on its
next request; every worker and every server sharing the marker sees it within a second. Then warm the main pages
([Warm the caches after a deploy](warm-the-caches-after-a-deploy.md)).

## Check it worked

```bash
curl -sI https://www.example.gov/ | grep -iE 'x-cache|age'
```

The first request after the clear is a miss (rendered); the next carries `X-Cache: HIT` and a small `Age`. Check the
changed content in a private window.

## Related pages
- [Clear caches after a template change](clear-caches-after-a-template-change.md)
- [Stale cache](../../operations/incidents/stale-cache.md)
- [Velocity response cache and HTTP cache](../../operations/availability/velocity-response-cache-and-http-cache.md)
- [Velocity response cache (hosting)](../../../hosting/performance/velocity-response-cache.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
