---
title: Approvals before release
audience: podcaster
category: podcasting-streaming
order: 30
summary: How a show makes episodes wait for an editor's or a legal review before they go out - the Approve workflow event and its fields, combining approval with a scheduled release, what producers and approvers see, and lighter alternatives.
keywords: approval, approve, review, sign-off, legal review, editor in chief, workflow, ezapprove, pending, collaboration, trigger, multiplexer, scheduled release
type: help
---
# Approvals before release

This page is for shows where someone must sign off an episode before listeners get it: an editor in chief, a legal
review for a sensitive interview, a sponsor check. Exponential does this with the **Approve** workflow event
(`kernel/classes/workflowtypes/event/ezapprove/`), set up by an administrator.

## Contents

1. [How approval works](#how-approval-works)
2. [Setting it up for a show](#setting-it-up-for-a-show)
3. [Approval and a release date together](#approval-and-a-release-date-together)
4. [What producers and approvers do](#what-producers-and-approvers-do)
5. [What approval does not cover](#what-approval-does-not-cover)
6. [Lighter alternatives](#lighter-alternatives)

## How approval works

When a producer presses **Send for publishing**, the workflow on the trigger "before publishing content" runs. The
Approve event holds the version as **pending** until an approver accepts it; then it is published. If the approver
denies it, it becomes a draft for the author again. The workflow cronjob must run for this to work
([Workflows](../../guides/workflows.md#3-worked-example-approval-before-publishing)).

## Setting it up for a show

The event's fields, from the workflows guide:

| Field | For a show |
|---|---|
| **Affected sections** | the section your show's content is in; give shows their own section if only they need approval |
| **Affected languages** | usually all |
| **Affected versions** | *All versions* to review every change, or *Publishing new object* to review only new episodes |
| **Users who approve content**, **Groups who approve content** | the editor in chief, or a group of reviewers |
| **Excluded user groups** | people who may publish without review |

An approver never needs to approve their own content: when the publishing user is one of the approvers, the event lets
the version through.

## Approval and a release date together

A trigger takes **one** workflow ([Workflows](../../guides/workflows.md#33-connect-it-with-a-trigger)). To have both an
approval and a scheduled release, put both events into one workflow, in the order you want:

1. **Approve**: the episode waits for the approver.
2. **Wait until date**: after approval, it waits for its release date ([Scheduling a release](scheduling-a-release.md)).

In this order, an episode approved after its release date goes out at the next cron run. To run separate workflows,
the guide describes the *Multiplexer* event.

## What producers and approvers do

**Producer**

1. Finish the episode and press **Send for publishing**.
2. The version is in **My pending items** ([My pending items](../publishing-and-versions.md#my-pending-items)).
3. If it is denied, the version is a draft for you again, with the approver's comment. Edit it and send it again.

**Approver**

1. Open **My account > Collaboration** (or **My pending items**): the approval requests are listed there.
2. Open the request, look at the preview, listen to the audio and read the notes.
3. Press **Approve**, or **Deny** with a comment for the author.

After an approval, the episode is published at the next run of the workflow cronjob
([Workflows](../../guides/workflows.md#35-try-it)).

The customer section describes a daily routine for approvers ([Approvals and review](../customers/day-to-day/approvals-and-review.md#a-daily-routine-for-approvers)).

## What approval does not cover

- **Changes outside content.** The feed's channel fields, the artwork address and layouts are not content versions;
  approval does not hold them. Limit who may edit RSS exports instead (the `rss/edit` policy).
- **Embedded players.** An approved page with an External video block shows what the service serves at the time.
- **Hiding and removing.** These are not publishing; the Approve event does not stop them.

## Lighter alternatives

| Need | Alternative |
|---|---|
| A second pair of eyes, no hard gate | the producer stores the draft and asks a colleague to review it before publishing |
| Visible stages without blocking | object states such as *ready for review* ([Production stages with object states](production-stages-with-object-states.md)) |
| Only some people may change the show at all | roles that limit `content/create` and `content/edit` to the show's subtree or section for its team ([Roles and policies](../../guides/roles-and-policies.md)) |

## Related pages

- [Approvals and review](../customers/day-to-day/approvals-and-review.md)
- [Workflows](../../guides/workflows.md)
- [A release calendar](a-release-calendar.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
