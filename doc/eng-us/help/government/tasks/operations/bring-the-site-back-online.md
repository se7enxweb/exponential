---
title: Bring the site back online
audience: government, admin, hosting
category: public-sector
order: 30
summary: Step by step - end a maintenance window safely - check the site from an allowed address, switch maintenance off, clear the caches in front of the site, check every server answers 200, resume cronjobs and record the window.
keywords: maintenance off, bring online, end maintenance, Bring the site back online, maintenance.php off, cache clear, Velocity cache, cronjobs, 200, maintenance.json
type: help
---
# Bring the site back online

This task is for administrators closing a maintenance window. At the end, everyone gets the real site again with
fresh caches, and the window is recorded. Reference: [Maintenance mode, section 14](../../../../guides/maintenance-mode.md#14-workflows-step-by-step).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [If it will not switch off](#if-it-will-not-switch-off)
5. [Related pages](#related-pages)

## Before you start

- The work is done and checked from an allowed address (front page, sign-in, a publish on a test item).
- No installation is running: the banner on **Setup > Maintenance** would say so.

## Steps

1. **Setup > Maintenance**, **Bring the site back online**; or on the shell:

   ```bash
   php bin/php/maintenance.php off --allow-root-user
   ```

2. If content or code changed, clear the caches in front of the site, after PHP and Velocity run the new code:

   ```bash
   ./console exp:velocity cache clear --apply --allow-root-user
   php bin/php/cache.php httpcache clear --allow-root-user
   ```

   Purge a CDN if you use one.
3. Warm the main pages: `php bin/php/preload.php --apply --siteaccess=site --allow-root-user`.
4. Resume paused cronjob parts on **Setup > Cronjobs** and run the frequent part once.

## Check it worked

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/      # 200
ls var/maintenance.json                                                 # No such file or directory
```

Check through every server or engine you run (Apache and Velocity ports). Note the end time in the change record; the
audit trail has `system.maintenance.change` for on and off.

## If it will not switch off

| Symptom | Fix |
|---|---|
| The administration shows the maintenance page too | `php bin/php/maintenance.php off --allow-root-user` on the shell |
| No PHP on the command line | remove the marker file `var/maintenance.json`; that is all maintenance is |
| The site comes back with old pages | clear Velocity's cache and the CDN |
| **An installation is running** but nothing installs | a kickstarter run stopped after a failed step; read the setup log before switching off |

## Related pages
- [Take the site into maintenance for an upgrade](take-the-site-into-maintenance-for-an-upgrade.md)
- [Warm the caches after a deploy](warm-the-caches-after-a-deploy.md)
- [Maintenance windows](../../operations/upgrades-and-change/maintenance-windows.md)
- [Stale cache](../../operations/incidents/stale-cache.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
