---
title: Security after adoption
audience: adopter, customer, admin, hosting
category: adoption
order: 63
summary: What keeps an adopted Exponential site secure over time - updates, hardening, accounts and two-step sign-in, backups, the audit trail and logs, private reporting of vulnerabilities, and a yearly security review.
keywords: security, hardening, updates, accounts, two-factor, backups, audit trail, logs, vulnerability, security@se7enx.com, review
type: help
---
# Security after adoption

## Contents

1. [The basics](#the-basics)
2. [Evidence when something happens](#evidence-when-something-happens)
3. [Reporting a vulnerability](#reporting-a-vulnerability)
4. [A yearly review](#a-yearly-review)

## The basics

| Practice | Page |
|---|---|
| Apply updates, security releases first | [Keeping current](keeping-current.md) |
| Harden the installation | [Security hardening](../../security-hardening.md), [chapter 13 of the installation book](../../../install/13-security-hardening.md) |
| Few administrators, strong passwords, two-step sign-in | [Admin accounts and access](../../customers/healthy/admin-accounts-and-access.md) |
| Backups that restore | [Backups](../../customers/healthy/backups.md), [Restore test](../../customers/healthy/restore-test.md) |
| A Content Security Policy | [Content Security Policy](../../../features/6.0/content-security-policy.md) |

A short list for owners: [Security checklist for owners](../../customers/healthy/security-checklist-for-owners.md).

## Evidence when something happens

Exponential keeps a tamper-evident audit trail and logs, with viewers in the administration
([Audit trail](../../../features/6.0/audit-trail.md), [Log viewer](../../../features/6.0/log-viewer.md),
[Logs for owners](../../customers/healthy/logs-for-owners.md)). Keep notes during an incident
([Incident notes template](../../customers/healthy/incident-notes-template.md)).

## Reporting a vulnerability

If you find a vulnerability in Exponential itself, report it privately to security@se7enx.com, not in a public place.
"You can expect a first answer within one business day" (`SECURITY.md`;
[How security reports are handled](../../community/how-it-works/how-security-reports-are-handled.md)).

## A yearly review

```text
[ ] All software on supported versions
[ ] Administrator accounts reviewed; leavers removed
[ ] Restore test done this year
[ ] Hardening checked against the current installation book
[ ] Incident notes reviewed; lessons applied
```

## Related pages

- [After adoption](README.md)
- [Security basics (customers)](../../customers/healthy/security-basics.md)
- [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md)
- [Private security reports](../../customers/healthy/private-security-reports.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
