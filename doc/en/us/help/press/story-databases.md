---
title: "Story: six databases"
audience: press
category: press-media
order: 68
summary: A feature story about the six databases Exponential supports - SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle and MySQL, in that official order - what each is for, how they are tested, and the limits, with printable facts and sources.
keywords: story, databases, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, database abstraction, press
type: help
---
# Story: six databases

This page is for technology journalists who write about databases and about content management systems that run on
more than one. Exponential documents six, in an official order of preference. This page gives that order, what the
sources say about each, and how they are tested.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [The six, in order](#the-six-in-order)
3. [What is new, and when](#what-is-new-and-when)
4. [Facts you can print](#facts-you-can-print)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"Supported databases (Exponential 6.0.x), in order of preference: 1. SQLite 3 (the default) · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work at
this time" ([README, Requirements](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements)). The order is a statement in itself: a server-free
file database first, a document database third, and MySQL, long the usual choice for PHP applications, last. One
command installs a site on five of them ([Install in one command](../../features/6.0/install-in-one-command.md#choose-a-database)).

## The six, in order

| # | Database | What the sources say | Source |
|---|---|---|---|
| 1 | SQLite 3 | The default; one file, no server | [Database support](../database-support.md#1-sqlite-3-the-default) |
| 2 | PostgreSQL | The recommended database server | [Talking points: hosting](../advocates/talking-points-hosting-databases-and-freedom.md#the-databases-in-one-line-each) |
| 3 | MongoDB | Supported since 6.0.14, with documented limits | [MongoDB as the database](../../features/6.0/mongodb-database-support.md) |
| 4 | MariaDB | Supported; shares one driver with MySQL | [Database support, At a glance](../database-support.md#at-a-glance) |
| 5 | Oracle | Supported through the `ezoracle` extension | same |
| 6 | MySQL | Supported; shares one driver with MariaDB | same |

## What is new, and when

| What | When | Source |
|---|---|---|
| SQLite arrives as a database that needs no server | January 2024 | [January 2024](../../history/2024/2024-01a.md) |
| MongoDB 8 as a database, in 6.0.14 | June 2026 (6.0.14 on 7 June) | [June 2026](../../history/2026/2026-06a.md) |
| Driver and installer fixes for all engines; SQLite is the setup wizard's default | 16 to 30 September 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#16-to-30-september-2026) |
| Oracle 11g and 12.1 (ezoracle 2.3.7) | 14 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#14-october-2026-oracle-11g-and-121-ezoracle-237) |

The September and October changes belong to the 6.0.15 line, which is in development and not released.

## Facts you can print

| Fact | Source |
|---|---|
| The official order is SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL. | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements) |
| The project's CI runs database tests on PostgreSQL 15 and MySQL 8.0; SQLite and MongoDB have driver or adapter unit tests; MariaDB and Oracle are not in CI. | [Database support, At a glance](../database-support.md#at-a-glance) |
| SQLite cannot be shared by several web servers; the five servers can. | same |
| Documented tools move a site from one database to another. | [Moving a site](../database-support.md#moving-a-site-to-another-database) |
| Microsoft SQL Server is "possible with additional work", not one of the six. | [Database support](../database-support.md#microsoft-sql-server-possible-with-additional-work) |

## What not to claim

- "Every feature works on every database." MongoDB has a documented list of limits, and `eztags` is not usable on it
  ([MongoDB, What works and what does not](../../features/6.0/mongodb-database-support.md#what-works-and-what-does-not-june-2026)).
- "Tested on all six in CI." Only PostgreSQL and MySQL run full database tests in CI.
- "Supports SQL Server." It does not, today.
- Any performance ranking of the databases: the sources publish none.

## Angles and headline ideas

```text
"MySQL last: Exponential's surprising database order"
"Six databases, one content model"
"From one file to Oracle: choosing a database for an open source CMS"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Why this order, plans for more | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Real-world database choices | the community | <https://share.exponential.earth/forums> |

## Related pages

- [Database support](../database-support.md)
- [Story: SQLite by default](story-sqlite-by-default.md)
- [Story: MongoDB as a database](story-mongodb.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
