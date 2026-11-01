---
title: "Module 9: The console and persistent objects"
audience: trainer, developer
category: training
order: 90
summary: The ninth module of the Developers course. Learners find and run commands with the console, write a command of their own, keep their extension's data in a table through eZPersistentObject, write the SQL that remains safely with eZDB, and ship a table as a migration. 4 hours 20 minutes.
keywords: developers course, module 9, console, bin/php/console, ext: commands, eZScript, eZPersistentObject, definition, fetchObjectList, eZDB, escapeString, transactions, migrations, exp:migration:migrate
type: help
---
# Module 9: The console and persistent objects

Real extensions run jobs on the command line and keep data of their own. This module teaches the console that runs
every command, a command of the learners' own with options and exit codes, a table mapped to a class with
`eZPersistentObject`, the rules for the SQL that persistent objects cannot express, and a migration that brings a
table to every installation of a site.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 4 hours 20 minutes (with two 10-minute breaks) |
| Lessons | 5 |
| Level | Beginner to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 9.1: The console](the-console.md) | 35 minutes | `list`, help, namespaces, cronjob parts |
| [Lesson 9.2: A command of your own](a-command-of-your-own.md) | 50 minutes | `ext:mysite_hello:latest` with options, help and exit codes |
| [Lesson 9.3: Persistent objects](persistent-objects.md) | 60 minutes | A table and the class `mysiteGreeting`: store, fetch, count, remove |
| [Lesson 9.4: SQL safely with eZDB](sql-safely-with-ezdb.md) | 45 minutes | A report, escaping, a transaction with rollback, dialects |
| [Lesson 9.5: Migrations](migrations.md) | 50 minutes | A table shipped as a migration: generate, check, dry run, simulate, run, roll back |

## Preparing this module

- Lessons 9.3 and 9.4 use SQLite syntax for the table; development installations from `exp:install` are on SQLite.
  For a class on MySQL or PostgreSQL, prepare the `CREATE TABLE` for that engine in advance.
- Learners run commands as their own user, not root.
- Keep finished copies of `latest.php`, `mysitegreeting.php` and the scripts for learners who fall behind.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 9.1 | 35 |
| Lesson 9.2 | 50 |
| Break | 10 |
| Lesson 9.3 | 60 |
| Lesson 9.4 | 45 |
| Break | 10 |
| Lesson 9.5 | 50 |
| **Total** | **260** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
