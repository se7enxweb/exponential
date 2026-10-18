---
title: "Backups: what to back up"
audience: hosting, admin
category: hosting
order: 50
featured: true
summary: The complete list of what an Exponential backup must contain (the database, uploaded files, settings, keys and the audit trail), what can be left out because it is regenerated, and how to take database and files at the same moment.
keywords: backup, backups, what to back up, database, files, var/storage, settings/override, keys, secrets, audit trail, maintenance mode, consistency, git_manager
type: help
---
# Backups: what to back up

This page is for hosting companies that design a backup service for Exponential sites and for the administrators who
run it. It answers: which data an Exponential site cannot live without, which files hold keys that cannot be
recreated, what can be left out because the site rebuilds it, and how to take a backup whose database and files agree.

The facts come from [10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore),
[Backup and restore](../../database-support.md#backup-and-restore) and the feature pages linked below.

## Contents

1. [A backup is two halves](#a-backup-is-two-halves)
2. [The files to back up](#the-files-to-back-up)
3. [Keys and secrets that cannot be recreated](#keys-and-secrets-that-cannot-be-recreated)
4. [What to leave out](#what-to-leave-out)
5. [Taking database and files together](#taking-database-and-files-together)
6. [Storing the backup](#storing-the-backup)
7. [Backups from the administration](#backups-from-the-administration)
8. [Checklist](#checklist)

## A backup is two halves

Exponential keeps its state in two places: the **database** (content, users, roles, versions, settings stored in
tables) and the **files** (uploaded images and documents, the installation's settings, your own extensions and
designs). "A backup is the database and the files, taken at the same moment"
([10.9](../../../install/10-after-installing.md#109-backups-and-restore)). An image that exists in the database but not
on disk shows as a broken image; a file without its database row is dead weight.

The database half depends on the engine; the commands for each of the six supported databases are on
[Database backups per engine](database-backups-per-engine.md).

## The files to back up

| Path | Why it matters |
|---|---|
| `var/storage/` | uploaded images and files and their variations |
| `var/<site>/storage/` | the same, when a site has its own var directory |
| `var/storage/sqlite3/` | the database file of a SQLite site; take it with SQLite's online backup, never by copying a busy file |
| `settings/override/` | the installation's settings, including database credentials and the generated keys below |
| `settings/siteaccess/` | the settings of each siteaccess |
| your own extensions and designs | when they are not kept in a repository |
| `config.php`, `config.cluster.php`, `config.env.php` | PHP-level configuration, cluster constants and the environment name, when present |
| `var/<site>/log/audit/` | the audit trail: live files, archives and their signed manifests |
| `var/log/` | optional: useful when an incident has to be investigated later |

When the customer's installation is managed with Composer, also keep the project's `composer.json` and
`composer.lock`: they are what rebuilds `vendor/` at the same versions.

> **Warning**
> `settings/override/` holds the database password and other credentials. A backup that contains it is as sensitive
> as the server itself: encrypt it, restrict who can read it, and never hand it to a customer without checking what
> it contains.

## Keys and secrets that cannot be recreated

Some files hold keys the software generated on first use. If they are lost, things break that no restore of content
can repair:

| File | Holds | If it is lost |
|---|---|---|
| `settings/override/audit.ini.append.php` | the signing and pseudonym keys of the audit trail | the existing records can no longer be verified as intact ([The audit trail](../../../features/6.0/audit-trail.md)) |
| `settings/override/mailpreferences.ini.append.php` | `[SecretSettings] TokenSecret`, the site secret of the e-mail preferences | "a new secret invalidates every link in every mail already sent, and every entry of the suppression list stops matching" ([E-mail preferences](../../../guides/mail-preferences-administrator.md)) |
| Velocity's configuration tree (`/etc/vc`, or `var/vc/qbix/etc` when `/etc/vc` is not used) | site configuration and, in its `ssl/` directory, private keys of certificates (mode `0600`) | HTTPS on Velocity has to be set up again ([8.3.10](../../../install/08-serving-the-site.md#8310-the-configuration-tree-etcvc-debian-style)) |

These files are small. Include them in every backup, and keep them as private as the site's credentials.

## What to leave out

These are regenerated and only cost space and time in a backup:

| Path | Why it can go |
|---|---|
| `var/cache/`, `var/<site>/cache/` | caches; "regenerated" ([10.9](../../../install/10-after-installing.md#109-backups-and-restore)) |
| `var/autoload/` | class maps; `php bin/php/ezpgenerateautoloads.php -e` rebuilds them |
| `vendor/` | Composer packages; rebuilt from `composer.lock` |
| `var/vc/*/log/`, `var/vc/*/run/` | Velocity's logs and pid files |
| `var/log/mail/` | mails written by the file transport on a test site |
| `var/tmp/` | temporary work |

The kernel, `lib/`, `bin/` and the shipped `settings/*.ini` come with the release and can be fetched again at the same
version. Back them up only when someone changed them in place; Setup > Upgrade check lists files that differ from the
release ([The upgrade check](../../../guides/upgrade-check.md)).

## Taking database and files together

Maintenance mode makes the pair consistent: no visitor or editor writes between the database dump and the file copy
([10.9, procedure](../../../install/10-after-installing.md#procedure)).

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# 1. the database, with the tool of its engine (see Database backups per engine)
# 2. the files
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

Add `var/<site>/storage`, the audit directory and your own extensions to the `tar` line as your layout needs.

> **Note**
> Maintenance mode is not required for the database alone: "On SQLite and MySQL with InnoDB the database backup is
> consistent without maintenance mode; maintenance mode makes the database and the files agree." A host that cannot
> take customer sites offline every night can take the database online and the files right after it, and accept a
> small window in which a new upload may be in one half only.

## Storing the backup

From the installation book: "Store backups away from the server, and keep several generations." How many and for how
long is a product decision; [Backup schedules and retention](backup-schedules-and-retention.md) lays out the options.

## Backups from the administration

For customers without a shell, the extension `git_manager` adds **Setup > Backup** (`/git_manager/backup`):
timestamped backups ("captions") of the database and files, optional encryption with GnuPG, and sanitized SQL dumps.
It needs `mysqldump` for the database part, so it covers MySQL and MariaDB sites
([git_manager](../../../features/6.0/extensions/git_manager.md)). A host-side backup of every engine remains the
host's own job.

## Checklist

- [ ] The database is backed up with the tool of its engine, not by copying data files of a running server.
- [ ] `var/storage` (and every `var/<site>/storage`) is in the backup.
- [ ] `settings/override/` and `settings/siteaccess/` are in the backup, encrypted at rest.
- [ ] The audit and mail-preference key files are in every backup.
- [ ] Caches, `var/autoload`, `vendor/` and Velocity's logs are excluded.
- [ ] Database and files are taken in one window, in maintenance mode where the customer allows it.
- [ ] Backups leave the server, and several generations are kept.
- [ ] A restore has been tested ([Restore and restore tests](restore-and-restore-tests.md)).

## Related pages

- [Database backups per engine](database-backups-per-engine.md)
- [Backup schedules and retention](backup-schedules-and-retention.md)
- [Restore and restore tests](restore-and-restore-tests.md)
- [Restore from backup](../runbooks/restore-from-backup.md) (runbook)
- [Storage layout](storage-layout.md)
- [Security hardening: backups and recovery](../../security-hardening.md#backups-and-recovery)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
