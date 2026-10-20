---
title: NIS2 and an Exponential site
audience: government, project-manager, admin
category: public-sector
order: 280
summary: For EU public bodies that fall under the NIS2 Directive (EU) 2022/2555 or national cybersecurity rules derived from it - a general framing of how features of Exponential support common risk-management measures (logging, access control, MFA, vulnerability handling, backups, supply chain), and what remains organizational.
keywords: NIS2, 2022/2555, cybersecurity, risk management measures, incident reporting, public administration entities, multi-factor authentication, logging, vulnerability handling, supply chain, backups, EU, public sector
type: help
---
# NIS2 and an Exponential site

This page is for information security officers and project managers of public bodies in the EU. The NIS2 Directive,
Directive (EU) 2022/2555 (<https://eur-lex.europa.eu/eli/dir/2022/2555/oj>), sets cybersecurity risk-management and
reporting obligations for entities in its scope, which member states define in their national law, including public
administration entities in many cases. Whether and how it applies to your organization is a legal question.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [How to use this page](#how-to-use-this-page)
2. [Common measures and what Exponential offers](#common-measures-and-what-exponential-offers)
3. [What stays organizational](#what-stays-organizational)
4. [Related pages](#related-pages)

## How to use this page

The directive and national laws describe measures in general terms (risk analysis, incident handling, business
continuity, supply chain security, secure development and maintenance, vulnerability handling, access control,
cryptography, multi-factor authentication and others). Your national authority's guidance turns them into concrete
requirements. The table below helps you find, for a website run with Exponential, the features that contribute
evidence. It is not a compliance mapping and the project makes no NIS2 claim.

## Common measures and what Exponential offers

| Measure area (general) | Exponential features that contribute | Page in this section |
|---|---|---|
| Access control and least privilege | roles, policies with section and subtree limitations; account review; Deactivate and move | Roles and least privilege |
| Multi-factor authentication | two-step sign-in (`sevenx_authentication_2fa`), can be required | Two-step sign-in for public staff |
| Logging and monitoring | tamper-evident audit trail on by default, alert rules, `security.log`, CSP reports, SIEM forwarding | The audit trail; Forwarding to a SIEM |
| Incident handling | audit console filters by request, user, time; verify; exports | Incident response |
| Vulnerability handling and disclosure | `SECURITY.md`, immutable releases; your own `security.txt` | Vulnerability disclosure; Adding security.txt |
| Secure configuration | secure defaults, security headers, CSP, `ManagedByDeployment` | Hardening; Configuration managed by the deployment |
| Integrity | file consistency check against release checksums | File consistency check |
| Cryptography | bcrypt password hashes, HTTPS and HSTS, `Secure` cookies | Password policy; HTTPS and HSTS |
| Business continuity | backups of database, storage and `settings/override`; restore tests | Backups and recovery |
| Supply chain | open source under GPL, Composer lock file, `composer audit`, release changelogs | Patching cadence |

## What stays organizational

- Scoping, risk analysis and management approval.
- Incident reporting to authorities within the deadlines your national law sets: Exponential provides records, not the
  reporting process.
- Contracts with hosting and implementation suppliers, and their security obligations.
- Training of staff.
- Testing of the measures (penetration tests, exercises).

## Related pages

- [Security standards](../../../standards/security.md)
- [Exponential's security posture at a glance](security-posture-at-a-glance.md)
- [Compliance and GDPR for hosts](../../hosting/security/compliance-and-gdpr.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
