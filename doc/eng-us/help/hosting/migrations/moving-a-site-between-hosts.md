---
title: Moving a site between hosts
audience: hosting, admin
category: hosting
order: 50
summary: Moving an Exponential 6 site from one server or host to another without changing its version: what to copy, what to fetch fresh, the settings that name the old server, the commands in order, and the checks afterwards.
keywords: move, transfer, server move, host change, copy site, rsync, backup, restore, settings override, siteaccess, database connection, caches, autoloads, Velocity, DNS, permissions
type: help
---
# Moving a site between hosts

This page is for system administrators who move an Exponential 6 site from one machine to another, between hosts or
inside one host, at the same version. It answers: what you copy, what you fetch again, which settings name the old
machine, and in which order the commands run.

When the version also changes, read [From older Exponential versions](from-older-exponential-versions.md) first; the
process for the customer side is on [Migrating customers to your hosting](migrating-customers-to-your-hosting.md).

## Contents

1. [What moves and what does not](#what-moves-and-what-does-not)
2. [Before the move](#before-the-move)
3. [The move, step by step](#the-move-step-by-step)
4. [Settings that name the old machine](#settings-that-name-the-old-machine)
5. [Checks afterwards](#checks-afterwards)

## What moves and what does not

| Item | Move it? | How |
|---|---|---|
| The database | yes | the engine's backup and restore tools ([Database backups per engine](../operations/database-backups-per-engine.md)) |
| `var/storage` (and `var/<site>/storage`) | yes | archive or `rsync` |
| `settings/override`, `settings/siteaccess` | yes | they contain passwords: transfer them privately |
| Custom extensions and designs | yes, preferably from their Git repositories | `git clone` |
| The kernel and the packaged extensions | fetch fresh at the same version | Git checkout of the tag, or `composer install` from the lock file |
| `var/cache`, `var/<site>/cache` | no | regenerated |
| `var/log` | optional | for later investigation |

Source of the list: [10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore).

## Before the move

- The target meets the requirements, with the same PHP major version or a newer supported one
  ([Requirements at a glance](../basics/requirements-at-a-glance.md)).
- The same database engine is installed, or you plan an engine change
  ([Changing the database engine](changing-the-database-engine.md)).
- The PHP extensions the site uses are installed on the target.
- The DNS time-to-live of the site's domains is lowered.
- A restore of the latest backup on the target has been tested once.

## The move, step by step

1. **Maintenance on, on the old machine**, so no write happens between the database and the file backup:

   ```bash
   php bin/php/maintenance.php on --message="Moving to a new server" --until=1h --allow-root-user
   ```

2. **Back up** the database with the engine's tool and the files:

   ```bash
   tar czf files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
   ```

3. **Put the code in place on the target**: the same tag of the kernel (`git checkout v6.0.<n>`) and
   `composer install`, plus custom extensions from Git.
4. **Restore** the database and unpack the files; set the owner and mode the target's web server or engine needs
   ([Writable directories and permissions](../basics/writable-directories-and-permissions.md)).
5. **Adjust the settings** listed below.
6. **Regenerate autoloads and clear caches**:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

7. **Start the engine** (or reload PHP-FPM), clear Velocity's response cache if it serves the site
   (`php bin/php/console exp:velocity cache clear --allow-root-user`).
8. **Test** through a hosts-file entry, then switch DNS.
9. **Leave the old machine in maintenance mode** until the DNS change has reached everyone. Maintenance mode lives
   in `var/maintenance.json` on the old machine, which the file backup above does not include, so the target is not
   in maintenance mode unless you switch it on there. If you did (to test in peace), end it with
   `php bin/php/maintenance.php off --allow-root-user`
   ([10.9](../../../install/10-after-installing.md#maintenance-windows)).

## Settings that name the old machine

| Setting | File | Change to |
|---|---|---|
| `[DatabaseSettings]` server, port, user, password, database | `settings/override/site.ini.append.php` | the target's database |
| Site addresses and siteaccess matching | `settings/override/site.ini.append.php`, `settings/siteaccess/*/site.ini.append.php` | the target's host names, if they change |
| `[MailSettings]` transport and server | `settings/override/site.ini.append.php` | the target's mail setup ([Mail delivery](../operations/mail-delivery.md)) |
| Cluster settings | `settings/override/file.ini.append.php` | the target's shared storage, if clustered |
| Velocity settings | `settings/override/velocity.ini.append.php`, if present | the target's ports and certificates |
| Absolute paths in custom settings | any override | the target's paths |

> **Warning**
> `settings/override` holds the database password and other secrets. Never put it in a ticket, a public repository
> or a shared folder; transfer it over an encrypted channel and keep the archive private.

## Checks afterwards

- Front page and administration load on the target.
- Sign in, publish a test object, upload an image, search.
- Compare row counts of `ezcontentobject`, `ezcontentobject_tree` and `ezcontentobject_attribute` with the old
  database ([Database support](../../database-support.md#moving-a-site-to-another-database)).
- The cron jobs run on the target, and no longer on the old machine ([Cron jobs](../operations/cron-jobs.md)).
- Mail arrives.
- The log viewer shows no new errors ([Log viewer and rotation](../operations/log-viewer-and-rotation.md)).

## Related pages

- [Restore from backup (runbook)](../runbooks/restore-from-backup.md)
- [What to back up](../operations/backups-what-to-back-up.md)
- [Go-live checklist for a customer site](../checklists/new-customer-site-go-live.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
