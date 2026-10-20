---
title: Backup schedules and retention
audience: government, hosting, admin, project-manager
category: public-sector
order: 50
summary: For public bodies that write a backup policy for an Exponential site. The decisions a policy must make, the rules the sources state, the retention periods Exponential keeps on its own, and how backup retention meets records management and data protection duties.
keywords: backup schedule, retention, generations, backup policy, records management, retention schedule, audit retention, LiveDays, ArchiveDays, erasure, personal data in backups, continuity
type: help
---
# Backup schedules and retention

This page is for IT managers, records officers and data protection officers in public bodies who write the backup
policy of a site. It lists the decisions the policy must make, the rules the sources state, which records Exponential
keeps for a set time by itself, and where backup retention meets your records and data protection duties. It gives no
fixed figures of its own; the hosting page [Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md)
has the same structure for providers.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Rules the sources state](#rules-the-sources-state)
2. [Decisions of a backup policy](#decisions-of-a-backup-policy)
3. [Retention inside Exponential](#retention-inside-exponential)
4. [Records management and backups](#records-management-and-backups)
5. [Personal data in backups](#personal-data-in-backups)
6. [Checklist](#checklist)

## Rules the sources state

| Rule | Source |
|---|---|
| A backup is the database and the files, taken at the same moment | [10.9](../../../../install/10-after-installing.md#109-backups-and-restore) |
| Store backups away from the server, and keep several generations | [10.9](../../../../install/10-after-installing.md#109-backups-and-restore) |
| Back up before every upgrade | [11 Upgrading](../../../../install/11-upgrading.md) |
| A backup from the last seven days exists and has been restored at least once on another machine | [Operating a site, weekly checklist](../../../../guides/operating-a-site.md#10-a-weekly-checklist) |

## Decisions of a backup policy

| Decision | Options | Consider |
|---|---|---|
| Database frequency | nightly; several times a day; continuous with the engine's own log-based recovery | how much editing happens; your recovery point ([RPO and RTO as decisions](rpo-and-rto-as-decisions.md)) |
| File frequency | with every database backup, or incremental | a restore needs both halves from the same moment |
| Generations | daily, weekly, monthly, yearly copies | how late a mistake may be noticed |
| Location | another building, another region, another provider | [Offsite and encrypted copies](offsite-and-encrypted-copies.md) |
| Encryption | at rest and in transit | the backup holds credentials and personal data |
| Who may restore | named roles | separation of duties |
| Restore tests | how often, on which machine | [Restore testing](restore-testing.md) |
| Extra backups | before upgrades, engine changes, restores, bulk imports | a restore point right before risky work |

## Retention inside Exponential

Some records are kept for a set time by Exponential itself. A backup keeps them longer, so align the two
([Log and data retention](../../../hosting/security/log-and-data-retention.md)):

| Record | Setting | Shipped |
|---|---|---|
| Audit trail, live (content, access, system, commerce) | `audit.ini [AuditChannel_*] LiveDays` | 90 days |
| Audit trail, archives | `ArchiveDays` | 730 days |
| Audit trail, read channel | `LiveDays` / `ArchiveDays` | 30 / 90 days |
| E-mail consent log of people who are gone | `mailpreferences.ini [ConsentSettings] RetentionDays` | 1095 days |
| Archives of `404.log` / `security.log` | `logviewer.ini [RotationSettings]` | pruned after 30 / 90 days |
| Archives of other logs | `site.ini [FileSettings] LogFileRotateCount`, `LogFileMaxAge` | 3 archives, no age limit |

## Records management and backups

Backups are a continuity tool, not an archive. Many records policies distinguish the two: the archive keeps records
for their required period in a managed form; backups keep the system restorable for a shorter time. For an Exponential
site that usually means:

- published content that must be kept as a record goes to your records system or archive by your records process,
  not by relying on old backups;
- the audit trail can be kept for years in its own signed archives (the sources show a reference configuration with
  `ArchiveDays=3650`) on storage the web server cannot change ([Audit trail archives and backups](audit-trail-archives-and-backups.md));
- backup generations are kept only as long as continuity needs.

Records standards such as [ISO 15489-1](https://www.iso.org/standard/62542.html) describe the records side; agree the
split with your records officer.

## Personal data in backups

When someone's data is erased on the live site (for example
`./console exp:mail:preferences erase --email=<address> --yes` for mail preferences), older backups still hold it until
they expire ([Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md)). Common
practices: keep backup retention short and documented in the privacy notice, and after a restore, apply the erasures
made since the backup date again. Which practice is required is a legal question for your organization.

## Checklist

- [ ] The policy states frequency, generations, location, encryption, who may restore and restore tests.
- [ ] Extra backups before upgrades and other risky changes are in the change procedure.
- [ ] Exponential's own retention settings are known and agree with the policy.
- [ ] Records management and backups are separated and agreed with the records officer.
- [ ] The handling of erased personal data after a restore is decided.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Privacy standards](../../../../standards/privacy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
