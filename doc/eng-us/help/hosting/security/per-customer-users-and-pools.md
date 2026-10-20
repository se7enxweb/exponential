---
title: Per-customer users and pools
audience: hosting
category: hosting
order: 120
summary: The concrete setup that gives each customer's Exponential site its own operating-system user, its own PHP-FPM pool or Velocity instance, its own crontab and its own database user, with ownership and modes for code, var/ and settings/, the shared-group variant, and the checks that show it holds.
keywords: per-customer user, operating-system user, PHP-FPM pool, pool per site, Velocity instance, User, Group, AllowRootWorkers, crontab, file ownership, permissions, FileSettings, setgid, read-only code, database user, deploy user, isolation
type: help
---
# Per-customer users and pools

This page is for the system administrators of a host who build the per-customer setup on a shared machine. It turns
the principle of [Multi-tenant isolation](multi-tenant-isolation.md) into users, pools, crontabs and file modes. The
procedure behind it is [Chapter 8, File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)
and [13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership).

## Contents

1. [The target](#the-target)
2. [The user](#the-user)
3. [Ownership and modes](#ownership-and-modes)
4. [A PHP-FPM pool per customer](#a-php-fpm-pool-per-customer)
5. [A Velocity instance per customer](#a-velocity-instance-per-customer)
6. [Cron and console as the customer's user](#cron-and-console-as-the-customers-user)
7. [When two users must write](#when-two-users-must-write)
8. [Database users](#database-users)
9. [Checks](#checks)
10. [Checklist](#checklist)

## The target

```text
customer A                               customer B
  user  site-a, group site-a               user  site-b, group site-b
  PHP-FPM pool "site-a" (or Velocity)      PHP-FPM pool "site-b" (or Velocity)
  crontab of site-a                        crontab of site-b
  database "a", database user "a"          database "b", database user "b"
  /var/www/vhosts/<domain-a>/              /var/www/vhosts/<domain-b>/
```

"Problems with permissions almost always come from **two users writing the same files**"
([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)). So one user writes each site: the
pool or the Velocity workers, the cronjobs and the console scripts all run as that customer's user.

## The user

One operating-system user and group per customer installation, created by your provisioning or your panel (panels
usually create one per subscription; check your panel's documentation). Shell access for the customer is a plan
decision ([Cron and shell access](../panels/cron-and-shell-access.md)).

## Ownership and modes

| Path | Owner | Writable by the site's user | Why |
|---|---|---|---|
| `var/` and below | site user, site group | yes | caches, logs, uploads, autoloads |
| `settings/`, `settings/override`, `settings/siteaccess` | site user, site group | yes, while administrators change settings in the browser | settings written by the administration |
| `design/`, `extension/` | another user (deploy user), or the site user during package installs | only while packages are installed from the administration | package installation |
| `kernel/`, `lib/`, `vendor/`, `index.php` | another user | no | a compromised PHP process cannot change code |
| `settings/override/*.ini.append.php` | site user or root, group the site's group | `0640` or stricter | secrets |

Set the hardened modes that `site.ini` recommends instead of the shipped `0777`/`0666`:

```ini
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0770
StorageFilePermissions=0660
TemporaryPermissions=0770
LogFilePermissions=0660
```

and fix existing files once, with the site's group on everything
([13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)):

```bash
find var -type d -exec chmod 2770 {} +
find var -type f -exec chmod 0660 {} +
```

Give each site its own `TemporaryDir` below `var/` instead of the shared `/tmp/`.

## A PHP-FPM pool per customer

"The pool runs PHP as the site's user" ([8.5.2](../../../install/08-serving-the-site.md#852-php-fpm)). Per pool set the
user and group, and the PHP values Exponential needs (`memory_limit` at least 256M, `max_execution_time`,
`upload_max_filesize`, `post_max_size`, OPcache on). Where pools live and how to reload the right master:
[PHP selection and FPM pools](../panels/php-selection-and-fpm-pools.md). On machines with several PHP versions,
`exp:velocity deploy` finds the master serving the site (`[DeploySettings] PhpFpmService=auto`).

Keep session files, `upload_tmp_dir` and any `auto_prepend_file` (for example request-shield) per pool, so nothing of
one customer lands in another's directory.

## A Velocity instance per customer

One Velocity per site, each as the customer's user, bound to `127.0.0.1` behind a proxy that routes by host name
([Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md)):

```bash
php bin/php/console exp:velocity config set ServerSettings User site-a --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Group site-a --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

- Who the workers become: `[ServerSettings] User` and `Group`, else `VC_RUN_USER` and `VC_RUN_GROUP`, else the owner of
  the document root. A document root owned by root with nothing configured leaves the workers as root; the start says
  so ([8.3.5](../../../install/08-serving-the-site.md#835-address-ports-user-and-group)).
- `AllowRootWorkers=disabled` (the default) refuses root as the worker user.
- Each instance needs its own ports; the per-site file `/etc/vc/sites-available/<site>.conf` is mode `0600`.
- Workers and pool sizes per plan: [Velocity workers and pools](../servers/velocity-workers-and-pools.md).

## Cron and console as the customer's user

Cronjobs from that user's crontab, or with `sudo -u`:

```bash
crontab -u site-a -e
# */5 * * * * cd /var/www/vhosts/<domain-a>/<installation> && php runcronjobs.php -q frequent
sudo -u site-a php bin/php/ezcache.php --clear-tag=ini
```

Exponential's command-line scripts refuse to run as root unless `--allow-root-user` is given; use it only for the
documented root tasks, and check afterwards that files in `var/` still belong to the site's user
([Security hardening](../../security-hardening.md#cron-and-command-line-scripts-as-root)).

## When two users must write

If the users must differ (for example the web server's PHP user and a deploy user), give them a common group, make the
writable directories group-writable and setgid so new files inherit the group, and use the group-preserving modes
above ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)):

```bash
sudo chgrp -R site-a design extension settings var
sudo find var settings -type d -exec chmod 2775 {} +
```

When Apache (PHP-FPM) and Velocity serve the same installation side by side, they share `var/`, the caches and the
sessions: give them the same user or this group setup, and look at the owner of new files in `var/` after a few
requests.

## Database users

One database and one database user per installation, with rights on that database only; the connection is written to
`settings/override/site.ini.append.php`. Per-engine notes, in the official order 1. SQLite 3 · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL: [Databases and users](../panels/databases-and-users.md) and
[Databases in the official order](../basics/databases-in-the-official-order.md). With SQLite 3 there is no database
user: the file below `var/storage/sqlite3/` is protected by the site user's file permissions.

## Checks

```bash
# no world-writable settings files
find settings extension/*/settings -perm -o+w
# who owns new files after some traffic (expect the site's user and group)
find var -newer index.php -not -user site-a | head
# which user each PHP-FPM pool runs as
ps -eo user,cmd | grep 'php-fpm: pool' | grep -v grep
```

The first must print nothing ([13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)).

## Checklist

- [ ] One operating-system user and group per installation.
- [ ] Pool or Velocity workers, cron and console all run as that user.
- [ ] Code owned by another user and read-only to the site's user.
- [ ] Hardened `[FileSettings]`; no world-writable files; `settings/override` `0640` or stricter.
- [ ] Per-site `TemporaryDir`, session directory and upload directory.
- [ ] Velocity: `User`, `Group` set; bound to `127.0.0.1` behind the proxy; own ports per instance.
- [ ] One database and database user per installation.
- [ ] The checks above run after provisioning and after every deploy.

## Related pages

- [Security for hosts: overview](README.md)
- [Multi-tenant isolation](multi-tenant-isolation.md)
- [Writable directories and permissions](../basics/writable-directories-and-permissions.md)
- [PHP selection and FPM pools](../panels/php-selection-and-fpm-pools.md)
- [Chapter 8: File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
