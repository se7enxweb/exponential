---
title: Editorial calendar
audience: government, editor, project-manager
category: public-sector
order: 120
summary: Planning publication in a public body - an editorial calendar is a practice, not an Exponential module; this page shows which dates the system can act on (Wait until date, hide and unpublish dates) and how a calendar, approvals and cronjobs fit together.
keywords: editorial calendar, content calendar, publication plan, scheduled publishing, Wait until date, embargo, press release, hide date, campaign, planning, communications
type: help
---
# Editorial calendar

This page is for communications teams and web managers who plan publication: press releases, campaign pages,
consultations, annual reports. An editorial calendar is a **practice**: Exponential has no calendar view or planning
module. What the system can do is act on dates stored with the content. Sources: the workflow event types in
`kernel/classes/workflowtypes/event/`, `settings/content.ini` and [Workflows](../../../../guides/workflows.md).

## Contents
1. [What the system can schedule](#what-the-system-can-schedule)
2. [Publishing at a date: Wait until date](#publishing-at-a-date-wait-until-date)
3. [Ending at a date: hide and unpublish](#ending-at-a-date-hide-and-unpublish)
4. [Running the calendar](#running-the-calendar)
5. [Related pages](#related-pages)

## What the system can schedule

| Date | Mechanism | Needs |
|---|---|---|
| Go live at a date | the **Wait until date** workflow event | a date attribute on the class; the event in the publishing workflow; the workflow cronjob |
| Hide at a date | the `hide.php` cronjob | a date attribute named in `[HideSettings] HideDateAttributeList[]`, root nodes in `RootNodeList[]` |
| Remove at a date | the `unpublish.php` cronjob | an `unpublish_date` attribute, `[UnpublishSettings] ClassList[]` and `RootNodeList[]` |

## Publishing at a date: Wait until date

The event type *Wait until date* (`ezwaituntildate`) holds a version until a date stored in the object:

- In the event you choose which class attributes (of date, time or date and time type) it reads.
- When the date is in the future, the event answers "Event delayed until ..." and the process waits for the
  workflow cronjob, which tries again on each run; once the date has passed, the version goes on. An option of the
  event sets the object's published date to that date.
- Put it in the workflow on **Before publishing content**, together with the Approve event if approval is needed
  (approve first, then wait).
- The timing is as precise as the schedule of the workflow cronjob (every 5 minutes in the usual crontab).

Embargoed content (for example statistics released at a fixed hour) waits as a pending version and is not public
before the date; it is still visible to staff who may read pending versions. Check this against your embargo rules.

## Ending at a date: hide and unpublish

Both cronjobs are listed in the global part of `settings/cronjob.ini` and act only on the root nodes and classes you
configure. Hiding can be undone; unpublishing removes the content. See
[Review dates with object states](review-dates-with-object-states.md#pattern-hide-at-a-date) for the settings.

## Running the calendar

A practical setup for a public body:

| Item | Where it lives |
|---|---|
| The plan (what, when, who writes, who approves) | your planning tool or a shared calendar |
| The draft | **My drafts** of the author |
| The approval | the approval workflow; approvers see it in their inbox |
| The go-live date | the date attribute read by *Wait until date* |
| The end date | the hide date attribute |
| Confirmation it went live | the audit trail's `content` channel; a check of the page |

Weekly: compare the plan with **Setup > Workflow processes** (what is waiting and why) and with what went live.
Before a fixed-time publication, run the frequent cronjob part by hand right after the time if minutes matter
([Run a cronjob part by hand](../../tasks/operations/run-a-cronjob-part-by-hand.md)), and clear caches if pages
show the old state ([Stale cache](../incidents/stale-cache.md)).

## Related pages
- [Approval workflows overview](approval-workflows-overview.md)
- [Style guide governance](style-guide-governance.md)
- [Cronjobs guide](../../../../guides/cronjobs.md)
- [Workflows guide](../../../../guides/workflows.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
