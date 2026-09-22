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

`expVelocity` now derives the list from `[Session]SessionNamePrefix` and writes
it into the server config. Override with `[ServerSettings]CacheSkipCookies`.

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
