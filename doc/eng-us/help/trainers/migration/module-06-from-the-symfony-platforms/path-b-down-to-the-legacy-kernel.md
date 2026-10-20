---
title: "Lesson 6.3: Path B: down to the Exponential 6 legacy kernel"
audience: trainer, admin, developer
category: training
order: 63
summary: Learners plan the move of an eZ Platform or Ibexa database down to the Exponential 6 legacy kernel - what the two share and what is lost and gained, field types without a datatype, the six database steps with ezsqldiff in the right direction, and keeping a Symfony stack next to it with the legacy bridge. 60 minutes.
keywords: migrating to exponential, path B, legacy kernel, Exponential Platform Legacy, legacy bridge, ezsqldiff, db_schema.dba, field types, ezrichtext, ezimageasset, ezlandingpage, version rows, ibexa table names, database translator
type: help
---
# Lesson 6.3: Path B: down to the Exponential 6 legacy kernel

The content model of every platform generation is the legacy kernel's, so a Symfony-based site can move down to
Exponential 6. Path B rewrites the presentation and the custom code; the content tables are shared. This lesson
teaches what the two share, what is lost and gained, the field types that need a decision, and the six database
steps of chapter 16.6.4, with the one command whose direction is easy to get wrong.

> **Note**
> Every database step in this lesson is rehearsed on a copy. Learners read and plan; the only commands they run are
> read-only.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 6: From the Symfony-based platforms](README.md) |
| Duration | 60 minutes (25 presenting, 35 exercise) |
| Level | Advanced |
| Audience | Developers and administrators |
| Prerequisites | [Lesson 6.1](two-destinations-and-choosing.md); [Module 4](../module-04-from-the-4x-line/README.md) for the legacy database background |

## Learning objectives

After this lesson, learners can:

- explain which tables the platform generations share with the legacy kernel and which they dropped or renamed;
- list what path B loses and gains;
- decide what to do with five field types that have no legacy datatype;
- order the six database steps and run `ezsqldiff.php` with the reference schema first;
- describe how a Symfony stack can keep running next to the legacy kernel.

## Trainer's notes

### How to present it

Source: [chapter 16.6](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#166-path-b-down-to-the-exponential-60-legacy-kernel).

1. **What the two share** (4 minutes). Content, classes, locations, versions, URL aliases, roles, sections, states,
   languages and users are the same tables with the same meaning. Shop, workflow, collaboration and other legacy
   tables were dropped by the vendor's 3.0 step (about 80 tables); 5.0 renamed the core tables to `ibexa_*` (16.6.1).
2. **Lose and gain** (4 minutes). Show the table of 16.6.2: lose the Symfony admin, Twig, REST v2 and GraphQL, Page
   Builder; gain the legacy admin, TPL with overrides, legacy workflows, information collection, shop, newsletters,
   Exponential Layouts, Velocity, SQLite in production.
3. **Field types** (5 minutes). From 16.6.3: `ezrichtext` (convert, or keep and adapt the output); `ezmatrix` of 2.5
   and later has a different format; `ezimageasset` changes to `ezobjectrelation` (both keep the target id in
   `data_int`); `ezcontentquery` becomes a template fetch; `ezlandingpage` and `ezform` are rebuilt with Exponential
   Layouts and information collection.
4. **Six database steps** (8 minutes). Back up and stop the Symfony stack; on 5.0, take the table names back (reverse
   renames, or the database translator extension); make the schema the 6.0.15 schema with `ezsqldiff.php`; give
   `ezkeyword_attribute_link.version` a default; insert the version rows; settings, storage with the same `VarDir`,
   caches and a clean search index.
5. **The direction trap** (2 minutes). `ezsqldiff.php` prints the SQL that turns the **second** argument into the
   **first**. Reference schema first, your database second. Sanity check: `DROP TABLE` lines may only name
   Symfony-side tables; if they list `ezbasket` or `ezworkflow`, the arguments are the wrong way round.
6. **Both stacks** (2 minutes). Platform Legacy and the older Nexus lines run both kernels on one database through the
   legacy bridge (16.6.8). Remove the `DROP` lines while the Symfony stack may still be needed.

### Common questions

**Can we read the old `ezpublish-version` row of a 2.5 database as a legacy version?** No. The Symfony kernel's update
files write their own kernel version into it; do not start the legacy chain of chapter 11 from it (16.6.4, step 5).

**Does SQLite work as a source?** `ezsqldiff.php` speaks MySQL and PostgreSQL only; convert first, or move the content
at the application level (16.6.4).

**What about emoji in content?** The legacy kernel's default connection charset on MySQL is the three-byte `utf8`,
which cannot carry characters outside the Basic Multilingual Plane. Check content for them before the move (16.6.4).

### Pitfalls

- **Running the `ezsqldiff.php` output unread.** It contains `DROP` statements; read it, keep what you need, then run
  it without `--force`.
- **Forgetting the keyword link default.** The legacy kernel's keyword inserts then fail in strict SQL mode.
- **Rebuilding nothing.** Clear the caches and rebuild the search index with `--clean`; it is cheaper than checking.

## Hands-on exercise

Part 1 is read-only on your training installation; part 2 is a planning exercise with chapter 16.6 open.

1. In the installation root, show the help of the schema comparison tool and find the options for the two schemas
   and for `--check-only`:

   ```bash
   php bin/php/ezsqldiff.php --help
   ```

   (On a machine where you work as root, the script asks for `--allow-root-user`.)
2. Count the table entries in the reference schema file:

   ```bash
   grep -c "^  '[a-z_0-9]*' => *$" share/db_schema.dba
   ```

   The file has one entry per table plus one `_info` entry; chapter 16.6.4 names 128 tables.
3. Show the version rows your training database carries:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
   ```

4. Planning: an Ibexa 4.6 site on MySQL 8 has `ezrichtext` in four content types, 120 fields of `ezimageasset`, three
   landing pages (`ezlandingpage`) and uses keywords. For each field type, write the decision from
   [16.6.3](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1663-field-types).
5. Write the six database steps of
   [16.6.4](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1664-the-database-step-by-step) for this
   site, with the `ezsqldiff.php` command (made-up values for host, user and password) and the two `grep` sanity
   checks.
6. Write the three `ezsite_data` statements of step 5 and say why the old row is deleted first.
7. The editors want to keep the Symfony admin for three months. Write which product line runs both kernels for the
   4.6 generation and which bridge branch it uses
   ([16.6.8](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1668-keeping-a-symfony-stack-next-to-the-legacy-kernel)).

## Expected result

- Step 1 shows the options; step 3 shows `6.0.15stable` and `1` on a current training installation.
- Step 4: RichText converted (or kept and the output adapted); `ezimageasset` changed to `ezobjectrelation`; landing
  pages rebuilt with Exponential Layouts and the field removed; keywords need the `ezkeyword_attribute_link.version`
  default.
- Step 5: `share/db_schema.dba` first, the database second; `grep -c '^CREATE TABLE'` large, `grep '^DROP TABLE'`
  only Symfony-side tables, which are removed while the Symfony stack may be needed.
- Step 7: Platform Legacy `v4.6.23.x` (or Nexus `1.2.0.x`), bridge branch `4.x`.
- The trainer checks the order of step 5 and the direction of the `ezsqldiff.php` command on every sheet.

## Quiz

1. `ezsqldiff.php A B` prints SQL that turns which schema into which?
2. Your diff output contains `DROP TABLE ezbasket;`. What went wrong?
   A. Nothing B. The arguments are the wrong way round C. The database is SQLite D. The schema file is old
3. Which field type keeps its value when changed to `ezobjectrelation`, and why?
4. What must the column `ezkeyword_attribute_link.version` get after the MySQL 2.5 to 3.0 script, and why?
5. Name two things path B gains and two things it loses.

<details>
<summary>Answers</summary>

1. It turns the second (`B`) into the first (`A`)
   ([chapter 16.6.4](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1664-the-database-step-by-step)).
2. **B.** `DROP TABLE` lines may only list Symfony-side tables (chapter 16.6.4).
3. `ezimageasset`: both keep the id of the target content item in `data_int`
   ([chapter 16.6.3](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1663-field-types)).
4. A default of 0, because the legacy kernel inserts keyword links without it and the insert fails in strict SQL mode
   (chapter 16.6.1 and 16.6.4, step 4).
5. Gains, any two of: the legacy admin, TPL with overrides, legacy workflows, information collection, shop,
   newsletters, Exponential Layouts, Velocity, SQLite in production. Losses, any two of: the Symfony admin, Twig,
   REST v2 and GraphQL, Page Builder, Form Builder
   ([chapter 16.6.2](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1662-what-you-lose-and-what-you-gain)).

</details>

## Further reading

- [Chapter 16.6: Path B](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#166-path-b-down-to-the-exponential-60-legacy-kernel):
  the full path with configuration and templates.
- [Exponential Platform Legacy](../../../../install/migration/exponential-platform-legacy.md): the hybrid, both
  directions.
- [Legacy bridge](../../../../features/6.0/legacy-bridge.md): running both kernels on one database.

## Related pages

- [Module 6: From the Symfony-based platforms](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
