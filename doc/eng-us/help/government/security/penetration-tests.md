---
title: Penetration tests of an Exponential site
audience: government, project-manager, admin
category: public-sector
order: 310
summary: For public bodies commissioning a security test of their site - scope, rules of engagement, what to give testers (versions, siteaccesses, test accounts per role, known gaps), how to prepare the installation, and how to handle findings in Exponential versus your own code.
keywords: penetration test, pentest, security test, scope, rules of engagement, test accounts, staging, findings, remediation, retest, request-shield, audit trail, disclosure, public sector, procurement
type: help
---
# Penetration tests of an Exponential site

This page is for project managers and security officers who commission a penetration test, and for administrators
who prepare the installation. A good test of an Exponential site focuses on what is yours (design, extensions,
configuration, hosting) and reports findings in the product to the project.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Scope](#scope)
2. [What to give the testers](#what-to-give-the-testers)
3. [Preparing the installation](#preparing-the-installation)
4. [During the test](#during-the-test)
5. [Handling findings](#handling-findings)
6. [Related pages](#related-pages)

## Scope

- **Environment**: a staging copy with the same version, extensions, designs and web server configuration is the
  safest target; testing production needs written approval and a time window.
- **Targets**: the public siteaccesses, the administration siteaccess, the REST API if enabled
  ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)), forms, file uploads, sign-in and two-step sign-in.
- **Out of scope**: third-party services your extensions call (analytics, CAPTCHA, maps, AI providers) unless their
  owners agree.

## What to give the testers

| Item | Source |
|---|---|
| Versions | `composer show se7enxweb/exponential`, Setup > System information |
| Activated extensions | Setup > Extensions |
| Siteaccesses and host names | your configuration |
| Test accounts, one per role (anonymous, member, editor, publisher, administrator) | created for the test, disabled afterwards |
| Known gaps and accepted risks | [Security standards: Known gaps](../../../standards/security.md#known-gaps) and your risk register |
| The security settings in force | password policy, lockout, CSP mode, HSTS |

Giving testers the known gaps (for example "lockout off by design, decision recorded") saves time and focuses the
report on what is new.

## Preparing the installation

1. Run the file consistency check so the test starts from a known state.
2. Decide what to do with request-shield: leave it in front to test the real setup, or put it in monitor mode so the
   testers reach the application; record the choice.
3. Tell the alert recipients: audit alerts (brute force, role granted) will fire.
4. Back up the staging database and `var/storage`.
5. If lockout is on, add the testers' addresses to `TrustedIPList[]` only if you want to exclude lockout from the test.

## During the test

- The audit trail records sign-ins, refusals and changes; `security.log` records probes. Both help you reconcile the
  testers' activity with what your monitoring saw: a useful detection exercise in itself.
- Every admin response carries `X-Exp-Request-Id`; testers can quote it so you find the exact records
  (`/audit/console/(request)/<id>`).

## Handling findings

| Finding in | Do |
|---|---|
| Your design, templates or extensions | fix, retest |
| Configuration or hosting | fix per the hardening pages, retest |
| Exponential itself or a shipped extension | report privately to security@se7enx.com (see "Vulnerability disclosure"); do not publish before a fix is released |

After the test: disable the test accounts, remove test content, put request-shield back to enforce, and keep the report
with your security documentation.

## Related pages

- [Security hardening](../../security-hardening.md)
- [Vulnerability disclosure](vulnerability-disclosure.md)
- [The audit trail for public bodies](audit-trail-for-public-bodies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
