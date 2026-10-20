---
title: File and settings backups
audience: government, hosting, admin
category: public-sector
order: 40
summary: For operations teams in public bodies that back up the file half of an Exponential site. Which directories hold uploads, settings and the audit trail, how to include per-site storage and the cluster mount, owners and modes on restore, and what an enterprise backup agent must be told.
keywords: file backup, var/storage, settings/override, settings/siteaccess, audit directory, tar, backup agent, exclusions, per-site storage, DFS mount, owners, permissions, restore
type: help
---
# File and settings backups

This page is for operations teams in public bodies who back up the files of an Exponential site, often with an
enterprise backup agent that copies directories on a schedule. It answers which directories to include, which to
exclude, how multi-site and cluster layouts change the list, and what to watch when files come back. The layout is
explained on [Storage layout](../../../hosting/operations/storage-layout.md).

## Contents

1. [The directories to include](#the-directories-to-include)
2. [Exclusions for your backup agent](#exclusions-for-your-backup-agent)
3. [Multi-site and cluster layouts](#multi-site-and-cluster-layouts)
4. [A manual file backup](#a-manual-file-backup)
5. [Owners and modes on restore](#owners-and-modes-on-restore)
6. [Checklist](#checklist)

## The directories to include

| Path | Holds |
|---|---|
| `var/storage/` | uploaded images and files and their variations (`original/`, `reference/`, `variations/`) |
| `var/<site>/storage/` | the same per site, when siteaccesses set `VarDir=var/<site>` |
| `var/<site>/log/audit/` | the audit trail (`audit.ini [AuditSettings] LogDir=log/audit` inside the var directory) and its archives, unless `ArchiveDir` points elsewhere |
| `settings/override/` | settings, credentials, generated keys |
| `settings/siteaccess/` | siteaccess settings |
| `extension/`, `design/` | when your own code is not in version control |
| `config.php`, `config.env.php`, `config.cluster.php` | when present |

A SQLite database in `var/storage/sqlite3/` is a special case: back it up with SQLite's `.backup` first
([Backups per database engine](backups-per-database-engine.md)).

## Exclusions for your backup agent

| Exclude | Why |
|---|---|
| `var/cache/`, `var/<site>/cache/` | regenerated |
| `var/autoload/` | regenerated |
| `var/tmp/` | temporary work |
| `var/vc/*/log/`, `var/vc/*/run/` | Velocity's logs and pid files |
| `var/log/mail/` | test mails of the file transport |
| `vendor/` | rebuilt from `composer.lock` |
| `var/storage/sqlite3/*` (the live files) | copy the `.backup` output instead |

Caches can be many small files; leaving them out shortens backups and restores
([Storage layout](../../../hosting/operations/storage-layout.md)).

## Multi-site and cluster layouts

- **Several sites in one installation**: each can have its own `VarDir`, and `LogVarDir` or `CacheVarDir` can move
  logs and caches to separate trees ([Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md)).
  List every site's storage and audit directory.
- **DFS cluster**: the reference copy of uploaded files is on the NFS export, and their metadata in the `ezdfsfile`
  table. Back up the export and the metadata database together, in the same window as the site's database
  ([Shared storage and the DFS cluster](../availability/shared-storage-and-the-dfs-cluster.md)).
- **Audit archives elsewhere**: when `[AuditArchiveSettings] ArchiveDir` points to another mount, include it.

## A manual file backup

```bash
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess var/site/log/audit
```

Adjust `var/site` to your var directory. Run it in the same window as the database backup
([What to back up](what-to-back-up.md)).

## Owners and modes on restore

Files restored as root may later be unwritable for the web server, and the site then fails in odd places. After
restoring, give the files to the site's user (or the shared group), then clear the caches
([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)):

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
```

Audit archive files are written with mode `0440` and directories `0750`; keep those modes, they protect the evidence.

## Checklist

- [ ] Every site's storage and audit directory is in the backup set.
- [ ] Caches, autoloads, temporary files and `vendor/` are excluded.
- [ ] SQLite is backed up through `.backup`, not by copying.
- [ ] With a cluster: the NFS export and the metadata database are in the same window.
- [ ] The restore procedure fixes owners and clears caches.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Writable directories and permissions](../../../hosting/basics/writable-directories-and-permissions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
