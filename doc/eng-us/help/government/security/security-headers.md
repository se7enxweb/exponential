---
title: Security headers
audience: government, admin, hosting
category: public-sector
order: 160
summary: For administrators of public sites - the five security headers Exponential sends on every page by default, how to check them, how to change one safely, and how Velocity, Apache and nginx handle static files.
keywords: security headers, X-Content-Type-Options, X-Frame-Options, Content-Security-Policy frame-ancestors, Referrer-Policy, Permissions-Policy, Strict-Transport-Security, HTTPHeaderSettings, SecurityHeaders, clickjacking, Velocity ResponseHeaders, public sector
type: help
---
# Security headers

This page is for administrators and hosting teams. HTTP security headers tell browsers to refuse common attacks:
content sniffing, framing (clickjacking), leaking addresses in referrers, and access to the camera or microphone.
Exponential sends five on every page, the administration included
([13.9 Security headers and HTTPS](../../../install/13-security-hardening.md#139-security-headers-and-https)).

## Contents

1. [The defaults](#the-defaults)
2. [Checking them](#checking-them)
3. [Changing one](#changing-one)
4. [Static files](#static-files)
5. [Related pages](#related-pages)

## The defaults

`settings/site.ini [HTTPHeaderSettings] SecurityHeaders[]`:

| Header | Default | Protects against |
|---|---|---|
| `X-Content-Type-Options` | `nosniff` | browsers guessing a file's type and running it |
| `X-Frame-Options` | `SAMEORIGIN` | the site being framed by another site |
| `Content-Security-Policy` | `frame-ancestors 'self'` | the same, in the modern form |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | full addresses leaking to other sites |
| `Permissions-Policy` | `camera=(), microphone=(), payment=(), usb=()` | pages using device features |
| `Strict-Transport-Security` | empty (off; only ever sent over HTTPS) | see "HTTPS and HSTS" |

Pages answered from the kernel's HTTP cache carry the same headers.

## Checking them

```bash
curl -sI https://www.example.gov/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security|strict-transport'
curl -sI https://admin.example.gov/user/login | grep -i -E 'x-frame|content-security'
```

Expected: the five lines of the table (order may differ). Record the output as evidence in your security
documentation after each change.

## Changing one

Use an override, never `settings/site.ini` itself. Read the current value first:

```bash
./console exp:ini get 'site.ini/HTTPHeaderSettings/SecurityHeaders[Content-Security-Policy]' --allow-root-user
```

Example: let a trusted editing host frame the administration:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self' https://edit.example.gov
```

An empty value in an override drops a header; names and values that could split the response are ignored. Per path,
`[HTTPHeaderSettings] HeaderList` can still override any header. Clear the INI cache and reload PHP afterwards.

## Static files

- **Velocity** sends four of them (all but the content security policy) on what it answers itself (static files,
  resized images, error pages, redirects) through `settings/velocity.ini [ServerSettings] ResponseHeaders[]`.
- **Apache and nginx** send nothing extra on static files by themselves; add them with `mod_headers`
  (`Header always set X-Content-Type-Options "nosniff"`) or `add_header ... always;` if you want them there.
- SVG files from the storage get a sandboxing policy from the shipped `.htaccess_root` when `mod_headers` is present.

## Related pages

- [13.9 Security headers and HTTPS](../../../install/13-security-hardening.md#139-security-headers-and-https)
- [Security and audit, 1](../../../guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes)
- [TLS and HTTPS for hosts](../../hosting/security/tls-and-https.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
