---
title: Security requirements
audience: government, project-manager, admin, partner
category: public-sector
order: 50
summary: How to write the security requirements of a CMS tender - authentication, access control, input handling, headers and CSP, logging, updates and vulnerability handling - with verifiable wording and Exponential's documented status against OWASP Top 10, ASVS and CSP.
keywords: security requirements, OWASP Top 10, ASVS, CSP, two-factor, 2FA, MFA, password, lockout, audit, logging, patching, vulnerability disclosure, NIS2, NIST CSF, tender
type: help
---
# Security requirements

This page is for procurement teams, information security officers and project managers who write the security part
of a CMS tender. It gives requirement wording per area and states what Exponential documents in
[Security standards](../../../../standards/security.md), including the gaps. Use it with your own risk assessment;
frameworks such as the [NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) or, in the EU, the
[NIS2 Directive](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) may set the context for your organization.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Product and service](#product-and-service)
2. [Requirement wording by area](#requirement-wording-by-area)
3. [Exponential's documented status](#exponentials-documented-status)
4. [Settings an evaluator can check](#settings-an-evaluator-can-check)
5. [Pitfalls](#pitfalls)

## Product and service

Separate what the **software** must do from what the **supplier or host** must do. "The software supports two-factor
sign-in" is a product requirement; "the supplier enables it for every administrator and keeps it on" is a service
requirement. Both are needed.

## Requirement wording by area

| Id | Area | Requirement |
|---|---|---|
| SEC-01 | Authentication | Administrators and editors can be required to use a second factor at sign-in. Name the mechanism. |
| SEC-02 | Authentication | Passwords are stored with a current adaptive hash function; state the minimum length and whether it can be raised. |
| SEC-03 | Authentication | Repeated failed sign-ins are detected and can be throttled or locked; state the defaults. |
| SEC-04 | Access control | Every administrative function is protected by a permission model with least privilege; describe it. |
| SEC-05 | Input and output | Forms are protected against cross-site request forgery; output is escaped against cross-site scripting; uploads are checked by content. |
| SEC-06 | Headers | The system sends security headers (for example `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`) and supports a Content Security Policy ([CSP Level 3](https://www.w3.org/TR/CSP3/)). State what is on by default. |
| SEC-07 | Logging | Security-relevant events (sign-ins, permission changes, content removal, settings changes) are logged in a tamper-evident way and can be exported. |
| SEC-08 | Updates | The supplier states the supported versions, how security fixes are published and how fast they are applied under this contract. |
| SEC-09 | Disclosure | There is a published, private channel for reporting vulnerabilities. |
| SEC-10 | Assurance | State any external assessment (penetration test, [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/) level) and its date, or state that none exists. |

## Exponential's documented status

From [Security standards](../../../../standards/security.md):

| Requirement | What is documented | Status or gap |
|---|---|---|
| SEC-01 | Two-step sign-in with the `sevenx_authentication_2fa` extension ([Two-step sign-in](../../../../guides/two-factor-login.md)) | Opt-in: an extension and a login handler to configure |
| SEC-02 | `password_hash()` (bcrypt today), rehash at sign-in; minimum length 10 (`MinPasswordLength`) | Below ASVS 4.0.3 V2.1.1 (12) unless raised |
| SEC-03 | Brute force detected by audit alert rules; lockout with `MaxNumberOfFailedLogin` | **Lockout off by default** (`MaxNumberOfFailedLogin=0`); rate limiting left to the web server or request-shield ([Security hardening](../../../security-hardening.md#a-shield-in-front-request-shield)) |
| SEC-04 | Roles and policies checked for every module view ([Roles and policies](../../../../guides/roles-and-policies.md)) | OWASP A01 **Partly complies** |
| SEC-05 | Form tokens (`ezformtoken`), `wash` escaping, upload checks, SVG sanitizer | Escaping is not automatic in templates; no prepared statements in the database layer |
| SEC-06 | Security headers on every page; CSP with nonces and reporting ([Content Security Policy](../../../../features/6.0/content-security-policy.md)) | CSP **report-only on the administration, off on public sites** by default; HSTS empty until the operator sets it |
| SEC-07 | Audit trail on by default, hash-chained, signed manifests ([Audit trail](../../../../features/6.0/audit-trail.md)) | OWASP A09 **Complies** |
| SEC-08 | `SECURITY.md`: 6.0 is the maintained line; fixes on `main`, released as the next 6.0.x; published tags never change | No dependency vulnerability scan in CI |
| SEC-09 | Private reporting by e-mail, first answer within one business day ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) | No `security.txt` ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)) shipped; an operator can add one |
| SEC-10 | Three documented hardening passes | **No ASVS assessment made** (status Unverified); no external assessment documented |

Overall: OWASP Top 10 (2021) **Partly complies**; ASVS 4.0.3 **Unverified**; CSP Level 3 **Partly complies**.

## Settings an evaluator can check

In a test installation ([Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)):

| Setting | Where | Shipped value |
|---|---|---|
| `[UserSettings] MinPasswordLength` | `settings/site.ini` | 10 |
| `[UserSettings] MaxNumberOfFailedLogin` | `settings/site.ini` | 0 (off) |
| `[ContentSecurityPolicy] Mode` | `settings/site.ini` | `auto` (report-only on admin, none on sites) |
| `[AuditSettings] Audit` | `settings/audit.ini` | `enabled` |
| `[HTTPHeaderSettings] SecurityHeaders[]` | `settings/site.ini` | headers listed in Security standards |

Ask the bidder which of these they will change for your site, and to deliver the changed values as part of the
documentation. Chapter 13 of the installation book lists the production steps
([Security hardening for production](../../../../install/13-security-hardening.md)).

## Pitfalls

- Treating defaults as the delivered state. The hardening steps are work the supplier does; put them in the contract.
- Asking for "ISO 27001 certification of the software". ISO/IEC 27001 certifies an organization's management system,
  not a product; ask the hosting supplier about it if relevant ([ISO/IEC 27001](https://www.iso.org/standard/27001)).
- Promises on time-to-fix that depend on the upstream project. Ask for time-to-apply a released fix instead
  ([SLA options](../../../hosting/business/sla-options.md)).

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Tender template: security clause](../tender-templates/security-clause.md)
- [Tender template: vulnerability disclosure and patching](../tender-templates/vulnerability-disclosure-and-patching-clause.md)
- [Security and audit guide](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
