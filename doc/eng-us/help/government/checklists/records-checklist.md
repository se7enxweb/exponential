---
title: Records checklist
audience: government, admin, project-manager
category: public-sector
order: 60
summary: A checklist for public bodies that must keep website content as records - what Exponential keeps (versions, trash, audit trail), what it does not do (retention schedules, disposal, archival transfer), and the routines that close the gap.
keywords: records management, records, retention schedule, versions, history, audit trail, archive, disposal, ISO 15489, OAIS, export, public sector
type: help
---
# Records checklist

This checklist is for the records manager and site administrator of a public body whose website content may count as
public records. It lists what Exponential keeps by itself and what you must organize around it.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel. The international standard for records management is ISO 15489-1
> ([iso.org](https://www.iso.org/standard/62542.html)); for long-term digital archives, ISO 14721 (OAIS,
> [iso.org](https://www.iso.org/standard/57284.html)).

## Contents

1. [Decide what is a record](#decide-what-is-a-record)
2. [What Exponential keeps](#what-exponential-keeps)
3. [What Exponential does not do](#what-exponential-does-not-do)
4. [Routines](#routines)
5. [Transfer and disposal](#transfer-and-disposal)

## Decide what is a record

- [ ] Your records schedule names which website content is a record (for example decisions, notices, consultation documents).
- [ ] Each such kind of content has its own content class, so it can be found and exported by class ([Designing your content model](../../creators/website-creators/designing-your-content-model.md)).
- [ ] The retention period of each class is written down.

## What Exponential keeps

- [ ] **Versions.** Each object keeps its versions; they can be compared and restored on the versions page ([Versions of an object](../../../guides/content-history.md)). Check how many versions your settings keep before older ones are removed.
- [ ] **Trash.** Removed objects wait in the trash, with who removed them and where from, until purged ([Trash](../../../features/6.0/trash-who-and-where.md)).
- [ ] **Audit trail.** Who changed what and when, hash-chained; 90 days live and 2 years archived per channel by default ([Audit trail](../../../features/6.0/audit-trail.md), [Privacy standards](../../../standards/privacy.md#gdpr)).
- [ ] **Object states.** Mark content as, for example, "record" or "archived" and limit who may change it ([Object states](../../../guides/object-states.md)).

## What Exponential does not do

Know these gaps; nothing in the documentation describes the following as built in:

- [ ] Retention schedules that remove or archive content automatically by class.
- [ ] Legal holds that prevent deletion.
- [ ] Transfer to an archive in an archival package format.
- [ ] Records metadata according to a records standard (beyond the fields your content classes define).

Plan routines or tools for each one you need.

## Routines

- [ ] Restrict removal of record classes to a few roles ([Roles and policies](../../../guides/roles-and-policies.md)).
- [ ] Do not purge the trash for record classes without the records manager's approval.
- [ ] Export record classes on a schedule with xrowextract (CSV, XML or JSON per class, with a manifest) ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md#1-what-to-export)).
- [ ] Keep the storage files (attached documents) with each export.
- [ ] Back up the audit directory and its signing keys ([Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md)).
- [ ] Verify the audit chain regularly: `./console exp:audit verify` ([Security and audit](../../../guides/security-and-audit.md)).

## Transfer and disposal

- [ ] Transfer to the archive: export, check the export against the admin's counts, hand over with a manifest.
- [ ] Disposal: remove the objects, purge them from the trash, and record what was disposed of and when.
- [ ] Remember backups: disposed content remains in older backups until they expire ([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md)).
- [ ] Static snapshot of the public site, if your rules require one: `exp:makestaticcache` writes static pages ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md)).

## Related pages

- [Government checklists](README.md)
- [FOI readiness](foi-readiness.md)
- The records and archiving section of this government help (written by another author)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
