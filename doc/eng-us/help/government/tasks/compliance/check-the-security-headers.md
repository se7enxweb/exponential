---
title: Check the security headers
audience: government, admin, hosting
category: public-sector
order: 130
summary: Step by step - check from outside that every Exponential siteaccess sends the expected security headers and cookie flags, that internal files are not served, and record the result as evidence for an audit.
keywords: security headers, curl, X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, Content-Security-Policy, Strict-Transport-Security, Set-Cookie flags, evidence, monthly check, public sector
type: help
---
# Check the security headers

For administrators and hosting teams, after a change of the web server, a deploy, or monthly as evidence. The commands
come from [Security and audit, 1](../../../../guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes)
and [13.9](../../../../install/13-security-hardening.md#139-security-headers-and-https).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What to expect](#what-to-expect)
4. [Recording the result](#recording-the-result)
5. [Related pages](#related-pages)

## Before you start

- A shell with `curl`, outside the server's network if possible (a check from inside can bypass a proxy).
- The list of host names: public siteaccesses and the administration.

## Steps

```bash
for host in www.example.gov admin.example.gov; do
  echo "== $host"
  curl -sI "https://$host/" | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security|strict-transport'
  curl -sI "https://$host/user/login" | grep -i set-cookie
done
for p in .env .git/config settings/site.ini composer.json extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" "https://www.example.gov/$p"
done
```

## What to expect

| Check | Expected |
|---|---|
| `x-content-type-options` | `nosniff` |
| `x-frame-options` | `SAMEORIGIN` |
| `content-security-policy` | `frame-ancestors 'self'`; on the administration also a policy or a report-only policy |
| `referrer-policy` | `strict-origin-when-cross-origin` |
| `permissions-policy` | `camera=(), microphone=(), payment=(), usb=()` |
| `strict-transport-security` | your value, once set |
| session cookie | `Secure; HttpOnly; SameSite=Lax` |
| internal files | `404`, or an Exponential page, never the file's content |

A missing header after a change usually means an override replaced the list or a proxy strips headers.

## Recording the result

Save the output with the date and host in your security documentation. Compare with the previous month; any
difference needs an explanation.

## Related pages

- [Security headers](../../security/security-headers.md)
- [Add HSTS](add-hsts.md)
- [Go-live security checklist](../../security/go-live-security-checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
