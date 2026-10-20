---
title: Patterns in kernel/private/classes/cronjobs
audience: developer
category: standards
order: 518
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/cronjobs with its design patterns.
keywords: kernel/private/classes/cronjobs, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/cronjobs

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/cronjobs` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (27)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Cronjob\Kernel\Audit` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/audit.php:17` | The cronjob part audit (cronjobs/audit.php; [CronjobPart-audit] and the frequent group in cronjob.ini): the audit's scheduled work, expAuditMaintenan… |
| `Exponential\Cronjob\Kernel\BasketCleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/basket_cleanup.php:25` | The code of cronjobs/basket_cleanup.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Cachecleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/cachecleanup.php:59` | The code of cronjobs/cachecleanup.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Cleanuprss` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/cleanuprss.php:29` | The code of cronjobs/cleanuprss.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Clusterpurge` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/clusterpurge.php:25` | The code of cronjobs/clusterpurge.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Contentcheck` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/contentcheck.php:18` | The code of cronjobs/contentcheck.php: builds the reference index of the content checks and keeps it in the cache (expContentCheckReferences). |
| `Exponential\Cronjob\Kernel\Contentjobs` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/contentjobs.php:20` | The cronjob part contentjobs (cronjobs/contentjobs.php, frequent group): starts the content jobs that need a worker and have none — a running job who… |
| `Exponential\Cronjob\Kernel\Explogrotate` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/explogrotate.php:19` | The code of cronjobs/explogrotate.php: the log rotation cronjob part ([CronjobPart-logs], in no default part, so it runs only when a site adds it to… |
| `Exponential\Cronjob\Kernel\Hide` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/hide.php:25` | The code of cronjobs/hide.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\HttpcacheCleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/httpcache_cleanup.php:23` | The code of cronjobs/httpcache_cleanup.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Indexcontent` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/indexcontent.php:25` | The code of cronjobs/indexcontent.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\InternalDraftsCleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/internal_drafts_cleanup.php:25` | The code of cronjobs/internal_drafts_cleanup.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Linkcheck` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/linkcheck.php:26` | The code of cronjobs/linkcheck.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Mailbounces` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/mailbounces.php:15` | – |
| `Exponential\Cronjob\Kernel\Mailpreferences` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/mailpreferences.php:15` | – |
| `Exponential\Cronjob\Kernel\Notification` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/notification.php:25` | The code of cronjobs/notification.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\OldDraftsCleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/old_drafts_cleanup.php:25` | The code of cronjobs/old_drafts_cleanup.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\QuerycacheCleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/querycache_cleanup.php:18` | The code of cronjobs/querycache_cleanup.php. |
| `Exponential\Cronjob\Kernel\Rssimport` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/rssimport.php:407` | The code of cronjobs/rssimport.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\SessionGc` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/session_gc.php:25` | The code of cronjobs/session_gc.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\StaticcacheCleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/staticcache_cleanup.php:25` | The code of cronjobs/staticcache_cleanup.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Subtreeexpirycleanup` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/subtreeexpirycleanup.php:25` | The code of cronjobs/subtreeexpirycleanup.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Trashpurge` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/trashpurge.php:25` | The code of cronjobs/trashpurge.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Unlock` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/unlock.php:93` | The code of cronjobs/unlock.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Unpublish` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/unpublish.php:25` | The code of cronjobs/unpublish.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Updateviewcount` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/updateviewcount.php:25` | The code of cronjobs/updateviewcount.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Kernel\Workflow` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `kernel/private/classes/cronjobs/workflow.php:25` | The code of cronjobs/workflow.php, moved into a class (#207 stage 1). |
