---
title: PHP and platform support over the contract term
audience: government, project-manager, admin, hosting
category: public-sector
order: 40
summary: Which PHP versions, databases, web servers and operating systems Exponential 6.0 supports, why the platform under the software has its own lifecycle, and how a public body plans PHP and operating system upgrades alongside Exponential upgrades.
keywords: PHP versions, PHP 8.0, PHP 8.5, platform, database, operating system, web server, Velocity, lifecycle, end of life, hosting, upgrade planning, public sector
type: help
---
# PHP and platform support over the contract term

This page is for project managers and hosting staff in a public body. A content management system runs on a platform
(PHP, a database, a web server, an operating system), and each part has its own lifecycle. A contract of several
years will see at least one PHP upgrade. The facts come from [2. Requirements](../../../../install/02-requirements.md)
and the support commitment in [Who is the Exponential Foundation?](../../../who-is-the-exponential-foundation.md#the-support-commitment).

## Contents

1. [PHP](#php)
2. [Databases](#databases)
3. [Web servers and engines](#web-servers-and-engines)
4. [Operating systems](#operating-systems)
5. [Planning platform upgrades](#planning-platform-upgrades)
6. [In a contract](#in-a-contract)

## PHP

| PHP | Exponential 6.0 |
|---|---|
| 7.4 and older | not supported from 6.0.8 on; a site that must stay on 7.4 stays on 6.0.7 |
| 8.0 | the oldest supported version (new in 6.0.15); the stock PHP of Red Hat Enterprise Linux 9 and its rebuilds; Velocity does not run on it |
| 8.1 to 8.4 | supported, Velocity included |
| 8.5 | supported; the newest version tested |
| later 8.x | accepted by `composer.json` but not yet released or tested |

Source: [2. Requirements, PHP versions](../../../../install/02-requirements.md#versions). The continuous integration
runs the test suite on PHP 8.1 to 8.5 and checks that 8.0 still boots
([Who is the Exponential Foundation?](../../../who-is-the-exponential-foundation.md#php-versions)).

PHP itself has a published support schedule ([php.net: supported versions](https://www.php.net/supported-versions.php)).
Older PHP versions lose security support first, so the requirements chapter recommends the newest PHP your system
offers.

## Databases

Exponential 6.0 supports six database systems, in this official order of preference: SQLite 3 (the default),
PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Database support](../../../database-support.md),
[9. Databases](../../../../install/09-databases.md)). Microsoft SQL Server is described as possible with additional work.
The database is part of the platform: plan its own upgrades, and its backup method
([Database backups per engine](../../../hosting/operations/database-backups-per-engine.md)).

## Web servers and engines

A site can be served by Exponential Velocity, by Apache or nginx with PHP-FPM, or by FrankenPHP
([8. Serving the site](../../../../install/08-serving-the-site.md)). Velocity needs PHP 8.1 or later with the `sockets`
extension ([2. Requirements](../../../../install/02-requirements.md)). Velocity is a separate package with its own
versions and changelog ([Velocity changelog](../../../../../changelogs/extensions/exponential-velocity.md)).

## Operating systems

Supported operating systems and their tiers are listed in [Operating system support](../../../os-support.md); hosts
will find the practical notes in [Operating systems for hosts](../../../hosting/basics/operating-systems-for-hosts.md).

## Planning platform upgrades

| Layer | Who upgrades it | Typical trigger |
|---|---|---|
| Exponential kernel and extensions | the supplier or your site administrator | a new 6.0.x release, a security fix |
| PHP | the host | the PHP version's end of security support |
| Database | the host | the database version's end of support |
| Operating system | the host | the distribution's end of support |

Practical rules:

- Upgrade one layer at a time, with a test on a staging copy in between.
- Before a PHP upgrade, check that every extension the site uses supports the new version; your own extensions are
  the usual risk ([14.1](../../../../install/14-migrating-from-4x.md#141-what-changes-and-what-does-not) explains why
  PHP code is the largest piece of a migration).
- Hosts have a runbook: [PHP upgrade](../../../hosting/runbooks/php-upgrade.md).
- After a PHP change, the engine must reload its code; the operations pages explain it per engine
  ([Deploying](../../../../guides/deploying.md)).

## In a contract

```text
Platform
1. The Supplier keeps PHP, the database and the operating system on versions that receive security
   updates from their publishers, and supported by the Exponential version in use
   (doc/eng-us/install/02-requirements.md).
2. The Supplier announces a platform upgrade <n> days ahead and tests it on a staging copy first.
3. The Supplier reports the PHP, database and operating system versions in each <period> report.
```

## Related pages

- [The product lifecycle](README.md)
- [Supported versions](supported-versions.md)
- [Requirements at a glance (for hosts)](../../../hosting/basics/requirements-at-a-glance.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
