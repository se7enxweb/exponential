---
title: Security engineering overview
audience: software-engineer, developer, admin
category: software-engineering
order: 700
summary: The security model of Exponential for engineers - access control through policies, output escaping, safe SQL, CSRF tokens, the secure defaults of 2026, the HTTP security layer, authentication and two-factor sign-in, the audit trail and request classification, the documented hardening passes, the OWASP status the project gives itself, and how vulnerabilities are reported and fixed.
keywords: security, security engineering, OWASP, ASVS, access control, escaping, wash, SQL injection, CSRF, ezformtoken, secure defaults, hardening, SECURITY.md, vulnerability disclosure, audit, software engineer
type: help
---
# Security engineering overview

This page is for engineers who assess, design or review the security of an Exponential system. It names the
mechanisms, where they live, what is on by default, and where the project itself says it falls short. The standards
status is [Security standards](../../standards/security.md); the attacker-oriented operator's guide is
[Security hardening](../security-hardening.md).

## Contents

1. [The mechanisms](#the-mechanisms)
2. [Secure defaults](#secure-defaults)
3. [Hardening passes on record](#hardening-passes-on-record)
4. [The project's own assessment](#the-projects-own-assessment)
5. [Reporting and fixing vulnerabilities](#reporting-and-fixing-vulnerabilities)
6. [Where to go next](#where-to-go-next)

## The mechanisms

| Threat | Mechanism | Where |
|---|---|---|
| Broken access control | roles, policies and limitations checked for every module view; content reads filtered by permission | [Modules, views and access control](modules-views-and-access-control.md) |
| Injection (SQL) | `escapeString()` and integer casts; persistent objects escape for you | [Writing portable SQL](writing-portable-sql.md) |
| Injection (shell) | arguments through `escapeshellarg()` | [Security hardening of 6.0.13](../../bc/6.0/hardening.md) |
| Cross-site scripting | `\|wash` in templates; Content Security Policy with nonces | [HTTP security](http-security-csp-and-proxies.md) |
| Cross-site request forgery | form tokens on every form (`ezformtoken`) | [ezformtoken and CSRF](../extension-development/part-8-reference/ezformtoken-and-csrf.md) |
| Weak authentication | `password_hash()`, minimum length 10, optional lockout, two-factor sign-in | [Authentication, sessions and two-factor sign-in](authentication-sessions-and-2fa.md) |
| Spoofed proxy headers | forwarded headers trusted only from `TrustedProxies[]` | [Trusted proxies](../../bc/6.0/trusted-proxies.md) |
| Open redirects | allowed redirect hosts | [Safe redirects](../../features/6.0/safe-redirects.md) |
| Malicious uploads | upload checks, SVG sanitizing | [SVG images](../../bc/6.0/svg-images.md) |
| Configuration drift in production | `ManagedByDeployment` | [Configuration managed by the deployment](../../features/6.0/managed-by-deployment.md) |
| Undetected abuse | tamper-evident audit trail, request classifier and `security.log` | [Observability](observability-logs-debug-and-audit.md) |

## Secure defaults

In September and October 2026 security headers, `HttpOnly` and `SameSite` session cookies, a stricter upload check and
new access requirements for some remote services were turned on by default. Security fixes are treated like bug fixes:
on by default, documented with how to undo them deliberately ([Security defaults](../../specifications/6.0/security-defaults-2026-09.md),
[Behaviour changes of 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)).

Shipped values worth knowing (`settings/site.ini`): security headers such as `X-Content-Type-Options: nosniff`,
`X-Frame-Options: SAMEORIGIN` and `Referrer-Policy: strict-origin-when-cross-origin`; `MinPasswordLength=10`;
`HashType=php_default`; `MaxNumberOfFailedLogin=0` (the lockout is off until you set it); the Content Security Policy
in `Mode=auto` (report-only on the administration, off on sites).

## Hardening passes on record

| Pass | Content |
|---|---|
| 6.0.13 | four SQL injections, two OS command injections, one reflected cross-site scripting ([Security hardening of 6.0.13](../../bc/6.0/hardening.md)) |
| August 2026 | [Security hardening, August 2026](../../specifications/6.0/security-hardening-2026-08.md) |
| September 2026 | [Security defaults](../../specifications/6.0/security-defaults-2026-09.md) |

The 6.0.13 page also lists the patterns extension developers must audit in their own code: a kernel update does not fix
them in extensions.

## The project's own assessment

The security standard rates OWASP Top 10 (2021) as **Partly complies**, with mitigations for all ten categories and
named gaps (account lockout off by default, no dependency scanning in CI, public-site CSP off), OWASP ASVS 4.0.3 as
**Unverified** (no assessment made), and Content Security Policy Level 3 as **Partly complies**. "Complies" would need
an external assessment, which has not been made. Use this list as the starting point of your own threat model.

## Reporting and fixing vulnerabilities

- Report privately by e-mail to the address in `SECURITY.md`, not in the public issue tracker or discussions. A first
  answer is promised within one business day.
- Include the version, the PHP version, the engine, the database, what an attacker can do and the steps to reproduce;
  leave out passwords, secrets and personal data.
- Fixes are made on `main` and released with the next 6.0.x version. A published tag is never changed: a fix for a
  problem in an older tag reaches you as the next release. Only the 6.0 line is supported.

## Where to go next

- [HTTP security: headers, CSP, request rules and proxies](http-security-csp-and-proxies.md)
- [Authentication, sessions and two-factor sign-in](authentication-sessions-and-2fa.md)
- [Security review checklist](security-review-checklist.md)
- [Chapter 13 of the installation book: Security hardening](../../install/13-security-hardening.md)

## Related pages

- [Security for applications](../creators/app-creators/security-for-applications.md)
- [Security in extensions](../extension-development/part-3-extension-developer/security-in-extensions.md)
- [Security and audit](../../guides/security-and-audit.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
