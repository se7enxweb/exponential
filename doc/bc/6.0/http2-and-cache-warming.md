# HTTP/2, and keeping the response cache full

Two things that together decide what a visitor waits for, and the measurements
that justify each.

---

## The short version

```
document, warm cache      67–88 ms
document, cold cache     ~800–1600 ms
TLS handshake             27–42 ms   (nginx on the same box: 25–31 ms)
```

The application is fast when the answer is already in the cache and slow when
it is not, because the front page costs 529 database queries. So the work is
in two halves: make the transport as good as it can be, and make sure a
visitor is never the one who finds the cache empty.

---

## HTTP/2

Spoken by the server itself. No proxy, no extension, no async library:

- **TLS/ALPN** — `stream_socket_enable_crypto()` with `alpn_protocols` on the
  stream context. The negotiated protocol is read from
  `stream_get_meta_data()['crypto']['alpn_protocol']`.
- **Framing** — a hand-written nine-octet codec.
- **HPACK** — written from scratch: integer codec, static table, dynamic table
  with eviction, and the full 257-entry Huffman table. Verified against the
  worked examples in RFC 7541 Appendix C, 22 of 22.
- **State machine** — streams, both flow-control windows, CONTINUATION,
  padding, GOAWAY.
- **Event loop** — the server's existing `stream_select()` loop.

### Switching it on

```ini
; settings/velocity.ini
[ServerSettings]
HTTP2=enabled
HTTP2ErrorImage=
```

Off by default, and that default is deliberate. The protocol is agreed during
the TLS handshake and a client does not fall back afterwards, so a fault is a
page that never arrives rather than one that arrives slowly.

### Why there is a fallback page

Because of that same property. A framing fault, a bad header block or a
throwable in a handler would otherwise produce nothing at all — an empty
window, and no status or log the person looking at it can reach. So wherever a
stream is still open, something is sent on it: a readable page naming what
failed, on which stream, at what time.

It is configurable, since an error page is the one page that cannot be
previewed before it is needed:

```
Q.web.http2.errorPage.template   a file used instead, given {title} {message}
                                 {detail} {image} {stream} {time}
Q.web.http2.errorPage.title      heading
Q.web.http2.errorPage.message    the sentence under it
Q.web.http2.errorPage.image      URL, data: URI, or a path to inline;
                                 "none" for no picture
Q.web.http2.errorPage.detail     false hides the technical line
```

`HTTP2ErrorImage` in `velocity.ini` sets the image without touching the
server's own configuration. The default illustration is drawn inline and
embedded, because the page appears exactly when the connection carrying it is
unreliable and must not depend on a second request succeeding.

### What HTTP/2 is not worth

It does not make a page render faster. It makes the page's *subresources*
cheaper: this site references 43, and over HTTP/1.1 a browser fetches them
about six at a time in waves. Measured asset delivery was 393–418 ms over
HTTP/1.1 against 85–104 ms over HTTP/2.

---

## Keeping the cache full

The response cache is enabled by default in the server and honours each
response's own `Cache-Control`. With anonymous pages sending
`public, max-age=300`, a page stays cached for five minutes and then the next
person to ask waits for a full render.

`exp:warm` makes that person a script.

```bash
bin/php/console exp:warm              # every published page
bin/php/console exp:warm --verbose    # name the ones that had to render
bin/php/console exp:warm --limit=40
bin/php/console exp:warm --json
```

On a timer, shorter than the cache window:

```
*/4 * * * * cd <root> && php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
```

Measured here: 142 pages, 9.6 s from cold, **0.3 s when everything is already
warm**, no failures.

### Three things it has to get right, each learned the hard way

**The Host header must match what a browser sends, port and all.** The cache
keys on host, path and encoding. A browser asking for
`https://alpha.se7enx.com:8080/site` sends `alpha.se7enx.com:8080`; warming
with `alpha.se7enx.com` fills a different entry that nothing ever reads. The
warmer then reports success while every visitor still pays the render — which
measured 1036 ms for a page the cache could have answered in 3 ms. The host is
now built from `SiteURL` plus the service's HTTPS port, with 443 omitted
because a browser omits it.

**It must use real URL aliases, not `path_identification_string`.** The latter
is an internal identifier — `bold_agency`, `fit_healthy` — and requesting it
returns 404.

**It must stay inside this siteaccess's own subtree.** This is a multisite: the
content root holds one subtree per site and the others answer on other hosts.
Warming from the content root asks this host for another site's pages and is
correctly told 404. The root is taken from `SiteSettings/IndexPage`.

---

## What is not achievable from PHP

Both tested against this server, both unavailable through PHP's stream layer:

- **TLS session resumption.** No session ticket is ever issued, so every
  connection pays a full handshake. `openssl s_client -sess_out` writes no
  session file.
- **OCSP stapling.** `OCSP response: no response sent`. In this case it costs
  nothing — the Let's Encrypt certificate in use carries no OCSP responder at
  all, so there is no revocation fetch for a browser to make.

Neither is a defect in this installation; they are limits of writing a TLS
server in PHP.

---

## What remains, honestly

The server answers a warm page in 67–88 ms and matches nginx on handshake time.
Anything a browser shows above that is distance to the server and the 43
subresources on the page — 5 stylesheets, 4 scripts and 41 images. Reducing
that count is the largest remaining item and it is theme work, not server work.

`/showcase` renders in 4.5–21 s, far worse than any other page, and distorts
the warm cycle. It deserves its own investigation.

---

## Corrections, and what the warmer had to learn since

Three faults were found in the warmer after this was first written, and every
one of them reported success while it was happening.

**It warmed addresses nobody asks for.** Paths came from `urlAlias()`, which
gives the bare form, so it filled `/fitness` while visitors asked for
`/site/fitness` -- a separate cache entry, because the cache keys on host and
path and this installation reaches the same content both by host match and by
URI match. Measured: `/fitness` 116ms warm, `/site/fitness` 496ms cold. Both
forms are warmed now, and both `/site` and `/site/`, which are also distinct.

**It renewed nothing.** Asking for a page returned a cache hit and left the
entry's expiry untouched, so entries lapsed between runs however often the cron
fired. Requests now carry `X-Cache-Refresh`, which the server reads past the
stored copy for, so the page is rendered and stored again. That needs
`qbix-webserver` 0.0.4.6 or later.

**It then became the reason pages were slow.** Refreshing means rendering, so a
cycle turned into 285 full renders. An admin page measured 141-287ms between
cycles and 679-1146ms during one. Concurrency is 1.

The signature worth remembering: **a cached response that is slow is a capacity
problem, not a rendering one.** The `expires` header said the page came from
cache while the clock said 1590ms, and that contradiction was the answer.

## The response cache skips on the wrong cookie by default

The server's own default names `PHPSESSID` and `Q_sid`. Those are PHP's and
Qbix's names, and a browser carrying a stale one of either -- from any Qbix
application on the same host -- bypassed the cache on every request, while a
real session sailed through it. 74ms from cache against about 1300ms rendered,
for pages that were byte for byte identical.

`expVelocity` now derives the list from `site.ini` and writes it into the server
config. Override with `[ServerSettings]CacheSkipCookies`.

**Corrected:** the first version of that derivation read `[Session]SessionNamePrefix`
alone, which is only right with `SessionNameHandler=custom`. With `default` -- the
`site.ini` default -- PHP names the session cookie, `PHPSESSID`, and the prefix is not
used at all. An installation on the default handler got a skip list of `eZSESSID`,
never matched its own session cookie, and with the response cache on answered a
signed-in request from the cache or stored it for everybody else. The list is now the
session cookie as the handler names it -- `session.name` for `default`,
`SessionNamePrefix` for `custom` (matched as a prefix, so it covers
`<prefix><digest>`) -- plus `is_logged_in`, which the kernel sets for signed-in
visitors for exactly this purpose.

## Signing in did not work over HTTP/2 before 0.0.4.7

Cookies a script sets are carried separately from its headers in a pooled
response, and the HTTP/2 path never read them, so every `Set-Cookie` was
dropped. The login answered 302 to the right place and set no session.

It hid well: a browser already holding a session carried on working, and a
session obtained over HTTP/1.1 is equally good over HTTP/2. Only a fresh
sign-in on a connection that had negotiated h2 could see it -- every private
window, and every automated test.

---

# Hardening the HTTP/2 connection

An HTTP/2 connection lets one peer ask a server to hold state on its behalf:
open streams, a header block being assembled, a body being received, bytes
queued to send. Every one of those was unbounded until `qbix-webserver`
0.0.4.9.

None of the following need an authenticated user, a malformed frame or a bug.
They are ordinary protocol use taken to excess, which is what makes them hard
to see: **no request completes, so nothing is written to an access log while it
happens.** The first sign is a worker that will not answer.

| limit | default | what it stops |
|---|---|---|
| `concurrentStreams` | 128 | streams open at once |
| `headerListSize` | 64 kB | CONTINUATION with no END_HEADERS |
| `bodySize` | 64 MB | a body held whole before the application sees it |
| `readBuffer` | 4 MB | frames announced and never completed |
| `writeBuffer` | 8 MB | a peer that requests much and stops reading |
| `resetStreams` | 256 | opening and cancelling, repeatedly |
| `reflexFrames` | 1000 | PING and SETTINGS, which oblige an answer |
| `idleSeconds` | 120 | sockets held open saying nothing |

All are overridable under `Q.web.http2.limits`. A configured value of zero or
less is **ignored** rather than read as "unbounded", because unbounded is the
state these exist to leave behind.

Two are worth knowing about specifically.

**The stream limit had been advertised and never counted.** SETTINGS told every
peer 128 for months while nothing enforced it, so a peer that believed us was
the only thing keeping the number down. A peer is entitled to treat an
advertised limit as real; one that does not was unopposed.

**The write buffer arrived with a bug fix.** When truncation of large responses
was fixed, what would not fit in the socket began waiting in memory — which is
what makes a large response survive a full send buffer, and also means a peer
that requests a great deal and then simply stops reading hands the process an
unbounded allocation. It is the quieter half of slowloris, because the request
was perfectly valid.

Idle connections are swept by the event loop rather than by a timer each, since
only the loop can see them together and a timer per connection is itself a
resource a peer could multiply.

## The HPACK decoder read past its buffer

Found by fuzzing rather than by reading: 600 random header blocks produced reads
as far as **119,315,352 bytes past the end**.

On PHP 8 that is a warning and an empty string rather than a crash, which is
why it went unnoticed. But a warning is written to the error log once per byte,
at a frequency the peer chooses, and with `display_errors` on it is written into
the response body instead.

The worse half is quieter. `substr()` with a length past the end returns what
there is, so a block declaring a 200-byte header value and supplying 12 had
those 12 decoded **as the value**. A lying block did not fail; it produced a
header the peer never sent.

Both sites check before reading now, and a block that will not decode closes the
connection with `COMPRESSION_ERROR`. That is what RFC 7541 requires and it is
not bureaucratic: HPACK's dynamic table is shared by every block on the
connection, so once one has been misread the table is wrong and every block
after it decodes to something nobody sent. There is no partial recovery worth
attempting.

## Checking any of this

The server's own suite carries a case per attack. Each **mounts** the attack
against the connection and asserts it is refused — asserting that a limit
constant exists would have passed against the state this replaced, where the
stream limit was advertised and never applied.

```bash
php vendor/se7enxweb/qbix-webserver/tests/run-unit.php
```

No server, no socket, no certificate. `tests/run.sh` runs it before starting
anything, because there is no sense binding a port to discover the frame codec
is broken.

## What this is not

It is not a security proof. The known HTTP/2 exhaustion classes are bounded and
each has a test; that is a different claim from "no remaining defects", and the
two bugs above were found by fuzzing and measurement rather than by reading,
which is the honest signal about what reading alone missed.

`Panel.php`, `WebSocket.php`, `Trust.php` and `Autohost.php` have had no such
pass. Memory behaviour over a long soak — a hundred thousand requests against a
resident process — has not been measured at all.

---

# Measuring it, and choosing a worker count

`vendor/se7enxweb/qbix-webserver/tests/bench-load.php` sweeps the concurrency
until throughput stops improving and reports where the knee is.

```bash
php vendor/se7enxweb/qbix-webserver/tests/bench-load.php \
    https://alpha.se7enx.com:8080/site --levels=1,4,16,32,64 --requests=200
```

It needs nothing installed: PHP's curl speaks HTTP/2 and `curl_multi` supplies
the concurrency. Every run writes a CSV under `var/storage/generated/stats/`,
named with the date, target, protocol, levels, request count and worker count,
so two runs sort beside each other and say what they were without being opened.

## What it found here

11 cores, 46 GB, and **167.6 MB PSS per worker** — the honest figure, since RSS
reports 200 MB by double-counting pages shared after fork.

| workers | peak req/s | load | resident |
|---|---|---|---|
| 8 | 233.3 | — | 1.7 GB |
| **16** | **307.0** | 7.8 | 1.5 GB |
| 32 | 303.4 | 15.6 | 2.9 GB |
| 64 | 272.8 | 10.5 | 3.2 GB |

Sixteen is the optimum, at roughly 1.5x the core count. Thirty-two bought
nothing and doubled the load average; sixty-four was worse than sixteen.

`[ServerSettings]Workers` in `settings/velocity.ini`, or an override.

## The cliff matters more than the peak

Per request, at 16 workers:

| in flight | p50 | p90 | p99 | req/s |
|---|---|---|---|---|
| 1 | 44 ms | 45 ms | 51 ms | 22 |
| 4 | 42 ms | 46 ms | 124 ms | 95 |
| **16** | **28 ms** | 46 ms | 295 ms | **307** |
| 32 | 70 ms | 445 ms | 465 ms | 227 |
| 64 | 188 ms | 857 ms | 882 ms | 153 |

At sixteen the p50 *falls* to 28 ms while throughput peaks — requests queue just
enough to keep every worker busy. Past it, p90 goes 46 to 445 ms, a tenfold
cliff, while throughput drops. **That is the operating limit, and it arrives
long before memory does.**

## The ceiling above that is arithmetic, not a measurement

At 167.6 MB per worker on 46 GB: 128 workers wants 21 GB and swaps, 512 wants
84 GB, 2500 wants 409 GB. Those configurations are impossible rather than slow,
and no benchmark is needed to say so. Predict before running a sweep.

This per-worker figure is the one lever the warm-up moves: rendering in the
parent before the fork cuts it roughly tenfold (see *Sharing memory across the
pool*), which raises this ceiling accordingly — though never the throughput
cliff or the event-loop ceiling, which memory does not touch.

## What a visitor pays

    dns     7 ms
    tcp    +0.3 ms
    tls    +18 ms
    ttfb   +41 ms
    -------------
    total   67 ms     9.4 kB gzipped

A complete page — document plus 35 subresources over one connection — is about
200 ms.

## Two caveats on all of it

The generator is **closed-loop**, like `ab` and `wrk`: a slow response delays
the request that would have followed, so the requests never sent are exactly the
ones that would have been slowest. Read the percentiles as "how it served what
it accepted", not as what a user would have seen. An open-loop generator at a
fixed arrival rate is the other half and is not provided.

And these figures describe *this* site's templates on *this* machine. They are
not a property of the server and should not be quoted as one.

---

# The ceiling the single event loop imposes

The memory arithmetic above says how many workers *fit*. There is a second,
lower ceiling that says how many can *run*, and it is not about memory at all.

The parent is one process running one event loop. It alone accepts connections
and services the dashboard's WebSocket; the workers only handle requests handed
to them. Fork enough workers and they starve that one process of CPU. Measured
here: at **1250 workers on 12 cores every request timed out at 45 s** and the
dashboard sat on a red "connecting" that never resolved — the WebSocket upgrade
never got a turn on the CPU. Reverting to a few hundred restored service at
once. Load was moderate throughout; this is starvation of one scheduler slot,
not saturation.

So there are three limits, and they arrive in this order as the count rises:
the **throughput cliff** (here ~16, where p90 turns over — the real operating
limit), the **event-loop ceiling** (a few hundred, where the parent can no
longer stay responsive), and the **memory ceiling** (arithmetic, highest of the
three). Tune to the first. The others are failure modes to stay well below, not
targets.

## Monitoring must not become the load

A corollary learned by breaking it: the dashboard read `/proc/<pid>/smaps_rollup`
for **every** worker to report real (PSS) memory, and it did so inside the event
loop, every couple of seconds while a dashboard was open. The kernel computes
`smaps_rollup` by walking all of a process's mappings, so at hundreds of workers
that read stalled the loop long enough to wedge the whole server — the same
symptom as the ceiling above, from an unrelated cause. The fix was to **sample**:
read at most ~24 workers, scale the average to the count, add the parent read
exactly. Any per-worker work on the request path has to be bounded the same way,
because the pool size is not.

---

# Sharing memory across the pool: warming a render in the parent

A worker builds the framework's per-request working set — compiled templates,
the resolved layout, the object graph — the first time it serves a page, and
keeps it: measured flat at ~209 MB private across 800 requests, once per worker.
That set lives in the Zend allocator's arena, which is anonymous memory, which
`fork()` shares copy-on-write. So if it is grown in the **parent**, before the
workers exist, every worker inherits it shared and pays only for what a request
needs beyond the shared baseline.

`bin/php/velocity-warmup.php`, wired through `Q.webserver.preload` and gated by
`[ServerSettings]PreloadWarmup`, renders a representative page in the parent
once, before the fork. Measured effect: **~17–24 MB private per warm worker
with it, versus ~209 MB without** — the difference between warming the whole
pool costing a few GB and costing over a hundred.

It is off by default, and the reason is the whole point of the next section: a
render leaves state behind, the pool's statics snapshot freezes that state as
every worker's baseline, and the wrong state frozen there is served to real
visitors. Getting the warm-up right *is* getting the reset right.

---

# The global scope a persistent worker must protect

This is the load-bearing lesson of the whole model, and it is not specific to
the warm-up: **any state a request leaves in a static property or in `$GLOBALS`
is inherited by the next request in that worker.** Under one process per request
the OS threw that state away at exit; under a persistent worker it survives, and
under a warm-up it is frozen into every worker at once. Three real defects here,
each a different frozen global, each serving something wrong to a visitor,
proved the point:

- Frozen **request routing** made every url serve the front page.
- A frozen **partial template-override map** left some page types without their
  override.
- A frozen **"assets already emitted" flag** made the search page skip its CSS.

The state divides into three kinds. The discipline is to know which kind each
global is, because the wrong move for one kind is the right move for another.

### 1. Request-scoped — must be cleared between requests (or after a warm-up)

Identity, the request, where it routed, and any handle to the outside world.
Never share it; never let it persist. In Exponential these are:

| Global / static | What it holds |
|---|---|
| `eZRequestedModule`, `eZRequestedModuleParams`, `eZRequestedURI` | the request and its routing |
| `eZURIRequestInstance`, `eZGlobalRequestURI`, `eZModuleViewStack` | the parsed URI and view stack |
| `eZSys` instance (`eZSys::setInstance(null)`) | server paths, script name, the URL the page builds links from |
| `ezpKernel::$instance` | the kernel object holding the last request/response |
| `eZCurrentAccess` | the resolved siteaccess |
| `eZUserGlobalInstance*`, `eZUserBuiltins` | the current user — sharing it leaks one visitor's identity to the next |
| `eZDBGlobalInstance` | the database connection — a forked child sharing the parent's socket interleaves traffic on the wire and corrupts it; close it before the fork |
| `eZHTTPToolInstance`, `eZExpiryHandlerInstance` | per-request request/expiry helpers |
| content object cache (`eZContentObject::clearCache()`) | the rendered page's objects |

### 2. Caches that must be *whole* to be correct — clear so they rebuild

The subtle kind. These are performance caches, not identity, so sharing them
looks safe — but a cache populated by *one* page is **partial**, and a partial
cache is worse than an empty one, because code trusts it as complete and stops
looking. Two here, and both bit:

| Global / static | Why partial is wrong |
|---|---|
| `eZOverrideTemplateCacheMap` | built for the warmed page's templates only; other page types found no override and rendered the wrong template |
| `ezjscPackerTemplateFunctions::$loaded` (and `$persistentVariable`) | the asset packer sets `$loaded['css_files'] = true` once a page emits its stylesheet; frozen true, the next page believes its CSS is already there and omits the `<link>` — this is why the *search* page specifically came back unstyled |

Clear these so each request rebuilds a complete one. The memory freed returns to
the arena, not the OS, so clearing them does not cost the warm-up's benefit.

### 3. Registries — expensive, identical every request, safe to keep

Type and path registries, parsed configuration, locale and charset tables.
These are why the warm-up saves memory: build them once, share them. Keep them.

`eZDataTypes*`, `eZWorkflowTypes*`, `eZNotificationEventTypes*`,
`eZModuleGlobalPathList`, the INI caches, the locale and charset tables, and the
template design *configuration* (bases, settings, compiler directory — but **not**
the override cache map from kind 2). `[ServerSettings]KeepGlobals` names the ones
the between-request reset preserves; the warm-up's keep-set is the same idea.

### How to find them in a codebase that was not built for this

They do not announce themselves. Two techniques found every one above:

- **Dump `$GLOBALS` after a render** and categorise each key by name and size
  (a short script that walks `$GLOBALS` will do). The request-scoped ones stand out;
  the big ones are usually the caches.
- **Grep for the symptom, not the state.** The CSS defect was found not by
  reading globals but by grepping the codebase for where `css_files` is set,
  which led straight to the packer's `$loaded` static.

And verify by behaviour, never by inspection: after any change to what the
warm-up renders or clears, confirm that **several distinct urls return distinct
content and each carries a resolving stylesheet.** Reset bugs are invisible in a
single request; they only appear on the second, different one.

### The list is not the fix — the discipline is

Everything above is a real, load-bearing catalogue, and it is still **not
exhaustive**. Three leaks were found and fixed by name; a fourth then appeared
anyway — the home page rendered a different node's layout and breadcrumb,
because the render had coupled yet another piece of request context into shared
scope that no entry above names. That is the actual lesson, and it is stronger
than any list: **in a `$GLOBALS`-heavy framework not built for a shared worker,
you cannot enumerate the request-scoped state, so do not try to.** A hand-kept
clear-list converges slowly if at all, and every gap in it ships a wrong page.

The robust design is the opposite of a clear-list: snapshot the *entire*
post-bootstrap scope once — every static property and every `$GLOBALS` key that
exists after the kernel initialises but before any request is served — and
restore *to that* after each request (or after a warm-up render), rather than
naming what to remove. The engine's between-request reset already works this way
for statics; a warm-up that renders before the snapshot has to bring `$GLOBALS`
under the same regime, and until it does, rendering in the parent is not safe to
enable on a site with real content. The memory it would save is rarely the
constraint that matters — the throughput cliff and the event-loop ceiling arrive
first — so this is an optimisation to reach for only after those are addressed,
and only with a full page-and-asset regression to catch exactly the class of bug
that a clear-list misses.

**This was carried out, and it works.** `bin/php/velocity-warmup.php` renders in
the parent, then resets *every* user-class static property to its declared
default and clears the request globals, keeping only the pure config and type
registries (`eZINI`, `eZDataType`, `eZWorkflowType`, `eZModule`, `eZExtension`,
`eZLocale`, `eZCharsetInfo`, `eZTextCodec`) — expensive to rebuild, holding no
request state. That single move cleared all four contamination classes at once,
including the current-node the breadcrumb read, where naming leaks had not
converged after three fixes. The registries stay shared, so the memory holds:
~21 MB per warm worker against ~209 without. The one rule that made it safe was
keeping *only* pure configuration in the skip-list — resetting the registries as
well timed pages out and doubled memory, and keeping the template or
content-class caches let the breadcrumb leak straight back. The verification that
catches that specific leak is a pair: the home page must have **no** breadcrumb
and a deep page must have **its own** — a single URL will not show it.
