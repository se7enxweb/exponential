---
title: Security basics
audience: customer, businesses, admin
category: customer-success
order: 8
summary: The owner's view of security on an Exponential site - what the software already does by default, the few things only your organization can decide, who should do what, and where the detailed guides are.
keywords: security, owner, default settings, updates, accounts, two-step sign-in, audit trail, backups, security headers, responsibility
type: help
---
# Security basics

This page is for the person who answers for the safety of the site without being its technician. It sets out what
Exponential already does, what remains your decision, and who on your team or at your provider should do each part.
The technical depth is in [Security and audit](../../../guides/security-and-audit.md),
[Security hardening](../../security-hardening.md) and the installation book's
[chapter 13](../../../install/13-security-hardening.md).

## What is already on

"Since September 2026 a default installation is safe without extra work. Verify it instead of trusting it"
([Security and audit](../../../guides/security-and-audit.md)). Among the defaults:

| Protection | What it does for you |
|---|---|
| Security headers on every page | Help browsers refuse framing and content sniffing |
| Session cookies with `HttpOnly` and `SameSite` | Make stolen sessions harder |
| A real logout | The old session id no longer signs anyone in |
| Upload checks by content | An image is accepted by what it contains, not by its name |
| A random first administrator password | When the installer was given none, or a well-known one |
| The audit trail, on by default | A tamper-evident record of sign-ins, content changes, settings and deploys ([The audit trail](../../../features/6.0/audit-trail.md)) |

Ask your provider to run the checks of [Security and audit](../../../guides/security-and-audit.md) once and send you
the result.

## What only you can decide

| Decision | Why it is yours | Page |
|---|---|---|
| Who gets which access | You know who needs what | [Administrator accounts and access](admin-accounts-and-access.md) |
| Whether two-step sign-in is required | It costs your staff a few seconds per sign-in | [Two-step sign-in](../../../guides/two-factor-login.md) |
| How quickly security releases are applied | It is a contract term with your provider | [Release line and support](release-line-and-support.md) |
| How much data loss is acceptable | It sets the backup schedule | [Backups](backups.md) |
| Who receives security alerts | Somebody must read them | [Monitoring](monitoring.md) |
| What personal data you collect, and for how long | It is a legal and business question | [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md) |

## The three habits that matter most

1. **Stay current.** Security fixes reach you only as the next release (`SECURITY.md`). An out-of-date site is
   the most common way to lose the protection the project already built
   ([Security hardening](../../security-hardening.md)).
2. **Keep access tight.** One account per person, few administrators, two-step sign-in for them, and accounts closed
   when people leave ([Change the administrator password, account hygiene](../../change-the-admin-password.md)).
3. **Know who to call.** Agree a contact for a suspected incident, and know that a security problem in the software
   is reported privately, never in public ([Private security reports](private-security-reports.md)).

## Who does what

| Task | Usually |
|---|---|
| Server, PHP, TLS certificates, firewall | Host |
| Applying Exponential updates | Host or partner, as agreed |
| Settings of the site (headers, sessions, admin address) | Partner or your developer |
| Roles, accounts, two-step sign-in for staff | Your site administrator |
| Reading audit alerts and logs | Host or operator, with a summary to you |
| Deciding after an incident | Owner |

Write your own version of this table into your agreements; see [Who does what](../partners-and-hosts/who-does-what.md).

## If you suspect a break-in

Treat it as the most urgent kind of incident: the hosting support guide says "A suspected security incident is always
severity 1" ([Support tiers](../../hosting/business/support-tiers.md)). Call your host at once, do not delete
anything, and let them follow the [Compromised site](../../hosting/runbooks/compromised-site.md) runbook, which
starts by containing the damage without destroying evidence. Note what you saw and when, in the
[Incident notes template](incident-notes-template.md).

> **Warning**
> Never send passwords, keys or customers' personal data in a ticket, chat or e-mail to report a problem
> ([When something goes wrong](../../when-something-goes-wrong.md)).

## Related pages

- [Security checklist for owners](security-checklist-for-owners.md)
- [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md)
- [GDPR basics](../../businesses/security/gdpr-basics.md) (not legal advice)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
