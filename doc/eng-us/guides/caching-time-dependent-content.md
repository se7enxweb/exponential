# Caching time-dependent content

This guide is for designers and developers whose pages show something that changes at a known time: the next events,
an article of the day, opening hours, a countdown, a notice for a holiday week, an offer with an end date. It shows
with complete templates how to keep such pages fast and correct at the same time, with the operators `cache_until`
and `seconds_until` of Exponential 6.0.15: one call says until when the output is true, and every cache of the page
keeps it no longer than that.

You need Exponential 6.0.15 or later and a design you can add templates to. Every template below is a complete file
in eZ template language (`.tpl`); put it in your design extension under the path given in its first comment. Time
needed: 30 minutes for the first example, a few minutes for each after it.

[Guides](README.md) · Related: [Pages that expire on time](../features/6.0/cache-until.md) (the feature, every cache
and the decision for it), [cache_until and seconds_until](../help/designers/templates/cache-until.md) (template
reference), [Upgrading to 6.0.15: pages that expire on time](../bc/6.0/cache-until.md), [Caches](caches.md)

## In short

- Write `{cache_until( <time> )}` in any template whose output stops being true at that time. It prints nothing.
- The time is a Unix time (`$event.data_map.from_time.content.timestamp`) or a string read in the site's time zone
  (`'tomorrow'`, `'18:00'`, `'+15 minutes'`, `'next monday'`, `'2026-12-24 18:00'`). A time of day that has passed
  today means its next occurrence.
- **The earliest call wins.** Call it as often as you like; you never need to compute a minimum.
- It works in the full view, the pagelayout, menus, footers, Exponential Layouts blocks and inside `{cache-block}`.
  A cache-block with a call needs no `expiry=`.
- `{cache_until( 0 )}` keeps a page out of every cache.
- Guard values that may be missing: `{if $next}{cache_until( $next... )}{/if}`.
- Set `site.ini [TimeZoneSettings] TimeZone` (new installations have it); otherwise "tomorrow" begins at midnight in
  whatever zone the server's PHP runs.
- Check it with `curl -sI <url> | grep -i -E 'x-exp-cache|cache-control|expires'` and the debug bar's **Until HH:MM**
  chip.

## Contents

1. [Before you start: the time zone](#before-you-start-the-time-zone)
2. [How it works in one minute](#how-it-works-in-one-minute)
3. [Upcoming events](#upcoming-events)
4. [Upcoming and ongoing events](#upcoming-and-ongoing-events)
5. [The entry of the day](#the-entry-of-the-day)
6. [Opening hours](#opening-hours)
7. [Countdowns](#countdowns)
8. [Pagelayout blocks: a header notice, the footer, a menu](#pagelayout-blocks-a-header-notice-the-footer-a-menu)
9. [Exponential Layouts blocks](#exponential-layouts-blocks)
10. [Inside a cache-block](#inside-a-cache-block)
11. [A live ticker that must not be cached](#a-live-ticker-that-must-not-be-cached)
12. [Publication windows](#publication-windows)
13. [Migrating from cache_ttl](#migrating-from-cache_ttl)
14. [Checking it](#checking-it)
15. [Common mistakes](#common-mistakes)

## Before you start: the time zone

`cache_until( 'tomorrow' )` means midnight in the **site's** time zone: `site.ini [TimeZoneSettings] TimeZone`, else
PHP's `date.timezone`. Look at what your site uses:

```bash
grep -A2 '^\[TimeZoneSettings\]' settings/override/site.ini.append.php
php -i | grep date.timezone
```

An installation made with 6.0.15 or later has the setting (the installer wrote it: `exp:install --timezone=`, kickstart.ini
`[language_options] TimeZone`, else the zone of the primary language's country). An older site usually has it empty,
and Setup > System information says "The site has no time zone of its own". Set it in
`settings/override/site.ini.append.php`:

```ini
[TimeZoneSettings]
TimeZone=Europe/Berlin
```

then deploy (`./console exp:velocity deploy --allow-root-user`), or clear the INI cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) and reload PHP-FPM. Changing the zone moves every date
the site shows, so check a few pages with dates afterwards.

## How it works in one minute

Every cache of a page now asks the same question: until when is this output true? A template answers with
`cache_until`. The view cache stores the answer with the view, a cache-block with the block, the HTTP cache with the
page, and the response headers lower `max-age` and `s-maxage` so Velocity's response cache, a CDN and the browser
follow. When a view or block is read back from its cache, it reports its expiry again, so every later cache gets the
time that is **left**. The static cache is the one cache that does not follow; see
[Static cache](../help/hosting/performance/static-cache.md#pages-that-expire-on-time).

The examples below use the `event` class of the ezwebin and media designs: a folder of `event` objects with the date
attributes `from_time` and `to_time`. Replace the identifiers with yours.

## Upcoming events

A full view of the events folder lists the next ten events. The list is correct until the first of them starts: then
it drops off the list. That moment is the expiry.

```tpl
{* design/<your design>/override/templates/full/event_calendar.tpl
   The next ten events below this folder. The list changes when the first one starts. *}
{def $now    = currentdate()
     $events = fetch( 'content', 'list', hash(
                   'parent_node_id',     $node.node_id,
                   'class_filter_type',  'include',
                   'class_filter_array', array( 'event' ),
                   'attribute_filter',   array( array( 'event/from_time', '>', $now ) ),
                   'sort_by',            array( 'attribute', true(), 'event/from_time' ),
                   'limit',              10 ) )}

<article class="event-calendar">
    <h1>{$node.name|wash}</h1>

    {if $events|count}
        {* the first event is the next one to start: keep the page until then *}
        {cache_until( $events[0].data_map.from_time.content.timestamp )}

        <ul class="event-list">
        {foreach $events as $event}
            <li>
                <time datetime="{$event.data_map.from_time.content.timestamp|datetime( 'custom', '%Y-%m-%dT%H:%i' )}">
                    {$event.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}
                </time>
                <a href={$event.url_alias|ezurl}>{$event.name|wash}</a>
            </li>
        {/foreach}
        </ul>
    {else}
        {* No event to wait for. A new event is published, and publishing clears this view anyway. *}
        <p>{'No upcoming events.'|i18n( 'design/mydesign/event' )}</p>
    {/if}
</article>
{undef $now $events}
```

Notes:

- **The guard** (`{if $events|count}`) matters: without it, `$events[0]` of an empty list is `null`, the call is
  ignored and the debug output shows a warning. That is harmless here, but the guard says what you mean.
- **No `cache_ttl`, no computing seconds.** The timestamp of the attribute is passed as it is.
- **Publishing still clears the view**: an event added, moved or changed is shown at once, as before.
- The `fetch` reads `currentdate()` at render time; the expiry makes sure the page is rendered again at the moment
  that value would give a different list.

## Upcoming and ongoing events

A list that shows events until they **end**, with a "now on" badge while they run, changes at two kinds of moments:
when an upcoming event starts (the badge appears) and when a running one ends (it drops off). Earliest wins, so you
call `cache_until` for every such moment and let the kernel pick the first:

```tpl
{* design/<your design>/override/templates/full/event_calendar.tpl
   Events that have not ended yet; "now on" while they run. *}
{def $now    = currentdate()
     $events = fetch( 'content', 'list', hash(
                   'parent_node_id',     $node.node_id,
                   'class_filter_type',  'include',
                   'class_filter_array', array( 'event' ),
                   'attribute_filter',   array( array( 'event/to_time', '>', $now ) ),
                   'sort_by',            array( 'attribute', true(), 'event/from_time' ),
                   'limit',              20 ) )
     $start  = 0
     $end    = 0}

<h1>{$node.name|wash}</h1>
<ul class="event-list">
{foreach $events as $event}
    {set $start = $event.data_map.from_time.content.timestamp
         $end   = $event.data_map.to_time.content.timestamp}

    {* the badge appears when it starts; the event disappears when it ends *}
    {if $start|gt( $now )}{cache_until( $start )}{/if}
    {if $end|gt( $now )}{cache_until( $end )}{/if}

    <li>
        {if and( $start|le( $now ), $end|gt( $now ) )}<span class="badge">{'Now on'|i18n( 'design/mydesign/event' )}</span>{/if}
        <a href={$event.url_alias|ezurl}>{$event.name|wash}</a>,
        {$start|l10n( 'shortdatetime' )} to {$end|l10n( 'shorttime' )}
    </li>
{/foreach}
</ul>
{undef $now $events $start $end}
```

The `{if ...|gt( $now )}` tests keep a time that has just passed out (it would mean "do not keep the page"), and an
event without an end date (`to_time` empty, timestamp 0) out as well.

## The entry of the day

A box that shows one article per day, picked from a folder by the day of the year, is correct until midnight:

```tpl
{* design/<your design>/templates/parts/entry_of_the_day.tpl
   One article per day from the folder with node id 120, chosen by the day of the year. *}
{def $folder_id = 120
     $total     = fetch( 'content', 'list_count', hash( 'parent_node_id', $folder_id,
                                                        'class_filter_type', 'include',
                                                        'class_filter_array', array( 'article' ) ) )}
{if $total|gt( 0 )}
    {def $day   = currentdate()|datetime( 'custom', '%z' )|int
         $entry = fetch( 'content', 'list', hash( 'parent_node_id', $folder_id,
                                                  'class_filter_type', 'include',
                                                  'class_filter_array', array( 'article' ),
                                                  'sort_by', array( 'published', true() ),
                                                  'offset', mod( $day, $total ),
                                                  'limit', 1 ) )}
    {* a new entry at midnight, in the site's time zone *}
    {cache_until( 'tomorrow' )}

    <aside class="entry-of-the-day">
        <h2>{'Entry of the day'|i18n( 'design/mydesign/parts' )}</h2>
        <a href={$entry[0].url_alias|ezurl}>{$entry[0].name|wash}</a>
    </aside>
    {undef $day $entry}
{/if}
{undef $folder_id $total}
```

Include it from the pagelayout or from a full view; the call counts in either. Because it is the pagelayout's
include, it also limits the HTTP cache and the headers of **every** page that shows the box, to midnight. That is what
you want: the box is on every page.

## Opening hours

A shop's opening hours: Monday to Friday 09:00 to 18:00, Saturday 10:00 to 14:00, closed on Sunday. The box says
"open now" or "closed", and today's hours. It changes at the next opening or closing time, and at midnight (the day
changes).

```tpl
{* design/<your design>/templates/parts/opening_hours.tpl
   "Open now" or "Closed", and today's hours. Changes at the next opening or closing time. *}
{* $hours: one entry per weekday, 0 = Sunday ... 6 = Saturday; false() = closed all day.
   $weekday: today's weekday in the same numbering. $now: the time of day as a number, 14:30 is 1430. *}
{def $hours   = array( false(),
                       array( '09:00', '18:00' ),
                       array( '09:00', '18:00' ),
                       array( '09:00', '18:00' ),
                       array( '09:00', '18:00' ),
                       array( '09:00', '18:00' ),
                       array( '10:00', '14:00' ) )
     $weekday = currentdate()|datetime( 'custom', '%w' )|int
     $now     = currentdate()|datetime( 'custom', '%H%i' )|int
     $today   = $hours[$weekday]
     $open    = false()}

{if $today}
    {def $opens  = $today[0]|explode( ':' )|implode( '' )|int
         $closes = $today[1]|explode( ':' )|implode( '' )|int}
    {if $now|lt( $opens )}
        {* closed, opens later today: '09:00' is today's 09:00 *}
        {cache_until( $today[0] )}
    {elseif $now|lt( $closes )}
        {set $open = true()}
        {* open: until closing time *}
        {cache_until( $today[1] )}
    {else}
        {* closed for today: tomorrow is another day *}
        {cache_until( 'tomorrow' )}
    {/if}
    {undef $opens $closes}
{else}
    {* closed all day *}
    {cache_until( 'tomorrow' )}
{/if}

<aside class="opening-hours">
    <p class="state">{if $open}{'Open now'|i18n( 'design/mydesign/parts' )}{else}{'Closed'|i18n( 'design/mydesign/parts' )}{/if}</p>
    {if $today}
        <p>{'Today'|i18n( 'design/mydesign/parts' )}: {$today[0]} to {$today[1]}</p>
    {else}
        <p>{'Closed today'|i18n( 'design/mydesign/parts' )}</p>
    {/if}
</aside>
{undef $hours $weekday $now $today $open}
```

Why it works:

- `currentdate()|datetime( 'custom', ... )` formats in PHP's zone, which the kernel sets from
  `[TimeZoneSettings] TimeZone`; `cache_until` reads its strings in the same zone. Keep the setting filled, and both
  agree.
- `{cache_until( '18:00' )}` before 18:00 means today at 18:00. You never need to build a date.
- After closing the box waits until midnight and then works out the next step (the opening time on a working day, or
  midnight again on Sunday). One render per day more than strictly needed, and no weekday arithmetic.

If you prefer to jump straight to the next opening, a time with a weekday works too: on Saturday after 14:00,
`{cache_until( 'monday 09:00' )}`; and since times of day roll forward, `{cache_until( '09:00' )}` after closing on a
working day already means tomorrow at 09:00.

**Holidays**: keep them as content (a folder of "closed on" dates) or in a small INI block, test the day before the
weekly hours, and use `{cache_until( 'tomorrow' )}` on a closed day.

## Countdowns

`seconds_until` gives the seconds until a time. Printed by the server, the number is frozen in every cache that
keeps the page, so a countdown that ticks needs the browser.

**A countdown that ticks.** The server prints the end time once, as data; a script of the design counts. The page can
then be kept until the end itself:

```tpl
{* design/<your design>/override/templates/full/offer.tpl
   An offer with an end date (attribute offer_end); a countdown while it runs. *}
{def $end = $node.data_map.offer_end.content.timestamp}

<article class="offer">
    <h1>{$node.name|wash}</h1>
    {attribute_view_gui attribute=$node.data_map.body}

    {if $end|gt( currentdate() )}
        {* the page changes when the offer ends *}
        {cache_until( $end )}
        <p class="countdown" data-until="{$end}">
            {'Offer ends'|i18n( 'design/mydesign/offer' )} {$end|l10n( 'shortdatetime' )}
        </p>
        {ezscript_require( 'countdown.js' )}
    {else}
        <p class="ended">{'This offer has ended.'|i18n( 'design/mydesign/offer' )}</p>
    {/if}
</article>
{undef $end}
```

```js
// design/<your design>/javascript/countdown.js: replaces the text with a ticking countdown
document.querySelectorAll('[data-until]').forEach(function (element) {
    var until = parseInt(element.getAttribute('data-until'), 10) * 1000;
    function tick() {
        var left = Math.max(0, Math.floor((until - Date.now()) / 1000));
        var d = Math.floor(left / 86400), h = Math.floor(left % 86400 / 3600),
            m = Math.floor(left % 3600 / 60), s = left % 60;
        element.textContent = d + 'd ' + h + 'h ' + m + 'm ' + s + 's';
        if (left > 0) { setTimeout(tick, 1000); }
    }
    tick();
});
```

The script is a file of the design, loaded by `ezscript_require`, so it passes the Content Security Policy without a
nonce. The text the server printed stays readable without the script.

**A countdown without a script.** Print what changes slowly and keep the page only as long as the number is right:

```tpl
{* design/<your design>/templates/parts/days_to_event.tpl
   "12 days to go", correct until midnight. *}
{def $days = div( '2026-12-24 00:00'|seconds_until, 86400 )|ceil}
{if $days|gt( 0 )}
    {cache_until( 'tomorrow' )}
    <p class="days-left">{$days} {'days to Christmas Eve'|i18n( 'design/mydesign/parts' )}</p>
{/if}
{undef $days}
```

Hours or minutes left would need `{cache_until( 'next hour' )}` (an hour from now) or `{cache_until( '+1 minute' )}`:
correct, but the page is rendered that often. For anything finer than a few minutes, use the script.

Note the full date with a time, `'2026-12-24 00:00'`. A date alone (`'2026-12-24'`) keeps the current time of day, so
the count would change at the time of rendering, not at midnight. And once the date has passed, `seconds_until`
returns 0, the `{if}` is false, and no call is made: a past fixed date in `cache_until` would keep the page out of
every cache.

## Pagelayout blocks: a header notice, the footer, a menu

Calls in the pagelayout and its includes run on **every request**, also when the content view comes from the view
cache. They limit the HTTP cache and the response headers of the page. Inside a `{cache-block}` they also limit the
block (see [Inside a cache-block](#inside-a-cache-block)).

**A header notice for a holiday week**, shown from 20 to 27 December:

```tpl
{* design/<your design>/templates/pagelayout/header_notice.tpl
   A notice shown from 20 December 00:00 to 27 December 00:00. *}
{def $from = '2026-12-20 00:00'
     $to   = '2026-12-27 00:00'}
{if $from|seconds_until|gt( 0 )}
    {* not yet: the page changes when the notice appears *}
    {cache_until( $from )}
{elseif $to|seconds_until|gt( 0 )}
    {* shown now: the page changes when it disappears *}
    {cache_until( $to )}
    <div class="site-notice" role="status">
        {'Our office is closed from 24 to 26 December.'|i18n( 'design/mydesign/pagelayout' )}
    </div>
{/if}
{* after the window: no call, the page is as before *}
{undef $from $to}
```

**The footer with the year**, kept in a cache-block for days, renewed at New Year:

```tpl
{* design/<your design>/templates/pagelayout/footer.tpl *}
{cache-block keys=array( 'footer' ) ignore_content_expiry expiry=86400}
    {cache_until( 'midnight first day of january next year' )}
    <footer class="site-footer">
        <p>&copy; {currentdate()|datetime( 'custom', '%Y' )} {ezini( 'SiteSettings', 'SiteName' )|wash}</p>
    </footer>
{/cache-block}
```

The block's `expiry=86400` still renders it once a day; the call adds the New Year deadline. On 31 December at 23:00
the block, the page in the HTTP cache and the headers all say "until 00:00".

**A menu item for a season**: an "Advent calendar" link in December only. The menu is kept in a cache-block with the
usual keys; the call limits it to the next switch:

```tpl
{* design/<your design>/templates/pagelayout/top_menu.tpl *}
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) subtree_expiry=2}
    {def $season_from = '2026-12-01 00:00'
         $season_to   = '2026-12-25 00:00'
         $in_season   = false()}
    {if $season_from|seconds_until|gt( 0 )}
        {cache_until( $season_from )}
    {elseif $season_to|seconds_until|gt( 0 )}
        {set $in_season = true()}
        {cache_until( $season_to )}
    {/if}
    <nav class="top-menu">
        {include uri='design:pagelayout/menu_items.tpl'}
        {if $in_season}<a href={'/Advent-calendar'|ezurl}>{'Advent calendar'|i18n( 'design/mydesign/pagelayout' )}</a>{/if}
    </nav>
    {undef $season_from $season_to $in_season}
{/cache-block}
```

Without the call, the block would show the link up to two hours late (its default `expiry`), and a page in the HTTP
cache up to `MaxAge` late.

## Exponential Layouts blocks

The blocks of an Exponential Layouts layout are rendered in the pagelayout, so a call in a block template counts for
the page. The media design keeps the whole rendered layout in a `{cache-block}` in its pagelayout
(`extension/sevenx_themes_media/design/media/templates/pagelayout.tpl`, keyed by the layout, the node, the page content
and the visitor's roles). A call in any block now limits that cache-block too: the layout is rendered again at the
expiry, not up to two hours later.

The simplest block for your own template is the **Template block** (`tpl_block`): an editor adds it to a zone and
names a template in its `block_name` parameter; it renders `design:explayouts/tpl_block/<block_name>.tpl`. With the
name `upcoming_events`:

```tpl
{* design/<your design>/templates/explayouts/tpl_block/upcoming_events.tpl
   A layout block: the next five events of the whole site. *}
{def $now    = currentdate()
     $events = fetch( 'content', 'tree', hash(
                   'parent_node_id',     2,
                   'class_filter_type',  'include',
                   'class_filter_array', array( 'event' ),
                   'attribute_filter',   array( array( 'event/from_time', '>', $now ) ),
                   'sort_by',            array( 'attribute', true(), 'event/from_time' ),
                   'limit',              5 ) )}
{if $events|count}
    {cache_until( $events[0].data_map.from_time.content.timestamp )}
    <section class="block-upcoming-events">
        <h2>{'Coming up'|i18n( 'design/mydesign/blocks' )}</h2>
        <ul>
        {foreach $events as $event}
            <li><a href={$event.url_alias|ezurl}>{$event.name|wash}</a>
                <time>{$event.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}</time></li>
        {/foreach}
        </ul>
    </section>
{/if}
{undef $now $events}
```

The same call works in any block view template, for example a list view type of your design under
`design:explayouts/block/list/`. The shared layouts (header, footer, prefooter) are rendered on every page that links
their zones, so a call there limits every one of those pages.

## Inside a cache-block

A `{cache-block}` whose body calls `cache_until` is stored with that expiry and expires then, whatever its
`expiry=`. Read back from the cache, it reports the expiry to the page again. So **time-dependent blocks need no
`expiry=`**: they live exactly as long as their content is true.

```tpl
{* Before 6.0.15: a guess. Up to an hour late, and rendered 24 times a day. *}
{cache-block keys=array( 'today-box' ) expiry=3600}
    {include uri='design:parts/entry_of_the_day.tpl'}
{/cache-block}

{* 6.0.15: the include calls cache_until( 'tomorrow' ). expiry=0 and ignore_content_expiry leave
   the deadline to that call: on time at midnight, and rendered once a day. *}
{cache-block keys=array( 'today-box' ) ignore_content_expiry expiry=0}
    {include uri='design:parts/entry_of_the_day.tpl'}
{/cache-block}
```

`expiry=0` means "no time limit of its own", so only the call (and the keys) decide. Keep the default when the block
also shows content that is not time-dependent and should be renewed regularly.

Things to know:

- `{cache_until( 0 )}` inside a block: the block is not stored, and the page is not kept either.
- The block's own `expiry=` never limits the page: it is how often the block is rendered, not a deadline.
- The Smarty `{expcacheblock}` and the Twig `{% expcacheblock %}` behave the same.

## A live ticker that must not be cached

A full view of a live ticker (sports results, a status page) changes all the time. Keep it out of every cache:

```tpl
{* design/<your design>/override/templates/full/live_ticker.tpl
   Updated every few seconds by an editor or an import: never cached. *}
{cache_until( 0 )}
<article class="live-ticker">
    <h1>{$node.name|wash}</h1>
    {def $entries = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                    'sort_by', array( 'published', false() ),
                                                    'limit', 30 ) )}
    <ol class="ticker">
    {foreach $entries as $entry}
        <li><time>{$entry.object.published|l10n( 'shorttime' )}</time> {$entry.name|wash}</li>
    {/foreach}
    </ol>
    {undef $entries}
</article>
```

What happens: the view cache stores its "no cache" marker (as `cache_ttl=0` always did), the HTTP cache says
`X-Exp-Cache: BYPASS (cache_ttl=0)`, the headers carry `s-maxage=0` (so Velocity does not keep the page for
`DefaultTtl` seconds) and `X-LiteSpeed-Cache-Control: no-cache`, and `X-Exp-Cache-Until: 0`. Every request renders the
page.

That is expensive on a busy page. Often better: keep the page cacheable and load the ticker's list from a small view
or the REST API with a script, or keep the page for a few seconds with `{cache_until( '+10 seconds' )}`, which still
absorbs a burst of visitors.

## Publication windows

Content with a publication window, for example a class `teaser` with the date attributes `publish_from` and
`publish_to`: a list shows only the teasers whose window is open now. It changes when a hidden teaser's window opens
and when a shown teaser's window closes. Call `cache_until` for both, and let the earliest win:

```tpl
{* design/<your design>/templates/parts/teasers.tpl
   Teasers below node 140 whose window is open now; empty dates mean "no limit". *}
{def $now      = currentdate()
     $teasers  = fetch( 'content', 'list', hash( 'parent_node_id', 140,
                                                 'class_filter_type', 'include',
                                                 'class_filter_array', array( 'teaser' ),
                                                 'sort_by', array( 'priority', true() ) ) )
     $from     = 0
     $to       = 0
     $shown    = array()}
{foreach $teasers as $teaser}
    {set $from = cond( $teaser.data_map.publish_from.has_content, $teaser.data_map.publish_from.content.timestamp, 0 )
         $to   = cond( $teaser.data_map.publish_to.has_content, $teaser.data_map.publish_to.content.timestamp, 0 )}
    {if $from|gt( $now )}
        {* not open yet: the list changes when it opens *}
        {cache_until( $from )}
    {elseif or( $to|eq( 0 ), $to|gt( $now ) )}
        {set $shown = $shown|append( $teaser )}
        {* open now: the list changes when it closes *}
        {if $to|gt( 0 )}{cache_until( $to )}{/if}
    {/if}
{/foreach}

{if $shown|count}
<section class="teasers">
    {foreach $shown as $teaser}
        {node_view_gui content_node=$teaser view='line'}
    {/foreach}
</section>
{/if}
{undef $now $teasers $from $to $shown}
```

The empty-date tests are the guard: an empty date attribute has the timestamp 0, and `cache_until( 0 )` would keep the
page out of every cache.

The same applies to the **full view** of a teaser: when its window has closed, the page should say so. Put
`{if and( $to|gt( 0 ), $to|gt( currentdate() ) )}{cache_until( $to )}{/if}` at the top of the full view.

If the window should also hide the object from search and from every other list, that is a job for the publishing
side (a cronjob that hides or unpublishes it at the time), not for the cache. `cache_until` only makes the cached
pages follow.

## Migrating from cache_ttl

`{set-block scope=root variable=cache_ttl}N{/set-block}` keeps working, unchanged: when the view is generated it is
read as `cache_until( time() + N )`, and `cache_ttl=0` as `cache_until( 0 )`. Migrate when you touch a template:

| Before | After | Why |
|---|---|---|
| `{set-block scope=root variable=cache_ttl}900{/set-block}` | `{cache_until( '+15 minutes' )}` | the same, and works outside the full view |
| `{set-block scope=root variable=cache_ttl}0{/set-block}` | `{cache_until( 0 )}` | the same, and works in the pagelayout |
| `cache_ttl` computed as the seconds until midnight | `{cache_until( 'tomorrow' )}` | no arithmetic; correct also on days with a clock change |
| a short `cache_ttl` "to be safe" on an event list | `{cache_until( $next_event_start )}` | on time, and fewer renders |

The usual "until midnight" before and after:

```tpl
{* before: seconds until midnight, computed by hand, full view only *}
{def $now = currentdate()}
{set-block scope=root variable=cache_ttl}{sub( maketime( 0, 0, 0,
    $now|datetime( 'custom', '%n' ), sum( $now|datetime( 'custom', '%j' ), 1 ), $now|datetime( 'custom', '%Y' ) ),
    $now )}{/set-block}

{* after: anywhere *}
{cache_until( 'tomorrow' )}
```

The ezwebin templates that set `cache_ttl` (`full/event.tpl` 600, `full/event_calendar.tpl` 400,
`full/multicalendar.tpl` 900, `line/poll.tpl` 900) work as before. An event calendar copied into your design is a good
candidate: replace the 400 seconds with the start of the next event, as in [Upcoming events](#upcoming-events).

Do not write `{cache_until( 900 )}` for "900 seconds": a number is a Unix time, 900 is in 1970, and the page would not
be cached at all. Write `'+900 seconds'`.

## Checking it

**The headers.** Ask for the page twice as an anonymous visitor:

```bash
curl -sI https://example.com/Events | grep -i -E 'x-exp-cache|cache-control|expires'
```

A page with an expiry carries:

```text
X-Exp-Cache-Until: 2026-11-16T19:00:00+01:00
```

With debug output on for your address, the template that set it is named:
`X-Exp-Cache-Until: 2026-11-16T19:00:00+01:00; source="extension/mydesign/design/mydesign/override/templates/full/event_calendar.tpl"`.
When the view or a cache-block came from its cache, the source says `view cache: ...` or `cache-block`.

With the role-aware HTTP cache on, `X-Exp-Cache` says `MISS` on the first request and `HIT` on the second, with
`Cache-Control: public, max-age=` at most the seconds left (at most 300). After the expiry the next request is a
`MISS` again. A page that must not be kept says `X-Exp-Cache: BYPASS (cache_until)` (or `BYPASS (cache_ttl=0)` when
the call was in the full view).

Within the last 30 seconds before the expiry (Velocity's `DefaultTtl`, when its response cache is enabled in
`velocity.ini`, as shipped) the default `Cache-Control` gains `s-maxage=<seconds left>`; with cacheable headers for
anonymous visitors (`[HTTPHeaderSettings]`), `max-age` is lowered to the time that is left.

**The debug bar.** On the Rendering tab, the chip **Until 19:00** (green) or **Not cached** (amber); its tooltip
names the expiry, the seconds left, what set it and how many calls there were. The debug output has the line
`cache_until: kept until 2026-11-16T19:00:00+01:00 (5400 s), set by <template>, 1 call`, a warning for every value it
ignored and a notice for every time that had passed ([The debug bar](../features/6.0/exp-debug-bar.md)).

**On time.** Wait for the expiry (or test with `{cache_until( '+1 minute' )}`) and request the page again: it is
rendered once (`X-Exp-Cache: MISS`), then served from the caches again.

## Common mistakes

| Mistake | What happens | Write instead |
|---|---|---|
| `{cache_until( 3600 )}` for one hour | 3600 is a Unix time in 1970: the page is never cached | `{cache_until( '+1 hour' )}` |
| `{cache_until( '2026-12-24' )}` for midnight | a date alone keeps the current time of day | `{cache_until( '2026-12-24 00:00' )}` |
| `{cache_until( 'first day of next month' )}` | the current time of day on the 1st | `{cache_until( 'midnight first day of next month' )}` |
| `{cache_until( 'January 1' )}` in November | this year's 1 January, past: the page is never cached | `{cache_until( 'midnight first day of january next year' )}` |
| a fixed date that has passed, left in a template | the page is never cached from then on | guard with `seconds_until`: `{if '2026-12-24 18:00'|seconds_until|gt( 0 )}...{/if}` |
| `{cache_until( $node.data_map.end.content )}` | an object, not a time: ignored, with a warning | `.content.timestamp` |
| an empty date attribute passed as it is | timestamp 0: the page is never cached | `{if $attr.has_content}...{/if}` |
| `$list[0]...` of an empty list | `null`: ignored, with a warning | `{if $list|count}...{/if}` |
| `seconds_until` printed, no `cache_until` | the number is frozen in the caches | add `cache_until`, or count in the browser |
| `expiry=` on a cache-block as the deadline | the page is not limited by it, and the block is late | `cache_until` in the block's body |
| a page with `cache_until` in the static cache | the static file does not expire | keep it out of `CachedURLArray[]` ([Static cache](../help/hosting/performance/static-cache.md#pages-that-expire-on-time)) |
| `TimeZone` empty on a server in UTC | "tomorrow" begins at midnight UTC | set `[TimeZoneSettings] TimeZone` |
| `{cache_until( 0 )}` in the pagelayout "for testing" | no page of the site is cached any more | remove it after the test, or use `'+1 minute'` |

## Related pages

- [Pages that expire on time](../features/6.0/cache-until.md)
- [cache_until and seconds_until](../help/designers/templates/cache-until.md)
- [Upgrading to 6.0.15: pages that expire on time](../bc/6.0/cache-until.md)
- [Cache-block](../help/designers/templates/cache-block.md)
- [Caching for designers](../help/designers/theme-design/caching-for-designers.md)
- [Caching layers](../help/hosting/performance/caching-layers.md)
- [Page caching with LiteSpeed and OpenLiteSpeed](litespeed-page-cache.md#pages-that-expire-on-time-cache_until)
- [Caches](caches.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
