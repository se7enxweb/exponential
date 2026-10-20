---
title: What to back up
audience: government, hosting, admin
category: public-sector
order: 20
summary: For public-sector IT teams that define the backup of an Exponential site. The two halves of every backup (database and files), the files that hold keys which cannot be recreated, what can be left out, and how to take a consistent pair.
keywords: backup, what to back up, database, var/storage, settings/override, settings/siteaccess, audit trail, keys, consistent backup, maintenance mode, records, continuity
type: help
---
# What to back up

This page is for IT and records teams in public bodies who define the backup of an Exponential site. It answers which
data the site cannot live without, which small files hold keys that cannot be recreated, what can be left out because
the site rebuilds it, and how to take a database and files that agree. The hosting page with the same facts is
[Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md); the installation book's reference
is [10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore).

## Contents

1. [Two halves](#two-halves)
2. [The files](#the-files)
3. [Keys that cannot be recreated](#keys-that-cannot-be-recreated)
4. [What to leave out](#what-to-leave-out)
5. [A consistent pair](#a-consistent-pair)
6. [Why this matters for a public body](#why-this-matters-for-a-public-body)
7. [Checklist](#checklist)

## Two halves

Exponential keeps its state in the **database** (content, versions, users, roles, settings stored in tables) and in
**files** (uploads, settings, the audit trail). "A backup is the database and the files, taken at the same moment"
([10.9](../../../../install/10-after-installing.md#109-backups-and-restore)). The database half depends on the engine:
[Backups per database engine](backups-per-database-engine.md).

## The files

| Path | Why |
|---|---|
| `var/storage/`, `var/<site>/storage/` | uploaded images and files and their variations |
| `var/storage/sqlite3/` | the database of a SQLite site; take it with SQLite's own backup, never by copying a busy file |
| `settings/override/` | the installation's settings, credentials and generated keys |
| `settings/siteaccess/` | the settings of each siteaccess |
| `var/<site>/log/audit/` | the audit trail: live files, archives and their signed manifests |
| your own extensions and designs | when they are not in version control |
| `config.php`, `config.cluster.php`, `config.env.php` | when present |
| `composer.json`, `composer.lock` | rebuild `vendor/` at the same versions |
| `var/log/` | optional; useful for investigating an incident later |

Details on each: [File and settings backups](file-and-settings-backups.md).

## Keys that cannot be recreated

| File | Holds | If lost |
|---|---|---|
| `settings/override/audit.ini.append.php` | signing and pseudonym keys of the audit trail | existing audit records can no longer be verified as intact |
| `settings/override/mailpreferences.ini.append.php` | the site secret of the e-mail preferences | every unsubscribe link already sent stops working |
| Velocity's configuration tree | certificate private keys | HTTPS must be set up again |

See [Keys and secrets in backups](keys-and-secrets-in-backups.md).

## What to leave out

| Path | Why |
|---|---|
| `var/cache/`, `var/<site>/cache/` | regenerated |
| `var/autoload/` | rebuilt by `php bin/php/ezpgenerateautoloads.php -e` |
| `vendor/` | rebuilt from `composer.lock` |
| `var/vc/*/log/`, `var/vc/*/run/` | Velocity's logs and pid files |
| `var/tmp/`, `var/log/mail/` | temporary work, test mails |

The kernel and shipped settings come with the release. Back them up only if someone changed them in place; **Setup >
Upgrade check** lists files that differ from the release ([The upgrade check](../../../../guides/upgrade-check.md)).

## A consistent pair

Maintenance mode makes database and files agree, because nobody writes between the two:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# 1. the database, with the tool of its engine
# 2. the files
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

Add the per-site storage, the audit directory and your extensions as your layout needs. On SQLite and MySQL with
InnoDB the database backup alone is consistent without maintenance mode; a site that cannot go offline every night
can take the database online and the files right after, accepting a small window in which a new upload may be in one
half only ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)).

## Why this matters for a public body

- **Continuity duties.** A restore must bring back the service, not only the text: forms, accounts, roles and
  settings are part of it.
- **Records and evidence.** The audit trail can be evidence of who published what; losing its keys makes it
  unverifiable. Coordinate with your records officer on how long backups and audit archives are kept
  ([Audit trail archives and backups](audit-trail-archives-and-backups.md)).
- **Personal data.** Backups contain accounts and form entries; they fall under the same protection rules as the live
  site ([Offsite and encrypted copies](offsite-and-encrypted-copies.md)).

## Checklist

- [ ] The database and the files listed above are in every backup.
- [ ] The key files are included and protected.
- [ ] Caches, autoloads and `vendor/` are left out.
- [ ] Database and files are taken in one window, or the small gap is accepted in writing.
- [ ] A restore of the backup has been tested ([Restore testing](restore-testing.md)).

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Storage layout](../../../hosting/operations/storage-layout.md)
- [Data location and what is stored where](../hosting/data-location-and-what-is-stored-where.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
