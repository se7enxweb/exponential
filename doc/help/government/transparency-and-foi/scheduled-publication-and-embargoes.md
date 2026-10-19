---
title: Scheduled publication and embargoes
audience: government, admin, editor
category: public-sector
order: 170
summary: How to publish transparency content at a set time with the Wait until date workflow event, how to hide content after a date with the hide cronjob, why the unpublish cronjob removes content and should not be used for records, and how precise the timing is.
keywords: scheduled publishing, embargo, publish at, wait until date, workflow, hide.php, HideSettings, unpublish.php, UnpublishSettings, cronjob, release time, statistics release
type: help
---
# Scheduled publication and embargoes

This page is for administrators and editors who must publish information at a set time (a statistics release, an
agenda deadline) or take it off public view after a date. Exponential does both with cronjobs: a workflow event for
publishing, and a hide cronjob for the end date.

## Contents

1. [Publishing at a set time](#publishing-at-a-set-time)
2. [Setting it up](#setting-it-up)
3. [How precise it is](#how-precise-it-is)
4. [Hiding after a date](#hiding-after-a-date)
5. [Do not use the unpublish cronjob for records](#do-not-use-the-unpublish-cronjob-for-records)
6. [Embargoed drafts](#embargoed-drafts)

## Publishing at a set time

The workflow event *Wait until date* holds a version until the date in a date or date-and-time field of the object
has passed (`kernel/classes/workflowtypes/event/ezwaituntildate/`). The editor presses **Send for publishing**; the
version waits; the workflow cronjob publishes it at its first run after the date. The event can also set the
object's publish date to that date.

## Setting it up

1. Add a field **Publish at** (Date and time) to the class, for example to your meeting or release class.
2. **Setup > Workflows**: create a workflow with the event **Wait until date**. In the event, choose the **Class**,
   press **Update attributes**, choose the **Attribute** (Publish at) and add the combination; it appears under
   **Class/attribute combinations**.
3. **Setup > Triggers**: connect the workflow to **Before publishing content**. If an approval workflow already uses
   that trigger, put both in one workflow with a **Multiplexer** event, or add both events to one workflow (approve
   first, then wait).
4. Make sure the `frequent` cronjob group runs often enough (below).

**Setup > Workflow processes** lists waiting versions with what they wait for; **Cancel...** stops one and returns
the version to a draft ([Workflows](../../../guides/workflows.md)).

## How precise it is

Publication happens at the first run of the workflow cronjob after the date. With the cronjob every 5 minutes, a
release set for 9:30 appears between 9:30 and about 9:35, plus the time caches take to update. If the exact minute
matters, schedule the cronjob more often, test the timing beforehand, and check by hand at release time.

## Hiding after a date

The `hide.php` cronjob (in the global part) hides content whose date has passed: for each class in
`content.ini [HideSettings] HideDateAttributeList[]` (class identifier and date attribute) below the nodes of
`RootNodeList[]`. It hides as **Hide** in the administration does: the content stays, with its versions, and can be
unhidden ([Cronjobs](../../../guides/cronjobs.md)).

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the consultations folder>
HideDateAttributeList[consultation]=close_date
```

## Do not use the unpublish cronjob for records

`unpublish.php` (`content.ini [UnpublishSettings] RootNodeList[]`, `ClassList[]`) looks for an attribute
`unpublish_date` and, when the date has passed, **removes** the node (`removeThis()` in
`kernel/private/classes/cronjobs/unpublish.php`). Removed content goes to the trash or is deleted, and its address
stops answering. For transparency content use hiding, or a status, never this cronjob.

## Embargoed drafts

Before release, content exists only as a draft or a waiting version. A draft is visible to its creator; staff with
`content/versionread` can read versions. Keep embargoed material in a section only the release team may read, and do
not upload files to published objects ahead of time: a file in a published object is downloadable at once.

## Related pages

- [Workflows](../../../guides/workflows.md)
- [Cronjobs](../../../guides/cronjobs.md)
- [Meeting records, agendas and minutes](meeting-records-agendas-and-minutes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
