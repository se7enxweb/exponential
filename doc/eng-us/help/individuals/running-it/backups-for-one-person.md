---
title: Backups for one person
audience: individual, customer, website-creator
category: personal-sites
order: 1641
summary: A backup routine one person can keep for an Exponential site - what to copy (database and files together), backups from the administration with git_manager, the command-line way for SQLite and other databases, keeping copies off the server, encryption, and the restore test that proves it works.
keywords: backup, restore, database, SQLite, var/storage, settings, git_manager, Setup Backup, encryption, offsite, restore test, maintenance mode, retention, individual
type: help
---
# Backups for one person

This page is for individuals who run their own site and have nobody else to rely on for backups. A backup is two
halves taken together, the **database** and the **files**, and it only counts once you have restored it somewhere
([Backups](../../customers/healthy/backups.md), [Backups and restores](../../creators/website-creators/backups-and-restores.md)).

## Contents

1. [What to copy](#what-to-copy)
2. [Way 1: from the administration](#way-1-from-the-administration)
3. [Way 2: on the command line](#way-2-on-the-command-line)
4. [Way 3: your host's backups](#way-3-your-hosts-backups)
5. [Getting copies off the server](#getting-copies-off-the-server)
6. [The restore test](#the-restore-test)
7. [A routine you can keep](#a-routine-you-can-keep)

## What to copy

| Part | Why |
|---|---|
| The database (with SQLite, the file under `var/storage/sqlite3/`) | All content, users, settings stored there |
| `var/storage` and `var/<site>/storage` | Uploaded images and files |
| `settings/override`, `settings/siteaccess` | Your settings; they contain passwords, so keep backups private |
| Your own extensions and designs | If they are not in a Git repository |

Caches are rebuilt by themselves and need no backup.

## Way 1: from the administration

The `git_manager` extension (in the root `composer.json`) has a backup manager at **Setup > Backup**
(`/git_manager/backup`) ([git_manager](../../../features/6.0/extensions/git_manager.md)):

| Type | Contains | Use |
|---|---|---|
| Full Site Backup | SQL dump, `var/` archive, site files (`extension/`, `settings/`, `config.php`) | Rebuilding on a new server |
| DB + Files caption | SQL dump and `var/` archive | The regular backup |

Optionally tick **Encrypt backup files** (needs GnuPG on the server). Its requirements list `mysqldump` for database
backups; on SQLite or PostgreSQL, check that the database part is in the archive, or use way 2 for the database.

## Way 2: on the command line

If you have a shell, the builder's page gives the exact commands
([Taking a backup](../../creators/website-creators/backups-and-restores.md#taking-a-backup)). The short version:

1. Optionally switch maintenance mode on, so database and files agree.
2. Back up the database with its own tool. For SQLite:
   `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/db.sqlite'"` (use your database file's name). Never
   copy a live SQLite file with a plain file copy ([SQLite for hosts](../../hosting/basics/sqlite-for-hosts.md)).
3. Archive `var/storage`, `settings/override` and `settings/siteaccess`.
4. Maintenance mode off.

Put it in a small script and a cron job so it does not depend on remembering.

## Way 3: your host's backups

Many hosts back up accounts. Find out ([What to ask your host](../../customers/healthy/what-to-ask-your-host.md)):
how often, how long they are kept, whether you can download one, and how a restore is requested. A host's backup is a
good second copy; do not make it your only one.

## Getting copies off the server

A backup on the same server dies with it. Copy at least one generation elsewhere: your computer, an external disk, a
storage service. Encrypt it if it leaves your control; a backup of a family or club site is a copy of everyone's
data and the settings' passwords.

| Keep | Example |
|---|---|
| Recent | One per day or week, a few generations |
| Older | One per month for a few months |
| Before every update | One, kept until the update has proved itself |

## The restore test

At least once, and after every major update, restore a backup on another machine (your laptop is fine) and check the
front page, a deep page, an image, a download, a sign-in and a form
([The restore test](../../creators/website-creators/backups-and-restores.md#the-restore-test),
[Restore test (customers)](../../customers/healthy/restore-test.md)). Write down how long it took.

## A routine you can keep

- [ ] Automatic backup (way 1 or 2) at a fixed interval.
- [ ] A manual backup before every update or big change.
- [ ] Monthly: copy the newest backup off the server; delete old generations.
- [ ] Twice a year: restore test.
- [ ] Your deputy (family member, club volunteer) knows where the backups are.

## Related pages

- [Running it alone](README.md)
- [Backups and restores](../../creators/website-creators/backups-and-restores.md)
- [Backups that restore](../../businesses/reliability/backups-that-restore.md)
- [Monthly upkeep checklist](../checklists/monthly-upkeep-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
