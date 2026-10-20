---
title: Warm the caches after a deploy
audience: government, admin, hosting
category: public-sector
order: 90
summary: Step by step - after a deploy or cache clear, request the important pages yourself (preload.php, warm.php or the Preload Sites view) so the first citizens do not wait for pages to be rendered.
keywords: cache warming, preload, warm.php, preload.php, Preload Sites, after deploy, first visitor, performance, WarmUpURL
type: help
---
# Warm the caches after a deploy

This task is for administrators after a deploy or a full cache clear. At the end the main pages are rendered and
cached before visitors ask for them. Reference: [Preloading caches](../../../../guides/preloading-caches.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The deploy is complete, including the PHP-FPM reload or Velocity restart; warming before that caches old pages.
- Shell access, or `setup/preload` for the **Preload Sites** view.

## Steps

Pick one:

1. **Section pages first, then links** (one run at a time; every run is listed on **Setup > Preload Sites**):

   ```bash
   php bin/php/preload.php --siteaccess=site --allow-root-user
   ```

2. **Every published page**, suitable for a cron entry:

   ```bash
   php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
   ```

3. **In the browser**: **Setup > Preload Sites** (`/setup/preload`), choose the site, a page limit (default 250) and a
   link depth (default 3), and watch the pages being fetched.

With Velocity, `exp:velocity deploy` already requests the addresses of `[DeploySettings] WarmUpURL[]` in its last
step; add your most visited pages there.

## Check it worked

```bash
curl -s -o /dev/null -w '%{time_total}s\n' https://www.example.gov/
```

Run it a few times: after warming, the time is the cached time from the first request. Use your own site's public
siteaccess name for `--siteaccess`.

## Related pages
- [Deploy a PHP change](deploy-a-php-change.md)
- [Cache warming after changes](../../operations/availability/cache-warming-after-changes.md)
- [Preload Sites view](../../../../features/6.0/preload-sites-view.md)
- [Cache clearing and warming (hosting)](../../../hosting/performance/cache-clearing-and-warming.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
