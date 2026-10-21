---
title: Databases in the ecosystem
audience: ecosystem, admin, hosting, developer
category: ecosystem
order: 32
summary: The databases of the Exponential ecosystem in the project's official order - SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle and MySQL - what each is chosen for, what the CI tests, Microsoft SQL Server's status, and where the platform line differs.
keywords: databases, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, order of preference, CI, drivers
type: help
---
# Databases in the ecosystem

> "**Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time." ([Database support](../../database-support.md))

## Contents

1. [The six databases](#the-six-databases)
2. [Microsoft SQL Server](#microsoft-sql-server)
3. [The platform line](#the-platform-line)
4. [Where to read more](#where-to-read-more)

## The six databases

| # | Database | Chosen for | Server needed | Tested in CI |
|---|---|---|---|---|
| 1 | SQLite 3 | The default; "a full production database for a site served from one machine" | No: one file | Driver unit tests |
| 2 | PostgreSQL | "the recommended database server" | Yes | Yes (PostgreSQL 15) |
| 3 | MongoDB | "organizations standardized on MongoDB", through an adapter | Yes | Adapter unit tests |
| 4 | MariaDB | "the MySQL-compatible server the project prefers" | Yes | No |
| 5 | Oracle | 19c and later, through `ezoracle`, present in every installation | Yes | No |
| 6 | MySQL | Supported on the same driver as MariaDB | Yes | Yes (MySQL 8.0) |

Source: [Database support](../../database-support.md).

## Microsoft SQL Server

"Microsoft SQL Server has no driver in Exponential 6.0.x. It is known to be possible", and "An extension written for
the 4.x line, `se7enxweb/xrowmssql`, is the natural starting point for that work" ([Database support](../../database-support.md)).
A contribution opportunity for the ecosystem.

## The platform line

The Symfony distributions use their own database layer. The ecosystem's addition there is SQLite support: the DXP
"features improvements not found in Ibexa DXP OSS", starting with "The SQLite database driver and framework support"
(`README.md`); see [SQLite for the platform](../../../features/6.0/platform-sqlite-install.md).

## Where to read more

- For hosts, one page per database: [Hosting basics](../../hosting/basics/README.md).
- Changing engines on a running site: [Changing the database engine](../../hosting/migrations/changing-the-database-engine.md).
- The SQLite feature: [SQLite database](../../../features/6.0/sqlite-database.md); MongoDB:
  [MongoDB database support](../../../features/6.0/mongodb-database-support.md).

## Related pages

- [The PHP stack](README.md)
- [Database support](../../database-support.md)
- [Databases in the official order](../../hosting/basics/databases-in-the-official-order.md)
- [Talking points: hosting, databases and freedom](../../advocates/talking-points-hosting-databases-and-freedom.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
