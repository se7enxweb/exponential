---
title: cache_until and seconds_until
audience: designer, developer
category: templates
order: 135
summary: The operators cache_until and seconds_until in detail: a template says until when its output is true and every cache keeps the page no longer; parameters and return values, every accepted argument (Unix times, date and relative-time strings, 0, null), the roll-forward of times of day, earliest wins, the guard pattern, and the pitfalls (a number is always a Unix time, a date without a time, a past date).
keywords: cache_until, seconds_until, cache expiry, expire at, until midnight, tomorrow, cache_ttl, time-dependent content, view cache, cache-block, HTTP cache, max-age, s-maxage, X-Exp-Cache-Until, time zone, TimeZoneSettings
type: help
---
# cache_until and seconds_until

This page is for designers who write templates whose output changes at a known time: the next events, an entry of
the day, opening hours, a countdown, an offer that ends. `cache_until` tells every cache of the page until when the
output is true; `seconds_until` computes the seconds until such a time. Both are kernel operators of Exponential
6.0.15 (`kernel/common/expcacheuntiloperator.php`).

The complete examples are in the guide [Caching time-dependent content](../../../guides/caching-time-dependent-content.md);
what each cache does with the time is on the feature page [Pages that expire on time](../../../features/6.0/cache-until.md).

## Contents

1. [cache_until](#cache_until)
2. [seconds_until](#seconds_until)
3. [Accepted arguments](#accepted-arguments)
4. [Times of day roll forward](#times-of-day-roll-forward)
5. [Earliest wins](#earliest-wins)
6. [Where a call counts](#where-a-call-counts)
7. [The guard pattern](#the-guard-pattern)
8. [Pitfalls](#pitfalls)
9. [Short examples](#short-examples)
10. [Checking it](#checking-it)
11. [Smarty and Twig](#smarty-and-twig)

## cache_until

```tpl
{cache_until( <time> )}
{<time>|cache_until}
```

| | |
|---|---|
| Parameter | `until`: the moment the output stops being true (see [Accepted arguments](#accepted-arguments)); piped input when no parameter is given |
| Returns | an empty string: it prints nothing |
| Effect | lowers the expiry of the page being rendered to that time; every cache keeps the page, and the view or cache-block being generated, no longer |
| `0`, `false`, `'0'` | keep the page in no cache |
| Evaluated | when the template runs, also in compiled templates (never folded at compile time) |

## seconds_until

```tpl
{def $seconds = seconds_until( <time> )}
{def $seconds = <time>|seconds_until}
```

| | |
|---|---|
| Parameter | `until`: a time, read exactly as `cache_until` reads it |
| Returns | the seconds from now until that time, an integer; `0` for a time that has passed, for `0` and `false`; `0` with a warning in the debug output for a value that is not a time |
| Effect | none: it sets no expiry |

Use it to print or compute with the time left. A number printed by the server is frozen in every cache that keeps the
page; pair it with `cache_until` or let a script in the browser count (see the guide's
[countdowns](../../../guides/caching-time-dependent-content.md#countdowns)).

## Accepted arguments

| You pass | It means | Example |
|---|---|---|
| an integer | a Unix time | `$node.object.published`, `$event.data_map.from_time.content.timestamp` |
| a numeric string | a Unix time | `'1798761600'` |
| a `DateTime` object | its Unix time | a value handed in by PHP code |
| a date or relative-time string | read like PHP's `DateTime::modify()`, in the site's time zone | `'tomorrow'`, `'+15 minutes'`, `'next monday'`, `'2026-12-24 18:00'` |
| `0`, `false`, `'0'` | do not keep the page at all | `cache_until( 0 )` |
| a time at or before now | the same as `0`, with a notice in the debug output | `cache_until( 'yesterday' )` |
| `null` (a variable that is not set), `''`, `true`, an array, another object, a string that is no time | ignored: no effect, a warning in the debug output | `cache_until( $missing )` |

Strings that work (checked against the code, site zone Europe/Berlin, on a Monday at 10:00):

| String | Result |
|---|---|
| `'tomorrow'`, `'midnight'`, `'today'` | the coming midnight |
| `'noon'` | today at 12:00 (after 12:00: tomorrow) |
| `'18:00'`, `'today 18:00'` | today at 18:00 (after 18:00: tomorrow) |
| `'tomorrow 06:00'`, `'tomorrow noon'` | tomorrow at that time |
| `'+15 minutes'`, `'+1 hour'`, `'next hour'`, `'+2 days'` | that long from now (`'next hour'` is one hour from now, not the top of the hour) |
| `'+2 days midnight'` | midnight two days from now |
| `'next monday'`, `'saturday 10:00'` | that weekday at 00:00 or at the time given |
| `'monday'`, `'monday 09:00'` on a Monday | after the time has passed: next Monday |
| `'midnight first day of next month'` | 00:00 on the first of next month |
| `'first monday of next month 09:00'` | that day and time |
| `'midnight first day of january next year'` | 00:00 on 1 January next year |
| `'2026-12-24 18:00'`, `'December 24 18:00'`, `'2026-12-24 00:00'` | that date and time |

## Times of day roll forward

A time of day that has already passed today means **its next occurrence**:

- `'midnight'` and `'today'`: the next midnight;
- `'noon'` at 15:00: tomorrow at noon;
- `'18:00'` at 19:30: tomorrow at 18:00;
- `'monday'` on a Monday evening: next Monday.

Not rolled, and therefore "not kept" once passed: strings that go backwards (`'-5 minutes'`, `'yesterday'`,
`'last monday'`, `'1 hour ago'`) and fixed dates (`'2026-12-24 18:00'`, `'24.12.2026'`, `'12/24/2026'`).

This is what makes opening hours simple: `{cache_until( '09:00' )}` always means the next 09:00.

## Earliest wins

Every call can only bring the expiry earlier. With

```tpl
{cache_until( 'tomorrow' )}
{cache_until( '18:00' )}
{cache_until( '+2 hours' )}
```

rendered at 17:00, the page expires at 18:00; rendered at 17:30, at 18:00 too; rendered at 10:00, at 12:00. Order does
not matter, and a later call never raises an earlier one. That is why any template can call it without knowing what
the others said.

## Where a call counts

`cache_until` reports to the request, not to a template variable. A call counts wherever the template runs:

| Template | What it limits |
|---|---|
| the full view and its includes | the view cache entry, and the page |
| the pagelayout, its includes, menus, the footer | the page (HTTP cache, headers, Velocity, CDN) |
| an Exponential Layouts block rendered in the pagelayout | the page, and the cache-block around the layout if there is one |
| the body of a `{cache-block}` | the cache-block (it expires then) and the page; a block read from the cache reports its expiry again |
| a template included from any of these | the same as the one that includes it |

Compare `cache_ttl` (`{set-block scope=root variable=cache_ttl}...{/set-block}`), which only works in the content view
([Set-block and append-block](set-block-and-append-block.md)).

## The guard pattern

A variable that does not exist is `null`, and `null` is ignored with a warning. When the time may be missing (no next
event, an empty attribute), test first:

```tpl
{if $next_event}
    {cache_until( $next_event.data_map.from_time.content.timestamp )}
{/if}
```

An empty date attribute has the timestamp `0`, which means "do not keep the page". Test the attribute's content too:

```tpl
{if $node.data_map.publish_to.has_content}
    {cache_until( $node.data_map.publish_to.content.timestamp )}
{/if}
```

And a fixed date that may already be past (it would keep the page out of every cache from then on):

```tpl
{def $left = '2026-12-24 18:00'|seconds_until}
{if $left|gt( 0 )}
    {cache_until( '2026-12-24 18:00' )}
{/if}
```

## Pitfalls

- **A number is always a Unix time.** `{cache_until( 3600 )}` is one hour after 1 January 1970, long past: the page is
  not cached at all. For a lifetime write `{cache_until( '+3600 seconds' )}` or `'+1 hour'`. The debug notice says so.
- **A date without a time keeps the current time of day.** `'2026-12-24'` rendered at 15:20 means 24 December at
  15:20. Write `'2026-12-24 00:00'`. The same for `'first day of next month'`: write
  `'midnight first day of next month'`.
- **A month and day without a year** (`'January 1'`) is this year's: in November it has passed, and the page is not
  kept. Write `'midnight first day of january next year'` or the full date.
- **A date attribute object is not a time.** `$event.data_map.from_time.content` is an object; pass
  `.content.timestamp`.
- **`null` is ignored, `0` is not.** A missing variable changes nothing; an empty date (`0`) keeps the page out of
  every cache. Use the [guard pattern](#the-guard-pattern).
- **`seconds_until` sets no expiry.** Printing "3 days left" in a cached page needs a `cache_until` as well.
- **A cache-block's `expiry=` does not limit the page.** It only decides how often the block is rendered again. A
  deadline is `cache_until`.
- **The static cache** does not honor `cache_until`: keep such pages out of it
  ([Static cache](../../hosting/performance/static-cache.md#pages-that-expire-on-time)).
- **Times are read in the site's time zone** (`site.ini [TimeZoneSettings] TimeZone`, else PHP's). An empty setting
  on a server in UTC makes `'tomorrow'` mean midnight in London, not in Berlin.

## Short examples

```tpl
{* until midnight: an entry of the day *}
{cache_until( 'tomorrow' )}

{* until the next 09:00, today's or tomorrow's: opening time *}
{cache_until( '09:00' )}

{* until the next event starts (a Unix time), when there is one *}
{if $events|count}{cache_until( $events[0].data_map.from_time.content.timestamp )}{/if}

{* the same, piped *}
{if $events|count}{$events[0].data_map.from_time.content.timestamp|cache_until}{/if}

{* until an offer ends, given as an attribute *}
{if $node.data_map.offer_end.has_content}{cache_until( $node.data_map.offer_end.content.timestamp )}{/if}

{* every 15 minutes: a block that shows the latest comments *}
{cache_until( '+15 minutes' )}

{* until next Monday: "this week's" list *}
{cache_until( 'next monday' )}

{* until the first of next month: a monthly archive box *}
{cache_until( 'midnight first day of next month' )}

{* a live ticker: never cached *}
{cache_until( 0 )}

{* a lifetime of one hour, written as a lifetime *}
{cache_until( '+3600 seconds' )}

{* seconds left, printed *}
{def $left = 'tomorrow'|seconds_until}
{cache_until( '+1 minute' )}
{div( $left, 3600 )|floor} hours left today

{* days until a date, changing at midnight *}
{def $days = div( '2026-12-24 00:00'|seconds_until, 86400 )|ceil}
{cache_until( 'tomorrow' )}
{if $days|gt( 0 )}{$days} days to Christmas Eve{/if}

{* inside a cache-block: no expiry= needed *}
{cache-block keys=array( 'today-box' )}
    {cache_until( 'tomorrow' )}
    {include uri='design:parts/today_box.tpl'}
{/cache-block}
```

## Checking it

- **Debug bar**, Rendering tab: **Until HH:MM** or **Not cached**; the tooltip names the expiry, the seconds left,
  what set it and how many calls there were.
- **Debug output**: `cache_until: kept until <time> (<seconds> s), set by <template>, <n> calls`; a warning for each
  ignored value, a notice for each time that has passed.
- **Headers**:

```bash
curl -sI https://example.com/events | grep -i -E 'x-exp-cache|cache-control|expires'
```

`X-Exp-Cache-Until: 2026-11-16T00:00:00+01:00` names the expiry (with debug output on, also
`; source="<template>"`, or `source="cache-block"` when it came from a stored block). In a compiled template whose
include was inlined at compile time, the source is the including template, for example the pagelayout. `X-Exp-Cache` is the role-aware
HTTP cache's answer.

## Smarty and Twig

The operators are bridged like every operator: in a `.stpl`, `{cache_until('tomorrow')}` as a function or
`{$event_start|cache_until}` as a modifier ([Smarty templates: operators](../../../guides/smarty-templates-operators.md));
in a `.twig`, as a function or a filter ([Twig templates: operators](../../../guides/twig-templates-operators.md)). The
Smarty `{expcacheblock}` and the Twig `{% expcacheblock %}` use the same block cache and honor a call in their body.

## Related pages

- [Caching time-dependent content](../../../guides/caching-time-dependent-content.md) (guide, complete templates)
- [Pages that expire on time](../../../features/6.0/cache-until.md) (every cache and the decision for it)
- [Upgrading to 6.0.15: pages that expire on time](../../../bc/6.0/cache-until.md)
- [Cache-block](cache-block.md)
- [Set-block and append-block](set-block-and-append-block.md)
- [Template compilation and cache](template-compilation-and-cache.md)
- [Caching for designers](../theme-design/caching-for-designers.md)
- [Dates, numbers and l10n](dates-numbers-and-l10n.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
