---
title: Cronjobs
audience: cms-developer
category: cms-development
order: 36
summary: For CMS developers, the scheduled work an Exponential site depends on. It covers parts and scripts, what the default parts do, scheduling with the crontab, running parts from the shell and the console, the Cronjobs page in the administration, and adding a cronjob of your own.
keywords: cronjobs, cron, runcronjobs.php, cronjob.ini, cronjob part, CronjobPart, frequent, infrequent, global part, workflow.php, indexcontent.php, notification.php, crontab, cron console command, scheduled tasks, custom cronjob
type: help
---
# Cronjobs

This page is for developers who need to know what runs in the background of a site and how to add their own
scheduled work. Several features only complete through cronjobs: approved content is published, delayed content is
indexed, notifications are sent, dated content is hidden. A site without its crontab looks fine and quietly stops
doing those things.

## Contents

1. [Parts and scripts](#parts-and-scripts)
2. [What the shipped parts do](#what-the-shipped-parts-do)
3. [Running parts](#running-parts)
4. [Scheduling](#scheduling)
5. [The Cronjobs page](#the-cronjobs-page)
6. [A cronjob of your own](#a-cronjob-of-your-own)
7. [Checks before going live](#checks-before-going-live)

## Parts and scripts

A **script** is a PHP file in a cronjob directory (`cronjobs/` of the installation, or of an extension named in
`[CronjobSettings] ExtensionDirectories[]`). A **part** is a group of scripts that run together on one schedule,
declared in `cronjob.ini` as `[CronjobPart-<name>] Scripts[]`. The scripts of `[CronjobSettings] Scripts[]` are the
default part, shown as **global** ([Cronjobs, section 1](../../guides/cronjobs.md#1-parts-scripts-and-the-global-part)).

## What the shipped parts do

| Part | Scripts include | Why it matters |
|---|---|---|
| global (default) | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | dated removal and hiding, RSS import, delayed search indexing |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | notifications, workflows that wait (approvals), background content jobs |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | shop baskets, checking links, mail preference retention |
| the other parts | logs, cache cleanup, `unlock`, `cleanuprss` and more | housekeeping |

`settings/cronjob.ini` has the full list (`infrequent`, `frequent`, `contentcheck`, `contentjobs`, `audit`, `logs`,
`notification`, `mailpreferences`, `mailbounces`, `unlock`, `cluster_maintenance`, `cleanuprss`, `cache_cleanup`), and
extensions add parts of their own, such as sitemaps or newsletters. List what your installation has:

```bash
php runcronjobs.php --list --allow-root-user
```

## Running parts

```bash
php runcronjobs.php -s <siteaccess> frequent --allow-root-user        # one part
php runcronjobs.php -s <siteaccess> --allow-root-user                 # the default part
php runcronjobs.php -s <siteaccess> --script=workflow.php --allow-root-user   # one script
./console list cron                                                    # every part as a console command, cron:<part>
```

Run as the user the web server runs as, so files the cronjobs write stay readable by the site. Each script takes its
own lock, so a second run of the same script waits or skips
([Running cronjobs from the shell](../../guides/cronjobs.md#13-running-cronjobs-from-the-shell)).

## Scheduling

Add one crontab line per part, from the installation root:

```text
*/5 * * * *  cd /var/www/example && php runcronjobs.php -s site frequent >/dev/null 2>&1
*/15 * * * * cd /var/www/example && php runcronjobs.php -s site >/dev/null 2>&1
17 * * * *   cd /var/www/example && php runcronjobs.php -s site infrequent >/dev/null 2>&1
```

These are the default schedules the Cronjobs page proposes; set your own per part with
`cronjob.ini [AdminSettings] CrontabSchedule_<part>` ([Scheduling with the crontab](../../guides/cronjobs.md#12-scheduling-with-the-crontab)).
Hosting panels have their own cron screens ([Cron and shell access](../hosting/panels/cron-and-shell-access.md)).

## The Cronjobs page

The administration lists every part and script, shows whether the crontab schedules it, runs a part or a script with
live output, keeps the recent runs and counts issues, and proposes crontab lines. Use it to try a new script before
scheduling it and to find out why something did not happen overnight ([Cronjobs](../../guides/cronjobs.md),
[Cronjobs console](../../features/6.0/cronjobs-console.md)).

## A cronjob of your own

1. Write the script in `extension/exp_mysite/cronjobs/exp_feed_sync.php`, with a `@description` tag in its doc comment
   (the console and the page show it).
2. Declare the directory and a part in `extension/exp_mysite/settings/cronjob.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [CronjobSettings]
   ExtensionDirectories[]=exp_mysite

   [CronjobPart-exp_mysite]
   Scripts[]=exp_feed_sync.php
   */ ?>
   ```

3. Clear the INI cache, run the part by hand (`php runcronjobs.php -s <siteaccess> exp_mysite`), then schedule it.

Quiet mode, logging and the stop switches are in [Cronjob parts](../extension-development/part-3-extension-developer/cronjob-parts.md)
and the tutorial [A cronjob part by hand](../extension-development/part-3-extension-developer/tutorials/by-hand-cronjob-part.md).
Long work belongs in a cronjob or a content job, not in a page request.

## Checks before going live

- [ ] The `frequent` part and the default part are scheduled for every public siteaccess that needs them.
- [ ] Workflows with approvals, delayed indexing and notifications have been tested end to end with cron running.
- [ ] Cron runs as the web server's user and its output is logged or mailed to someone who reads it.
- [ ] Your own parts are scheduled and documented in the project.

## Related pages

- [Cron jobs for hosts](../hosting/operations/cron-jobs.md)
- [Cronjobs for builders](../extension-development/part-2-builder/cronjobs-for-builders.md)
- [Workflows and triggers](workflows-and-triggers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
