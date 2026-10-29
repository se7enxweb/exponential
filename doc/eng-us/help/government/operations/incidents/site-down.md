---
title: Site down
audience: government, admin, hosting
category: public-sector
order: 30
summary: Runbook for a public website that does not serve pages - confirm, read which error page visitors get, find the cause (database, maintenance left on, missing libraries, an uncaught error, the web server or Velocity), fix, and tell the public.
keywords: site down, outage, 500, 503, blank page, white page, We'll be right back, error reference, Velocity status, PHP-FPM, runbook, incident
type: help
---
# Site down

This runbook is for the on-call administrator when a public website does not serve pages. It follows
[Site down (hosting runbook)](../../../hosting/runbooks/site-down.md) and the error pages of
[chapter 12.9](../../../../install/12-troubleshooting.md#129-pages-and-errors-at-run-time), adding the public-sector
steps: incident roles, communication, record. Start with [Incident response basics](incident-response-basics.md).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../../examples/web-servers/nginx.md), [LiteSpeed](../../../../examples/web-servers/litespeed.md),
> [Caddy](../../../../examples/web-servers/caddy.md), [IIS](../../../../examples/web-servers/iis.md), [Velocity](../../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../../hosting/panels/plesk.md), [cPanel](../../../hosting/panels/cpanel.md), [DirectAdmin](../../../hosting/panels/directadmin.md), [ISPConfig](../../../hosting/panels/ispconfig.md).

## Contents
1. [Confirm](#confirm)
2. [Which page does the visitor see?](#which-page-does-the-visitor-see)
3. [Nothing answers at all](#nothing-answers-at-all)
4. [Tell the public](#tell-the-public)
5. [After the fix](#after-the-fix)
6. [Related pages](#related-pages)

## Confirm

```bash
curl -sI https://www.example.gov/ | head -5
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/admin/user/login
./console exp:logs --tail=var/log/error.log --lines=50
df -h
```

A full disk causes most symptoms; see [Disk full](disk-full.md).

## Which page does the visitor see?

| The visitor sees | Cause | Do |
|---|---|---|
| "We'll be right back" (503) | the database could not be reached | [Database unreachable](database-unreachable.md) |
| "Down for maintenance" (503) | maintenance mode is on | `php bin/php/maintenance.php status --allow-root-user`; if the work is done, `php bin/php/maintenance.php off --allow-root-user` |
| "The site is missing the software libraries it needs" (503) | `vendor/` missing | put it back, or repair: `php bin/php/exprepair.php --create-key` and the key in the page ([Repair from the browser](../../../../features/6.0/repair-from-the-browser.md)) |
| "Something went wrong on our side" (500) with `ERR-...` | an uncaught error | `./console exp:logs --reference=ERR-...` |
| A blank page, or 500 with nothing in `error.log` | PHP failed before Exponential could log | the web server's and PHP-FPM's error logs; `php -l` on a recently changed file |
| 404 for every page under Apache | `.htaccess` not read or `mod_rewrite` off | the virtual host's `AllowOverride` and modules |

Errors that start right after a deploy are almost always in that change: [Broken deploy rollback](broken-deploy-rollback.md).

## Nothing answers at all

- Velocity: `./console exp:velocity status --allow-root-user`; if it is not running,
  [Velocity will not start (hosting runbook)](../../../hosting/runbooks/velocity-will-not-start.md).
- Apache with PHP-FPM: is the web server running, and the PHP-FPM service that serves this site (on servers with
  several PHP versions it is not always called `php-fpm`)?
- The network, DNS and certificate: [Certificate expiry](certificate-expiry.md).

## Tell the public

If the outage lasts longer than a few minutes, the communications role posts a short notice on your other channels
(where to get the service meanwhile). If the site answers at all, maintenance mode with a clear message is better than
an error page.

## After the fix

1. Clear the caches the fix needs; under Velocity its response cache **after** any restart:
   `./console exp:velocity cache clear --apply --allow-root-user`.
2. Confirm from outside; check the error digest for an hour.
3. Record cause, timeline and fix; hold a [post-incident review](post-incident-review.md) for severity 1.

## Related pages
- [Incident response basics](incident-response-basics.md)
- [External uptime monitoring](../monitoring/external-uptime-monitoring.md)
- [When something goes wrong (help)](../../../when-something-goes-wrong.md)
- [Slow site (hosting runbook)](../../../hosting/runbooks/slow-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
