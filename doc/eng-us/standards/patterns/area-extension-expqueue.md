---
title: Patterns in extension/expqueue
audience: developer
category: standards
order: 427
summary: Every class, view, fetch function, operator and plugin point of extension/expqueue with its design patterns.
keywords: extension/expqueue, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expqueue

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expqueue` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (27)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expQueue` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/expqueue/classes/expqueue.php:26` | The job queue: what producers call (enqueue, groups, cancel, pause/resume, retry, results) and the state changes the workers and the admin make. |
| `expQueueAdmin` | class | [Utility (static helper)](utility.md) | `extension/expqueue/classes/expqueueadmin.php:14` | Helpers of the admin module expqueue: the page result with its path, the rows the templates show (every label and duration prepared here, so the temp… |
| `expQueueAuditBranch` | class implements expAuditTaxonomyBranch | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/expqueue/classes/expqueueauditbranch.php:15` | The audit taxonomy branch of expqueue (registered in settings/audit.ini.append.php): system.queue., one event per operator action on the queue and pe… |
| `expQueueCancelledException` | class extends expQueueException | [Exception hierarchy](exception.md) | `extension/expqueue/classes/expqueuecancelledexception.php:9` | Thrown by expQueueContext::throwIfCancelled() when the job was cancelled while it ran; the job ends cancelled. |
| `expQueueCommand` | class | [Command (console, scripts, cronjobs)](command.md), [Factory and static factory methods](factory.md) | `extension/expqueue/classes/expqueuecommand.php:17` | The commands ./console ext:expqueue: (extension/expqueue/bin/php/.php); each file only calls expQueueCommand::main( '' ). |
| `expQueueConfig` | class | [Singleton / instance()](singleton.md) | `extension/expqueue/classes/expqueueconfig.php:15` | The settings of expqueue (expqueue.ini), with the defaults that apply when a setting is missing. |
| `expQueueContext` | class | – | `extension/expqueue/classes/expqueuecontext.php:32` | What a handler gets with its job: the lease (heartbeat), cooperative cancellation, progress, the result checkpoint and the rate-limit hand-off of its… |
| `expQueueEvent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/expqueue/classes/expqueueevent.php:15` | The history of a job (table expqueue_event), append-only, removed with the job. |
| `expQueueException` | class extends RuntimeException | [Exception hierarchy](exception.md) | `extension/expqueue/classes/expqueueexception.php:9` | An error of the queue itself: an unknown class or type, a quota reached, a job that cannot change state. |
| `expQueueFailHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expqueue/classes/handlers/expqueuefailhandler.php:16` | expqueue.fail: fails on purpose, to try retries, backoff, the dead letter and the rate-limit adaptation. |
| `expQueueGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expqueue/classes/expqueuegroup.php:15` | A set of jobs that belong together (table expqueue_group): "translate subtree X into ten languages", a package import, a chain run. |
| `expQueueHandler` | interface | [INI plugin point (extension point)](ini-plugin-point.md), [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `extension/expqueue/classes/expqueuehandler.php:27` | A job handler: the code that runs the jobs of one type. |
| `expQueueHandlerBase` | abstract class implements expQueueHandler | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/expqueue/classes/expqueuehandlerbase.php:12` | A base for handlers with the optional parts: validate() is called when a job is queued (a wrong payload is refused there and then, not at 3 a.m. |
| `expqueueInfo` | class | [Value object / struct](value-object.md) | `extension/expqueue/ezinfo.php:7` | – |
| `expQueueJob` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expqueue/classes/expqueuejob.php:25` | One job (table expqueue_job). |
| `expQueueLock` | class | [Utility (static helper)](utility.md) | `extension/expqueue/classes/expqueuelock.php:25` | The dispatcher lock and the queue's directory. |
| `expQueueNoopHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expqueue/classes/handlers/expqueuenoophandler.php:12` | expqueue.noop: does nothing and is done. |
| `expQueuePermanentException` | class extends expQueueException | [Exception hierarchy](exception.md) | `extension/expqueue/classes/expqueuepermanentexception.php:12` | Thrown by a handler for an error that another attempt cannot fix (invalid input, a refusal, a missing object): the job goes to the dead letter at onc… |
| `expQueueResource` | class | [Utility (static helper)](utility.md) | `extension/expqueue/classes/expqueueresource.php:22` | The concurrency limiter of a resource (table expqueue_resource): a key such as "anthropic:standard" or "imaging" that jobs name with the option resou… |
| `expQueueResult` | class | [Factory and static factory methods](factory.md) | `extension/expqueue/classes/expqueueresult.php:18` | What a handler's run() returns when an array (done) is not enough. |
| `expQueueRetryableException` | class extends expQueueException | [Exception hierarchy](exception.md) | `extension/expqueue/classes/expqueueretryableexception.php:13` | Thrown by a handler for an error that may go away: a timeout, a 429 or 5xx answer, a resource that is busy. |
| `expQueueScheduler` | class | – | `extension/expqueue/classes/expqueuescheduler.php:38` | Chooses the next job for a worker and leases it to that worker; keeps the queue moving (maintenance). |
| `expQueueSchema` | class | [Utility (static helper)](utility.md) | `extension/expqueue/classes/expqueueschema.php:28` | The tables of expqueue, defined once here in the kernel's generic schema format. |
| `expQueueService` | class | [Facade / service](facade.md), [Utility (static helper)](utility.md) | `extension/expqueue/classes/expqueueservice.php:22` | The systemd service of the queue's workers: ./console ext:expqueue:service install\|start\|stop\|status\|uninstall. |
| `expQueueSleepHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/expqueue/classes/handlers/expqueuesleephandler.php:16` | expqueue.sleep: waits, in steps, reporting progress and honouring a cancel between the steps. |
| `expQueueStats` | class | [Utility (static helper)](utility.md) | `extension/expqueue/classes/expqueuestats.php:15` | The figures of the queue for the dashboard (expqueue/dashboard) and ./console ext:expqueue:status: counts per state, per class and per resource, the… |
| `expQueueWorker` | class | – | `extension/expqueue/classes/expqueueworker.php:24` | Runs jobs one after another until a limit is reached: the loop of ./console ext:expqueue:work, of the cron part expqueue and of the systemd service. |

## Module views (5)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/expqueue/dashboard` | `dashboard.php` | view | `extension/expqueue/modules/expqueue/module.php:14` |
| `/expqueue/list` | `list.php` | view | `extension/expqueue/modules/expqueue/module.php:20` |
| `/expqueue/job` | `job.php` | view | `extension/expqueue/modules/expqueue/module.php:28` |
| `/expqueue/group` | `group.php` | view | `extension/expqueue/modules/expqueue/module.php:34` |
| `/expqueue/action` | `action.php` | manage | `extension/expqueue/modules/expqueue/module.php:41` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `expqueue/view` | – | `extension/expqueue/modules/expqueue/module.php:48` |
| `expqueue/manage` | – | `extension/expqueue/modules/expqueue/module.php:49` |

## Cronjob parts (1)

| Cronjob | File | Description |
|---|---|---|
| `expqueue` | `extension/expqueue/cronjobs/expqueue.php` | The cron part "expqueue" (php runcronjobs.php -q expqueue, every minute): the default worker of the queue and the safety net next to a systemd servic… |

## INI plugin points (5)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[expqueue]` | `expQueueAuditBranch` | `extension/expqueue/settings/audit.ini.append.php:5` |
| `[JobType_expqueue.noop] Handler` | `expQueueNoopHandler` | `extension/expqueue/settings/expqueue.ini.append.php:167` |
| `[JobType_expqueue.sleep] Handler` | `expQueueSleepHandler` | `extension/expqueue/settings/expqueue.ini.append.php:173` |
| `[JobType_expqueue.fail] Handler` | `expQueueFailHandler` | `extension/expqueue/settings/expqueue.ini.append.php:178` |
| `[Registry_expqueue_job_types] Entries[Handler]` | `expQueueHandler` | `extension/expqueue/settings/rad.ini.append.php:21` |
