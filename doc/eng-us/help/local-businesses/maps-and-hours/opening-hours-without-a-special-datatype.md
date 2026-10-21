---
title: Opening hours without a special datatype
audience: local-business, businesses, customer, website-creator
category: business-solutions
order: 32
summary: Exponential has no opening-hours datatype; three ways to model opening hours with what ships - a text block in one shared object, a small opening hours class with time and matrix fields, or a table in rich text - with the trade-offs, the fields to use and how to show the hours in the footer and on the contact page.
keywords: opening hours, business hours, no datatype, eztime, ezmatrix, eztext, content class, footer, contact page, model, local business, restaurant, shop
type: help
---
# Opening hours without a special datatype

This page is for the owner of a local business and the person who builds the site. Visitors look for opening hours
more than for almost anything else. Exponential has **no opening-hours datatype**: the kernel's datatypes in
`kernel/classes/datatypes/` include text, date, time, date and time, selection and a matrix (table), but nothing that
knows about weekdays and opening times, and no extension in the standard `composer.json` adds one (checked on
20 October 2026). Opening hours are modeled with the datatypes that exist.

## Contents

1. [Three ways](#three-ways)
2. [Option 1: a text block in one shared object](#option-1-a-text-block-in-one-shared-object)
3. [Option 2: a small opening hours class](#option-2-a-small-opening-hours-class)
4. [Option 3: a table in rich text](#option-3-a-table-in-rich-text)
5. [Showing the hours everywhere](#showing-the-hours-everywhere)
6. [What none of the options does](#what-none-of-the-options-does)
7. [Checklist](#checklist)

## Three ways

| | Option 1: text block | Option 2: own class | Option 3: rich text table |
|---|---|---|---|
| Datatypes | `eztext` | `ezmatrix` or `eztime` fields, `eztext` | `ezxmltext` |
| Set up by | builder, an hour | builder, a few hours | nobody; editors do it |
| Easy to edit | yes | yes, fixed fields | yes, but tables in rich text are fiddly |
| Same layout everywhere | as typed | yes, the template decides | as typed |
| Usable for structured data | no | yes | no |
| Suits | most small businesses | restaurants, practices, several locations | a quick start |

Field types are explained in [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md).

## Option 1: a text block in one shared object

Add a **Text block** (`eztext`) field called "Opening hours" to the object that already holds the address and phone
(see [Planning your pages](../getting-started/planning-your-pages.md#write-each-fact-once)). The editor types:

```text
Monday to Friday   8:00 - 18:00
Saturday           9:00 - 14:00
Sunday             closed
```

Add a second text field "Notice" for exceptions ("Closed on 1 May"). The template prints both, keeping line breaks.

## Option 2: a small opening hours class

The builder creates a class, for example `opening_hours`, with:

| Field | Identifier | Datatype | Note |
|---|---|---|---|
| Title | `title` | Text line | "Opening hours", or the location name |
| Regular hours | `regular_hours` | Matrix (`ezmatrix`) | columns: Day, Opens, Closes, Note; one row per day |
| Notice | `notice` | Text block | holidays, exceptions |
| Notice valid until | `notice_until` | Date | the template hides the notice after this date |

The matrix gives every day the same format and lets the template lay it out as a table, or print structured data
([Structured data: what exists](../getting-found/structured-data-what-exists.md#adding-a-localbusiness-block)).
Instead of a matrix, fourteen **Time** (`eztime`) fields (open and close for each day) are possible but tedious to
edit; most builders prefer the matrix. For a lunch break, add a second row for the same day.

A ready text for the editor is in the [Opening hours content template](../templates/opening-hours-content-template.md).

## Option 3: a table in rich text

Editors can insert a table in any rich text field with the online editor. This needs no setup, which makes it a good
start. Its weak points: the table looks different on each page it is typed on, and it is easy to forget one copy when
hours change. Move to option 1 or 2 when you can.

## Showing the hours everywhere

Whatever option you choose, the hours should be typed **once** and shown:

- in the footer of every page, or a short "Today: 8:00 - 18:00" line in the header;
- on the contact and location page;
- on the front page, if it is the question visitors ask most.

The builder fetches the one object in the pagelayout or the footer template
([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md)). The footer is cached for
speed, so after editing the hours the change may take a moment to appear; the builder makes sure publishing the hours
object clears the cache that holds it ([View cache and cache blocks](../../creators/website-creators/view-cache-and-cache-blocks.md)).

## What none of the options does

- **"Open now" / "Closed now"** needs a small template or script that compares the current time with the hours; it is
  not built in.
- **Holiday calendars** for your region are not built in. Holiday hours are edited by hand
  ([Holiday and seasonal hours](holiday-and-seasonal-hours.md)).
- **Your listings on map platforms** are not updated from the site
  ([Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md)).

## Checklist

- [ ] One option chosen and written into the brief.
- [ ] Hours typed in one place only.
- [ ] Shown in the footer or header and on the contact page.
- [ ] A notice field exists for exceptions.
- [ ] Editing the hours updates every page that shows them.
- [ ] The same hours are on the map and search platforms.

## Related pages

- [Holiday and seasonal hours](holiday-and-seasonal-hours.md)
- [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)
- [Opening hours content template](../templates/opening-hours-content-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
