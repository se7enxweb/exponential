---
title: Cronjobs for applications
audience: app-creator, developer, admin
category: app-creation
order: 280
summary: Run an application's periodic work with cronjob parts - how parts and scripts are defined in cronjob.ini, running them with runcronjobs.php and the console, the crontab, siteaccess flags, pausing, locks and monitoring in Setup > Cronjobs.
keywords: cronjobs, cronjob part, cronjob.ini, CronjobPart, runcronjobs.php, cron:frequent, crontab, siteaccess, pause, MaxScriptExecutionTime, Setup > Cronjobs, app creator
type: help
---
# Cronjobs for applications

This page is for application creators whose application has periodic work: imports, reminders, cleanups, reports.
Exponential runs such work as **cronjob parts**: named groups of scripts that the system crontab starts. The
administrator's guide is [Cronjobs](../../../guides/cronjobs.md).

## Contents

1. [Parts and scripts](#parts-and-scripts)
2. [Adding your part](#adding-your-part)
3. [Running parts](#running-parts)
4. [The crontab](#the-crontab)
5. [Locks, pauses and time limits](#locks-pauses-and-time-limits)
6. [Watching them](#watching-them)

## Parts and scripts

`settings/cronjob.ini`:

| Block | Holds |
|---|---|
| `[CronjobSettings] Scripts[]` | the global part (run when no part is named) |
| `[CronjobSettings] ExtensionDirectories[]` | extensions that have a `cronjobs/` directory |
| `[CronjobPart-<name>] Scripts[]` | a named part |

Shipped parts include `frequent` (notification, workflow, content jobs, audit), `infrequent` (basket cleanup, link
check, mail preferences), `contentjobs`, `audit`, `unlock`, `cleanuprss` and `cache_cleanup`
([Commands: cronjob parts](../../../bc/6.0/console.md#cronjob-parts-and-the-crontab-added-june-2026)).

## Adding your part

```ini
# extension/expdemo/settings/cronjob.ini.append.php
<?php /* #?ini charset="utf-8"?
[CronjobSettings]
ExtensionDirectories[]=expdemo

[CronjobPart-expdemo]
Scripts[]=expdemo_import.php
*/ ?>
```

The script lives in `extension/expdemo/cronjobs/expdemo_import.php` ([Writing a cronjob script](writing-a-cronjob-part.md)).
Add it to an existing part only when it is quick and has the same rhythm; a part of your own can be scheduled
separately.

## Running parts

```bash
php runcronjobs.php --list --allow-root-user
php runcronjobs.php -s site expdemo --allow-root-user
php runcronjobs.php -s site --script=expdemo_import.php --allow-root-user
php bin/php/console cron:expdemo -s site
```

`-s <siteaccess>` matters when your extension is active only in some siteaccesses (`ActiveAccessExtensions[]`): the
script is not found otherwise.

## The crontab

```text
*/5 * * * *  cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
15  * * * *  cd /path/to/installation && php runcronjobs.php -q -s site expdemo >> var/log/cron-expdemo.log 2>&1
```

Run as the web server's user, never as root. `php bin/php/console crontab:list` shows the current crontab; **Setup >
Cronjobs** proposes lines ([12. Scheduling with the crontab](../../../guides/cronjobs.md#12-scheduling-with-the-crontab)).

## Locks, pauses and time limits

- Only one instance of a cronjob script runs at a time. `MaxScriptExecutionTime` (default 43200 seconds) lets a new
  instance take over a stuck one; set it per part for long jobs.
- Parts can be paused in **Setup > Cronjobs** (`[PauseSettings] Pause=enabled`); a paused part is skipped whoever starts
  it ([Pausing cronjob parts](../../../features/6.0/cronjob-pause.md)).
- Make scripts safe to re-run: a crashed run is followed by another.

## Watching them

**Setup > Cronjobs** shows each part, its last runs, output and issues, and whether the crontab schedules it
([Cronjobs console](../../../features/6.0/cronjobs-console.md)). Write clear output lines; they are what an administrator
reads when something fails.

## Related pages

- [Writing a cronjob script](writing-a-cronjob-part.md)
- Background work: queues, cronjobs and content jobs
- Cronjob examples
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
