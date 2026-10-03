# Velocity response cache

*Applies to: Exponential Velocity 0.0.4.x. Related: [HTTP cache](../../bc/6.0/http-caching.md), [Velocity engines](../../bc/6.0/velocity-engines.md). History: [22 September](../../history/velocity/2026-09b.md), [25 to 30 September](../../history/velocity/2026-09e.md).*

## What it is

The response cache keeps rendered public pages and answers repeat requests inside the server process, before a worker is chosen. A hit costs no worker at all. It behaves like a small reverse proxy: conditional requests (`304`), `ETag`, `Age`, stale-while-revalidate, negative caching, compressed storage, and invalidation by a single touched file.

## Why use it

A cached public page is served in about 0.2 ms of CPU (measured on a 12-core host: a front page over TLS with gzip, about 2,950 pages a second from one server, 11,985 from four servers on one port). Rendering the same page takes hundreds of milliseconds. A busy page never sends every visitor to PHP at the same moment, because one request renews an expired page while the others are answered from the old copy.

## Turn it on

**The cache is off until a setting says `enabled: true`.** Since release 0.0.4.39 no cache setting means no cache (before it, the built-in default was on, and disabling the cache module did not stop it).

In a site file or a module such as `mods-available/cache.conf`:

```json
{ "Q": { "web": { "cache": {
  "enabled": true,
  "dir": "/var/cache/qbix/example.com"
} } } }
```

With a configuration directory, `qbixctl enmod cache` turns it on at the next restart and `qbixctl dismod cache` turns it off. A module that only sets `dir` or `defaultTtl` does not turn it on. The site file (`--config`) is read last, so `enabled: false` there wins over a module; a setting saved in the control panel wins over both.

Then let the application say how long a page may live:

```php
<?php
Q_Response::header('Cache-Control: public, max-age=300');
echo renderFeed();
```

Restart or reload the server so it picks up the setting.

## What is kept

A response is stored only when all of these hold:

- the request is a `GET` for a path that is not the server's own (`/Q/...` and `/.well-known/...` are never cached);
- the status is `200`, or `404`/`410` when `negativeTtl` is set;
- the request carries no `Authorization` header and none of the `skip.cookies`;
- the response sets no cookie;
- `Cache-Control` says neither `no-store` nor `private`.

The key is host, path, query string and the coding the body is stored in (gzip for clients that accept it). Brotli and gzip clients share one entry. A `q=0` in `Accept-Encoding` counts as "not accepted". A visitor's own `Cache-Control: no-cache` does not bypass the cache, so pressing reload cannot make the server render every page again. A cache warmer sends the header `x-cache-refresh` instead.

The lifetime is `s-maxage`, else `max-age`, else `defaultTtl` (default `0`, which stores nothing).

## Settings (all under `Q.web.cache`)

| Setting | Default | Meaning |
|---|---|---|
| `enabled` | `false` | Turn the cache on |
| `dir` | `<app>/files/cache/reverse` | Where entries and the marker live |
| `defaultTtl` | `0` | Lifetime in seconds for a response that states none |
| `skip.cookies` | `["Q_sid", "PHPSESSID"]` | Cookie names (matched by prefix) that mark a request as personal |
| `staleWhileRevalidate` | `0` | Seconds an expired page is still served while one request renders a new copy |
| `revalidateLockSeconds` | `30` | After this long a request renewing a stale page is presumed gone |
| `negativeTtl` | `0` | Seconds to keep `404` and `410` answers |
| `refreshHeader` | `x-cache-refresh` | Request header that renders and stores the page again |
| `generationFile` | `<dir>/.generation` | The generation marker |
| `apcu.enabled` | when APCu is usable | Keep small entries in APCu as well as on disk (needs `apc.enable_cli=1` under the CLI) |
| `apcu.maxSize` | `65536` | Largest body in bytes kept in APCu |
| `memory.maxEntries` | `0` | Pages kept in the server's own memory in front of APCu; `0` turns the layer off |
| `memory.maxBytes` | `33554432` | Most bytes of bodies kept in memory |
| `memory.maxEntrySize` | `65536` | Largest body kept in memory |
| `minifyHtml` | `false` | Collapse whitespace in cached HTML |
| `middleOut` | `false` | Compress stored bodies against a shared dictionary (measure first) |
| `fileMode` | `0666` less the umask | Mode of entry files |
| `dirMode` | `0755` | Mode of cache directories |
| `sweep.every` | `300` | Seconds between sweeps of expired files; `0` turns the sweep off |
| `sweep.budget` | `2000` | Most files one sweep looks at |
| `sweep.maxAge` | `0` | Also remove files older than this many seconds |
| `pauseFile` | none | While this file exists, neither this cache nor the application's cache answers (maintenance window) |

Where the entries are: APCu for small ones when usable, and one file per page on disk below `dir` in a two-level directory.

## Invalidate everything: the generation marker

A changed template or stylesheet is invisible to a cache that judges pages by what the application says. After a deploy, touch the marker:

```bash
touch /var/cache/qbix/example.com/.generation
qbixconsole cache:clear
```

Only the file's modification time counts. Every entry stored at or before that time is treated as a miss and replaced on its next request; each process reads the marker at most once a second, so every worker and every other server sharing the file sees it within a second. From Exponential, `exp:velocity cache clear` does the same and `exp:velocity deploy` calls it last ([Velocity engines](../../bc/6.0/velocity-engines.md)).

## An application's own cache

The response cache skips requests with a session cookie because it cannot tell visitors apart. An application that keeps rendered pages per visitor or role can be asked first, in the server process, so a hit never wakes a worker:

```json
{ "Q": { "web": { "appCache": {
    "file":  "/path/to/app/lib/PageCache.php",
    "class": "PageCache",
    "dir":   "/path/to/app/var/page-cache"
} } } }
```

`class` needs a static `fromDir($dir)` and a `serve(array $request)` that returns `[status, headers, body]` for a hit and `null` otherwise. The request carries `scheme`, `host`, `uri`, `method`, `cookies`, `acceptEncoding`, `ifNoneMatch`, and (since 0.0.4.35) `headers` and `port`. A request with a session cookie goes to the application's cache only; any other request is answered by the response cache first and the application's cache on a miss, and that answer is kept in the response cache. Because the response cache then answers first, point `Q.web.cache.generationFile` at a file every application purge rewrites (Exponential's `exp:velocity` uses the HTTP cache's state file).

## Reading it: headers and health

| Header | When |
|---|---|
| `X-Cache: HIT` | Served from the cache |
| `X-Cache: STALE` | Served from an expired copy while a new one renders |
| `Age` | Seconds since the entry was stored |
| `ETag` | On every stored page |

`/Q/health` (full figures need admin access) reports `hits`, `misses`, `hitRate`, `stale`, `hitsFrom.index|memory|apcu|disk`, `memory.*` and `apcu.*`. Mostly `disk` with APCu enabled means APCu is not doing its part: check `apc.enable_cli`.

## From the control panel

The panel's **Cache** tab (`/Q/panel`) offers switches for the cache, APCu and the memory layer, live hit rate, presets for the lifetimes, **Clear everything**, purge by URL or pattern, warm a page, and a browser of stored pages. Settings saved there live in the panel store and win over the file; only ten settings are taken from it (`enabled`, `defaultTtl`, `staleWhileRevalidate`, `negativeTtl`, `skip.cookies`, `apcu.enabled`, `apcu.maxSize`, `memory.maxEntries`, `memory.maxBytes`, `minifyHtml`). The API is under `/Q/api/cache`.

## Limits and traps

- **Do not cache personalised pages.** Anything with a cookie, `Authorization`, `private` or `no-store` is skipped; keep that true in your templates.
- **Upgrading across 0.0.4.29**: cached pages are filed under the coding they are stored in, so the first request for each page after the upgrade renders again.
- **Upgrading to 0.0.4.39**: an installation that relied on the old default with no `enabled` anywhere stops caching; add `{"Q":{"web":{"cache":{"enabled":true}}}}`.
- APCu under the CLI needs `apc.enable_cli=1`; the server says so at start when it is off.
- The in-memory layer saves about 12 percent CPU per hit for large pages and nothing for small ones; measure on your own site.

Full upgrade steps: [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md).

## See also

- Specification: [Worker pool](../../specifications/6.0/velocity-worker-pool.md), [Engine settings](../../specifications/6.0/velocity-engine-settings.md), [HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md) (what is never cached).
- Related: [Control panel](velocity-control-panel.md) (Cache tab), [Static files and images](velocity-static-files-and-images.md) (file caching), [Scheduler](velocity-scheduler.md) (the cache sweep).
- Upgrade: [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md), [HTTP cache](../../bc/6.0/http-caching.md), [Velocity engines](../../bc/6.0/velocity-engines.md).
- History: [22 September](../../history/velocity/2026-09b.md), [25 to 30 September](../../history/velocity/2026-09e.md); [changelog](../../changelogs/extensions/exponential-velocity.md).
- [The response cache, and getting out of the network's way](../../bc/6.0/response-cache-and-navigation.md)
- [The role-aware HTTP cache](../../bc/6.0/httpcache.md)
- [Velocity: running Exponential in a persistent-worker web server](velocity-persistent-worker-server.md)
