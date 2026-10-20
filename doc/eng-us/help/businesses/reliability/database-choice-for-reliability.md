---
title: Database choice for reliability
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 150
summary: Which database a business should run Exponential on, from the project's official order of preference, with what each choice means for writes under load, several web servers, backups, testing and the skills your team needs.
keywords: database, database choice, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, reliability, backups, replication, several web servers, CI
type: help
---
# Database choice for reliability

This page is for owners, managers and project leads who choose, or inherit, the database behind an Exponential site.
It explains the project's official order, what each choice means for a business's reliability, and how to decide.
The full technical comparison is [Database support](../../database-support.md); the operator's chapter is
[chapter 9 of the installation book](../../../install/09-databases.md).

## Contents

1. [The official order](#the-official-order)
2. [What the choice affects](#what-the-choice-affects)
3. [Each database in business terms](#each-database-in-business-terms)
4. [How well each one is tested](#how-well-each-one-is-tested)
5. [Backups per database](#backups-per-database)
6. [Choosing for your business](#choosing-for-your-business)
7. [Changing later](#changing-later)

## The official order

"**Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work at
this time" ([Database support](../../database-support.md#the-supported-databases-in-order), also in the
[README, "Requirements"](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements)).

The order is a recommendation for new sites. All six are supported; the page notes that MySQL "is last in the order,
not because it works less well, but because the project recommends the others first for new sites".

## What the choice affects

| Concern | Why it matters to a business |
|---|---|
| Writes under load | Many editors publishing, many orders at checkout, many sign-ups at once |
| Several web servers | Whether you can add servers later ([Clustering and high availability](clustering-and-high-availability.md)) |
| Backups and point-in-time recovery | How much data you can lose, and how fast you restore ([Backups that restore](backups-that-restore.md)) |
| Testing | How much of the project's own testing covers your choice |
| Skills | Whether your team or hosting partner knows how to run and repair it |
| Hosting | Whether your host offers and manages it |

## Each database in business terms

From [Database support](../../database-support.md#the-supported-databases-in-order) and
[Performance and scaling](../../database-support.md#performance-and-scaling):

| Database | In business terms |
|---|---|
| **1. SQLite 3** | The default. No database server: "the whole content repository is one file". A full production database for a site served from **one machine**. Reads scale; writes queue for a single lock, so "For many editors publishing at once use MySQL or PostgreSQL" ([Operating a site](../../../guides/operating-a-site.md#7-know-your-database-driver)) |
| **2. PostgreSQL** | "The recommended database server: strict SQL, row-level locking, replication and point-in-time recovery of its own, and it is tested in CI on every push" |
| **3. MongoDB** | Supported through an adapter; "the choice for organizations standardized on MongoDB". Read its [limits](../../database-support.md#mongodb-limits) first |
| **4. MariaDB** | "The MySQL-compatible server the project prefers"; same driver as MySQL |
| **5. Oracle** | Supported (19c and later) through the `ezoracle` extension, present in every installation |
| **6. MySQL** | Supported on the same driver as MariaDB, and tested in CI |

Microsoft SQL Server has no driver in Exponential 6.0.x; it is "known to be possible" with additional work
([Database support](../../database-support.md#microsoft-sql-server-possible-with-additional-work)).

Several web servers on one database: every engine except SQLite supports it
([Database support, at a glance](../../database-support.md#at-a-glance)).

## How well each one is tested

What the project's continuous integration runs on every push
([Database support, test coverage](../../database-support.md#test-coverage-ci)):

| Database | In the project's CI |
|---|---|
| SQLite 3 | Driver unit tests, on PHP 8.1 to 8.5 |
| PostgreSQL | Database tests against PostgreSQL 15 |
| MongoDB | Adapter unit tests without a server |
| MariaDB | Not directly; covered indirectly by the MySQL job (same driver) |
| Oracle | No |
| MySQL | Database tests against MySQL 8.0 |

For a business that wants the most project-side testing behind its database server, PostgreSQL and MySQL are the
engines with live database tests on every push. For the others, rehearse upgrades on a copy of your own site with
extra care ([Upgrades without surprises](upgrades-without-surprises.md)).

## Backups per database

Each engine has its own backup and restore tool; the commands are in
[Database support, backup and restore](../../database-support.md#backup-and-restore). In business terms:

- **SQLite**: an online, consistent backup with `.backup`; "never copy a database file that is in use".
- **PostgreSQL**: dumps, plus point-in-time recovery of its own for a finer recovery point.
- **MariaDB and MySQL**: `--single-transaction` dumps are consistent for InnoDB tables without locking them.
- **MongoDB**: `mongodump` and `mongorestore`.
- **Oracle**: Data Pump, through `ext:ezoracle:datapump` or Oracle's own tools.

Whatever the engine, the backup is only half; the files are the other half.

## Choosing for your business

| Your situation | A sensible starting point |
|---|---|
| A small site, one server, few editors | SQLite, the default; simplest to run and back up |
| A shop or member site with many writes at once, or several editors publishing all day | PostgreSQL, or MariaDB or MySQL if that is what your host knows best |
| You expect to need several web servers | Any engine except SQLite |
| Your organization standardized on MongoDB or Oracle | That engine, after reading its section in [Database support](../../database-support.md) |
| Your hosting partner manages one engine very well | Weigh that skill heavily: a well-run database beats a better one that nobody knows |

> **Tip**
> Ask your hosting partner which engine they back up, monitor and restore most often. Their experience is part of
> your reliability.

## Changing later

A site can be moved to another database; the procedure is in
[Database support, moving a site to another database](../../database-support.md#moving-a-site-to-another-database)
and, for hosts, [Changing the database engine](../../hosting/migrations/changing-the-database-engine.md). Treat it as
a project: back up first, rehearse on a copy, compare the content afterwards, and plan a maintenance window.

To see which database a site uses today, open **Setup > System information**; its Database card shows the engine
([System information](../../../guides/system-information.md)).

## Related pages

- [Database support](../../database-support.md)
- [Databases in the official order (hosting)](../../hosting/basics/databases-in-the-official-order.md)
- [Choosing hosting](../choosing/choosing-hosting.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
