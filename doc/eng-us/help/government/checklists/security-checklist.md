---
title: Security checklist
audience: government, admin, hosting, project-manager
category: public-sector
order: 40
summary: A security checklist for a public body running Exponential - accounts and sign-in, hardening, headers and CSP, logging and audit, updates, the server and reporting - built on the security standards page and the hardening guides, with the defaults you must change.
keywords: security, checklist, hardening, two-step sign-in, lockout, password length, CSP, HSTS, audit trail, request-shield, updates, SECURITY.md, security.txt, public sector
type: help
---
# Security checklist

This checklist is for the security officer and site administrator of a public body. It follows
[Security standards](../../../standards/security.md), [Security hardening](../../security-hardening.md) and
[13. Security hardening for production](../../../install/13-security-hardening.md). Items marked "default" name a
setting that ships off or open and that you decide on.

## Contents

1. [Accounts and sign-in](#accounts-and-sign-in)
2. [Configuration](#configuration)
3. [Headers and Content Security Policy](#headers-and-content-security-policy)
4. [Logging and audit](#logging-and-audit)
5. [Updates](#updates)
6. [The server and in front of it](#the-server-and-in-front-of-it)
7. [Reporting](#reporting)

## Accounts and sign-in

- [ ] No shared accounts; every administrator is a named person.
- [ ] Two-step sign-in for administrators: opt-in through `sevenx_authentication_2fa` ([Two-step sign-in](../../../guides/two-factor-login.md)).
- [ ] Default: account lockout is off (`[UserSettings] MaxNumberOfFailedLogin=0`); decide to set it, or rate-limit in front of the site ([Security standards](../../../standards/security.md#known-gaps)).
- [ ] Default: minimum password length is 10; OWASP ASVS level 1 asks 12 (`MinPasswordLength`) ([Security standards](../../../standards/security.md#status-1)).
- [ ] The initial administrator password changed ([Change the admin password](../../change-the-admin-password.md)).
- [ ] Roles follow least privilege ([Roles and policies](../../../guides/roles-and-policies.md)); the [Quarterly access review](quarterly-access-review.md) is scheduled.

## Configuration

- [ ] Debug output limited to listed addresses ([13. Security hardening](../../../install/13-security-hardening.md)).
- [ ] Settings, templates and packages locked in production with `ManagedByDeployment` where your process allows ([Configuration managed by the deployment](../../../features/6.0/managed-by-deployment.md)).
- [ ] **Setup > Upgrade check** shows no unexpected changed files ([The upgrade check](../../../guides/upgrade-check.md)).
- [ ] Third-party extensions active only when needed and named in the privacy notice ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
- [ ] Allowed redirect hosts set ([Safe redirects](../../../features/6.0/safe-redirects.md)).

## Headers and Content Security Policy

- [ ] Security headers on (default on): `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` ([Security standards](../../../standards/security.md#owasp-cheat-sheets-reflected-in-the-code)).
- [ ] Default: HSTS empty; set it when the whole site is HTTPS.
- [ ] Default: CSP report-only on the administration and off on sites; review `var/log/csp.log` and decide on enforcement per siteaccess ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## Logging and audit

- [ ] Audit trail on (default on) and chains intact: `./console exp:audit status` and `./console exp:audit verify` ([Security and audit](../../../guides/security-and-audit.md)).
- [ ] Alert recipients set: `./console exp:audit alerts recipients`.
- [ ] Audit signing keys in `settings/override/audit.ini.append.php` included in backups ([Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).
- [ ] `var/log/security.log` reviewed ([Request logs](../../../features/6.0/request-logs.md)).

## Updates

- [ ] A named person watches releases; fixes come only as the next release (`SECURITY.md`; [Security updates and patching cadence](../procurement/lifecycle/security-updates-and-patching-cadence.md)).
- [ ] Composer dependencies checked with `composer audit`, which the project does not run in CI ([Security standards](../../../standards/security.md#known-gaps)).
- [ ] PHP and the operating system receive security updates ([PHP and platform support](../procurement/lifecycle/php-and-platform-support.md)).

## The server and in front of it

- [ ] HTTPS with a valid certificate ([TLS and HTTPS](../../hosting/security/tls-and-https.md)).
- [ ] The administration restricted by network where possible ([Restricting the administration](../../hosting/security/restricting-the-administration.md)).
- [ ] A request filter such as request-shield in front ([Security hardening](../../security-hardening.md#a-shield-in-front-request-shield)).
- [ ] File permissions as documented ([Writable directories and permissions](../../hosting/basics/writable-directories-and-permissions.md)).

## Reporting

- [ ] Your site tells visitors where to report a security problem; Exponential ships no `security.txt` ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)).
- [ ] Software issues go privately to the project as `SECURITY.md` says, without personal data.
- [ ] Incident procedure ready ([Incident readiness](incident-readiness.md)).

## Related pages

- [Government checklists](README.md)
- [Security standards](../../../standards/security.md)
- [Security review (for hosts)](../../hosting/checklists/security-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
