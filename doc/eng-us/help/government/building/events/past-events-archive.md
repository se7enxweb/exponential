---
title: Past events and the archive
audience: government, website-creator, admin, editor
category: public-sector
order: 90
summary: What to do with events after they happen on an Exponential site - keep them in a yearly archive, hide them automatically with hide.php on the To Time, remove sign-up data, and publish outcomes such as recordings and reports.
keywords: past events, archive, hide.php, HideSettings, to_time, event archive by year, recordings, outcomes, sign-up data removal, cleanup
type: help
---
# Past events and the archive

This page is for website creators, administrators and editors who decide what happens to events after they happen.
Calendars fill up; past events either become a useful record or clutter. Features named are documented in
[Cronjobs](../../../../guides/cronjobs.md) and [Archive and expiry](../news-and-alerts/archive-and-expiry.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Keep, archive or hide](#keep-archive-or-hide)
2. [Hiding past events automatically](#hiding-past-events-automatically)
3. [A yearly archive](#a-yearly-archive)
4. [After the event](#after-the-event)

## Keep, archive or hide

| Event | Usually |
|---|---|
| Public meetings (council, consultation sessions) | keep: they are part of the record; add outcomes |
| Community and cultural events | hide after the date, or keep a year in an archive |
| Training sessions, open days | hide after the date |
| Cancelled events | hide after the date |

The calendar views and "upcoming" lists already leave past events out of the current month and of lists filtered on
the start time ([Listing upcoming events](listing-upcoming-events.md)); hiding takes them out of search and menus too.

## Hiding past events automatically

The `hide.php` cronjob hides nodes whose date attribute has passed. For events, use the end time:

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Events calendar>
HideDateAttributeList[event]=to_time
```

Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`); the script is in the global cronjob part. Events
without a To Time are not hidden (the filter needs a value). If you hide on `from_time` instead, an event disappears
when it starts, which is too early for events people may still want to find that day.

Exclude events you want to keep by putting them in a calendar not listed in `RootNodeList[]`, for example a separate
"Public meetings" calendar.

## A yearly archive

| Step | How |
|---|---|
| Create an "Archive <year>" folder below Events | at the start of the year |
| Move the past year's events there | tick them in the **Sub items** list, **More actions** > **Move selected**; a large move can run as a content job ([Content model and editing, 8](../../../../guides/content-model-and-editing.md#8-large-operations-as-content-jobs)) |
| Old addresses keep working | moving keeps redirects from the old aliases ([Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md)) |
| Label them | an "archived" note in the template for events in archive folders |

## After the event

| Task | Where |
|---|---|
| Add outcomes: a summary, presentations, a recording with captions | edit the event, or relate a report |
| Remove sign-up data | **Setup > Collected information**, the event's collections, **Remove selected** ([Retention of collected information](../forms/retention-of-collected-information.md)) |
| Remove the organizer's mail copies of sign-ups | the mailbox |
| Photos of attendees | publish only with consent where your rules require it |

## Related pages

- [Events](README.md)
- [Cancellations and changes](cancellations-and-changes.md)
- [Archive and expiry](../news-and-alerts/archive-and-expiry.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
