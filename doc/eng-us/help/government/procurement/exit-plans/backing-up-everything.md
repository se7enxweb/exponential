---
title: Backing up everything you need to leave
audience: government, project-manager, admin, hosting
category: public-sector
order: 50
summary: What a complete backup of an Exponential site contains, so a public body can hold its own copy and leave a supplier at any time - database and files taken together, settings, generated keys, code, audit records - and how to check that your copy is complete.
keywords: backup, own copy, escrow, data custody, database and files, settings, keys, audit trail, restore test, exit, continuity
type: help
---
# Backing up everything you need to leave

This page is for the project manager who wants the public body to hold its own complete copy of its site, and for
the administrator who checks it. A backup kept only by the supplier does not help when the supplier is the problem.
The contents come from [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) and
[10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore).

## Contents
1. [A backup is two halves](#a-backup-is-two-halves)
2. [The complete list](#the-complete-list)
3. [Keys that cannot be recreated](#keys-that-cannot-be-recreated)
4. [Taking a consistent copy](#taking-a-consistent-copy)
5. [Holding your own copy](#holding-your-own-copy)
6. [Checking the copy](#checking-the-copy)

## A backup is two halves

"A backup is the database and the files, taken at the same moment." The database holds content, users, roles and
versions; the files hold uploads, settings and code. One without the other cannot rebuild the site.

## The complete list

| Item | Path or source | Why |
|---|---|---|
| Database | the engine's dump tool | content, users, roles, versions |
| Uploaded files | `var/storage/`, `var/<site>/storage/` | images, documents, media |
| SQLite database file (if SQLite) | `var/storage/sqlite3/` via SQLite's online backup | the database itself |
| Installation settings | `settings/override/` | credentials and generated keys |
| Siteaccess settings | `settings/siteaccess/` | each site's settings |
| Custom extensions and designs | their Git repositories | your code |
| PHP-level configuration | `config.php`, `config.cluster.php`, `config.env.php` when present | environment |
| Audit trail | `var/<site>/log/audit/` with archives and signed manifests | evidence of who did what |
| Composer files | `composer.json`, `composer.lock` | rebuild `vendor/` at the same versions |
| Velocity configuration (if Velocity) | `/etc/vc` or `var/vc/qbix/etc` | engine settings and certificate keys |

Leave out caches (`var/cache/`), class maps (`var/autoload/`), `vendor/` and temporary files: they are rebuilt.

## Keys that cannot be recreated

| File | Holds | If lost |
|---|---|---|
| `settings/override/audit.ini.append.php` | audit signing and pseudonym keys | existing audit records can no longer be verified |
| `settings/override/mailpreferences.ini.append.php` | `[SecretSettings] TokenSecret` | every unsubscribe link already sent stops working |
| Velocity `ssl/` directory | certificate private keys | HTTPS on Velocity must be set up again |

See [Keys and secrets handover](keys-and-secrets-handover.md) for how to transfer them safely.

## Taking a consistent copy

Maintenance mode keeps database and files in step:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# database: the engine's dump tool
tar czf files-<date>.tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

On SQLite and MySQL with InnoDB the database dump alone is consistent without maintenance mode; maintenance mode makes
database and files agree.

## Holding your own copy

- Agree in the contract that the supplier delivers a complete backup to you at a fixed interval and on request.
- Store it in a place the supplier cannot reach or delete, encrypted, with access limited to named roles: it contains
  credentials and personal data.
- Keep it under your retention rules; delete old copies as those rules say.

## Checking the copy

- [ ] Every item of the list above is present.
- [ ] The dump restores on a test machine and the site starts ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)).
- [ ] Images on the restored site display (files and database agree).
- [ ] `./console exp:audit verify` reports the chain intact on the restored copy.
- [ ] The date of the last successful check is recorded in the exit plan.

## Related pages
- [Exit plans: overview](README.md)
- [Data export formats](data-export-formats.md)
- [Backups and restores for site builders](../../../creators/website-creators/backups-and-restores.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
