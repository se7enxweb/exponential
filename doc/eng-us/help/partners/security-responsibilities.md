---
title: Security responsibilities of partners
audience: partner
category: partnership
order: 690
summary: What partners are responsible for in the security of the sites they build and host - secure defaults, hardening, updates, credentials, private disclosure, incidents, and a security checklist per site.
keywords: security, responsibilities, hardening, updates, credentials, passwords, disclosure, incident, checklist, CSP, TLS
type: help
---
# Security responsibilities of partners

This page is for partners who build, host or maintain Exponential sites. Under the partner contract, "7x does not
warranty the products/services delivered by partners" (<https://exponential.earth/partner/program>): the security of
your customers' sites is your responsibility.

## Shared responsibility

| Who | Responsible for |
|---|---|
| The project | Secure code in the kernel and its extensions; fixes published in releases (SECURITY.md) |
| The hosting partner | Servers, PHP, database, TLS, network, backups ([Hosting partner technical checklist](hosting-partner-technical-checklist.md)) |
| The implementation partner | Custom code, configuration, extensions chosen, updates applied |
| The customer | Accounts and passwords of their editors, content |

Write the split into each contract.

## Per-site checklist

- [ ] Hardened as in [Security hardening](../security-hardening.md) and `doc/en/us/install/13-security-hardening.md`
- [ ] Default and shared passwords changed ([Default administrator password](../default-administrator-password.md),
      [Change the admin password](../change-the-admin-password.md))
- [ ] Setup wizard and installer files not reachable
- [ ] TLS everywhere; security headers; the administration's Content Security Policy not weakened
- [ ] Debug output off in production
- [ ] Two-factor authentication offered to administrators where available ([Accounts and sign-in](../accounts-and-sign-in.md))
- [ ] Custom code reviewed for SQL injection, XSS and CSRF ([Security hardening](../security-hardening.md))
- [ ] Updates applied on a schedule ([Keeping customer sites current](keeping-customers-current.md))
- [ ] Backups tested

## Credentials

- Never in tickets, chats, e-mails or Git repositories.
- Hand credentials to customers through a secure channel; make them change passwords on first sign-in.
- Remove accounts of staff who leave, on your side and the customer's.

## Vulnerabilities

Report privately to security@se7enx.com; never in public ([Escalations and security issues](escalations-and-security-issues.md)).

## Incidents

Have a short plan for every customer:

1. Contain: take the affected part offline or block the attack path.
2. Preserve: keep logs and copies for analysis.
3. Inform: the customer at once; authorities if the law requires it.
4. Fix: apply the fix, rotate credentials, restore clean backups.
5. Learn: write down what happened and change the process.

## Related pages

- [Escalations and security issues](escalations-and-security-issues.md)
- [Protecting customer data](protecting-customer-data.md)
- [Security hardening](../security-hardening.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
