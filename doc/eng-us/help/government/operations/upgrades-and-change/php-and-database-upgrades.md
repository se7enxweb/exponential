---
title: PHP and database upgrades
audience: government, admin, hosting
category: public-sector
order: 90
summary: Upgrading the platform under Exponential - PHP versions supported, database server versions, the order of steps, staging, and what an in-house IT team or a government cloud provider must coordinate.
keywords: PHP upgrade, PHP 8.5, database upgrade, MySQL, MariaDB, PostgreSQL, SQLite, platform upgrade, end of life, OS upgrade, deprecations, deprecated.log, coordination, cloud provider
type: help
---
# PHP and database upgrades

This page is for in-house IT teams and service owners who must keep PHP and the database server supported, often on
a schedule set by the operating system or the cloud provider. The hosting runbook
[PHP upgrade](../../../hosting/runbooks/php-upgrade.md) has the technical steps; this page adds order and coordination.

## Contents
1. [What is supported](#what-is-supported)
2. [The order of upgrades](#the-order-of-upgrades)
3. [Preparing with the deprecations log](#preparing-with-the-deprecations-log)
4. [Database server upgrades](#database-server-upgrades)
5. [Coordination](#coordination)
6. [Related pages](#related-pages)

## What is supported

| Component | Support | Source |
|---|---|---|
| PHP 8.0 | the oldest supported; Velocity runs on it from its release 0.0.4.54 | [Requirements](../../../../install/02-requirements.md) |
| PHP 8.1 to 8.5 | supported, Velocity included | the same |
| PHP 7.4 | a site that must stay on it stays on 6.0.7 | [Upgrading, Part B](../../../../guides/upgrading.md#part-b-from-54-or-590-to-exponential-600) |
| Databases | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, with practical minimums | [Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md) |

## The order of upgrades

1. Update Exponential to the newest 6.0.x first, so fixes for newer PHP versions are in place.
2. Upgrade PHP on staging; install the PHP extensions the site needs
   (`./console exp:velocity ext check --allow-root-user` lists what is missing and prints the install command).
3. Test on staging ([Staging and approval](staging-and-approval.md)).
4. Upgrade production in a window; reload the PHP-FPM that serves the site (the service may not be named `php-fpm` on
   servers with several PHP versions; `exp:velocity deploy` finds it with `[DeploySettings] PhpFpmService=auto`) or
   restart Velocity.
5. Never upgrade PHP, the database server and Exponential in the same window: one change at a time keeps the cause of
   a problem obvious.

## Preparing with the deprecations log

PHP 8 reports deprecations for code a later PHP version will refuse. Exponential writes them to
`var/log/deprecated.log`, each distinct message once per request
([Operating a site, section 8](../../../../guides/operating-a-site.md#php-deprecations-and-log-file-sizes)):

```bash
sort var/log/deprecated.log | uniq -c | sort -rn | head
```

To log them in production too, add `AlwaysLog[]=deprecated` under `[DebugSettings]`. The list is your developers' work
list before the next PHP upgrade; your own extensions are the usual source.

## Database server upgrades

- Follow the vendor's procedure; Exponential does not upgrade database servers.
- Back up with the engine's tool first ([Backups per database engine](../backups-and-recovery/backups-per-database-engine.md)).
- Afterwards run **Setup > Upgrade check**, **Check database consistency**: it compares the schema with what
  Exponential expects.
- Character sets matter: `utf8mb4` for MySQL and MariaDB, `UTF8` for PostgreSQL
  ([Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md#side-by-side)).

## Coordination

| Party | Agree |
|---|---|
| Operating system or cloud provider | the end-of-support dates of PHP and the database, and the window |
| Developers of your own extensions | fixes for the deprecations found |
| Change board | one change per component |
| Service owner | the window and the public notice |

## Related pages
- [Upgrade overview for the public sector](upgrade-overview-for-public-sector.md)
- [Check PHP extensions before an upgrade](../../tasks/operations/check-php-extensions-before-an-upgrade.md)
- [PHP versions and extensions (hosting)](../../../hosting/basics/php-versions-and-extensions.md)
- [Database choice in the official order](../hosting/database-choice-in-the-official-order.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
