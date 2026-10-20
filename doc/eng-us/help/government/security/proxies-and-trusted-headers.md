---
title: Proxies and trusted headers
audience: government, admin, hosting
category: public-sector
order: 370
summary: For public sites behind load balancers, CDNs or TLS terminators - why forwarded headers must only be trusted from your own proxies, Exponential's TrustedProxies setting and reading from the right, Velocity's own list, and how to check that a visitor cannot spoof HTTPS or an address.
keywords: proxy, load balancer, CDN, reverse proxy, X-Forwarded-For, X-Forwarded-Proto, X-Forwarded-Host, TrustedProxies, ClientIpByCustomHTTPHeader, Velocity proxy trusted, IP spoofing, lockout, DebugByIP, audit addresses, public sector
type: help
---
# Proxies and trusted headers

This page is for administrators and hosting teams of sites behind a proxy, load balancer or CDN, common in government
hosting. The proxy tells the application what the visitor asked for in headers (`X-Forwarded-Proto`,
`X-Forwarded-Host`, `X-Forwarded-For`), but any visitor can send those headers too. Believed blindly, they let a
visitor fake HTTPS, a host name, or the address that the lockout, `DebugByIP`, request rules, the audit log and the
consent log record ([13.10 Behind a proxy: trusted headers](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)).

## Contents

1. [The rule](#the-rule)
2. [Which setup needs what](#which-setup-needs-what)
3. [Under Velocity](#under-velocity)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## The rule

Since 6.0.15 the kernel believes forwarded headers only when `REMOTE_ADDR` is in
`site.ini [HTTPHeaderSettings] TrustedProxies[]`; the default trusts only `127.0.0.1` and `::1`. The visitor's address
in `X-Forwarded-For` is read **from the right**: the walk passes trusted proxies and stops at the first address that is
not one, so a forged entry further left is never used.

## Which setup needs what

| Setup | Do |
|---|---|
| No proxy | nothing |
| A proxy on the same machine that sets the headers | nothing; make the proxy set `X-Forwarded-Proto` itself |
| A proxy, load balancer or CDN on another address | list its addresses or ranges, only those; make sure the application server cannot be reached around them |
| A front end that rewrites `REMOTE_ADDR` (`mod_remoteip`, nginx `realip`) | nothing in Exponential |
| You want the visitor's real address | `ClientIpByCustomHTTPHeader=X-Forwarded-For` with the proxies in `TrustedProxies[]` |

Example:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=10.0.0.0/16
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

Never widen the list to a whole provider network or `0.0.0.0/0`. Host names are not resolved, and an entry that is not
an address is ignored.

## Under Velocity

Velocity keeps its own list, `Q.webserver.proxy.trusted` (loopback by default), set in a snippet under
`/etc/vc/conf-available/`; list exactly your proxies there too
([13.12 Velocity-specific hardening](../../../install/13-security-hardening.md#1312-velocity-specific-hardening)).

## Checking

The installation guide's check: send a request directly, without the proxy, with a forged `X-Forwarded-Proto: https`;
the redirect must stay `http`, because the header changed nothing
([Check it](../../../install/13-security-hardening.md#check-it)). Also check that the audit records your test
address, not a forged one, after a test sign-in.

What goes wrong when it is misconfigured: users signed out on every page with `CookieSecure=true`, redirect loops,
every visitor recorded with the proxy's address, or a lockout that locks everyone behind the proxy.

## Related pages

- [13.10 Behind a proxy](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)
- [Forwarded headers are trusted only from configured proxies](../../../bc/6.0/trusted-proxies.md)
- [HTTP caching and CDNs](../../creators/website-creators/http-caching-and-cdns.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
