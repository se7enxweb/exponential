---
title: "Lesson 9.1: What to back up"
audience: trainer, admin
category: training
order: 91
summary: Learners list the two halves of a backup, find the files that hold keys no restore can recreate, decide what to leave out, and write the backup plan of their training installation. 40 minutes.
keywords: site administration, backup, what to back up, database, var/storage, settings/override, keys, audit keys, mail preferences secret, git_manager, Setup Backup, backup plan
type: help
---
# Lesson 9.1: What to back up

A backup that misses one small file can be worthless: restore the content without the audit keys and the audit trail
can no longer be verified; restore it without the mail preferences secret and every unsubscribe link already sent
stops working. This lesson teaches what an Exponential backup must contain, what it can leave out, and how the two
halves are taken together.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 9: Backups and maintenance](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff |
| Prerequisites | Modules 6 to 8 of this course help; a training installation with shell access |

## Learning objectives

After this lesson, learners can:

- explain why a backup is the database and the files taken at the same moment;
- list the directories an Exponential backup must contain;
- name the key files that cannot be recreated and what breaks without each;
- list what can be left out because it is regenerated;
- write a backup plan for one installation: what, when, where, how many generations.

## Trainer's notes

### How to present it

Source: [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) and
[After installing, 10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore).

1. **Two halves** (3 minutes). The database holds content, users, roles and versions; the files hold uploads,
   settings and own extensions. An image row without its file is a broken image; a file without its row is dead
   weight. Take them at the same moment.
2. **The files** (5 minutes). `var/storage/` (and every `var/<site>/storage/`), `var/storage/sqlite3/` on SQLite
   (with SQLite's online backup, never a plain copy of a busy file), `settings/override/`, `settings/siteaccess/`,
   own extensions and designs, `config.php` and friends when present, the audit directory `var/<site>/log/audit/`.
3. **Keys** (4 minutes). `settings/override/audit.ini.append.php` (audit signing and pseudonym keys),
   `settings/override/mailpreferences.ini.append.php` (the site secret of the e-mail preferences), and Velocity's
   configuration tree with its certificate keys. Small, essential, sensitive.
4. **Leave out** (2 minutes). Caches, `var/autoload/`, `vendor/` (rebuilt from `composer.lock`), Velocity's logs and
   pid files, `var/log/mail/`, `var/tmp/`.
5. **Where it goes** (3 minutes). Away from the server, several generations, encrypted: `settings/override/` holds the
   database password. A backup is as sensitive as the server.
6. **From the administration** (3 minutes). Where the extension `git_manager` is installed, **Setup > Backup**
   creates timestamped backups of the database and files, optionally encrypted. It needs `mysqldump`, so it covers
   MySQL and MariaDB sites only.

### Common questions

**Do we need maintenance mode for every backup?** Not for the database alone on SQLite or MySQL with InnoDB; it makes
the database and the files agree. A site that cannot go offline every night can take the database online and the
files right after it, accepting a small window.

**Should the kernel be in the backup?** It comes with the release and can be fetched again at the same version. Back
it up only when someone changed files in place; **Setup > Upgrade check** lists files that differ from the release.

**How long to keep backups?** A decision for the organization; see
[Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md).

### Pitfalls

- **Copying a busy SQLite file.** Use the online backup; a plain copy during a write, or without its `-wal` file, can
  be inconsistent.
- **Backing up to the same disk.** A full disk or a lost server takes the backup with it.
- **Handing a backup to a customer unchecked.** It contains credentials.

## Hands-on exercise

Use your own training installation, in a terminal at the installation root. Nothing is changed in this exercise.

1. Find your database: `ls -l var/storage/sqlite3/` (on SQLite), or read the database section of
   `settings/override/site.ini.append.php` for the engine (do not show the password to anyone).
2. Measure the storage: `du -sh var/storage var/*/storage 2>/dev/null`.
3. Check which key files exist:

   ```bash
   ls -l settings/override/audit.ini.append.php settings/override/mailpreferences.ini.append.php
   ```

4. Find the audit directory: `ls -d var/*/log/audit var/log/audit 2>/dev/null`.
5. Measure what you will leave out: `du -sh var/cache var/*/cache var/autoload vendor 2>/dev/null`.
6. If your installation runs on Velocity, find its configuration tree as your trainer shows.
7. Open **Setup** in the administration and check whether **Backup** is in the left column (it appears only where
   `git_manager` is installed). If it is, open it and read **Create a backup** and **Existing backups**; do not create
   anything.
8. Write your backup plan in five lines: database command, file list, key files, destination off the machine, and
   generations kept.
9. Compare your plan with the checklist of
   [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#checklist).

## Expected result

- Each learner has a written plan naming `var/storage`, `settings/override`, `settings/siteaccess`, the two key files
  and the audit directory, and leaving out caches, `var/autoload` and `vendor`.
- The plan stores the backup off the training machine and keeps more than one generation.
- The trainer checks two plans against the checklist on the projector.

## Quiz

1. Why must the database and the files be taken at the same moment?
2. Which file holds the keys that make the audit trail verifiable?
   A. `settings/site.ini` B. `settings/override/audit.ini.append.php` C. `var/log/audit.log` D. `config.php`
3. What breaks when `settings/override/mailpreferences.ini.append.php` is lost?
4. Name three things a backup can leave out.
5. Why must a backup be encrypted and restricted?

<details>
<summary>Answers</summary>

1. So that every row has its file and every file its row; otherwise images break or files are orphaned.
2. **B.** ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).
3. A new secret is generated: every link in mail already sent stops working and the suppression list stops matching.
4. Any three of: caches, `var/autoload/`, `vendor/`, Velocity's logs and pid files, `var/log/mail/`, `var/tmp/`.
5. It contains `settings/override/` with the database password and other credentials.

</details>

## Further reading

- [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md): the full list and checklist.
- [Database backups per engine](../../../hosting/operations/database-backups-per-engine.md): the commands for each
  database.
- [git_manager](../../../../features/6.0/extensions/git_manager.md): backups from the administration.
- [Storage layout](../../../hosting/operations/storage-layout.md): where Exponential keeps its files.

## Related pages

- [Module 9: Backups and maintenance](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
