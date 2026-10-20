---
title: Data minimization
audience: government, admin, developer, project-manager
category: public-sector
order: 50
summary: For public bodies applying data minimization and privacy by default - what Exponential minimizes already (audit, request logs, log viewer, mail), and what you minimize yourself in content classes, forms, logs, search statistics and extensions.
keywords: data minimization, privacy by default, privacy by design, GDPR article 5, article 25, IP masking, pseudonymization, hashing, forms, content classes, search statistics, request logs, audit privacy, extensions, public sector
type: help
---
# Data minimization

This page is for data protection officers, administrators and developers. Collect only what you need, keep only what
you must, and mask what you show. Exponential minimizes several of its own records by default; the rest is a matter
of configuration and design ([Privacy standards: GDPR](../../../standards/privacy.md#gdpr)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Minimized by default](#minimized-by-default)
2. [What you minimize](#what-you-minimize)
3. [A review checklist](#a-review-checklist)
4. [Related pages](#related-pages)

## Minimized by default

| Record | Default minimization |
|---|---|
| Audit trail | addresses truncated to /24 (IPv6 /48); session ids hashed; user agents reduced to browser and OS family; e-mail addresses and attempted logins of unknown users hashed; no content values, passwords, tokens or POST bodies; reads off; index pseudonymized after 90 days |
| `404.log`, `security.log` | client addresses masked (`MaskIP=enabled`); every query value `***` in 404.log; user agent hashed and shortened |
| Log viewer | e-mail addresses, IP addresses and secrets masked on screen and in downloads |
| Mail suppression list | only a salted hash of each address |
| Mail preference tools on the console | addresses hidden unless `--addresses` is given |
| Search statistics | off by default (`LogSearchStats=disabled`) |
| Mobile detection cookie | off by default (`DetectMobileDevice=disabled`) |
| Administration | no web fonts or scripts from other hosts |

## What you minimize

- **User class**: remove attributes you do not need (date of birth, phone, address) from the user class; every field
  is personal data you must protect, export and delete.
- **Forms**: ask only for what the case needs; make optional what is optional; no free-text "comments" field on a form
  where it is not needed. Remove submissions when the case is closed (see "Information collection forms").
- **Search statistics**: switch on only where you use them, and reset them when the figures are no longer needed.
- **Audit**: for stricter needs, per-field `full | truncate | hash | off` in `[AuditPrivacySettings] Field[]` and
  `BeforeAfter=keys` ([Audit privacy](audit-trail-privacy.md)).
- **Logs**: give kernel logs an age limit (see "Log retention").
- **Extensions**: activate third-party services only with a purpose and a legal basis (see "Third-party extensions").
- **Exports**: CSV files of users, consent logs and submissions hold personal data; delete them when the task is done.

## A review checklist

- [ ] Each personal field of each content class has a purpose.
- [ ] Each form asks only for necessary data, says why, and has a retention rule.
- [ ] Search statistics are off or reset regularly.
- [ ] Log retention set for every log.
- [ ] Audit privacy settings reviewed with the DPO.
- [ ] No third-party extension active without a decision.
- [ ] Exports deleted after use.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Where personal data lives in Exponential](personal-data-inventory.md)
- [Privacy by design checklist](privacy-by-design-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
