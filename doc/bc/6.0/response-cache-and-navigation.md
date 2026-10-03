# The response cache, and getting out of the network's way

Read this page if you run Exponential under the Velocity (Qbix) server and want to know what its response cache now
does for a visitor: what changed in 6.0.15, which settings control it, and the measurements behind each change. Every
change is listed with its setting, so you can turn back any one of them.

## In short

| | |
|---|---|
| What changed | Cached pages answer conditional requests (`304`) with a content-based `ETag`; every response has `Date`; stale-while-revalidate; cached 404s; HTML minification; the cache is checked before routing; a larger listen backlog; service worker paths always revalidated; a browser navigation cache (`/index.js`). |
| Who is affected | Installations running Velocity. Apache and PHP-FPM are not affected. |
| How to check | `./console exp:velocity config list` shows the settings in effect; `curl -sI https://your-host/` twice shows `X-Cache: HIT`. |
| How to turn something back | Set the key in the table below to its "old behaviour" value in `settings/override/velocity.ini.append.php`, then restart Velocity. |

## The settings

File `settings/velocity.ini`, block `[CacheSettings]`, scope: installation. The older names in `[ServerSettings]`
still work; a value in `[CacheSettings]` wins.

| Key | Shipped value | Old behaviour | Former `[ServerSettings]` name |
|---|---|---|---|
| `StaleWhileRevalidate` | `60` (seconds) | `0` | `CacheStaleWhileRevalidate` |
| `NotFoundSeconds` | `60` (seconds) | `0` | `CacheNotFoundSeconds` |
| `MinifyHtml` | `enabled` | `disabled` | `MinifyCachedHtml` |
| `MiddleOutCompression` | `disabled` | `disabled` | `MiddleOutCompression` |

Conditional requests, `ETag`, `Date`, the routing order, the backlog and the revalidated paths have no switch: they
are fixes to the server.

## The two numbers everything sits between

```
cache hit      0.36 ms
full render  1382 ms
```

Almost everything below is about keeping requests on the left.

## Added: conditional requests for cached pages

A returning browser sends the validators it holds. When they still stand, the answer is `304` with no body at all.

Static files answered conditional requests from the beginning; cached pages never did. A browser that already held
the page said so in the request, and was sent 76 KB (9.7 KB compressed) anyway.

```
before   200, 9.7 KB
after    304, 202 bytes of headers, no body
```

## Added: an ETag derived from the page, not the clock

An entry carried only `Last-Modified`, which recorded when the *entry was stored*. Rebuilding it moved the timestamp
even when the HTML was byte identical, so the next reload revalidated, failed, and transferred the whole document. A
page nobody had edited cost full price once per lifetime, forever.

```
rebuild 1  etag="ae368d…-gzip"  last-modified=20:48:29
rebuild 2  etag="ae368d…-gzip"  last-modified=20:48:30
rebuild 3  etag="ae368d…-gzip"  last-modified=20:48:31
```

The tag is a hash of the body as the application produced it, before compression: gzip is not byte-stable across
versions, and hashing its output would invent a new validator for an unchanged page. The identity and gzip forms get
different tags, because RFC 9110 scopes a validator to the selected representation.

## Added: `Date` on every response

It was missing on a 200 and on a 304. RFC 9110 requires it, and RFC 9111 computes a response's age against it.
Without it, a client whose clock runs slightly ahead reads the absolute `Expires` beside it as already past, and
revalidates every navigation.

## Added: stale-while-revalidate (`StaleWhileRevalidate`)

An entry expires at a moment, not gradually, so every request in flight missed at the same instant and each one
rendered the page.

```
                      before                    after
8 requests at an      8 rendered, 1815-2131ms   1 rendered
expiry                each                      7 served at ~62ms
```

Exactly one request renders; the rest get the copy that already exists, labelled `X-Cache: STALE` with an honest
`Age`. The claim to render is a directory, because `mkdir` is atomic and needs no cleanup protocol to be correct. A
claim older than `revalidateLockSeconds` may be taken over, so a worker that dies mid-render cannot freeze a page.

`0` gives the old behaviour exactly.

## Added: negative caching (`NotFoundSeconds`)

`put()` refused any status but 200, so every 404 ran the whole routing and rendering path, every time. Measured across
278 public URLs: 220 to 340 ms each, none cached.

```
first 404   758 ms
second 404   27 ms   X-Cache: HIT
```

Only 404 and 410 are kept. A 500 is never remembered (the next request is exactly when it might work), and a redirect
is never remembered as a negative. The lifetime is the server's, not the response's, because a not-found carries
whatever `Cache-Control` the application happened to set.

`0` gives the old behaviour.

## Added: HTML minification (`MinifyHtml`)

30.8% of the front page was whitespace that the template engine sends because templates are written to be read by
people.

```
decompressed   94,128 -> 69,101 bytes   26.6% smaller
gzipped         9,707 ->  9,707 bytes   unchanged
```

The gzipped size does not move, as expected: gzip already handles repeated whitespace. The saving is in the
decompressed document: what the browser parses, what a service worker stores, what sits in memory. It is done once, at
store time.

`<pre>`, `<textarea>`, `<script>`, `<style>` and `<code>` are returned exactly as written. Whitespace there is rendered
literally, changes what a form submits, or can end a statement early through automatic semicolon insertion.

## Updated: the cache is checked before routing, not after it

`handleRequest()` reached the cache **322 lines in**, after host configuration resolution, a `realpath()`, a favicon
`file_exists()`, the bundled-asset checks and the well-known handlers, all of which a cache hit then threw away.

```
                        CPU per request
                        before   after
cached page, 9.3 KB     3.80 ms  0.54 ms
cached 404              1.30 ms  0.22 ms
```

Moving it up is safe because nothing above it decided whether the response may be served: the key includes the Host,
and an entry exists only because an earlier request passed every check below it. The ACME challenge still runs first.

## Updated: the listen backlog

PHP's `stream_socket_server` sets the backlog to 32. A burst past it does not queue and does not fail: the kernel
drops the SYN and the client retransmits a second later.

```
                  backlog 32              backlog 1024
concurrency  64    745 req/s  p99 1067 ms   2,703 req/s  p99 19.5 ms
concurrency 128    603 req/s  p99 1317 ms   2,773 req/s  p99 37.9 ms
```

At 64 concurrent requests, throughput did not degrade; it collapsed to a quarter, and the p99 became a round thousand
milliseconds (the retransmit timer) while the server sat mostly idle.

## Added: paths that are always revalidated

A service worker installs into a browser and then decides what every later navigation is answered with. Served with
the year-long static lifetime, a bug in one cannot be withdrawn: the fix sits on the server while browsers keep running
the old copy. The root `index.js`, `sw.js`, `service-worker.js`, `*.webmanifest` and `manifest.json` are now always
revalidated.

## Added: a navigation cache in the browser (`/index.js`)

The worker is `/index.js`, the Exponential Service Workers Index, registered with scope `/`. It was `/sw.js` until
2026-09-30. `/sw.js` stays as a one-line script that loads `/index.js`, so a browser registered under the old name
runs the same code until the site registers `/index.js` over it. **Keep `/sw.js` until no browser can still hold the
old registration.**

The last thing between a visitor and the page is a network round trip, and a round trip cannot be shortened, only
skipped.

```
kernel RTT to a real browser connection   12.47 ms
server-side answer to the same request     0.36 ms
```

So the page is answered from the browser's own store, and the network is asked afterwards. Rules:

- only same-origin GET navigations;
- never a page fetched with a session cookie;
- never a non-200;
- never `/admin`, `/user`, `/explayouts_ui` or `/api`.

If a session cookie appears, the page-side script unregisters the worker and clears its cache, rather than leaving it
to serve one visitor another's page.

**Kill switch:** serve `index.js` as `self.registration.unregister()`. Every browser drops it on its next update check,
within 24 hours, because `index.js` is `no-cache`.

The worker serves the stored copy first and refreshes it in the background, so a published change appears on the
**second** view, not the first. The cache version is `exp-nav-v4` (see
[Behaviour changes, 16 to 30 September 2026](behaviour-changes-2026-09b.md)).

## Added: Middle-Out, shared-dictionary compression (off by default)

Measured on one installation's own 273 cached pages: 1,626,195 bytes as plain gzip against 1,290,140 with a 32 KB
dictionary, 20.7% smaller, 273 of 273 round-tripping exactly.

It is off, and the reason matters before you switch it on: the cache holds bodies already gzipped, and a dictionary
cannot shrink gzip output. When enabled against that cache, 271 of 272 entries were stored uncompressed anyway. The
whole story, and when it is worth switching on, is in [Middle-Out compression](middle-out-compression.md).

## What this adds up to

```
                              before        after
cached page, server side      0.36 ms       0.36 ms
CPU per cached request        3.80 ms       0.54 ms
peak throughput             2,961 req/s   8,941 req/s
p99 at 128 concurrent       1,317 ms       37.9 ms
reload of a held page         9.7 KB        202 bytes
expiry, 8 concurrent        8 renders     1 render
a cached 404                  758 ms        27 ms
document, decompressed       94,128 B      69,101 B
```

The settings that reproduce this configuration are in `settings/velocity.ini`; read them with
`./console exp:velocity config list`.

## Related pages

- [Velocity response cache](../../features/6.0/velocity-response-cache.md)
- [Velocity](../../features/6.0/velocity-persistent-worker-server.md) and [Velocity engines](velocity-engines.md)
- [Middle-Out compression](middle-out-compression.md) and [HTTP caching for anonymous visitors](http-caching.md)
- [Cache clears that move directories aside](../../features/6.0/cache-clear-rename-aside.md)
- [Behaviour changes, 16 to 30 September 2026](behaviour-changes-2026-09b.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
