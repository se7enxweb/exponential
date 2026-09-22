# HTTP caching for anonymous visitors

Two settings, one in the application and one in the web server, that together
decide what a visitor's browser is allowed to keep.

Neither changes anything for a logged-in user, and neither is on by default.

---

## The problem

Every page response carried:

```
Cache-Control: no-cache, must-revalidate
Expires: Mon, 26 Jul 1997 05:00:00 GMT
Pragma: no-cache
```

and every static file carried:

```
Cache-Control: public, max-age=0, must-revalidate
```

So nothing — no browser, no proxy, no CDN — was permitted to keep a page for
any length of time, and every stylesheet, script and image had to be
revalidated on every single view. On the front page that is **41 conditional
requests per visit**, each one a round trip, each one answered `304`. The bytes
were saved; the latency was not, and on a page with dozens of assets the
latency is the whole cost of first paint.

---

## Pages: `[HTTPHeaderSettings]`

The kernel has always had this; it was simply never switched on. In
`settings/override/site.ini.append.php`:

```ini
[HTTPHeaderSettings]
CustomHeader=enabled
OnlyForAnonymous=enabled
OnlyForContent=enabled

HeaderList[]
HeaderList[]=Cache-Control
HeaderList[]=Expires

Cache-Control[]
Cache-Control[/]=public, max-age=60
Expires[]
Expires[/]=60
```

**`OnlyForAnonymous` is what makes this safe.** `eZHTTPHeader::enabled()`
resolves it to `!eZUser::isCurrentUserRegistered()`, so a logged-in editor
always receives the original no-cache set and a personalised page is never
handed to a shared cache. Verify both directions with
`check_anonymous_cache_headers.sh`, which fails if a logged-in request ever
comes back cacheable.

`Expires` is given in seconds and the kernel converts it to a date.

**The trade is staleness.** An anonymous visitor may see a page up to
`max-age` seconds old after it is edited. Sixty seconds is short enough that
nobody publishing notices and long enough to absorb a burst of traffic. Set
`CustomHeader=disabled` to go back.

### One kernel change was needed

`Pragma: no-cache` is the HTTP/1.0 spelling of `Cache-Control` and cannot
express "cacheable". Left in place beside `public, max-age=60` the response
contradicted itself; browsers resolve that in favour of `Cache-Control`, but a
proxy is entitled to read the `Pragma` and decline to store the page, which
defeats the point of setting the header.

`ezpKernelWeb::run()` now drops the default `Pragma` when, and only when, a
configured header has overridden `Cache-Control`. A response that keeps the
default `Cache-Control` keeps its matching `Pragma`, so an installation that
has configured nothing is unaffected.

---

## Static files: `StaticMaxAge`

In `settings/velocity.ini`:

```ini
[ServerSettings]
StaticMaxAge=31536000
```

The service writes this into the server's configuration as
`Q.web.static.maxAge`. Zero leaves the server's own default alone.

A year is what nginx already sends for the same files in front of this
installation. The trade is that a file replaced in place is not noticed until
the lifetime expires — clear the browser cache, or lower the number, while
working on a stylesheet.

---

## What it is worth

Counted per asset, front page:

| | assets | reusable with no request | must revalidate |
|---|---|---|---|
| before | 41 | **0** | **41** |
| after | 41 | **41** | **0** |
| nginx, for comparison | 41 | 41 | 0 |

This does not make a first visit faster. It makes every subsequent visit and
every in-site navigation cost 41 fewer round trips, which is what a returning
visitor experiences as the page painting immediately.

---

## What this is not

It is not the server's reverse proxy cache, which stores rendered responses in
the parent process and is a separate feature, off by default.

It is not the kernel's static cache (`StaticCache` in `site.ini`), which writes
flat files.

It does not reduce the work of rendering a page. The front page costs about
529 database queries, and no caching header changes that for the visitor who
misses the cache.
