---
title: Check that cronjobs run
audience: government, admin, hosting
category: public-sector
order: 100
summary: Step by step - confirm the scheduled jobs of the site run - the crontab entries, the cards and recent runs on Setup > Cronjobs, the workflow status bar, the system information check - and install the proposed crontab lines when they are missing.
keywords: cronjobs, crontab, crontab:list, runcronjobs, Setup Cronjobs, frequent, infrequent, workflow.php, scheduled, not scheduled, recent runs, system information
type: help
---
# Check that cronjobs run

This task is for administrators doing a weekly check, or chasing "approved content was not published". At the end you
know every part runs on schedule. Reference: [Cronjobs](../../../../guides/cronjobs.md); background:
[Cronjob monitoring](../../operations/monitoring/cronjob-monitoring.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Install missing lines](#install-missing-lines)
4. [Related pages](#related-pages)

## Before you start

- `setup/managecronjobs` for **Setup > Cronjobs**; shell access as the site's user for the crontab.

## Steps

1. The installed schedule:

   ```bash
   ./console crontab:list
   php runcronjobs.php --list --allow-root-user
   ```

2. **Setup > Cronjobs** (`/setup/cronjobs`): each part's card shows its schedule (read from the crontab), next and
   last run, and whether it is **Paused**. **Recent runs** lists the last 20 runs started from the page with *OK* or
   *n issues*.
3. **Setup > Workflow processes**: the status bar must say `workflow.php` is scheduled.
4. **Setup > System information**, health check *Cronjobs*: a warning means no run found or the last one more than two
   days ago.
5. The audit trail: `./console exp:audit search --name='system.cronjob.*' --limit=20 --allow-root-user` shows runs
   (`system.cronjob.run`) and failures (`system.cronjob.fail`).

## Install missing lines

The cronjobs page proposes lines; a common start (as the site's user, your paths and siteaccess):

```
*/5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
17 * * * *  cd /path/to/installation && php runcronjobs.php --siteaccess=site infrequent --allow-root-user >> var/log/cron-infrequent.log 2>&1
```

Open the crontab with `./console crontab:edit`. Under Velocity its scheduler can run the parts instead
([Velocity scheduler](../../../../features/6.0/velocity-scheduler.md)). On several web servers, run each part on one
server only.

Note: `--allow-root-user` is needed only when the line runs as root; as the site's user, leave it out.

## Related pages
- [Run a cronjob part by hand](run-a-cronjob-part-by-hand.md)
- [Pause cronjobs during maintenance](pause-cronjobs-during-maintenance.md)
- [Cron jobs (hosting)](../../../hosting/operations/cron-jobs.md)
- [Cronjobs console](../../../../features/6.0/cronjobs-console.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
