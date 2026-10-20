---
title: "Measuring a project: KPIs from the product's own logs and audit trail"
audience: project-manager, admin
category: leadership
order: 150
summary: Key figures Exponential produces itself (audit trail, request logs, error digest, log health, cronjobs, content checks, upgrade check, benchmarks, CI), where each is shown and how to collect it.
keywords: KPI, metrics, measure, report, dashboard, audit, audit trail, charts, export, CSV, logs, error digest, 404, security.log, CSP reports, cronjobs, content checks, broken relations, upgrade check, benchmark, CI health, status report
type: help
---
# Measuring a project: KPIs from the product's own logs and audit trail

This page is for project managers who report on a site's health and progress, and for the administrators who
collect the numbers. It lists only figures that **Exponential itself produces**: each one names the page in the
administration or the command that shows it, so the numbers in your status report can be checked by anyone with
the same access.

The page does not suggest targets. A good value for your site depends on your site; the first measurement is your
baseline, and the trend over time is what you report.

## Contents

1. [Principles](#principles)
2. [Who may see the numbers](#who-may-see-the-numbers)
3. [Editorial activity: the audit trail](#editorial-activity-the-audit-trail)
4. [Security and access](#security-and-access)
5. [Reliability: errors and missing pages](#reliability-errors-and-missing-pages)
6. [Operations: cronjobs, logs and system health](#operations-cronjobs-logs-and-system-health)
7. [Content quality](#content-quality)
8. [Currency: versions and file consistency](#currency-versions-and-file-consistency)
9. [Performance](#performance)
10. [Engineering: CI and the repository](#engineering-ci-and-the-repository)
11. [What the product does not measure](#what-the-product-does-not-measure)
12. [A monthly KPI report template](#a-monthly-kpi-report-template)

## Principles

- **Measure what the product records.** Every KPI below comes from a page or command of Exponential. Where you
  need something else (visitor analytics, satisfaction), say where it comes from.
- **Same window, same source.** Compare like with like: the same number of days, read from the same page.
- **Privacy first.** The logs and the audit trail mask client addresses, hash sessions and remove query values
  ([Request logs, "Privacy"](../../features/6.0/request-logs.md#privacy), [The audit trail](../../features/6.0/audit-trail.md)).
  Report aggregates, not individuals. A "top actors" list is for the administrator, not for a slide.
- **A number is a pointer.** The cronjobs page says it of its own issue count: "It is a pointer, not a verdict"
  ([Cronjobs](../../guides/cronjobs.md#10-recent-runs-and-how-issues-are-counted)). Read the lines behind a figure
  before you act on it.

## Who may see the numbers

| Source | Policy needed | Source page |
|---|---|---|
| Audit tab: dashboard, console, charts, alerts, export | `audit/read` (optionally limited by channel); only Administrator holds it in a new installation | [The audit trail](../../features/6.0/audit-trail.md) |
| Audit archives and settings, "Verify now" | `audit/manage` | same |
| Log viewer, error digest, log health | `setup/logs` | [Log viewer](../../features/6.0/log-viewer.md) |
| System information | `setup/system_info` | [System information](../../guides/system-information.md) |
| Upgrade check | `setup/setup` | [The upgrade check](../../guides/upgrade-check.md) |
| Content checks | policies of the module `contentcheck` | [Content checks](../../features/6.0/content-checks.md) |

A reporting role for a project manager can hold `audit/read` limited to the channels they need, without the rights to
change anything. Create it as [Roles and policies](../../guides/roles-and-policies.md) describes.

## Editorial activity: the audit trail

The audit trail is on by default and records content changes, sign-ins, settings writes, cache clears, cronjob runs
and orders in channels `content`, `access`, `system`, `commerce` and, when switched on, `read`
([The audit trail](../../features/6.0/audit-trail.md)). It needs the `frequent` cronjob group, whose `audit` part
indexes the events that the charts and the console read.

| KPI | Where | How |
|---|---|---|
| Events per day, per channel | **Audit > Charts** (`audit/charts`) | "Events per day per channel", last 14 days by default, up to 366 (`/(days)/<n>`), each chart with its numbers in a table |
| Today and the last 7 days, per channel | **Audit > Dashboard** | The *Today and 7 days* card |
| Items published in a period | **Audit > Console** filtered on the name `content.object.publish` and a time range; **Export** says how many records the filter has | Or `./console exp:audit search --name=content.object.publish --from=<time> --to=<time>` |
| Content removed in a period | Console or search with `content.node.*` and `content.object.*` names (for example `content.node.remove`) | A subtree removal is one parent event with one child per node |
| Most active editors and most changed objects | **Audit > Dashboard**, *Activity* card (today); **Charts**, "top actors" and "top event names" | For the administrator; report counts, not names |
| Bulk work | Content jobs are parent events of everything their batches did | [Content jobs](../../features/6.0/content-jobs.md) |

**Collecting the numbers.** **Audit > Export** writes the console's current filter as CSV, JSON lines or JSON;
**Download now** streams it, **Export in the background** runs a content job for a large filter. From a shell,
`./console exp:audit export` does the same ([audit reference](../../bc/6.0/audit.md)). A monthly CSV kept with the
project's reports gives you a series you can chart in any spreadsheet.

**Approval backlog.** Versions waiting in a workflow, usually an approval, are listed on **My pending items**
(`/content/pendinglist`) for each approver ([My drafts and my pending items](../../guides/drafts-and-pending.md)) and
in **Setup > Workflow processes** ([Workflows](../../guides/workflows.md)). The number of waiting processes, and how
old the oldest is, is a fair measure of whether approvals keep up.

## Security and access

| KPI | Where | Notes |
|---|---|---|
| Failed sign-ins, by address and by login | **Audit > Dashboard**, *Security* card | Unknown accounts are recorded hashed only |
| Logins against failed logins, per day | **Audit > Charts** | |
| Refusals and failures per day | **Audit > Charts** | Includes `access.permission.refused`: a typed URL a user may not open |
| Role grants | **Audit > Dashboard**, *Security* card: the latest role grants | Each one should match a request you know of |
| Alerts fired | **Audit > Alerts** (`audit/alerts`): rule, count, window | Rules for brute force, administrator roles granted, settings written out of hours, mass deletes, audit switched off, broken chain |
| Audit chain intact | **Audit > Dashboard**, *Health*; `./console exp:audit verify` (exit 1 when broken) | Report "intact on <date>" |
| Probes and attacks, by kind | **Setup > Log files > Error digest**, `security.log` grouped by `<category>/<rule>` (for example `probe/wp-login`) | The classifier only classifies; nothing is blocked ([Request logs](../../features/6.0/request-logs.md)) |
| Content Security Policy violations | Error digest of the `csp` log, grouped by directive and blocked source | The basis for switching a policy to enforce ([Content Security Policy](../../features/6.0/content-security-policy.md#reports)) |

> **Note**
> A rise in `security.log` usually says more about the internet than about your site. The help's advice is to look
> at the error digest and `security.log` weekly: "a sudden rise of one rule is worth a look"
> ([Security hardening](../security-hardening.md#watching-logs-the-request-classifier-and-the-audit-trail)).

## Reliability: errors and missing pages

| KPI | Where | How it is counted |
|---|---|---|
| Distinct errors and warnings in the last 24 hours | **Setup > Log files > Error digest** | Messages masked, normalized, grouped and counted, most frequent first; another window with `/(hours)/N`, 1 to 168 ([Log viewer](../../features/6.0/log-viewer.md#error-digest)) |
| New error groups since the last report | Error digest with **Mark as known** for the groups already explained | Known groups are hidden for the viewer |
| Missing pages (404 and 410), by reason | Error digest of `404.log`, grouped by its `reason=` field (`module:...`, `node:#`, `view:...`) | "Why pages are missing, not a list of every missing address" ([Request logs](../../features/6.0/request-logs.md#the-log-viewer-rotation-and-the-error-digest)) |
| Errors editors reported | The error references (`ERR-...`) passed on by editors | [Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md) |

After a migration, the 404 groups by reason are a direct measure of how many old addresses still need a redirect or
a URL alias ([URLs and aliases](../../guides/urls-and-aliases.md)).

## Operations: cronjobs, logs and system health

| KPI | Where | How it is counted |
|---|---|---|
| Health check failures and warnings | **Setup > System information**, *Health checks* | Failure, Warning, Note or OK per check; `./console exp:system:info --json` exits 1 when a check fails ([System information](../../guides/system-information.md#3-health-checks)) |
| Cronjob runs and their result | **Setup > Cronjobs**, *Recent runs*: the last 20 runs started from the page, with duration and *OK* or *n issues* | Issues are lines with *error*, *fatal*, *exception*, *failed*, *failure* or *warning* ([Cronjobs](../../guides/cronjobs.md#10-recent-runs-and-how-issues-are-counted)) |
| Every cronjob run, also from the crontab | Audit, `system.cronjob.run` and `system.cronjob.fail` | Count per day in the console |
| Cronjob freshness | System information's cronjob check (the last run more than two days ago is flagged); the audit dashboard warns when its part has not run for an hour | |
| Log disk use against the budget | **Setup > Log files > Log health**: disk against `[HealthSettings] DiskBudget` (default 256 MB; warning from 80 %) | Also the fastest growing logs and logs over their size limit ([Log health](../../features/6.0/log-viewer.md#log-health)) |
| Size of `var/` and the caches | **Setup > System information > Measure sizes** | Kept for ten minutes; **Measure again** measures anew |
| Cache status | `php bin/php/cache.php status` ends in `PASS` | [Operating a site](../../guides/operating-a-site.md#10-a-weekly-checklist) |

The weekly checklist of [Operating a site](../../guides/operating-a-site.md#10-a-weekly-checklist) is a ready-made
set of yes/no KPIs: caches PASS, every audit chain intact, nothing unexplained in `error.log`, cron entries
present, a backup from the last seven days that has been restored at least once.

## Content quality

From [Content checks](../../features/6.0/content-checks.md), in the Setup menu:

| KPI | Where | What it counts |
|---|---|---|
| Broken relations | `/contentcheck/brokenrelations/<node id>` | Published content that refers to something deleted, in the trash, hidden or never published |
| Unreferenced assets | `/contentcheck/unreferenced/<node id>` | Published images, files and media below a node that nothing uses |
| Orphan files and their size | `/contentcheck/storage/<node id>` | Files in `var/<site>/storage` no content uses, with totals |
| Missing files | Missing files in `var/<site>/storage` | Images, files and media whose file is missing, and image aliases missing or out of date |

The same report from a shell: `./console exp:content-check` (report only). Start each check on the same node every
time, so the numbers compare.

## Currency: versions and file consistency

| KPI | Where | Notes |
|---|---|---|
| Version running vs. newest release | **Setup > System information** vs. the newest tag (`git tag -l 'v*' --sort=version:refname`) | Security fixes reach a site only through the next release ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |
| Files that differ from the release | **Setup > Upgrade check > Check file consistency**: *Modified*, *Missing*, *Unreadable* | Files changed on purpose are expected; a growing list is not ([The upgrade check](../../guides/upgrade-check.md)) |
| Database differences from the schema | **Check database consistency** | Grouped by table |
| Extension versions | **Setup > Extensions** | Each extension states its own version |
| Local patches carried | Your own record (handover document) | See [Working with the community](working-with-the-community.md#planning-contribution-into-a-project) |

## Performance

`./console exp:benchmark` requests a set of pages a number of times and prints median, p90, p99, minimum, maximum and
standard deviation per page; `--save` keeps a run and `--baseline` compares with it and exits 1 on a regression
([Benchmarking](../../guides/benchmarking.md)).

| KPI | How |
|---|---|
| Median and p90 response time of key pages, cached | `exp:benchmark` with your page list |
| The same for rendered pages | `--cold` |
| Regression after a deploy | `--baseline=<file>`; exit status 0 means no regression |

The guide's warning applies to every report: "One run on a busy machine proves little: compare runs made back to
back, with the same counts, the same pages and the same PHP set-up" (section 10 has a checklist). Report the
conditions together with the numbers.

## Engineering: CI and the repository

For teams whose repositories run on GitHub Actions, the project's own report script works on them too:
`python3 .github/scripts/ci_health_report.py --days 14` prints the pass rate per workflow and event, the steps and
tests that fail most, and flaky runs ([CI, "How healthy CI is"](../../bc/6.0/ci.md#how-healthy-ci-is-the-report)).
The project published its own baseline: 61.7 % of 1,540 finished runs passed from 2026-10-03 to 2026-10-17, before
that week's repairs.

From Git itself, the same commands [Who is 7x?](../who-is-7x.md#7x-in-numbers) uses for its figures:

| KPI | Command |
|---|---|
| Commits in a period | `git rev-list --count --since=<date> HEAD` |
| Merged pull requests | `git log --grep="Merge pull request" --oneline` (count the lines) |
| Changes by type | `git log --format=%s` and count the `Added:`, `Updated:`, `Fixed:`, `Removed:`, `Renamed:` prefixes |

## What the product does not measure

Be honest in your reports about these:

- **Visitor traffic.** The sources used here describe no built-in visitor analytics report. What exists: the audit
  `read` channel records sampled node views, searches and downloads, but only when reads are switched on
  ([The audit trail](../../features/6.0/audit-trail.md)); the `updateviewcount` cronjob (named in the
  [6.0.11 changelog](../../../changelogs/6.0/6.0.11.md)); and the [bcwebsitestatistics](../../features/6.0/extensions/bcwebsitestatistics.md)
  extension, which adds an external analytics service's script. Choose deliberately, with privacy in mind.
- **Editor satisfaction, time saved, conversions.** Measure them with your own surveys or tools, and say so.
- **Uptime from the outside.** System information reads the server it runs on; check from outside too
  ([10.11 Monitoring and health checks](../../install/10-after-installing.md#1011-monitoring-and-health-checks)).

## A monthly KPI report template

```text
KPI REPORT: <site name>, <month>          Prepared by: <role>     Data read on: <date>

Editorial (Audit > Charts / Export, window <dates>)
  Items published:                         <n>      previous month: <n>
  Items removed:                           <n>      previous month: <n>
  Approval backlog (waiting processes):    <n>, oldest <days> days

Security (Audit dashboard and alerts; error digest of security.log and csp)
  Failed sign-ins:                         <n>
  Alerts fired:                            <n>  (<rules>)
  Audit chains intact on:                  <date>   yes / no
  security.log top groups:                 <category/rule: n>, ...
  CSP violation groups:                    <n>

Reliability (Error digest, window 24 h / 168 h)
  Distinct error groups:                   <n>   new since last report: <n>
  404 groups by reason (top 3):            <reason: n>, ...

Operations
  System information failures / warnings:  <n> / <n>
  Cronjob runs with issues (last 20):      <n>
  Log disk use vs budget:                  <MB> of <MB>
  Backup restored on:                      <date>

Content quality (Content checks, start node <node>)
  Broken relations:                        <n>
  Unreferenced assets:                     <n>
  Orphan files:                            <n>, <MB>
  Missing files:                           <n>

Currency
  Running version / newest release:        <version> / <version>
  Upgrade check: modified / missing:       <n> / <n>
  Local patches carried:                   <n>

Performance (exp:benchmark, conditions: <server, counts, cached/cold>)
  Front page median / p90:                 <ms> / <ms>
  Regression against baseline:             yes / no

Comments and actions:
  - <finding> -> <action>, owner <role>, due <date>
```

## Related pages

- [The audit trail](../../features/6.0/audit-trail.md) and the [audit reference](../../bc/6.0/audit.md)
- [Log viewer](../../features/6.0/log-viewer.md), [Request logs](../../features/6.0/request-logs.md)
- [Cronjobs](../../guides/cronjobs.md), [System information](../../guides/system-information.md), [Content checks](../../features/6.0/content-checks.md)
- [Benchmarking](../../guides/benchmarking.md), [Continuous integration](../../bc/6.0/ci.md)
- [Quality gates and acceptance](quality-gates-and-acceptance.md), [Risk management](risk-management.md), [Project templates](project-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
