---
title: Monitoring overview
audience: government, admin, hosting, project-manager
category: public-sector
order: 20
summary: What to watch on a public-sector Exponential site and where each signal comes from - outside checks, system information, logs and their digests, request and CSP logs, audit alerts, cronjobs and file consistency - with a daily, weekly and monthly routine for an in-house team.
keywords: monitoring, observability, health check, uptime, logs, error digest, log health, security log, audit alerts, cronjobs, system information, routine, on-call, service management
type: help
---
# Monitoring overview

This page is for in-house IT teams and service owners who must know that a public website is up, healthy and not
under attack, and must be able to show it. It maps each question to the place in Exponential that answers it, and
gives a routine. The hosting view is [Monitoring overview (hosting)](../../../hosting/operations/monitoring-overview.md).

## Contents
1. [Questions and where they are answered](#questions-and-where-they-are-answered)
2. [Signals that can feed your monitoring system](#signals-that-can-feed-your-monitoring-system)
3. [A routine](#a-routine)
4. [Who watches what](#who-watches-what)
5. [Related pages](#related-pages)

## Questions and where they are answered

| Question | Where | Page |
|---|---|---|
| Is the site up for citizens? | an outside check of the public address and the sign-in page | [External uptime monitoring](external-uptime-monitoring.md) |
| Is the installation healthy? | **Setup > System information**, Health checks; `./console exp:system:info` | [System information and health checks](system-information-and-health-checks.md) |
| What errors happen? | **Setup > Log files**, **Error digest**, **Log health** | [The log viewer](the-log-viewer.md), [Error digest and log health](error-digest-and-log-health.md) |
| Is someone probing us? | `security.log`, `404.log` | [Request logs](request-logs-404-and-security.md) |
| Would the content security policy break something? | `csp.log` | [CSP reports](csp-reports.md) |
| Did anyone do something risky? | the audit alerts | [Audit alerts](audit-alerts.md) |
| Is the record intact? | `exp:audit verify` | [Audit trail verification](audit-trail-verification.md) |
| Do scheduled jobs run? | **Setup > Cronjobs**, the cronjob health check | [Cronjob monitoring](cronjob-monitoring.md) |
| Did files change unexpectedly? | **Setup > Upgrade check**; `checkmanifest.php` | [Upgrade check as a monitor](upgrade-check-as-a-monitor.md) |
| Are logs within budget? | **Log health**; rotation settings | [Log retention and rotation](log-retention-and-rotation.md) |

## Signals that can feed your monitoring system

Commands that end with a meaningful exit code or JSON, suitable for a monitoring agent:

| Command | Signal |
|---|---|
| `./console exp:system:info --json --allow-root-user` | exits 1 when a health check fails |
| `./console exp:audit verify --allow-root-user` | exits 1 when a chain is broken |
| `php bin/php/checkmanifest.php --all` | exits non-zero when files are modified or missing |
| `php bin/php/cache.php status --allow-root-user` | ends in `PASS` or `FAIL`; `--json` available |
| `./console exp:logs --search=... --json` | exit 0 found, 1 nothing found |
| the audit syslog sink | records in syslog, `journalctl -t exponential` with `Transport=local` |

The audit's alert sinks (syslog, a signed webhook, e-mail) push events to you
([Audit trail, "Sinks, alerts and archives"](../../../../features/6.0/audit-trail.md#sinks-alerts-and-archives)).

## A routine

| When | What |
|---|---|
| Continuously | outside checks of the public site and the sign-in page; audit alert mail and syslog |
| Daily | **Error digest** of the last 24 hours; fired audit alerts |
| Weekly | `security.log` review ([Review the security log weekly](../../tasks/operations/review-the-security-log-weekly.md)); cronjob runs; `exp:audit verify`; system information health checks |
| Monthly | upgrade check; log health and disk; account hygiene; backup restore test as your plan says ([Monthly operations review](../../tasks/operations/monthly-operations-review.md)) |

The [operating guide's weekly checklist](../../../../guides/operating-a-site.md#10-a-weekly-checklist) is a good minimum.

## Who watches what

Write down who receives which alert and who acts out of hours. Exponential sends audit alerts to the rule's
recipients, else the configured groups, else the site's `AdminEmail`; check with
`./console exp:audit alerts recipients --allow-root-user`. On-call rotations and escalation are an organizational
matter outside the software.

## Related pages
- [Incidents](../incidents/README.md)
- [Health checks and uptime (hosting)](../../../hosting/operations/health-checks-and-uptime.md)
- [Operating a site](../../../../guides/operating-a-site.md)
- [Logs and the audit trail course module](../../../trainers/site-administrators/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
