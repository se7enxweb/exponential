# HTTP caching for anonymous visitors

Read this page if you want browsers, proxies or a CDN to keep pages and static files for anonymous visitors, or if
you wonder why a returning visitor's browser asks for every stylesheet and image again. Two settings decide what a
visitor's browser may keep: `[HTTPHeaderSettings]` in the application (pages) and `StaticMaxAge` in Velocity (static
files). Neither changes anything for a logged-in user.

## In short

| | |
|---|---|
| What changed | The page headers can be made cacheable for anonymous visitors only; `ezpKernelWeb::run()` drops the default `Pragma` when a configured header overrides `Cache-Control`; Velocity sends a long lifetime for static files. |
| Who is affected | Sites that want caching for anonymous visitors. An installation that configures nothing sends the same page headers as before. |
| How to check | `curl -sI https://your-host/ \| grep -i -E "cache-control\|expires\|pragma"` as an anonymous visitor. |
| How to fix | Turn on the page headers as shown below; lower `StaticMaxAge` while you work on a stylesheet. |

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

So no browser, proxy or CDN was allowed to keep a page for any length of time, and every stylesheet, script and
image had to be revalidated on every view. On the front page that is **41 conditional requests per visit**, each a
round trip answered `304`. The bytes were saved; the latency was not, and on a page with dozens of assets the latency
is the whole cost of the first paint.

## Pages: `[HTTPHeaderSettings]`

The kernel has always had these settings; they are off by default (`CustomHeader=disabled` in `settings/site.ini`).
To switch them on, add to `settings/override/site.ini.append.php`:

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

Then clear the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

- **`OnlyForAnonymous` is what makes this safe.** `eZHTTPHeader::enabled()` resolves it to
  `!eZUser::isCurrentUserRegistered()`, so a logged-in editor always receives the original no-cache headers, and a
  personalised page is never handed to a shared cache.
- `Expires` is given in seconds; the kernel converts it to a date.
- **The trade is staleness.** An anonymous visitor may see a page up to `max-age` seconds old after it was edited.
  Sixty seconds is short enough that nobody publishing notices, and long enough to absorb a burst of traffic.
- To go back, set `CustomHeader=disabled`.

### How to check

Check both directions: the anonymous response must be cacheable, and a logged-in response must never be.

```bash
curl -sI https://your-host/ | grep -i -E "cache-control|expires|pragma"
```

Expected for an anonymous visitor with the settings above: `Cache-Control: public, max-age=60`, an `Expires` date 60
seconds ahead, and no `Pragma: no-cache`. Repeat the request with the session cookie of a logged-in user (copy it from
the browser): the response must show `Cache-Control: no-cache, must-revalidate`.

### The kernel change behind it

`Pragma: no-cache` is the HTTP/1.0 spelling of `Cache-Control` and cannot express "cacheable". Left beside
`public, max-age=60`, the response contradicted itself. Browsers resolve that in favour of `Cache-Control`, but a
proxy may read the `Pragma` and decline to store the page, which defeats the point of the header.

`ezpKernelWeb::run()` now drops the default `Pragma` when, and only when, a configured header has overridden
`Cache-Control`. A response that keeps the default `Cache-Control` keeps its matching `Pragma`, so an installation
that has configured nothing is unaffected.

## Static files: `StaticMaxAge` (Velocity)

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/velocity.ini` | `ServerSettings` | `StaticMaxAge` | `31536000` (one year, in seconds) | installation |

Velocity writes the value into the server's configuration as `Q.web.static.maxAge`. `0` leaves the server's own
default, which permits caching and then makes the client revalidate anyway.

A year is what the other web server in front of this installation already sends for the same files. The trade is
that a file replaced in place is not noticed until the lifetime expires: clear the browser cache, or lower the
number, while you work on a stylesheet.

## What it is worth

Counted per asset on the front page:

| | Assets | Reusable with no request | Must revalidate |
|---|---|---|---|
| Before | 41 | **0** | **41** |
| After | 41 | **41** | **0** |
| nginx, for comparison | 41 | 41 | 0 |

This does not make a first visit faster. It makes every later visit and every in-site navigation cost 41 fewer round
trips, which a returning visitor experiences as the page painting immediately.

## What this is not

- **Not the server's response cache**, which stores rendered responses in the parent process; that is a separate
  feature, off by default ([Velocity response cache](../../features/6.0/velocity-response-cache.md)).
- **Not the kernel's static cache** (`StaticCache` in `site.ini`), which writes flat files.
- **Not the role-aware HTTP cache**, which keeps whole pages for signed-in visitors as well and serves them before the
  kernel starts: see [HTTP cache](httpcache.md).
- **Not a reduction of the rendering work.** The front page costs about 529 database queries, and no caching header
  changes that for a visitor who misses the cache.

## Related pages

- [HTTP cache (role-aware)](httpcache.md)
- [Velocity: running Exponential in a persistent-worker web server](../../features/6.0/velocity-persistent-worker-server.md)
- [Velocity response cache](../../features/6.0/velocity-response-cache.md)
- [Cache clears that move directories aside](../../features/6.0/cache-clear-rename-aside.md)
- [Behaviour changes, 16 to 30 September 2026](behaviour-changes-2026-09b.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
