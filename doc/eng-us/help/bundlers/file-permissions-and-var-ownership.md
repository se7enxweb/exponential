---
title: File permissions and var/ ownership in a bundle
audience: bundler, hosting
category: bundling
order: 390
summary: Which directories of a bundled installation must be writable and by whom, the one-user and one-group setups, the hardened file modes to ship instead of the world-writable defaults, the SQLite directory, Velocity's workers, and how to build images so root never owns var/.
keywords: permissions, ownership, var/, settings, design, extension, chown, chmod, setgid, umask, StorageDirPermissions, StorageFilePermissions, TemporaryPermissions, LogFilePermissions, root, Velocity User, bundle
type: help
---
# File permissions and var/ ownership in a bundle

This page is for bundlers who set the ownership and modes of the files they ship. "Permission problems are among the
most common support cases with any PHP application; on Exponential they almost always come from two users writing
the same files" ([Writable directories and permissions](../hosting/basics/writable-directories-and-permissions.md)).

## Contents

1. [What must be writable](#what-must-be-writable)
2. [Who writes](#who-writes)
3. [One user or one group](#one-user-or-one-group)
4. [Modes to ship](#modes-to-ship)
5. [Building without root-owned files](#building-without-root-owned-files)
6. [Checklist](#checklist)

## What must be writable

The setup wizard's check `directory_permissions` reads `settings/setup.ini [directory_permissions] CheckList`:
`design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/admin`, `var` and
its cache, log, storage and autoload subdirectories.

| Directory | Must stay writable |
|---|---|
| `var/` and everything below | always |
| `settings/`, `settings/override`, `settings/siteaccess` | while administrators change settings in the browser |
| `design/`, `extension/` | only while packages are installed from the administration |

"Everything else (`kernel/`, `lib/`, `vendor/`, `index.php`) only needs to be readable. A code tree the site's PHP
cannot change is a strong defense."

## Who writes

The web server's PHP, Velocity's workers, cron jobs (`runcronjobs.php`), and console commands and Composer run by an
administrator. "A cache file written by `root` from the command line cannot be replaced by the site user afterwards,
and the site breaks in odd places."

## One user or one group

| Setup | How |
|---|---|
| One user (recommended) | PHP-FPM pool `user = <site user>`; Velocity `[ServerSettings] User` and `Group`; cron in the site user's crontab; console and Composer as the site user |
| One group | a common group, writable directories group-writable and setgid, umask `0002` for every process |

The group setup from the hosting page:

```bash
sudo chgrp -R <site group> design extension settings var
sudo find var settings -type d -exec chmod 2775 {} +
```

For Velocity: "Velocity started as root is fine: only its parent process stays root, and root workers are refused
unless `AllowRootWorkers=enabled` (shipped `disabled`)."

## Modes to ship

"The shipped values are `0777` and `0666`, world-writable; `site.ini` itself recommends `0770` and `0660` with the
right user and group." A bundle should ship the hardened values in the settings it installs:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

Because the installer writes `settings/override/site.ini.append.php`, apply these values after the install (or through
your site package's settings), not by shipping a filled file. The SQLite directory needs the same treatment:
"directory mode `2775` and a umask of `0002`" for a common group ([SQLite bundles](sqlite-bundles.md)).

## Building without root-owned files

- Run `composer install`, `ezpgenerateautoloads.php` and the installer **as the site user** in your build and first
  boot.
- If a step must run as root, finish with an ownership repair of the writable directories before the image is sealed.
- The installers do not refuse root ("both accept `--allow-root-user` and ignore it"), "but files created by `root` may
  later be unwritable for the web server" ([4.6](../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).
- In containers, "Decide the user ID once for the image and the volume, and keep it."

## Checklist

- [ ] One documented user (or group) owns `var/`, `settings/`, `design/`, `extension/`.
- [ ] Code directories are read-only for the site's PHP where the bundle allows it.
- [ ] Hardened `[FileSettings]` modes applied after the install.
- [ ] No file in the bundle owned by root under `var/`.
- [ ] Cron and console instructions name the site user.

## Related pages

- [Writable directories and permissions](../hosting/basics/writable-directories-and-permissions.md)
- [13.11 File permissions and ownership](../../install/13-security-hardening.md#1311-file-permissions-and-ownership)
- [File modes](../../bc/6.0/file-modes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
