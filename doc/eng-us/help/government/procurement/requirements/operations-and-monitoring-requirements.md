---
title: Operations and monitoring requirements
audience: government, project-manager, hosting, admin
category: public-sector
order: 100
summary: Requirement wording for running a public CMS day to day - monitoring, logs, scheduled jobs, caches, maintenance windows, upgrades and health checks - with the Exponential tools and pages that a supplier's answer should name.
keywords: operations, monitoring, uptime, health checks, logs, log viewer, cronjobs, caches, maintenance mode, upgrades, upgrade check, system information, runbooks, tender, requirements
type: help
---
# Operations and monitoring requirements

This page is for IT managers and procurement teams who specify how the CMS is run after go-live. Operations are
usually a service delivered by a supplier or your own IT team; the software provides the tools. The page gives
requirement wording and names the Exponential tools that an answer should use, from
[Operating a site](../../../../guides/operating-a-site.md) and the
[hosting operations help](../../../hosting/operations/README.md).

## Contents
1. [What operations covers](#what-operations-covers)
2. [Requirement wording](#requirement-wording)
3. [Tools Exponential provides](#tools-exponential-provides)
4. [Reports to ask for](#reports-to-ask-for)
5. [Pitfalls](#pitfalls)

## What operations covers

- Watching that the site is up and fast.
- Reading logs and acting on errors.
- Running scheduled jobs (cronjobs): notifications, search indexing, audit housekeeping.
- Clearing and warming caches after changes.
- Planned maintenance and upgrades, announced to editors and visitors.
- Backups and restore tests (see [Hosting and data location requirements](hosting-and-data-location-requirements.md)).

## Requirement wording

| Id | Requirement |
|---|---|
| OPS-01 | The site is monitored from outside every <interval> with a check that touches the database; alerts reach <role> at any hour / in <hours>. |
| OPS-02 | Application errors are logged with a reference that a visitor can quote; the supplier can find the log entry from that reference. |
| OPS-03 | Scheduled jobs run as documented and their failures are alerted. |
| OPS-04 | Planned maintenance happens in announced windows; visitors see a maintenance message, administrators can still sign in. |
| OPS-05 | Before each upgrade the supplier checks file and database consistency and reports differences from the release. |
| OPS-06 | The supplier delivers runbooks for site down, slow site, disk full, certificate expiry, restore from backup and compromised site. |
| OPS-07 | Logs are rotated and kept for <period>, with personal data masked. |
| OPS-08 | The supplier delivers a monthly operations report (availability, incidents, changes, updates applied). |

## Tools Exponential provides

| Id | Tool | Page |
|---|---|---|
| OPS-01 | Health checks on **Setup > System information** (`/setup/info`) | [System information](../../../../guides/system-information.md), [Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md) |
| OPS-02 | Error references | [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md) |
| OPS-03 | Cronjob console and pause | [Cronjobs](../../../../guides/cronjobs.md), [Cronjobs console](../../../../features/6.0/cronjobs-console.md) |
| OPS-04 | **Setup > Maintenance**, `php bin/php/maintenance.php on --message=... --until=...` | [Maintenance mode](../../../../guides/maintenance-mode.md) |
| OPS-05 | **Setup > Upgrade check** (`/setup/systemupgrade`) | [The upgrade check](../../../../guides/upgrade-check.md), [File consistency check](../../../../features/6.0/file-consistency-check.md) |
| OPS-06 | Runbooks written for hosts | [Hosting runbooks](../../../hosting/runbooks/README.md) |
| OPS-07 | **Setup > Log files**, log rotation, request logs | [Log viewer](../../../../features/6.0/log-viewer.md), [Log rotation](../../../../features/6.0/log-rotation.md), [Request logs](../../../../features/6.0/request-logs.md) |
| OPS-08 | Monthly operations checklist for hosts | [Monthly operations](../../../hosting/checklists/monthly-operations.md) |

Caches: **Setup > Cache management** (`/setup/cache`) and `php bin/php/ezcache.php --clear-all`
([Caches](../../../../guides/caches.md)). Upgrades: [Upgrading](../../../../guides/upgrading.md).

## Reports to ask for

- Availability per month, measured as the contract defines.
- Incidents with cause, duration and fix.
- Exponential, PHP and operating system updates applied, with dates.
- Restore tests with date and duration.
- Audit alerts raised and how they were handled ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Pitfalls

- Specifying monitoring without saying who answers the alert.
- Treating "the software has a maintenance mode" as an operations plan.
- Forgetting that the audit housekeeping depends on the `frequent` cronjob group running.

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Support and maintenance requirements](support-and-maintenance-requirements.md)
- [Tender template: service levels clause](../tender-templates/service-levels-clause.md)
- [Operating a site](../../../../guides/operating-a-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
