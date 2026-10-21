---
title: Events and calendars for clubs
audience: individual, website-creator, customer
category: website-creation
order: 1714
summary: How a volunteer publishes meetings, matches, concerts and outings on an Exponential club site with the shipped Event, Event calendar and Multicalendar classes - calendar or program view, public and members-only events, sign-ups, maps, announcing, and what is not built in (tickets, calendar export).
keywords: events, calendar, Event class, Event calendar, Multicalendar, program view, meetings, matches, concerts, members only events, sign-up, ezgmaplocation, map, announce, ticketing, iCal, club
type: help
---
# Events and calendars for clubs

This page is for individuals who keep a club's calendar on its website. The full owner's page for member
organizations is [Events and calendars](../../businesses/members/events-and-calendars.md); this one is the short
version for a volunteer who also has to do everything else.

## Contents

1. [The classes](#the-classes)
2. [Setting up the calendar](#setting-up-the-calendar)
3. [Adding an event](#adding-an-event)
4. [Public and members-only](#public-and-members-only)
5. [Sign-ups](#sign-ups)
6. [Where it happens: maps](#where-it-happens-maps)
7. [Telling people](#telling-people)
8. [Not built in](#not-built-in)
9. [Checklist](#checklist)

## The classes

| Class | Fields | Use |
|---|---|---|
| *Event* | title, short title, text, category, from time, to time, metadata | One meeting, match or concert |
| *Event calendar* | title, short title, **View** | Holds events; shows a month calendar or a program list |
| *Multicalendar* | name, description, calendars | One page combining several calendars |

Sources: `class-event.xml` and `class-event_calendar.xml` in the `sevenx_classes` package; the `ezwebin` packages
for all three ([Events and calendars, The event classes](../../businesses/members/events-and-calendars.md#the-event-classes)).

## Setting up the calendar

1. Create an *Event calendar* called "Calendar" under the home page.
2. Choose its **View**: a month calendar suits clubs with frequent meetings; a program list suits a season of
   concerts or matches.
3. Publish. A calendar placed in the tree appears in the menu of the `ezwebin` design.

The calendar page is cached for a few minutes, so a new event can take a moment to appear there.

## Adding an event

1. **Create here** > *Event* inside the calendar.
2. **Short title** (required) for the calendar grid, **full title** for the event page.
3. **From time** (required) and **to time**.
4. **Category**: keywords such as "training", "match", "social"; use the same words every time.
5. **Text**: place, what to bring, cost, contact.
6. Publish.

Recurring events (every Tuesday) are entered one by one; there is no repeat rule. Copy an existing event to save
typing.

## Public and members-only

An event calendar is content, so its section decides who sees it. Keep two calendars if you need both: a public one
and one in the members' area ([Private areas with roles and sections](../family-sites/private-areas-with-roles-and-sections.md)).
A *Multicalendar* can show both to signed-in members on one page.

## Sign-ups

There is no event registration module. What works:

- a sign-up form next to the event, built with the information collector
  ([Forms, polls and sign-ups](forms-polls-and-sign-ups.md));
- a newsletter list for people who want invitations ([Club newsletters](club-newsletters.md)).

## Where it happens: maps

`ezgmaplocation` (in the root `composer.json`) adds a map location datatype you can put on the *Event* class or on a
"Venue" article ([ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md)). Showing a map sends visitors'
browsers to the map provider; mention it in your privacy text. A plain address and a link to a map service work too.

## Telling people

- An "upcoming events" list on the home page.
- The next newsletter edition.
- An RSS export of the public calendar ([RSS feeds for your blog](../blogs/rss-feeds-for-your-blog.md)).
- After the event: results, photos (with consent) in a *Gallery*.

## Not built in

- **Ticketing** and paid registration.
- **Export to phone calendars** (iCal files) out of the box.
- **Repeat rules** for recurring events.
- **Automatic reminders** to people who signed up.

## Checklist

- [ ] One public calendar, one members-only calendar if needed.
- [ ] Category words agreed.
- [ ] Every event has a short title, start time, place and contact.
- [ ] Sign-up form where needed, protected against spam.
- [ ] Past events kept or archived deliberately.

## Related pages

- [Club sites](README.md)
- [Events and calendars](../../businesses/members/events-and-calendars.md)
- [Events and news for groups](../../businesses/associations/events-and-news-for-groups.md)
- [Birthdays and family dates](../family-sites/birthdays-and-family-dates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
