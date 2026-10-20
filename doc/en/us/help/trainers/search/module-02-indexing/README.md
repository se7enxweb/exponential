---
title: "Module 2: Indexing"
audience: trainer, admin
category: training
order: 20
summary: The second module of the Search course, indexing at publish, delayed indexing with the indexcontent cronjob, rebuilding the whole index, and choosing searchable attributes. Four lessons, 2 hours.
keywords: search course, module 2, indexing, DelayedIndexing, indexcontent, ezpending_actions, updatesearchindex, searchable attributes, reindex
type: help
---
# Module 2: Indexing

A search can only find what is in its index. This module follows content into the index: at publish, through the
delayed-indexing queue and its cronjob, in a full rebuild, and through the class setting that decides which fields
are indexed at all.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Duration | 2 hours (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Hands-on; administration and terminal on the learner's own training installation |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [2.1 Indexing at publish](indexing-at-publish.md) | 25 min | Prove that drafts are not found, new versions replace words, moves need no reindex |
| [2.2 Delayed indexing and the indexcontent cronjob](delayed-indexing-and-the-indexcontent-cronjob.md) | 35 min | Switch on delayed indexing, watch the queue, run the cronjob, switch it off |
| [2.3 Rebuilding the search index](rebuilding-the-search-index.md) | 30 min | Run `updatesearchindex.php` with and without `--clean` and plan it for a live site |
| [2.4 Choosing searchable attributes](choosing-searchable-attributes.md) | 30 min | Read and change the **Searchable** flag of a class and rebuild |

## Preparing this module

- One training installation per learner (lessons 2.2 and 2.4 change settings and a class; never share an installation
  for this module).
- A terminal in the installation root for every learner, and the name of the public siteaccess written on the board.
- Check before the class that `DelayedIndexing` is `disabled`
  (`php bin/php/console exp:ini get site.ini/SearchSettings/DelayedIndexing`).
- Rehearse lesson 2.3 once to know how long a rebuild takes on the training content.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 2.1 (25 min) |
| 0:25 | Lesson 2.2 (35 min) |
| 1:00 | Lesson 2.3 (30 min) |
| 1:30 | Lesson 2.4 (30 min) |
| 2:00 | End of module |

## Related pages

- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
