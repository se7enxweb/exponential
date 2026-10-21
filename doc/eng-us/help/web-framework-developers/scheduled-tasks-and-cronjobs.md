---
title: "Scheduled tasks: cronjobs"
audience: web-framework-developer, developer
category: web-frameworks
order: 330
summary: The Exponential counterpart of Laravel's scheduler, Symfony Scheduler, Celery beat, whenever and Spring's @Scheduled - cronjob parts in cronjob.ini run by runcronjobs.php from the system crontab, the shipped parts the site needs, adding a part of your extension, locks, pauses and the Setup > Cronjobs console.
keywords: cron, cronjob, scheduler, scheduled tasks, runcronjobs.php, cronjob.ini, CronjobPart, frequent, infrequent, crontab, cron:, MaxScriptExecutionTime, pause, Laravel scheduler, Symfony Scheduler, Celery beat, Spring Scheduled, web framework developer
type: help
---
# Scheduled tasks: cronjobs

This page is for developers who declare scheduled tasks in code (`$schedule->command()->hourly()`, `#[AsCronTask]`,
Celery beat, `@Scheduled`). In Exponential, scheduled work is grouped into **cronjob parts** declared in
`settings/cronjob.ini`; the system's crontab runs `php runcronjobs.php <part>` at the rhythm each part needs. The
schedule itself lives in the crontab, not in code ([Cronjobs for applications](../creators/app-creators/cronjobs-for-applications.md)).

## Contents

1. [Parts and scripts](#parts-and-scripts)
2. [Parts the site needs](#parts-the-site-needs)
3. [Adding your part](#adding-your-part)
4. [Running and scheduling](#running-and-scheduling)
5. [Locks, pauses and watching](#locks-pauses-and-watching)
6. [Compared with other frameworks](#compared-with-other-frameworks)

## Parts and scripts

| Block | Holds |
|---|---|
| `[CronjobSettings] Scripts[]` | the global part (run when no part is named) |
| `[CronjobSettings] ExtensionDirectories[]` | extensions that have a `cronjobs/` directory |
| `[CronjobPart-<name>] Scripts[]` | a named part |

`eZRunCronjobs` (`kernel/classes/ezruncronjobs.php`) runs a script; the code of a kernel part is an
`Exponential\Runnable\CronjobPart` class in `kernel/private/classes/cronjobs/`
([Cronjobs](../../standards/architecture/kernel-architecture.md#cronjobs)).

## Parts the site needs

Shipped parts include `frequent` (notification, workflow, content jobs, audit) and `infrequent` (basket cleanup, link
check, mail preferences), among others. Without `frequent`, notifications are not sent and content waiting in a
deferred workflow never publishes. Schedule them in every installation ([Cronjobs guide](../../guides/cronjobs.md)).

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

The script lives in `extension/expdemo/cronjobs/expdemo_import.php` ([Writing a cronjob script](../creators/app-creators/writing-a-cronjob-part.md)).
A part of your own can be scheduled separately from the kernel's.

## Running and scheduling

```bash
php runcronjobs.php --list --allow-root-user
php runcronjobs.php -s site expdemo --allow-root-user
php bin/php/console cron:expdemo -s site
```

```text
*/5 * * * *  cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
15  * * * *  cd /path/to/installation && php runcronjobs.php -q -s site expdemo >> var/log/cron-expdemo.log 2>&1
```

Run as the web server's user, never as root. `-s <siteaccess>` matters when your extension is active only in some
siteaccesses. **Setup > Cronjobs** proposes crontab lines.

## Locks, pauses and watching

- Only one instance of a cronjob script runs at a time; `MaxScriptExecutionTime` lets a new instance take over a stuck
  one.
- Parts can be paused in **Setup > Cronjobs**; a paused part is skipped whoever starts it
  ([Pausing cronjob parts](../../features/6.0/cronjob-pause.md)).
- **Setup > Cronjobs** shows each part, its last runs, output and issues ([Cronjobs console](../../features/6.0/cronjobs-console.md)).
- Make scripts safe to run twice and to be interrupted.

Velocity has a scheduler of its own for handlers of its application layer; it does not run Exponential's cronjob
scripts, so keep the system crontab for them ([Velocity scheduler](../../features/6.0/velocity-scheduler.md)).

## Compared with other frameworks

| Framework | Concept | Exponential |
|---|---|---|
| Laravel | `schedule:run` every minute, schedule in code | one crontab line per part, parts in INI |
| Symfony | Scheduler component, recurring messages | cronjob parts; recurring jobs can enqueue expqueue jobs |
| Django, Celery | management commands from cron, Celery beat | cronjob parts |
| Rails | whenever, Solid Queue recurring tasks | cronjob parts |
| Spring | `@Scheduled` | cronjob parts |

## Related pages

- [Queues and background jobs](queues-and-background-jobs.md)
- [Cronjob part filter](../../features/6.0/cronjob-part-filter.md)
- [Runnable commands, cronjob parts and views](../../specifications/6.0/runnable-commands-cronjobs-views.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
