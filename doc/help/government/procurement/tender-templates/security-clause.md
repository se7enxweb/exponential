---
title: "Tender template: security clause"
audience: government, project-manager, partner, admin
category: public-sector
order: 40
summary: Clause text for the security obligations of a CMS contract - secure configuration delivered and documented, sign-in protections, logging, access for the supplier, security testing and incident notification - with blanks and the Exponential settings it touches.
keywords: security clause, tender template, hardening, secure configuration, two-factor, lockout, CSP, HSTS, audit trail, penetration test, incident notification, NIS2, contract
type: help
---
# Tender template: security clause

This page is for procurement officers and information security officers who write the security obligations of a
CMS contract. The clause turns the requirement areas of
[Security requirements](../requirements/security-requirements.md) into contract terms. Patching and vulnerability
handling have their own template: [Vulnerability disclosure and patching](vulnerability-disclosure-and-patching-clause.md).

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [The secure configuration annex](#the-secure-configuration-annex)
3. [What an Exponential-based bid can show](#what-an-exponential-based-bid-can-show)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
Y. SECURITY

Y.1 Secure configuration. Before go-live the Supplier shall configure the system
    according to the secure configuration annex <annex> and deliver a document
    listing every security-relevant setting that differs from the software's
    shipped defaults, with the reason.

Y.2 Sign-in. All accounts with access to the administration shall use a second
    authentication factor [from <date>]. Repeated failed sign-ins shall be
    limited as set out in <annex>.

Y.3 Least privilege. The Supplier shall implement the role model in <annex>.
    Supplier staff shall have personal accounts; shared accounts are not permitted.
    The Supplier shall review access with the Customer every <interval>.

Y.4 Logging. Security-relevant events shall be logged in a tamper-evident form,
    retained for <period>, and made available to the Customer on request within
    <time>. [Events shall be forwarded to <Customer's log platform>.]

Y.5 Transport. The system shall be reachable only over HTTPS with a current
    TLS configuration; HTTP Strict Transport Security shall be enabled.

Y.6 Security testing. Before go-live and every <interval>, [the Supplier shall
    commission / the Customer may commission] a security test by <party>.
    Findings rated <severity> or higher shall be corrected within <time>.

Y.7 Incidents. The Supplier shall notify the Customer of a security incident
    affecting the system or the Customer's data within <time> of becoming aware
    of it, and shall cooperate with the Customer's incident response
    [and reporting obligations under <applicable rules>].

Y.8 Secrets. Credentials and keys shall be stored only in <location>, never in
    the source code repository, and rotated at the end of the contract.

Y.9 Evidence. The Supplier shall state any external security assessment of the
    software or service and its date, or state that none exists.
```

## The secure configuration annex

List the decisions, so that Y.1 can be checked at acceptance. For Exponential the settings are documented in
[Security hardening for production](../../../../install/13-security-hardening.md) and
[Security standards](../../../../standards/security.md):

| Topic | Setting (file, block, key) | Shipped default | Your decision |
|---|---|---|---|
| Minimum password length | `site.ini [UserSettings] MinPasswordLength` | 10 | <value> |
| Lockout | `site.ini [UserSettings] MaxNumberOfFailedLogin` | 0 (off) | <value> |
| Two-step sign-in | extension `sevenx_authentication_2fa` ([Two-step sign-in](../../../../guides/two-factor-login.md)) | not active | <on for roles> |
| Content Security Policy | `site.ini [ContentSecurityPolicy] Mode` | `auto` (report-only admin, none on sites) | <mode per siteaccess> |
| HSTS | `site.ini [HTTPHeaderSettings] SecurityHeaders[Strict-Transport-Security]` | empty | <value> |
| Audit trail | `audit.ini [AuditSettings] Audit` | `enabled` | keep |
| Settings locked in production | [Configuration managed by the deployment](../../../../features/6.0/managed-by-deployment.md) | off | <decision> |
| Rate limiting in front | request-shield ([Security hardening](../../../security-hardening.md#a-shield-in-front-request-shield)) | not installed | <decision> |

## What an Exponential-based bid can show

- Y.2: two-step sign-in exists as an opt-in extension; lockout exists but is off by default.
- Y.4: the audit trail is on by default and hash-chained; `./console exp:audit verify` checks it; audit sinks can
  forward events, including a signed webhook ([Audit trail](../../../../features/6.0/audit-trail.md)).
- Y.5: Velocity manages certificates ([Velocity HTTPS certificates](../../../../features/6.0/velocity-https-certificates.md));
  HSTS is set by the operator.
- Y.9: no ASVS assessment and no external assessment are documented for the software.

## Notes for the evaluator

- A bidder who returns an empty Y.1 list has not planned the hardening; the shipped defaults leave lockout off and CSP
  unenforced.
- Y.6 is cheaper and more independent when the Customer chooses the tester.

## Related pages
- [Tender templates](README.md)
- [Security requirements](../requirements/security-requirements.md)
- [Vulnerability disclosure and patching clause](vulnerability-disclosure-and-patching-clause.md)
- [Security and audit guide](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
