---
title: ISO/IEC 27001 and an Exponential site
audience: government, project-manager, admin
category: public-sector
order: 300
summary: For public bodies with an information security management system based on ISO/IEC 27001 - how a website run with Exponential enters the ISMS scope, which product features provide evidence for typical controls, and what stays with the organization and its suppliers.
keywords: ISO/IEC 27001, ISMS, information security management system, controls, statement of applicability, evidence, access control, logging, change management, supplier relationships, certification, public sector
type: help
---
# ISO/IEC 27001 and an Exponential site

This page is for information security managers of public bodies that run an ISMS based on ISO/IEC 27001
(<https://www.iso.org/standard/27001>). A public website is usually in the ISMS scope as an information system. This
page helps you find evidence for typical control themes in an Exponential installation. It does not quote the standard,
and it is not a statement of applicability; the project holds no certification.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Scope](#scope)
2. [Evidence by theme](#evidence-by-theme)
3. [Suppliers](#suppliers)
4. [Related pages](#related-pages)

## Scope

Describe the website as an asset: the Exponential version and extensions, the hosting (own data center or supplier),
the administration and public siteaccesses, the data it holds (content, user accounts, form submissions, consent
records, logs). The privacy section of this help lists where personal data lives.

## Evidence by theme

| Theme | Evidence in Exponential | How to produce it |
|---|---|---|
| Access control | roles, assignments, account list | role pages; Users > Check user accounts, CSV export |
| Authentication | password and two-step settings | `./console exp:ini get` output; enrolment status |
| Logging and monitoring | audit trail, alert recipients, verification | `./console exp:audit status`, `verify`, `alerts recipients` |
| Change management | `ManagedByDeployment`, setting writes recorded (`system.setting.write`) | settings; audit console filtered on `system.setting.*` |
| Configuration management | security headers, CSP mode, debug off | `curl -sI` output; settings |
| Technical vulnerability management | update records, `composer audit` | patching records |
| Integrity of software | file consistency check | Upgrade check report, `verifyfiles.sh` output |
| Backup | backup and restore records | operations records |
| Secure development | the project's tests and hardening notes; your own code reviews | [Security standards: Tests and CI](../../../standards/security.md#tests-and-ci) |
| Information deletion | retention of logs, audit, consent log | retention settings (see the privacy section) |

## Suppliers

Name the suppliers in your supplier register: the hosting company, the implementation partner, and any third-party
services activated through extensions (analytics, CAPTCHAs, maps, AI providers). The Exponential project itself is an
open source project, not a supplier under contract unless you buy support from 7x or a partner
([Who is 7x](../../who-is-7x.md)).

## Related pages

- [Security standards](../../../standards/security.md)
- [Exponential's security posture at a glance](security-posture-at-a-glance.md)
- [Go-live security checklist for public sites](go-live-security-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
