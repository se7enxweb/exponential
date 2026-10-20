---
title: Scheduled publishing
audience: customer, editor, admin
category: customer-success
order: 12
summary: What an Exponential site can do on a date by itself - publish at a date with the Wait until date workflow, hide or remove at a date with the shipped cronjobs, switch landing pages by a time rule, send newsletters on a schedule - what each needs from your administrator and how to check it worked.
keywords: scheduled publishing, publish later, embargo, wait until date, unpublish date, hide date, cronjob, time condition, layout rule, workflow
type: help
---
# Scheduled publishing

This page is for owners and editors who want content to appear, disappear or change on a date without someone
pressing a button at that moment. Exponential does this with tools your administrator switches on; none of them is
active for every class out of the box. Ask which of them your site has before you promise a date.

## Contents

1. [What can happen on a date](#what-can-happen-on-a-date)
2. [Publishing at a date](#publishing-at-a-date)
3. [Hiding or removing at a date](#hiding-or-removing-at-a-date)
4. [Landing pages for a period](#landing-pages-for-a-period)
5. [Everything depends on cron](#everything-depends-on-cron)
6. [Checking that it worked](#checking-that-it-worked)

## What can happen on a date

| Need | Tool | Set up by |
|---|---|---|
| Publish an article at a future date | the *Wait until date* workflow event on publishing | administrator ([ezwaituntildate](../../../examples/workflow-events/ezwaituntildate.md)) |
| Hide an item at a date | the `hide.php` cronjob with a date field per class | administrator (`settings/content.ini [HideSettings]`) |
| Remove an item at a date | the `unpublish.php` cronjob and an `unpublish_date` field | administrator (`settings/content.ini [UnpublishSettings]`) |
| Show a campaign layout for a period | a layout rule with a `time` condition | layouts editor ([Layout rules](../../creators/website-creators/layout-rules.md)) |
| Send a newsletter at a time, or every week | scheduled and recurring sends | newsletter sender ([Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md)) |

## Publishing at a date

The *Wait until date* event runs before publishing. When the item's chosen date field holds a date in the future,
the version waits; the event records "Event delayed until" that date, and the workflow cronjob publishes it once the
date has passed. An option of the event can also set the item's published date to that date
(`kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php`).

For editors it looks like this:

1. Fill in the date field your administrator named (for example a publish date) and press **Send for publishing**.
2. The version is **pending**: it appears on *My pending items*, not on the site.
3. After the date, at the next run of the workflow cronjob, it is published.

> **Note**
> If the date field is empty or already past, the item is published at once. Agree with the team which field is the
> one that counts, and put it near the top of the edit form.

## Hiding or removing at a date

Two cronjobs of the default (global) cron part act on dates stored in the content (`settings/cronjob.ini`):

- **`hide.php`** hides items whose date field, named per class in `[HideSettings] HideDateAttributeList[]`, has
  passed, below the nodes in `RootNodeList[]`. Hidden items stay in the administration and can be unhidden
  ([Publishing, versions and the trash](../../publishing-and-versions.md#hide-an-item)).
- **`unpublish.php`** removes items of the classes in `[UnpublishSettings] ClassList[]` below `RootNodeList[]` when
  their `unpublish_date` field has passed. The removal is the kernel's removal to the **trash**, so the item can be
  restored from there (`kernel/private/classes/cronjobs/unpublish.php`).

Both lists are empty in the shipped `settings/content.ini`, so nothing happens until your administrator fills them in.
Prefer hiding when the content may come back; see [Archiving and the trash](archiving-and-the-trash.md).

## Landing pages for a period

For a campaign, map a layout to the landing page with a rule whose `time` condition has `from` and `to`, at a higher
priority than the page's normal rule. An empty time range always matches
([Layout rules](../../creators/website-creators/layout-rules.md)). Before and after the period, the normal layout
shows. See [Landing pages with layouts](../growth/landing-pages-with-layouts.md).

## Everything depends on cron

> **Warning**
> Waiting versions are published by the workflow cronjob (`workflow.php`, part *frequent*); hiding and removing run in
> the global part. If cron does not run, nothing happens on the date
> ([Workflows](../../../guides/workflows.md), [Cronjobs](../../../guides/cronjobs.md)). The cronjobs page shows
> each part as *Scheduled* or *Not scheduled* with its next run.

The time of publication is therefore "the first cron run after the date", not the exact minute. Ask your host how
often the frequent part runs.

## Checking that it worked

- [ ] The day before: the version is pending, and **Setup > Workflow processes** lists it as waiting.
- [ ] On the day, after the next cron run: the item is on the site, signed out.
- [ ] Menus, lists and the front page show it (caches are cleared on publish; if a list looks old, tell your
      administrator).
- [ ] For a hide or removal: the item is hidden, or in the trash, and nothing links to it any more
      ([Content audits](content-audits.md)).

## Related pages

- [Workflows](../../../guides/workflows.md)
- [Content calendar](content-calendar.md)
- [Approvals and review](approvals-and-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
