---
title: Workflows for builders
audience: developer, designer, app-creator, admin
category: extension-development
order: 190
summary: Make something happen around publishing: workflows, events, triggers, processes and the workflow cronjob, explained for builders, with an approval workflow and the event types that need no code.
keywords: workflow, event, trigger, approve, approval, multiplexer, wait until date, workflow process, workflow cronjob, content/publish, pre_publish, post_publish
type: help
---
# Workflows for builders

Part 2, chapter 19. A **workflow** runs steps around an operation of the system, most often publishing: "send every
article to an editor for approval before it is published", "publish this at a date". Builders set workflows up in the
administration; developers add new kinds of steps (Workflow event types).
The complete guide is [Workflows](../../../guides/workflows.md); this chapter is the builder's summary.

## The pieces

| Piece | What it is | Where |
|---|---|---|
| Workflow | an ordered list of events | Setup > Workflows (`/workflow/grouplist`) |
| Event | one step, of an event type | the workflow's edit page |
| Trigger | connects a workflow to an operation, before or after it (`pre_publish`, `post_publish`) | Setup > Triggers (`/trigger/list`) |
| Process | one run of a workflow for one object and version that has not finished | Setup > Workflow processes (`/workflow/processlist`) |
| Workflow cronjob | resumes processes that wait | `cronjobs/workflow.php` |

## Event types that need no code

The kernel ships these (`workflow.ini [EventSettings] AvailableEventTypes[]`):

| Type | Does |
|---|---|
| Approve | holds publishing until an approver approves |
| Wait until date | holds publishing until a date in an attribute |
| Multiplexer | runs another workflow only for some sections, classes or users |
| Payment gateway | holds an order until payment (shop) |
| Simple shipping | adds shipping cost to an order (shop) |
| Finish user register | completes a user registration |

Extensions add more; `swark`, for example, ships event types in `eventtypes/event/`.

## Worked plan: approval before publishing

1. Setup > Workflows: create a workflow "Approve articles" with one event **Approve**, choosing the approvers (a user
   group), and the sections, languages or classes it applies to.
2. Setup > Triggers: connect the workflow to `content`, `publish`, **before**.
3. Make sure the workflow cronjob runs (`cronjob.ini`, part `frequent`):

   ```bash
   php runcronjobs.php -s <siteaccess> --script=workflow.php
   ```

4. Try it: an editor publishes an article; it waits as pending; the approver approves it in their collaboration
   inbox; the next run of the workflow cronjob publishes it.

The guide walks through each screen: [Workflows](../../../guides/workflows.md).

## Using a multiplexer to target

A trigger runs one workflow for **all** publishing. To approve only articles in one section, make the triggered
workflow a **Multiplexer** that starts "Approve articles" only for the class Article and the section you choose.

## When a builder needs a developer

| You need | Use |
|---|---|
| A step the list does not have (notify an external system, set a field, move content) | a custom workflow event type (With RAD: a workflow event) |
| Something on publish that never waits and never asks | often simpler as an event listener (Events, listeners and filters) |

## Troubleshooting

| Symptom | Check |
|---|---|
| Articles stay pending forever | does the workflow cronjob run? Setup > Workflow processes lists waiting processes |
| The approver gets nothing | the approvers chosen in the event; their collaboration inbox |
| The workflow runs for everything | the trigger has no filter; use a multiplexer |

## Related pages

- Previous: [Sections, roles and access for builders](sections-roles-and-access-for-builders.md)
- Next: [Cronjobs for builders](cronjobs-for-builders.md)
- [Workflows](../../../guides/workflows.md)
- Triggers and workflows for developers
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
