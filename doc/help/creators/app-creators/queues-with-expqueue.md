---
title: Queues with expqueue
audience: app-creator, developer
category: app-creation
order: 420
summary: Run application work in the background with the expqueue job queue - switching it on, enqueueing jobs with options, writing handlers with retries and checkpoints, workers from cron or systemd, and the admin pages.
keywords: expqueue, job queue, background jobs, enqueue, handler, expQueueHandler, expQueueResult, retry, idempotency key, worker, cron, systemd, priority, app creator
type: help
---
# Queues with expqueue

This page is for application creators whose application has work that must not happen in a web request: calls to slow
services, bulk updates, sending, AI requests. `expqueue` is a database-backed job queue: producers enqueue a job with one
call; workers run it on the command line ([expqueue](../../../features/6.0/extensions/expqueue.md)). Its full
documentation is in `extension/expqueue/doc/` (`api.md`, `scheduling.md`, `workers-cron-systemd.md`).

## Contents

1. [Switching it on](#switching-it-on)
2. [Enqueueing a job](#enqueueing-a-job)
3. [Writing a handler](#writing-a-handler)
4. [Workers](#workers)
5. [Watching and controlling](#watching-and-controlling)
6. [Rules for handlers](#rules-for-handlers)

## Switching it on

1. Activate it globally: `[ExtensionSettings] ActiveExtensions[]=expqueue` in `settings/override/site.ini.append.php`.
2. Create the tables (idempotent): `./console ext:expqueue:install-schema`.
3. Regenerate autoloads and deploy.
4. A worker every minute:

   ```text
   * * * * * cd /path/to/installation && php runcronjobs.php -q expqueue >> var/log/cronjob-expqueue.log 2>&1
   ```

5. Check: `./console ext:expqueue:enqueue expqueue.noop`, `./console ext:expqueue:work --once`,
   `./console ext:expqueue:status`.

## Enqueueing a job

```php
$job = expQueue::enqueue( 'expdemo.archive', array( 'object_id' => $objectID ), array(
    'idempotency_key' => "archive:$objectID:$version",
    'priority' => 50 ) );
```

Options include `class` (priority class), `priority`, `group`, `idempotency_key`, `resource` (a concurrency key),
`not_before`, `max_attempts`, `timeout`, `depends_on`, `user_id`. The payload is stored as JSON and shown in the admin:
**never put a secret in it**; pass IDs and let the handler read credentials from settings.

## Writing a handler

Register the type:

```ini
# extension/expdemo/settings/expqueue.ini.append.php
[JobType_expdemo.archive]
Handler=expDemoArchiveHandler
MaxAttempts=4
Timeout=300
Description=Sends one published object to the archive
```

Implement `expQueueHandler::run( expQueueJob $job, expQueueContext $context )` or extend `expQueueHandlerBase`
(which adds `validate()` at enqueue and `describe()` for the admin). `run()` returns nothing or an array (done), or an
`expQueueResult`: `done()`, `retry( $message, $retryAfter )`, `fail()`, `defer()`, `hold()`, `cancelled()`. Other
exceptions count as retryable.

```php
class expDemoArchiveHandler extends expQueueHandlerBase
{
    public function run( expQueueJob $job, expQueueContext $context )
    {
        $payload = $context->payload();
        $object = eZContentObject::fetch( (int)$payload['object_id'] );
        if ( !$object )
            return expQueueResult::fail( 'The object is gone' );
        expDemoArchiveClient::send( $object );
        return array( 'sent' => true );
    }
}
```

## Workers

Workers run from cron (`runcronjobs.php expqueue`) or as a systemd service (`./console ext:expqueue:service install`) for
shorter waits, never inside a web request. If your job types are active only in some siteaccesses, give the worker
`-s <siteaccess>`.

## Watching and controlling

**Setup > Job queue** shows the dashboard, jobs, groups and controls; policies `expqueue/view` and `expqueue/manage`.
Jobs can be cancelled, retried, paused per queue, class, resource or group.

## Rules for handlers

- Idempotent: a job can run again after a crash or a retry; use the checkpoint for steps that cost money.
- No database transaction around a slow call.
- `progress()` or `heartbeat()` at least every half timeout in long work.
- The job runs as the user who queued it; check permissions as that user.

## Related pages

- [Background work: queues, cronjobs and content jobs](background-work-patterns.md)
- [Content jobs](content-jobs.md)
- [Exponential Intelligence for applications](exponential-intelligence.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
