---
title: Cronjobs and background jobs
audience: businesses, admin, project-manager
category: business-solutions
order: 210
summary: The scheduled and background work an Exponential site depends on (notifications, approvals, content jobs, the audit trail, newsletters, sitemaps), what fails quietly when it stops, and how a business makes sure it runs and notices when it does not.
keywords: cronjobs, cron, runcronjobs, background jobs, content jobs, scheduled tasks, workflow, notifications, audit, Setup Cronjobs, crontab, pause, frequent, infrequent
type: help
---
# Cronjobs and background jobs

This page is for managers who want to know what runs behind the scenes, and for the administrator who schedules it.
Much of a site's work happens outside page requests: sending notifications, publishing approved content, finishing
large removals, indexing the audit trail, sending newsletters, building sitemaps. When that work stops, nothing breaks
visibly at first, which is exactly why it needs attention. The full guide is
[Cronjobs: running, scheduling and following them](../../../guides/cronjobs.md).

![The cronjobs page](../../images/help/bu-cron.webp)

## Contents

1. [Why background work matters to a business](#why-background-work-matters-to-a-business)
2. [What runs, in parts](#what-runs-in-parts)
3. [Scheduling it](#scheduling-it)
4. [Knowing that it ran](#knowing-that-it-ran)
5. [Content jobs: large operations in the background](#content-jobs-large-operations-in-the-background)
6. [Pausing during planned work](#pausing-during-planned-work)
7. [Several servers and test copies](#several-servers-and-test-copies)
8. [Checklist](#checklist)

## Why background work matters to a business

"Cronjobs do the work nobody waits for: publishing scheduled content, importing feeds, cleaning baskets, sending
newsletters, building sitemaps" ([Operating a site, run cronjobs](../../../guides/operating-a-site.md#3-run-cronjobs)).
Examples of quiet failures when they stop:

| If this stops | The business notices |
|---|---|
| The workflow cronjob | Approved content is never published: "Without that cronjob, approved content is never published" ([Workflows](../../../guides/workflows.md#in-short)) |
| Notifications | Subscribers stop getting update mails |
| The audit part | The audit is not indexed, alert rules are not evaluated, daily archives are not written ([Audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes)) |
| Content jobs | Large removals or copies started in the background do not progress |
| Newsletter parts | Editions are not sent ([Newsletters](../newsletters/README.md)) |
| Sitemap parts | Search engines see an outdated sitemap |

## What runs, in parts

Scripts are grouped into **parts**, each run on its own schedule. The shipped `frequent` part, from `settings/cronjob.ini`
([Cronjobs, parts and scripts](../../../guides/cronjobs.md#1-parts-scripts-and-the-global-part)):

```ini
[CronjobPart-frequent]
Scripts[]=notification.php
Scripts[]=workflow.php
Scripts[]=contentjobs.php
Scripts[]=audit.php
Scripts[]=mailbounces.php
```

Other parts (`infrequent` and those that extensions add, such as newsletter and sitemap parts) carry slower work. Each
script takes a lock while it runs, so a second run of the same script does not overlap the first.

**Setup > Cronjobs** (`/setup/cronjobs`, policy `setup/managecronjobs`) shows one card per part: whether the crontab
runs it, its schedule in words, its next and last run, and the command that runs it
([Cronjobs, in short](../../../guides/cronjobs.md#in-short)).

## Scheduling it

Cronjobs are started by the server's scheduler (the crontab). The page **Setup > Cronjobs** proposes the lines to
install. A common starting point from the operations guide is the frequent part every few minutes and the infrequent
part once an hour or a day ([Operating a site](../../../guides/operating-a-site.md#3-run-cronjobs)):

```
*/5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent >> var/log/cron-frequent.log 2>&1
17 * * * *  cd /path/to/installation && php runcronjobs.php --siteaccess=site infrequent >> var/log/cron-infrequent.log 2>&1
```

Replace the path and the siteaccess with your own. Show what is installed with `php bin/php/console crontab:list`.
Shared hosting panels usually offer a cron page for the same lines
([Cron and shell access in panels](../../hosting/panels/cron-and-shell-access.md)).

> **Note**
> Velocity has a scheduler of its own, but it runs handlers of Velocity applications, "not an arbitrary command-line
> PHP script"; Exponential's own periodic jobs run through `runcronjobs.php`
> ([Velocity scheduler](../../../features/6.0/velocity-scheduler.md)).

## Knowing that it ran

Three signals, from cheapest to most thorough:

1. **Setup > System information** fails its Cronjobs check when no run is found, or the last one was "more than two
   days ago" ([System information, health checks](../../../guides/system-information.md#3-health-checks)). Its
   command-line form exits 1, so a monitor sees it ([Health checks and monitoring](health-checks-and-monitoring.md)).
2. **Setup > Cronjobs** shows the last run of each part and **Recent runs** with their results; issues are lines with
   words such as *error* or *failed*, "a pointer, not a verdict" ([Cronjobs, recent runs](../../../guides/cronjobs.md#10-recent-runs-and-how-issues-are-counted)).
3. **The audit trail** records each run of a part as `system.cronjob.run` or `system.cronjob.fail`, whoever started it
   (same section).

Add the check of `crontab:list` and the cron log dates to the weekly routine
([Uptime practice](uptime-practice.md#every-week)).

## Content jobs: large operations in the background

Removing, copying or moving a large subtree "used to run inside one web request and could fail on the time limit, the
memory limit or one enormous transaction". From 6.0.15 such an operation can run as a **content job**: "a background
worker that works in batches, takes a checkpoint after each batch, and resumes exactly where it stopped, even after a
`kill -9`" ([Content jobs](../../../features/6.0/content-jobs.md)).

For a business this means:

- An editor removing a large section no longer risks a half-finished operation.
- **content/jobs** lists all jobs with who started them, progress and time left; a job can be canceled or resumed.
- A subtree that one job works on is refused for another job.
- The audit trail records the job as the parent of everything its batches did.
- Jobs are resumed by the `contentjobs` script of the `frequent` part, which must therefore run.

## Pausing during planned work

Cronjobs run in a shell and are **not** stopped by maintenance mode
([Maintenance mode guide](../../../guides/maintenance-mode.md#10-installations-cronjobs-and-deploys)). For an import,
a migration or an upgrade, pause the parts that must not run: **Setup > Cronjobs** pauses one part or all of them until
someone resumes, records who paused what and why, and `runcronjobs.php` leaves paused parts out whoever starts it
([Pausing cronjob parts](../../../features/6.0/cronjob-pause.md)). Resume them when the work is done, and put
"resume cronjobs" on the window's closing checklist.

## Several servers and test copies

- **Several web servers**: install the crontab on one server only, or the same jobs run twice; the script lock is local
  to each server ([Several web nodes](../../hosting/operations/several-web-nodes.md#cron-deploys-maintenance-and-logs)).
- **Restored copies and test machines**: do not install the crontab, "or notifications, newsletters and content jobs
  run against real addresses" ([Restore drills](restore-drills.md#keep-the-copy-quiet-and-private)).

## Checklist

- [ ] The `frequent` and `infrequent` parts (and any extension parts you use) are in the crontab of one server.
- [ ] **Setup > System information** shows no failed Cronjobs check.
- [ ] Someone looks at **Setup > Cronjobs** weekly, and at failed runs at once.
- [ ] Planned work pauses the parts that must not run, and resumes them afterwards.
- [ ] Test copies have no crontab.
- [ ] Editors know large removals and moves can run as content jobs.

## Related pages

- [Cronjobs](../../../guides/cronjobs.md)
- [Cron jobs (hosting)](../../hosting/operations/cron-jobs.md)
- [Content jobs](../../../features/6.0/content-jobs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
