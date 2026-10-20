---
title: Storage layout
audience: hosting, admin
category: hosting
order: 20
summary: What an Exponential installation keeps on disk, which directories must be writable, which hold customer data and which can be thrown away, and how a multi-site installation spreads logs and caches.
keywords: storage, var, VarDir, CacheDir, LogDir, StorageDir, file system, disk, permissions, writable, cache, logs, multi-site, tmpfs, open_basedir
type: help
---
# Storage layout

This page is for system administrators who size disks, write backup jobs and set file permissions for hosted
Exponential sites. It answers: what is in the installation directory, where uploaded files, caches and logs go, which
of them are customer data that must be backed up, which can be regenerated, and which settings move them.

Every path and setting below is from `settings/site.ini [FileSettings]` and the installation book; follow the links to
check them.

## Contents

1. [The installation directory at a glance](#the-installation-directory-at-a-glance)
2. [The var directory](#the-var-directory)
3. [The settings that place files](#the-settings-that-place-files)
4. [What must be writable](#what-must-be-writable)
5. [Several sites in one installation](#several-sites-in-one-installation)
6. [What to watch](#what-to-watch)
7. [Checklist](#checklist)

## The installation directory at a glance

| Path | What it is | Writable by PHP | Back up |
|---|---|---|---|
| `index.php`, `index_rest.php`, `index_treemenu.php` | the front controllers | no | comes with the code |
| `kernel/`, `lib/`, `bin/`, `share/`, `update/` | the kernel, libraries, scripts, translations, database updates | no | comes with the code |
| `vendor/` | the Composer packages | no (Composer writes it) | rebuilt by Composer from `composer.lock` |
| `extension/` | extensions, from Composer or written by hand | only while packages are installed from the administration | yes, when not in a repository |
| `design/` | designs | only while packages are installed from the administration | yes, when not in a repository |
| `settings/` | shipped settings (`settings/*.ini`) | yes, when administrators edit settings in the browser | `settings/override/` and `settings/siteaccess/` always |
| `var/` | everything the running site writes | yes | partly, see below |
| `config.php`, `config.env.php` | PHP-level configuration, the environment name | no | yes, when present |

`config.env.php` names the environment of a machine (`EXP_ENV`), and `.gitignore` keeps it out of version control
([Settings per environment](../../../features/6.0/environment-settings.md)).

## The var directory

`var/` is the one tree the running site writes. Its layout, with the shipped settings:

| Path | Holds | Customer data? |
|---|---|---|
| `var/storage/` and `var/<site>/storage/` | uploaded images and files and their image variations (`original/`, `reference/`, `variations/`) | **yes** |
| `var/storage/sqlite3/` | the database file of a SQLite site, with its `-wal` and `-shm` files | **yes** (back it up with the database tool, never by copying) |
| `var/cache/`, `var/<site>/cache/` | compiled templates, INI cache, view cache, template blocks, translations, packed scripts and styles | no: regenerated |
| `var/<site>/cache/exphttpcache/` | the HTTP cache | no: regenerated |
| `var/log/`, `var/<site>/log/` | the kernel's logs | operational, optional in a backup |
| `var/<site>/log/audit/` | the audit trail (`audit.ini [AuditSettings] LogDir=log/audit`, inside the var directory) | **yes**: a record you may have to produce |
| `var/autoload/` | generated class maps | no: `php bin/php/ezpgenerateautoloads.php -e` |
| `var/vc/` | Exponential Velocity: logs, pid files, its own configuration when `/etc/vc` is not used | no, except a configuration kept here |
| `var/log/mail/` | mails written by the file transport | no |
| `var/<site>/cronjobs/` | output and history of jobs started from **Setup > Cronjobs** | no |
| `var/maintenance.json` | present only while maintenance mode is on | no |
| `var/tmp/` | temporary work, for example the HTTP cache moved aside by maintenance mode | no |

`<site>` stands for the site's var directory when its siteaccesses set `VarDir=var/<site>`; many installations keep
everything in `var/` alone. The cronjob paths follow the var directory of the administration siteaccess
([Cronjobs, section 14](../../../guides/cronjobs.md#14-logs-and-the-files-the-page-keeps)); the Velocity tree is
described in [8.3.12 Logs](../../../install/08-serving-the-site.md#8312-logs).

Files under `storage/` are spread over subdirectories by the first characters of their name (`[FileSettings]
DirDepth=3`), so no single directory grows too large.

> **Note**
> Caches can be deleted at any time; the site rebuilds them on the next requests, at the cost of slower first pages.
> `php bin/php/ezcache.php --clear-all` is the supported way, because it also handles the expiry timestamps
> ([Caches](../../../guides/caches.md)).

## The settings that place files

All in `site.ini [FileSettings]`; set them in `settings/override/site.ini.append.php` or per siteaccess.

| Setting | Shipped | Meaning |
|---|---|---|
| `VarDir` | `var` | the root of everything written; only relative paths are supported |
| `StorageDir` | `storage` | uploaded files, below `VarDir` |
| `CacheDir` | `cache` | caches, below `VarDir`; an absolute path is "not supported" (its public caches would not be served) |
| `LogDir` | `log` | logs, below `VarDir`; an absolute path is supported |
| `TemporaryDir` | `/tmp/` | temporary files of uploads and conversions |
| `DirDepth` | `3` | directory depth in the storage |
| `StorageDirPermissions`, `StorageFilePermissions` | `0777`, `0666` | modes of new storage directories and files |
| `TemporaryPermissions` | `0777` | mode of temporary files |
| `LogFilePermissions` | `0666` | mode of new log files |

The shipped modes are world-writable. `site.ini` itself recommends `0770` and `0660` with the right owner and group,
which is the hardened choice ([8.7.2 One user, or one group](../../../install/08-serving-the-site.md#872-one-user-or-one-group)).

> **Warning**
> `TemporaryDir=/tmp/` lies outside the installation. Under an `open_basedir` restriction (common on shared hosting
> panels) either allow `/tmp/` or point `TemporaryDir` at a directory inside the installation that PHP may write.

## What must be writable

The setup wizard checks the list in `settings/setup.ini [directory_permissions] CheckList`: `design`, `extension`,
`settings` with `settings/override` and `settings/siteaccess`, and `var` with its cache, log, storage and autoload
directories. After installation, `design/` and `extension/` can be made read-only again when nothing installs packages
from the administration ([8.7.1](../../../install/08-serving-the-site.md#871-what-must-be-writable)).

The most common cause of odd failures is **two system users writing the same files**: the PHP-FPM pool, Velocity's
workers, cronjobs and command-line scripts. Run all of them as the site's user, or give them a common group with
setgid directories ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)). The details for a
hosting platform are on [Writable directories and permissions](../basics/writable-directories-and-permissions.md).

## Several sites in one installation

An installation can serve many sites, each with its own `VarDir`. Six settings in `[FileSettings]` decide where their
logs and caches go ([Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md)):

| Setting | Shipped | Effect |
|---|---|---|
| `UseGlobalLogDir` | `enabled` | `disabled` writes each site's debug logs into its own log directory |
| `LogVarDir` | empty | a separate tree for all logs: `var/example` logs to `var_log/example/log` |
| `CacheVarDir` | empty | a separate tree for all caches: `var_cache/example/cache`, for example one memory file system |
| `ExpiryDir` | empty | keeps `expiry.php` out of a cache directory that is emptied at a restart |
| `INICacheDir` | `global` | `site` caches each site's INI files in its own cache directory |
| `AllowedDeletionDirs[]` | empty | extra absolute directories a cache clear may delete in |

The feature page shows a complete example with a `tmpfs` cache for all sites. A cache in memory is fast and is lost
at every reboot; keep `ExpiryDir` on disk so image variations made before a clear are not taken as current again.

## What to watch

- **Disk growth**: storage grows with the customer's uploads; caches grow with traffic and templates; logs are
  rotated (see [Log viewer and rotation](log-viewer-and-rotation.md)). `./console exp:systeminfo --sizes` and
  **Setup > System information > Measure sizes** report the size of `var/` and of each cache directory
  ([System information](../../../guides/system-information.md)).
- **Inodes**: the view cache and image variations are many small files; watch inode use on the file system that holds
  `var/` as well as bytes.
- **Owners**: after any command run as `root`, check the owner of new files in `var/`.

## Checklist

- [ ] `var/` is on a file system with room for caches, logs and a local backup.
- [ ] The PHP-FPM pool, Velocity workers and cronjobs run as one user, or share a group with setgid directories.
- [ ] `StorageDirPermissions` and `StorageFilePermissions` are set to `0770` and `0660` where the owner allows it.
- [ ] `TemporaryDir` is reachable under the account's `open_basedir`.
- [ ] Backups include `var/storage`, the per-site storage, the audit directory and the settings overrides.
- [ ] Caches, `var/autoload` and `var/vc` logs are excluded from backups to save space.

## Related pages

- [Backups: what to back up](backups-what-to-back-up.md)
- [Clustering and shared storage](clustering-and-shared-storage.md)
- [Writable directories and permissions](../basics/writable-directories-and-permissions.md)
- [Sizing memory, CPU and disk](../basics/sizing-memory-cpu-and-disk.md)
- [8.7 File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
