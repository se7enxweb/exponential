---
title: Check security headers
audience: government, admin, hosting
category: public-sector
order: 310
summary: Step by step - verify that every page of a public website carries the five security headers Exponential sends by default, that dot files and PHP files below the root answer 404, and that the session cookie has the right flags; change a header deliberately when needed.
keywords: security headers, X-Content-Type-Options, X-Frame-Options, Content-Security-Policy, Referrer-Policy, Permissions-Policy, HSTS, cookies, HttpOnly, SameSite, Secure, curl, hardening check
type: help
---
# Check security headers

This task is for administrators and security officers, at go-live, after upgrades and in periodic security reviews.
Since September 2026 a default installation sends these headers; verify instead of trusting
([Security and audit, step 1](../../../../guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Changing a header](#changing-a-header)
4. [Related pages](#related-pages)

## Before you start

- `curl` on any machine that reaches the site; a browser for the cookie check.

## Steps

1. Headers on the public site and the administration:

   ```bash
   curl -sI https://www.example.gov/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
   curl -sI https://www.example.gov/admin/user/login | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
   ```

   Expected: `x-content-type-options: nosniff`, `x-frame-options: SAMEORIGIN`, `content-security-policy:
   frame-ancestors 'self'`, `referrer-policy: strict-origin-when-cross-origin`, `permissions-policy: camera=(),
   microphone=(), payment=(), usb=()`. The administration also sends a report-only Content Security Policy.
2. Files that must not be served:

   ```bash
   for p in .env .git/config extension/expservices/ezinfo.php; do
     curl -s -o /dev/null -w "$p %{http_code}\n" https://www.example.gov/$p
   done
   ```

   Expected: `404` three times.
3. The session cookie: sign in, open the browser's developer tools, Cookies: `HttpOnly`, `SameSite=Lax`, and on
   HTTPS `Secure`.

## Changing a header

Read the value first, then change it in the override scope through your change process:

```bash
./console exp:ini get 'site.ini/HTTPHeaderSettings/SecurityHeaders[Content-Security-Policy]' --allow-root-user
```

Turn on HSTS only when every name of the site is HTTPS for good:
`SecurityHeaders[Strict-Transport-Security]=max-age=31536000` (under Velocity, `[HTTPSSettings] HSTSMaxAge`).

Record the output of the checks with your security review.

## Related pages
- [Hardening the host](../../operations/hosting/hardening-the-host.md)
- [CSP reports](../../operations/monitoring/csp-reports.md)
- [Security standard](../../../../standards/security.md)
- [Security review checklist (hosting)](../../../hosting/checklists/security-review.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
