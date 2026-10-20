---
title: "Module 4: Assessing the technical courses"
audience: trainer, developer, designer, hosting
category: training
order: 40
summary: Written tests with sourced answer keys and practical tasks with scoring rubrics for the technical courses, Developers, Theme design and Hosting and operations. About 7 hours 15 minutes if all six are run.
keywords: assessments and practical exercises, developers, theme design, hosting and operations, written test, practical task, rubric, extension, theme extension, backup, restore
type: help
---
# Module 4: Assessing the technical courses

This module holds the assessments for the technical courses: Developers, Theme design, and Hosting and operations.
The practical tasks here are the longest of the course, because they are real jobs: an extension with access
control, a theme for one siteaccess, a backup restored as a safe test copy.

> **Note**
> These are the trainer's own assessments, not an official certification. Every task runs on the learner's own
> training installation, with a shell.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Duration | 7 hours 15 minutes for all six; each pair 2 hours 20 minutes to 2 hours 30 minutes |
| Lessons | 6 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [4.1 Developers, written test](developers-written-test.md) | 45 min | Fourteen questions on extensions, views, templates, Git, REST and releases |
| [4.2 Developers, practical task](developers-practical-task.md) | 105 min | A protected greeting page as an extension, an override, two Git commits |
| [4.3 Theme designers, written test](theme-designers-written-test.md) | 40 min | Thirteen questions on theme extensions, registration, loading order, CSP, versions |
| [4.4 Theme designers, practical task](theme-designers-practical-task.md) | 100 min | A theme for the public siteaccess only, without inline code, traced with path comments |
| [4.5 Hosting and operations, written test](hosting-operations-written-test.md) | 45 min | Fourteen questions on permissions, backups, deploys, monitoring and restores |
| [4.6 Hosting and operations, practical task](hosting-operations-practical-task.md) | 100 min | A consistent backup, a safe restored copy, an error found by reference, a restore record |

## Preparing this module

- [ ] One training installation per learner, from a Git clone for lesson 4.2, on SQLite for lesson 4.6, served on a
      local port, mail transport `file`.
- [ ] A shell, an editor and Git for every learner; `sqlite3` installed for lesson 4.6.
- [ ] For lesson 4.6: a second empty directory per learner, and one error page produced per installation with its
      reference noted.
- [ ] Printed questions and rubrics; answer keys kept separately.
- [ ] A reset and clean-up plan: extensions, themes and test copies are removed after scoring
      ([Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).

## Timing plan

| Time | Part |
|---|---|
| 0:00 to 0:45 | Lesson 4.1 |
| 0:45 to 2:30 | Lesson 4.2 |
| 2:30 to 3:10 | Lesson 4.3 |
| 3:10 to 4:50 | Lesson 4.4 |
| 4:50 to 5:35 | Lesson 4.5 |
| 5:35 to 7:15 | Lesson 4.6 |
| Total | 7 hours 15 minutes, without breaks |

## Related pages

- [Module 1: Designing assessments](../module-01-designing-assessments/README.md)
- [Module 3: Assessing the administration courses](../module-03-administration-courses/README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
