---
title: Database choice
audience: government, admin, project-manager
category: public-sector
order: 140
summary: The six databases Exponential supports, in the project's official order (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), and what matters to a public body when choosing one: licensing, existing skills, operations, scale and the ability to move later.
keywords: database, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, database choice, official order, sovereignty, migration
type: help
---
# Database choice

This page is for IT managers and administrators of a public body who choose the database under Exponential. The
project's statement and the details per engine are in [Database support](../../database-support.md); this page adds the
questions public bodies ask.

## Contents

1. [The official order](#the-official-order)
2. [Questions for a public body](#questions-for-a-public-body)
3. [Typical choices](#typical-choices)
4. [Changing later](#changing-later)

## The official order

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time.

All six have a driver in the kernel or a shipped extension, an entry in the setup wizard and a path in the
command-line installers ([Database support](../../database-support.md#the-supported-databases-in-order)).

| Engine | In short |
|---|---|
| SQLite 3 | the default; no database server, the content repository is one file; for a site served from one machine |
| PostgreSQL | the recommended database server; tested in CI on every push |
| MongoDB | through an adapter; read its limits first |
| MariaDB | same driver as MySQL; the MySQL-compatible server the project prefers |
| Oracle | 19c and later through the `ezoracle` extension |
| MySQL | supported and tested; last in the order for new sites |

## Questions for a public body

| Question | Why it matters |
|---|---|
| Which database does our IT already run and back up? | Existing skills and backup tooling count more than small differences |
| Is the engine's license acceptable under our policy? | Some bodies prefer free software servers |
| One machine or several web nodes? | SQLite suits one machine; several nodes need a database server |
| Does our cloud offer a managed service of that engine and version? | Check the version against the requirements |
| How do we restore, and how fast? | Test it (see "Exit planning") |

## Typical choices

- A small site on one server, no database team: SQLite 3.
- A central IT with a standard database server: PostgreSQL, or the MariaDB/MySQL or Oracle the IT already runs.
- Several web nodes: a database server, not SQLite.

These are starting points, not rules; the hosting help compares the engines for hosts
([Databases in the official order](../../hosting/basics/databases-in-the-official-order.md)).

## Changing later

A site can move between SQL engines with an engine-neutral dump and load, and into Oracle or MongoDB with their own
scripts; plan a maintenance window and compare row counts afterwards
([Moving a site to another database](../../database-support.md#moving-a-site-to-another-database)). The choice is
therefore not permanent, which is itself a guard against lock-in.

## Related pages

- [Database support](../../database-support.md)
- [9. Databases](../../../install/09-databases.md)
- [Changing the database engine](../../hosting/migrations/changing-the-database-engine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
