---
title: Logs, caches and operations per site
audience: government, admin
category: public-sector
order: 90
summary: How to run many department sites on one installation without them stepping on each other: logs per site, caches on their own storage, INI cache per site, cronjobs per siteaccess, maintenance mode, and backups.
keywords: multisite operations, logs per site, UseGlobalLogDir, LogVarDir, CacheVarDir, INICacheDir, cronjobs per siteaccess, maintenance mode, backups, VarDir
type: help
---
# Logs, caches and operations per site

This page is for administrators of a multisite installation. Each site can have its own VarDir; the settings below
separate logs and caches per site ([Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md)).

## Contents

1. [Logs per site](#logs-per-site)
2. [Caches per site](#caches-per-site)
3. [Cronjobs](#cronjobs)
4. [Maintenance and backups](#maintenance-and-backups)

## Logs per site

| Setting (`site.ini [FileSettings]`) | Effect |
|---|---|
| `UseGlobalLogDir=disabled` | debug logs and the default logs go to the site's log directory instead of `var/log` |
| `LogDir` (absolute path) | logs on another file system |
| `LogVarDir=var_log` | the logs of every site in `var_log/<site>/log` |

Per-site logs let a department's operator read their own logs, and make retention per site possible.

## Caches per site

| Setting | Effect |
|---|---|
| `CacheVarDir=var_cache` | caches of every site in `var_cache/<site>/cache`, for example on a memory file system |
| `INICacheDir=site` | the INI files of a site are cached in its cache directory; clearing them leaves other sites alone |

Every value is derived again from site.ini when the siteaccess changes, under Velocity and on the command line.

## Cronjobs

Cronjobs run per siteaccess (`php runcronjobs.php -s <siteaccess> <part>`). Parts that work on the whole database
(workflows, notifications, audit) need to run once per installation, not once per site; parts tied to a site's
settings (newsletter, sitemaps) run for that site ([Cronjobs](../../../../guides/cronjobs.md)).

## Maintenance and backups

- Maintenance mode can close the installation for updates ([Maintenance mode](../../../../guides/maintenance-mode.md)).
- One database and one storage directory mean one backup, and a restore restores all sites; plan restores of single
  sites as content packages or exports instead ([Packages](../../../../guides/packages.md)).

## Related pages

- [Logs, INI cache and expiry timestamps per site](../../../../features/6.0/multi-site-log-and-cache-paths.md)
- [Operating a site](../../../../guides/operating-a-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
