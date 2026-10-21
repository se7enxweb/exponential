---
title: "HTTP security: headers, CSP, request rules and proxies"
audience: software-engineer, admin, developer
category: software-engineering
order: 710
summary: The HTTP security layer of Exponential - security headers from SecurityHeaders[], the Content Security Policy with modes, directives, nonces and reports, request rules that allow, deny, redirect or log after URL translation, trusted proxies for forwarded headers, safe redirects, and HTTP caching rules that keep private pages private.
keywords: HTTP security, security headers, Content Security Policy, CSP, nonce, csp_nonce, report-only, request rules, trusted proxies, X-Forwarded-For, HSTS, safe redirects, Cache-Control, software engineer
type: help
---
# HTTP security: headers, CSP, request rules and proxies

This page is for engineers who configure or review the HTTP layer of an Exponential site. Every mechanism here is a
setting in `site.ini` or its own INI file, so it can be reviewed and versioned like code.

## Contents

1. [Security headers](#security-headers)
2. [Content Security Policy](#content-security-policy)
3. [Request rules](#request-rules)
4. [Trusted proxies](#trusted-proxies)
5. [Redirects](#redirects)
6. [Caching headers](#caching-headers)
7. [A review list](#a-review-list)

## Security headers

`site.ini [HTTPHeaderSettings] SecurityHeaders[]` is sent on every page. Shipped:

```ini
SecurityHeaders[X-Content-Type-Options]=nosniff
SecurityHeaders[X-Frame-Options]=SAMEORIGIN
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self'
SecurityHeaders[Referrer-Policy]=strict-origin-when-cross-origin
SecurityHeaders[Permissions-Policy]=camera=(), microphone=(), payment=(), usb=()
SecurityHeaders[Strict-Transport-Security]=
```

`Strict-Transport-Security` is empty until you set it, because HSTS must only be sent once HTTPS works everywhere.
Under Velocity, `[HTTPSSettings] HSTSMaxAge` does the same for the server.

## Content Security Policy

`site.ini [ContentSecurityPolicy]`:

| Setting | Meaning |
|---|---|
| `Mode` | `disabled`, `report-only`, `enforce` or `auto` (shipped): report-only on administration siteaccesses, disabled on the others |
| `Directives[]` | one directive per entry; shipped `'self'` for scripts, styles and connections, `object-src 'none'` |
| `Nonce=enabled`, `NonceDirectives[]` | a per-request nonce added to `script-src`; templates print it with `csp_nonce()` |
| `ReportURI` | where browsers post reports; the kernel also has a `csp/report` collector, and violations go to `var/log/csp.log` |

Turning it on for a site: run it in `report-only`, collect what it would block (extensions and site designs are the
usual source), move inline scripts to files or give them the nonce, then switch to `enforce`
([Content Security Policy](../../features/6.0/content-security-policy.md),
[CSP: moving inline scripts out](../extension-development/part-4-upgrading/csp-moving-inline-scripts-out.md)).

## Request rules

Request rules run after URL translation and after the role policies allowed the view, and can allow, deny, redirect,
rewrite or log. The classic use is sending system URLs such as `/content/view/full/123` to their alias unless the user
holds `content/view_system_url`. Check the decision for any address without changing anything:

```bash
php bin/php/ezrequestrules.php -s site --check
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2
```

See [Request rules](../../features/6.0/request-rules.md) and [View full security](../../bc/6.0/view_full_security.md).

## Trusted proxies

Since 5 October 2026 the kernel believes `X-Forwarded-For`, `-Proto`, `-Port`, `-Server` and `-Host` only when the
connection comes from an address in `site.ini [HTTPHeaderSettings] TrustedProxies[]` (shipped: `127.0.0.1` and `::1`).
`X-Forwarded-For` is read from the right, skipping trusted proxies. Without the right list behind a load balancer,
HTTPS detection, absolute URLs, the client address of `DebugByIP`, the sign-in lockout, request rules by network and
the audit log are all wrong; with a too-wide list, a visitor can forge them ([Trusted proxies](../../bc/6.0/trusted-proxies.md)).

## Redirects

Redirects go through `eZRedirectManager`, which only follows hosts allowed in `[SiteSettings] AllowedRedirectHosts`
([Safe redirects](../../features/6.0/safe-redirects.md), [Redirect, query and image alias guards](../../bc/6.0/redirect-query-and-image-alias-guards.md)).

## Caching headers

Pages are sent `no-cache` by default. Caching headers for pages can be switched on for anonymous visitors only; a
signed-in user's page is never made cacheable by them ([HTTP caching](../../bc/6.0/http-caching.md)). Any cache you
add in front (CDN, proxy) must respect the same split, or one user's page reaches another.

## A review list

- [ ] `TrustedProxies[]` lists exactly the proxies in front of the site, no more.
- [ ] HSTS is set once HTTPS covers every host name.
- [ ] The CSP is enforced on the administration after a report-only period, and planned for the sites.
- [ ] No inline script without the nonce; no `on...=` attributes or `javascript:` URLs in templates.
- [ ] Request rules exist for system URLs if they must not be reachable.
- [ ] No proxy or CDN stores pages of signed-in users.

## Related pages

- [Security engineering overview](security-engineering-overview.md)
- [Content Security Policy for extensions](../extension-development/part-3-extension-developer/content-security-policy-for-extensions.md)
- [Content security policy, for application creators](../creators/app-creators/content-security-policy.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
