---
title: "Databases: choosing and running one"
audience: software-engineer, admin
category: software-engineering
order: 200
summary: The six databases Exponential 6.0.x supports, in the project's order of preference - SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL - compared as an engineer compares them - driver, CI coverage, multi-node use, transactions, character sets and limits - with a decision table and the commands that show which one a site uses.
keywords: database, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, DatabaseImplementation, driver, CI, transactions, character set, multi-node, choice, decision, software engineer
type: help
---
# Databases: choosing and running one

This page is for engineers who choose the database for a project or review that choice. The support statement, with
versions, settings, backup and migration for each engine, is [Database support](../database-support.md); this page
condenses it into the questions an architecture decision needs.

## Contents

1. [The official order](#the-official-order)
2. [Compared for engineers](#compared-for-engineers)
3. [A decision table](#a-decision-table)
4. [What the choice changes in code](#what-the-choice-changes-in-code)
5. [Which database is a site using?](#which-database-is-a-site-using)

## The official order

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work.

All six have a driver in the kernel or a shipped extension, an entry in the setup wizard and a path in the command-line
installers. The order is a recommendation for new sites, not a ranking of quality ([Database support](../database-support.md#the-supported-databases-in-order)).

## Compared for engineers

| | SQLite 3 | PostgreSQL | MongoDB | MariaDB | Oracle | MySQL |
|---|---|---|---|---|---|---|
| `DatabaseImplementation` | `sqlite3` | `ezpostgresql` | `mongodb` | `ezmysqli` | `ezoracle` | `ezmysqli` |
| Driver class | `eZSQLite3DB` | `eZPostgreSQLDB` | `expMongoDB` | `eZMySQLiDB` | `eZOracleDB` | `eZMySQLiDB` |
| Server process | none (one file) | yes | yes | yes | yes | yes |
| Several web servers on one database | no | yes | yes | yes | yes | yes |
| Transactions | yes, writers serialized | yes | no: each operation commits on its own | yes | yes | yes |
| Tested in the project's CI | driver unit tests | yes (PostgreSQL 15) | adapter unit tests | no | no | yes (MySQL 8.0) |
| SQL query cache | yes | yes | not used | yes | yes (ezoracle hooks) | yes |

Source: [Database support, At a glance](../database-support.md#at-a-glance), [Database and SQL standards](../../standards/databases-and-sql.md)
and [The SQL query cache](../../bc/6.0/sql-query-cache.md).

## A decision table

| Situation | A good fit | Why |
|---|---|---|
| One machine, no database administrator | SQLite 3 | no server to run; a full production database for a site served from one machine |
| Several web nodes, or a team that wants the best-tested server | PostgreSQL | tested in CI on every push; strict SQL; replication and point-in-time recovery |
| The organization standardized on MongoDB | MongoDB | supported through an adapter; read its limits first ([MongoDB as the database](../../features/6.0/mongodb-database-support.md)) |
| A MySQL-compatible server is a given | MariaDB, then MySQL | same driver (`mysqli`); MySQL is tested in CI |
| The organization standardized on Oracle | Oracle 19c or later | through `ezoracle`, which `composer.json` requires |

Weigh the CI column: MariaDB and Oracle have no CI job in the kernel repository, so a regression specific to them is
found later ([Database and SQL standards, Known gaps](../../standards/databases-and-sql.md#known-gaps)).

## What the choice changes in code

- Kernel and extension code must work on every engine: escape and cast, pass limits as parameters, avoid one engine's
  functions without a fallback ([Writing portable SQL](writing-portable-sql.md)).
- Schema changes must ship for every driver ([Schema changes and migrations](schema-changes-and-migrations.md)).
- On MySQL and MariaDB, characters outside the Basic Multilingual Plane (emoji, for example) cannot pass through the
  connection even with `utf8mb4` tables ([Internationalization standards](../../standards/internationalization.md)).
- The MongoDB adapter needs PHP 8.2 or later.

## Which database is a site using?

```bash
./console exp:ini where site.ini/DatabaseSettings/DatabaseImplementation
```

The administration shows it under Setup > System information. Moving a site to another engine is described in
[Database support, Moving a site to another database](../database-support.md#moving-a-site-to-another-database) and
[Changing the database engine](../hosting/migrations/changing-the-database-engine.md).

## Related pages

- [Chapter 9 of the installation guide: Databases](../../install/09-databases.md)
- [Persistence and the database layer](persistence-and-the-database-layer.md)
- [Databases in the official order, for hosts](../hosting/basics/databases-in-the-official-order.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
