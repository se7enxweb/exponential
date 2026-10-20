---
title: Writable directories and permissions
audience: hosting, admin
category: hosting
order: 160
summary: Which directories of an Exponential installation must be writable and by whom, the one-user and one-group setups, the hardened file modes, settings files with secrets, Velocity's own files, and how to repair ownership after a command run as root.
keywords: permissions, file permissions, ownership, writable directories, var, settings, design, extension, chmod, chown, setgid, umask, StorageDirPermissions, StorageFilePermissions, TemporaryPermissions, LogFilePermissions, PHP-FPM user, Velocity User Group, AllowRootWorkers, root, hosting, multi-tenant
type: help
---
# Writable directories and permissions

This page is for system administrators and support staff at a hosting company. It answers: what an Exponential
installation must be able to write, which user should own it, how to set it up when several users are involved, which
file modes to use instead of the shipped world-writable ones, and how to repair a site that a root command broke.
Permission problems are among the most common support cases with any PHP application; on Exponential they almost
always come from two users writing the same files. Sources:
[chapter 8.7](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership) and
[chapter 13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership).

## Contents

1. [What must be writable](#what-must-be-writable)
2. [Who writes](#who-writes)
3. [Setup 1: one user](#setup-1-one-user)
4. [Setup 2: one group](#setup-2-one-group)
5. [Hardened file modes](#hardened-file-modes)
6. [Settings files with secrets](#settings-files-with-secrets)
7. [Velocity's own files](#velocitys-own-files)
8. [Repairing a site](#repairing-a-site)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## What must be writable

The setup wizard's critical test `directory_permissions` checks the list in `settings/setup.ini`,
`[directory_permissions] CheckList`: `design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`,
`settings/siteaccess/admin`, `var` and its cache, log, storage and autoload subdirectories.

| Directory | Why | Must stay writable |
|---|---|---|
| `var/` and everything below | caches, compiled templates, logs, uploaded files, image variations, autoload arrays, the SQLite database | always |
| `settings/`, `settings/override`, `settings/siteaccess` | the installers and the administration write settings | while administrators change settings in the browser |
| `design/`, `extension/` | the setup wizard and package installation write there | only while packages are installed from the administration |

Everything else (`kernel/`, `lib/`, `vendor/`, `index.php`) only needs to be readable. A code tree the site's PHP
cannot change is a strong defense: a flaw in an extension then cannot plant code
([chapter 13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)).

> **Tip**
> Sites deployed from version control can lock their settings with `[ConfigurationSettings] ManagedByDeployment=enabled`,
> which stops the administration from writing settings and templates on the server
> ([ManagedByDeployment](../../../features/6.0/managed-by-deployment.md)). Settings changes then arrive only through
> the deployment, never through the browser.

## Who writes

Four kinds of process write `var/` and must agree on ownership:

- the web server's PHP (the PHP-FPM pool user, or mod_php's Apache user);
- Velocity's workers, if Velocity serves the site;
- cronjobs (`runcronjobs.php`);
- console commands and Composer, run by an administrator.

A cache file written by `root` from the command line cannot be replaced by the site user afterwards, and the site
breaks in odd places.

## Setup 1: one user

The recommended setup ([chapter 8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)): **run
everything as the site's user.**

- PHP-FPM pool: `user = <customer>`.
- Velocity: `settings/velocity.ini [ServerSettings] User=<customer>`, `Group=<customer>`, or leave them empty and let
  the document root's owner decide. Velocity started as root is fine: only its parent process stays root, and root
  workers are refused unless `AllowRootWorkers=enabled` (shipped `disabled`).
- Cron: the site user's crontab (`crontab -u <customer> -e`), or `sudo -u <customer> php runcronjobs.php`.
- Console and Composer: as the site user (`sudo -u <customer> php bin/php/console ...`).

For a hosting company with one system user per customer this is also the natural isolation model; see
[Per-customer users and pools](../security/per-customer-users-and-pools.md).

## Setup 2: one group

When the users must differ (for example the web server's user and a deploy user), give them a common group, make the
writable directories group-writable and setgid, so new files inherit the group:

```bash
sudo chgrp -R <customer> design extension settings var
sudo find var settings -type d -exec chmod 2775 {} +
```

and give every process a umask of `0002`. The SQLite directory needs the same treatment
([SQLite for hosts](sqlite-for-hosts.md#files-and-permissions)).

When Apache with PHP-FPM and Velocity serve the same installation side by side, they share `var/`, the caches and the
sessions: give them the same user, or this group setup.

## Hardened file modes

Exponential creates files with the modes in `settings/site.ini`, `[FileSettings]`. The shipped values are `0777` and
`0666`, world-writable; `site.ini` itself recommends `0770` and `0660` with the right user and group:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

and fix existing files once, with the site's group on everything:

```bash
find var -type d -exec chmod 2770 {} +
find var -type f -exec chmod 0660 {} +
```

`settings/image.ini [FileSettings]` has its own `DirPermissions` and `ImagePermissions` (shipped `0777` and `0666`)
for image directories and files; set them the same way. `config.php` can cap every mode with `EZP_DIR_MODE_MAX` and
`EZP_FILE_MODE_MAX` ([file modes](../../../bc/6.0/file-modes.md)).

> **Warning**
> On a server with more than one customer, world-writable files let one customer's PHP change another customer's
> site. Set the hardened modes on every installation you host.

**`TemporaryDir`** (`/tmp/` shipped) is shared with every user of the machine; consider a directory of the site's own,
for example below `var/`.

## Settings files with secrets

`settings/override/` and `settings/siteaccess/` hold the database credentials. Keep them `0640` or stricter. Since
6.0.15 a save from the administration or `exp:ini` keeps the file's mode, owner and group, and creates a new settings
file `0640`; check once that nothing is left world-writable
([INI save file permissions](../../../bc/6.0/ini-save-file-permissions.md)):

```bash
find settings extension/*/settings -perm -o+w
```

It must print nothing. Make sure the web server never hands these files out
([chapter 13.2](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).

## Velocity's own files

| Path | Written by | Mode |
|---|---|---|
| `/etc/vc/sites-available/<site>.conf` | `exp:velocity` (root) | `0600` |
| private keys in the configuration's `ssl/` directory | the server | `0600` |
| `var/vc/qbix/log/` | the server | `[LogSettings] FileMode` `0640`, `DirMode` `0750` |
| `var/cache/qbix-reverse/` | the server, for the workers | `[CacheSettings] FileMode` `0640`, `DirMode` `0750` |

The worker user must be able to read the engine's source in `vendor/`
([chapter 8.7.3](../../../install/08-serving-the-site.md#873-velocitys-own-files)).

## Repairing a site

After a command was run as root by mistake, hand the writable tree back to the site's user:

```bash
sudo chown -R <customer>:<customer> design extension settings var
sudo chmod -R ug+rwx design extension settings var
```

then clear the caches as the site's user. The setup wizard's system check prints the exact `chown` and `chmod`
commands for the machine. Do not use `bin/modfix.sh`: it makes directories world-writable.

## Checklist

- [ ] One user per site for PHP-FPM, Velocity workers, cron and console, or one group with setgid directories.
- [ ] `var/` and `settings/` writable by that user; `design/` and `extension/` only while needed.
- [ ] Code (`kernel/`, `lib/`, `vendor/`) not writable by the site's PHP where the plan allows it.
- [ ] Hardened modes in `[FileSettings]` and `image.ini [FileSettings]`.
- [ ] `settings/override` files `0640`; `find settings -perm -o+w` prints nothing.
- [ ] Velocity workers never root.

## Related pages

- [Chapter 8.7: File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)
- [Chapter 13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)
- [Multi-tenant isolation](../security/multi-tenant-isolation.md)
- [Storage layout](../operations/storage-layout.md)
- [Security hardening](../../security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
