---
title: FOI readiness
audience: government, admin, project-manager
category: public-sector
order: 210
summary: How a public body prepares its Exponential site and data for freedom of information requests - knowing where information lives, finding it, versions and audit records, exports, redaction of personal data, and proactive publication.
keywords: freedom of information, FOI, FOIA, access to information, requests, records, search, versions, audit trail, export, redaction, proactive publication, public sector
type: help
---
# FOI readiness

This checklist is for the information officer and site administrator of a public body that receives freedom of
information requests. Website content, form submissions and system records may all fall within a request. This page
helps you find and produce them; whether they must be released is a legal question.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel. In the United States, federal agencies follow FOIA ([foia.gov](https://www.foia.gov/)).

## Contents

1. [Know where information lives](#know-where-information-lives)
2. [Finding it](#finding-it)
3. [Producing it](#producing-it)
4. [Personal data in responses](#personal-data-in-responses)
5. [Publishing proactively](#publishing-proactively)

## Know where information lives

- [ ] Published and unpublished content: objects, their versions and translations ([Versions of an object](../../../guides/content-history.md)).
- [ ] Removed content in the trash until purged ([Trash](../../../features/6.0/trash-who-and-where.md)).
- [ ] Form submissions under **Setup > Collected information** ([Collected information](../../../guides/collected-information.md)).
- [ ] Who changed what: the audit trail ([Audit trail](../../../features/6.0/audit-trail.md)).
- [ ] Newsletter content and subscriber data, if used ([Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md)).
- [ ] Backups, which may hold content deleted from the site ([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md)).
- [ ] The map documented, with an owner for each source.

## Finding it

- [ ] Search in the administration and advanced search for content ([Searching](../../searching.md)).
- [ ] The content finder for finding objects across the tree (**Setup > Content finder**) ([Content finder](../../../features/6.0/content-finder.md)).
- [ ] Audit search by name, user and period: `./console exp:audit search --query=<text> --limit=<n>` ([Security and audit](../../../guides/security-and-audit.md)).
- [ ] Draft and pending versions: ask the owners; drafts belong to their authors ([My drafts and my pending items](../../../guides/drafts-and-pending.md)).
- [ ] A staff member trained to do this, not only the supplier.

## Producing it

- [ ] A page or version as visitors saw it: preview of a version ([Version preview view modes](../../../features/6.0/version-preview-view-modes.md)).
- [ ] Many objects of a class: xrowextract export to CSV, XML or JSON ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md#1-what-to-export)).
- [ ] Form submissions as CSV: `./console ext:bccie:export --object=<id> --format=csv` (same page).
- [ ] Audit records: the audit export (`/audit/export`) or `./console exp:audit export` ([Audit guide](../../../bc/6.0/audit.md)).
- [ ] A record of what was searched, when and by whom.

## Personal data in responses

- [ ] Responses reviewed for personal data of third parties before release.
- [ ] Exports of form submissions and user data handled under access controls and deleted when the request is closed.
- [ ] Audit records contain pseudonymized fields after the set period; know what they can and cannot show ([Audit trail](../../../features/6.0/audit-trail.md)).

## Publishing proactively

- [ ] A disclosure log or publication scheme page, if your law expects one.
- [ ] Frequently requested information published as pages or datasets ([Open data readiness](open-data-readiness.md)).
- [ ] Records and FOI rules aligned ([Records checklist](records-checklist.md)).

## Related pages

- [Government checklists](README.md)
- [Records checklist](records-checklist.md)
- The transparency and FOI section of this government help (written by another author)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
