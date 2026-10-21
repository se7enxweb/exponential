---
title: Queues and background jobs
audience: web-framework-developer, developer
category: web-frameworks
order: 320
summary: Background work in Exponential for developers used to Symfony Messenger, Laravel queues, Celery, Sidekiq or Spring's @Async - the expqueue job queue with handlers, retries, idempotency keys and workers, content jobs for large content operations, cronjob parts and deferred workflow events, and a decision table for which to use.
keywords: queue, jobs, background jobs, workers, expqueue, expQueue::enqueue, expQueueHandlerBase, expQueueResult, retry, idempotency key, content jobs, Messenger, Laravel queues, Celery, Sidekiq, Active Job, web framework developer
type: help
---
# Queues and background jobs

This page is for developers who dispatch messages or jobs to a queue (Symfony Messenger, Laravel queues, Celery,
Sidekiq, Active Job, Spring `@Async`). Exponential's job queue is the **`expqueue`** extension: database-backed, with
handlers, retries, priorities and workers on the command line. Two more mechanisms cover kernel work: **content jobs**
for very large content operations and **workflow events deferred to cron**
([Background work: queues, cronjobs and content jobs](../creators/app-creators/background-work-patterns.md)).

## Contents

1. [Which mechanism](#which-mechanism)
2. [expqueue in five steps](#expqueue-in-five-steps)
3. [A handler](#a-handler)
4. [Content jobs](#content-jobs)
5. [Rules for jobs](#rules-for-jobs)
6. [Compared with other frameworks](#compared-with-other-frameworks)

## Which mechanism

| The work | Place |
|---|---|
| Call an external service after each publish | a workflow event or listener enqueues an expqueue job |
| Send a reminder mail tomorrow at 9 | an expqueue job with `not_before` |
| Import a feed every hour | a cronjob part ([Scheduled tasks](scheduled-tasks-and-cronjobs.md)) |
| Hold publishing until someone approves | a workflow event deferred to cron ([Workflows](workflows-triggers-and-domain-events.md)) |
| Remove or move a subtree of 50 000 nodes | a content job |

## expqueue in five steps

From [Queues with expqueue](../creators/app-creators/queues-with-expqueue.md):

1. Activate it: `[ExtensionSettings] ActiveExtensions[]=expqueue` in `settings/override/site.ini.append.php`.
2. Create its tables (idempotent): `./console ext:expqueue:install-schema`.
3. Regenerate autoloads and deploy.
4. Run a worker every minute from cron (`php runcronjobs.php -q expqueue`), or as a systemd service
   (`./console ext:expqueue:service install`) for shorter waits.
5. Check: `./console ext:expqueue:enqueue expqueue.noop`, `./console ext:expqueue:work --once`,
   `./console ext:expqueue:status`.

Enqueue from any code:

```php
$job = expQueue::enqueue( 'expdemo.archive', array( 'object_id' => $objectID ), array(
    'idempotency_key' => "archive:$objectID:$version",
    'priority' => 50 ) );
```

Options include `class`, `priority`, `group`, `idempotency_key`, `resource` (a concurrency key), `not_before`,
`max_attempts`, `timeout`, `depends_on` and `user_id`. The payload is stored as JSON and shown in the administration:
never put a secret in it.

## A handler

```ini
# extension/expdemo/settings/expqueue.ini.append.php
[JobType_expdemo.archive]
Handler=expDemoArchiveHandler
MaxAttempts=4
Timeout=300
Description=Sends one published object to the archive
```

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

`run()` returns nothing or an array (done), or an `expQueueResult`: `done()`, `retry( $message, $retryAfter )`,
`fail()`, `defer()`, `hold()`, `cancelled()`; other exceptions count as retryable. **Setup > Job queue** shows the
dashboard and lets administrators cancel, retry and pause; the policies are `expqueue/view` and `expqueue/manage`
([expqueue](../../features/6.0/extensions/expqueue.md)).

## Content jobs

Removing, copying, moving, hiding or revealing large subtrees, changing sections or states and adding or removing
locations can run as **content jobs** in batches, each batch its own transaction, started from the administration or
the command line and watched with progress ([Content jobs](../../features/6.0/content-jobs.md),
[Content jobs for applications](../creators/app-creators/content-jobs.md)).

## Rules for jobs

- Idempotent: a job can run again after a crash or a retry.
- No database transaction around a slow call.
- A job runs as the user who queued it; check permissions as that user.
- Workers run on the command line, never inside a web request.

## Compared with other frameworks

| Framework | Concept | Exponential |
|---|---|---|
| Symfony Messenger | messages, handlers, transports | job types, handlers, a database transport |
| Laravel | jobs, `dispatch()`, Horizon | `expQueue::enqueue()`, **Setup > Job queue** |
| Celery, Sidekiq | brokers (Redis, RabbitMQ), workers | the database; workers from cron or systemd |
| Spring | `@Async`, task executors | no in-process async; queue the work |

There is no broker to run: the queue lives in the installation's database, which keeps installations simple at the
cost of throughput compared with a dedicated broker.

## Related pages

- [Background work: queues, cronjobs and content jobs](../creators/app-creators/background-work-patterns.md)
- [Exponential Intelligence for applications](../creators/app-creators/exponential-intelligence.md)
- [Events and listeners](events-and-listeners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
