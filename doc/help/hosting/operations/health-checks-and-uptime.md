---
title: Health checks and uptime
audience: hosting, admin
category: hosting
order: 130
summary: What an external uptime monitor should request on an Exponential site, how to tell a planned maintenance window from an outage, which internal checks to run on the server, and how Velocity's health endpoint and watchdog fit in.
keywords: health check, uptime, monitoring, HTTP check, 503, Retry-After, maintenance, /Q/health, watchdog, exp:systeminfo, load balancer health check, response time
type: help
---
# Health checks and uptime

This page is for system administrators who set up uptime monitoring and load balancer health checks for Exponential
sites, and for product managers who describe availability in a hosting plan. It answers: which URLs to request from
outside, what a healthy answer looks like, how a planned maintenance window shows, which checks to run on the server
itself, and what Velocity adds.

Exponential has "no single health URL"; the installation book recommends combining "a check from outside with the
status commands inside" ([10.11](../../../install/10-after-installing.md#1011-monitoring-and-health-checks)). The
inside half is on [Monitoring overview](monitoring-overview.md).

## Contents

1. [Checks from outside](#checks-from-outside)
2. [Planned maintenance is not an outage](#planned-maintenance-is-not-an-outage)
3. [Checks on the server](#checks-on-the-server)
4. [Velocity: health endpoint and watchdog](#velocity-health-endpoint-and-watchdog)
5. [Load balancer health checks](#load-balancer-health-checks)
6. [Uptime in a hosting plan](#uptime-in-a-hosting-plan)
7. [Checklist](#checklist)

## Checks from outside

Two requests cover the public site and the application behind it:

| Check | Command | Healthy |
|---|---|---|
| The site answers | `curl -s -o /dev/null -w '%{http_code} %{time_total}\n' https://www.example.com/` | `200` and a time you know |
| The administration answers | `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/admin/user/login` | `200` |

The front page is often served from a cache (Velocity's response cache, the HTTP cache, a CDN), so it can answer while
the database is down. That is one reason to check the administration login page as well, as the installation book
does; a page whose content you can check (a word only the rendered page contains) is stronger than a status code. Measure the time from the same place each time; "a single page load
proves nothing" ([Deploying](../../../guides/deploying.md#8-make-it-fast)).

> **Tip**
> Point the monitor at the address visitors use, including HTTPS, so an expired certificate or a broken redirect is an
> alert too. Add a second check per node or engine port when you run several.

## Planned maintenance is not an outage

While maintenance mode is on, every page request gets the maintenance page with these headers
([Maintenance mode guide](../../../guides/maintenance-mode.md#1-what-maintenance-mode-does)):

```text
HTTP/1.1 503 Service Unavailable
Retry-After: <seconds until the expected end, or 120 when none is given>
Cache-Control: no-store, no-cache, must-revalidate, private
X-Robots-Tag: noindex
```

Search engines read a 503 with `Retry-After` as "come back later". For your monitoring:

- Either pause the check for the window, or let it accept a 503 that carries `Retry-After` while
  `php bin/php/maintenance.php status` says maintenance is on.
- Alert when the window lasts longer than announced: "maintenance never ends by itself when the expected time
  passes". The administration page warns once the end has passed; your monitor should too.
- Images, styles and scripts are still served during maintenance; a check on a static file stays green.

See [Maintenance mode](maintenance-mode.md).

## Checks on the server

Run these on a schedule as the site's user and alert on a non-zero exit status:

```bash
./console exp:systeminfo --json                      # exit 1 when a health check fails
php bin/php/cache.php status --json --allow-root-user   # ends in PASS
php bin/php/audit.php verify --allow-root-user          # exit 0: every chain intact
df -h /path/to/installation                             # room for caches, logs and a backup
```

`exp:systeminfo` reports the health checks of **Setup > System information** for the command-line PHP: PHP version
and extensions, OPcache, `memory_limit`, `max_execution_time` and more, each with what to do
([System information, health checks](../../../guides/system-information.md#3-health-checks)). Open the administration
page through each server you run to see the checks of the PHP that serves visitors.

## Velocity: health endpoint and watchdog

A site served by Exponential Velocity has a health endpoint of the server itself
([Velocity control panel](../../../features/6.0/velocity-control-panel.md)):

```bash
curl -s http://127.0.0.1:8088/Q/health
```

It "answers anyone with `{"status":"ok"}` and gives figures to an admin only". It tells you that the server process
answers; it does not render a page of the site, so keep the external page checks as well. `/Q/metrics` gives
Prometheus text for a scraper; access from other machines is governed by `settings/velocity.ini [DashboardSettings]`
(`Token`, `Remote`). Port `8088` is the shipped plain-HTTP port of the `qbix` engine (`[ServerSettings] Port`).

The **watchdog** is a systemd timer that starts the default engine again when it is down and nobody meant it to be
(a crash, an out-of-memory kill, a restart whose caller was killed):

```bash
./console exp:velocity watchdog install --allow-root-user   # service and timer, enabled
./console exp:velocity watchdog status --allow-root-user    # timer, markers, last check and last start
```

It checks every `[WatchdogSettings] Interval` seconds (shipped `45`), never touches a server that runs ("whether it
answers or not"), and backs off after three failed starts in a row. It writes `var/log/velocity-watchdog.log` and
audit events ([The watchdog](../../../bc/6.0/velocity-engines.md#the-watchdog-expvelocity-watchdog)). A server that runs
but does not answer is for your monitoring to catch.

## Load balancer health checks

A load balancer needs a cheap request that fails when a node cannot serve. Options, from the sources above:

| Option | Pros | Cons |
|---|---|---|
| The administration login page | goes through the kernel, as visitors' pages do | heavier than a static file |
| Velocity's `/Q/health` | cheap, made for monitors | says the server process answers, not that the site renders |
| A static file | cheapest | stays green when PHP is broken |

Whatever you choose, make sure a node in maintenance mode or with a stopped engine is taken out, and that the check
does not create sessions or fill caches with one entry per request.

## Uptime in a hosting plan

The figures of an availability promise are a business decision; the sources give none. What the platform gives you
to keep such a promise:

- maintenance windows that answer with a clean 503 and an expected end, instead of errors;
- a watchdog that restarts Velocity after a crash;
- status commands with exit codes for automated checks;
- logs and an audit trail to explain an incident afterwards.

See [SLA options](../business/sla-options.md) for how to frame them.

## Checklist

- [ ] An external check requests the front page and the administration login page over HTTPS.
- [ ] Maintenance windows are either paused in the monitor or recognized by the 503 with `Retry-After`.
- [ ] An overrunning maintenance window raises an alert.
- [ ] `exp:systeminfo`, cache status and audit verify run on the server by exit code.
- [ ] The Velocity watchdog is installed where Velocity serves the site, and its log is watched.
- [ ] Load balancer checks take a node out when it cannot render a page.

## Related pages

- [Monitoring overview](monitoring-overview.md)
- [Maintenance mode](maintenance-mode.md)
- [Velocity for hosts](../servers/velocity-for-hosts.md)
- [Site down](../runbooks/site-down.md), [Velocity will not start](../runbooks/velocity-will-not-start.md),
  [Certificate expiry](../runbooks/certificate-expiry.md) (runbooks)
- [SLA options](../business/sla-options.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
