---
title: NIST Cybersecurity Framework and an Exponential site
audience: government, project-manager, admin
category: public-sector
order: 290
summary: For public bodies that organize security with the NIST Cybersecurity Framework 2.0 - a general framing of where Exponential's features and the operator's practices fit under the six functions Govern, Identify, Protect, Detect, Respond and Recover.
keywords: NIST CSF, Cybersecurity Framework 2.0, govern, identify, protect, detect, respond, recover, profile, risk management, United States, public sector, controls, website security
type: help
---
# NIST Cybersecurity Framework and an Exponential site

This page is for security and project staff of public bodies that use the NIST Cybersecurity Framework (CSF) 2.0
(<https://www.nist.gov/cyberframework>) to organize their work, in the United States and elsewhere. The framework
groups outcomes under six functions. Below, for a website run with Exponential, the features and practices that
contribute to each function. It is a framing aid, not a profile or an assessment; the project makes no CSF claim.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Govern](#govern)
2. [Identify](#identify)
3. [Protect](#protect)
4. [Detect](#detect)
5. [Respond](#respond)
6. [Recover](#recover)
7. [Related pages](#related-pages)

## Govern

- Roles and responsibilities for the site (owner, administrators, suppliers); see "Roles and least privilege".
- Policies for passwords, MFA, patching and disclosure; see "Password policy", "Patching cadence",
  "Vulnerability disclosure".
- Supplier management: Exponential is open source (GPL v2 or later); hosting and implementation partners are
  contracted.

## Identify

- Inventory: Exponential version (`composer show se7enxweb/exponential`), activated extensions (Setup > Extensions),
  Composer lock file, PHP and database versions (Setup > System information).
- Known gaps from the project's statement ([Security standards](../../../standards/security.md#known-gaps)) go into the
  risk register.

## Protect

| Outcome area | Exponential |
|---|---|
| Identity and access | roles and policies, two-step sign-in, password policy, lockout, account review |
| Data security | HTTPS and HSTS, `Secure`/`HttpOnly` cookies, secrets in `settings/override` |
| Platform security | security headers, CSP, upload controls, `ManagedByDeployment`, file permissions, updates |

## Detect

- Audit trail with alert rules (brute force, administrator role granted, mass delete, audit disabled, broken chain).
- `security.log` from the request classifier; CSP reports in `csp.log`.
- File consistency check against release checksums.
- Forwarding to your SIEM.

## Respond

- Audit console to reconstruct what happened, by user, request or time; exports in signed bundles.
- Disabling accounts, ending sessions, maintenance mode.
- The hosting runbook for a compromised site ([Runbook: compromised site](../../hosting/runbooks/compromised-site.md)).

## Recover

- Backups of the database, `var/storage` and `settings/override`, restore tested
  ([10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore)).
- Content versions and the trash to restore content; immutable releases to rebuild code from a known state.

## Related pages

- [Exponential's security posture at a glance](security-posture-at-a-glance.md)
- [Incident response](incident-response.md)
- [Security hardening](../../security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
