---
title: Events and calendars
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: What Exponential offers a member organization for events - the Event, Event calendar and Multicalendar classes of ezwebin with calendar and program views, members-only events, sign-up with forms, announcing events, and the honest list of what is not built in such as ticketing and calendar file export.
keywords: events, event calendar, calendar, program, multicalendar, ezwebin, event class, from_time, to_time, sign-up form, information collector, members-only events, invitations, ticketing, iCal
type: help
---
# Events and calendars

This page is for organizers of meetings, courses, conferences and social events who want them on the member site. It
describes the event classes that come with the `ezwebin` design, verified in the repository and on the demonstration
server, how members find events and sign up, and what you would have to add.

## Contents

1. [The event classes](#the-event-classes)
2. [Calendar and program views](#calendar-and-program-views)
3. [Several calendars in one](#several-calendars-in-one)
4. [Public and members-only events](#public-and-members-only-events)
5. [Signing up for an event](#signing-up-for-an-event)
6. [Announcing events](#announcing-events)
7. [After the event](#after-the-event)
8. [What is not built in](#what-is-not-built-in)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## The event classes

`ezwebin` ships installer packages for the classes `event`, `event_calendar` and `multicalendar`
(`extension/ezwebin/packages/`) with templates in its design; the three classes exist on the demonstration server
([ezwebin](../../../features/6.0/extensions/ezwebin.md)).

**Event** (`event`), fields as defined on the demonstration server:

| Field | Identifier | Type | Required |
|---|---|---|---|
| Full title | `title` | text line | no |
| Short title | `short_title` | text line | yes |
| Text | `text` | rich text | no |
| Category | `category` | keywords | no |
| From time | `from_time` | date and time | yes |
| To time | `to_time` | date and time | no |
| Meta data | `metadata` | metadata (`xrowmetadata`) | no |

**Event calendar** (`event_calendar`) is a folder for events with a title, a short title and a **View** choice.

Date and time fields use the date picker of Exponential UI (`exp::datepicker`) since ezwebin 6.0.14 and 6.0.15, and
the event calendar texts are translated (ezwebin 6.0.9 and 6.0.10)
([ezwebin, What changed](../../../features/6.0/extensions/ezwebin.md#what-changed-in-the-exponential-6-releases)).

## Calendar and program views

The full view of an event calendar (`full/event_calendar.tpl`) includes either `event_view_calendar.tpl` or
`event_view_program.tpl`, depending on the calendar's **View** field. Editors choose per calendar whether members see a
month calendar or a program list. The event's full view shows the title, the category, the date range and the text
(`full/event.tpl`). The calendar's full view sets a cache time of 400 seconds (`cache_ttl`), so a new event can take a
few minutes to appear there ([Set block and append block](../../designers/templates/set-block-and-append-block.md)).

`settings/menu.ini.append.php` of ezwebin lists `event_calendar` among the classes shown in the top and left menus,
so a calendar placed in the tree appears in the navigation
([Menus and navigation](../../creators/website-creators/menus-and-navigation.md)).

## Several calendars in one

**Multicalendar** (`multicalendar`) has a name, a description and a **Calendars** field that relates several event
calendars, so one page can bring together, for example, the calendars of several regional groups.

## Public and members-only events

An event calendar is content like any other, so its section decides who sees it:

| Calendar | Section | Who sees it |
|---|---|---|
| Public events | Standard | everyone |
| Member events | Members area | members only |

Members-only areas are described in [Members-only content](members-only-content.md). A public teaser ("Members'
evening on ..., sign in for details") can announce a members-only event.

## Signing up for an event

There is no event registration module. Two documented building blocks do the job for most organizations:

- **A form with the information collector.** Fields of a class can be marked as information collectors, so visitors
  fill in a form on the page; submissions are stored and can be mailed to a receiver
  ([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md),
  [Form submissions](../../creators/website-creators/form-submissions.md)). A sign-up form per event, or a sign-up
  field group added to the Event class by your partner, collects name, e-mail and options.
- **A newsletter list per event series.** Members who want invitations subscribe to it
  ([Member communications](member-communications.md)).

Protect public sign-up forms against spam with a CAPTCHA
([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)), and say on the form
what data is kept and why ([Consent and forms](../security/consent-and-forms.md)).

## Announcing events

- An "upcoming events" block on the front page, with the layouts editor or `ezflow`
  ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)).
- An invitation in the newsletter.
- Notifications for members who follow the event calendar
  ([Notifications: the user's guide](../../../features/6.0/notifications.md)).
- An RSS export of the calendar for public events ([RSS feeds](../../../guides/rss-feeds.md)).
- Metadata for social sharing ([Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)).

## After the event

Add slides, recordings and photos to the event page, or to a members-only folder related to it. Large videos are
better hosted on a video service and embedded
([Files and downloads, Large files](../../creators/website-creators/files-and-downloads.md#large-files)). Past events
stay in the calendar as an archive.

## What is not built in

Searched in the repository on 19 October 2026:

| Not built in | Plan instead |
|---|---|
| Event registration with seat limits, waiting lists, tickets | an extension, or an external ticketing service linked from the event |
| Paid tickets | a product per event in the built-in shop ([Stores](../stores/README.md)), or an external service |
| Calendar files (iCal) for members' calendar apps | an extension; no such export was found in the code |
| Recurring events | one Event object per date |
| Automatic reminders before an event | a newsletter edition or a mail from the office |

Ask a partner to estimate what you need ([Choosing a partner](../choosing/choosing-a-partner.md)).

## Checklist

- [ ] Event classes installed (they come with ezwebin packages).
- [ ] One event calendar per audience, each in the right section.
- [ ] Calendar or program view chosen per calendar.
- [ ] Sign-up form decided and tested, with CAPTCHA and a privacy note.
- [ ] An upcoming events block on the front page.
- [ ] Invitation process with the newsletter.
- [ ] Missing features (tickets, iCal) planned or consciously skipped.

## Related pages

- Events and news for groups
- [Member communications](member-communications.md)
- [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)
- Volunteers and sign-up forms
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
