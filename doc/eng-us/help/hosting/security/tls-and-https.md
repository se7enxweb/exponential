---
title: TLS and HTTPS
audience: hosting, admin
category: hosting
order: 30
summary: How a host serves every Exponential site over HTTPS only: who terminates TLS, redirecting plain HTTP, HSTS in the kernel and in Velocity, secure session cookies, proxies and load balancers that terminate TLS, certificate monitoring, and the checks that prove it.
keywords: TLS, HTTPS, SSL, certificate, HSTS, Strict-Transport-Security, CookieSecure, redirect, X-Forwarded-Proto, TrustedProxies, ClientIpByCustomHTTPHeader, Velocity, HTTPSSettings, ACME, Let's Encrypt, load balancer, CDN, SSLZoneSettings
type: help
---
# TLS and HTTPS

This page is for system administrators who serve Exponential sites over HTTPS, and for support staff who meet the
usual symptoms: `http://` links behind a TLS proxy, sign-in loops, or a browser warning. It says who can terminate
TLS, which Exponential settings must agree with that choice, and how to check the result. The reference is
[13.9 Security headers and HTTPS](../../../install/13-security-hardening.md#139-security-headers-and-https) and
[13.10 Behind a proxy](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers).

## Contents

1. [Who terminates TLS](#who-terminates-tls)
2. [HTTPS everywhere, step by step](#https-everywhere-step-by-step)
3. [HSTS](#hsts)
4. [Secure cookies](#secure-cookies)
5. [Proxies that terminate TLS](#proxies-that-terminate-tls)
6. [Watching certificates](#watching-certificates)
7. [Checks](#checks)
8. [What goes wrong](#what-goes-wrong)
9. [Checklist](#checklist)

## Who terminates TLS

| Setup | TLS is ended by | Certificate handled by |
|---|---|---|
| Apache with PHP-FPM | Apache `mod_ssl` | the panel or an ACME client on the server |
| nginx with PHP-FPM | nginx `ssl` | the panel or an ACME client on the server |
| Velocity facing visitors | Velocity itself, with HTTP/2 | Velocity: a certificate you give it, a self-signed stand-in, or ACME (`letsencrypt` mode) |
| Velocity or PHP-FPM behind a proxy, load balancer or CDN | the proxy | the proxy's operator |

Velocity needs no `mod_ssl`, stunnel or proxy for HTTPS; it verifies a certificate pair before using it, swaps a
renewed pair in live within `watchInterval` (60 seconds), and serves a self-signed stand-in rather than a broken pair
([8.3.9](../../../install/08-serving-the-site.md#839-https-served-by-velocity-itself)). Details for hosts:
[Velocity HTTPS certificates](../servers/velocity-https-certificates.md); panel certificates:
[TLS certificates in panels](../panels/tls-certificates-in-panels.md).

## HTTPS everywhere, step by step

1. **Serve every name of the site over HTTPS**, including `www.` and the administration's host name.
2. **Redirect plain HTTP to HTTPS** in Apache or nginx (sections 8.5 and 8.6 of
   [Chapter 8](../../../install/08-serving-the-site.md)). Velocity listens on both ports and has no setting that
   redirects one to the other; put the redirect in front, or close the plain port to the outside.
   Exponential's own `[SSLZoneSettings]` in `site.ini` can switch individual subtrees, for a site that cannot be
   HTTPS-only yet.
3. **Make the site build HTTPS addresses**: `site.ini [SiteSettings] SiteURL` and the siteaccesses' URLs.
4. **Set `[Session] CookieSecure=true`** (see [Secure cookies](#secure-cookies)).
5. **Turn HSTS on**, short first (see [HSTS](#hsts)).
6. **Watch certificate expiry** (see [Watching certificates](#watching-certificates)).

## HSTS

A browser that has seen `Strict-Transport-Security` refuses plain HTTP to that host for the whole period, even after
you lower the value. Start short, raise it to a year only when every name is served over HTTPS for good.

| Who sends it | Setting | Shipped |
|---|---|---|
| Velocity, on every HTTPS answer | `velocity.ini [HTTPSSettings] HSTSMaxAge`, `HSTSIncludeSubDomains`, `HSTSPreload` | `300`, `disabled`, `disabled` |
| the kernel, under Apache or nginx | `site.ini [HTTPHeaderSettings] SecurityHeaders[Strict-Transport-Security]` | empty (off); sent only over HTTPS |

```ini
# settings/override/site.ini.append.php, once every name is HTTPS for good
[HTTPHeaderSettings]
SecurityHeaders[Strict-Transport-Security]=max-age=31536000
```

Preload needs `HSTSIncludeSubDomains` and at least a year
([8.3.9, Strict-Transport-Security](../../../install/08-serving-the-site.md#strict-transport-security)).

> **Warning**
> `includeSubDomains` applies to every subdomain of the name, including ones that other teams or other customers may
> run on plain HTTP. Agree it with the owner of the domain before turning it on.

## Secure cookies

`settings/site.ini [Session]`, with the defaults that apply while the keys are unset
([13.7](../../../install/13-security-hardening.md#137-sessions-and-cookies)):

| Key | Default | For an HTTPS-only site |
|---|---|---|
| `CookieSecure` | `auto`: `Secure` when the request itself came over HTTPS | `true` |
| `CookieHttponly` | `true` | keep |
| `CookieSameSite` | `Lax` | keep; `None` only with `Secure` |

```ini
# settings/override/site.ini.append.php
[Session]
CookieSecure=true
```

`CookieSecure=true` makes sure the session cookie is never sent over plain HTTP, even on a request that wrongly
arrives without TLS.

## Proxies that terminate TLS

When a proxy ends TLS, PHP sees a plain HTTP request and learns about HTTPS only from headers such as
`X-Forwarded-Proto`. Any visitor can send those headers too, so since 6.0.15 the kernel believes them **only when the
connection comes from an address in `site.ini [HTTPHeaderSettings] TrustedProxies[]`**. The default is the loopback
addresses `127.0.0.1` and `::1`.

| Setup | What to do |
|---|---|
| No proxy | nothing; forwarded headers a visitor sends are ignored |
| A proxy on the same machine that sets the headers | nothing; make the proxy set `X-Forwarded-Proto` itself |
| A proxy, load balancer or CDN on another address | list its addresses or ranges, and only those; firewall the application server so it cannot be reached around them |
| A front end that rewrites `REMOTE_ADDR` (`mod_remoteip`, nginx `realip`, as in Plesk's nginx in front of Apache) | nothing; `ClientIpByCustomHTTPHeader` stays `false` |

```ini
# settings/override/site.ini.append.php: a load balancer on a private subnet
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=10.0.0.0/16
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

The visitor's address is read from the right of `X-Forwarded-For`, so a forged entry further left is never used.
Entries are addresses or ranges; host names are not resolved, and a comment on the same line makes the entry invalid.

Under Velocity, list the proxies in Velocity's own `Q.webserver.proxy.trusted`, in a snippet under
`/etc/vc/conf-available/` enabled with `exp:velocity conf enable <name>`; Velocity then passes the visitor as
`REMOTE_ADDR` and sets `$_SERVER['HTTPS']` itself (from engine release 0.0.4.44 on, only for trusted peers). Details:
[13.10, Under Velocity](../../../install/13-security-hardening.md#under-velocity) and
[Forwarded headers are trusted only from configured proxies](../../../bc/6.0/trusted-proxies.md).

## Watching certificates

- Velocity: `php bin/php/console exp:velocity ssl show --json --allow-root-user` and watch `daysLeft`. A 90-day CA
  certificate is renewed when about 30 days are left, so fewer than 14 days left means renewal has failed for more than
  two weeks ([8.3.9, Inspecting certificates](../../../install/08-serving-the-site.md#inspecting-certificates)).
- Apache or nginx: your ACME client's timer or the panel's renewal, plus an external expiry check.
- Keep NTP running: certificates are checked against the system clock.

When it has already happened: [Certificate expiry](../runbooks/certificate-expiry.md).

## Checks

```bash
# headers on a page
curl -sI https://example.com/ | grep -i -E 'strict-transport|x-content-type|x-frame|referrer|permissions|content-security'
# the session cookie: expect Secure; HttpOnly; SameSite=Lax
curl -sI https://example.com/user/login | grep -i set-cookie
# directly, no proxy: a visitor's own header must change nothing (the redirect stays http)
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
```

## What goes wrong

| Symptom | Cause |
|---|---|
| Absolute URLs and redirects say `http://` behind an HTTPS proxy; a redirect loop on the sign-in page | the proxy is not in `TrustedProxies[]` (or not in Velocity's list), or it does not send `X-Forwarded-Proto` |
| Signed-in users are signed out on every page with `CookieSecure=true` | the same: the kernel thinks the request is plain HTTP |
| Every visitor has the proxy's address in the audit and lockout | `ClientIpByCustomHTTPHeader` not set, or the proxy not trusted |
| A visitor can still choose the scheme or the address | a trusted range far wider than your proxies, or the application server reachable directly |
| Browser warns about the certificate on a Velocity site | Velocity serves its self-signed stand-in: the real pair is expired, mismatched or not yet issued; `exp:velocity ssl show` |

## Checklist

- [ ] Every name of the site serves HTTPS; plain HTTP redirects.
- [ ] `SiteURL` and the siteaccesses' URLs use `https://`.
- [ ] `[Session] CookieSecure=true`.
- [ ] HSTS on, short first; raised to a year once stable; `includeSubDomains` agreed with the domain owner.
- [ ] Behind a proxy: `TrustedProxies[]` (or `Q.webserver.proxy.trusted`) lists exactly your proxies; the
      `X-Forwarded-Proto` check above changes nothing.
- [ ] Certificate expiry monitored; NTP running.

## Related pages

- [Security for hosts: overview](README.md)
- [Velocity HTTPS certificates](../servers/velocity-https-certificates.md)
- [TLS certificates in panels](../panels/tls-certificates-in-panels.md)
- [Varnish and reverse proxies](../performance/varnish-and-reverse-proxies.md)
- [Certificate expiry](../runbooks/certificate-expiry.md)
- [Chapter 13: Security hardening for production](../../../install/13-security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
