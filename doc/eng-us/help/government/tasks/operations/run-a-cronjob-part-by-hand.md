---
title: Run a cronjob part by hand
audience: government, admin
category: public-sector
order: 110
summary: Step by step - run one cronjob part or one script now, from Setup > Cronjobs with live output or from the shell, for example to publish approved content at once or after a restore.
keywords: run cronjob, Run part, runcronjobs, --script, workflow.php, frequent, manual run, output, console cron alias
type: help
---
# Run a cronjob part by hand

This task is for administrators who cannot wait for the schedule: approved content must go live now, an import must
run after a restore, or a new script must be tried. Reference: [Cronjobs, section 7](../../../../guides/cronjobs.md#7-running-a-part-one-script-or-every-part).

## Contents
1. [Before you start](#before-you-start)
2. [From the administration](#from-the-administration)
3. [From the shell](#from-the-shell)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- `setup/managecronjobs`, or shell access as the site's user.
- The part is not paused (a paused part is left out by every runner).

## From the administration

1. **Setup > Cronjobs**; choose the site under **Run for site** (a job reads the settings of that siteaccess).
2. On the part's card press **Run part**, or open its scripts and run one alone.
3. The output follows while the job runs; **Recent runs** records it.

Some destructive parts (such as cluster purging) are kept off the buttons by `cronjob.ini [AdminSettings]
ForbiddenParts[]` and run only from a shell.

## From the shell

```bash
php runcronjobs.php --siteaccess=site frequent --allow-root-user
php runcronjobs.php --siteaccess=site --script=workflow.php --allow-root-user
./console list cron                    # the console aliases, for example ./console cron:frequent
```

Use your public siteaccess name; leave out `--allow-root-user` when you run as the site's user (preferred, so files it
writes stay writable for the site).

## Check it worked

- The output ends without errors; for `workflow.php` it ends like "N out of M processes was finished".
- The audit trail has `system.cronjob.run` for the run.
- For workflows: **Setup > Workflow processes** no longer lists the approved processes.

## Related pages
- [Check that cronjobs run](check-that-cronjobs-run.md)
- [Workflow troubleshooting](../../operations/editorial-workflows/workflow-troubleshooting.md)
- [Cronjob console (hosting)](../../../hosting/operations/cronjob-console.md)
- [Cronjobs guide](../../../../guides/cronjobs.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
