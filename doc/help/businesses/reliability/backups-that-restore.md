---
title: Backups that restore
audience: businesses, admin, hosting, project-manager
category: business-solutions
order: 70
summary: What a business must back up on an Exponential site, how often, where to keep it, and the decisions a manager has to make, with links to the exact commands for each database.
keywords: backup, backups, restore, recovery point, recovery time, retention, database backup, var/storage, settings/override, maintenance mode, offsite
type: help
---
# Backups that restore

This page is for owners and managers who are responsible for not losing the business's content, orders and member
data, and for the operator who takes the backups. It explains what a complete backup is, the decisions a manager
must make, and how to know the backups work. The commands are on
[Operating a site, back up and restore](../../../guides/operating-a-site.md#6-back-up-and-restore) and
[Database support, backup and restore](../../database-support.md#backup-and-restore).

## Contents

1. [A backup is two halves](#a-backup-is-two-halves)
2. [What to back up](#what-to-back-up)
3. [The decisions a manager makes](#the-decisions-a-manager-makes)
4. [Taking a backup](#taking-a-backup)
5. [Backups before every change](#backups-before-every-change)
6. [Keeping backups safe](#keeping-backups-safe)
7. [Small mistakes do not need a restore](#small-mistakes-do-not-need-a-restore)
8. [Checklist](#checklist)

## A backup is two halves

"Exponential keeps state in two places: the **database** and the **files**" ... "A backup is both, taken at the same
time" ([Operating a site](../../../guides/operating-a-site.md#6-back-up-and-restore)). A database without the
uploaded images, or images without the database that points to them, does not bring a site back.

The second rule is just as important: "Test a restore on another server at least once; a backup you have never
restored is a hope" (same source). That is the subject of [Restore drills](restore-drills.md).

## What to back up

From [Backups and restores](../../creators/website-creators/backups-and-restores.md#what-to-back-up):

| What | Why |
|---|---|
| The database | All content, users, orders and many settings |
| `var/storage` and each site's `var/<site>/storage` | Uploaded images and files |
| `settings/override` and `settings/siteaccess` | The installation's settings; they contain passwords, so keep backups private |
| Your own extensions and designs | If they are not kept in a Git repository |
| `settings/override/audit.ini.append.php` | Holds the audit trail's signing keys ([Audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes)) |

Caches are rebuilt automatically and need no backup.

## The decisions a manager makes

These are business decisions, not technical ones. Write the answers down; the operator turns them into a schedule.
The hosting section explains each option in depth:
[Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md#the-decisions-of-a-backup-policy).

| Decision | Question to answer |
|---|---|
| Recovery point | How much work (orders, edits, sign-ups) can we afford to lose? That sets how often the database is backed up. |
| Recovery time | How long may the site be down while we restore? Only a [restore drill](restore-drills.md) tells you how long it really takes. |
| Generations | How far back must we be able to go, for a mistake noticed late? |
| Location | Where are copies kept, away from the server? Another server, data center or provider? |
| Encryption | Are backups encrypted at rest and in transit? They contain credentials and personal data. |
| Who may restore | Only the host, or also our own staff? |
| Retention and deletion | How long are backups kept, and how do we honor deletion requests for personal data? |

> **Note**
> The project publishes no recommended frequency or retention period. The guides give an example only: "for example
> daily for a week, weekly for a month"
> ([Backups and restores](../../creators/website-creators/backups-and-restores.md#retention)). Choose your own based
> on your answers above.

## Taking a backup

The operator follows the guide for your database. In outline
([Operating a site](../../../guides/operating-a-site.md#6-back-up-and-restore)):

1. Optionally switch on maintenance mode so the database and the files match:
   `php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user`.
2. Back up the database with its own tool (each engine's command is in
   [Database support](../../database-support.md#backup-and-restore)).
3. Archive the files: `var/storage`, `settings/override`, `settings/siteaccess`.
4. Switch maintenance off: `php bin/php/maintenance.php off --allow-root-user`.

On SQLite (with its online `.backup`) and on MySQL with InnoDB (with `--single-transaction`) the database backup
alone is consistent without maintenance mode
([Backups and restores](../../creators/website-creators/backups-and-restores.md#taking-a-backup)). PostgreSQL has
point-in-time recovery of its own ([Database support](../../database-support.md#2-postgresql)).

> **Warning**
> Never write a database password on the command line, where it lands in the shell history and can be seen by other
> users of the machine. Use the tool's prompt or option file
> ([Database support](../../database-support.md#backup-and-restore)).

## Backups before every change

Besides the schedule, take a backup right before each risky change
([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md#backups-before-every-change)):

- before an **upgrade** of Exponential: "Back up first, every time";
- before a **Composer update** or package install from the administration;
- before a **change of database engine**;
- before a **restore**, so a wrong restore can be undone.

## Keeping backups safe

- **Away from the server.** A backup on the same disk is lost with the disk.
- **Private.** Backups contain `settings/override` (credentials) and personal data. Restrict who can read them.
- **Several generations.** One copy that was taken after the damage is no help.
- **Watched.** A failed backup job must alert someone, just like a failed health check
  ([Health checks and monitoring](health-checks-and-monitoring.md)).
- **Data protection.** Backups are subject to the same rules as the live data. See
  [Data retention and deletion](../security/data-retention-and-deletion.md); this is not legal advice.

## Small mistakes do not need a restore

A full restore brings the whole site back to the moment of the backup, and everything published since is lost
([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md#before-you-restore)). For a single
mistake, use the tools inside Exponential first:

| Mistake | Use instead |
|---|---|
| An item was removed | The trash: restore it ([Publishing, versions and the trash](../../publishing-and-versions.md#remove-and-restore)) |
| A text was overwritten | Manage versions: make a new draft from an earlier version ([Publishing, versions and the trash](../../publishing-and-versions.md#go-back-to-an-earlier-version)) |
| Not sure who changed it | The audit trail ([Audit trail for operations](audit-trail-for-operations.md)) |

## Checklist

- [ ] Database and files are backed up together, on a schedule that matches the recovery point you chose.
- [ ] Copies are kept away from the server, encrypted, in several generations.
- [ ] A failed backup alerts a person.
- [ ] A backup is taken before every upgrade, Composer update, engine change and restore.
- [ ] A backup was restored on another machine in the last quarter ([Restore drills](restore-drills.md)).
- [ ] Editors know about the trash and versions, so small mistakes never need a restore.

## Related pages

- [Restore drills](restore-drills.md)
- [Backups and restores (website creators)](../../creators/website-creators/backups-and-restores.md)
- [Backups: what to back up (hosting)](../../hosting/operations/backups-what-to-back-up.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
