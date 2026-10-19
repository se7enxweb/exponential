---
title: Check file and directory permissions
audience: government, admin, hosting
category: public-sector
order: 370
summary: Step by step - check that only var/ (and, where needed, settings/) is writable by the site's PHP, that every process writing var/ uses the same owner, and that new files are not world-readable, with the hardened modes from the security chapter.
keywords: file permissions, directory permissions, ownership, writable directories, var, settings, EZP_DIR_MODE_MAX, EZP_FILE_MODE_MAX, root, mixed owners, hardening
type: help
---
# Check file and directory permissions

This task is for administrators doing a security review, or repairing a site after a command run as root broke it.
At the end the site's PHP can write only what it must, every writer agrees on the owner, and new files are not open
to other accounts. Sources: [Writable directories and permissions (hosting)](../../../hosting/basics/writable-directories-and-permissions.md)
and [chapter 13.11](../../../../install/13-security-hardening.md#1311-file-permissions-and-ownership).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Hardened modes](#hardened-modes)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- Shell access as root; know the site's user (the PHP-FPM pool user, or the user Velocity's workers run as).

## Steps

1. What must be writable: `var/` and everything below; `settings/`, `settings/override` and `settings/siteaccess` only
   while administrators change settings in the browser; `design/` and `extension/` only while packages are installed
   from the administration. Everything else (`kernel/`, `lib/`, `vendor/`, `index.php`) only needs to be readable.
2. Look for files in `var/` owned by someone else:

   ```bash
   find var -not -user <site user> | head
   ```

   Root-owned cache or log files after a command run as root are the usual cause of odd failures; **Log health** also
   warns about logs with mixed owners.
3. Look for PHP files where only data belongs:

   ```bash
   find var/storage -name '*.php'
   ```

   Any result is suspect ([Defacement](../../operations/incidents/defacement.md)).
4. Run cronjobs and console commands as the site's user from now on, not as root.

## Hardened modes

New files and directories are created as open as `site.ini [FileSettings]` and the code ask (0777/0666 by default). On a
live server limit them in `config.php`:

```php
define( 'EZP_DIR_MODE_MAX', 0750 );
define( 'EZP_FILE_MODE_MAX', 0640 );
```

and narrow what exists once ([limits for file modes](../../../../bc/6.0/file-modes.md)). With
[ManagedByDeployment](lock-configuration-with-managed-by-deployment.md) on, `settings/` and `design/` need not be
writable by PHP at all.

## Check it worked

- **Setup > System information**: the *var directory* check is green.
- **Log health**: no logs listed as not writable.
- `find var/storage -name '*.php'` finds nothing.

## Related pages
- [Hardening the host](../../operations/hosting/hardening-the-host.md)
- [Check log health](check-log-health.md)
- [Security and audit, step 1](../../../../guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes)
- [Hardening for hosts](../../../hosting/security/hardening-for-hosts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
