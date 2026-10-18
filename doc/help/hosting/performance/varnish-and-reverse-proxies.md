---
title: Varnish and reverse proxies
audience: hosting, admin
category: hosting
order: 70
summary: Putting a reverse proxy or a caching proxy such as Varnish in front of Exponential sites: whether you need one, trusting the proxy for HTTPS and the visitor's address, what may and may not be cached, and how to purge when Exponential sends no purges.
keywords: reverse proxy, caching proxy, Varnish, xkey, purge, ban, TrustedProxies, ClientIpByCustomHTTPHeader, X-Forwarded-Proto, X-Forwarded-For, X-Forwarded-Host, Q.webserver.proxy.trusted, TagHeader, content/cache event, load balancer, session cookie
type: help
---
# Varnish and reverse proxies

This page is for system administrators at hosting companies who run a reverse proxy, a caching proxy such as Varnish,
or a load balancer in front of customer sites. It answers: whether Exponential needs one at all, what the proxy must
send and be trusted for, what a caching proxy may store, and how to purge it. Varnish's own configuration language is
not described here; check its documentation. The sources are
[Chapter 8, section 8.8](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy),
[Forwarded headers are trusted only from configured proxies](../../../bc/6.0/trusted-proxies.md) and
[Chapter 15, section 15.14](../../../install/15-migrating-from-5x-legacy.md#1514-caches).

## Contents

1. [Do you need a proxy?](#do-you-need-a-proxy)
2. [What the proxy must send](#what-the-proxy-must-send)
3. [Trusting the proxy](#trusting-the-proxy)
4. [A caching proxy: what may be stored](#a-caching-proxy-what-may-be-stored)
5. [Purging: Exponential sends none](#purging-exponential-sends-none)
6. [Checks and symptoms](#checks-and-symptoms)
7. [Checklist](#checklist)

## Do you need a proxy?

| Reason | Needed? |
|---|---|
| TLS termination | no for Velocity, which serves HTTPS itself; yes when a load balancer or CDN already terminates TLS |
| Several services or customer instances behind one public address | a plain reverse proxy is the right shape |
| Running Velocity as an ordinary user on a high port | a proxy or a firewall redirect in front |
| Caching anonymous pages | not necessarily: Velocity's response cache and the role-aware HTTP cache do this inside Exponential |

For sites migrated from older platforms that used Varnish, the installation book says: "Either drop Varnish (the HTTP
cache or Velocity's response cache does the same job inside Exponential), or let it cache anonymous pages for a short
time only ..., or purge it yourself."

## What the proxy must send

| Header | Why |
|---|---|
| `X-Forwarded-Proto` (set by the proxy, never passed through from the visitor) | so generated URLs, redirects and the session cookie's `Secure` flag are right |
| `X-Forwarded-For` (appended or set) | the visitor's address; Exponential reads the list from the right, so appending is safe |
| the original `Host` | Velocity does not use `X-Forwarded-Host`; under PHP-FPM a trusted `X-Forwarded-Host` replaces `Host` |

Derived examples for nginx and Apache in front of Velocity are on
[Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md#shape-1-a-proxy-in-front-of-velocity).

## Trusting the proxy

Forwarded headers count only from a trusted peer. There are two lists:

| Server behind the proxy | List | Default |
|---|---|---|
| Apache or nginx with PHP-FPM, FrankenPHP | `site.ini [HTTPHeaderSettings] TrustedProxies[]` | `127.0.0.1`, `::1` |
| Velocity | `Q.webserver.proxy.trusted` in the engine configuration | `127.0.0.1`, `::1` |

A proxy on the same machine (including Varnish on loopback) is covered. A proxy elsewhere must be listed:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
TrustedProxies[]=10.0.0.0/16
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

- Entries are addresses or ranges, IPv4 and IPv6; host names are not resolved. An override that starts with
  `TrustedProxies[]` on its own line replaces the list.
- Comments go on lines of their own: after a value a `#` is read as part of the value and the entry is ignored.
- `ClientIpByCustomHTTPHeader` is needed under PHP-FPM only; under Velocity the engine hands the visitor's address
  over as `REMOTE_ADDR`.
- Trusting a proxy means trusting it to **set** the headers. A local proxy that passes on a visitor's own
  `X-Forwarded-Proto` would carry it through; if you cannot change such a proxy, set `TrustedProxies[]` empty.
- Make sure the origin cannot be reached around the proxy (firewall, security group).

For Velocity, put the list in a snippet such as `/etc/vc/conf-available/reverse-proxy.conf` and enable it with
`exp:velocity conf enable reverse-proxy` ([Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md#who-trusts-the-proxy)).
Engine 0.0.4.44 or later is needed for the forwarded protocol to be read only from trusted proxies.

## A caching proxy: what may be stored

> **Warning**
> A caching proxy must not store signed-in pages. Do not cache responses that set or depend on the session cookie
> ([Chapter 8, section 8.8](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).

What Exponential gives a proxy to work with:

- Pages are `no-cache` by default. To let a proxy store anonymous pages, switch on `[HTTPHeaderSettings]` with
  `OnlyForAnonymous=enabled` and a short `max-age` ([HTTP cache headers](http-cache-headers.md)).
- Pages that carry a form token are sent `private`.
- The session cookie's name follows `site.ini [Session] SessionNameHandler`: with `default` PHP names it; with
  `custom` it is `SessionNamePrefix` (shipped `eZSESSID`) plus a per-siteaccess digest. The kernel also sets
  `is_logged_in` for signed-in visitors. A proxy rule that bypasses the cache on either cookie matches what Velocity's
  own response cache does.
- Static files carry long lifetimes under Velocity (`StaticMaxAge`) and can be cached freely.

## Purging: Exponential sends none

Exponential sends no purge or ban requests to a proxy; there is no `PURGE` or `BAN` in the kernel. A proxy that keeps
pages longer than their `max-age` will serve stale pages after an edit. Options from the installation book:

1. **Short lifetimes only:** let the proxy keep anonymous pages for the `max-age` you configured, and nothing longer.
2. **Purge by tag:** `httpcache.ini [HttpCacheSettings] TagHeader=xkey` names each page's purge tags in a header for
   the Varnish xkey module (`Surrogate-Key` for Fastly, `Cache-Tag` for Cloudflare). The setting and the tags belong
   to the role-aware HTTP cache ([HTTP cache](../../../bc/6.0/httpcache.md)); test that pages carry them.
3. **Purge on publish yourself:** the kernel emits `content/cache` with the node and object ids on every publish
   (`kernel/classes/ezcontentcachemanager.php`), which a listener in `site.ini [Event] Listeners[]` can turn into
   purges.
4. **Drop the proxy cache** and use Velocity's response cache or the HTTP cache, which purge themselves.

## Checks and symptoms

With the site reachable directly, a forwarded header from a visitor must change nothing:

```bash
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
# expected: http://example.com/   -- https:// here means the header was believed
```

| Symptom | Cause | Fix |
|---|---|---|
| redirects and absolute links go to `http://` | the proxy is not trusted, or sends no `X-Forwarded-Proto` | list it; set the header |
| every visitor has the proxy's address (sign-in lockout, logs) | the proxy is not trusted, or `ClientIpByCustomHTTPHeader` not set under PHP-FPM | list the proxy; set the header setting |
| a redirect loop after switching to HTTPS | the server does not know the visitor used HTTPS | as the first row |
| signed-in pages served to other visitors | a caching proxy stores pages with a session cookie | bypass the cache on the session cookie and `is_logged_in` |
| pages stay stale behind Varnish | Exponential sends no purges | one of the purge options above |

## Checklist

- [ ] The proxy sets `X-Forwarded-Proto` and appends or sets `X-Forwarded-For`; passes `Host`.
- [ ] The proxy is trusted (`TrustedProxies[]`, or `Q.webserver.proxy.trusted` for Velocity); the origin is not
      reachable around it.
- [ ] The curl check passes directly and through the proxy.
- [ ] A caching proxy bypasses requests with the session cookie or `is_logged_in`, and stores only `public` pages.
- [ ] A purge strategy is chosen and written into the plan.

## Related pages

- [Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md)
- [HTTP cache headers](http-cache-headers.md)
- [CDN](cdn.md)
- [Forwarded headers are trusted only from configured proxies](../../../bc/6.0/trusted-proxies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
