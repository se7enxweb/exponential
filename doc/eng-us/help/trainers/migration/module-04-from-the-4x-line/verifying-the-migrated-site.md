---
title: "Lesson 4.4: Verifying the migrated site"
audience: trainer, admin, developer
category: training
order: 44
summary: Learners verify a migrated site the way chapter 14.12 describes - schema against the reference, counts against the old database, URL aliases, images, the admin's Upgrade check, every siteaccess, editing, sign-in, cron jobs and logs - and recognise the common issues and their fixes. 45 minutes.
keywords: migrating to exponential, verify, verification, ezsqldiff, db_schema.dba, counts, verify_aliases, updateniceurls, upgrade check, database consistency, search index, cron, logs, common issues
type: help
---
# Lesson 4.4: Verifying the migrated site

A migration is finished when it is verified, not when the last script ends. This lesson teaches the ten checks of
chapter 14.12 and the table of common issues of chapter 14.13, so that learners can tell a missed step from a real
problem and know where the fix is.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 4: From the 4.x line](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 4.3](files-settings-and-code.md); an administrator account and shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- list the checks of chapter 14.12 and run the read-only ones;
- read the output of a schema comparison and say whether a line means a missed step;
- compare counts of the old and the migrated database;
- find the fix for five common issues in the table of chapter 14.13.

## Trainer's notes

### How to present it

Sources: [chapter 14.12](../../../../install/14-migrating-from-4x.md#1412-verify-the-content-after-the-migration)
and [14.13](../../../../install/14-migrating-from-4x.md#1413-common-issues).

1. **Schema against the reference** (3 minutes). `share/db_schema.dba` is the schema of the current release;
   `ezsqldiff.php` prints the SQL that would turn the database into the reference. Read the output, do not run it: a
   `CREATE TABLE` or `ADD` line names a missed step, a `DROP TABLE` line a table only your database has (often your
   own extension's). The admin's **Database consistency check** shows the same kind of SQL.
2. **Counts** (2 minutes). Objects, attributes and nodes must match exactly; `ezurlalias_ml` grows when aliases are
   rebuilt.
3. **Aliases, images, siteaccesses** (3 minutes). `updateniceurls.php --update-nodes`, then `verify_aliases.php`;
   open a page of each class with images; `eztemplatecheck.php -s<siteaccess>` and the URL list from lesson 4.1.
4. **Editing, users, cron, logs** (3 minutes). Create, edit and publish an object of each class with rich text,
   image and relation in every language; sign in as an editor with an old hash type and check that it changed to
   type 7; run the cron jobs; the logs stay quiet.
5. **Common issues** (4 minutes). Pick five rows of 14.13 and show cause and fix: duplicate entry on the remote id
   index, unknown column from a repair script, `ArgumentCountError` in a datatype constructor, `?` instead of accented
   characters, users who cannot sign in.

### Common questions

**The search finds nothing after the migration.** Rebuild the index rather than trust the old one:
`php bin/php/updatesearchindex.php --clean` (14.11). It is usually the longest single step, and can be finished
after the switch if search may be incomplete for a while (14.15).

**Can we run the SQL that `ezsqldiff.php` prints?** No. Repair by restoring and running the missed step (14.12).

**Stale pages after the switch?** Old caches, or a cache filled by old code before the reload:
`php bin/php/ezcache.php --clear-all --purge` after the PHP reload (14.13).

### Pitfalls

- **Pressing a "fix" option during the exercise.** `verify_aliases.php` without `--fix` only reports; learners use it
  that way here.
- **Comparing counts after rebuilding aliases.** The alias count grows; compare objects, attributes and nodes.
- **Skipping editing tests.** Many problems only show when someone publishes.

## Hands-on exercise

Use your own training installation and an administrator account. Every step only reads; nothing is
repaired or changed.

1. In the admin, open **Setup > Upgrade check** and press **Check database consistency**. Read the result: is there
   SQL that would bring the database in line? Do not run it.
2. In the installation root, check the URL alias tables and read the report (no `--fix`):

   ```bash
   php bin/php/verify_aliases.php --verbose
   ```

3. Count objects, attributes and nodes:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM ezcontentobject;"
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM ezcontentobject_attribute;"
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM ezcontentobject_tree;"
   ```

   Compare them with the numbers you noted in lesson 4.1.
4. Check the templates of your public siteaccess with `php bin/php/eztemplatecheck.php -s<siteaccess>`.
5. Look at the end of the error log: `tail -20 var/log/error.log` (and `var/<VarDir>/log/error.log` if it exists).
6. Open a page of your training site with an image and check that the image is shown.
7. In your notes, answer from the table of [chapter 14.13](../../../../install/14-migrating-from-4x.md#1413-common-issues):
   what is the cause and the fix of "Illegal mix of collations" on MySQL 8, "Call to undefined function each()",
   "Admin has no menus, YUI is not defined", "A template override no longer wins", and "Users cannot sign in"?
8. Write your own verification sheet: the ten checks of 14.12 as a checklist with a column "result".

## Expected result

- Step 1 shows the database consistency result; on a fresh installation there is little or no SQL.
- Step 2 reports the state of the alias tables without changing them.
- Step 3 gives the same numbers as in lesson 4.1, unless content was added since.
- Step 7: five causes and fixes, each with the section of chapter 14 that has the details.
- The trainer checks the answers of step 7 and one verification sheet.

## Quiz

1. `ezsqldiff.php` prints a `CREATE TABLE` line for a kernel table. What does it mean, and what do you do?
2. Which counts must match exactly between the old and the migrated database?
   A. Objects, attributes and nodes B. URL aliases only C. Sessions D. Cache files
3. How do you check that an old password hash was upgraded at sign-in?
4. Which command rebuilds the search index after a migration?
5. True or false: `verify_aliases.php` without `--fix` changes the alias tables.

<details>
<summary>Answers</summary>

1. A step was missed; restore and run the missed step, do not run the printed SQL
   ([chapter 14.12](../../../../install/14-migrating-from-4x.md#1412-verify-the-content-after-the-migration)).
2. **A.** `ezurlalias_ml` grows when aliases are rebuilt (chapter 14.12).
3. Sign in as an editor with an old hash type and check that `password_hash_type` changed to 7 (chapter 14.12).
4. `php bin/php/updatesearchindex.php --clean`
   ([chapter 14.11](../../../../install/14-migrating-from-4x.md#1411-search)).
5. **False.** `--fix` repairs what it can safely; without it the script reports (chapter 14.12).

</details>

## Further reading

- [Chapter 14.12: Verify the content after the migration](../../../../install/14-migrating-from-4x.md#1412-verify-the-content-after-the-migration):
  the ten checks.
- [Chapter 14.13: Common issues](../../../../install/14-migrating-from-4x.md#1413-common-issues): symptoms, causes and
  fixes.
- [Chapter 12: Troubleshooting](../../../../install/12-troubleshooting.md): more symptoms of running sites.

## Related pages

- [Module 4: From the 4.x line](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
