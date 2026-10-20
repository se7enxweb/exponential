---
title: "Going live: hosts, HTTPS and siteaccesses"
audience: website-creator, admin
category: website-creation
order: 670
summary: Move a site to its public addresses - host matching for each siteaccess, SiteURL, DNS, HTTPS certificates with Velocity or your web server, redirects to one host, proxies, and the checks that prove each address reaches the right siteaccess.
keywords: go live, hosts, HTTPS, TLS, certificate, ACME, Let's Encrypt, DNS, siteaccess, host matching, SiteURL, redirect, www, TrustedProxies, website creator
type: help
---
# Going live: hosts, HTTPS and siteaccesses

This page is for website creators who put a site on its real host names. Three things must agree: DNS sends the host to
your server, the server (Velocity or a web server) answers it with a certificate, and Exponential maps the host to the
right siteaccess.

## Contents

1. [Map hosts to siteaccesses](#map-hosts-to-siteaccesses)
2. [SiteURL](#siteurl)
3. [HTTPS](#https)
4. [One host, one scheme](#one-host-one-scheme)
5. [Behind a proxy](#behind-a-proxy)
6. [Checks](#checks)

## Map hosts to siteaccesses

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=admin.example.com;admin
```

The empty line clears inherited entries; list every siteaccess you need
([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
`HostMatchMethod=start` lets a test host like `www.example.com.test.local` reach the same siteaccess
([Siteaccess by host prefix](../../../features/6.0/siteaccess-host-and-language-matching.md)).

## SiteURL

Set `[SiteSettings] SiteURL` in each siteaccess to its public host without scheme (`www.example.com`). Links in mail,
feeds and sitemaps are built from it. Then clear the INI cache.

## HTTPS

| Served by | Certificates |
|---|---|
| Velocity | its own certificates, a self-signed fallback, or ACME (Let's Encrypt) as background jobs ([Velocity HTTPS and certificates](../../../features/6.0/velocity-https-certificates.md)) |
| Apache, nginx | `mod_ssl` or nginx TLS with a certificate tool or your hosting panel ([Serving the site](../../../install/08-serving-the-site.md)) |

Every host name of every siteaccess must be in the certificate. Add `Strict-Transport-Security` once HTTPS works
everywhere ([8.3.9](../../../install/08-serving-the-site.md#839-https-served-by-velocity-itself)).

## One host, one scheme

Redirect `http://` to `https://` and the other spellings (`example.com`) to the one you chose (`www.example.com`) in the
server, with a 301. One address per page is better for visitors and search engines.

## Behind a proxy

When a load balancer or CDN terminates HTTPS, list its address in `[HTTPHeaderSettings] TrustedProxies[]`; otherwise the
site believes every request is plain HTTP and records the proxy's address as every visitor's
([8.8 Behind a reverse proxy](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).

## Checks

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' https://admin.example.com/
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' http://example.com/
```

Expect `200` for the site, a redirect to the login for the administration, and a `301` to `https://www.example.com/`
for the other spelling. If two hosts answer the same, the host map does not match: the host must be exactly as the
browser sends it, lower case, without port.

## Related pages

- [The launch checklist](launch-checklist.md)
- [Setting up several sites in one installation](multisite-setup.md)
- [Hosting requirements for a website](hosting-for-a-website.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
