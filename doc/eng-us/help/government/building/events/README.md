---
title: Events
audience: government, website-creator, project-manager, editor
category: public-sector
order: 10
summary: Index of the guides for publishing public events on an Exponential site with what really ships - the Event, Event calendar and Multicalendar classes and the ezwebin calendar and program views - and how to extend, list, register for, change and archive events.
keywords: events, event calendar, multicalendar, calendar view, program view, ezwebin, upcoming events, registration, cancellation, accessible events, archive
type: help
---
# Events

This folder is for website creators, project managers and editors who publish public events: council meetings open to
the public, information evenings, consultation sessions, open days, community events. It starts from what really
ships with Exponential (three content classes and two calendar views in the ezwebin design) and shows how to build
on it where needed. Nothing here assumes an event booking system: Exponential does not include one.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [What ships and what does not](#what-ships-and-what-does-not)

## The pages

| Page | The answer in one line |
|---|---|
| [What ships](what-ships.md) | Event, Event calendar and Multicalendar, their attributes, the calendar and program views and where they come from |
| [Modeling an event class](modeling-an-event-class.md) | Attributes to add for public events (place, accessibility, audience, booking status) and how to add them safely |
| [Calendar views](calendar-views.md) | The month grid and the program list of ezwebin, their addresses, caching, and what to do on another design |
| [Listing upcoming events](listing-upcoming-events.md) | Template fetches for "next events" lists, by place or tag, with cache expiry |
| [Registrations for events](registrations-for-events.md) | A sign-up form with the information collector, its limits, and when to link a booking system |
| [Cancellations and changes](cancellations-and-changes.md) | Changing time or place, cancelling, telling people, and the history kept in versions |
| [Accessible event information](accessible-event-information.md) | What every event page must say in text, venue accessibility, dates for screen readers |
| [Past events and the archive](past-events-archive.md) | Hiding or keeping past events, archives by year, removing sign-up data |

## Reading order

1. **What ships**, then **Calendar views**.
2. **Modeling an event class** if the shipped fields are not enough.
3. **Listing upcoming events** for front pages and topic pages.
4. **Registrations for events**, **Cancellations and changes**, **Accessible event information**.
5. **Past events and the archive**.

Up: [Building a public-sector site](../README.md), the index of all building guides, and [Governments: start here](../../government-start-here.md), the start of the Governments
section. Libraries have their own events page: [Events and catalogs linked rather than built](../../levels/public-libraries/events-and-linked-catalogs.md).

## What ships and what does not

| Ships | Does not ship |
|---|---|
| Event (`event`), Event calendar (`event_calendar`), Multicalendar (`multicalendar`) classes in `sevenx_classes` | a booking system with places, waiting lists and payments |
| ezwebin's `event_view_calendar.tpl` and `event_view_program.tpl` | calendar templates in the default `simple` design |
| `ezdatetime` attributes and fetches with attribute filters | iCalendar (`.ics`) export of events |
| RSS exports of a calendar folder | recurring events (each date is its own object) |

## Related pages

- [News and alerts](../news-and-alerts/README.md)
- [Building forms](../forms/README.md)
- [sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
