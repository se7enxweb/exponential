---
title: "Workflows and triggers"
audience: admin, builder, developer
category: books
order: 300
summary: What a workflow, an event, a trigger and the multiplexer are, which event types ship today, and where to build a first approval workflow.
keywords: workflow, workflow event, trigger, multiplexer, approve, wait until date, content publish, cronjob, Learning book
type: help
---
# Workflows and triggers

A **workflow** does something in the background, without a user, or asks a user to do something, such as approving an
object before it is published. The system gives the building blocks for business procedures; you assemble them in the
administration, and a developer can add event types of their own in PHP.

## Triggers

A **trigger** starts the chain: it is a point in a kernel operation, "before publish", "after publish", and some shop
steps (such as checkout). For each trigger you choose the workflow to run. The list is in **Setup > Triggers** (the
**Advanced** part in the book's menu).

The book points out that a trigger holds one workflow, but workflows can be parallel. The **multiplexer** event is a hub:
it starts other workflows by the content class and the section, and has an exclude list of users and groups. Sections
once more.

## Events

A workflow is a list of **events**. The event types the book names were four (multiplexer, approve, wait until date,
and another). The installation's list now is in `settings/workflow.ini` (`AvailableEventTypes[]`) and holds:

| Event type | What it does |
|---|---|
| Approve (`event_ezapprove`) | Holds the object until a user approves it, with notification to the approvers. |
| Wait until date (`event_ezwaituntildate`) | Holds the object until a date attribute is reached. |
| Multiplexer (`event_ezmultiplexer`) | Runs a chosen workflow by class, section and user. |
| Simple shipping, Payment gateway | Shop checkout steps. |
| Finish user register (`event_ezfinishuserregister`) | Completes the registration of a user. |

Extensions add more (look in the list in the workflow editor).

## Cronjob and processes

A workflow that waits is a **process**; the cronjob that handles workflows picks it up when the waited-for thing happens.
If the cron is not running, the object stays pending. That is why the book puts the cron job in chapter 1
([Installing, then and now](installing-then-and-now.md)). Pending objects show on the
[pending items](../manual/where-do-i-see-my-pending-items.md) page.

## Where to do it

[Workflows: approvals, triggers and the processes that wait](../../guides/workflows.md) is the worked example (create the
workflow, set up the Approve event, connect the trigger, make sure the cronjob runs, try it).
[Workflows for builders](../extension-development/part-2-builder/workflows-for-builders.md) is the short version.

Next: [more functions of the administration](more-administrator-functions.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
