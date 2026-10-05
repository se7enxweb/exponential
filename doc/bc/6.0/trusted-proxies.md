# Forwarded headers are trusted only from configured proxies (6.0.15)

From 5 October 2026 the kernel believes the forwarded request headers only when the connection comes from a proxy
listed in the new setting `site.ini [HTTPHeaderSettings] TrustedProxies[]`. This page says what changed, who has to
set the list, and how, for the usual front ends.

## What changed

`X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Port`, `X-Forwarded-Server` and `X-Forwarded-Host` are ordinary
request headers. A proxy writes them to tell the application what the visitor asked for, but any visitor can send them
too. The one thing the server knows for certain is the address of the peer that opened the connection,
`REMOTE_ADDR`, so that is what now decides.

| | Before | Now |
|---|---|---|
| `eZSys::isSSLNow()` | `X-Forwarded-Proto: https` (else `X-Forwarded-Port` equal to `SSLPort`, else `X-Forwarded-Server` equal to `SSLProxyServerName`) from **anyone** made the request HTTPS. | The same headers count only when `REMOTE_ADDR` is a trusted proxy. `$_SERVER['HTTPS']` and the port are checked first, as before, and need no proxy. |
| `eZSys::hostname()` | The first entry of `X-Forwarded-Host` from **anyone** replaced `Host`. | `X-Forwarded-Host` counts only from a trusted proxy; otherwise `Host`, then `SiteURL`, as before. |
| `eZSys::clientIP()` with `ClientIpByCustomHTTPHeader=X-Forwarded-For` | The **left-most** address of the header, which is the part the visitor writes. | Only from a trusted proxy, and read **from the right**: trusted proxies are skipped and the first address that is not one is the visitor. Anything further left is ignored. Every entry is checked with `filter_var( FILTER_VALIDATE_IP )`; an entry that is not an address ends the walk at the last trusted proxy. Otherwise `REMOTE_ADDR`. |
| HTTP cache (`httpcache.ini`) lookup | Worked out scheme and host from the same headers, from anyone. | Follows the kernel: the contract carries the trusted list (`trustedProxies`) and the lookup believes the headers only when it knows `REMOTE_ADDR` and that address is trusted. |
| Debug bar, "iptest" | `trusts_proxy` was true whenever `ClientIpByCustomHTTPHeader` was set. | True only when the request also came from a trusted proxy. |

Why it matters: a visitor could make the kernel believe a plain HTTP request was HTTPS (absolute URLs, redirects,
`CookieSecure=auto`, anything that asks for HTTPS, and cache keys that include the scheme), could put another host
name into every absolute URL the kernel builds, and could choose the address that `DebugByIP`,
`[UserSettings] TrustedIPList` (the sign-in lockout), request rules by network, the audit log and the consent log of
the e-mail preferences record.

When a header is a list that proxies append to, the value the **outermost trusted** proxy wrote is used, counted from
the right with the number of trusted hops seen in `X-Forwarded-For`. A proxy that *sets* the header (one value) is
read as before. This is what keeps a value the visitor sent in front of a proxy's own from being used.

`$_SERVER['HTTPS']` is the web server's own answer and is not affected: Apache, nginx, PHP-FPM, FrankenPHP and
Velocity's TLS listener set it, and the kernel believes it without any proxy setting.

## The setting

```ini
[HTTPHeaderSettings]
# Default, in settings/site.ini
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
```

- Entries are IP addresses or address/prefix ranges, IPv4 and IPv6: `192.0.2.10`, `10.0.0.0/8`, `2001:db8::/32`.
  An IPv4 peer reported as an IPv4-mapped IPv6 address (`::ffff:10.0.0.5`, what a dual-stack socket gives) matches
  its IPv4 entry. Host names are not resolved: an entry that is not an address or range is ignored, so a typo trusts
  nothing rather than something unintended.
- An override that starts with `TrustedProxies[]` on its own line replaces the list. `TrustedProxies[]` alone, with no
  entry after it, trusts no proxy at all.
- If the setting is missing altogether (an old `site.ini` copied over the shipped one), the kernel uses the same
  loopback default.
- `ClientIpByCustomHTTPHeader` is unchanged: it still names the header to read the visitor's address from
  (`X-Forwarded-For`, or a single-address header such as `X-Real-IP` or `CF-Connecting-IP`) and is still `false` by
  default. It now takes effect only for requests from a trusted proxy.
- `[SiteSettings] SSLProxyServerName` is unchanged and, like `X-Forwarded-Server`, only counts from a trusted proxy.

### Why loopback is trusted by default

Nothing outside the machine can open a connection from `127.0.0.1` or `::1`, so the default stands only for a proxy
on the same host: a TLS terminator, Varnish, or a local web server in front of PHP. That is the most common shape of a
proxied installation, and those sites keep working after the upgrade without a settings change. A proxy elsewhere
(another machine, a load balancer, a CDN) was never trusted implicitly and has to be listed.

Trusting a proxy means trusting it to **set** these headers itself rather than pass on what the visitor sent. A local
proxy that forwards a visitor's `X-Forwarded-Proto` unchanged would carry the visitor's value through. If you have
such a proxy and cannot change it, set `TrustedProxies[]` empty.

## Who has to act

- **No proxy in front** (Apache or nginx with PHP-FPM, Velocity serving the site directly): nothing to do. The
  forwarded headers a visitor sends are now ignored, which is the point.
- **A proxy on the same machine** (nginx or Apache in front of Velocity or PHP-FPM on `127.0.0.1`, Varnish, Plesk's
  nginx in front of Apache): nothing to do, as long as the proxy sets `X-Forwarded-Proto` (and `X-Forwarded-For` if
  you read it) itself. Check with the test at the end of this page.
- **A proxy, load balancer or CDN on another address**: add its addresses or ranges to `TrustedProxies[]` in
  `settings/override/site.ini.append.php`. Until you do, the kernel treats requests as plain HTTP from the proxy's
  address, which shows as `http://` absolute URLs, redirects to `http://`, and every visitor having the proxy's
  address.
- **Sites that set `ClientIpByCustomHTTPHeader`**: the visitor's address is now the right-most untrusted entry, not the
  left-most. Behind one proxy that appends (`$proxy_add_x_forwarded_for`, Apache `mod_proxy`) the answer is the same
  for honest visitors and no longer spoofable. Behind several proxies, list all of them.

## Examples

### Apache in front (mod_proxy)

Apache's `mod_proxy` appends to `X-Forwarded-For` and sets `X-Forwarded-Host` and `X-Forwarded-Server`, but does not
set `X-Forwarded-Proto`; set it, so the value a visitor sent is replaced:

```apache
ProxyPreserveHost On
ProxyPass        / http://127.0.0.1:8088/
ProxyPassReverse / http://127.0.0.1:8088/
RequestHeader set X-Forwarded-Proto "https"
```

On the same machine the default list already covers it. From another machine:

```ini
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=192.0.2.10
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

### nginx in front

```nginx
location / {
    proxy_pass http://127.0.0.1:8088;
    proxy_set_header Host              $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
    proxy_http_version 1.1;
}
```

Appending (`$proxy_add_x_forwarded_for`) is now safe: the kernel reads from the right. `$remote_addr`, which
replaces the header, works too.

### Apache with PHP-FPM behind a proxy that rewrites REMOTE_ADDR

Plesk puts nginx in front of Apache and Apache's `mod_remoteip` (`RemoteIPHeader X-Forwarded-For`,
`RemoteIPInternalProxy 127.0.0.1`) sets `REMOTE_ADDR` to the visitor; nginx talks to Apache over HTTPS, so Apache
sets `HTTPS=on`. The kernel then sees the visitor as the peer and needs no forwarded header: nothing to configure,
and `ClientIpByCustomHTTPHeader` can stay `false`. The same holds for nginx's `realip` module (`set_real_ip_from`,
`real_ip_header`).

### Exponential Velocity

Velocity works out the visitor's address itself, from its own list of trusted proxies (`Q.webserver.proxy.trusted`,
in `/etc/vc/conf-available/reverse-proxy.conf`; `127.0.0.1` and `::1` by default), and passes that address to the
application as `REMOTE_ADDR`. Configure proxies in front of Velocity **there**. The kernel then sees the visitor as
the peer, so:

- `ClientIpByCustomHTTPHeader` is not needed and `TrustedProxies[]` does not affect the visitor's address.
- `$_SERVER['HTTPS']` is set by the engine and the kernel takes it as the server's answer: `on` on Velocity's TLS
  listener (`https://example.com:8080/`).
- `X-Forwarded-Host` reaches the kernel from the visitor's address and is not used. Let the proxy pass the original
  `Host` (`ProxyPreserveHost On`, `proxy_set_header Host $host`).
- The HTTP cache lookup inside the engine is not told the peer's address and so ignores the forwarded headers too,
  which matches what the kernel stores. An engine that passes `remoteAddr` to the cache gets the same treatment as the
  kernel.

### Cloud load balancers and CDNs

List the ranges the provider publishes, and only those:

```ini
[HTTPHeaderSettings]
TrustedProxies[]
# the load balancer's subnet (AWS ALB, GCP, Azure, Hetzner ...)
TrustedProxies[]=10.0.0.0/16
# Cloudflare, two of its published ranges
TrustedProxies[]=173.245.48.0/20
TrustedProxies[]=2400:cb00::/32
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

Comments go on lines of their own: after a value, a single `#` is read as part of the value, and such an entry is not
an address and is ignored.

- AWS ALB, Google Cloud and Azure load balancers append to `X-Forwarded-For` and set `X-Forwarded-Proto`; trust the
  subnet the load balancer's private addresses come from.
- Cloudflare appends to `X-Forwarded-For` and also sends `CF-Connecting-IP` (one address); either works as
  `ClientIpByCustomHTTPHeader`. Its ranges change from time to time: take them from Cloudflare's published list.
- A CDN in front of a load balancer: list both, the walk from the right passes through all of them.
- Make sure the origin cannot be reached around the load balancer (firewall, security group), or a visitor talks to
  PHP directly and the kernel, correctly, ignores the headers.

## Checking it

With the site reachable directly (no proxy), a visitor's headers must change nothing:

```bash
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
# http://example.com/   (before: https://example.com/)
```

Behind a proxy, the debug bar's "Is my address listed?" (iptest) shows `REMOTE_ADDR`, the address the kernel uses,
and whether the proxy header was read. `eZSys::trustedProxies()` returns the list in effect;
`eZSys::isFromTrustedProxy()` says whether the current request came from one.

## Code

- `lib/ezutils/classes/eztrustedproxy.php` (`eZTrustedProxy`): matching of addresses and ranges, the right-to-left
  walk, the hop count. Free of every other class, so the HTTP cache's early exit and Velocity's process can load it.
- `lib/ezutils/classes/ezsys.php`: `isSSLNow()`, `hostname()`, `clientIP()`, new `trustedProxies()` and
  `isFromTrustedProxy()`.
- `kernel/private/classes/httpcache/ezphttpcachecontract.php` and `ezphttpcachelistener.php`: the lookup's view of
  scheme and host.
- Tests: `tests/tests/lib/ezutils/eZTrustedProxyTest.php`, `eZSysTrustedProxyTest.php`, `eZSysTest.php`, and HC-15,
  HC-16 in `tests/tests/kernel/classes/httpcache/ezpHttpCacheContractTest.php` (suites `lib` and `kernel-classes`).
