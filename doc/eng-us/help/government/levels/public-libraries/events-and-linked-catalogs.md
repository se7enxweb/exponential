---
title: Events and catalogs linked rather than built
audience: government, website-creator, project-manager, editor
category: public-sector
order: 70
summary: How a library runs its events calendar with the shipped Event and Event calendar classes - views, an upcoming-events list, branch filters, cancellations, sign-up - and why and how to link to the library catalog instead of rebuilding it in the CMS.
keywords: events calendar, event class, event calendar, calendar view, program view, upcoming events, fetch, attribute_filter, cancellation, sign-up, catalog, OPAC, catalog search box, link
type: help
---
# Events and catalogs linked rather than built

This page is for website creators, project managers and editors at a library. It covers the two things visitors use
most after opening hours: events and the catalog. Events are built in Exponential with shipped classes; the catalog
stays in the library management system and the website links to it.

## Contents

1. [The events calendar](#the-events-calendar)
2. [An upcoming-events list](#an-upcoming-events-list)
3. [Changes, cancellations and sign-up](#changes-cancellations-and-sign-up)
4. [The catalog: link it](#the-catalog-link-it)
5. [Checklist](#checklist)

## The events calendar

| Part | What ships | Read |
|---|---|---|
| Event class (`event`) | Short title (required), Full title, Text, Category (Keywords), From Time (required), To Time | [Content model for public libraries](content-model.md) |
| Event calendar class (`event_calendar`) | a container with Full Title and a **View** selection | |
| Views | the ezwebin design's `event_view_calendar.tpl` (month grid) and `event_view_program.tpl` (list) | [ezwebin](../../../../features/6.0/extensions/ezwebin.md) |
| Cache | the ezwebin event calendar keeps its page for 400 seconds (`cache_ttl`) | [Caching for designers](../../../designers/theme-design/caching-for-designers.md) |

The views come with the ezwebin design. The default `simple` design of new installations has no event calendar
template; a site on it needs its own `full/event_calendar.tpl` override or a list made with a fetch (next section)
([Template overrides](../../../creators/website-creators/template-overrides.md)).

To add an event: open the Event calendar, **Create new** > **Event**, fill Short title and From Time (and To Time),
the text with place and accessibility, and **Send for publishing** ([Creating and editing content](../../../editing-content.md)).

## An upcoming-events list

For the front page or a branch page, a template fetch lists the next events by their start time:

```text
{def $events = fetch( 'content', 'list', hash(
        'parent_node_id', $calendar_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'event' ),
        'attribute_filter', array( array( 'event/from_time', '>=', currentdate() ) ),
        'sort_by', array( 'attribute', true(), 'event/from_time' ),
        'limit', 5 ) )}
<ul>
{foreach $events as $event}
  <li><a href={$event.url_alias|ezurl}>{$event.name|wash}</a>,
      {$event.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}</li>
{/foreach}
</ul>
{undef $events}
```

`$calendar_node_id` is the node of your Event calendar. For one branch, add a relation attribute `branch` to Event and
filter on it, or tag events with the branch and list by tag ([Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md)).
A cached front page shows a past event until its cache expires; give the block a cache expiry or a short `cache_ttl`
([View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md)).

## Changes, cancellations and sign-up

| Situation | Do |
|---|---|
| Time or place changes | edit the event; write the change at the top of the text ("Moved to <room>") |
| Event cancelled | keep the page, put "Cancelled" in the short title and the text; or set a Booking attribute to *cancelled* that the template shows; hide it after the date |
| Event full | the Booking attribute set to *full* |
| Sign-up needed | an information collector form on the event (name, e-mail, number of places), or a link to the booking system you use |

Sign-up forms collect personal data: ask only what the event needs, and remove the submissions after the event in
**Setup > Collected information** ([Collected information](../../../../guides/collected-information.md)). The
information collector does not count places; set the event to *full* by hand.

## The catalog: link it

The catalog (the library management system's public search) holds titles, holdings, availability, loans and reader
accounts. Rebuilding it as content objects would duplicate data that changes every minute, and would put reader data
where it does not belong. Instead:

| Need | Pattern |
|---|---|
| Search from the website | a plain search form in the pagelayout that sends the query to the catalog's search address with `method="get"` |
| A link to a title in a reading list | the catalog's permanent link to the record, in a Link field or rich text |
| "My account", renewals, reservations | a link to the catalog's sign-in; no account data on the website |
| New acquisitions | an RSS feed from the catalog, if it offers one, read by an RSS import ([RSS feeds](../../../../guides/rss-feeds.md)) |

A catalog search form is ordinary HTML in your design; give its field a visible label and its button a clear name.
An RSS import creates an object per feed item and needs the `rssimport` cronjob; review what it imports, because the
content comes from another system.

## Checklist

- [ ] The calendar view works with a keyboard and a screen reader, or the program list is offered beside it.
- [ ] Every event states date, time, place and the venue's accessibility in text.
- [ ] Past events leave the upcoming list without manual work.
- [ ] The catalog search box sends visitors to the catalog's results.
- [ ] No reader account data is stored in the CMS.

## Related pages

- [Public libraries](README.md)
- [Content model for public libraries](content-model.md)
- [RSS feeds for your site](../../../creators/website-creators/rss-feeds-for-your-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
