---
title: What ships for events
audience: government, website-creator, project-manager
category: public-sector
order: 20
summary: The event features that really ship with Exponential - the Event, Event calendar and Multicalendar classes of sevenx_classes with their attributes, the calendar and program views of the ezwebin design, policies in the default roles, and an example of an event import extension.
keywords: event class, event calendar class, multicalendar, sevenx_classes, ezwebin, event_view_calendar, event_view_program, from_time, to_time, ezdatetime, ezkeyword, cjw_tmv
type: help
---
# What ships for events

This page is for website creators and project managers who want to know exactly what Exponential offers for events
before planning. Every item is taken from the class packages, the templates and the default roles of this version.

## Contents

1. [The classes](#the-classes)
2. [The views](#the-views)
3. [Permissions in the default package](#permissions-in-the-default-package)
4. [Importing events from another system](#importing-events-from-another-system)
5. [Gaps](#gaps)

## The classes

From [sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md) and the class view of the
demonstration server:

**Event** (`event`), not a container:

| Attribute | Identifier | Datatype | Required | Translatable |
|---|---|---|---|---|
| Full title | `title` | Text line | no | yes |
| Short title | `short_title` | Text line | yes | yes |
| Text | `text` | XML block | no | yes |
| Category | `category` | Keywords (`ezkeyword`) | no | yes |
| From Time | `from_time` | Date and time | yes | no |
| To Time | `to_time` | Date and time | no | no |
| Meta Data | `metadata` | Metadata (`xrowmetadata`) | no | yes |

The object name pattern is `<short_title|title>`: the short title names the event in lists.

**Event calendar** (`event_calendar`), a container: Full Title (required), Short Title, **View** (Selection,
required: the calendar or the program view), Meta Data.

**Multicalendar** (`multicalendar`): an object relation list `calendars` to several Event calendars; its full view
lists the events of all of them, for example a site-wide calendar built from department calendars.

## The views

| Template | In | Shows |
|---|---|---|
| `full/event_calendar.tpl` | ezwebin override templates | includes the view chosen in **View** |
| `full/event_view_calendar.tpl` | ezwebin templates | a month grid with previous and next month; addresses carry `(month)/<n>/(year)/<yyyy>` |
| `full/event_view_program.tpl` | ezwebin templates | a list of events, 15 per page, with newer and older pages |
| `full/event.tpl` | ezwebin override templates | one event |
| `full/multicalendar.tpl` | ezwebin override templates | the events of the related calendars |

The default `simple` design of new installations (`sevenx_themes_simple`) has no event templates; there events show
with the generic full view unless you add templates ([Calendar views](calendar-views.md)).

## Permissions in the default package

The installer's Editor role allows creating `event_calendar` and `multicalendar` below folders and frontpages, and
`event` below `event_calendar` ([Roles and policies of sevenx_multisite](../../../site-packages/sevenx-multisite/roles-and-policies.md)).
Visitors read events in the Standard section like any content.

## Importing events from another system

`cjw_tmv` is an example of an extension that imports events from an external event database as content objects with a
cronjob (it serves one regional tourism database and has its own classes). It shows the pattern for an import:
remote ids per event, updates of changed events, removal of ended dates. An import from your own event system is a
development task along the same lines ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)).

## Gaps

| Missing | Workaround |
|---|---|
| Recurring events | one Event object per date; or one event with the dates in the text |
| Places as structured data | add a Place relation or text attributes ([Modeling an event class](modeling-an-event-class.md)) |
| Bookings and capacity | an information collector form or a booking system ([Registrations for events](registrations-for-events.md)) |
| iCalendar export | not shipped; a template producing `.ics` is a development task |

## Related pages

- [Events](README.md)
- [Calendar views](calendar-views.md)
- [ezwebin](../../../../features/6.0/extensions/ezwebin.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
