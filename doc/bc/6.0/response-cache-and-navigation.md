# The response cache, and getting out of the network's way

**Introduced:** Exponential CMS 6.0.15
**Affects:** any installation running under the Velocity (Qbix) server

What a visitor waits for, where it actually goes, and the measurements behind
each change. Every setting is off or unchanged by default; an installation that
configures none of this behaves exactly as it did before.

---

## The two numbers everything sits between

```
cache hit      0.36 ms
full render  1382 ms
```

Almost everything below is about keeping requests on the left.

---

## Added: conditional requests for cached pages

A returning browser sends the validators it holds. When they still stand the
answer is `304` and no body at all.

Static files had answered conditional requests since the beginning; cached pages
never did. A browser that already held the page said so, in the request, and was
sent 76 KB — 9.7 KB compressed — anyway.

```
before   200, 9.7 KB
after    304, 202 bytes of headers, no body
```

## Added: an ETag derived from the page, not the clock

An entry carried only `Last-Modified`, which recorded when the *entry was
stored*. Rebuild it and the timestamp moved even when the HTML was byte
identical, so the next reload revalidated, failed, and transferred the whole
document. A page nobody had edited cost full price once per lifetime, forever.

```
rebuild 1  etag="ae368d…-gzip"  last-modified=20:48:29
rebuild 2  etag="ae368d…-gzip"  last-modified=20:48:30
rebuild 3  etag="ae368d…-gzip"  last-modified=20:48:31
```

The tag is a hash of the body as the application produced it — before
compression, because gzip is not byte-stable across versions and hashing its
output would invent a new validator for an unchanged page. The identity and
gzip forms get different tags, because RFC 9110 scopes a validator to the
selected representation.

## Added: `Date` on every response

Not present on a 200 or a 304. RFC 9110 requires it and RFC 9111 computes a
response's age against it; without one a client whose clock runs slightly ahead
reads the absolute `Expires` beside it as already past, and revalidates every
navigation.

## Added: stale-while-revalidate — `CacheStaleWhileRevalidate`

An entry expires at a moment, not gradually, so every request in flight misses
at the same instant and each one rendered the page.

```
                      before                    after
8 requests at an      8 rendered, 1815-2131ms   1 rendered
expiry                each                      7 served at ~62ms
```

Exactly one request renders; the rest are handed the copy that already exists,
labelled `X-Cache: STALE` with an honest `Age`. The claim is a directory,
because `mkdir` is atomic and needs no cleanup protocol to be correct. A claim
older than `revalidateLockSeconds` may be taken, so a worker that dies mid
render cannot freeze a page.

Default `0`, which is the old behaviour exactly.

## Added: negative caching — `CacheNotFoundSeconds`

`put()` refused any status but 200, so every 404 ran the whole routing and
rendering path, every time, unbounded. Measured across 278 public URLs:
220–340 ms each, none cached.

```
first 404   758 ms
second 404   27 ms   X-Cache: HIT
```

Only 404 and 410. A 500 is never remembered — the next request is exactly when
it might work — and a redirect is never remembered as a negative. The lifetime
is the server's, not the response's, because a not-found carries whatever
`Cache-Control` the application happened to set.

Default `0`.

## Added: HTML minification — `MinifyCachedHtml`

30.8% of the front page was whitespace a template engine shipped because a
template is written to be read by people.

```
decompressed   94,128 -> 69,101 bytes   26.6% smaller
gzipped         9,707 ->  9,707 bytes   unchanged
```

The gzipped size does not move, and that is expected: gzip already handles
repeated whitespace. The saving is in the decompressed document — what the
browser parses, what a service worker stores, what sits in memory. Done once at
store time.

`<pre>`, `<textarea>`, `<script>`, `<style>` and `<code>` are returned exactly
as written. Whitespace there is rendered literally, changes what a form submits,
or can end a statement early through automatic semicolon insertion.

## Updated: the cache is consulted before the routing, not after it

`handleRequest()` reached the cache **322 lines in**, after host-config
resolution, a `realpath()`, a favicon `file_exists()`, the bundled-asset checks
and the well-known handlers — all of which a cache hit then threw away.

```
                        CPU per request
                        before   after
cached page, 9.3 KB     3.80 ms  0.54 ms
cached 404              1.30 ms  0.22 ms
```

Safe to hoist because nothing above it decided whether the response may be
served: the key includes the Host, and an entry exists only because an earlier
request passed every check below it. The ACME challenge still runs first.

## Updated: the listen backlog

PHP's `stream_socket_server` defaults it to 32, which is not a queue so much as
a cliff. A burst past it does not queue and does not fail: the kernel drops the
SYN and the client retransmits a second later.

```
                  backlog 32              backlog 1024
concurrency  64    745 req/s  p99 1067 ms   2,703 req/s  p99 19.5 ms
concurrency 128    603 req/s  p99 1317 ms   2,773 req/s  p99 37.9 ms
```

Throughput did not degrade at 64, it collapsed to a quarter, and the p99 became
a round thousand milliseconds — the retransmit timer, while the server sat
mostly idle.

## Added: paths that are always revalidated

A service worker installs into a browser and then decides what every later
navigation is answered with. Served with the year-long static lifetime, a bug in
one cannot be withdrawn: the fix sits on the server while browsers keep running
the old copy. `sw.js`, `service-worker.js`, `*.webmanifest` and `manifest.json`
are now always revalidated.

## Added: a navigation cache in the browser (`sw.js`)

The last thing between a visitor and the page is a network round trip, and a
round trip cannot be shortened, only skipped.

```
kernel RTT to a real browser connection   12.47 ms
server-side answer to the same request     0.36 ms
```

So the page is answered from the browser's own store and the network is asked
afterwards. Only same-origin GET navigations; never a page fetched with a
session cookie; never a non-200; never `/admin`, `/user`, `/explayouts_ui` or
`/api`. If a session cookie appears the page-side script unregisters the worker
and clears its cache rather than leaving it to serve one visitor another's page.

Kill switch: serve `sw.js` as `self.registration.unregister()`. Every browser
drops it on its next update check, at most 24 hours, because `sw.js` is
`no-cache`.

The worker serves the stored copy first and refreshes behind it, so a published
change appears on the **second** view, not the first.

## Added: Middle-Out — shared-dictionary compression, off by default

Named after the compression in HBO's *Silicon Valley*, which is fiction; the
technique is real and old, the idea behind Brotli's built-in dictionary and the
late SDCH.

Measured on this installation's own 273 cached pages: 1,626,195 bytes as plain
gzip against 1,290,140 with a 32 KB dictionary — 20.7% smaller, 273 of 273
round-tripping exactly.

**It is off, and the reason is worth reading before switching it on.** Enabled
against this cache, 271 of 272 entries were stored uncompressed anyway: the
cache holds bodies in *wire form*, already gzipped so a hit needs no compression
work, and a dictionary cannot shrink gzip output. Capturing the 20.7% would mean
storing plain HTML and re-compressing on every hit — 20% of cache memory bought
with CPU on every request.

Worth it where the cache holds uncompressed bodies, or where memory is scarcer
than CPU. Every payload carries a CRC of the dictionary it was built against;
decompression refuses rather than guessing, so a rebuilt dictionary reads as a
miss and never as a corrupted page.

---

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

Reproducing the configuration: `qbix-settings.md` in the project root.
The full account, including what was tried and abandoned: `ai/doc/waves/T016-*`.
