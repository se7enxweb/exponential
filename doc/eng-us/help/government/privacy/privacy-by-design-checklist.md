---
title: Privacy by design checklist
audience: government, project-manager, admin, developer
category: public-sector
order: 320
summary: A checklist for public bodies building or reviewing a site on Exponential - privacy by design and by default from planning through launch and operation, with the setting or tool for each item.
keywords: privacy by design, privacy by default, GDPR article 25, checklist, launch, review, content model, forms, extensions, retention, logs, audit, mail, cookies, rights, public sector
type: help
---
# Privacy by design checklist

This page is for project managers, data protection officers, administrators and developers. Use it when planning a new
site, before launch, and at each yearly review. Each item points to the place in Exponential where it is done; details
are in the pages of this section.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Planning](#planning)
2. [Building](#building)
3. [Before launch](#before-launch)
4. [Operating](#operating)
5. [Related pages](#related-pages)

## Planning

- [ ] Purposes and legal bases for each processing activity written down.
- [ ] Data inventory drafted ("Where personal data lives in Exponential").
- [ ] DPIA decision taken and documented.
- [ ] Processors named; agreements planned (hosting, implementation, support).
- [ ] Third-party services decided one by one; none activated "just in case".

## Building

- [ ] User class with only the fields needed.
- [ ] Forms with only the fields needed, a notice next to each, an owner and a retention rule.
- [ ] No third-party script or embed loaded before consent where consent is needed; CSP allows only the hosts used.
- [ ] Mail: every optional mail has a category; footer sender details set (`[FooterSettings] OrganisationName`,
      `OrganisationAddress`); the site secret backed up.
- [ ] Search statistics off unless needed.
- [ ] Audit privacy settings reviewed (`[AuditPrivacySettings]`); decide on recording publishing.
- [ ] Log masking left on (`MaskIP=enabled`, log viewer masking).

## Before launch

- [ ] Retention settings applied: audit channels, `MaxAgeDays` per log, consent log, suppression list.
- [ ] Cronjobs running: `frequent` (audit), `infrequent` (mail preferences), `logs` if you rely on age limits.
- [ ] Cookies checked in a private window; cookie notice written.
- [ ] Privacy notice published and linked from every page, form and mail.
- [ ] Policies for sensitive data given to few: `infocollector/read`, `mailpreferences/export`, `audit/read`,
      `setup/unmasklogs`, `role/assign` (shows e-mail addresses in account lists).
- [ ] Access and erasure procedure written and tested on a test account.

## Operating

- [ ] Form submissions removed on schedule.
- [ ] Search statistics reset on schedule.
- [ ] Account review twice a year (Users > Check user accounts).
- [ ] Exports deleted after use.
- [ ] Notice reviewed when extensions, forms or categories change.
- [ ] Yearly dry runs of retention: `./console exp:logs --prune --dry-run`, `./console exp:mail:consent cleanup --dry-run`,
      `./console exp:audit purge --dry-run`.

## Related pages

- [Data minimization](data-minimization.md)
- [Retention of personal data: an overview](retention-overview.md)
- [Privacy standards](../../../standards/privacy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
