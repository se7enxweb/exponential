---
title: Opening hours content template
audience: local-business, businesses, customer, editor
category: business-solutions
order: 97
summary: Ready-to-fill texts for opening hours on a local business site - regular hours as a text block or matrix rows, split hours, appointment-only times, holiday and closure notices, seasonal hours and emergency notes - written the same way every time and easy to translate.
keywords: opening hours template, business hours text, notice template, holiday notice, closure notice, seasonal hours, matrix, text block, local business
type: help
---
# Opening hours content template

This template is for whoever types the opening hours of a local business into the site. Writing them the same way
every time makes them easy to read, easy to translate and easy to compare with your listings on map platforms. How the
hours are stored is explained in [Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md).

## Contents

1. [Writing rules](#writing-rules)
2. [Regular hours as a text block](#regular-hours-as-a-text-block)
3. [Regular hours as matrix rows](#regular-hours-as-matrix-rows)
4. [Notices](#notices)
5. [Special cases](#special-cases)
6. [Before you publish](#before-you-publish)

## Writing rules

| Rule | Example |
|---|---|
| One format for times, the one your customers use | `8:00 - 18:00` or `8 am - 6 pm`, never both |
| Days written out or abbreviated the same way everywhere | `Monday`, not `Mon` here and `Monday` there |
| Group days with the same hours | `Monday to Friday` |
| Say "closed" for closed days | do not leave a day out |
| Full dates in notices | `Monday 25 December`, not "next Monday" |
| Same wording as on your listings | so visitors are not confused |

## Regular hours as a text block

```text
Monday to Friday    8:00 - 18:00
Saturday            9:00 - 14:00
Sunday              closed
```

With a lunch break:

```text
Monday to Friday    8:00 - 12:30 and 14:00 - 18:00
Saturday            9:00 - 13:00
Sunday and public holidays   closed
```

## Regular hours as matrix rows

If your site has an opening hours class with a matrix (columns Day, Opens, Closes, Note):

| Day | Opens | Closes | Note |
|---|---|---|---|
| Monday | 8:00 | 18:00 | |
| Tuesday | 8:00 | 18:00 | |
| Wednesday | 8:00 | 12:30 | afternoon closed |
| Thursday | 8:00 | 18:00 | |
| Friday | 8:00 | 20:00 | late opening |
| Saturday | 9:00 | 14:00 | |
| Sunday | | | closed |

A day with a lunch break gets two rows with the same day name.

## Notices

Public holiday:

```text
Closed on <weekday> <date> (<holiday name>). Open again <weekday> <date> from <time>.
```

Several days:

```text
Holiday closure: closed from <weekday> <date> to <weekday> <date>.
Open again <weekday> <date> from <time>. Orders placed in this time are sent on <date>.
```

Changed hours for a period:

```text
Summer hours from <date> to <date>: Monday to Saturday <time> - <time>, Sunday closed.
```

Unplanned closure:

```text
Today, <weekday> <date>, we are closed because of <short reason>. We are sorry.
Open again <weekday> <date>. Urgent questions: <phone or e-mail>.
```

Remember to remove each notice after its date, or give it a "valid until" date if the class has one
([Holiday and seasonal hours](../maps-and-hours/holiday-and-seasonal-hours.md)).

## Special cases

| Business | Add |
|---|---|
| Practice or salon with appointments only at certain times | `Thursday 14:00 - 19:00 by appointment only` |
| Trades | `Office hours` for the phone, and `Emergency service: <phone>` if offered |
| Restaurant | separate `Kitchen` hours: `Kitchen open until 21:30` |
| Market stall | the place with the days: `Saturday 8:00 - 13:00, Market Square` ([Several locations](../maps-and-hours/several-locations.md)) |
| Seasonal business | the season: `Open from <month> to <month>` |

## Before you publish

- [ ] Every day of the week appears.
- [ ] Times match what is on the door.
- [ ] The same hours are on every map and search platform.
- [ ] Translated into every language of the site.
- [ ] Notices have full dates and someone to remove them.

## Related pages

- [Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md)
- [Holiday and seasonal hours](../maps-and-hours/holiday-and-seasonal-hours.md)
- [Templates for local businesses](README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
