---
title: "Lesson 4.2: The database runbook: preflight, phase A and phase B"
audience: trainer, admin, developer
category: training
order: 42
summary: Learners plan the in-place database migration of an old site - charset and table type first, the preflight queries, the SQL update files in order (phase A), then the repair scripts against the finished schema (phase B) - and know why the two-phase order is the one that works with today's code. 60 minutes.
keywords: migrating to exponential, database update, dbupdate, update files, preflight queries, phase A, phase B, repair scripts, ezconvertdbcharset, ezconvertmysqltabletype, checkdbfiles, remote id, runbook
type: help
---
# Lesson 4.2: The database runbook: preflight, phase A and phase B

The database of an old site is not exported and re-imported; it is brought forward by the update files of the
repository. This lesson teaches the runbook of chapter 14.5 in the order that works with today's single code base,
and lets learners plan it for a given starting version.

> **Note**
> The runbook is always rehearsed on a restored copy, never on the production database. In this lesson learners
> plan it and run only read-only checks.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 4: From the 4.x line](README.md) |
| Duration | 60 minutes (25 presenting, 35 exercise) |
| Level | Advanced |
| Audience | Administrators and developers who run the database track |
| Prerequisites | [Lesson 4.1](taking-stock-of-the-old-site.md); basic SQL; shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- put the steps of the database runbook in their order: charset and table type, preflight, phase A, phase B;
- list the SQL update files from a given starting version to 6.0.15;
- explain why the repair scripts run after the whole schema (phase B), with one exception;
- run the preflight queries and say what to do when they return rows;
- check that the tree of update files is complete with `exp:checkdbfiles`.

## Trainer's notes

### How to present it

Source: [chapter 14.5](../../../../install/14-migrating-from-4x.md#145-the-database-step-by-step).

1. **Charset and table type first** (4 minutes). On MySQL: InnoDB with `ezconvertmysqltabletype.php`, UTF-8 with
   `ezconvertdbcharset.php`; set the database default charset to the one of the converted tables before the update
   files create new tables (14.5.2).
2. **Preflight** (5 minutes). Three statements of the old chain fail on data older releases allowed: duplicate object
   remote ids, duplicate digest addresses, users without an `ezuser_setting` row. Each query must return no rows, or
   its fix is planned (14.5.3).
3. **Two phases** (6 minutes). The repair scripts run on the 6.0 kernel, whose persistent object definitions describe
   the current schema. So phase A applies every SQL file in order, phase B runs the repair scripts against the
   finished schema. The one exception: `updateimagesystem.php` runs before the 4.1 file, and only if `ezimage` has
   rows (14.5.4).
4. **Phase A** (5 minutes). Show the table of 14.5.5: one file per step, from the file after your version up to
   `6.0/dbupdate-6.0.0-6.0.15.sql`. Stop at the first error (`mysql` stops by itself; `psql -v ON_ERROR_STOP=1`).
   Patch-release markers `-- START: from 4.0.1` hold statements a later patch release already shipped.
5. **Phase B** (5 minutes). Show the table of 14.5.6. Dry runs first; `addlockstategroup.php` exactly once;
   `deduplicatecontentstategrouplanguage.php` is required for sites that passed 5.0.

### Common questions

**What if a file fails halfway?** Note the statement, restore the backup, fix the cause (usually a preflight case)
and start again from the backup (14.5.5). Never run a file a second time to repair a half-done run (14.14).

**Is there a chain for SQLite or MongoDB?** No. SQLite support begins with 6.0.1, MongoDB with 6.0.14: migrate on
MySQL or PostgreSQL; moving engines afterwards is a separate project.

**And Oracle?** The `ezoracle` extension carries the chain up to 5.3; the later steps are written by hand from the
MySQL files. Chapter 14.5.5 advises rehearsing this path twice as often.

**What does "Unknown column 'priority'" from a repair script mean?** A script ran against an intermediate schema;
phase A first, then phase B (14.13).

### Pitfalls

- **Old copies of update files.** Copies from before October 2026 started with `SET storage_engine`, which current
  MySQL and MariaDB reject. Take the current files.
- **Interleaving scripts and files** as the old per-version pages did. With today's code it fails.
- **Skipping the record of times.** Write down how long each file and script took; the cut-over window follows from
  it.

## Hands-on exercise

Part 1 is read-only on your training installation; part 2 is a planning exercise.

1. In the installation root, list the update directories for MySQL and their files:

   ```bash
   ls update/database/mysql/
   ls update/database/mysql/4.1/ update/database/mysql/6.0/
   ```

2. Check that the tree of update files is complete:

   ```bash
   php bin/php/console exp:checkdbfiles --no-verify-branches
   ```

3. List the repair scripts of the 5.x steps:

   ```bash
   ls update/common/scripts/5.0/ update/common/scripts/5.4/
   ```

4. Run the three preflight queries of [chapter 14.5.3](../../../../install/14-migrating-from-4x.md#1453-preflight-queries)
   against your training database with `sqlite3 -readonly var/storage/sqlite3/exponential.db` (the digest table may
   not exist on a fresh installation; note the message if it does not).
5. Planning: an old site runs **4.3** on MySQL 5.5 with MyISAM tables in `latin1`, and `SELECT COUNT(*) FROM ezimage;`
   returns 0. Write its runbook:
   - the charset and table-type steps with their commands;
   - the preflight queries;
   - phase A: every SQL file in order, from the first one after 4.3 to 6.0.15;
   - phase B: every repair script of the versions it passes, in order, with the dry runs.
6. Mark the steps where the run must stop on an error and what you do then.

## Expected result

- Step 1 shows the directories `4.0` to `5.4`, `6.0`, and the historic `6.12` and `7.3`; the `6.0` directory holds
  `dbupdate-5.4.0-6.0.0.sql` and `dbupdate-6.0.0-6.0.15.sql`.
- Step 2 prints nothing when the tree is complete.
- Step 4: the queries return no rows on a fresh installation.
- Step 5: phase A starts with `4.4/dbupdate-4.3.0-to-4.4.0.sql` and ends with `6.0/dbupdate-6.0.0-6.0.15.sql`;
  `updateimagesystem.php` is skipped because `ezimage` is empty; phase B starts with
  `4.5/updatesectionidentifier.php` (the 4.4 and 4.5 copies are identical) and includes the 5.0
  `deduplicatecontentstategrouplanguage.php`.
- The trainer checks one runbook against the tables of 14.5.5 and 14.5.6.

## Quiz

1. Why do the repair scripts run after all SQL files, and not after each file?
2. Which script, if needed, must run **before** the 4.1 SQL file?
   A. `addlockstategroup.php` B. `updateimagesystem.php` C. `createaudittables.php` D. `fixorphanimages.php`
3. What does `exp:checkdbfiles --no-verify-branches` print when the tree of update files is complete?
4. A preflight query finds duplicate object remote ids. What is the documented way through?
5. True or false: SQLite has an update chain from 4.x.

<details>
<summary>Answers</summary>

1. The scripts run on the 6.0 kernel, whose definitions describe the current schema
   ([chapter 14.5.4](../../../../install/14-migrating-from-4x.md#1454-two-phases-schema-first-data-repair-second)).
2. **B**, and only if `ezimage` has rows (chapter 14.5.4).
3. Nothing: silence means the tree is complete
   ([chapter 14.5.5](../../../../install/14-migrating-from-4x.md#1455-phase-a-the-sql-files)).
4. Run the 4.1 file without the unique index line, finish the chain, run `fixobjectremoteid.php --mode=a`, then add
   the index ([chapter 14.5.3](../../../../install/14-migrating-from-4x.md#1453-preflight-queries)).
5. **False.** SQLite support begins with 6.0.1; migrate on MySQL or PostgreSQL (chapter 14.5.5).

</details>

## Further reading

- [Chapter 14.5: The database, step by step](../../../../install/14-migrating-from-4x.md#145-the-database-step-by-step):
  the full runbook with every file and script.
- [Chapter 11: Upgrading](../../../../install/11-upgrading.md): the update chain and the 6.0.x steps.
- [Chapter 17.9: Migration and upgrade tooling](../../../../install/17-migration-reference.md#179-migration-and-upgrade-tooling):
  every script with its options.

## Related pages

- [Module 4: From the 4.x line](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
