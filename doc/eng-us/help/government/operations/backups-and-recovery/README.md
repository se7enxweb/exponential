---
title: Backups and recovery for public-sector sites
audience: government, hosting, admin, project-manager
category: public-sector
order: 10
summary: Index of the backup and recovery pages for public bodies: what to back up, per-engine database backups, files and keys, schedules and retention, backups from the administration, restore tests, disaster recovery, RPO and RTO, audit archives and off-site encrypted copies.
keywords: backup, recovery, restore, restore test, disaster recovery, RPO, RTO, retention, keys, audit archives, offsite, encryption, git_manager, continuity
type: help
---
# Backups and recovery for public-sector sites

This folder is for operations teams, continuity planners, records officers and security officers in public bodies who
must make sure an Exponential site can be brought back after a failure, an attack or a mistake. It covers what to back
up, how for each database engine, the files and keys, schedules and retention, backups from the administration,
restore testing, disaster recovery planning, recovery objectives, the audit trail's archives and off-site encrypted
copies. The commands are the same as in the hosting section's
[operations pages](../../../hosting/operations/README.md); these pages add the public-sector duties: continuity plans,
records management, evidence and data location.

## Pages

| Page | Answers |
|---|---|
| [What to back up](what-to-back-up.md) | The database and files a backup needs, the key files, and what to leave out. |
| [Backups per database engine](backups-per-database-engine.md) | The backup and restore command of each supported engine, and fitting your backup system. |
| [File and settings backups](file-and-settings-backups.md) | The directories to include and exclude, multi-site and cluster layouts, owners on restore. |
| [Backup schedules and retention](backup-schedules-and-retention.md) | The decisions of a backup policy and how retention meets records and data protection duties. |
| [Keys and secrets in backups](keys-and-secrets-in-backups.md) | Which files hold keys that cannot be recreated, and how to back them up safely. |
| [Backups from the administration with git_manager](backups-from-the-admin-with-git-manager.md) | Making a backup from Setup > Backup without a shell, and its limits. |
| [Restore testing](restore-testing.md) | How often to test, what to check, and how to record evidence. |
| [Restoring to a test server](restoring-to-a-test-server.md) | The restore procedure on a separate machine, with mail and cron kept quiet. |
| [A disaster recovery plan](disaster-recovery-plan.md) | Scenarios, the parts of a plan, an outline and the technical recovery. |
| [RPO and RTO as decisions](rpo-and-rto-as-decisions.md) | Who decides recovery objectives, and what meets which value. |
| [Audit trail archives and backups](audit-trail-archives-and-backups.md) | Keeping audit evidence for years, verifying it and reading old days. |
| [Offsite and encrypted copies](offsite-and-encrypted-copies.md) | Where copies may go, how to encrypt them and keep the keys recoverable. |

## Reading order

1. [What to back up](what-to-back-up.md), then [per engine](backups-per-database-engine.md) and
   [files and settings](file-and-settings-backups.md).
2. [Keys and secrets in backups](keys-and-secrets-in-backups.md) and [Offsite and encrypted copies](offsite-and-encrypted-copies.md).
3. The policy: [RPO and RTO as decisions](rpo-and-rto-as-decisions.md), then [Backup schedules and retention](backup-schedules-and-retention.md)
   and [Audit trail archives and backups](audit-trail-archives-and-backups.md).
4. Proof: [Restore testing](restore-testing.md) and [Restoring to a test server](restoring-to-a-test-server.md).
5. The plan: [A disaster recovery plan](disaster-recovery-plan.md).
6. Optional: [git_manager](backups-from-the-admin-with-git-manager.md) for administrators without a shell.

See also [Hosting](../hosting/README.md) and [Availability](../availability/README.md).

## Related pages

- Up: [Operations](../README.md)
- Start of the section: [Governments: start here](../../government-start-here.md)
- Runbook for an incident: [Restore from backup](../../../hosting/runbooks/restore-from-backup.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
