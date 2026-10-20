---
title: Maintenance mode and planned downtime
audience: government, hosting, admin, project-manager
category: public-sector
order: 100
summary: For public bodies that must announce and control planned downtime of a website. How Exponential's maintenance mode answers visitors with a 503 page and an expected end, who still gets in, how monitoring and search engines see it, and how to fit maintenance windows into your change calendar and duty to inform.
keywords: maintenance mode, planned downtime, maintenance window, 503, Retry-After, maintenance.php, exp:maintenance, Setup > Maintenance, allow-ip, allow-admin, change calendar, public notice, accessibility of notice
type: help
---
# Maintenance mode and planned downtime

This page is for operations teams and project managers in public bodies who plan downtime for upgrades, imports or
restores, and who must tell the public about it. It answers how maintenance mode works, what visitors, monitors and
search engines see, who still gets in, and how a maintenance window fits your change calendar. The full guide is
[Maintenance mode: taking the site offline and bringing it back](../../../../guides/maintenance-mode.md); the feature
page is [Maintenance mode](../../../../features/6.0/maintenance-mode.md).

## Contents

1. [What visitors and machines see](#what-visitors-and-machines-see)
2. [Switching it on and off](#switching-it-on-and-off)
3. [Who still gets in](#who-still-gets-in)
4. [Planning a window in a public body](#planning-a-window-in-a-public-body)
5. [Things maintenance mode does not do](#things-maintenance-mode-does-not-do)
6. [Checklist](#checklist)

## What visitors and machines see

While maintenance is on, every request to the front controllers is answered with the maintenance page before the
settings or the database are touched:

- HTTP status `503 Service Unavailable` with a `Retry-After` header (the seconds left, or 120 when no end is set);
- `Cache-Control: no-store` and `noindex`, so no cache or search engine keeps the page;
- images, styles and scripts are still served, so the page keeps its look.

The page is the active theme's `errors/maintenance.html` when an active extension has one, otherwise
`share/maintenance.html`. Check that your theme's page is in plain language and states when the service returns;
the page's preview in the administration shows exactly what visitors get.

External uptime monitors see a 503 with `Retry-After`. Configure them to tell planned maintenance from an outage
([Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md)).

## Switching it on and off

In the administration: **Setup > Maintenance** (`/setup/maintenance`) with a message, an expected duration, who still
gets in, a preview and a required confirmation before **Take the site offline**. It needs the `setup/administrate`
right. On the command line:

```bash
php bin/php/maintenance.php on --message="Back at 06:00" --until=2h --allow-admin --allow-root-user
php bin/php/maintenance.php status --allow-root-user
php bin/php/maintenance.php off --allow-root-user
```

`./console exp:maintenance on|off|status` is the same through the console. Maintenance never ends by itself: when the
expected end has passed, the page shows a warning, and someone must switch it off. Every change is recorded in the
audit trail as `system.maintenance.change`, and **Setup > Maintenance** lists the recent changes.

## Who still gets in

| Who | How |
|---|---|
| The administration under `/admin` | always from the page; from the command line only with `--allow-admin` |
| Your own address | the tick **Let my own address still see the site**, or `--allow-ip` |
| Testers | **Other addresses that still see the site**, or `--allow-ip=a,b` |
| Signed-in users on the public site | not let through: maintenance is decided before the session is read |

Behind a proxy, the address is the one PHP sees; allow the address your web server logs
([Maintenance mode guide](../../../../guides/maintenance-mode.md)).

## Planning a window in a public body

| Step | What to do |
|---|---|
| Book it | in the change calendar, approved by the change board, outside peak hours of the service |
| Announce it | on the site, in the channels your organization uses, with the date, time and expected end |
| Check dependencies | online forms, appointment bookings or newsletters that will be unavailable; other services that link to the site |
| Prepare | a backup, a shell on the server, the rollback plan ([Maintenance windows](../upgrades-and-change/maintenance-windows.md)) |
| Run it | switch on, work, check from an allowed address, switch off |
| Close it | `curl` gets 200 for everyone; caches in front of the site cleared; the change ticket updated |

Cronjobs keep running during a window, because they run in a shell. Stop the crontab yourself when a job must not
run while you change the site (an import, a search index rebuild).

## Things maintenance mode does not do

- It does not take a backup; take one first ([What to back up](../backups-and-recovery/what-to-back-up.md)).
- It does not stop cronjobs.
- It is local to an installation's `var/maintenance.json`: with several web servers, switch it on every node.
- Deploys do not switch it on or off; for a deploy that changes the database or many files at once, open a window
  first.

## Checklist

- [ ] The window is approved and announced.
- [ ] The maintenance page states when the service returns, in plain words.
- [ ] Monitoring treats a 503 with `Retry-After` as planned.
- [ ] Cronjobs that must not run are stopped.
- [ ] After the window: 200 for everyone, caches cleared, `var/maintenance.json` gone.

## Related pages

- [Availability](README.md) index of this folder
- [Maintenance mode for hosts](../../../hosting/operations/maintenance-mode.md)
- [Availability targets and service levels](availability-targets-and-service-levels.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
