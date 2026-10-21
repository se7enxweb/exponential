---
title: Events and specials
audience: local-business, businesses, customer, editor
category: local-business
order: 61
summary: Announcing events, daily specials and seasonal offers on a local business site - the event and event calendar classes from ezwebin, specials as articles that appear and disappear on a date, a seasonal landing page with a layout rule, front page blocks, and what is not built in (tickets, iCal, recurring events).
keywords: events, specials, offers, daily special, event calendar, ezwebin, scheduled publishing, wait until date, hide at a date, layout rule, front page, local business
type: help
---
# Events and specials

This page is for the person who announces what is happening at a local business: a tasting evening, a workshop, the
lunch special, a summer sale. Exponential has event classes and date-based publishing; this page shows how a small
business uses them.

## Contents

1. [Events](#events)
2. [Specials and offers](#specials-and-offers)
3. [On a date, automatically](#on-a-date-automatically)
4. [Getting them seen](#getting-them-seen)
5. [What is not built in](#what-is-not-built-in)
6. [Checklist](#checklist)

## Events

The `ezwebin` site packages ship the classes `event`, `event_calendar` and `multicalendar`
([Events and calendars](../../businesses/members/events-and-calendars.md#the-event-classes)):

| Class | What it is | Fields that matter |
|---|---|---|
| Event | one event | title, short title, text, category, from time, to time |
| Event calendar | a folder of events, shown as a month calendar or a program list | title, view (calendar or program) |
| Multicalendar | one page combining several calendars | calendars |

For a local business: one event calendar called "What's on", with events in it. Choose the **program** view if you
have a few events a month; the list is easier to read on a phone than a calendar grid.

Adding an event:

1. Open the event calendar and create an **Event**.
2. Short title ("Wine tasting"), full title ("Autumn wine tasting with a local vineyard"), the text with price and how
   to sign up.
3. From time and to time with the date picker.
4. Publish. The calendar view refreshes within a few minutes (it is cached).

Sign-ups: an information collector form on the event page, or a link to a ticketing service
([Quote and request forms](../contact-and-forms/quote-and-request-forms.md)).

## Specials and offers

Specials are short-lived, so keep them simple:

- A folder "Specials" with one **article** per special: title, short text, photo, price, "valid until".
- Or a single page "This week" that you edit every week. Older versions remain in the version history.
- For a restaurant's daily special, one article you edit every morning is the least work.

## On a date, automatically

If your administrator has set them up ([Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md)):

| Want | Tool |
|---|---|
| The Christmas offer appears on 1 December | the *Wait until date* workflow event: prepare and publish now, it goes live on the date |
| The offer disappears after 24 December | the `hide.php` cron job with a date field on the class |
| A seasonal front page for a period | a layout rule with a time condition ([Layout rules](../../creators/website-creators/layout-rules.md)) |

Check on the day that it happened; all three depend on cron jobs running on the server.

## Getting them seen

- A block "What's on" or "This week" on the front page, built in the layouts editor or `ezflow`
  ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)).
- A line in the next newsletter ([Newsletters for local businesses](newsletters-for-local-businesses.md)).
- An RSS feed of the calendar for people and sites that follow feeds
  ([RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md)).
- A good sharing image so the event looks right when shared in chats
  ([Titles and descriptions with xrowmetadata](../getting-found/titles-and-descriptions-with-xrowmetadata.md#the-sharing-image)).
- The event on your business listing, if the platform supports it (outside Exponential).

## What is not built in

From [Events and calendars](../../businesses/members/events-and-calendars.md#what-is-not-built-in):

| Not built in | Instead |
|---|---|
| Tickets with seat limits and waiting lists | a ticketing service, linked from the event |
| Paid tickets | a product per event in the shop, or a ticketing service |
| iCal files for visitors' calendars | an extension; none found |
| Recurring events | one event object per date |
| Automatic reminders | a newsletter edition |

## Checklist

- [ ] One event calendar, program view if events are few.
- [ ] Each event says date, time, price and how to sign up.
- [ ] Specials have a clear end date, and are hidden after it.
- [ ] Date-based publishing tested once with a dummy special.
- [ ] Front page shows what is on.

## Related pages

- [Seasonal changes](../running-it/seasonal-changes.md)
- [Content calendar template](../../customers/day-to-day/content-calendar-template.md)
- [Restaurant orders and what is not built in](../simple-shop/restaurant-orders-and-what-is-not-built-in.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
