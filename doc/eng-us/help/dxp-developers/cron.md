---
title: Cron
audience: dxp-developer
category: dxp-development
order: 33
summary: The DXP's cron runner (ibexa/cron) - the one crontab line that runs every scheduled job, which user runs it, logging, and what does and does not run there (no publishing scheduler ships with the DXP).
keywords: cron, ibexa/cron, ibexa:cron:run, crontab, scheduled jobs, www-data, log, maintenance, scheduler
type: help
---
# Cron

This page is for developers and administrators who set up the scheduled jobs of a DXP project. The DXP ships
`ibexa/cron`: one console command runs every job that bundles have registered.

## Contents

1. [The crontab line](#the-crontab-line)
2. [Which user](#which-user)
3. [What runs there](#what-runs-there)
4. [Adding your own job](#adding-your-own-job)
5. [Checking it works](#checking-it-works)

## The crontab line

From the skeleton's guide (section 17), every five minutes:

```bash
*/5 * * * * /usr/bin/php /var/www/exponential/bin/console ibexa:cron:run --env=prod >> /var/log/exponential-cron.log 2>&1
```

The command still has its upstream name; there is no `exponential:` alias for it
([Console commands](console-commands-and-renames.md)). `--quiet` suppresses its output.

## Which user

Install it in the web server user's crontab (`crontab -e -u www-data`), so files it writes belong to the same user as
the web requests' files ([File permissions](file-permissions.md)).

## What runs there

Whatever the installed bundles registered as cron jobs. The DXP does **not** include a publishing scheduler, so no
"publish at a date" job exists out of the box ([What the DXP ships](what-the-dxp-ships-and-what-it-does-not.md#what-it-does-not-ship)).
Maintenance you may want to schedule yourself, as separate crontab lines:

| Job | Command |
|---|---|
| Prune old versions (nightly, test the `--keep` value first) | `exponential:content:cleanup-versions --keep=5` |
| Check external links | `exponential:check-urls` |
| Process queued messages, if you use an async transport | `messenger:consume ...` under a process manager ([Messenger](messenger-and-notifications.md)) |

## Adding your own job

Write a Symfony console command in `src/Command/` and add a crontab line for it, or register it with the cron bundle
if you want it to run inside `ibexa:cron:run`. Keep each job idempotent: it must be safe when it runs twice.

## Checking it works

```bash
sudo -u www-data php bin/console ibexa:cron:run --env=prod -vv   # run once by hand
tail -n 50 /var/log/exponential-cron.log
```

## Related pages

- [Messenger and notifications](messenger-and-notifications.md)
- [Day-to-day commands](day-to-day-commands.md)
- [DXP skeleton, Day to day](../../features/6.0/platform-dxp-skeleton.md#day-to-day)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
