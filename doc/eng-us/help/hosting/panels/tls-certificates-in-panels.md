---
title: TLS certificates in panels
audience: hosting, admin
category: hosting
order: 100
summary: How HTTPS works for an Exponential site on a panel server: the panel's certificate for Apache or nginx, what Exponential itself must be told (site URLs, secure cookies, HSTS, trusted proxies), and how Velocity or FrankenPHP beside the panel can use the panel's certificate or their own, including the renewal trap.
keywords: TLS, HTTPS, certificate, Let's Encrypt, ACME, panel, Plesk, redirect, HSTS, Strict-Transport-Security, CookieSecure, SiteURL, SSLZoneSettings, trusted proxies, X-Forwarded-Proto, Velocity, FrankenPHP, renewal, exp:velocity ssl show
type: help
---
# TLS certificates in panels

This page is for administrators and support staff at hosting companies who set up HTTPS for Exponential sites on panel
servers. It answers who terminates TLS (the panel's web server, Velocity or FrankenPHP), what Exponential needs to be
told once a site is on HTTPS, and how to share a panel's certificate with Velocity or FrankenPHP without serving an
expired one after the next renewal.

## Contents

1. [Who terminates TLS](#who-terminates-tls)
2. [The panel's certificate for Apache or nginx](#the-panels-certificate-for-apache-or-nginx)
3. [What Exponential must be told](#what-exponential-must-be-told)
4. [Velocity or FrankenPHP beside the panel](#velocity-or-frankenphp-beside-the-panel)
5. [Watching expiry](#watching-expiry)
6. [Checklist](#checklist)

## Who terminates TLS

| Setup | TLS ends in | Certificate comes from |
|---|---|---|
| Panel's Apache (or nginx) with PHP-FPM | the panel's web server | the panel's certificate management ("`mod_ssl` plus certbot or a hosting panel") |
| The panel's nginx in front of Apache (as Plesk does) | the front nginx; nginx talks to Apache over HTTPS | the panel |
| Velocity facing visitors | Velocity's own process | your files, a self-signed fallback, or ACME (Let's Encrypt and other CAs) |
| FrankenPHP through Velocity | FrankenPHP | "your certificate or a self-signed one"; Velocity does not drive ACME for it |
| Velocity or FrankenPHP behind the panel's web server | the panel's web server | the panel; the proxy must be trusted |

Sources: [8.1](../../../install/08-serving-the-site.md#81-the-choice-in-one-table),
[Trusted proxies](../../../bc/6.0/trusted-proxies.md), [FrankenPHP](../../../bc/6.0/frankenphp.md).

## The panel's certificate for Apache or nginx

For a panel-managed web server, issue or upload the certificate in the panel and switch on the redirect from HTTP to
HTTPS. For Plesk the sources say exactly that: "install the certificate in Plesk (its Let's Encrypt extension or your
own) and switch on the redirect to HTTPS" ([8.5.3](../../../install/08-serving-the-site.md#853-plesk)). For other
panels, check your panel's documentation for where certificates are issued and renewed.

ACME HTTP-01 challenges need `/.well-known/` to be reachable. The shipped rewrite rules keep it so: dot files and
directories answer 404, with `.well-known` as the exception
([13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).

## What Exponential must be told

A certificate alone does not make an Exponential site "HTTPS everywhere". From
[13.9](../../../install/13-security-hardening.md#139-security-headers-and-https):

1. "Serve every name of the site over HTTPS."
2. "Redirect plain HTTP to HTTPS." For individual subtrees Exponential has its own `[SSLZoneSettings]`.
3. "Set `[Session] CookieSecure=true`."
4. "Turn HSTS on, starting short and raising it once everything works": under Apache or nginx with
   `SecurityHeaders[Strict-Transport-Security]=max-age=31536000` in `site.ini`, "sent by the kernel only over HTTPS";
   under Velocity with `[HTTPSSettings] HSTSMaxAge` (300 shipped).
5. "Keep certificates renewed and watched."

Make sure the addresses the site generates are HTTPS ones (`site.ini [SiteSettings] SiteURL` and the siteaccesses'
URLs) before raising HSTS ([8.3.9](../../../install/08-serving-the-site.md#839-https-served-by-velocity-itself)).

> **Warning**
> "A browser remembers HSTS for the whole period and refuses plain HTTP to that host, even after you lower the value.
> Raise it to a year only when every name is served over HTTPS for good."

**Behind a proxy.** When TLS ends in front of the application (a panel's nginx in front of Velocity or PHP-FPM, a
load balancer), Exponential decides whether the request was HTTPS from the proxy's headers, and only for trusted
proxies. "A proxy on the same machine that sets the headers" needs nothing: the default `TrustedProxies[]` covers
`127.0.0.1` and `::1`; make the proxy set `X-Forwarded-Proto` itself. Plesk's nginx-in-front-of-Apache setup needs
nothing either, because Apache's `mod_remoteip` makes the visitor the peer and Apache sets `HTTPS=on`
([13.10](../../../install/13-security-hardening.md#1310-behind-a-proxy-trusted-headers)).

```bash
curl -sI https://example.com/ | grep -i -E 'strict-transport|x-content-type|x-frame|referrer|permissions|content-security'
curl -sI https://example.com/user/login | grep -i set-cookie     # Secure; HttpOnly; SameSite=Lax
```

## Velocity or FrankenPHP beside the panel

When Velocity serves the same site on its own port next to the panel's web server, it terminates TLS itself and needs a
certificate of its own. Three routes:

| Route | How | Renewal |
|---|---|---|
| Velocity's ACME | an `https-acme` snippet in `/etc/vc/conf-available/`, enabled with `exp:velocity conf enable` | Velocity renews "when a third of the lifetime is left" |
| A copy of the panel's certificate | `[HTTPSSettings] Certificate` and `Key` pointing at the copied files, `Enabled=true` | repeat the copy after every renewal by the panel |
| Self-signed | nothing configured | for development and tests only |

Velocity's ACME needs port 80 of the server reachable for the HTTP-01 challenge. On a panel server the panel's web
server usually owns port 80; the sources list `webroot` "(when another server owns port 80)" and the `dns-01`
challenge as the options for that case ([8.3.9](../../../install/08-serving-the-site.md#839-https-served-by-velocity-itself),
[Velocity HTTPS and certificates](../../../features/6.0/velocity-https-certificates.md)).

**The renewal trap.** The project's test installation shares the Let's Encrypt certificate Plesk holds for the host
with FrankenPHP and Velocity. "Plesk renews the Let's Encrypt cert on its own schedule, writing a **new file under a
new name**; the exported copy then goes stale." The export must be "repeated after each renewal", followed by a
reload ([FrankenPHP, Renewal](../../../bc/6.0/frankenphp.md)):

```bash
# after copying the renewed certificate and key into place:
php bin/php/console exp:velocity graceful --engine=frankenphp --allow-root-user
```

Velocity itself checks its certificate files every `watchInterval` seconds (60) and swaps a changed, valid pair in live,
so for Velocity copying the new files into place is enough. "A pair that fails is never shown to a visitor; a
self-signed certificate stands in until the real one is usable again."

> **Tip**
> Automate the copy with the panel's renewal hook if it has one (check your panel's documentation), or with a daily
> job that compares the panel's current certificate with the copy. Then watch expiry as below, so a broken copy is
> noticed before visitors see the self-signed stand-in.

## Watching expiry

```bash
php bin/php/console exp:velocity ssl show --allow-root-user          # served certificate, every known one by expiry
php bin/php/console exp:velocity ssl show --json --allow-root-user   # for monitoring: watch daysLeft
php bin/php/console exp:velocity ssl renew --apply example.com --allow-root-user
```

"A 90-day CA certificate is renewed when about 30 days are left, so one with fewer than 14 days left means renewal has
been failing for more than two weeks." "Keep NTP running: certificates are checked against the system clock."
For certificates the panel serves, monitor the public endpoint with your usual tools. See
[Certificate expiry](../runbooks/certificate-expiry.md).

## Checklist

- [ ] Every name of every site has a CA certificate; HTTP redirects to HTTPS.
- [ ] `CookieSecure=true`; site URLs are HTTPS.
- [ ] HSTS started short, raised to a year only when everything is HTTPS.
- [ ] Proxies in front are trusted (or rewrite `REMOTE_ADDR` themselves, as Plesk's front does).
- [ ] Velocity or FrankenPHP beside the panel: certificate route chosen; copies refreshed after each panel renewal.
- [ ] Expiry monitored (`exp:velocity ssl show --json`, or the public endpoint).

## Related pages

- [Control panels: overview](README.md), [Plesk](plesk.md)
- [TLS and HTTPS](../security/tls-and-https.md), [Velocity HTTPS certificates](../servers/velocity-https-certificates.md)
- [Chapter 13: Security hardening](../../../install/13-security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
