# Pages that expire on time: cache_until

Read this page if a page of your site shows something that changes at a known time: the next events, an article of
the day, opening hours, a countdown, an offer that ends on Friday. It explains what Exponential 6.0.15 adds for that,
the template operators `cache_until` and `seconds_until`, what each cache of the installation does with the time a
template names, the decisions taken for each one, how to see it working, and what stays as it was.

Related: [Caching time-dependent content](../../guides/caching-time-dependent-content.md) (the guide, with complete
templates), [cache_until and seconds_until](../../help/designers/templates/cache-until.md) (template reference),
[Upgrading to 6.0.15: pages that expire on time](../../bc/6.0/cache-until.md) (upgrade notes),
[Caching layers](../../help/hosting/performance/caching-layers.md) (every cache in order).

## In short

- A template says **until when its output is true**: `{cache_until( 'tomorrow' )}`,
  `{cache_until( $event.data_map.from_time.content.timestamp )}`, `{cache_until( '+15 minutes' )}`. The operator prints
  nothing.
- **Every page cache** of the installation then keeps the page no longer than that: the content view cache, the
  template-block cache (`{cache-block}`), the role-aware HTTP cache, the response headers (`Cache-Control` `max-age`
  and `s-maxage`, `Expires`), and through them Velocity's response cache, a CDN and the browser. LiteSpeed's page
  cache gets a header of its own. The static cache is the exception: it has no lifetime and does not honor it.
- `{cache_until( 0 )}` keeps the page out of every cache, as `cache_ttl=0` always did, but it works in any template.
- A call **works wherever the template runs**: the full view, an `{include}`, the pagelayout, a menu, a footer, an
  Exponential Layouts block, the body of a `{cache-block}`, a Smarty or Twig template. Several calls: **the earliest
  wins**.
- Time strings are read in the **site's time zone** (`site.ini [TimeZoneSettings] TimeZone`), and a time of day that
  has passed today means its next occurrence: `'18:00'` at 19:30 is tomorrow at 18:00.
- `seconds_until` returns the seconds from now until such a time, for templates that print or compute with it.
- The **role-aware HTTP cache** now keeps a page only for the time that is left. Before, it took the view's original
  `cache_ttl` as a fresh lifetime, so a page replayed from the view cache late in the evening could live on until the
  next afternoon (fixed, see [The bug this fixes](#the-bug-this-fixes)).
- New installations write a **time zone** into `settings/override/site.ini.append.php` (`exp:install --timezone=`,
  kickstart.ini `[language_options] TimeZone`, else derived from the primary language). Existing sites are not
  changed; Setup > System information notes an empty setting.
- A page without a call is **unchanged byte for byte**: same headers, same cache files. `cache_ttl` keeps working.

## Contents

1. [The problem](#the-problem)
2. [The operators](#the-operators)
3. [How a time is read](#how-a-time-is-read)
4. [Earliest wins, everywhere](#earliest-wins-everywhere)
5. [Where the expiry goes: every cache and the decision for it](#where-the-expiry-goes-every-cache-and-the-decision-for-it)
6. [The bug this fixes](#the-bug-this-fixes)
7. [Seeing it: debug output and headers](#seeing-it-debug-output-and-headers)
8. [cache_ttl and backward compatibility](#cache_ttl-and-backward-compatibility)
9. [The time zone](#the-time-zone)
10. [Settings](#settings)
11. [Limits](#limits)
12. [Code and tests](#code-and-tests)

## The problem

The caches of Exponential expire on **change**: publishing an object clears the view caches of the pages that show
it, and cache-blocks expire on publish too. That is right for content, and wrong for time. A list of upcoming events
is correct when it is rendered and wrong the moment the first event starts, although nobody published anything. The
"entry of the day" is wrong after midnight. "Open now" is wrong at closing time.

Until 6.0.15 a template had one tool for this: `{set-block scope=root variable=cache_ttl}N{/set-block}`, a lifetime
in seconds for the view cache. It had three gaps:

- it worked **only in the content view** (the full view and what it includes), never in the pagelayout, a menu, a
  footer, a layout block or a cache-block;
- it was a **lifetime**, not a time: a template had to compute "seconds until midnight" itself, and every later cache
  counted the same lifetime again from when it stored the page;
- the caches after the view cache did not honor it in full: the role-aware HTTP cache used the original `cache_ttl`
  as its own fresh lifetime, and Velocity's response cache, a CDN and the browser were never told.

`cache_until` replaces the lifetime with the **moment the output stops being true**, an absolute time that every
cache understands the same way.

## The operators

```tpl
{cache_until( 'tomorrow' )}                                   {* midnight in the site's time zone *}
{cache_until( 'next monday' )}
{cache_until( '+15 minutes' )}
{cache_until( '2026-12-24 18:00' )}
{cache_until( $event.data_map.from_time.content.timestamp )}  {* a Unix time *}
{$event.data_map.from_time.content.timestamp|cache_until}     {* the same, piped *}
{cache_until( 0 )}                                            {* keep this page in no cache *}

{def $seconds = 'tomorrow'|seconds_until}                     {* seconds from now, an integer *}
```

- **`cache_until( <time> )`** lowers the expiry of the page being rendered to that time, prints nothing (it returns an
  empty string) and can be called any number of times. The argument can also be piped in.
- **`seconds_until( <time> )`** returns the seconds from now until that time: an integer, `0` for a time that has
  passed, `0` (with a warning in the debug output) for a value that is not a time. It sets no expiry.
- Both are kernel operators (`kernel/common/expcacheuntiloperator.php`), available in every design and siteaccess.
  They are **evaluated when the template runs**, also in compiled templates: they have no template hints, so a
  constant argument such as `'tomorrow'` is never folded into the compiled file.
- In Smarty (`.stpl`) and Twig (`.twig`) templates the operators are bridged like every operator, as a function or as
  a modifier or filter ([Smarty: operators](../../guides/smarty-templates-operators.md),
  [Twig: operators](../../guides/twig-templates-operators.md)).

The [template reference](../../help/designers/templates/cache-until.md) lists every accepted argument with an
example; the [guide](../../guides/caching-time-dependent-content.md) has complete templates.

## How a time is read

| Argument | Meaning |
|---|---|
| an integer, a numeric string, a `DateTime` object | a Unix time |
| any other string | a date or relative time as PHP's `DateTime::modify()` reads it (`'tomorrow'`, `'+15 minutes'`, `'next monday'`, `'2026-12-24 18:00'`, `'midnight first day of next month'`), in the site's time zone |
| `0`, `false`, `'0'` | do not keep the page at all |
| a time at or before now | the same as `0`: the page is not kept, with a notice in the debug output |
| `null` (a missing variable), `''`, `true`, an array, an object that is not a `DateTime`, a string that is no time | ignored: no effect, a warning in the debug output |

**A time of day that has passed today means its next occurrence.** `'midnight'` and `'today'` mean the coming
midnight; `'noon'` at 15:00 is tomorrow noon; `'18:00'` at 19:30 is tomorrow at 18:00; `'monday'` on a Monday
evening is next Monday. Strings that go backwards are not rolled forward (`'-5 minutes'`, `'yesterday'`,
`'last monday'`, `'1 hour ago'`), and neither are fixed dates (`'2026-12-24 18:00'`): once they have passed, the page
is not kept.

**A number is always a Unix time.** `cache_until( 3600 )` is one hour after 1 January 1970, long past, so the page is
not cached at all; the debug notice says to write `'+3600 seconds'` for a lifetime.

**A date without a time keeps the current time of day.** `DateTime::modify( '2026-12-24' )` changes the date only:
rendered at 15:20, `'2026-12-24'` means 24 December at 15:20. Give the time: `'2026-12-24 00:00'`. The same applies to
`'first day of next month'` (write `'midnight first day of next month'`). Checked against the code at several clocks
on 2026-11-15.

**Days with a clock change** are read with the zone's own rules: `'tomorrow'` is the real local midnight, also on the
night the clocks go forward or back.

## Earliest wins, everywhere

A template does not set a variable; it reports to the request (`expCacheUntil`). Each call can only bring the expiry
earlier, never later: `min( current, new )`. So the full view can say "until the next event starts", the pagelayout's
footer "until midnight" and a layout block "until 18:00", and the page expires at whichever comes first.

Because it reports to the request, a call counts wherever it runs: in the content view and what it includes, in the
pagelayout and its includes, in menus and footers, in Exponential Layouts blocks rendered in the pagelayout, and in
the body of a `{cache-block}` (where it also limits the block, see below).

## Where the expiry goes: every cache and the decision for it

Each of these applies only when a template set an expiry. A request without a call is exactly as before.

### 1. The content view cache

When a view is generated, a scope is open: the earliest `cache_until` of the view's templates, and `cache_ttl` taken
as `time() + cache_ttl`, are stored in the cache file as an absolute time (`$Result['cache_expires']`, and
`cache_expires_source` naming what set it). The entry expires then; the debug log says
`Content cache is expired by cache_until (<time>)`.

When the entry is **replayed**, it reports its expiry to the request again, so every cache after it gets the
**remaining** time, not the original lifetime. Entries written before 6.0.15 carry only `cache_ttl`; their expiry is
the file time plus `cache_ttl`, also as remaining time.

`cache_until( 0 )` in the view is `cache_ttl=0`: the view stores its "no cache" marker, as `cache_ttl=0` always did.
The marker stays until the node's view cache is cleared (for example on the next publish), and the page is rendered
on every request meanwhile.

### 2. The template-block cache

A `{cache-block}` whose body called `cache_until` is stored with its expiry (an internal mark at the start of the
file; blocks without a call are stored exactly as before). The block **expires at that time**, in addition to its own
`expiry=` (default 7200 seconds) and the publish rules. When it is read from the cache, it reports its expiry to the
page again, as if the call had run. `cache_until( 0 )` inside a block: the block is not stored.

So **`expiry=` is not needed for time-dependent blocks**: the block lives exactly until its content changes.

**Decision:** the `expiry=` of a block does **not** limit the page. It is a cache granularity ("render this again at
least every two hours"), not a deadline of the content. Only `cache_until` is.

The Smarty `{expcacheblock}` and the Twig `{% expcacheblock %}` use the same block cache and behave the same.

### 3. The role-aware HTTP cache

The role-aware HTTP cache (`settings/httpcache.ini`; the early exit under Apache, PHP-FPM and FrankenPHP; Velocity's
application cache, which uses the same code) now treats the expiry as a deadline:

- an entry lives `min( expiry - now, MaxAge )` seconds and keeps the expiry;
- it is **never served stale** after the expiry (no stale-while-revalidate for such entries). **Decision:** a deadline
  is a deadline; "open now" one minute after closing is wrong, however busy the page;
- nothing is stored when the expiry has already come: `X-Exp-Cache: BYPASS (cache_until)`;
- a lookup at or after the expiry is a miss with the reason "expired (cache_until)": the page rendered then says
  `X-Exp-Cache: MISS (expired (cache_until))`;
- an answer from it (`X-Exp-Cache: HIT`) to an anonymous visitor carries `Cache-Control: public, max-age=` the
  smaller of 300 and the seconds left, instead of a fixed 300. Signed-in visitors keep
  `private, no-cache, must-revalidate`;
- stored entries carry the header `X-Exp-Cache-Until` with the expiry.

### 4. The response headers of a rendered page

After the pagelayout has run, and before the page is sent, the kernel applies the expiry to the response headers
(`expCacheUntil::sendResponseHeaders()`):

| Header | What happens |
|---|---|
| `Cache-Control` | every `max-age` and `s-maxage` above the remaining seconds is lowered to them; nothing is ever raised |
| `Cache-Control` without `max-age` and `s-maxage` (the kernel default `no-cache, must-revalidate`), not `private` or `no-store` | `s-maxage=<remaining>` is added when the remaining time is `0`, or below Velocity's `velocity.ini [CacheSettings] DefaultTtl` (30 seconds as shipped) while that cache is `Enabled=enabled` (as shipped); otherwise nothing is added, because it would raise the lifetime |
| `Expires` | lowered when it names a later time; the kernel's default date in 1997 stays |
| `Pragma: no-cache` | dropped only when the new `Cache-Control` has a positive `max-age` and no `no-cache` |
| `X-LiteSpeed-Cache-Control` | `no-cache` when the remaining time is 0 (always); `max-age=<remaining>` for anonymous visitors only with `[CacheUntilSettings] LiteSpeedMaxAge=enabled` |
| `X-Exp-Cache-Until` | the expiry as ISO 8601 in the site's zone (`2026-11-16T00:00:00+01:00`), or `0`; with debug output on, also `; source="<what set it>"` |

Layers after this one still have the last word, unchanged: a page carrying the debug report is sent
`private, no-store`, and a page with a form token `private, no-cache`.

### 5. Velocity's response cache

Velocity keeps an anonymous page for its `s-maxage`, else its `max-age`, else `DefaultTtl` seconds. The headers above
make sure it keeps a rendered page no longer than the expiry: below `DefaultTtl` the page carries `s-maxage`, a page
that must not be kept carries `s-maxage=0`. An answer of the HTTP cache that Velocity stores carries the lowered
`max-age` of point 3. The Velocity engine itself is unchanged.

### 6. A CDN, Cloudflare

A CDN that respects the origin's headers (Cloudflare: Edge TTL "Use cache-control header if present") keeps a page no
longer than the lowered `max-age` or `s-maxage`. An edge TTL set to override the origin cannot be limited by the
application: purge there, or use a shorter edge TTL for those pages. See [CDN](../../help/hosting/performance/cdn.md).

### 7. LiteSpeed's page cache

A page that must not be kept always gets `X-LiteSpeed-Cache-Control: no-cache`, which only ever keeps a page out.
LiteSpeed keeps a page for the `max-age` of its rewrite rule, often whatever the response's `Cache-Control` says; with
`site.ini [CacheUntilSettings] LiteSpeedMaxAge=enabled` an anonymous visitor's page with an expiry also gets
`X-LiteSpeed-Cache-Control: max-age=<remaining>`, which LiteSpeed always reads. It is opt-in because that header could
also make a page cacheable that the rewrite rules do not cache. See
[Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md#pages-that-expire-on-time-cache_until).

### 8. The static cache: not honored

**Decision:** the static cache does not honor the expiry. Its files have no lifetime; they are regenerated on publish
and served by the web server without PHP, so nothing could expire them on time. Keep pages that call `cache_until`
out of it (leave them out of `staticcache.ini [CacheSettings] CachedURLArray[]`, which lists only what is cached,
and out of `AlwaysUpdateArray[]`, which regenerates on publish), regenerate them by a cron
job at the time (`php bin/php/makestaticcache.php`), or use the role-aware HTTP cache instead. See
[Static cache](../../help/hosting/performance/static-cache.md#pages-that-expire-on-time).

### 9. Browsers

Browsers read the same `Cache-Control` and `Expires`. With the shipped headers (`no-cache, must-revalidate`) they keep
nothing; with cacheable headers for anonymous visitors (`[HTTPHeaderSettings]`) the `max-age` is lowered to the time
that is left.

## The bug this fixes

The role-aware HTTP cache stored a page with the view's original `cache_ttl` as a fresh lifetime. A full view that
set `cache_ttl` at 09:00 to last until midnight (54000 seconds) was replayed from the view cache at 23:00; the HTTP
cache stored it then for 54000 seconds more (capped by `MaxAge`), so with a large `MaxAge` the page lived until about
14:00 the next day. Now the HTTP cache gets the absolute expiry and keeps the page for the time that is left: one
hour, until midnight. Test CU-07 replays exactly this case.

## Seeing it: debug output and headers

- **Debug bar**, Rendering tab: a chip **Until HH:MM** (green) or **Not cached** (amber). Its tooltip names the
  expiry, the seconds left, what set it and the number of calls ([The debug bar](exp-debug-bar.md)).
- **Debug output**, a notice at the end of the page:
  `cache_until: kept until 2026-11-16T00:00:00+01:00 (3600 s), set by <template>, 2 calls`, or
  `cache_until: the page is not kept, ...`.
- **Warnings** for ignored values (`cache_until() ignored in <template>: no time given (NULL)`), **notices** for times
  that have passed (`... the time 3600 has passed; for a lifetime in seconds write '+N seconds'; the page is not
  kept`).
- **What "set by" names**: the template that made the call. In a compiled template whose `{include}` was inlined
  when it was compiled, that is the including template (for example the pagelayout). A replayed view says
  `view cache: <what set it originally>`, a block read from its cache `cache-block`, `cache_ttl` `cache_ttl=N`.
- **Headers**: `X-Exp-Cache-Until` on every page with an expiry; `X-Exp-Cache` from the HTTP cache shows
  `BYPASS (cache_until)`, `MISS (...)` or `HIT`.

```bash
curl -sI https://example.com/events | grep -i -E 'x-exp-cache|cache-control|expires'
```

Seen on the development server on 2026-11-15, a cache-block with `{cache_until( '+90 seconds' )}` in the pagelayout:
the first request answered
`X-Exp-Cache-Until: 2026-11-15T11:00:05-08:00; source="design/standard/templates/cache_until_probe_block.tpl"`, the
second, with the block read from the cache, the same time with `source="cache-block"`.

## cache_ttl and backward compatibility

- `{set-block scope=root variable=cache_ttl}N{/set-block}` keeps working: when the view is generated it becomes
  `cache_until( time() + N )`; `cache_ttl=0` is `cache_until( 0 )`. No setting changes.
- `cache_ttl` still works only in the content view; `cache_until` works everywhere. Migrate
  `{set-block scope=root variable=cache_ttl}<seconds until midnight>{/set-block}` to `{cache_until( 'tomorrow' )}`.
- Template-block files written before 6.0.15 keep working (no mark, no expiry).
- Pages without a call: unchanged.
- Smaller differences: a page with `cache_ttl` above 0 in the HTTP cache gets no stale-while-revalidate past its
  expiry; `Cache-Control` gains `s-maxage` when the time left is below Velocity's `DefaultTtl`, and `cache_ttl=0`
  pages now carry `s-maxage=0`, so Velocity no longer keeps them for `DefaultTtl` seconds.

Details: [Upgrading to 6.0.15: pages that expire on time](../../bc/6.0/cache-until.md).

## The time zone

Time strings are read in `site.ini [TimeZoneSettings] TimeZone`, else in PHP's default zone (`date.timezone`). A
German site on a server whose PHP runs in UTC would otherwise begin "tomorrow" at 01:00 local time, and one in
America/Los_Angeles at 09:00.

**New installations** write the setting to `settings/override/site.ini.append.php`, chosen in this order:

1. the zone asked for: `./console exp:install --timezone=Europe/Berlin`, or kickstart.ini `[language_options]
   TimeZone=Europe/Berlin` (an unknown zone stops `exp:install` with an error, and the kickstarter at the language
   step);
2. else the zone of the primary language's country, when the country has one main zone: `ger-DE` Europe/Berlin,
   `eng-GB` Europe/London, `fre-FR` Europe/Paris, `nor-NO` Europe/Oslo, `ser-SR` (Serbian) Europe/Belgrade, and so on.
   Countries with several zones of real weight (the United States, Canada, Australia, Brazil, Russia, Mexico,
   Indonesia, ...) are never guessed;
3. else php.ini's `date.timezone`;
4. else UTC.

`exp:install` prints the zone and why, in the dry run and in its summary (`Time zone: Europe/Berlin (from ger-DE)`).
The web setup wizard uses rules 2 to 4, with no new form field. A site package may still write its own value
afterwards. The multisite installers (the `sevenx_multisite` and `cjw_multisite` packages) go through the same step
and get the zone too.

**Existing sites are not changed**: changing a site's zone moves every date it shows, so there is no upgrade step.
Setup > System information shows a note "The site has no time zone of its own" when `TimeZone` is empty and PHP has
a `date.timezone` (and, as before, a warning when neither is set). Set it yourself:

```ini
[TimeZoneSettings]
TimeZone=Europe/Berlin
```

in `settings/override/site.ini.append.php`, then `./console exp:velocity deploy` (or clear the INI cache with
`php bin/php/ezcache.php --clear-tag=ini` and reload PHP-FPM).

## Settings

| Setting | Default | Meaning |
|---|---|---|
| `site.ini [CacheUntilSettings] LiteSpeedMaxAge` | `disabled` | `enabled`: anonymous visitors' pages with an expiry also get `X-LiteSpeed-Cache-Control: max-age=<seconds left>` |
| `site.ini [TimeZoneSettings] TimeZone` | empty (new installations: written by the installer) | the zone time strings are read in |
| `velocity.ini [CacheSettings] DefaultTtl` | `30` | read only: below it, a page with an expiry gets `s-maxage` |
| `httpcache.ini [HttpCacheSettings] MaxAge` | `3600` | the HTTP cache keeps a page `min( time left, MaxAge )` |

Nothing has to be switched on: every cache except LiteSpeed's optional header honors the expiry without a setting.

## Limits

- **The static cache** does not honor it (see above).
- **An edge cache that ignores the origin's headers** cannot be limited from the application.
- **Velocity's own stale-while-revalidate** (`StaleWhileRevalidate=60`): when a page Velocity keeps expires, the one
  request that renders it again gets the new page; other requests arriving during that render may get the old copy
  (`X-Cache: STALE`), for at most the time the render takes. The HTTP cache does not do this for pages with an expiry.
- **The `no_cache` marker** of `cache_until( 0 )` in a view stays until the node's view cache is cleared, as for
  `cache_ttl=0`.
- **The resolution is one second**, and a page is rendered again on the first request after its expiry, not at the
  expiry. A countdown printed by the server shows the time of rendering: use a script in the browser, or a short
  expiry.
- **The debug output keeps 100 calls** per request; a call in a loop over a long list counts every run.
- **A number is a Unix time**, a date without a time keeps the current time of day: see
  [How a time is read](#how-a-time-is-read).

## Code and tests

- `kernel/classes/expcacheuntil.php`: the request's expiry, `resolve()`, `limit()`, scopes, the headers.
- `kernel/common/expcacheuntiloperator.php`: the operators; registered in `kernel/common/eztemplateautoload.php`.
- `kernel/classes/eznodeviewfunctions.php`: the view cache (`viewExpiry()`, `cachedViewExpiry()`).
- `lib/eztemplate/classes/eztemplatecacheblock.php`, `eztemplatecachefunction.php`: the template-block cache.
- `kernel/private/classes/httpcache/ezphttpcachelistener.php`, `ezphttpcachecontract.php`: the HTTP cache.
- `kernel/private/classes/ezpkernelweb.php`: the headers, after the pagelayout.
- `lib/ezutils/classes/expdebugbarreport.php`: the debug bar chip.
- `kernel/classes/expinstalltimezone.php`, `bin/php/install.php`, `kickstart.ini-dist`,
  `kernel/setup/steps/ezstep_language_options.php`, `ezstep_create_sites.php`, `kernel/classes/expsystemreport.php`:
  the time zone of new installations and the system information note.
- `tests/tests/kernel/classes/expCacheUntilTest.php` (CU-01 to CU-13) and `expSystemReportTest.php` (SR-14).
