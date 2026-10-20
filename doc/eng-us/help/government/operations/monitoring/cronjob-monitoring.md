---
title: Cronjob monitoring
audience: government, admin, hosting
category: public-sector
order: 90
summary: Making sure the scheduled jobs of a public-sector Exponential site really run - what depends on them (approvals, audit alerts, notifications, indexing), Setup > Cronjobs with its runs and issues, pauses, the system information check, and outside monitoring of the crontab.
keywords: cronjobs, cron, scheduled jobs, runcronjobs, crontab, Setup Cronjobs, frequent, infrequent, workflow.php, audit cronjob, pause, recent runs, monitoring
type: help
---
# Cronjob monitoring

This page is for administrators who must notice when scheduled work stops. On a public website more depends on
cronjobs than people expect: approved content is published by the workflow cronjob, audit alerts and archives come
from the `audit` part, notifications and newsletters are sent by cron. Reference: [Cronjobs](../../../../guides/cronjobs.md);
hosting view: [Cron jobs (hosting)](../../../hosting/operations/cron-jobs.md).

## Contents
1. [What stops when cron stops](#what-stops-when-cron-stops)
2. [Where to look](#where-to-look)
3. [Pauses](#pauses)
4. [Monitoring from outside](#monitoring-from-outside)
5. [Related pages](#related-pages)

## What stops when cron stops

| Part or script | Effect when it does not run |
|---|---|
| `workflow.php` (frequent) | approved content is never published |
| `audit` part (in frequent) | no indexing, no alerts, no daily rotation, verification and archiving |
| `notification.php` (frequent) | no notification mails |
| `mailpreferences` (infrequent) | consent log and confirmations not kept within retention |
| indexing, sitemaps, RSS import | search and feeds out of date |
| `hide.php`, `unpublish.php` (global part) | scheduled hiding and removal do not happen |

## Where to look

- **Setup > Cronjobs** (`/setup/cronjobs`, needs `setup/managecronjobs`): a card per part with its schedule read from
  the installed crontab, the next and last run, **Run part**, and **Recent runs** (the last 20 started from the
  page) with *OK* or *n issues*. Issues are lines with words such as *error* or *failed*: a pointer, not a verdict.
- **Setup > Workflow processes**: its status bar says whether `workflow.php` is scheduled.
- **Setup > System information**: the health check *Cronjobs* warns when no run is found or the last one is more than
  two days old.
- Shell: `./console crontab:list` shows the installed lines; `php runcronjobs.php --list --allow-root-user` the parts.
- Audit trail: each run is `system.cronjob.run` or `system.cronjob.fail`, whoever started it.

## Pauses

A part can be paused on **Setup > Cronjobs** (**Pause**, **Pause all parts**) during a release or migration; a paused
part is left out by every runner and the card shows since when, by whom and why
([Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md)). Monitoring should treat "paused" as a state to
report daily: a pause forgotten after a window silently stops approvals and alerts.

## Monitoring from outside

- Alert when the system information check fails: `./console exp:system:info --json --allow-root-user` exits 1 on a
  failing check.
- Write the cron output to a log and alert when it stops growing, for example with the lines the cronjobs page
  proposes:

  ```
  */5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
  ```

- On several web servers, run each part on one server only, and remember pause files are local to each server unless
  `cronjob.ini [PauseSettings] Directory` points at a shared directory.
- Under Velocity, its own scheduler can start the parts instead of the system cron
  ([Velocity scheduler](../../../../features/6.0/velocity-scheduler.md)).

## Related pages
- [Check that cronjobs run](../../tasks/operations/check-that-cronjobs-run.md)
- [Run a cronjob part by hand](../../tasks/operations/run-a-cronjob-part-by-hand.md)
- [Workflow troubleshooting](../editorial-workflows/workflow-troubleshooting.md)
- [Cronjobs console](../../../../features/6.0/cronjobs-console.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
