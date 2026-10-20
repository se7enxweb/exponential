---
title: Listing upcoming events
audience: government, website-creator, developer
category: public-sector
order: 50
summary: Template fetches that list upcoming events on an Exponential site - by start time from now on, for one calendar or the whole site, for one place or tag - with cache expiry so past events drop out, and an RSS feed of events.
keywords: upcoming events, fetch, content list, content tree, attribute_filter, from_time, currentdate, sort_by attribute, main_node_only, cache block, expiry, RSS, events feed
type: help
---
# Listing upcoming events

This page is for website creators and developers who show "next events" on the front page, a topic page or a place
page. The shipped Event class stores its start in `from_time` (Date and time), which fetches can filter and sort on.
The fetch functions are those of `kernel/content/function_definition.php`; template basics are in
[Templates and design](../../../../guides/templates-and-design.md).

## Contents

1. [The next events of one calendar](#the-next-events-of-one-calendar)
2. [Across the whole site](#across-the-whole-site)
3. [For a place or a topic](#for-a-place-or-a-topic)
4. [Caching](#caching)
5. [A feed of events](#a-feed-of-events)

## The next events of one calendar

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
      <time>{$event.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}</time></li>
{/foreach}
</ul>
{undef $events}
```

`currentdate()` is the current time; events that started before now drop out. For events lasting several days that
should stay listed until they end, filter on `event/to_time` instead (and give every such event a To Time).

## Across the whole site

Use `fetch( 'content', 'tree', ... )` from the root node, add `'main_node_only', true()` so an event with two
locations appears once. A tree fetch over the whole site is heavier than a list: keep `limit` small and cache the
result.

## For a place or a topic

| Filter | How |
|---|---|
| Events at a place | add a Place relation to Event ([Modeling an event class](modeling-an-event-class.md)); on the Place page, `fetch( 'content', 'reverse_related_objects', ... )` lists the events that point to it, then keep those with a future start |
| Events on a topic | tags: eztags adds an attribute filter for `content/list` and `content/tree` (see `extension/eztags/doc/`); or the shipped Keywords category |
| Events of several calendars | a Multicalendar object relating the calendars ([What ships](what-ships.md)) |

## Caching

A list in a cached page shows past events until the cache is renewed. Choose one:

| Way | Effect |
|---|---|
| A cache block with an expiry, for example an hour | the list is at most an hour old |
| A subtree expiry on the calendar | publishing an event renews the list |
| `cache_ttl` on the full view of the page | the page is rebuilt after that many seconds |

[View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md) explains the
options; combine an expiry (for time passing) with a subtree expiry (for new events).

## A feed of events

An RSS export of the calendar folder, class Event, with the title from `short_title` and the description from `text`
(**Setup > RSS**, [RSS feeds](../news-and-alerts/rss-feeds.md)). RSS orders items by publication, not by event date;
say so where you offer it. An iCalendar feed is not shipped.

## Related pages

- [Events](README.md)
- [Calendar views](calendar-views.md)
- [Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
