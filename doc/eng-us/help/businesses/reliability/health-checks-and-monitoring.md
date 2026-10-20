---
title: Health checks and monitoring
audience: businesses, admin, hosting
category: business-solutions
order: 40
summary: What a business should monitor on an Exponential site, from outside and from inside, which shipped commands and pages give a clear healthy or unhealthy answer, and how to route alerts to a person.
keywords: monitoring, health check, uptime monitor, exp:system:info, exp:velocity status --json, system information, /Q/health, watchdog, exit code, alerts
type: help
---
# Health checks and monitoring

This page is for managers who want to be sure someone notices when the site has a problem, and for the operator who
sets up the checks. It explains the four layers to watch, the commands that answer with a clear exit code or JSON,
and the pages a person reads once a week. The technical reference is
[Monitoring overview](../../hosting/operations/monitoring-overview.md) and
[Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md).

## Contents

1. [No single health URL](#no-single-health-url)
2. [The four layers](#the-four-layers)
3. [Checks from outside](#checks-from-outside)
4. [Checks on the server](#checks-on-the-server)
5. [Velocity's status and health endpoint](#velocitys-status-and-health-endpoint)
6. [Pages for a person](#pages-for-a-person)
7. [Alerts that reach someone](#alerts-that-reach-someone)
8. [Checklist](#checklist)

## No single health URL

The installation book says it directly: "There is no single health URL; combine a check from outside with the status
commands inside" ([10.11 Monitoring and health checks](../../../install/10-after-installing.md#1011-monitoring-and-health-checks)).
A business needs both halves: the outside check sees what customers see, the inside checks explain why.

## The four layers

From the [Monitoring overview](../../hosting/operations/monitoring-overview.md#four-layers-of-monitoring):

| Layer | The business question | Tools |
|---|---|---|
| Reachability | Can customers reach the site, fast enough? | an HTTP check from outside; Velocity's `/Q/health` |
| Platform | Are PHP, the database, the disk and cron in order? | `exp:system:info`, the cache status, `crontab:list` |
| Application | Are there new errors, attacks, failed jobs, mail problems? | the logs, the error digest, mail and notification status |
| Integrity | Has anyone changed what they should not? | the audit trail and its verify command |

## Checks from outside

Use any external uptime monitoring service or tool you trust. Point it at two addresses
([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md#checks-from-outside)):

| Check | Healthy answer |
|---|---|
| The front page, over HTTPS, at the address customers use | `200` and a response time you know |
| The administration login page (`/admin/user/login` on a typical install) | `200` |

Why both: the front page is often answered from a cache, so it can stay green while the database is down. The
login page goes through the application. A check that looks for a word only the real page contains is stronger than a
status code alone.

During planned maintenance the site answers `503` with a `Retry-After` header. Either pause the monitor for the
window or let it accept that answer while maintenance is on, and alert when the window lasts longer than announced
([Maintenance mode and planned work](maintenance-mode-and-planned-work.md)).

## Checks on the server

Run these on a schedule, as the site's user, and alert on a non-zero exit status. All were checked against the
code; run them from the installation root.

| Check | Command | Healthy |
|---|---|---|
| Platform health | `php bin/php/console exp:system:info --json --allow-root-user` | exit status 0; it exits 1 when a health check fails |
| Caches | `php bin/php/cache.php status --json --allow-root-user` | ends in `PASS` |
| Velocity, if used | `php bin/php/console exp:velocity status --json --allow-root-user` | running, workers as configured |
| Audit trail | `php bin/php/audit.php verify --allow-root-user` | exit status 0; 1 means a chain is broken |
| Cron | `php bin/php/console crontab:list` and the dates of the cron logs | entries present, logs recent |
| Disk | `df -h` on the installation's file system | room for caches, logs and a backup |

Sources: [System information, from the command line](../../../guides/system-information.md#8-from-the-command-line),
[Monitoring overview](../../hosting/operations/monitoring-overview.md#status-commands-for-a-monitoring-system).

> **Note**
> The command-line report describes the command-line PHP, not the PHP-FPM pool or Velocity that serves visitors.
> Open **Setup > System information** through each server you run to see the PHP that customers actually reach
> ([System information, which server answered](../../../guides/system-information.md#2-which-server-answered)).

The installation book's advice for a single server: "A small script in the site user's crontab that runs these and
mails on a non-zero exit status is enough" ([10.11](../../../install/10-after-installing.md#1011-monitoring-and-health-checks)).

## Velocity's status and health endpoint

Where the site is served by Exponential Velocity, two more signals exist
([Health checks and uptime, Velocity](../../hosting/operations/health-checks-and-uptime.md#velocity-health-endpoint-and-watchdog)):

- `exp:velocity status` says `running` only for a server that answers an HTTP request; processes that do not answer
  are reported `not answering` and the command exits non-zero
  ([Velocity engines](../../../bc/6.0/velocity-engines.md#stop-restart-and-status-of-the-qbix-engine)). The README
  notes that "`status --json` is there for monitoring".
- `/Q/health` on the server "answers anyone with `{"status":"ok"}`". It tells you the server process answers; it does
  not render a page, so keep the outside checks too.
- The **watchdog**, a systemd timer installed with `exp:velocity watchdog install`, starts the server again when it
  is down and nobody meant it to be. It checks every 45 seconds by default (`[WatchdogSettings] Interval` in
  `settings/velocity.ini`).

## Pages for a person

Some signals are for a human reading once a week rather than for an alarm
([Monitoring overview, pages for a human](../../hosting/operations/monitoring-overview.md#pages-for-a-human)):

| Page | What it shows |
|---|---|
| **Setup > System information** | Which server answered, health checks with what to do, versions, database, storage, cron, mail |
| **Setup > Log files > Log health** | Disk used by logs, fastest growing logs, the newest error of each log |
| **Setup > Log files > Error digest** | The distinct errors and warnings of the last 24 hours, grouped and counted |
| **Audit** tab | The state of each audit channel, recent events, alerts |
| **Setup > Cronjobs** | Parts, schedules, last runs, parts that need attention |

The health checks of **Setup > System information** include PHP version and extensions, OPcache, memory and time
limits, writable directories, free disk, debug output left on, development settings, caches switched off, cronjobs
not run for two days, mail and database ([System information, health checks](../../../guides/system-information.md#3-health-checks)).
Each failing check says what to do.

## Alerts that reach someone

A check nobody reads is not monitoring. Decide in advance:

- **Who receives an alert**, during and outside office hours, and who is the deputy.
- **How fast** they are expected to look, and what they do first ([Incident response for small teams](incident-response-for-small-teams.md)).
- **Where alerts go**: mail, a chat channel, a phone. The audit trail also sends its own alert mail for events such
  as brute-force sign-ins or an administrator role being granted; check its recipients with
  `php bin/php/console exp:audit alerts recipients` ([Audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes)).
- **What is not an alert**: a planned maintenance window, a single slow request. Too many false alarms train people
  to ignore real ones.

## Checklist

- [ ] An external check requests the front page and the administration login page over HTTPS.
- [ ] The server checks above run on a schedule and alert on a non-zero exit status.
- [ ] Planned maintenance is recognized and does not page anyone; an overrunning window does.
- [ ] With Velocity: the watchdog is installed and its log (`var/log/velocity-watchdog.log`) is watched.
- [ ] A named person and a deputy receive alerts, and know the first steps.
- [ ] Someone reads **Setup > System information** and the error digest every week.

## Related pages

- [Logs and error references](logs-and-error-references.md)
- [Uptime practice](uptime-practice.md)
- [Monitoring overview](../../hosting/operations/monitoring-overview.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
