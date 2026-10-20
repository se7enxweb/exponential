---
title: "FAQ: records and audit"
audience: government, admin, project-manager
category: public-sector
order: 130
summary: Answers to the records and audit questions public bodies ask about Exponential - version history, the trash, the audit trail and its integrity, retention, exports for archives, and what records management functions it does not have.
keywords: records FAQ, audit FAQ, version history, trash, audit trail, tamper-evident, hash chain, retention, archive, records management, export, public sector
type: help
---
# FAQ: records and audit

This page answers the record-keeping and audit questions public bodies ask about Exponential. The answers come from
[Versions of an object](../../../guides/content-history.md), [Audit trail](../../../features/6.0/audit-trail.md) and
[Security and audit](../../../guides/security-and-audit.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the records law that
> applies to your organization with your legal counsel.

## Contents

1. [Does Exponential keep a history of changes?](#does-exponential-keep-a-history-of-changes)
2. [Is deleted content gone?](#is-deleted-content-gone)
3. [What does the audit trail record?](#what-does-the-audit-trail-record)
4. [Can the audit trail be tampered with?](#can-the-audit-trail-be-tampered-with)
5. [How long are audit records kept?](#how-long-are-audit-records-kept)
6. [Is Exponential a records management system?](#is-exponential-a-records-management-system)
7. [Can we export records for an archive?](#can-we-export-records-for-an-archive)

## Does Exponential keep a history of changes?

Yes. Each content object keeps its versions; the versions page compares, restores and cleans them up
([Versions of an object](../../../guides/content-history.md)).

## Is deleted content gone?

Not at once. Removed objects go to the trash, which shows who removed them and from where, until they are purged
([Trash](../../../features/6.0/trash-who-and-where.md)). User objects never go to the trash
([Privacy standards](../../../standards/privacy.md#known-gaps)).

## What does the audit trail record?

Security-relevant events in five channels (content, access, system, commerce, read): sign-ins, content changes,
settings writes, cache clears, maintenance switches and more ([Audit trail](../../../features/6.0/audit-trail.md),
[the Exponential glossary](../../../glossary.md#a)). It is on by default.

## Can the audit trail be tampered with?

It is tamper-evident: records are hash-chained with signed manifests, and `./console exp:audit verify` checks the
chain ([Security and audit](../../../guides/security-and-audit.md)). An alert rule fires if the chain breaks or the
audit is switched off. Keep the signing keys safe and backed up
([Backups: keys and secrets](../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).

## How long are audit records kept?

By default 90 days live and two years archived per channel, then removed automatically; fields are pseudonymized after
a set number of days ([Privacy standards](../../../standards/privacy.md#gdpr), [Audit guide 4.3](../../../bc/6.0/audit.md#43-retention)).
Set the periods your rules require.

## Is Exponential a records management system?

No. It keeps versions, a trash and an audit trail, but the documentation describes no retention schedules by class,
legal holds or archival transfer packages. Use routines or a records system alongside it ([Records checklist](../checklists/records-checklist.md)).

## Can we export records for an archive?

Yes: xrowextract exports each class to CSV, XML or JSON with a manifest; the storage directory holds the attached files;
audit records export with `./console exp:audit export` ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md#1-what-to-export)).

## Related pages

- [Government FAQ](README.md)
- [Records checklist](../checklists/records-checklist.md)
- [FOI readiness](../checklists/foi-readiness.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
