---
title: Cronjobs for builders
audience: developer, designer, app-creator, admin
category: extension-development
order: 200
summary: What runs on a schedule - cronjob parts and scripts in cronjob.ini, runcronjobs.php, the Setup > Cronjobs page and the crontab lines - and how a builder adds an existing script to a part or schedules a part.
keywords: cronjob, cronjob.ini, CronjobPart, frequent, infrequent, runcronjobs.php, crontab, workflow.php, notification.php, schedule, Setup Cronjobs, --list, --script
type: help
---
# Cronjobs for builders

Part 2, chapter 20. Some work happens outside a page request: resuming workflows, sending notifications, cleaning up
drafts, rotating logs. Exponential groups such scripts into **cronjob parts** and runs them with `runcronjobs.php`,
started by the system's crontab. This chapter is the builder's view; writing your own script is
[Cronjob parts](../part-3-extension-developer/cronjob-parts.md) in Part 3, and the complete guide is
[Cronjobs](../../../guides/cronjobs.md).

## Parts and scripts

`settings/cronjob.ini` defines them. The scripts of the **global part** are in `[CronjobSettings] Scripts[]`; each
named part is a block `[CronjobPart-<name>]` with its own `Scripts[]`. Shipped parts include:

| Part | Scripts (shipped) | Runs |
|---|---|---|
| global | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | every quarter hour is usual |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | every few minutes |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | hourly |
| `contentcheck`, `contentjobs`, `audit`, `logs` | one task each | as needed |

Scripts are found in the directories of `[CronjobSettings] ScriptDirectories[]` (the kernel's `cronjobs/`) and of
active extensions listed in `ExtensionDirectories[]`.

## Running them

```bash
php runcronjobs.php --list                        # every part and its scripts
php runcronjobs.php -s site frequent              # one part, for the siteaccess "site"
php runcronjobs.php -s site --script=workflow.php # one script
php runcronjobs.php -s site                       # the global part
```

`-q` runs quietly. Run cronjobs as the user the web server runs as, so the files they write stay writable for the site.

## The administration page

**Setup > Cronjobs** lists the parts with their scripts, runs a part or a script while you watch its output, shows
recent runs and problems, and proposes crontab lines written with your installation's own path and PHP binary, for
example:

```text
*/5 * * * * cd /path/to/installation && php runcronjobs.php -q -s site frequent >/dev/null 2>&1
```

Nothing is added to the crontab for you: copy the lines into the crontab of the site's user (`crontab -e`). The
schedule the page proposes can be set per part with `[AdminSettings] CrontabSchedule_<part>` in your
`cronjob.ini.append.php`.

## What a builder changes

**Add an existing script to a part** (for example a cleanup script an extension ships but no part runs):

```ini
# extension/mysite_settings/settings/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=session_gc.php
```

**A part of your own**, scheduled at night:

```ini
[CronjobPart-nightly]
Scripts[]=internal_drafts_cleanup.php

[AdminSettings]
CrontabSchedule_nightly=30 3 * * *
```

**Pause during a release**: **Setup > Cronjobs** pauses one part (**Pause** on its card) or all parts until someone
resumes them, and `runcronjobs.php` then skips them (`[PauseSettings] Pause=disabled` switches the check off); see
[Pausing cronjobs](../../../features/6.0/cronjob-pause.md).

## Checks

| Symptom | Check |
|---|---|
| Articles wait for approval forever, notifications never arrive | `frequent` is not in the crontab |
| A script is listed but never runs | it is in no part, or its part is not scheduled |
| A script runs three times in `--list` | `ScriptDirectories[]` names the same directory more than once; harmless for `--list` |
| Files written by cron cannot be changed by the site | cron runs as another user |

## Related pages

- Previous: [Workflows for builders](workflows-for-builders.md)
- Next: [Building a site without writing code](building-a-site-without-code.md)
- [Cronjobs](../../../guides/cronjobs.md), [Cronjobs on the console](../../../features/6.0/cronjobs-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
