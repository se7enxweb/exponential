---
title: "Migrating to Exponential: course overview"
audience: trainer, admin, developer
category: training
order: 1
summary: A trainer's course on migrating sites to Exponential 6 - planning, mapping, import and export tools, the in-place paths from the 4.x line and the 5.x legacy stack, the two paths from eZ Platform and Ibexa, WordPress, Drupal, Joomla and static HTML, leaving Exponential, and a capstone plan. Eight modules, 25 lessons, about 22.5 hours of lessons.
keywords: migrating to exponential, migration course, course overview, reading path, schedule, migration planning, content mapping, xrowextract, 4.x, 5.x, eZ Platform, Ibexa, Nexus, Platform Legacy, WordPress, Drupal, Joomla, static HTML, leaving exponential, capstone
type: help
---
# Migrating to Exponential: course overview

This course teaches administrators, developers and project managers how to move a website to Exponential 6, and how
to plan such a project so that no content, address or user is lost. It teaches methods and planning; the facts it
relies on live in the migration chapters of the installation book and in the migration hub, which every lesson links
rather than copies.

Exponential 6 is the continuation of the eZ Publish legacy kernel, a product its creator discontinued after almost 18
years. The old name appears in this course only to identify the releases learners migrate from.

## Who it is for

- **Administrators** who run the database, files and settings of a migration.
- **Developers** who write imports, port extensions to PHP 8 or rewrite templates.
- **Project managers** who plan the phases, the freeze, the cut-over and the rollback (modules 1, 2 and 8).

After the course, learners can choose the right migration path for a site, take an inventory, write a mapping and a
redirect table, use Exponential's import and export tools, plan an in-place migration from an older release of the
kernel or a move from a Symfony-based platform or another system, and write a complete migration plan with a
rollback.

## At a glance

| | |
|---|---|
| Duration | About 22.5 hours of lessons in 8 modules (25 lessons), plus breaks |
| Format | Classroom or online; every lesson has a presentation part and a hands-on exercise |
| Level | Intermediate to advanced |
| Prerequisites | Working knowledge of a CMS; shell use; SQL basics for modules 4 to 6; PHP for lessons 3.3 and 4.3; Twig and TPL basics for lesson 5.3 |
| Lab | One training installation of Exponential 6 per learner, with shell access and an administrator account |

## Lab setup

- One training installation per learner, installed with `php bin/php/console exp:install`
  ([The console install](../../../install/07-console-install.md)). The default installation uses SQLite in
  `var/storage/sqlite3/exponential.db`, which learners open read-only with `sqlite3 -readonly`.
- The extensions the root `composer.json` requires, in particular xrowextract, active on the training installation.
- The `sqlite3`, `curl` and `unzip` command line tools on the training machines.
- Each learner has a course folder in the content tree and knows its node id.
- No real customer data on any training installation. Test accounts use addresses at `example.invalid`.
- Module 6 is a planning module: no eZ Platform, Ibexa or Nexus installation is needed.
- The trainer's guidance on labs: [Train the trainer](../train-the-trainer/README.md), module 2.

## Modules

| Module | Duration | Lessons | What it covers |
|---|---|---|---|
| [1. Planning a migration](module-01-planning-a-migration/README.md) | 3 h 15 min | 4 | The Exponential family and paths, inventory and scope, phases and checklist, rehearsal, cut-over and rollback |
| [2. Mapping content to Exponential](module-02-mapping-content/README.md) | 2 h 45 min | 3 | Classes, datatypes and the tree; users, roles and passwords; URLs, redirects and metadata |
| [3. Import and export tools](module-03-import-and-export-tools/README.md) | 2 h 45 min | 3 | Choosing the tool, xrowextract dry runs and repeatable imports, a custom import script |
| [4. From the 4.x line](module-04-from-the-4x-line/README.md) | 3 h 30 min | 4 | Taking stock, the database runbook, files, settings and PHP 8 porting, verification |
| [5. From the 5.x legacy stack](module-05-from-the-5x-legacy-stack/README.md) | 2 h 45 min | 3 | Variants and the shared database, extraction and INI, field types, image aliases and Twig |
| [6. From the Symfony-based platforms](module-06-from-the-symfony-platforms/README.md) | 2 h 30 min | 3 | Two destinations, path A to Nexus, path B down to the legacy kernel |
| [7. From other systems](module-07-from-other-systems/README.md) | 2 h 45 min | 3 | WordPress, Drupal and Joomla, static HTML |
| [8. Leaving Exponential, and the capstone](module-08-leaving-and-capstone/README.md) | 2 h 15 min | 2 | The complete export, and a capstone migration plan with a rubric |

The module durations include one 15-minute break each.

## Reading path

For a trainer preparing the course, in this order:

1. [Migrating to and from Exponential](../../../install/migration/README.md): the hub, its product table and its
   decision guide.
2. [The common migration checklist](../../../install/migration/checklist.md): the frame of every lesson.
3. [Mapping content to Exponential](../../../install/migration/content-model-mapping.md) and
   [Exponential's import and export tools](../../../install/migration/exponential-tools.md).
4. [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md): the address tools
   learners use in class.
5. Modules 1 to 3 of this course, then try their exercises on your own training installation.
6. The chapter of each source module you will teach:
   [chapter 14](../../../install/14-migrating-from-4x.md) for module 4,
   [chapter 15](../../../install/15-migrating-from-5x-legacy.md) for module 5,
   [chapter 16](../../../install/16-migrating-from-ez-platform-and-ibexa.md) for module 6, and the
   [WordPress](../../../install/migration/wordpress.md), [Drupal](../../../install/migration/drupal.md),
   [Joomla](../../../install/migration/joomla.md) and [static HTML](../../../install/migration/static-html.md) guides
   for module 7.
7. [Chapter 17: Migration reference](../../../install/17-migration-reference.md): the lookup tables for questions in
   class.
8. [Migrating away from Exponential](../../../install/migration/leaving-exponential.md) and module 8.

Product versions and release lines in the hub and in chapter 16 change over time: check them on the day you teach.

## Suggested schedules

Built from the lesson durations above (breaks added per day).

**Four days, the full course**

| Day | Content |
|---|---|
| 1 | Module 1, module 2 |
| 2 | Module 3, module 4 lessons 4.1 and 4.2 |
| 3 | Module 4 lessons 4.3 and 4.4, module 5, module 6 lesson 6.1 |
| 4 | Module 6 lessons 6.2 and 6.3, module 7, module 8 |

Day 4 is the longest; move lesson 7.2 to day 3 if your days are short, or leave out the source modules your learners
do not need.

**Two days: planning and tools for any source**

| Day | Content |
|---|---|
| 1 | Module 1, module 2 |
| 2 | Module 3, the source lesson the group needs (for example lesson 7.1), lesson 8.2 |

**One day: from WordPress to Exponential**

Lessons 1.1, 1.2, 2.1, 2.3, 3.2 and 7.1 (5 hours 45 minutes of lessons), with a closing discussion of lesson 1.4's
rollback section.

**Two half days for project managers**

Half day 1: lessons 1.1 to 1.4. Half day 2: lessons 2.2 and 2.3, then lesson 8.2 with a planning-only scenario (the
proof step left out).

## Assessing learners

Every lesson ends with a quiz with sourced answers, and lesson 8.2 has a capstone with a scoring rubric. For a written
test and a practical task on migration, see [Assessments and practical exercises](../assessments/README.md).
Any assessment you run is your own course material; this is not an official certification; the partner program's certifications are separate ([Certifications](../../partners/certifications.md)).

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Train the trainer: course overview](../train-the-trainer/README.md)
- [Migrating to and from Exponential](../../../install/migration/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
