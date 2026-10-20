---
title: "Lesson 4.1: Taking stock of an old 4.x site"
audience: trainer, admin, developer
category: training
order: 41
summary: Learners find out what an old 4.x site really runs (version, datatypes, password hash types, charset and table type, cluster mode, search engine, size) and prepare it while it still runs its old code - consistency checks, leaving the database cluster, clean-ups, backups and a record of what works. 45 minutes.
keywords: migrating to exponential, 4.x, legacy kernel, take stock, ezsite_data, version, datatypes, charset, innodb, cluster, eZDBFileHandler, upgrade check, backup, preparing the old site
type: help
---
# Lesson 4.1: Taking stock of an old 4.x site

A site that still runs a 4.x release of the old legacy kernel on PHP 5 moves to Exponential 6 in place: the same
tables, the same datatypes, the same templates, brought forward. The work is in knowing exactly where the site
starts. This lesson teaches the stock-taking of chapter 14.3 and the preparation of chapter 14.4.

> **Note**
> Exponential 6 is the continuation of the eZ Publish legacy kernel, a product its creator discontinued after almost
> 18 years. The old name is used in this module only to identify the releases learners migrate from.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 4: From the 4.x line](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Administrators and developers who run the database and code tracks |
| Prerequisites | [Module 1](../module-01-planning-a-migration/README.md); a training installation with shell access and an administrator account |

## Learning objectives

After this lesson, learners can:

- read the version of the code and of the database and explain why they must agree;
- list the facts chapter 14.3 asks for, and the query or setting that gives each;
- name the preparation steps that must happen on the old site while it still runs its old code;
- explain why a site on the database cluster handler must leave it before the migration.

## Trainer's notes

### How to present it

Sources: [chapter 14.1](../../../../install/14-migrating-from-4x.md#141-what-changes-and-what-does-not),
[14.3](../../../../install/14-migrating-from-4x.md#143-take-stock-of-the-old-site) and
[14.4](../../../../install/14-migrating-from-4x.md#144-prepare-the-old-installation).

1. **What stays, what changes** (5 minutes). Show the first table of 14.1: the schema family, the datatypes, XmlText,
   ezflow pages, templates, the extension API and the `eZ` class names stay. What changes: PHP 5 to PHP 8 (the largest
   piece of work), Zeta Components, the removed cluster handlers, YUI gone, jQuery 4, the online editor, rewrite rules
   and a stricter security baseline.
2. **Which version** (3 minutes). The rows `ezpublish-version` and `ezpublish-release` of `ezsite_data` tell where
   the chain starts ([chapter 11.2](../../../../install/11-upgrading.md#112-which-version-do-you-run)). The row
   names keep their historic spelling on purpose.
3. **The facts** (6 minutes). Datatypes in use, password hash types, charset and table type on MySQL, cluster mode
   (`[ClusteringSettings] FileHandler`), search engine, sizes of the large tables and of `var/`.
4. **Prepare the old site** (6 minutes). Consistency checks with **Setup > Upgrade check**; leave the database
   cluster (`eZDBFileHandler` and `eZFS2FileHandler` were removed, and Exponential has no handler that can read
   `ezdbfile`); optional clean-ups; a backup restored once; a record of what works (URLs per siteaccess with their
   status, searches with result counts, sign-ins of the last month).

### Common questions

**Can a 3.9 site be migrated directly?** No. The update scripts of 3.0 to 3.9 are not in the repository; bring the
site to 3.10 with the old 3.10 release first ([14.5.1](../../../../install/14-migrating-from-4x.md#1451-which-starting-points-work)).

**Do we run an installer against the old database?** Never. The setup wizard, the kickstarter and `exp:install`
create a new database (chapter 14.2).

**Is `5.4.0alpha1` in the version row a problem?** No. The 5.x update files wrote those values; it is how they were
shipped.

### Pitfalls

- **Forgetting kernel changes.** Files someone changed inside the old kernel are not carried over; note them now so
  they can become extensions or overrides.
- **Skipping the record of what works.** Without it, chapter 14.12's verification has nothing to compare with.
- **Teaching on a production copy with real users.** Use the training installation, or an old demo database without
  personal data.

## Hands-on exercise

Use your own training installation as a stand-in for "the old site": the queries are the same on an Exponential 6
database. The default database is SQLite in `var/storage/sqlite3/exponential.db`.

1. Read the version of the code:

   ```bash
   php bin/php/console --version
   ```

2. Read the version of the database:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
   ```

3. Run the size queries of chapter 14.3 (objects, attributes, nodes, URL aliases, image files) with `sqlite3
   -readonly` in the same way, and note the numbers.
4. Read the cluster handler and the search engine your installation uses:

   ```bash
   php bin/php/console exp:ini get file.ini/ClusteringSettings/FileHandler
   php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
   ```

5. In the admin, open **Setup > Upgrade check**. Read the **Overview** (version, files in the manifest, database
   engine). The page says that both checks only read; press **Check file consistency** and wait for the result.
6. Write a "stock-taking sheet" with the headings of chapter 14.3: version, datatypes, hash types, charset and table
   type, cluster mode, search engine, size. Fill in what you found; write "MySQL only" for charset and table type on
   SQLite.
7. Add the preparation list of chapter 14.4 as a checklist below it, and mark which items you would do on the **old**
   site before the dump.

## Expected result

- Step 1 prints a line such as `console (Exponential) 6.0.15stable`; step 2 the rows `ezpublish-version` and
  `ezpublish-release`, and the two versions agree.
- Step 4 prints `eZFSFileHandler` on a default installation and the configured search engine.
- Step 5 shows the overview and the result of the file consistency check.
- The sheet has every heading of 14.3 filled in or explained, and the 14.4 checklist marks consistency checks,
  leaving the database cluster, clean-ups, backup and the record of what works as "old site".
- The trainer checks one sheet and asks why the database cluster must be left on the old site.

## Quiz

1. Which table and rows hold the database's version?
2. Which cluster handler cannot be read by Exponential 6 at all?
   A. `eZFSFileHandler` B. `eZDFSFileHandler` C. `eZDBFileHandler` D. none of them
3. Name three things chapter 14.1 says stay the same.
4. Why must you never run `exp:install` against the old database?
5. What do you record on the old site so that you can verify the migration later?

<details>
<summary>Answers</summary>

1. `ezsite_data`, rows `ezpublish-version` and `ezpublish-release`
   ([chapter 11.2](../../../../install/11-upgrading.md#112-which-version-do-you-run)).
2. **C.** It was removed; move the files out with the old installation's tools first
   ([chapter 14.4](../../../../install/14-migrating-from-4x.md#144-prepare-the-old-installation)).
3. Any three of: the database schema family, the datatypes, `ezxmltext`, the ezflow page datatype, templates, the
   extension API, `eZ` class names ([chapter 14.1](../../../../install/14-migrating-from-4x.md#141-what-changes-and-what-does-not)).
4. Installers create a new database ([chapter 14.2](../../../../install/14-migrating-from-4x.md#142-plan-the-migration-on-a-staging-copy)).
5. URLs per siteaccess with their HTTP status, searches with result counts, sign-ins of the last month (chapter 14.4).

</details>

## Further reading

- [Chapter 14: Migrating from the 4.x line](../../../../install/14-migrating-from-4x.md): the complete runbook.
- [Older releases of this kernel](../../../../install/migration/older-releases.md): which chapter fits which release.
- [Upgrade check](../../../../guides/upgrade-check.md): the two consistency checks in the admin.

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
