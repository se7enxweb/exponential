---
title: File permissions and ownership
audience: government, admin, hosting
category: public-sector
order: 380
summary: For administrators and hosting teams - the file ownership and permission model that stops a bug in a PHP file from becoming a lasting compromise: one writing user, only var/ and settings/ writable, no world-writable files, secrets 0640, no root workers, and how to check it.
keywords: file permissions, ownership, privilege separation, var, settings, read-only code, world-writable, StorageDirPermissions, StorageFilePermissions, EZP_DIR_MODE_MAX, EZP_FILE_MODE_MAX, root, AllowRootWorkers, TemporaryDir, public sector
type: help
---
# File permissions and ownership

This page is for administrators and hosting teams. The most common path from a bug to a lasting compromise is a file
the attacker can write and the web server later runs or serves. If the PHP process can change only `var/` and
`settings/`, and nothing below the root is executed, a flaw in an extension cannot plant code
([13.11 File permissions and ownership](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)).

## Contents

1. [The model](#the-model)
2. [Hardened modes](#hardened-modes)
3. [Root](#root)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## The model

- **One user writes**: PHP-FPM, Velocity workers, cronjobs and console scripts run as the site's user (or share one
  group).
- **Only what must be written is writable**: `var/` and `settings/` (and, while packages are installed from the
  administration, `design/` and `extension/`). The code (`kernel/`, `lib/`, `vendor/`, `index.php`) can belong to
  another user and be read-only for the site's user.
- **Secrets** in `settings/override`: `0640` or stricter.
- **Temporary files**: `TemporaryDir` (`/tmp/` shipped) is shared by every user of the machine; prefer a directory of
  the site's own below `var/`.
- **Symbolic links** out of the document root: refused by Velocity (`FollowSymlinks=disabled`); with Apache prefer
  `SymLinksIfOwnerMatch`.

## Hardened modes

The shipped `[FileSettings]` values are `0777` and `0666`. Set:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

and fix existing files once (with the site's group on everything):

```bash
find var -type d -exec chmod 2770 {} +
find var -type f -exec chmod 0660 {} +
```

Limits for new files can also be set in `config.php` with `EZP_DIR_MODE_MAX` and `EZP_FILE_MODE_MAX`
([limits for the modes of new files](../../../bc/6.0/file-modes.md)).

## Root

- Exponential's command-line scripts refuse to run as root unless `--allow-root-user` is given.
- Velocity's workers never run as root (`[ServerSettings] AllowRootWorkers=disabled`).
- Run cronjobs and the console as the site's user from that user's crontab. After a documented root task, check that
  files in `var/` still belong to the site's user and group.

## Checking

```bash
find settings extension/*/settings -perm -o+w          # must print nothing
find var -perm -o+w | head                             # should print nothing after hardening
ls -l settings/override
```

## Related pages

- [13.11 File permissions and ownership](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)
- [Chapter 8: file permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)
- [Per-customer users and pools (hosts)](../../hosting/security/per-customer-users-and-pools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
