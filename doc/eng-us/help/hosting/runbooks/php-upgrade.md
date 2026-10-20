---
title: "Runbook: PHP upgrade"
audience: hosting, admin
category: hosting
order: 70
summary: A planned move of an Exponential site to a newer PHP 8.x version: check support and extensions on a staging copy, switch the web PHP and the command-line PHP together, reload PHP-FPM or restart Velocity, clear caches, verify, and keep a way back.
keywords: runbook, PHP upgrade, PHP 8.0, 8.1, 8.2, 8.3, 8.4, 8.5, PHP-FPM, Velocity, extensions, OPcache, cron PHP, Plesk, staging, rollback, deprecations, exp:velocity ext check
type: help
---
# Runbook: PHP upgrade

For system administrators who move customer sites to a newer PHP version, one site at a time or a whole server. A PHP
upgrade is planned work, not an incident, but it is the change most likely to break a customer's own code; treat it
with a runbook.

## Contents

1. [What is supported](#what-is-supported)
2. [Before: on a staging copy](#before-on-a-staging-copy)
3. [The switch](#the-switch)
4. [Verify](#verify)
5. [Rollback](#rollback)

## What is supported

| PHP | Exponential 6.0 | Source |
|---|---|---|
| 8.0 | the oldest supported; Velocity runs on it from its release 0.0.4.54 | [Chapter 2](../../../install/02-requirements.md) |
| 8.1 to 8.4 | supported, Velocity included | Chapter 2 |
| 8.5 | supported | Chapter 2 |

The project's CI runs the test suite on PHP 8.1 to 8.5 and checks that 8.0 still boots
([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#php-versions)). Update Exponential to
the newest 6.0.x before a PHP upgrade, so PHP fixes already made are in place.

## Before: on a staging copy

1. Copy the site to a staging installation that runs the new PHP ([Moving a site between hosts](../migrations/moving-a-site-between-hosts.md)).
2. Install the PHP extensions the site needs for the new version; the setup wizard's system check or `exp:install`'s
   `SystemCheck` step lists missing ones ([PHP versions and extensions](../basics/php-versions-and-extensions.md)).
   For Velocity:

   ```bash
   php bin/php/console exp:velocity ext check --allow-root-user
   ```

3. Carry the `php.ini` settings over: `memory_limit`, `max_execution_time`, `date.timezone`, upload limits, OPcache
   ([PHP settings for Exponential](../basics/php-settings-for-exponential.md), [OPcache](../performance/opcache.md)).
4. Run the site, click through the main pages and the administration, publish, upload, search. Watch
   `var/log/error.log` and `warning.log` for deprecations from the customer's own extensions.
5. Benchmark before and after if performance matters to the plan
   ([Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md)).
6. Tell the customer what you found in their own code; fixing it is usually their part
   ([Support tiers](../business/support-tiers.md)).

## The switch

1. Announce the window.
2. Switch the **web** PHP of the site:
   - PHP-FPM: point the site at the new version's pool (in a panel: the site's PHP version setting) and reload that
     version's PHP-FPM service. On Plesk the service is `plesk-php<XY>-fpm`, not `php-fpm`
     ([11.6](../../../install/11-upgrading.md#116-from-any-60x-to-today)).
   - Velocity: the server runs on the command-line PHP that starts it; start it with the new binary (see
     [Velocity for hosts](../servers/velocity-for-hosts.md)).
3. Switch the **command-line** PHP the site's cron jobs and scripts use. "Use the same PHP binary the web server uses
   (`/opt/.../php` on hosts with several PHP versions); `php -v` in the cron environment may differ from your shell's"
   ([10.3](../../../install/10-after-installing.md#a-crontab)).
4. Clear caches and regenerate autoloads, then restart:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/console exp:velocity deploy --allow-root-user     # under Velocity: every step in order
   ```

## Verify

- [ ] `php -v` as the site's user, in the cron environment and in the web (**Setup > System information** shows the
      PHP that serves the administration).
- [ ] Front page, administration, sign-in, publish, upload, search.
- [ ] No new entries in `error.log`: `./console exp:logs --tail=var/log/error.log --lines=50`.
- [ ] Cron jobs ran with the new PHP (their logs).
- [ ] OPcache active: `php bin/php/cache.php opcache status --allow-root-user`.

## Rollback

Keep the old PHP version installed until the site has run on the new one for the agreed period. Rollback is the switch
in reverse: the site's PHP setting back, reload the old PHP-FPM or start Velocity with the old binary, cron back,
caches cleared. Compiled caches are specific to the PHP that wrote them, so clear them on the way back too.

## Related pages

- [PHP versions and extensions](../basics/php-versions-and-extensions.md)
- [PHP selection and FPM pools in panels](../panels/php-selection-and-fpm-pools.md)
- [Upgrading customer sites](../operations/upgrading-customer-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
