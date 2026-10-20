---
title: Backups
audience: customer, businesses, hosting
category: customer-success
order: 6
summary: What a complete backup of an Exponential site is, the decisions only the owner can make (how much loss is acceptable, how fast a restore must be, where copies are kept), what to ask your host, and how to avoid a restore for small mistakes.
keywords: backup, restore, database, files, var/storage, settings, recovery point, recovery time, retention, offsite, encryption
type: help
---
# Backups

This page is for the owner who is responsible for not losing content, orders and member data. The backup itself
is usually taken by the host or an operator; the commands are in
[Operating a site, back up and restore](../../../guides/operating-a-site.md) and
[Backups and restores](../../creators/website-creators/backups-and-restores.md). Your job is to decide what is good
enough, and to make sure it is really done.

## A backup is two halves

"Exponential keeps state in two places: the **database** and the **files**" ... "A backup is both, taken at the
same time" ([Operating a site](../../../guides/operating-a-site.md)). The database without the uploaded images, or
the images without the database, does not bring a site back.

| What | Why it matters to you |
|---|---|
| The database | All content, users, orders and many settings |
| `var/storage` and each site's storage | Uploaded images and files |
| `settings/override` and `settings/siteaccess` | The installation's settings; they contain passwords, so backups must stay private |
| Your own extensions and designs | Unless they are kept in a Git repository |
| `settings/override/audit.ini.append.php` | The audit trail's signing keys |

Source: [Backups that restore](../../businesses/reliability/backups-that-restore.md). Caches are rebuilt by
themselves and need no backup.

## The decisions only you can make

Write the answers down and give them to your host; they turn them into a schedule
([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md)).

| Decision | Question |
|---|---|
| Recovery point | How much work (orders, edits, sign-ups) can we afford to lose? That sets how often the database is backed up. |
| Recovery time | How long may the site be down during a restore? Only a [restore test](restore-test.md) tells you the real time. |
| Generations | How far back must we be able to go, for a mistake noticed late? |
| Location | Where are copies kept, away from the server? |
| Encryption | Are copies encrypted? They hold passwords and personal data. |
| Who may restore | Only the host, or also our own staff? |
| Retention | How long are copies kept, and how do we handle a request to delete someone's data? |

> **Note**
> The project publishes no recommended frequency or retention; the guides give only an example, "daily for a week,
> weekly for a month" ([Backups and restores](../../creators/website-creators/backups-and-restores.md)). Choose from
> your own answers.

## What to ask your host

- [ ] Are the database and the files backed up together, on what schedule?
- [ ] Where are copies kept, and is that away from the server and its data center?
- [ ] Are copies encrypted, and who can read them?
- [ ] How many generations are kept?
- [ ] Does a failed backup alert a person?
- [ ] When was a backup last restored on another machine, how long did it take, and what was checked?
- [ ] Is a backup taken right before every upgrade, Composer update or database change?

The last question matters: the hosting guide asks for a backup "before an upgrade", "before a Composer update",
"before a change of database engine" and "before a restore"
([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md)).

## Small mistakes do not need a restore

A full restore takes the whole site back to the moment of the backup, and everything since is lost. Train your
editors to use the tools inside Exponential first
([Backups that restore](../../businesses/reliability/backups-that-restore.md)):

| Mistake | Use instead |
|---|---|
| An item was removed | The trash: restore it ([Publishing and versions](../../publishing-and-versions.md)) |
| A text was overwritten | Versions: make a new draft from an earlier one (same page) |
| Not sure who changed what | The audit trail ([Audit trail for operations](../../businesses/reliability/audit-trail-for-operations.md)) |

## Your checklist

- [ ] The decisions above are written down and agreed with the host.
- [ ] Database and files are backed up together.
- [ ] Copies are off the server, private and in several generations.
- [ ] A failed backup reaches a person.
- [ ] A restore test was done in the last quarter ([Restore test](restore-test.md)).
- [ ] Editors know the trash and versions.

## Related pages

- [Restore test](restore-test.md)
- [Backups that restore](../../businesses/reliability/backups-that-restore.md)
- [What to back up (for hosts)](../../hosting/operations/backups-what-to-back-up.md)
- [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
