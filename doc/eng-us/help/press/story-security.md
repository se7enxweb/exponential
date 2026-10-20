---
title: "Story: security"
audience: press
category: press-media
order: 59
summary: A feature story about how Exponential handles security - private reporting with a first answer within one business day, fixes in the next release, published versions that never change, and the protections of the 6.0.15 line - told as process and evidence, with sources and the claims you must not make.
keywords: story, security, SECURITY.md, vulnerability reporting, security@se7enx.com, hardening, Content Security Policy, audit trail, two-step sign-in, passwords, Zeta Components, press
type: help
---
# Story: security

This page is for journalists who cover the security of open source software. No honest source says any software "is
secure"; what the sources of Exponential do describe is a **process** and a set of **protections**. This page gives
both, with dates and sources, and tells you how to handle a vulnerability someone shows you.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [If someone shows you a vulnerability](#if-someone-shows-you-a-vulnerability)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

Exponential's security policy fits on one page. "Please report security issues privately by e-mail to
security@se7enx.com, not in the public issue tracker or the discussions. You can expect a first answer within one
business day." Fixes are made on `main` and "published with the next 6.0.x release", and "A published tag is never
changed" ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Around that process the 6.0.15 development line adds protections an
administrator can see: a Content Security Policy, a tamper-evident audit trail, two-step sign-in, refusal of
well-known passwords and an administration without inline scripts
([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)).

## What is new, and when

All in the 6.0.15 line, which is in development and not released.

| What | When | Source |
|---|---|---|
| Security defaults: headers on every page, the session cookie, uniform login answers | 16 to 30 September 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#16-to-30-september-2026) |
| The audit trail, on by default | 2 October 2026 | [The audit trail](../../features/6.0/audit-trail.md) |
| Two-step sign-in in the changelog | 6 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#6-october-2026-two-step-sign-in) |
| Administration hardening | 14 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#14-october-2026-administration-hardening) |
| SVG images sanitized on upload | 15 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#15-october-2026-svg-images-sanitized-on-upload) |
| A Content Security Policy, report-only on the administration | 16 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#16-october-2026-a-content-security-policy-report-only-on-the-administration) |
| Well-known passwords refused; forgot-password links expire | 18 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#18-october-2026-well-known-passwords-refused-everywhere-and-forgot-password-links-expire) |

## Facts you can print

| Fact | Source |
|---|---|
| Reports go privately to security@se7enx.com; first answer within one business day. | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| 6.0.x is the maintained line; 5.x, 4.x, 3.x and older are not supported. | same |
| A fix never changes a published version; it arrives as the next release. | same |
| A new installation generates a random administrator password when none or a well-known one is given. | [The default administrator password](../default-administrator-password.md) |
| A forgot-password link works for one hour (`ForgotPasswordKeyLifetime`). | [6.0.15 changelog, Upgrading](../../../../changelogs/6.0/6.0.15.md#upgrading) |
| Probes and attacks are classified into a security log, with masked addresses. | [Talking points: security](../advocates/talking-points-security.md#the-points) |
| The se7enxweb profile reports 14 security pull requests to Zeta Components in April 2026. | [The PHP stack it stands on](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |
| Administrators get a threat-by-threat hardening guide with a checklist. | [Security hardening](../security-hardening.md) |

## What not to claim

- "Exponential is secure", "unhackable", "bank-grade". Describe the process and the protections.
- "It has never had a vulnerability." No source says so.
- "Compliant with" any law or standard. Compliance depends on how a site is built and run.
- "Support answers within N hours." The only documented time is the first answer to a security report.
- Details of any vulnerability that has not been published with its fix.

## If someone shows you a vulnerability

Do not publish details of an unfixed vulnerability. Ask the person to report it privately to security@se7enx.com, as
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) asks, and report only after the fix is released. Do not test it against a site that
is not yours. The routine for advocates applies to reporters too:
[Security reports stay private](../advocates/security-reports-stay-private.md).

## Angles and headline ideas

```text
"One business day: how an old open source CMS answers security reports"
"Fixed in the next release, never in the old one: Exponential's rule for security fixes"
"Process, not promises: inside Exponential's security work"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| The security process, on the record | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| A vulnerability | privately, never in public | security@se7enx.com |

## Related pages

- [Talking points: security](../advocates/talking-points-security.md)
- [Story: the Content Security Policy](story-content-security-policy.md)
- [Story: the audit trail](story-audit-trail.md)
- [Story: two-step sign-in](story-two-step-sign-in.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
