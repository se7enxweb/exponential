---
title: Databases in the official order
audience: hosting, admin
category: hosting
order: 60
summary: The six databases Exponential 6.0.x supports, in the project's official order (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), compared side by side for hosts, with how to choose which to offer and what each one costs to operate. Microsoft SQL Server is known to be possible with additional work.
keywords: database, databases, official order, supported databases, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, mysqli, pgsql, sqlite3, oci8, hosting plans, database offer, managed database, CI
featured: true
type: help
---
# Databases in the official order

This page is for product managers who decide which databases a hosting offer includes, and for system administrators
who will run them. It answers: which databases Exponential 6.0.x supports, in which order the project recommends
them, how they compare from a host's point of view, and how to choose what to offer. The support statement and every
detail are in [Database support](../../database-support.md); the operator's chapter is
[chapter 9, Databases](../../../install/09-databases.md).

## Contents

1. [The official order](#the-official-order)
2. [Side by side](#side-by-side)
3. [What each one means for a host](#what-each-one-means-for-a-host)
4. [Choosing what to offer](#choosing-what-to-offer)
5. [Things every database has in common](#things-every-database-has-in-common)
6. [Checklist](#checklist)
7. [Related pages](#related-pages)

## The official order

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time.

All six have a driver in the kernel or in a shipped extension, an entry in the setup wizard and a path in the
command-line installers ([Database support](../../database-support.md#the-supported-databases-in-order)). The order
is the order of recommendation for new sites; it does not mean the later ones work less well. Microsoft SQL Server has
no driver in 6.0.x ([Microsoft SQL Server status](microsoft-sql-server-status.md)).

Use this order wherever your own material lists databases: plan descriptions, order forms, support articles.

## Side by side

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL |
|---|---|---|---|---|---|---|
| Database server to run | none (one file) | yes | yes | yes | yes | yes |
| `DatabaseImplementation` | `sqlite3` | `ezpostgresql` | `mongodb` | `ezmysqli` | `ezoracle` | `ezmysqli` |
| PHP extension | `sqlite3` | `pgsql` | `mongodb` + library `mongodb/mongodb` | `mysqli` | `oci8` | `mysqli` |
| `exp:install --db=` | `sqlite` (default) | `pgsql` | `mongodb` | `mariadb` | `oracle` | `mysql` |
| Several web servers on one database | no | yes | yes | yes | yes | yes |
| Practical minimum | library 3.33 | 13 | server 6.0, PHP 8.2 | a release the vendor supports | 19c | a release the vendor supports |
| Character set | UTF-8 | `UTF8` | UTF-8 (BSON) | `utf8mb4` | `AL32UTF8` | `utf8mb4` |
| Tested in the project's CI | driver unit tests | yes (PostgreSQL 15) | adapter unit tests | no (covered by the MySQL job, same driver) | no | yes (MySQL 8.0) |
| Backup tool | `sqlite3 .backup` | `pg_dump` | `mongodump` | `mariadb-dump` | `ext:ezoracle:datapump`, `expdp` | `mysqldump` |

Sources: [At a glance](../../database-support.md#at-a-glance),
[versions](../../database-support.md#versions-checked-against-the-code) and
[test coverage](../../database-support.md#test-coverage-ci).

## What each one means for a host

| Database | For a host | Page |
|---|---|---|
| 1. SQLite 3 | Nothing to run or account for: the database lives in the site's own `var/storage/sqlite3/`. Fits every plan where one machine serves the site, including shared hosting. Backups are a file operation, but never a plain copy of a file in use. | [SQLite for hosts](sqlite-for-hosts.md) |
| 2. PostgreSQL | The recommended database server. One database per site, with the site's user as its owner; `pgcrypto` must be available. | [PostgreSQL for hosts](postgresql-for-hosts.md) |
| 3. MongoDB | For customers standardized on MongoDB. Needs PHP 8.2, a Composer library, a user in the site's database and a set of indexes; has documented limits. | [MongoDB for hosts](mongodb-for-hosts.md) |
| 4. MariaDB | The MySQL-compatible server the project prefers; the usual database of hosting panels. | [MariaDB for hosts](mariadb-for-hosts.md) |
| 5. Oracle | For customers who run Oracle. Needs `oci8` built against an Oracle client; usually the customer's own database. | [Oracle for hosts](oracle-for-hosts.md) |
| 6. MySQL | Supported and tested in CI on the same driver as MariaDB; last in the order because the project recommends the others first for new sites. | [MySQL for hosts](mysql-for-hosts.md) |

## Choosing what to offer

The project does not prescribe a hosting offer. These questions, taken from the decision table of
[Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md), decide it:

| Question | If yes |
|---|---|
| Does more than one machine have to write the site's database? | A database server, PostgreSQL first; not SQLite |
| Is the plan a single machine, shared or not? | SQLite 3 covers it with no database server to run |
| Do customers bring their own MongoDB or Oracle? | Offer that database as an option, with its page's requirements |
| Does your panel only provision MariaDB or MySQL accounts? | Offer MariaDB, and SQLite where the panel allows a writable directory |
| Do you run a database service already? | Offer the engine it runs, if it is among the six |

Typical offers a host can build from this:

- **Entry plans**: SQLite 3 only. No database account to provision, nothing to tune, backups per site.
- **Business plans**: SQLite 3 or PostgreSQL, with MariaDB where the panel provisions it.
- **Enterprise or dedicated plans**: any of the six, including the customer's own MongoDB or Oracle.

> **Tip**
> Whatever you offer, write the engine of each customer site into your inventory. Backups, restores, upgrades and
> support all differ per engine, and the update files of an upgrade are chosen by the engine the site runs on now
> ([moving a site](../../database-support.md#moving-a-site-to-another-database)).

## Things every database has in common

- **The connection** is configured in `settings/site.ini`, block `[DatabaseSettings]`, overridden in
  `settings/override/site.ini.append.php` (written by the installers). Never put a real password into a file under
  version control.
- **The installers do not create a database server's database or user.** Create them first; each page shows how.
- **`exp:install` reads the password** from the environment variable `EXP_INSTALL_DB_PASSWORD`, so it stays out of
  the shell history and the process list.
- **After a settings change**, clear the INI cache and restart Velocity's workers:

  ```bash
  php bin/php/ezcache.php --clear-tag=ini --allow-root-user
  php bin/php/console exp:velocity restart --allow-root-user
  ```

- **Which database is a site using?**

  ```bash
  php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user
  ```

  or **Setup > System information**, the Database card.
- **A complete backup** is the database and the files: `var/storage`, `settings/override` and
  `settings/siteaccess` ([Backup and restore](../../database-support.md#backup-and-restore)).
- **Moving between engines** is possible; see [Changing the database engine](../migrations/changing-the-database-engine.md).

## Checklist

- [ ] The plan's databases are listed in the official order.
- [ ] The PHP extension of each offered database is installed in the web and the command line PHP.
- [ ] A procedure exists to create a database and a user per site (servers only).
- [ ] Backups per engine are in place and tested ([Database backups per engine](../operations/database-backups-per-engine.md)).
- [ ] The engine of every customer site is recorded.

## Related pages

- [Database support](../../database-support.md) and [chapter 9, Databases](../../../install/09-databases.md)
- [SQLite](sqlite-for-hosts.md), [PostgreSQL](postgresql-for-hosts.md), [MongoDB](mongodb-for-hosts.md),
  [MariaDB](mariadb-for-hosts.md), [Oracle](oracle-for-hosts.md), [MySQL](mysql-for-hosts.md) for hosts
- [Microsoft SQL Server status](microsoft-sql-server-status.md)
- [Databases and users in control panels](../panels/databases-and-users.md)
- [Operating system support](../../os-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
