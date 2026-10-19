---
title: Triggers and workflows for developers
audience: developer, app-creator
category: extension-development
order: 190
summary: How operations, triggers and workflows connect: operation definitions, making more operations triggerable, running an operation from PHP, and what happens to deferred processes.
keywords: trigger, operation, operation_definition.php, eZOperationHandler, execute, AvailableOperationList, content_publish, pre_publish, post_publish, workflow process, workflow cronjob, memento
type: help
---
# Triggers and workflows for developers

Part 3, chapter 19. Workflow events run because an **operation** of the kernel passes a **trigger**. This chapter
explains that machinery from the developer's side: which operations exist, how to make more of them triggerable, and
how to run an operation yourself so that the workflows the builders configured run too.

## Operations

An operation is a named sequence of steps that the kernel runs through `eZOperationHandler`
(`lib/ezutils/classes/ezoperationhandler.php`). Each module that has operations defines them in
`kernel/<module>/operation_definition.php`:

| Module | Operations |
|---|---|
| `content` | `publish`, `read`, `hide`, `move`, `swap`, `sort`, `delete`, `addlocation`, `removelocation`, `updatemainassignment`, `updatepriority`, `updatesection`, `updateobjectstate`, `updateinitiallanguage`, `updatealwaysavailable`, `removetranslation`, `translationavailable`, `createnodefeed`, `removenodefeed` |
| `shop` | `addtobasket`, `updatebasket`, `checkout`, `confirmorder` |
| `user` | `register`, `activation`, `password`, `forgotpassword`, `preferences`, `setsettings` |

## Triggers

Each operation can have a trigger **before** and **after** it (internally `pre_<operation>` and `post_<operation>`).
A builder connects a workflow to a trigger in **Setup > Triggers**. Which operations appear there is
`workflow.ini [OperationSettings] AvailableOperationList[]`; the shipped list holds `content_publish` and the shop
operations, and the file lists the others, commented out. To offer, for example, workflows around moving content:

```ini
# extension/mysite_hello/settings/workflow.ini.append.php
[OperationSettings]
AvailableOperationList[]=content_move
```

The kernel's comment on that list warns that the code behind the extra operations "may be changed if needed" without
the usual compatibility rules; test after every upgrade.

## Running an operation from PHP

When your code does something that has an operation, call the operation instead of the low-level methods, so that
triggers, workflows, caches and notifications all run:

```php
<?php
$result = eZOperationHandler::execute( 'content', 'publish',
                                       array( 'object_id' => $objectID, 'version' => $versionNumber ) );
```

The signature is `execute( $moduleName, $operationName, $operationParameters, $lastTriggerName = null, $useTriggers = true )`.
`$result['status']` tells you whether the operation finished (`eZModuleOperationInfo::STATUS_CONTINUE`), is held by a
workflow, or was cancelled. For creating content, `eZContentFunctions::createAndPublishObject()` does the whole job
(see Creating, publishing and changing content from PHP).

## What happens when an event waits

When an event returns `STATUS_DEFERRED_TO_CRON`, the kernel stores the process and a **memento** of the held-back
operation. The workflow cronjob (`cronjobs/workflow.php`, part `frequent`) runs the event again; when it finally
returns `STATUS_ACCEPTED`, the cronjob finishes the operation from the memento. The administration shows waiting
processes in **Setup > Workflow processes**. The details are in [Workflows](../../../guides/workflows.md).

So: an event that defers must be safe to run many times, and must decide only from stored state, never from the
request that started it.

## Workflows without the administration

An installation script can create workflows and triggers with the kernel's classes (`eZWorkflow`, `eZWorkflowEvent`,
`eZTrigger`), but configuring them through the administration and exporting them in a package is usually easier to
maintain. Prefer documenting the two clicks a builder has to make.

## Related pages

- Previous: [Workflow event types](workflow-event-types.md)
- Next: [Cronjob parts](cronjob-parts.md)
- [Workflows for builders](../part-2-builder/workflows-for-builders.md)
- [Events, listeners and filters](events-and-filters.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
