---
title: Backups and recovery
audience: government, admin, hosting
category: public-sector
order: 390
summary: For public bodies that must be able to restore their site after ransomware, a failed update or a mistaken deletion - what an Exponential backup contains, how to take a consistent one with maintenance mode, the secrets it holds, restore testing, and recovery of single items without a full restore.
keywords: backup, restore, recovery, ransomware, business continuity, maintenance mode, database dump, var/storage, settings/override, offline copy, restore test, trash, versions, recovery time, public sector
type: help
---
# Backups and recovery

This page is for administrators and hosting teams of public sites, and the project managers who set recovery targets.
A backup is the database and the files, taken at the same moment
([10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore)).

## Contents

1. [What to back up](#what-to-back-up)
2. [A consistent backup](#a-consistent-backup)
3. [Protecting the backups](#protecting-the-backups)
4. [Restore tests](#restore-tests)
5. [Smaller recoveries](#smaller-recoveries)
6. [Related pages](#related-pages)

## What to back up

| Path | Why |
|---|---|
| the database | content, users, roles, settings stored in the database |
| `var/storage`, `var/<site>/storage` | uploaded images and files |
| `settings/override`, `settings/siteaccess` | the installation's settings, **including secrets** (database password, audit keys, mail preference secret) |
| your own extensions and designs | if they are not in a repository |
| `var/log` (optional) | later investigation; the audit trail lives under `var/<site>/log/audit` |

Caches need no backup.

## A consistent backup

1. Maintenance mode, so nothing is written between the database and the file backup:
   `php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user` (`--allow-admin` keeps the
   administration reachable).
2. The database with its engine's tool (`mysqldump --single-transaction`, `pg_dump -Fc`, the SQLite online backup, ...).
3. The files: `tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess`.
4. `php bin/php/maintenance.php off --allow-root-user`.

On SQLite and MySQL with InnoDB the database backup is consistent without maintenance mode; maintenance mode makes
database and files agree.

## Protecting the backups

- **Encrypt** them: they contain the database password and the audit keys.
- Keep at least one copy **off the machine** and one **offline**, so ransomware on the server cannot reach it
  ([Security hardening: Backups and recovery](../../security-hardening.md#backups-and-recovery)).
- Keep several generations, as long as your records and privacy rules allow (backups contain personal data too).
- Keep the break-glass procedure for the administrator account next to the restore procedure.

## Restore tests

Restore to a test machine regularly (for example each quarter):

1. maintenance on; under Velocity stop the server;
2. restore the database and unpack the files; fix owner and mode;
3. clear caches and start;
4. check: front page, sign-in, a content page with images, `./console exp:audit verify --allow-root-user`.

Record the date, the backup used, the time it took and problems found. The time is your real recovery time.

## Smaller recoveries

Not every loss needs a full restore:

| Lost | Recover with |
|---|---|
| a page moved to the trash | **Restore** in the trash (`content/trash`) ([The trash view](../../../bc/6.0/trash.md)) |
| an earlier text of a page | **Make a new draft** from the old version on the versions page ([Versions of an object](../../../guides/content-history.md)) |
| a user account removed | only a restore: user objects never go to the trash; prefer **Deactivate and move** for leavers |
| an archived audit day | `./console exp:audit restore --channel=<channel> --date=<YYYY-MM-DD> --allow-root-user` |

## Related pages

- [10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore)
- [Runbook: restore from backup](../../hosting/runbooks/restore-from-backup.md)
- [Backups and restores for website creators](../../creators/website-creators/backups-and-restores.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
