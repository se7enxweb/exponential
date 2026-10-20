---
title: Monthly operations checklist
audience: businesses, admin, hosting
category: business-solutions
order: 60
summary: A copy-ready monthly checklist for keeping a live Exponential site healthy and safe - releases and updates, backups, logs and errors, security, accounts, jobs, content and forms - with where each check is done and a short report for management.
keywords: monthly operations, checklist, maintenance, updates, backups, logs, errors, security, audit trail, accounts, cron jobs, disk, report, operations
type: help
---
# Monthly operations checklist

This page is for the administrator, partner or host who looks after a live site each month, and for the owner who
wants to know it was done. It is the business companion to the
[Monthly operations checklist for hosts](../../hosting/checklists/monthly-operations.md), which goes deeper into
server work. A month without these checks is how small problems become outages.

## Contents

1. [How long it takes](#how-long-it-takes)
2. [The checklist](#the-checklist)
3. [Where each check is done](#where-each-check-is-done)
4. [The report to management](#the-report-to-management)

## How long it takes

On a well-run site, about an hour a month, plus the time to apply any update. The
[budgeting page](../costs/budgeting-for-upgrades-and-maintenance.md#a-worked-yearly-budget) uses that as an example
assumption; measure your own.

## The checklist

```text
MONTHLY OPERATIONS - <site>   Month: <month year>   Done by: <name>   Date: <date>

UPDATES                                                                     Done  Note
[ ] New Exponential 6.0.x release? Read changelog; plan update              [ ]   ____
[ ] Extension updates checked (Setup > Updates and packages)               [ ]   ____
[ ] PHP / operating system updates applied by the host                     [ ]   ____
[ ] Upgrade check clean after any update                                   [ ]   ____

BACKUPS
[ ] Backups ran every day this month                                       [ ]   ____
[ ] Backup includes database, files, settings, keys, audit trail           [ ]   ____
[ ] Restore test this quarter? (date: ______)                              [ ]   ____

LOGS AND ERRORS
[ ] Error log read; recurring errors ticketed                              [ ]   ____
[ ] Not-found (404) list read; redirects added                             [ ]   ____
[ ] Disk space healthy (System information health checks)                  [ ]   ____

SECURITY
[ ] Audit dashboard looked at; alerts of the month answered                [ ]   ____
[ ] Audit chain verified                                                   [ ]   ____
[ ] Failed sign-ins: anything unusual?                                     [ ]   ____
[ ] Certificate valid for more than 30 days / renews automatically         [ ]   ____

ACCOUNTS
[ ] Leavers of the month disabled                                          [ ]   ____
[ ] New administrators intended? (role assignments in the audit trail)     [ ]   ____

JOBS AND CONTENT
[ ] Cron jobs ran (cronjob page)                                           [ ]   ____
[ ] Broken links and content checks reviewed                               [ ]   ____
[ ] Form submissions processed; old ones removed per retention             [ ]   ____
[ ] Mail status clean (exp:mail:status)                                    [ ]   ____

FOR THE REPORT
Incidents this month: ____   Downtime (hours): ____   Updates applied: ____
Open risks: ______________________________________________
```

## Where each check is done

| Check | Where | Read |
|---|---|---|
| Releases | GitHub releases and the issue tracker named in `README.md` | [Security updates and disclosure](../security/security-updates-and-disclosure.md) |
| Extension updates | **Setup > Updates and packages** (`ezupdate`), check changes nothing | [Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md) |
| Upgrade check | **Setup > Upgrade check** | [Upgrade check](../../../guides/upgrade-check.md) |
| Backups | your host's backup report | [Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md) |
| Logs and 404s | the log viewer | [Log viewer](../../../features/6.0/log-viewer.md), [Request logs](../../../features/6.0/request-logs.md) |
| Disk and health | **System information** | [System information](../../../guides/system-information.md) |
| Audit | the **Audit** tab; `exp:audit verify` | [The audit trail](../../../features/6.0/audit-trail.md) |
| Accounts | **Users > Check user accounts** | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| Cron jobs | the cronjob page | [Cronjobs console](../../../features/6.0/cronjobs-console.md) |
| Content checks | link and content checks | [Link and content checks](../../creators/website-creators/link-and-content-checks.md) |
| Forms | **Setup > Collected information** | [Collected information](../../../guides/collected-information.md) |
| Mail | `exp:mail:status` | [E-mail preferences: administrator's guide](../../../guides/mail-preferences-administrator.md) |

## The report to management

```text
SITE REPORT - <site> - <month year>
Status: green / amber / red
Availability: <hours down, planned and unplanned>
Updates: <releases and extensions applied>
Security: <alerts, actions>
Backups: <all ran yes/no>, last restore test <date>
Open risks and proposals: <...>
Next month: <planned work and window>
```

Keep the reports; they are the input for the [Annual review checklist](annual-review-checklist.md) and evidence for
your [ROI](../costs/roi-of-stability.md) figures.

## Related pages

- [Maintaining your website](../../creators/website-creators/maintaining-your-website.md)
- [Uptime practice](../reliability/uptime-practice.md)
- [Operating a site](../../../guides/operating-a-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
