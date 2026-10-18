---
title: Hosting partner technical checklist
audience: partner
category: partnership
order: 390
summary: A technical checklist for hosting partners preparing a platform for Exponential 6 - PHP and extensions, php.ini, databases, engines, file permissions, cronjobs, caches, TLS and headers, backups, monitoring, updates - each item linked to the documentation that explains it.
keywords: hosting, checklist, PHP, extensions, php.ini, database, Velocity, PHP-FPM, permissions, cron, cache, TLS, headers, backup, monitoring, updates
type: help
---
# Hosting partner technical checklist

This page is for system administrators at hosting partners. Use it when you build a hosting product for Exponential
or audit an existing one. Each section names the chapter of the documentation that explains it.

## PHP

Source: `doc/install/02-requirements.md`.

- [ ] A supported PHP version: 8.0 to 8.5 for the 6.0.15 line (Velocity needs 8.1 or later)
- [ ] 64-bit PHP build
- [ ] Required extensions present in both the web PHP and the CLI PHP: `dom`, `simplexml`, `xsl`, `mbstring`,
      `iconv`, `intl`, `zlib`, and at least one database extension
- [ ] `php -v` and `php -m` on the command line match the web server's PHP information
- [ ] php.ini: `memory_limit` 64M or more, `max_execution_time` 30 or more, `date.timezone` set, `file_uploads`
      on, `allow_url_fopen` on

## Database

Source: [Database support](../database-support.md).

- [ ] The database chosen in the project's order of preference: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle,
      MySQL
- [ ] The matching PHP extension: `sqlite3`, `pgsql`, `mongodb` (plus the library `mongodb/mongodb`), `mysqli`
      or `oci8`
- [ ] Character set as the database page requires
- [ ] Backup and restore procedure tested

## Engine and web server

Sources: `doc/install/08-serving-the-site.md`, [Security hardening](../security-hardening.md).

- [ ] Engine chosen: Velocity, FrankenPHP, or a web server with PHP-FPM
- [ ] Only the public entry points are reachable; internal directories are not served
- [ ] The setup wizard and installer files are not reachable after installation
- [ ] The web user and any CLI/cron user can both write `var/` without permission conflicts

## Scheduled work

- [ ] Cronjobs for the installation run as the site's user, not as root
      ([Security hardening](../security-hardening.md#cron-and-command-line-scripts-as-root))
- [ ] Logs of cron runs are kept and rotated

## Security

Source: [Security hardening](../security-hardening.md), `doc/install/13-security-hardening.md`.

- [ ] TLS on every host name, with renewal automated
- [ ] Security headers set; the administration's Content Security Policy not weakened
- [ ] Debug output off in production
- [ ] Default passwords changed ([Default administrator password](../default-administrator-password.md))
- [ ] Separate users per customer site

## Backups and recovery

- [ ] Database and `var/storage` (uploaded files) backed up on a schedule
- [ ] Off-site copy
- [ ] A restore tested at least every quarter, with the time it took written down

## Monitoring

- [ ] Uptime check of the front page and the administration sign-in page
- [ ] Disk space alarms for `var/`
- [ ] Error log watched ([When something goes wrong](../when-something-goes-wrong.md))

## Updates

- [ ] OS and PHP security updates applied on a schedule
- [ ] A staging copy for each customer site to test Exponential releases ([Testing releases](testing-releases.md))
- [ ] A documented path to the next PHP version before the current one ends its support

## Related pages

- [Hosting partners](hosting-partners.md)
- [Operating system support](../os-support.md)
- [Security responsibilities of partners](security-responsibilities.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
