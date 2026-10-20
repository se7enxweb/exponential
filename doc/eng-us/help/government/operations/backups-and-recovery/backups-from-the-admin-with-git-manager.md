---
title: Backups from the administration with git_manager
audience: government, admin, hosting
category: public-sector
order: 70
summary: For site administrators in public bodies who have no shell on the server. How the git_manager extension's Setup > Backup page creates database and file backups, encrypts them, and makes sanitized dumps; its limits (MySQL and MariaDB only, stored on the same server) and how it fits an official backup policy.
keywords: git_manager, backup manager, Setup > Backup, captions, encrypted backup, GnuPG, AES-256, sanitized dump, AGPL dump, backup-create.php, mysqldump, MaxBackups, no shell
type: help
---
# Backups from the administration with git_manager

This page is for site administrators in public bodies who need a backup before a change but have no shell on the
server, for example because a central IT department runs the machines. It answers what the `git_manager` extension's
backup page does, how to use it, and why it complements but does not replace the organization's backup system. The
feature page is [git_manager](../../../../features/6.0/extensions/git_manager.md).

## Contents

1. [What it is](#what-it-is)
2. [Making a backup](#making-a-backup)
3. [Encryption and sanitized dumps](#encryption-and-sanitized-dumps)
4. [Limits](#limits)
5. [Fitting it into a backup policy](#fitting-it-into-a-backup-policy)
6. [Checklist](#checklist)

## What it is

`git_manager` is an extension (required by the installation's `composer.json`) that adds two tools to the
administration: a git dashboard and a **backup manager** at **Setup > Backup** (`/git_manager/backup`). Backups are
timestamped sets of archives of the database and files. It needs PHP 8.1 or later, GnuPG for encrypted backups and
`mysqldump` for database backups.

| Type | Creates | Use for |
|---|---|---|
| Full site backup | SQL dump, `var/` archive and site files (`extension/`, `settings/`, `config.php`) | rebuilding the site on a new server |
| Database and files | SQL dump and `var/` archive | the usual backup before a change |
| Database only | SQL dump | before database changes |
| Files only | `var/` archive | before bulk file changes |

## Making a backup

1. Open **Setup > Backup**.
2. Choose the type, add a description such as "Before upgrade, change request 123".
3. Optionally tick **Encrypt the archives (GPG, AES-256)** and enter a passphrase.
4. Create it. It appears under **Existing backups** with its archives to download.

The page shows a warning when the newest backup is old (orange after 7 days) and an age indicator on each backup.
The same from the command line:

```bash
php extension/git_manager/bin/php/backup-create.php full -d "Before upgrade"
php extension/git_manager/bin/php/backup-list.php -v -s
```

Access needs the extension's `backup` policy function; give it only to the people who take backups.

## Encryption and sanitized dumps

- **Encryption** uses GnuPG with AES-256. "There is no recovery for a lost passphrase." Keep the passphrase in your
  secrets store, not with the backup. On the command line pass it with `--passphrase-file` or the environment variable
  `GIT_MANAGER_BACKUP_PASSPHRASE`, not with `-p`, which puts it in the process list.
- **Sanitized dumps**: the option on the page (labeled as an AGPL compatible dump) writes an extra dump with password
  hashes, e-mail addresses, keys, credentials, paths and IP addresses removed, and tables with secrets or personal data
  left out. Useful when a developer or a supplier needs a realistic database without personal data; review it before
  sharing, as you would any data release.
- Files are created owner-only, the backup folder denies web access, and downloads go through an authenticated
  controller.

## Limits

- **MySQL and MariaDB only** for the database part, because it uses `mysqldump`. Sites on SQLite, PostgreSQL, MongoDB
  or Oracle need the engine's own tool ([Backups per database engine](backups-per-database-engine.md)).
- **Stored on the same server**: backups go to `var/site/backups/captions` (`BackupPath`). A backup on the same disk
  does not survive the loss of that server. Download the archives to approved storage, or let your backup system
  copy the directory.
- **Number kept**: `MaxBackups` (36 shipped) removes the oldest beyond that.
- **No schedule of its own** is described in its documentation; scheduled backups belong to your backup system.

## Fitting it into a backup policy

Use `git_manager` for what it does well: a quick, documented restore point right before a change, taken by the person
who makes the change. Reference the backup's timestamp in the change request. Keep the organization's scheduled,
off-site backups as the backbone ([Backup schedules and retention](backup-schedules-and-retention.md)).

## Checklist

- [ ] The site's database is MySQL or MariaDB, or another method covers the database.
- [ ] Only backup takers hold the `backup` policy function.
- [ ] Encrypted backups have their passphrase in the secrets store.
- [ ] Archives are copied off the server.
- [ ] Sanitized dumps are reviewed before they are shared.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
