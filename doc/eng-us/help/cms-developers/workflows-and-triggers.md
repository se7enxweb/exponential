---
title: Workflows and triggers
audience: cms-developer
category: cms-development
order: 33
summary: For CMS developers, how to run logic when content is published, a user registers or an order is placed. It covers workflows, events and triggers, the shipped event types, approval before publishing, the workflow cronjob, event listeners as the lighter alternative, and workflow event types of your own.
keywords: workflows, triggers, workflow events, approve, approval, wait until date, multiplexer, payment gateway, finish user register, content/publish, pre_publish, post_publish, workflow cronjob, workflow process, event listener, ezpEvent, custom workflow event type, hooks
type: help
---
# Workflows and triggers

This page is for developers who need "when X happens, do Y": approval before publishing, a mail after a
registration, a sync to another system after an update. Exponential has two mechanisms for this: **workflows**, which
editors and administrators configure in the administration and which can hold an operation back, and **event
listeners**, which your code registers in settings.

## Contents

1. [Workflows, events and triggers](#workflows-events-and-triggers)
2. [The shipped event types](#the-shipped-event-types)
3. [Approval before publishing](#approval-before-publishing)
4. [Processes and the workflow cronjob](#processes-and-the-workflow-cronjob)
5. [Event listeners: the lighter way](#event-listeners-the-lighter-way)
6. [A workflow event type of your own](#a-workflow-event-type-of-your-own)
7. [Choosing](#choosing)
8. [Pitfalls](#pitfalls)

## Workflows, events and triggers

| Term | What it is | Where |
|---|---|---|
| Workflow | a named, ordered list of events, in a workflow group | **Setup** > **Workflows** (`/workflow/grouplist`) |
| Event | one step of a workflow, of an event type | the workflow's edit page |
| Trigger | the link between a workflow and an operation, before or after it (`pre_publish`, `post_publish` ...) | **Setup** > **Triggers** (`/trigger/list`) |
| Process | one unfinished run of a workflow for one object and version | **Setup** > **Workflow processes** (`/workflow/processlist`) |

The operations offered for triggers are `workflow.ini [OperationSettings] AvailableOperationList[]`: shipped are
`content_publish` and the shop operations (`before_shop_confirmorder`, `shop_checkout`, `shop_addtobasket`,
`shop_updatebasket`). More (moving, hiding, removing, reading content, user activation and others) are listed there as
comments and can be switched on in your settings, with the note that their code may change
([Workflows, section 1](../../guides/workflows.md#1-workflows-events-triggers-and-processes)).

## The shipped event types

`settings/workflow.ini [EventSettings] AvailableEventTypes[]` (`kernel/classes/workflowtypes/event/`):

| Event type | Does |
|---|---|
| Approve (`ezapprove`) | holds publishing until an approver decides in the collaboration inbox |
| Wait until date (`ezwaituntildate`) | holds publishing until a date attribute's value is reached |
| Multiplexer (`ezmultiplexer`) | runs another workflow only for some classes, sections or users |
| Payment gateway (`ezpaymentgateway`) | hands an order to a payment gateway |
| Simple shipping (`ezsimpleshipping`) | adds a shipping cost to an order |
| Finish user register (`ezfinishuserregister`) | completes a registration held by a workflow |

Extensions add more.

## Approval before publishing

1. Create a workflow with an **Approve** event: the approvers (users or a group), the sections and languages it
   applies to, and who it does not apply to (for example the approvers themselves).
2. Wrap it in a **Multiplexer** if only some classes need approval.
3. Connect it to the trigger `content` / `publish` / *before*.
4. Make sure the workflow cronjob runs.

The editor's version becomes *pending*; an approver approves or denies in the collaboration inbox; the next run of
the workflow cronjob publishes or returns the draft. The worked example with every screen:
[Approval before publishing](../../guides/workflows.md#3-worked-example-approval-before-publishing).

## Processes and the workflow cronjob

A held operation is stored as a process with a memento of the operation; `workflow.php` in the cronjob parts resumes
waiting processes. **Without cron, approved content is never published** ([Cronjobs](cronjobs.md),
[The workflow cronjob](../../guides/workflows.md#6-the-workflow-cronjob-how-processes-resume)). Processes that wait
for a removed approver need cleaning up ([Cancelling and cleaning up processes](../../guides/workflows.md#7-cancelling-and-cleaning-up-processes-safely)).

## Event listeners: the lighter way

When your code only needs to react (log, notify, sync) and never to hold the operation, an event listener is simpler:
a callback registered in `site.ini [Event] Listeners[]` for an event the kernel announces, such as `content/state/assign`
(states changed) or `content/cache` (the caches of nodes were cleared, which publishing does). There is no dedicated
"after publish" event; for that use a workflow event on `post_publish`. Filter events let a listener change a value and
must return it
([Events, listeners and filters](../extension-development/part-3-extension-developer/events-and-filters.md)).

## A workflow event type of your own

When editors must be able to add your step to their workflows, or the step must hold the operation, write a workflow
event type: a class extending `eZWorkflowEventType` with an `execute()` method that answers accepted, rejected, or
"run again later", registered in your extension's `workflow.ini.append.php`
([Workflow event types](../extension-development/part-3-extension-developer/workflow-event-types.md),
[A workflow event by hand](../extension-development/part-3-extension-developer/tutorials/by-hand-workflow-event.md),
[The eZWorkflowType API](../extension-development/part-8-reference/ezworkflowtype-api.md)).

## Choosing

| Need | Use |
|---|---|
| An editorial approval | the Approve event, configured by an administrator |
| Publishing at a date | Wait until date, or the `hide.php` and `unpublish.php` cronjobs for hiding and removal ([Versions, drafts and publishing](versions-drafts-and-publishing.md#publishing-and-hiding-on-a-date)) |
| Notify another system after publishing | a workflow event on `post_publish` |
| Stop a publish that breaks a rule of yours | a workflow event type on `pre_publish` that rejects |
| Stages with their own permissions | object states, changed by editors or by a workflow event ([Object states](../../guides/object-states.md)) |

## Pitfalls

- **Slow events slow publishing.** A call to another system in `post_publish` makes the editor wait; queue it and let
  a cronjob or a content job do the work.
- **Loops.** An event that publishes content triggers the publish workflows again; guard against it.
- **Testing as administrator.** Many approval setups exclude administrators; test with an editor account at
  `example.invalid`.
- **Forgetting cron on a new server.** Check the workflow cronjob is scheduled before going live.

## Related pages

- [Workflows](../../guides/workflows.md): the complete guide
- [Workflows for builders](../extension-development/part-2-builder/workflows-for-builders.md)
- [Triggers and workflows for developers](../extension-development/part-3-extension-developer/triggers-and-workflows-for-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
