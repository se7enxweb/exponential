---
title: Database choices for bundles
audience: bundler, hosting
category: partnership
order: 240
summary: Which databases a bundle of Exponential 6.0.x can use, in the project's official order (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), what each needs inside the bundle, how each installer names it, and which database fits which kind of bundle.
keywords: database, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, --db, Type, pdo, sqlite3, pgsql, mysqli, oci8, bundle
type: help
---
# Database choices for bundles

This page is for bundlers deciding which database their bundle uses or offers. The support statement and every
detail are on [Database support](../database-support.md); this page applies it to bundles.

## Contents

1. [The official order](#the-official-order)
2. [What each needs in the bundle](#what-each-needs-in-the-bundle)
3. [Naming the database to the installers](#naming-the-database-to-the-installers)
4. [Which database for which bundle](#which-database-for-which-bundle)
5. [Things a bundle must not promise](#things-a-bundle-must-not-promise)

## The official order

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time. Details: [Database support](../database-support.md).

"All six are supported: every one of them has a driver in the kernel or in a shipped extension, an entry in the setup
wizard, and a path in the command-line installers" ([The supported databases, in order](../database-support.md#the-supported-databases-in-order)).

## What each needs in the bundle

| Database | `DatabaseImplementation` | PHP extension | Server in the bundle |
|---|---|---|---|
| 1. SQLite 3 | `sqlite3` | `sqlite3` | none: one file below `var/storage/sqlite3/` |
| 2. PostgreSQL | `ezpostgresql` | `pgsql` | yes (13 or later in practice) |
| 3. MongoDB | `mongodb` | `mongodb` plus the library `mongodb/mongodb`; PHP 8.2 or later | yes (6.0 or later) |
| 4. MariaDB | `ezmysqli` | `mysqli` | yes |
| 5. Oracle | `ezoracle` | `oci8` and the `ezoracle` extension (required by `composer.json`) | yes (19c or later) |
| 6. MySQL | `ezmysqli` | `mysqli` | yes |

From [At a glance](../database-support.md#at-a-glance) and
[Versions, checked against the code](../database-support.md#versions-checked-against-the-code). Use server versions
"your vendor still supports with security fixes".

## Naming the database to the installers

| Database | `exp:install --db=` | `kickstart.ini [database_choice] Type=` |
|---|---|---|
| SQLite 3 | `sqlite` (default) | `sqlite3` (default) |
| PostgreSQL | `pgsql` | `pgsql` |
| MongoDB | `mongodb` | `mongodb` |
| MariaDB | `mariadb` | `mysqli` |
| Oracle | `oracle` | `oci8` |
| MySQL | `mysql` | `mysqli` |

"None of the installers creates a database server's database or user. Create them first"
([Installer support per path](../database-support.md#installer-support-per-path)). Your bundle's first boot or your
panel does that.

> **Note**
> The MongoDB configuration step "can take one to three minutes" in the setup wizard; give a bundle's install step
> enough time, or use the console.

## Which database for which bundle

| Bundle | Database | Why |
|---|---|---|
| Demo, classroom, laptop, appliance with one machine | SQLite 3 | no server to install, secure or back up separately ([SQLite bundles](sqlite-bundles.md)) |
| Single-server image for a real site | SQLite 3 or PostgreSQL | SQLite for one machine; PostgreSQL when the recipient wants a server |
| Container platform with several replicas | PostgreSQL (or another server) | "Several containers serving one site need a database server ('PostgreSQL first')" ([Containers](../hosting/models/containers-docker-and-podman.md#the-database-and-the-one-writer-rule)) |
| Panel installer | what the panel provides; often MariaDB or MySQL, PostgreSQL where offered | the panel creates database and user ([Databases and users in panels](../hosting/panels/databases-and-users.md)) |
| Organization standardized on a database | that database | all six are supported |

## Things a bundle must not promise

- **Microsoft SQL Server.** "Microsoft SQL Server has no driver in Exponential 6.0.x"; it is "known to be possible"
  with additional work ([Database support](../database-support.md#microsoft-sql-server-possible-with-additional-work)).
- **Several web servers on one SQLite file.** "Several web servers on one database: no" for SQLite.
- **Untested combinations.** CI tests PostgreSQL 15 and MySQL 8.0 and the drivers' unit tests; test the database your
  bundle ships ([Testing a bundle](testing-a-bundle.md)).

## Related pages

- [Database support](../database-support.md)
- [Chapter 9: Databases](../../install/09-databases.md)
- [Databases in the official order, for hosts](../hosting/basics/databases-in-the-official-order.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
