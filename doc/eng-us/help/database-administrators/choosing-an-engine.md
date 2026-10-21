---
title: Choosing an engine
audience: dba
category: database-administration
order: 4
summary: A decision guide for database administrators - which of the six supported databases fits a given Exponential site, from one machine with SQLite to several web servers on PostgreSQL, MariaDB or MySQL, and when MongoDB or Oracle make sense, with the questions to ask and the costs of each choice.
keywords: choose database, which database, SQLite or PostgreSQL, MySQL or MariaDB, MongoDB, Oracle, decision, several web servers, replication, editorial load, writes, concurrency, comparison
type: help
---
# Choosing an engine

This page is for database administrators who must pick, or approve, the database of a new Exponential site. It
answers: which questions decide the choice, what each engine costs to run, and where each one stops fitting. It
condenses [9.7 Comparison](../../install/09-databases.md#97-comparison) and the limits sections of
[Database support](../database-support.md).

## Contents

1. [Five questions](#five-questions)
2. [The comparison](#the-comparison)
3. [Typical answers](#typical-answers)
4. [When SQLite stops fitting](#when-sqlite-stops-fitting)
5. [Before you choose MongoDB or Oracle](#before-you-choose-mongodb-or-oracle)
6. [Changing your mind later](#changing-your-mind-later)

## Five questions

1. **How many machines write the database?** One machine (any number of PHP processes) can use SQLite. Several web
   servers need a database server: PostgreSQL, MariaDB, MySQL, MongoDB or Oracle.
2. **How much is written at the same time?** SQLite queues writers one after another
   ([the write queue](sqlite-write-queue-and-locking.md)); the server engines lock rows.
3. **What does your organization already run and back up?** An engine your team already monitors, patches and
   restores is worth more than a small technical advantage.
4. **Do you need replication, point-in-time recovery or database users and grants?** SQLite has none of its own.
5. **Which extensions will the site use?** Extensions with their own SQL were usually written for MySQL; they may
   need work on PostgreSQL or Oracle and a separate path on MongoDB
   ([Reviewing extension SQL for every engine](portable-sql-for-extensions.md)).

## The comparison

| | SQLite 3 | MySQL / MariaDB | PostgreSQL | MongoDB | Oracle |
|---|---|---|---|---|---|
| Server to run | none | yes | yes | yes | yes |
| Concurrent writers | one at a time, queued | row locks (InnoDB) | row locks (MVCC) | document-level | row locks |
| Several application servers | no (one host) | yes | yes | yes | yes |
| Read replicas | no | built in (`UseSlaveServer`) | external | replica sets | external (Data Guard) |
| SQL query cache | yes | yes | yes | no | yes |
| Users and grants | file system | yes | yes | yes | yes |
| Known gaps | one host; queued writes | 4-byte characters through the `utf8` connection | | adapter limits | `''` is NULL; case-sensitive |

Source: [9.7 Comparison](../../install/09-databases.md#97-comparison).

## Typical answers

| Situation | Usual choice | Why |
|---|---|---|
| A site on one server, an editorial team, any number of readers | SQLite 3 | No server to run; WAL lets readers continue during writes; most page views are answered by caches |
| A site on several web servers, or many editors publishing large changes all day | PostgreSQL | The project's recommended server: strict SQL, row-level locking, replication and point-in-time recovery of its own, tested in CI |
| An organization standardized on MySQL or MariaDB, or many MySQL-only extensions | MariaDB or MySQL | The engine most extensions were written for; built-in read replicas |
| An organization standardized on MongoDB | MongoDB | Supported through the adapter; read its limits first |
| An organization standardized on Oracle | Oracle 19c or later with `ezoracle` | Connection pooling (DRCP), failover and tracing for an Oracle shop |
| A public demo rebuilt every few hours | SQLite 3 | Nothing to drop between builds ([9.10](../../install/09-databases.md#910-microsoft-sql-server)) |

## When SQLite stops fitting

From [Limits, and when to choose another engine](../../install/09-databases.md#limits-and-when-to-choose-another-engine):

- **More than one machine** must write the same database. WAL needs shared memory on one host; a network file system
  is not a substitute.
- **Many editors publish large changes at the same moment**, all day, so the queue is regularly longer than
  `SQLiteTransactionWait`.
- You need **replication**, a hot standby or point-in-time recovery from the database itself.
- You need **database users and grants**.

Database size is rarely the reason. SQLite handles databases far larger than an Exponential site's content.

## Before you choose MongoDB or Oracle

- **MongoDB**: the adapter never runs real SQL; hand-written SQL it cannot translate returns an empty result and logs
  `MONGO TODO`. Deep URL alias regeneration is incomplete, `eztags` is not usable, the shop checkout and `ezflow` are
  untested end to end, there are no SQL update files and the SQL query cache does not apply
  ([MongoDB: limits and operations](mongodb-limits-and-operations.md)).
- **Oracle**: needs Exponential 6.0.15 or later, Oracle 19c or later in `AL32UTF8`, `oci8` built against an Oracle
  client, and attention to `''` as NULL and case-sensitive comparison. Oracle is not in the project's CI
  ([Setting up Oracle with ezoracle](oracle-setup.md)).

## Changing your mind later

A site can move between the SQL engines through the `.dba` format, into Oracle with the extension's transfer commands
and into MongoDB with the scripts in `bin/mongodb/`. Plan a maintenance window
([Moving a site to another engine](moving-between-engines.md)).

## Related pages

- [Supported engines and versions](supported-engines-and-versions.md)
- [Performance and scaling](performance-and-scaling.md)
- [Databases in the official order](../hosting/basics/databases-in-the-official-order.md)
- [Database support](../database-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
