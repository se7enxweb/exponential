---
title: New server checklist
audience: hosting, admin
category: hosting
order: 20
summary: A checklist for building a server that will host Exponential sites: operating system, PHP and extensions, php.ini, database, engine or web server, TLS, users and permissions, cron, mail, backups, monitoring and a test installation.
keywords: checklist, new server, build, provisioning, operating system, PHP, extensions, php.ini, OPcache, database, Velocity, PHP-FPM, Apache, nginx, TLS, firewall, users, permissions, cron, mail, backup, monitoring
type: help
---
# New server checklist

This checklist is for system administrators who build a machine that will host Exponential sites. Work through it
top to bottom; each item links to the page with the details. The order matters: the test installation at the end
checks most of what comes before.

```text
Server: <name>          Built by: <name>          Date: <date>          Plan(s): <plans this server serves>
```

## Operating system

- [ ] A tier 1 or tier 2 system from [Operating system support](../../os-support.md) (for example AlmaLinux 9, Red Hat
      Enterprise Linux 9 or 10 and rebuilds, Debian 12 or 13, Ubuntu 22.04 or 24.04).
- [ ] Security updates applied; automatic security updates decided.
- [ ] Time synchronization running; time zone set.
- [ ] Firewall: only the ports the engine or web server, SSH and your monitoring need.
- [ ] SSH: keys only, no password sign-in for root.

## PHP

- [ ] PHP 8.0 or later; 8.1 or later if Velocity will serve sites; the newest 8.x the system offers is the
      recommendation ([PHP versions and extensions](../basics/php-versions-and-extensions.md)).
- [ ] The required extensions installed; the setup wizard's system check or `exp:install`'s `SystemCheck` step
      reports none missing ([Chapter 2](../../../install/02-requirements.md#23-php-extensions)).
- [ ] For Velocity: `sockets`, plus `pcntl` and `openssl`; `php bin/php/console exp:velocity ext check --allow-root-user`
      lists what is missing.
- [ ] `php.ini`: `memory_limit` at least 64M, `max_execution_time` 30 or more, `date.timezone` set, `file_uploads` on
      ([PHP settings for Exponential](../basics/php-settings-for-exponential.md)).
- [ ] OPcache on for the web PHP ([OPcache](../performance/opcache.md)).
- [ ] 64-bit PHP ("for support of more than 30 languages", [Operating system support](../../os-support.md)).
- [ ] Composer 2 installed ([Composer and Git on servers](../basics/composer-and-git-on-servers.md)).
- [ ] GD or ImageMagick's `convert`, both recommended ([Image processing and media](../basics/image-processing-and-media.md)).

## Database

- [ ] The engine chosen in the official order: SQLite 3 (no server needed), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL
      ([Databases in the official order](../basics/databases-in-the-official-order.md)).
- [ ] For a server engine: version at or above the practical minimum on [Database support](../../database-support.md#versions-checked-against-the-code).
- [ ] One database and one user per customer site; no shared superuser.
- [ ] The database's own backup tool tested ([Database backups per engine](../operations/database-backups-per-engine.md)).

## Engine or web server

- [ ] Decided: Velocity, FrankenPHP, or Apache/nginx with PHP-FPM ([Choosing a web server](../servers/choosing-a-web-server.md)).
- [ ] With PHP-FPM: one pool per site, running as the site's system user ([PHP-FPM tuning](../performance/php-fpm-tuning.md)).
- [ ] With Velocity: the engine set to `qbix` for the site (the shipped default engine is PHP's built-in server, for
      development) ([Velocity for hosts](../servers/velocity-for-hosts.md)).
- [ ] Only the asset paths are served as files; the rewrite rules from the installation book are in place
      ([Chapter 8](../../../install/08-serving-the-site.md)).

## TLS

- [ ] Certificates issued and renewed automatically ([TLS and HTTPS](../security/tls-and-https.md)).
- [ ] HTTP redirects to HTTPS.

## Users and permissions

- [ ] One system user per customer site ([Per-customer users and pools](../security/per-customer-users-and-pools.md)).
- [ ] Writable directories as the setup's `directory_permissions` check lists them, nothing more
      ([Writable directories and permissions](../basics/writable-directories-and-permissions.md)).

## Cron, mail, logs

- [ ] A crontab per site that runs `runcronjobs.php` ([Cron jobs](../operations/cron-jobs.md)).
- [ ] Outgoing mail with a sender domain that passes SPF and DKIM checks ([Mail delivery](../operations/mail-delivery.md)).
- [ ] Log rotation decided ([Log viewer and rotation](../operations/log-viewer-and-rotation.md)).

## Backups and monitoring

- [ ] Backups of the database and `var/storage`, `settings/override`, `settings/siteaccess`, stored off the machine
      ([What to back up](../operations/backups-what-to-back-up.md)).
- [ ] External availability check and disk, memory and load alerts ([Monitoring overview](../operations/monitoring-overview.md)).

## Test installation

- [ ] `exp:install` (or the setup wizard) completes on this server ([One-click installers](../provisioning/one-click-installers.md)).
- [ ] Front page and administration load over HTTPS.
- [ ] An upload, a publish and a search work.
- [ ] A backup of the test site is restored on another machine.
- [ ] The test installation is removed, or kept as a reference site that is not public.

## Related pages

- [Requirements at a glance](../basics/requirements-at-a-glance.md)
- [Hardening for hosts](../security/hardening-for-hosts.md)
- [New customer site go-live](new-customer-site-go-live.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
