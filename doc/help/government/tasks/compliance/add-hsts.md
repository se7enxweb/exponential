---
title: Add HSTS
audience: government, admin, hosting
category: public-sector
order: 110
summary: Step by step - turn on HTTP Strict Transport Security for a site that is fully HTTPS, with Exponential's SecurityHeaders setting (Apache, nginx) or Velocity's HSTSMaxAge, starting short and raising the value in stages.
keywords: HSTS, Strict-Transport-Security, max-age, includeSubDomains, preload, HSTSMaxAge, SecurityHeaders, HTTPS, TLS, staged rollout, public sector
type: help
---
# Add HSTS

For administrators and hosting teams. HSTS is empty by default in Exponential and must be set by the operator once the
whole site is HTTPS-only ([HTTPS and HSTS](../../security/https-and-hsts.md)). A browser remembers HSTS for the whole
period, even if you lower it later; go in stages.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [If something goes wrong](#if-something-goes-wrong)
5. [Related pages](#related-pages)

## Before you start

- Every name of the site is served over HTTPS with a valid certificate; HTTP redirects to HTTPS.
- Every page and asset loads over HTTPS (no mixed content).
- Behind a proxy: `TrustedProxies[]` configured so the kernel knows the request was HTTPS.

## Steps

**Apache or nginx with PHP-FPM** (the kernel sends it only over HTTPS):

```bash
./console exp:ini set 'site.ini/HTTPHeaderSettings/SecurityHeaders[Strict-Transport-Security]' 'max-age=300' global --dry-run --allow-root-user
./console exp:ini set 'site.ini/HTTPHeaderSettings/SecurityHeaders[Strict-Transport-Security]' 'max-age=300' global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

then reload PHP-FPM.

**Velocity**: `settings/override/velocity.ini.append.php`, `[HTTPSSettings] HSTSMaxAge=300` (300 is the shipped value;
also `HSTSIncludeSubDomains`, `HSTSPreload`), then `./console exp:velocity deploy --allow-root-user`.

Raise in stages: 300 seconds, one day (86400), 30 days (2592000), one year (31536000), each after a period without
problems.

## Check that it worked

```bash
curl -sI https://www.example.gov/ | grep -i strict-transport-security
```

shows the value you set. Over plain HTTP the header is not sent (correct).

## If something goes wrong

- A name that is not HTTPS becomes unreachable for browsers that saw the header, until the period ends: fix HTTPS on
  that name; lowering the value does not help browsers that already stored it.
- Add `includeSubDomains` only when every subdomain is HTTPS-only.

## Related pages

- [HTTPS and HSTS](../../security/https-and-hsts.md)
- [13.9 Security headers and HTTPS](../../../../install/13-security-hardening.md#139-security-headers-and-https)
- [Check the security headers](check-the-security-headers.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
