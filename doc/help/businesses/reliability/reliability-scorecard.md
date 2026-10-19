---
title: Reliability scorecard
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 220
summary: A checklist to score how reliably your Exponential site is run, in ten areas with three levels each, how to use the score each quarter, and links to the page that explains every item.
keywords: scorecard, reliability score, checklist, assessment, maturity, quarterly review, operations review, self-assessment, audit
type: help
---
# Reliability scorecard

This page is for owners and managers who want to know, in one sitting, how well their site is protected, and for the
operator or partner who fills it in with them. It has ten areas, each scored 0, 1 or 2. It is not a certification and
gives no target score: it shows where you stand and what to do next. Every item links to the page that explains it.

## Contents

1. [How to use it](#how-to-use-it)
2. [The scorecard](#the-scorecard)
3. [Reading your score](#reading-your-score)
4. [Evidence to keep](#evidence-to-keep)
5. [A record to copy](#a-record-to-copy)

## How to use it

1. Sit down with the person who runs the site, or your hosting partner, once a quarter.
2. Score each area honestly: **0** = not in place, **1** = in place but not tested or not regular, **2** = in place,
   regular and proven.
3. Score only what you can show: a log entry, a report, a calendar item. "We would do it" scores 0.
4. Pick at most three areas to improve before the next review, with an owner each.
5. Keep the scores; the trend matters more than the number.

## The scorecard

| # | Area | 0 | 1 | 2 | Read |
|---|---|---|---|---|---|
| 1 | Release line | Version unknown, or older than 6.0.x | On 6.0.x, upgrades irregular | On the newest 6.0.x or within one or two releases, upgrades on the calendar | [Release line and support policy](release-line-and-support-policy.md) |
| 2 | PHP | Unsupported PHP | Supported PHP, retirement date unknown | Supported PHP, retirement date known and planned for | [Release line and support policy](release-line-and-support-policy.md#php-versions) |
| 3 | Outside monitoring | None | Front page checked | Front page and administration checked over HTTPS, alerts reach a named person and deputy | [Health checks and monitoring](health-checks-and-monitoring.md) |
| 4 | Inside checks | None | Looked at now and then | System information, cache status and audit verify run on a schedule by exit code | [Health checks and monitoring](health-checks-and-monitoring.md#checks-on-the-server) |
| 5 | Backups | None, or database only | Database and files, not restored | Database and files, off the server, several generations, failures alert someone | [Backups that restore](backups-that-restore.md) |
| 6 | Restore drills | Never | Once, long ago | On a schedule, timed, recorded, mail stopped on the copy | [Restore drills](restore-drills.md) |
| 7 | Changes and upgrades | Made directly on the live site | Backup first, no rehearsal | Rehearsed on a copy, upgrade check before and after, maintenance window, rollback owner | [Upgrades without surprises](upgrades-without-surprises.md) |
| 8 | Deploys | Caches cleared by hand, server restarted hard | Documented steps | `exp:velocity deploy` or an equivalent ordered procedure, dry run first, checked afterwards | [Graceful deploys](graceful-deploys.md) |
| 9 | Background work | Cron not set up or unknown | Cron set up, not watched | Cron on one server, watched weekly, paused during planned work | [Cronjobs and background jobs](cronjobs-and-background-jobs.md) |
| 10 | Incident readiness | No plan | A plan nobody has rehearsed | Printed plan, named roles, runbooks known, rehearsed this year, reviews written | [Incident response for small teams](incident-response-for-small-teams.md) |

Optional areas, for sites where they apply:

| # | Area | 2 means | Read |
|---|---|---|---|
| 11 | Caching | View caching and template caches on, page caches cleared in the right order, warming after clears | [Caching for availability](caching-for-availability.md) |
| 12 | Peaks | Peak dates known weeks ahead, measured baseline, change freeze | [Peak traffic planning](peak-traffic-planning.md) |
| 13 | Audit trail | Indexed by cron, verified on a schedule, alerts reach a shared mailbox, keys backed up | [Audit trail for operations](audit-trail-for-operations.md) |
| 14 | Editorial safety | Preview, approval where mistakes are costly, editors know versions and the trash | [Preview before you publish](preview-before-you-publish.md) |
| 15 | Logs | Error digest read weekly, support asks for `ERR-` references, retention decided | [Logs and error references](logs-and-error-references.md) |

## Reading your score

The ten core areas give at most 20 points. The score is for your own comparison over time, not a pass mark:

- **Any area at 0** is the first thing to fix, whatever the total. A site with perfect monitoring and no backup is
  one disk failure away from losing everything.
- **Areas at 1** usually need a habit, not a purchase: a calendar entry, a named owner, a first rehearsal.
- **Areas at 2** need to stay there: re-check them each quarter, because people, servers and sites change.

Areas 5, 6 and 10 (backups, restore drills, incident readiness) decide how bad a bad day gets. If you can only work on
one thing this quarter, work there.

## Evidence to keep

A score of 2 should point to something you can show a manager, an auditor or a customer:

| Area | Evidence |
|---|---|
| Release line, PHP | The version shown on **Setup > System information**, and the upgrade plan |
| Monitoring | The monitor's alert settings and last month's report |
| Inside checks | The scheduled job and its last results |
| Backups | The backup job's log and the storage location |
| Restore drills | The drill record: date, backup, minutes, result ([Restore drills](restore-drills.md#record-the-result)) |
| Upgrades | The upgrade check reports before and after, the signed-off rehearsal |
| Deploys | The audit trail's `system` channel and your change log |
| Background work | **Setup > Cronjobs** recent runs |
| Incidents | The printed plan, the rehearsal date, the reviews |

For compliance use of the audit trail, see
[Compliance evidence from the audit trail](../security/compliance-evidence-from-the-audit-trail.md).

## A record to copy

```text
Reliability review of <site>          Date: <yyyy-mm-dd>        Present: <roles>

 1 Release line          0 1 2     6 Restore drills       0 1 2
 2 PHP                   0 1 2     7 Changes, upgrades    0 1 2
 3 Outside monitoring    0 1 2     8 Deploys              0 1 2
 4 Inside checks         0 1 2     9 Background work      0 1 2
 5 Backups               0 1 2    10 Incident readiness   0 1 2

Total (of 20): ____      Last quarter: ____

Actions before the next review (at most three):
1. <area> - <what> - <owner> - <by when>
2.
3.
```

The [Templates](../templates/README.md) section has more checklists to copy, including the
[Annual review checklist](../templates/annual-review-checklist.md) and the
[Monthly operations checklist](../templates/monthly-operations-checklist.md).

## Related pages

- [Reliability for businesses](README.md)
- [Uptime practice](uptime-practice.md)
- [Risk management](../../leadership/risk-management.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
