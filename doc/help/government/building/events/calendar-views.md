---
title: Calendar views
audience: government, website-creator, designer
category: public-sector
order: 40
summary: How the ezwebin calendar and program views of the Event calendar class work - choosing the view, month navigation addresses, paging, caching - and what to do on a design without event templates, including accessibility of a month grid.
keywords: calendar view, program view, event calendar, ezwebin, event_view_calendar, event_view_program, view parameters, month, year, offset, cache_ttl, template override, accessibility, grid
type: help
---
# Calendar views

This page is for website creators and designers who show events as a calendar or a list. It describes the two views
of the ezwebin design and how to provide the same on another design. Template facts are from
`extension/ezwebin/design/ezwebin/templates/full/` and [What ships](what-ships.md).

## Contents

1. [Choosing the view](#choosing-the-view)
2. [The calendar view](#the-calendar-view)
3. [The program view](#the-program-view)
4. [Caching](#caching)
5. [On a design without event templates](#on-a-design-without-event-templates)
6. [Accessibility of calendars](#accessibility-of-calendars)

## Choosing the view

Edit the Event calendar object; the **View** selection decides which template `full/event_calendar.tpl` includes:
the calendar or the program. Each calendar object has one view; two calendar objects (or a Multicalendar) can show the
same events differently.

## The calendar view

`event_view_calendar.tpl` shows one month as a grid:

| Feature | Detail |
|---|---|
| Month shown | the current month, or the one in the address: `<calendar address>/(month)/<1-12>/(year)/<yyyy>` |
| Navigation | previous and next month links (titled "Previous month" / "Next month") |
| Events | fetched below the calendar with attribute filters on the From Time and To Time of the month |
| A day | `(day)/<n>` in the address selects a day |

## The program view

`event_view_program.tpl` lists events in time order, 15 per page, with links to newer and older events; the page is
in the address as an offset. For many visitors this list is easier to use than a grid, on phones and with screen
readers.

## Caching

The ezwebin event calendar sets a `cache_ttl` on its full view so the "current month" moves on without a publish
([Caching for designers](../../../designers/theme-design/caching-for-designers.md)). Publishing an event clears the
view cache of the event and its parent calendar. Lists of events elsewhere (front page blocks) need their own expiry
([Listing upcoming events](listing-upcoming-events.md)).

## On a design without event templates

The default design of new installations (`simple`) has no event templates. Options:

| Option | Effort |
|---|---|
| Add `full/event_calendar.tpl` and `full/event.tpl` overrides to your design, based on the ezwebin templates | moderate; copy, adapt markup and texts |
| A program-style list only: a folder with a fetch template sorted by From Time | small ([Listing upcoming events](listing-upcoming-events.md)) |
| Exponential Layouts: a block that lists events of a calendar | no template work for editors ([The layouts editor](../../../layouts-editor.md)) |

Template overrides match the class identifier ([Template overrides](../../../creators/website-creators/template-overrides.md)).
The ezwebin templates are GPL code; copying them into your design is allowed under the same license.

## Accessibility of calendars

| Issue | Practice |
|---|---|
| A month grid is a data table | real table markup with header cells for weekdays (`scope="col"`) and a caption with the month |
| Events in cells are often tiny links | a minimum target size (WCAG 2.2 criterion 2.5.8) and readable text |
| Navigation links shown as arrows only | link text or an accessible name: "Previous month" |
| Days without events | do not announce empty cells as links |
| Alternative | always offer the program list next to the grid |

Test with a keyboard and a screen reader ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md));
the shipped ezwebin template was not audited ([Accessibility standards](../../../../standards/accessibility.md)).

## Related pages

- [Events](README.md)
- [What ships](what-ships.md)
- [Designs and the fallback chain](../../../creators/website-creators/designs-and-the-fallback-chain.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
