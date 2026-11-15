# Upgrading to 6.0.15: pages that expire on time

Exponential 6.0.15 adds the template operators `cache_until` and `seconds_until`: a template says until when its
output is true, and every cache of the page keeps it no longer than that. This page says what changes for an existing
installation that moves to the 6.0.15 line, what you may want to do, and what stays as it was.

Feature reference: [Pages that expire on time](../../features/6.0/cache-until.md) · Guide:
[Caching time-dependent content](../../guides/caching-time-dependent-content.md) · Template reference:
[cache_until and seconds_until](../../help/designers/templates/cache-until.md)

## In short

- **Nothing to do for pages that do not call `cache_until` and do not set `cache_ttl`.** Their headers and cache files
  are the same byte for byte.
- **`cache_ttl` keeps working**, now as an absolute expiry: `time() + cache_ttl` when the view is generated.
- **The role-aware HTTP cache keeps a page for the time that is left**, not for the view's original `cache_ttl`, and
  serves no stale copy after the expiry. This fixes pages that lived on in the HTTP cache long after their
  `cache_ttl`.
- **Pages with an expiry can carry `s-maxage`** (when less time is left than Velocity's `DefaultTtl`, and `s-maxage=0`
  for `cache_ttl=0`), so Velocity's response cache keeps them no longer than the expiry.
- **Answers from the HTTP cache** carry `max-age` = the smaller of 300 and the seconds left, instead of a fixed 300,
  for pages with an expiry.
- **New setting** `site.ini [CacheUntilSettings] LiteSpeedMaxAge=disabled` (opt-in for LiteSpeed's page cache).
- **The time zone**: new installations write `[TimeZoneSettings] TimeZone`. Existing sites are not changed; set it
  yourself if it is empty (recommended). Setup > System information shows a note while it is empty.

## Contents

1. [What changes](#what-changes)
2. [What you do](#what-you-do)
3. [The time zone of an existing site](#the-time-zone-of-an-existing-site)
4. [The new setting](#the-new-setting)
5. [Migrating templates (optional)](#migrating-templates-optional)
6. [Things to check after the upgrade](#things-to-check-after-the-upgrade)

## What changes

### Pages without a call: nothing

A request in which no template calls `cache_until` and no view sets `cache_ttl` has no expiry. The kernel then changes
no header, the view cache and the template-block cache write the same files as before, and the HTTP cache stores the
page exactly as before. Cache files written by 6.0.14 or earlier are read as before.

### cache_ttl becomes an absolute expiry

`{set-block scope=root variable=cache_ttl}N{/set-block}` in a full view is read, when the view is generated, as
`cache_until( time() + N )`. The view cache entry stores that time (`cache_expires`) and expires then, as before. What
is new is that every cache after it gets the **remaining** time:

- a view cache entry replayed later reports its expiry to the request, and the HTTP cache, the headers and Velocity
  get the seconds that are left;
- an entry written before the upgrade has only `cache_ttl`; its expiry is taken as the file time plus `cache_ttl`.

`cache_ttl=0` is `cache_until( 0 )`: the view stores its "no cache" marker as before.

### The HTTP cache: remaining time, no stale copy after the expiry (bug fix)

Before 6.0.15 the role-aware HTTP cache (`httpcache.ini`) stored a page with the view's original `cache_ttl` as a
fresh lifetime, capped by `MaxAge`. A full view that set `cache_ttl` at 09:00 to last until midnight (54000 seconds),
replayed from the view cache at 23:00, was stored then for up to 54000 seconds more: with a large `MaxAge` it lived
until about 14:00 the next day.

Now:

- an entry lives `min( expiry - now, MaxAge )` seconds: at 23:00, one hour;
- an entry with an expiry is **never served stale** after it: stale-while-revalidate (`StaleWhileRevalidate=60`) does
  not apply past the expiry. Pages with `cache_ttl` above 0 are affected by this; pages without an expiry keep their
  stale-while-revalidate;
- nothing is stored when the expiry has already come (`X-Exp-Cache: BYPASS (cache_until)`);
- an answer from the cache (`X-Exp-Cache: HIT`) to an anonymous visitor says `Cache-Control: public, max-age=` the
  smaller of 300 and the seconds left (before: always 300). Signed-in visitors keep
  `private, no-cache, must-revalidate`. Pages without an expiry keep `max-age=300`;
- stored entries carry the header `X-Exp-Cache-Until`.

Entries stored before the upgrade have no expiry and are served as before until they expire or are purged. To apply
the new rules at once, clear the HTTP cache (`php bin/php/ezcache.php --clear-id=exphttpcache --allow-root-user`).

### Response headers of pages with an expiry

After the pagelayout has run, the kernel adjusts the headers of a page with an expiry:

- every `max-age` and `s-maxage` of `Cache-Control` above the seconds left is lowered (never raised);
- the default `Cache-Control: no-cache, must-revalidate` gains `s-maxage=<seconds left>` when that is 0 or below
  `velocity.ini [CacheSettings] DefaultTtl` (30 seconds, while Velocity's response cache is enabled as shipped).
  In particular **`cache_ttl=0` pages now carry `s-maxage=0`**, so Velocity no longer keeps them for `DefaultTtl`
  seconds;
- `Expires` is lowered when it names a later time; the default date in 1997 stays;
- `Pragma: no-cache` is dropped only when the new `Cache-Control` has a positive `max-age` and no `no-cache`;
- `X-LiteSpeed-Cache-Control: no-cache` for a page that must not be kept;
- the new header `X-Exp-Cache-Until` names the expiry (with debug output on, also what set it).

If a proxy, CDN or monitoring check of yours compares headers exactly, expect `X-Exp-Cache-Until` and, on pages with
`cache_ttl`, the occasional `s-maxage`.

### Template-block files

A `{cache-block}` whose body calls `cache_until` is stored with an internal mark and its expiry at the start of the
file. Blocks without a call are stored exactly as before, and files written before the upgrade are read as before (no
mark, no expiry). Code of your own that reads template-block files directly (it should not) would see the mark on
blocks that use the new operator.

### The static cache

Unchanged. It does not honor the expiry: keep pages that call `cache_until` out of it
([Static cache](../../help/hosting/performance/static-cache.md#pages-that-expire-on-time)).

## What you do

Nothing is required. Recommended:

1. Set the site's time zone if it is empty ([below](#the-time-zone-of-an-existing-site)).
2. If the role-aware HTTP cache is on, clear it once after the upgrade, so pages stored with the old lifetimes go:
   `php bin/php/ezcache.php --clear-id=exphttpcache --allow-root-user`.
3. On LiteSpeed or OpenLiteSpeed with the page cache of `.htaccess_root`, decide on `LiteSpeedMaxAge`
   ([below](#the-new-setting)).
4. Over time, replace `cache_ttl` with `cache_until` where a page changes at a known time
   ([Migrating templates](#migrating-templates-optional)).

## The time zone of an existing site

`cache_until( 'tomorrow' )` means midnight in `site.ini [TimeZoneSettings] TimeZone`, else in PHP's `date.timezone`.
Installations made before 6.0.15 left the setting empty, so their sites follow the server: often UTC, or whatever
zone a container image has. A German site on such a server begins "tomorrow" at 01:00 or 02:00 local time.

**The upgrade does not change it.** Changing a site's zone moves every date the site shows (publishing dates, event
times, timed publishing), so it is your decision, not an upgrade step. Setup > System information shows the note
**"The site has no time zone of its own"** (information) while `TimeZone` is empty and PHP has a `date.timezone`, and
a warning, as before, when neither is set ([System information](../../guides/system-information.md)).

Set it in `settings/override/site.ini.append.php`:

```ini
[TimeZoneSettings]
TimeZone=Europe/Berlin
```

Then `./console exp:velocity deploy --allow-root-user`, or clear the INI cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) and reload PHP-FPM (and restart Velocity). Check a few
pages that show dates.

**New installations** get the setting written by the installer: `./console exp:install --timezone=<zone>` or
kickstart.ini `[language_options] TimeZone=<zone>`; without one, the zone of the primary language's country when it
has one main zone (`ger-DE` Europe/Berlin, `eng-GB` Europe/London, `fre-FR` Europe/Paris), else php.ini's
`date.timezone`, else UTC. An unknown zone stops `exp:install` with an error and the kickstarter at the language step.
The web setup wizard uses the same rules without the first one (it has no new form field); the multisite installers
(the `sevenx_multisite` and `cjw_multisite` packages) go through the same installation step and get the zone too. A
site package may still write its own value afterwards.

## The new setting

```ini
[CacheUntilSettings]
LiteSpeedMaxAge=disabled
```

in `settings/site.ini`. LiteSpeed keeps a page for the `max-age` of its rewrite rule, often whatever the response's
`Cache-Control` says. A page that must not be kept always gets `X-LiteSpeed-Cache-Control: no-cache`, which only keeps
pages out. With `LiteSpeedMaxAge=enabled` (in `settings/override/site.ini.append.php`), an anonymous visitor's page
with an expiry also gets `X-LiteSpeed-Cache-Control: max-age=<seconds left>`, so LiteSpeed keeps it no longer than the
expiry. LiteSpeed reads that header on every response, also on pages its rewrite rules do not cache: enable it only
when the pages that call `cache_until` are pages your LiteSpeed block caches
([Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md#pages-that-expire-on-time-cache_until)).

## Migrating templates (optional)

`cache_ttl` only works in the content view; `cache_until` works in every template, takes a time instead of a lifetime,
and reaches every cache. Typical replacements:

```tpl
{* before *}
{set-block scope=root variable=cache_ttl}900{/set-block}
{* after *}
{cache_until( '+15 minutes' )}

{* before: seconds until midnight computed in the template *}
{* after *}
{cache_until( 'tomorrow' )}

{* before: a short cache_ttl on an event list "to be safe" *}
{set-block scope=root variable=cache_ttl}400{/set-block}
{* after: until the next event starts *}
{if $events|count}{cache_until( $events[0].data_map.from_time.content.timestamp )}{/if}
```

Never write `cache_until( 900 )` for 900 seconds: a number is a Unix time, and 900 is in 1970, so the page would not be
cached at all. More: [Caching time-dependent content](../../guides/caching-time-dependent-content.md#migrating-from-cache_ttl).

## Things to check after the upgrade

- A page without a call: same headers as before (`curl -sI <url>`), no `X-Exp-Cache-Until`.
- A full view with `cache_ttl`: `X-Exp-Cache-Until` names a time about `cache_ttl` seconds after the view was
  generated; with the HTTP cache on, a `HIT` carries `max-age` at most the seconds left.
- Setup > System information: no "The site has no time zone of its own" note once `TimeZone` is set.
- The debug bar, Rendering tab: **Until HH:MM** on pages with an expiry.
