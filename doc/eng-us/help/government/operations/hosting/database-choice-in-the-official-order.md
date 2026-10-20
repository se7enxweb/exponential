---
title: Choosing a database in the official order
audience: government, hosting, admin, project-manager
category: public-sector
order: 50
summary: For public-sector architects and IT teams choosing the database of an Exponential site. The six supported databases in the project's official order, how they compare for availability, backups and procurement, and how to match the choice to your organization's database standard.
keywords: database, official order, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, database standard, procurement, replication, high availability, backup tools
type: help
---
# Choosing a database in the official order

This page is for architects and IT teams in public bodies who decide which database runs an Exponential site. It
answers which databases are supported, in which order the project recommends them, what each means for availability,
backups and procurement, and how to fit the choice to a database standard your organization may already have. The
support statement is [Database support](../../../database-support.md); the hosting view is
[Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md).

## Contents

1. [The official order](#the-official-order)
2. [Side by side for a public body](#side-by-side-for-a-public-body)
3. [Matching your database standard](#matching-your-database-standard)
4. [Which database for which situation](#which-database-for-which-situation)
5. [Finding out what a site runs on](#finding-out-what-a-site-runs-on)
6. [Changing later](#changing-later)
7. [Checklist](#checklist)

## The official order

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time.

The order is the order of recommendation for new sites; it does not mean the later ones work less well
([Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md)). Microsoft SQL Server
has no driver in 6.0.x ([Microsoft SQL Server status](../../../hosting/basics/microsoft-sql-server-status.md)); if your
organization only runs SQL Server, plan for that work or choose another engine for this application.

## Side by side for a public body

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL |
|---|---|---|---|---|---|---|
| Database server to run | none (one file) | yes | yes | yes | yes | yes |
| Several web servers on one database | no | yes | yes | yes | yes | yes |
| Replication of its own | none | yes, with point-in-time recovery | see vendor | see vendor | see vendor | see vendor |
| Read replicas used by Exponential | no | no | no | yes (`UseSlaveServer`) | no | yes (`UseSlaveServer`) |
| Backup tool | `sqlite3 .backup` | `pg_dump` | `mongodump` | `mariadb-dump` | `ext:ezoracle:datapump`, `expdp` | `mysqldump` |
| Tested in the project's CI | driver unit tests | yes | adapter unit tests | via the MySQL job | no | yes |

Sources: [Database support](../../../database-support.md) and the per-engine hosting pages. Read replicas are only
built in for the MySQL driver, which MariaDB shares; see [Read replicas](../availability/read-replicas.md).

## Matching your database standard

Many public bodies have a database standard set by central IT, with database administrators, licenses and backup
tooling already in place. That often decides more than technical preference:

| Your organization standardizes on | Consider | Note |
|---|---|---|
| PostgreSQL | PostgreSQL | the recommended database server; needs `pgcrypto` ([PostgreSQL for hosts](../../../hosting/basics/postgresql-for-hosts.md)) |
| MariaDB or MySQL | MariaDB, or MySQL | same driver; `utf8mb4` and InnoDB ([MariaDB for hosts](../../../hosting/basics/mariadb-for-hosts.md)) |
| Oracle | Oracle 19c or later | through the `ezoracle` extension and `oci8` ([Oracle for hosts](../../../hosting/basics/oracle-for-hosts.md)) |
| MongoDB | MongoDB | read its documented limits first ([MongoDB for hosts](../../../hosting/basics/mongodb-for-hosts.md)) |
| no database standard, one machine | SQLite 3 | nothing extra to run, license or patch |

Choosing the engine your database team already operates usually brings monitoring, backups and on-call support with
it. That is worth more than small differences in features.

## Which database for which situation

| Situation | Choice | Why |
|---|---|---|
| One site, one machine, small team | SQLite 3 | no database server; backups are a file operation done with SQLite's own tool |
| More than one web server must serve the site | a database server, PostgreSQL first | SQLite needs every process on one machine |
| Many editors publishing large changes all day | a database server | SQLite queues writers for one lock |
| A continuity duty that needs a standby database | PostgreSQL, or your standard server engine with its replication | SQLite has no replication of its own |
| Database-level users and grants are required by policy | a database server | SQLite's access control is the file system's |

## Finding out what a site runs on

One setting decides the engine:

```bash
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini
```

The values are `sqlite3`, `ezpostgresql` (or `pgsql`), `mongodb`, `ezmysqli` and, with its extension, `ezoracle`
([Operating a site](../../../../guides/operating-a-site.md)). The **Setup > System information** page shows the
database too ([System information](../../../../guides/system-information.md)).

## Changing later

A site can move to another engine; the hosting page
[Changing the database engine](../../../hosting/migrations/changing-the-database-engine.md) describes how. Treat it as
a planned change with its own backup and test restore. Starting with SQLite and moving to PostgreSQL when a second
web server is needed is a documented path.

## Checklist

- [ ] The chosen engine is one of the six; Microsoft SQL Server is not assumed to work.
- [ ] It matches your organization's database standard, or the exception is recorded.
- [ ] Availability needs (several web servers, a standby) are compatible with the engine.
- [ ] The backup tool of the engine is part of your backup plan.
- [ ] The database team knows the minimum versions in [Database support](../../../database-support.md).

## Related pages

- [Hosting](README.md) index of this folder
- [Database high availability](../availability/database-high-availability.md)
- [Backups per database engine](../backups-and-recovery/backups-per-database-engine.md)
- [9 Databases](../../../../install/09-databases.md) in the installation book
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
