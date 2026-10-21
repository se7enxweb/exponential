---
title: Holiday and seasonal hours
audience: local-business, businesses, customer, editor
category: local-business
order: 33
summary: Holiday closures, summer hours and other exceptions on a local business site - edited by hand, prepared in advance with scheduled publishing and hiding at a date, shown as a notice, and kept in step with outside listings - with a yearly planner.
keywords: holiday hours, seasonal hours, closed, public holiday, summer hours, notice, scheduled publishing, wait until date, hide at a date, layout rule, local business
type: help
---
# Holiday and seasonal hours

This page is for the person who keeps the opening hours of a local business up to date. Exponential has no holiday
calendar: exceptions are **edited by hand**. With a little planning, most of the work can be done in January and
published automatically on the right day.

## Contents

1. [Three kinds of exceptions](#three-kinds-of-exceptions)
2. [Editing by hand](#editing-by-hand)
3. [Preparing ahead with dates](#preparing-ahead-with-dates)
4. [A notice that stands out](#a-notice-that-stands-out)
5. [Do not forget the platforms](#do-not-forget-the-platforms)
6. [A yearly planner](#a-yearly-planner)

## Three kinds of exceptions

| Kind | Example | Best handled as |
|---|---|---|
| A single closed day | a public holiday | a notice, plus the platforms |
| A period with other hours | summer hours, December late opening | change the regular hours, then change them back |
| An unplanned closure | illness, a burst pipe | a notice, published now |

## Editing by hand

For a notice with the opening hours object (see
[Opening hours without a special datatype](opening-hours-without-a-special-datatype.md)):

1. Sign in and open the opening hours object for editing.
2. Write the notice: "Closed on Monday 25 December. Open again Tuesday from 8:00." Use the full date, not "next
   Monday".
3. Publish. Check the footer and the contact page.
4. After the date, edit again and remove the notice, or let the template hide it if the class has a "valid until" date.

Each edit is a new **version**; the previous text can be restored
([Drafts, versions and undo](../../customers/day-to-day/drafts-versions-and-undo.md)).

## Preparing ahead with dates

Exponential can act on a date if your administrator has set it up
([Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md)):

| Need | Tool | Set up by |
|---|---|---|
| A holiday notice page appears on a date | the *Wait until date* workflow event on publishing | administrator |
| A notice disappears after the date | the `hide.php` cron job with a date field on the class | administrator |
| A seasonal front page (summer menu, December offers) for a period | a layout rule with a time condition | layouts editor ([Layout rules](../../creators/website-creators/layout-rules.md)) |

All of these depend on cron jobs running on the server. Check on the day that it worked; see
[Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md) for how.

Changing the regular hours in a matrix or text field on a date is **not** automated: open the object and edit it on
the day, or prepare a draft and publish it then.

## A notice that stands out

A holiday notice should be seen before visitors read the regular hours:

- at the top of the front page (a banner, an alert block in the layouts editor, or the notice field shown in the
  header);
- next to the regular hours on the contact page;
- in a newsletter if you send one ([Newsletters for local businesses](../marketing/newsletters-for-local-businesses.md)).

Write the notice in every language your site has ([Multilingual for tourist areas](../marketing/multilingual-for-tourist-areas.md)).

## Do not forget the platforms

Map and search platforms show their own copy of your hours. Set special hours there too, ideally on the same day you
prepare the notice on the site ([Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md)).

## A yearly planner

Fill this in each January and keep it next to the till:

| Date or period | What changes | Notice text ready | Site done | Platforms done | Notice removed |
|---|---|---|---|---|---|
| | | [ ] | [ ] | [ ] | [ ] |
| | | [ ] | [ ] | [ ] | [ ] |
| | | [ ] | [ ] | [ ] | [ ] |
| | | [ ] | [ ] | [ ] | [ ] |

## Related pages

- [Seasonal changes](../running-it/seasonal-changes.md)
- [Opening hours content template](../templates/opening-hours-content-template.md)
- [Content calendar](../../customers/day-to-day/content-calendar.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
