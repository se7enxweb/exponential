---
title: Events and calendars for non-profits
audience: non-profit, businesses, editor, website-creator
category: business-solutions
order: 30
summary: What a non-profit can do with events on Exponential - the event, event_calendar and multicalendar classes shipped as ezwebin packages, calendar and program views, public and volunteers-only events, sign-up with an information collector form, maps with ezgmaplocation, announcing events, paid tickets through the shop - and the plain list of what is not built in (ticketing with seat limits, iCal export, recurring events, reminders).
keywords: events, event calendar, multicalendar, ezwebin, event class, from_time, to_time, calendar view, program view, sign-up form, fundraising event, volunteers-only events, ezgmaplocation, map, tickets, iCal, recurring events
type: help
---
# Events and calendars for non-profits

This page is for organizers of fundraising events, volunteer days, workshops and community meetings. The classes
were checked in the repository: `ezwebin` ships installer packages for `event`, `event_calendar` and `multicalendar`
(`extension/ezwebin/packages/`). The full description is
[Events and calendars](../../businesses/members/events-and-calendars.md) in the Businesses section; this page applies
it to a non-profit.

## Contents

1. [The event classes](#the-event-classes)
2. [Calendar or program](#calendar-or-program)
3. [Public and volunteers-only events](#public-and-volunteers-only-events)
4. [Signing up](#signing-up)
5. [Location and maps](#location-and-maps)
6. [Paid fundraising events](#paid-fundraising-events)
7. [Announcing events](#announcing-events)
8. [What is not built in](#what-is-not-built-in)
9. [Checklist](#checklist)

## The event classes

| Class | What it is |
|---|---|
| `event` | One event: full and short title, text, category, from time (required), to time, metadata |
| `event_calendar` | A folder for events with a **View** choice |
| `multicalendar` | Shows several calendars in one |

Source: [Events and calendars, The event classes](../../businesses/members/events-and-calendars.md#the-event-classes).

## Calendar or program

The event calendar's full view shows either a month calendar or a program list, depending on its **View** field. A
calendar suits many small events (volunteer days); a program suits a few big ones (an annual gala, a conference). The
calendar view is cached for 400 seconds, so a new event can take a few minutes to appear (same source).

## Public and volunteers-only events

- Public events go in a public calendar.
- Volunteer-only events go in a calendar inside the volunteers' section, visible only to the Volunteers role
  ([A members area for volunteers](../volunteers/volunteer-member-area.md)).
- A `multicalendar` can show both to volunteers who are logged in.

## Signing up

There is no registration feature on the event class. Put a sign-up form object below or next to the event, built
with the information collector ([Volunteer sign-up form, step by step](../volunteers/volunteer-sign-up-form-step-by-step.md)
shows the method). Ask only what the event needs; delete submissions after the event
([Processing volunteer applications](../volunteers/processing-volunteer-applications.md#a-retention-plan)).

## Location and maps

The `ezgmaplocation` datatype stores a latitude and longitude; the editor finds the point by typing an address under a
Google map. It needs a Google Maps API key; with an empty key no Google script loads and the coordinates show without a
map ([ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md)). It is not an attribute of the shipped
`event` class; a website creator adds it, or you write the address as text. A map loads a third party's script:
mention it in the privacy notice ([Maps and embeds](../../government/privacy/maps-and-embeds.md)).

## Paid fundraising events

Tickets with a price can be sold as a product per event in the built-in shop
([Shop donation items, step by step](../donations/shop-donation-items-step-by-step.md) shows the same steps), or
through an external ticketing service linked from the event page.

## Announcing events

| Channel | How |
|---|---|
| Home page | A layout block listing upcoming events ([Landing pages with Layouts](../../creators/website-creators/landing-pages-with-layouts.md)) |
| Newsletter | An edition invites ([Supporter newsletter](supporter-newsletter-with-cjw-newsletter.md)) |
| Feed | An RSS feed of the calendar ([Open data, RSS and REST](../transparency/open-data-rss-and-rest.md)) |
| Social media | Share the event page; its metadata gives the preview |

## What is not built in

Searched in the repository (see also [Events and calendars, What is not built in](../../businesses/members/events-and-calendars.md#what-is-not-built-in)):

| Not built in | Plan instead |
|---|---|
| Registration with seat limits, waiting lists, tickets | An extension, or an external ticketing service |
| Calendar files (iCal) for people's calendar apps | An extension; no such export was found in the code |
| Recurring events | One Event object per date |
| Automatic reminders | A newsletter edition or a mail from the office |

## Checklist

- [ ] Event calendar installed and its view chosen.
- [ ] Event pages with date, time, place, accessibility information (step-free access, captioning, contact).
- [ ] Sign-up form, if needed, with a deletion date.
- [ ] Map only if the privacy notice covers it.
- [ ] Announced on the home page, newsletter and feed.
- [ ] After the event: results or photos (with permission) published; old sign-ups deleted.

## Related pages

- [Communications for non-profits](README.md)
- [Events and news for groups](../../businesses/associations/events-and-news-for-groups.md)
- [ezwebin](../../../features/6.0/extensions/ezwebin.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
