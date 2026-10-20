---
title: "FAQ: security"
audience: government, project-manager, admin, hosting
category: public-sector
order: 30
summary: Answers to the security questions public bodies ask about Exponential - OWASP Top 10, ASVS, penetration tests, two-step sign-in, account lockout, audit trail, Content Security Policy, vulnerability reporting and fixes - backed by the security standards page.
keywords: security FAQ, OWASP Top 10, ASVS, penetration test, two-factor, 2FA, lockout, audit trail, CSP, vulnerability, SECURITY.md, patches, hardening, public sector
type: help
---
# FAQ: security

This page answers the security questions that security officers and evaluation panels in public bodies ask about
Exponential. The answers come from [Security standards](../../../standards/security.md), `SECURITY.md` and the
hardening guides.

## Contents

1. [How does Exponential address the OWASP Top 10?](#how-does-exponential-address-the-owasp-top-10)
2. [Has it been assessed against OWASP ASVS or penetration tested?](#has-it-been-assessed-against-owasp-asvs-or-penetration-tested)
3. [Does it support two-step sign-in?](#does-it-support-two-step-sign-in)
4. [Are accounts locked after failed sign-ins?](#are-accounts-locked-after-failed-sign-ins)
5. [Can we see who did what?](#can-we-see-who-did-what)
6. [Does it send a Content Security Policy?](#does-it-send-a-content-security-policy)
7. [How are vulnerabilities reported and fixed?](#how-are-vulnerabilities-reported-and-fixed)
8. [What must we harden ourselves?](#what-must-we-harden-ourselves)

## How does Exponential address the OWASP Top 10?

Every category of the OWASP Top 10 (2021) has built-in mitigations, mapped one by one on
[Security standards](../../../standards/security.md#owasp-top-10-2021): role-based access checks, password hashing with
`password_hash()`, output escaping, CSRF tokens on forms, security headers, a tamper-evident audit trail, SSRF checks
and more. The status is **Partly complies**: "Complies" would need an external assessment, which has not been made.

## Has it been assessed against OWASP ASVS or penetration tested?

No ASVS assessment has been made; status **Unverified**. The default minimum password length (10) is below ASVS level
1's 12, so level 1 needs a settings change ([Security standards](../../../standards/security.md#owasp-asvs-403)). No
penetration test report is published in the repository. The kernel has had three documented hardening passes. If your
rules require a test, commission one for your site.

## Does it support two-step sign-in?

Yes, opt-in: the extension `sevenx_authentication_2fa` adds a code from an authenticator app or by e-mail after the
password ([Two-step sign-in](../../../guides/two-factor-login.md)).

## Are accounts locked after failed sign-ins?

Not by default: `MaxNumberOfFailedLogin=0`. You can set a limit, with a trusted address list. Brute force is detected
by the audit alert rules, and rate limiting is left to the web server or a request filter such as request-shield
([Security standards](../../../standards/security.md#known-gaps), [Security hardening](../../security-hardening.md#a-shield-in-front-request-shield)).

## Can we see who did what?

Yes. The audit trail is on by default and hash-chained, records sign-ins, content changes, settings writes and more,
and has alert rules ([Audit trail](../../../features/6.0/audit-trail.md)). It can be verified with
`./console exp:audit verify` ([Security and audit](../../../guides/security-and-audit.md)).

## Does it send a Content Security Policy?

Yes, built from `site.ini [ContentSecurityPolicy]` with per-request nonces and a report collector. As shipped it is
**report-only on the administration and off on public sites**; enforcement is the operator's decision per siteaccess
([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## How are vulnerabilities reported and fixed?

Privately by e-mail to the address in `SECURITY.md`, with "a first answer within one business day". Accepted issues
are fixed on `main` and published with the next 6.0.x release; published versions are never changed. No time to fix is
promised. No `security.txt` is shipped ([Security standards](../../../standards/security.md#vulnerability-disclosure)).
How to plan for this: [Security updates and patching cadence](../procurement/lifecycle/security-updates-and-patching-cadence.md).

## What must we harden ourselves?

HTTPS and HSTS, the lockout decision, password length, two-step sign-in, CSP enforcement, restricting the
administration, a request filter, dependency checks with `composer audit` (not run in the project's CI), and server
hardening. The guides: [Security hardening](../../security-hardening.md),
[13. Security hardening for production](../../../install/13-security-hardening.md),
[Security checklist](../checklists/security-checklist.md).

## Related pages

- [Government FAQ](README.md)
- [Security standards](../../../standards/security.md)
- [Security checklist](../checklists/security-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
