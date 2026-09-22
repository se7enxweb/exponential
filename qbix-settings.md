# Reproducing this installation

Everything Exponential and the Qbix web server need to behave the way this
installation does, with the measurement behind each setting and the reason it
is set that way. Every default is the upstream default; nothing here is
required for the server to run, and an installation that sets none of it works
exactly as it did before.

Measured on: 12 cores, 46.8 GB, PHP 8.5.10, MariaDB and several other vhosts
sharing the machine.

---

## 1. `settings/override/velocity.ini.append.php`

This file is **not** in version control — `settings/override/` holds secrets —
so it has to be recreated by hand on a new host.

```ini
[ServerSettings]
MiddleOutCompression=disabled

Host=0.0.0.0
Port=8088
HTTPSPort=8080
Workers=16
DocumentRoot=

# How long an expired page may still be served while one request renders the
# replacement. An entry expires at a moment, so every request in flight misses
# together, and before this each of them rendered the page: 0.6ms from cache
# against 1382ms rendered. Eight simultaneous requests at an expiry went from
# eight renders of ~1900ms to one render and seven served at ~62ms.
CacheStaleWhileRevalidate=60

# How long a 404 is remembered. A not-found runs the whole routing and
# rendering path before concluding there is nothing there: 220-340ms each,
# measured across 278 public URLs, and none were cached. A crawler walking dead
# links paid that on every request and so did the server, unbounded.
#   first 404 758ms -> second 27ms
CacheNotFoundSeconds=60

# Collapse the indentation templates ship with. The front page was 94,128 bytes
# of which 28,934 were whitespace; collapsing leaves 69,101. The gzipped size
# does not move -- gzip already handles repeated whitespace -- so this is for
# the decompressed document, which is what the browser parses and what a
# service worker stores.
MinifyCachedHtml=enabled

# How long a connection may sit idle, and how many requests one may carry.
# Every new connection pays a TLS handshake, and that is 21ms here against
# 0.3ms of TCP and 1.5ms of work. Someone who reads for three minutes and then
# clicks was paying a fresh handshake at the default of 120 seconds.
ConnectionIdleSeconds=600
KeepAliveMaxRequests=1000

# Which cookies mean "this response is personal".
# Left unset, it is derived from SessionNamePrefix in site.ini, which is what
# actually signs people in here. The server's own default is PHPSESSID and
# Q_sid, neither of which this installation uses -- so anyone carrying a stale
# cookie of either name bypassed the cache on every request while a real signed
# -in session sailed straight through it.
# CacheSkipCookies=

[HTTPSSettings]
IsEnabled=true
Certificate=/etc/qbix/certs/fullchain.pem
Key=/etc/qbix/certs/privkey.pem
```

### `MiddleOutCompression` — why it is off

Shared-dictionary compression of stored entries. Correct, tested, and measured
on this installation's own 273 pages at **20.7% smaller** than plain gzip, every
one round-tripping exactly.

**It is off because it cannot pay off in this cache.** Switched on, 271 of 272
entries were stored uncompressed anyway: the cache holds bodies in *wire form*,
already gzipped so a hit needs no compression work, and a dictionary cannot
shrink gzip output. Capturing that 20.7% would mean storing plain HTML and
re-compressing on every hit — 20% of cache memory bought with CPU on every
request, on a machine with 24 GB free.

Worth switching on where the cache holds uncompressed bodies, or where memory
is scarcer than CPU. Build the dictionary first:

```bash
php ai/bin/one/build_middle_out_dictionary_from_cache.php --dry-run
php ai/bin/one/build_middle_out_dictionary_from_cache.php
```

Rebuilding invalidates every entry written against the old dictionary. That is
safe — a mismatch reads as a miss, never as wrong bytes — but each page renders
once more, so do it when the site is quiet or immediately before a warm.

---

## 2. `settings/override/site.ini.append.php`

```ini
[HTTPHeaderSettings]
CustomHeader[]
CustomHeader[/]
Cache-Control[/]=public, max-age=300
```

`max-age` is what the reverse cache reads to decide an entry's lifetime, so this
one line governs both the browser and the server.

**`immutable` was considered and rejected.** Firefox skips revalidation entirely
on `immutable`, even on a forced reload, which would put a reload at 0 ms. The
cost is that for 300 seconds no browser can be told to pick up a change,
including an urgent correction. Not worth it for a CMS being edited.

---

## 3. `extension/sevenx_themes_media/settings/image.ini.append.php`

```ini
[AliasSettings]
AliasList[]=i160
AliasList[]=i320
AliasList[]=i400
AliasList[]=i480
AliasList[]=i770
AliasList[]=i1320
AliasList[]=i1920

[i400]
Reference=
Filters[]
Filters[]=geometry/scalewidthdownonly=400
```

`i400` sits between `i320` and `i480` for the size the list grids actually draw.
Measured in a real browser at 1280px: 28 of 43 images arrived at 480px and were
drawn at 352–356px, discarding about 46% of the delivered pixels.

**An alias needs its `AliasList[]` entry as well as its own section.** Without
the registration `ng_image_alias()` silently returns *the original image*, so
the page offers full-size originals as srcset candidates — worse than what it
replaced. Always check every candidate URL carries an alias suffix.

---

## 4. Files that are not settings

| file | what it is |
|---|---|
| `sw.js` (document root) | navigation cache; serves repeat navigations from the browser with no round trip |
| `extension/sevenx_themes_media/design/media/stylesheets/inter.css` | self-hosted webfont CSS, generated |
| `extension/sevenx_themes_media/design/media/fonts/*.woff2` | 7 subsets, 214 KB, generated |
| `files/cache/reverse/middle-out.dict` | shared dictionary, only if Middle-Out is on |

Regenerate the fonts with:

```bash
python3 ai/bin/one/selfhost_google_fonts_into_media_theme.py
```

The fonts are checked in deliberately: a build that reaches out to a third party
to render text is a build that fails when they do.

---

## 5. The cache warmer

```cron
*/4 * * * * cd /var/www/vhosts/alpha.se7enx.com/doc/alpha.se7enx.com && \
  /usr/bin/php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
```

Four minutes against a five-minute lifetime, so an entry is renewed before it
expires. 285 pages in 16–33 s. It sends `X-Cache-Refresh: 1`, which reads past
the stored copy so the page is rendered and stored again — deliberately its own
header rather than `Cache-Control: no-cache`, which browsers send on a plain
reload and which would let any client make the server render on demand.

---

## 6. Things that are *not* configuration

Set in the server itself, listed because they change behaviour and someone
comparing two installations will want to know:

| | default | why |
|---|---|---|
| `Q.webserver.backlog` | 1024 | PHP's `stream_socket_server` default is 32. A burst past it does not queue and does not fail — the kernel drops the SYN and the client retransmits a second later. At concurrency 64 that was 745 req/s with p99 1067 ms; at 1024 it is 2,703 req/s with p99 19.5 ms. |
| `Q.web.static.revalidate` | `sw.js`, `service-worker.js`, `*.webmanifest`, `manifest.json` | These decide how a client behaves afterwards. Served with the year-long static lifetime, a bug in one cannot be withdrawn. |
| `Q.web.static.maxAge` | 31536000 | A year for everything else. |

---

## 7. Verifying an installation matches

```bash
# the server answers, and says how
curl -sk -D- -o/dev/null https://HOST:8080/site/ | grep -iE 'x-cache|etag|date|cache-control'

# a reload costs headers, not a page
bash ai/bin/one/measure_conditional_reload_savings.sh /site/ 8

# an expiry costs one render, not N
bash ai/bin/one/measure_cache_expiry_stampede.sh https://HOST:8080/site/ 8

# throughput and the shape of the tail
php vendor/se7enxweb/qbix-webserver/tests/bench-load.php http://IP:8088/site/ \
    --http1 --levels=1,8,32,64,128,256 --requests=2000

# the worker is installed, controlling, and staying out of the admin
python3 ai/bin/one/verify_service_worker_live.py

# nothing third-party is contacted
python3 ai/bin/one/screenshot_front_page_after_selfhosting_fonts.py
```

Expected, on a machine that is not otherwise busy:

```
cached page, server side        0.36 ms p50
CPU per cached request          0.54 ms
peak throughput               8,941 req/s at 384 in flight, 0 errors
p99 at 256 concurrent            74 ms
reload of a held page           202 bytes, 304
cached 404                       27 ms
document, decompressed       69,101 bytes
```
