---
title: "Lesson 6.1: What to back up"
audience: trainer, hosting, admin
category: training
order: 61
summary: Learners list the two halves of an Exponential backup, the key files that cannot be recreated and what to leave out, then take a consistent backup of database and files in one maintenance window. 40 minutes.
keywords: hosting and operations, backup, what to back up, var/storage, settings/override, audit keys, mail preferences secret, ssl, maintenance mode, tar, exclude caches, git_manager backup
type: help
---
# Lesson 6.1: What to back up

A site restored from a backup that missed a key file can look complete and still be broken: audit records that can no
longer be verified, links in every sent newsletter that stop working, HTTPS that falls back to a stand-in
certificate. This lesson teaches what belongs in a backup of an Exponential site, what can safely be left out, and how
to take database and files at the same moment.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 6: Backups and recovery](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | System administrators and backup service designers |
| Prerequisites | [Lesson 3.4](../module-03-databases/database-backups-and-moves.md) (database backups per engine) |

## Learning objectives

After this lesson, learners can:

- explain why a backup is the database and the files, taken at the same moment;
- list the directories and files to back up, including the key files that cannot be recreated;
- list what to leave out because the site rebuilds it;
- take a consistent backup in maintenance mode and say when maintenance mode is not needed;
- explain why a backup that contains `settings/override/` must be encrypted.

## Trainer's notes

### How to present it

Source: [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) and
[Chapter 10, Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore).

1. **Two halves** (3 minutes). The database holds content, users, roles and versions; the files hold uploads,
   settings, own extensions and designs. An image in the database but not on disk shows as broken.
2. **The files** (5 minutes). `var/storage/` and every `var/<site>/storage/`; `settings/override/` and
   `settings/siteaccess/`; own extensions and designs not kept in a repository; `config.php` and its companions when
   present; the audit trail directory; `composer.json` and `composer.lock` when Composer manages the site.
3. **Keys that cannot be recreated** (4 minutes). The audit trail's signing and pseudonym keys in
   `settings/override/audit.ini.append.php`; the e-mail preferences' `TokenSecret` (a new secret invalidates every link
   in every mail already sent); Velocity's configuration tree and its `ssl/` directory.
4. **What to leave out** (3 minutes). Caches, `var/autoload/`, `vendor/` (rebuilt from `composer.lock`), Velocity's
   logs and pid files, `var/tmp/`. The release files can be fetched again; **Upgrade check** lists files changed in
   place.
5. **Together** (5 minutes). Maintenance mode makes the pair consistent. On SQLite and MySQL with InnoDB the database
   backup alone is consistent without it; a host that cannot take sites offline every night accepts a small window in
   which a new upload may be in one half only.

### Common questions

**Can customers back up from the administration?** The extension `git_manager` adds **Setup**, **Backup**, with
timestamped backups and optional GnuPG encryption. It needs `mysqldump`, so it covers MySQL and MariaDB sites; the
host's own backup of every engine stays the host's job.

**Do I need the logs?** `var/log/` is optional, useful when an incident must be investigated later.

**How many copies, how often?** That is the backup policy of lesson 6.2.

### Pitfalls

- **Copying a busy SQLite file** instead of using `.backup`. Repeat the rule from lesson 3.4.
- **Backups readable by everyone.** `settings/override/` holds the database password; restrict and encrypt.
- **Forgetting per-site storage.** On an installation with several var directories, list them all.

## Hands-on exercise

Use your own training installation, as the site's user, and keep the backup inside `var/training-backup/` for the
exercise only.

1. List what exists in your installation: `ls -d var/storage var/*/storage settings/override settings/siteaccess`.
2. Check for the key files: `ls settings/override/ | grep -E 'audit|mailpreferences'` (note which exist on your
   installation).
3. Check the size of what you will leave out: `du -sh var/cache var/autoload vendor 2>/dev/null`.
4. Switch maintenance mode on: `php bin/php/maintenance.php on --message="Backup practice" --until=15m`.
5. Back up the database with its engine's tool (SQLite: the `.backup` command of lesson 3.4) into
   `var/training-backup/`.
6. Pack the files:

   ```bash
   tar czf var/training-backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
   ```

7. Switch maintenance mode off: `php bin/php/maintenance.php off`.
8. List the archive's contents and check that the key files of step 2 are in it:
   `tar tzf var/training-backup/files-<date>.tar.gz | grep -E 'audit|mailpreferences'`.
9. Restrict the backup: `chmod 600 var/training-backup/*` and check with `ls -l`.
10. Write a one-paragraph backup list for a customer site: what is included, what is excluded and why.

## Expected result

- The backup directory holds a database copy and a files archive taken in one window.
- Step 8 lists the key files that exist on the installation.
- Step 9: only the owner can read the backups.
- The list of step 10 names `var/storage`, `settings/override`, `settings/siteaccess`, the key files and the excluded
  caches.
- The trainer checks one learner's archive listing on the projector, without showing file contents.

## Quiz

1. Why must database and files be taken at the same moment?
2. Which file, if lost, invalidates every link in every mail already sent?
   A. `site.ini.append.php` B. the e-mail preferences' `TokenSecret` C. `var/autoload` D. a cache file
3. Name three things to leave out of a backup.
4. Why must a backup that contains `settings/override/` be encrypted and restricted?
5. When is maintenance mode not needed for a consistent database backup?

<details>
<summary>Answers</summary>

1. An image in the database but not on disk shows as broken; a file without its database row is dead weight.
2. **B.** It is in `settings/override/mailpreferences.ini.append.php`
   ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).
3. Any three of: caches, `var/autoload/`, `vendor/`, Velocity's logs and pid files, `var/tmp/`.
4. It holds the database password and other credentials; the backup is as sensitive as the server.
5. On SQLite and MySQL with InnoDB the database backup alone is consistent; maintenance mode makes database and files
   agree.

</details>

## Further reading

- [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)
- [Database backups per engine](../../../hosting/operations/database-backups-per-engine.md)
- [git_manager](../../../../features/6.0/extensions/git_manager.md): backups from the administration.
- [Storage layout](../../../hosting/operations/storage-layout.md): where each site keeps its files.

## Related pages

- [Module 6: Backups and recovery](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
