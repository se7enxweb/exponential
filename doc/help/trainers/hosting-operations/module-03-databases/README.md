---
title: "Module 3: Databases"
audience: trainer, hosting, admin
category: training
order: 30
summary: The third module of Hosting and operations, choosing a database in the official order, SQLite in production, database servers for a site, database backups and moving between engines. Four lessons, 2 hours 55 minutes.
keywords: hosting and operations, module 3, databases, SQLite, PostgreSQL, MariaDB, MySQL, MongoDB, Oracle, backup, restore, switching engines
type: help
---
# Module 3: Databases

Exponential 6.x supports six databases, and a host must run, check and back up whichever one a plan includes. This
module starts with the project's official order and how to choose, spends a lesson on SQLite (the default, and a
production database for one machine), then covers database servers and ends with backups and moving a site between
engines.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Duration | 2 hours 55 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Hands-on; one Linux virtual machine with root per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [3.1 Choosing a database](choosing-a-database.md) | 40 min | Learn the official order, compare engines, find a site's database |
| [3.2 SQLite in production](sqlite-in-production.md) | 45 min | Run the monitoring checks, ANALYZE and a truncating checkpoint |
| [3.3 Database servers for a site](database-servers.md) | 45 min | Create a PostgreSQL or MariaDB database and install on it |
| [3.4 Database backups and moving between engines](database-backups-and-moves.md) | 45 min | Back up, check and restore a SQLite database |

## Preparing this module

- A training installation on SQLite per learner, and the `sqlite3` command-line program.
- For lesson 3.3: PostgreSQL or MariaDB installed on each learner's virtual machine, with the matching PHP extension
  (`pgsql` or `mysqli`) and a second copy of the code to install into. Decide in advance which engine the class uses.
- Throwaway training passwords only; never show a real password on the projector.
- Rehearse the restore of lesson 3.4 once on your own machine; it is the step learners remember.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 3.1 (40 min) |
| 0:40 | Lesson 3.2 (45 min) |
| 1:25 | Break (15 min) |
| 1:40 | Lesson 3.3 (45 min) |
| 2:25 | Lesson 3.4 (45 min) |
| 3:10 | End (2 h 55 min of lessons plus one break) |

## Related pages

- [Hosting and operations: course overview](../README.md)
- [Database support](../../../database-support.md)
- [Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
