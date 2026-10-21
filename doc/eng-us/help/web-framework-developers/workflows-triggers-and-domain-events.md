---
title: "Workflows and triggers: hooks on content operations"
audience: web-framework-developer, developer
category: web-frameworks
order: 230
summary: Where a framework developer writes model observers, post-save signals, state machines or process managers, Exponential offers workflows - event types run before or after operations such as publish and checkout, attached by triggers, able to accept, reject, wait for cron, show a page or redirect - how they work, how to write an event type, and when to use them.
keywords: workflow, trigger, event type, eZWorkflowEventType, operation, content_publish, before, after, deferred to cron, approval, state machine, observers, signals, domain events, Symfony Workflow, process manager, web framework developer
type: help
---
# Workflows and triggers: hooks on content operations

This page is for developers who would hook business logic into an ORM's save events, write a Django `post_save`
signal, or model an approval process with a state machine library. In Exponential, **operations** such as publishing
content or confirming an order can start a **workflow** **before** or **after** they run. A workflow is a list of
**event types** (PHP classes), connected to an operation by a **trigger**, and it can hold the operation back until
something happens: an approval, a payment, a date ([Workflows, triggers and events](../creators/app-creators/workflows-and-events.md)).

## Contents

1. [The pieces](#the-pieces)
2. [Operations you can hook](#operations-you-can-hook)
3. [What an event can answer](#what-an-event-can-answer)
4. [Writing an event type](#writing-an-event-type)
5. [Compared with other frameworks](#compared-with-other-frameworks)
6. [When to use what](#when-to-use-what)

## The pieces

| Term | What |
|---|---|
| Workflow | a named list of events, in a workflow group (**Setup > Workflows**) |
| Event type | a class that does one step: approve, wait until date, multiplexer, payment gateway, and those of extensions |
| Trigger | connects a workflow to an operation, `before` or `after` it (**Setup > Triggers**) |
| Process | one run of a workflow for one object and version, kept in the database while it waits |

Workflows are configured by administrators in the browser; your extension provides the event types.

## Operations you can hook

`settings/workflow.ini [OperationSettings] AvailableOperationList[]` lists the operations that offer triggers. Shipped
active: `content_publish`, `before_shop_confirmorder`, `shop_checkout`, `shop_addtobasket`, `shop_updatebasket`.
Others are listed commented out (`content_move`, `content_hide`, `content_delete`, `content_updatesection`,
`content_read`, `content_updateobjectstate`, `user_activation`, `user_password` ...); an installation enables them in an
override. Your own persistent object classes have no operations: emit an event of your own instead
([Events and listeners](events-and-listeners.md)).

## What an event can answer

`execute()` returns a status constant of `eZWorkflowType`:

| Status | Effect |
|---|---|
| `STATUS_ACCEPTED` | go on with the next event, then the operation |
| `STATUS_REJECTED` | stop the operation |
| `STATUS_DEFERRED_TO_CRON` | wait; the `workflow.php` cronjob (part `frequent`) runs the process again |
| `STATUS_FETCH_TEMPLATE` | show a page to the user (a form, a confirmation) |
| `STATUS_REDIRECT` | send the user elsewhere (a payment provider) |
| `STATUS_WORKFLOW_CANCELLED` | cancel the workflow |

Waiting processes are listed in **Setup > Workflow processes**. Without the `frequent` cron part, content waiting for
a deferred event never publishes ([Scheduled tasks](scheduled-tasks-and-cronjobs.md)).

## Writing an event type

From [Writing a workflow event type](../creators/app-creators/writing-a-workflow-event-type.md):

```ini
# extension/expdemo/settings/workflow.ini.append.php
<?php /* #?ini charset="utf-8"?
[EventSettings]
ExtensionDirectories[]=expdemo
AvailableEventTypes[]=event_expnotify
*/ ?>
```

```php
<?php
// extension/expdemo/eventtypes/event/expnotify/expnotifytype.php
class expNotifyType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'expnotify';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/expdemo', 'Notify the archive' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $object = eZContentObject::fetch( (int)$parameters['object_id'] );
        if ( !$object )
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
        // queue slow work instead of doing it here
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( expNotifyType::WORKFLOW_TYPE_STRING, 'expNotifyType' );
```

## Compared with other frameworks

| Framework idea | Exponential |
|---|---|
| Doctrine lifecycle events, Eloquent observers, Django `post_save`, Rails callbacks | an `after` trigger on `content_publish` |
| Validation before save that can veto | a `before` trigger whose event rejects |
| Symfony Workflow, state machine gems | object states for the status, workflows for the steps ([Object states](../../guides/object-states.md)) |
| Approval queues | the shipped approve event type with collaboration items |
| Process managers, sagas | a workflow whose events defer to cron and resume |
| Queued listeners | an event that enqueues a job ([Queues](queues-and-background-jobs.md)) |

## When to use what

| Use a workflow when | Use a kernel event listener when |
|---|---|
| administrators should switch it on and off, or order steps | the behavior is always part of your application |
| the step may wait (approval, payment, a date) | it must run synchronously and never wait |
| it is tied to one of the trigger operations | the event has no trigger |

## Related pages

- [Workflows guide](../../guides/workflows.md)
- [Workflow event pattern](../../standards/patterns/workflow-event.md)
- [Content jobs](../../features/6.0/content-jobs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
