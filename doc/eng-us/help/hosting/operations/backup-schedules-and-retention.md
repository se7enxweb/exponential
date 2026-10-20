---
title: Backup schedules and retention
audience: hosting
category: hosting
order: 60
summary: The choices a host makes when it turns Exponential backups into a service (how often, how many generations, where they are kept, what is backed up before a change) and the retention settings inside Exponential that a backup policy has to agree with.
keywords: backup schedule, retention, generations, off-site, before upgrade, point-in-time recovery, audit retention, consent retention, log retention, hosting plan, backup policy
type: help
---
# Backup schedules and retention

This page is for product managers who define a backup service for Exponential hosting and for the system
administrators who implement it. It answers: which decisions a backup policy has to make, what the Exponential sources
recommend, which retention settings exist inside Exponential itself, and how to describe the result to customers
without promising what the platform cannot keep.

What goes into a backup is on [Backups: what to back up](backups-what-to-back-up.md); the commands are on
[Database backups per engine](database-backups-per-engine.md). This page gives no prices and no fixed figures of its
own: every number below is quoted from the repository, and the rest are options for you to decide.

## Contents

1. [What the sources recommend](#what-the-sources-recommend)
2. [The decisions of a backup policy](#the-decisions-of-a-backup-policy)
3. [Backups before every change](#backups-before-every-change)
4. [Retention inside Exponential](#retention-inside-exponential)
5. [Backups and erased personal data](#backups-and-erased-personal-data)
6. [Describing the service to customers](#describing-the-service-to-customers)
7. [Checklist](#checklist)

## What the sources recommend

The installation book and the guides state a small number of rules, and they are the floor of any policy:

| Rule | Source |
|---|---|
| A backup is the database and the files, taken at the same moment | [10.9](../../../install/10-after-installing.md#109-backups-and-restore) |
| Store backups away from the server, and keep several generations | [10.9](../../../install/10-after-installing.md#109-backups-and-restore) |
| Back up before every upgrade: "Back up first, every time" | [11.1](../../../install/11-upgrading.md#111-before-you-start) |
| Test a restore on another machine at least once and after every major upgrade | [10.9, Restore](../../../install/10-after-installing.md#restore) |
| Weekly: "A backup from the last seven days exists and has been restored at least once on another machine" | [Operating a site, weekly checklist](../../../guides/operating-a-site.md#10-a-weekly-checklist) |

## The decisions of a backup policy

Each row is a choice you make for a plan or a customer; the right-hand column says what to consider.

| Decision | Options | What to consider |
|---|---|---|
| Frequency of the database backup | nightly, several times a day, continuous | how much editing the customer does; the online backup of SQLite (`.backup`) and `--single-transaction` dumps of InnoDB run without taking the site offline |
| Frequency of the file backup | with every database backup, or less often with incremental copies | uploads change less often than content, but a restore needs both halves from the same moment |
| Consistency | maintenance window, or online database plus files right after it | a window makes database and files agree; it costs the customer a short 503 page |
| Generations | how many daily, weekly and monthly copies | the more generations, the further back a customer can go after a mistake noticed late |
| Location | another server, another data center, another provider | "away from the server" is the minimum |
| Encryption | at rest, in transit | the backup contains `settings/override/`, which holds credentials |
| Point-in-time recovery | none, or the database's own log-based recovery | the sources name it for PostgreSQL ("point-in-time recovery of its own", [Database support](../../database-support.md#2-postgresql)); for other engines check their documentation |
| Who may restore | the host only, or the customer from a panel | a customer restore must never overwrite another customer's data |
| Restore tests | how often, on which machine | a backup "that was never restored is a hope, not a backup" |

> **Tip**
> Write the policy per plan in one table like this, with your values in a third column, and put it into the plan's
> description. Support staff then answer "how far back can you go?" from the same table the customer saw.

## Backups before every change

Besides the schedule, take a backup right before each risky change. The sources ask for one before:

- **an upgrade** of Exponential: the database step "cannot be undone by putting files back"
  ([11.1](../../../install/11-upgrading.md#111-before-you-start));
- **a Composer update or package install** from the administration: ezupdate asks you to confirm that a backup
  exists before **Install updates** ([ezupdate](../../../features/6.0/extensions/ezupdate.md));
- **a change of database engine** ([Moving a site to another database](../../database-support.md#moving-a-site-to-another-database));
- **a restore**: back up the current state first, so a wrong restore can be undone.

A host can offer this as a button or a ticket ("snapshot before change"); see [Upgrading customer sites](upgrading-customer-sites.md).

## Retention inside Exponential

Exponential keeps some records for a set time on its own. A backup keeps them longer, so align the two:

| Record | Setting | Shipped value |
|---|---|---|
| Audit trail, live files (content, access, system, commerce) | `audit.ini [AuditChannel_*] LiveDays` | 90 days |
| Audit trail, archives | `ArchiveDays` | 730 days |
| Audit trail, read channel | `LiveDays` / `ArchiveDays` of `AuditChannel_read` | 30 / 90 days |
| E-mail consent log of people who are gone | `mailpreferences.ini [ConsentSettings] RetentionDays` | 1095 days |
| Unconfirmed e-mail opt-ins | `[ConsentSettings] PendingDays` | 7 days |
| Suppression list entries | `[SuppressionSettings] RetentionDays` | 0 (kept forever; only a hash is stored) |
| Archives of `404.log` / `security.log` | `logviewer.ini [RotationSettings]` | pruned after 30 / 90 days |
| Archives of other logs | `site.ini [FileSettings] LogFileRotateCount`, `LogFileMaxAge` | 3 archives, no age limit |

Sources: [The audit trail](../../../features/6.0/audit-trail.md),
[E-mail preferences, retention](../../../guides/mail-preferences-administrator.md#9-retention-and-the-cronjob),
[Request logs](../../../features/6.0/request-logs.md), [Log rotation](../../../features/6.0/log-rotation.md).
The security pages discuss what these values mean for a data protection policy:
[Log and data retention](../security/log-and-data-retention.md).

## Backups and erased personal data

When a person asks for their data to be erased, Exponential can remove it from the live site (for example
`./console exp:mail:preferences erase --email=<address> --yes` for the e-mail preferences). The copies in older
backups still hold it until those backups expire. How to handle that (shortening retention, documenting the backup
period in the privacy notice, restoring with care) is a legal question.

> **Note**
> This page describes what the software provides. It is not legal advice. The host and each customer must take their
> own legal counsel on retention periods and on personal data in backups.

## Describing the service to customers

Say what is backed up, how often, how long, where, and how a restore is requested. Avoid promises the platform does
not make by itself:

- "Every backup contains the database and the uploaded files from the same moment" is true when the job is built as on
  [Backups: what to back up](backups-what-to-back-up.md).
- A restore brings back the whole site to that moment; restoring one deleted page is a different service (content can
  often be recovered from the trash or from older versions inside Exponential instead).
- Name the restore time you measured in a test, not one you hope for.

## Checklist

- [ ] The policy names frequency, generations, location, encryption and who may restore, per plan.
- [ ] Backups leave the server and are encrypted.
- [ ] A backup is taken before every upgrade, Composer update, engine change and restore.
- [ ] Audit, consent and log retention settings are known and agree with the backup period.
- [ ] Restores are tested on a schedule and the measured time is recorded.
- [ ] Erasure requests and backup retention are covered by the host's and customer's legal advice.

## Related pages

- [Backups: what to back up](backups-what-to-back-up.md)
- [Restore and restore tests](restore-and-restore-tests.md)
- [SLA options](../business/sla-options.md), [Designing hosting plans](../business/designing-hosting-plans.md)
- [Compliance and GDPR](../security/compliance-and-gdpr.md)
- [Monthly operations](../checklists/monthly-operations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
