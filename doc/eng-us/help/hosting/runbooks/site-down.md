---
title: "Runbook: site down"
audience: hosting, admin
category: hosting
order: 20
summary: What to do when an Exponential site returns errors or nothing: identify the error page, check the engine, the database, maintenance mode, vendor and permissions, read the right log, fix and verify.
keywords: runbook, site down, outage, 500, 503, error page, blank page, database unavailable, maintenance mode, vendor missing, Velocity, PHP-FPM, error.log, permissions
type: help
---
# Runbook: site down

For on-call staff when a customer's Exponential site does not serve pages. Work top to bottom; stop when the cause is
found. The error pages and their causes come from [12.9 Pages and errors at run time](../../../install/12-troubleshooting.md#129-pages-and-errors-at-run-time).

## Contents

1. [Symptoms](#symptoms)
2. [First five minutes](#first-five-minutes)
3. [Which page does the visitor see?](#which-page-does-the-visitor-see)
4. [Nothing answers at all](#nothing-answers-at-all)
5. [After the incident](#after-the-incident)

## Symptoms

- The external check alerts.
- Customers report "Something went wrong", "We'll be right back", a blank page, or a browser connection error.

## First five minutes

1. Open the site yourself and note exactly what it shows, with the HTTP status:

   ```bash
   curl -sI https://www.example.com/ | head -5
   ```

2. Open the administration too: does it fail the same way?
3. Read the newest errors:

   ```bash
   ./console exp:logs --tail=var/log/error.log --lines=50
   ```

4. Check the machine: disk space (`df -h`), memory, load. A full disk causes most of the symptoms below; see
   [Disk full](disk-full.md).

## Which page does the visitor see?

| The visitor sees | Cause | Do |
|---|---|---|
| "We'll be right back" (503, `Retry-After: 30`) | the database could not be reached | check that the database server runs; check the credentials in `settings/override/site.ini.append.php`; the log line holds the driver's message |
| "Down for maintenance" (503) | maintenance mode is on | `php bin/php/maintenance.php status --allow-root-user`; if the work is done: `php bin/php/maintenance.php off --allow-root-user`; deleting `var/maintenance.json` ends it whatever else is wrong |
| "The site is missing the software libraries it needs" (503) | `vendor/` is missing, moved or renamed | put `vendor/` back or run `composer install --no-dev`; then `php bin/php/ezcache.php --clear-all`, and restart Velocity if it serves the site |
| "Something went wrong on our side" (500) with `ERR-...` | an uncaught error | `./console exp:logs --reference=ERR-...`; the line names the message, file and line ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)) |
| A blank page, or 500 with nothing in `error.log` | PHP failed before Exponential could log: syntax error, missing extension, memory exhausted | read the web server's or PHP-FPM's error log; `php -l` on a recently changed file |
| 404 for every page under Apache | `.htaccess` not read or `mod_rewrite` off | allow overrides, enable `mod_rewrite` ([Apache with PHP-FPM](../servers/apache-with-php-fpm.md)) |

> **Tip**
> Errors that started right after a deploy or an update: the cause is almost always in that change. Under Velocity a
> changed class or setting needs `exp:velocity restart` or `exp:velocity deploy`; see
> [Deploying changes](../operations/deploying-changes.md).

## Nothing answers at all

The connection is refused or times out.

**Under Velocity:**

```bash
php bin/php/console exp:velocity status --allow-root-user
```

`status` reports "running" only when the server answers a request; otherwise it prints the problems it finds and
exits with 1. If it is not running, start it (`exp:velocity start`) and read `var/vc/qbix/run/console.log` if it does
not come up; see [Velocity will not start](velocity-will-not-start.md). A server that runs but does not answer:
`exp:velocity restart`, and if a worker is wedged, `exp:velocity kill` then `start`.

**Under Apache or nginx with PHP-FPM:**

- Is the web server running? Is the site's PHP-FPM pool running? (`systemctl status <service>`; on Plesk the service
  is `plesk-php<XY>-fpm`.)
- Are all PHP-FPM children busy? The pool's status page or its log says so; see [Slow site](slow-site.md).

**In front of it:** a load balancer, reverse proxy, CDN or firewall rule may be the part that fails. Test the origin
directly.

## After the incident

- [ ] Clear the caches the fix needs, and with Velocity its response cache **after** any restart
      (`php bin/php/console exp:velocity cache clear --allow-root-user`).
- [ ] Verify: front page, administration, a sign-in, a publish.
- [ ] Write the incident note: start, detection, cause, fix, end, what prevents a repeat.
- [ ] Tell the customer.
- [ ] If the cause is a bug in Exponential, report it ([When something goes wrong](../../when-something-goes-wrong.md)).

## Related pages

- [Slow site](slow-site.md)
- [Velocity will not start](velocity-will-not-start.md)
- [Chapter 12: Troubleshooting](../../../install/12-troubleshooting.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
