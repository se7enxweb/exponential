---
title: Security and data protection
audience: businesses, admin, project-manager
category: business-solutions
order: 1
summary: The landing page of the security section. Basics for businesses that keep customer data in Exponential - security, GDPR, where personal data lives, consent, accounts, access reviews, updates, retention and evidence from the audit trail. Not legal advice.
keywords: security, data protection, GDPR, privacy, personal data, consent, passwords, two-step sign-in, access review, security updates, retention, deletion, audit trail, compliance, not legal advice
type: help
---
# Security and data protection

This section is for business owners, managers and the person who answers for customer data in a small or mid-sized
organization. It explains in plain words what Exponential already does to protect a site and its data, what you
must decide and do yourself, and where the technical pages are for whoever does the work.

> **Warning**
> These pages are basics, not legal advice. Whether your site complies with the GDPR or any other law depends on
> what you process, why and for whom. Read the regulation text itself, and take advice from a lawyer or your data
> protection officer (DPO). The project's own pages say the same ([Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md)).

## Contents

1. [What this section covers](#what-this-section-covers)
2. [The pages](#the-pages)
3. [A suggested order](#a-suggested-order)
4. [What Exponential does by default](#what-exponential-does-by-default)
5. [Neighboring sections](#neighboring-sections)

## What this section covers

- The security basics every business site needs, and who does them.
- The GDPR in outline, and how the software helps you meet your duties.
- Where personal data sits inside an Exponential installation.
- Consent for mail and forms, accounts and passwords, and reviewing who has access.
- Security updates and how to report a vulnerability privately.
- How long data is kept, how it is deleted, and how to show evidence with the audit trail.

## The pages

| Page | What it answers |
|---|---|
| [Security basics for businesses](security-basics-for-businesses.md) | The ten things a business must have in place, and who does each |
| [GDPR basics](gdpr-basics.md) | The principles in outline and the software features that support them |
| [Personal data in Exponential](personal-data-in-exponential.md) | Where accounts, orders, form data, newsletter subscribers, logs and the audit trail are stored |
| [Consent and forms](consent-and-forms.md) | Mail consent, double opt-in, unsubscribe, forms that collect data |
| [User accounts and passwords](user-accounts-and-passwords.md) | Password rules, two-step sign-in, password changes, account hygiene |
| [Access reviews](access-reviews.md) | A yearly check of who can do what, step by step |
| [Security updates and disclosure](security-updates-and-disclosure.md) | Supported versions, applying fixes, reporting a vulnerability privately |
| [Data retention and deletion](data-retention-and-deletion.md) | How long logs and records are kept, and how to delete on request |
| [Compliance evidence from the audit trail](compliance-evidence-from-the-audit-trail.md) | Using the tamper-evident audit trail as proof |

## A suggested order

1. [Security basics for businesses](security-basics-for-businesses.md): the overview.
2. [Personal data in Exponential](personal-data-in-exponential.md): know what you hold.
3. [GDPR basics](gdpr-basics.md) and [Consent and forms](consent-and-forms.md): your duties towards people.
4. [User accounts and passwords](user-accounts-and-passwords.md) and [Access reviews](access-reviews.md): who gets in.
5. [Security updates and disclosure](security-updates-and-disclosure.md): staying safe over time.
6. [Data retention and deletion](data-retention-and-deletion.md) and
   [Compliance evidence from the audit trail](compliance-evidence-from-the-audit-trail.md): proving it.

## What Exponential does by default

| Protection | Source |
|---|---|
| Passwords stored as bcrypt hashes; common passwords refused | [Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in) |
| Security headers on every page, including the administration | [Security and audit](../../../guides/security-and-audit.md) |
| A Content Security Policy, report-only on the administration | [Content Security Policy](../../../features/6.0/content-security-policy.md) |
| A tamper-evident audit trail, on by default | [The audit trail](../../../features/6.0/audit-trail.md) |
| Client addresses masked in request logs (`MaskIP=enabled`) | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#privacy-defaults-in-the-logs) |
| Optional mail only with consent, one-click unsubscribe | [E-mail preferences](../../../features/6.0/mail-preferences.md) |
| No installer leaves the old default administrator password | [The default administrator password](../../default-administrator-password.md) |
| Two-step sign-in available through `sevenx_authentication_2fa` | [Two-step sign-in](../../../guides/two-factor-login.md) |

The defaults are a strong start. The decisions about your data, your people and your contracts remain yours.

## Neighboring sections

- [Reliability](../reliability/README.md): backups, restores and incident response.
- [Stores: security checklist](../stores/store-security-checklist.md) and
  [Card payments and PCI basics](../stores/card-payments-and-pci-basics.md).
- [Newsletters: opt-in and consent](../newsletters/opt-in-and-consent.md).
- [Costs](../costs/README.md) and [Choosing hosting and partners](../choosing/README.md).
- Governments have their own section: [Governments: start here](../../government/government-start-here.md).

## Related pages

- [Security hardening](../../security-hardening.md)
- [Security and audit guide](../../../guides/security-and-audit.md)
- [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md)
- [Security for hosts](../../hosting/security/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
