---
title: Publish an event
audience: government, editor
category: public-sector
order: 140
summary: For editors - add a public event to the events calendar with the shipped Event class, with start and end times, place, accessibility and booking information, and check it in the calendar.
keywords: event, publish event, event calendar, From Time, To Time, short title, category, place, accessibility, booking, calendar view
type: help
---
# Publish an event

This page is for editors who add events to the site's calendar. It uses the shipped **Event** class (Short title, Full
title, Text, Category, From Time, To Time) inside an **Event calendar** ([What ships for events](../../building/events/what-ships.md));
your site may have extra fields such as place, audience or booking ([Modeling an event class](../../building/events/modeling-an-event-class.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- You know the calendar the event belongs in (one per site, or per department).
- You have: date and time (start and end), place with address, what it is, who it is for, cost, booking, and the
  venue's accessibility ([Accessible event information](../../building/events/accessible-event-information.md)).
- Your role may create events in the calendar (the default Editor role may create `event` below `event_calendar`).

## Steps

1. Open the Event calendar, **Create new**, choose **Event**.
2. **Short title** (required): the name shown in the calendar, short. **Full title**: the longer name, if needed.
3. **From Time** (required) and **To Time**: date and time.
4. **Text**: one or two sentences on what it is, then the place, how to get there, accessibility, cost and how to book.
5. **Category**: keywords such as "consultation" or "family"; or the Tags field if your class has one.
6. Extra fields of your site: place, audience, booking status, booking link.
7. **Preview**, then **Send for publishing**.

## Check the result

- [ ] The calendar shows the event on the right day (open the month view and the program list if your site has both).
- [ ] The event page, signed out, shows date, time, place and accessibility information.
- [ ] "Upcoming events" lists on the front page show it after their cache renews (up to the expiry your web team set).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Date and time only on a poster image | the facts belong in the text ([Accessible event information](../../building/events/accessible-event-information.md)) |
| No To Time on a long event | upcoming lists may drop it when it starts; set the end |
| A recurring event | each date is its own Event; copy the first one ([Copy a page as a starting point](copy-a-page-as-a-starting-point.md)) |
| Sign-ups by e-mail to a personal address | use a team mailbox or a sign-up form ([Registrations for events](../../building/events/registrations-for-events.md)) |

## Related pages

- [Publishing tasks](README.md)
- [Cancel or postpone an event](cancel-or-postpone-an-event.md)
- [Calendar views](../../building/events/calendar-views.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
