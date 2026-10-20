---
title: Cancellations and changes
audience: government, website-creator, editor
category: public-sector
order: 70
summary: How to handle changed and cancelled events on an Exponential site - editing time or place, a visible status, keeping the address, telling people who follow or signed up, the history in versions, and hiding cancelled events after their date.
keywords: event cancelled, postponed, event change, status, booking field, versions, notifications, sign-ups, hide, event history, calendar
type: help
---
# Cancellations and changes

This page is for website creators who set up how events are changed and cancelled, and for the editors who do it.
People rely on event pages; a change must be visible to anyone who opens the page, not only to those who notice the
edit. Features named are documented in [Versions of an object](../../../../guides/content-history.md) and
[Notifications: the user's guide](../../../../features/6.0/notifications.md).

## Contents

1. [Principles](#principles)
2. [A change of time or place](#a-change-of-time-or-place)
3. [Postponed or cancelled](#postponed-or-cancelled)
4. [Telling people](#telling-people)
5. [Afterwards](#afterwards)

## Principles

| Principle | Why |
|---|---|
| Keep the same page and address | people shared the link; search engines know it |
| Say what changed at the top | not hidden in the text |
| A status field, not only words in the text | lists and the calendar can show it too |
| Do not remove a cancelled event before its date | people who look it up must find "cancelled" |

The Booking selection proposed in [Modeling an event class](modeling-an-event-class.md) carries the status
(free entry, booking required, fully booked, cancelled, postponed); templates show it in the event, the calendar and
lists.

## A change of time or place

1. **Edit** the event.
2. Change **From Time**, **To Time** or the Place.
3. At the top of the text: "Changed on <date>: now at <time> in <place>."
4. **Send for publishing**. The previous version stays in the history; **Compare** shows what changed.
5. Check the calendar: an event moved to another month moves in the calendar view.

## Postponed or cancelled

| Situation | Do |
|---|---|
| Postponed, new date known | change the dates, status "postponed" only if the template shows the new date clearly; otherwise just update the dates and the note |
| Postponed, date not known | status "postponed"; keep the old date so it stays in the calendar where people look |
| Cancelled | status "cancelled"; a short reason if appropriate; keep the page |

Templates can add "Cancelled:" before the title in lists, so nobody misses it in a calendar cell.

## Telling people

| Who | How |
|---|---|
| People who follow the calendar | publishing the change sends notification mails at the next run (do not use **Publish without notification** here) |
| People who signed up | mail them from the sign-up list (**Setup > Collected information**, **Export CSV**) using your normal mail system |
| Feed readers | the RSS export shows the updated item when the feed is next written |
| Front page lists | refreshed with the cache expiry of the list ([Listing upcoming events](listing-upcoming-events.md#caching)) |

## Afterwards

- Hide cancelled events after their date, or keep them in the archive marked cancelled
  ([Past events and the archive](past-events-archive.md)).
- Remove sign-up data of cancelled events once people were told.

## Related pages

- [Events](README.md)
- [Registrations for events](registrations-for-events.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
