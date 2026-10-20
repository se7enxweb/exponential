---
title: "Module 4: From the 4.x line"
audience: trainer, admin, developer
category: training
order: 40
summary: The module on moving an old 4.x site of the legacy kernel to Exponential 6 in place - taking stock, the database runbook, files, settings, designs and porting extensions to PHP 8, and verifying the result. Four lessons, 3 hours 30 minutes.
keywords: migrating to exponential, 4.x, legacy kernel, older releases, database update, phase A, phase B, PHP 8, porting extensions, settings, verify, module
type: help
---
# Module 4: From the 4.x line

Sites that still run a 4.x release of the old legacy kernel on PHP 5 move to Exponential 6 in place: nothing is
exported and imported, the database is brought forward, and the code is ported. This module follows chapter 14 of
the installation book in the order a real project works through it.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Duration | 3 hours 30 minutes |
| Lessons | 4 |
| Level | Advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [4.1 Taking stock of an old 4.x site](taking-stock-of-the-old-site.md) | 45 minutes | Read versions, sizes and settings; run the file consistency check; write a stock-taking sheet |
| [4.2 The database runbook: preflight, phase A and phase B](the-database-runbook.md) | 60 minutes | Check the update files; run the preflight queries; write the runbook for a 4.3 site |
| [4.3 Files, settings, designs and porting extensions to PHP 8](files-settings-and-code.md) | 60 minutes | Lint and load an extension's classes; port PHP 5 code or review old settings |
| [4.4 Verifying the migrated site](verifying-the-migrated-site.md) | 45 minutes | Run the read-only checks of chapter 14.12; match common issues to their fixes |

## Preparing this module

- [ ] One training installation per learner with shell access and an administrator account; `sqlite3` installed.
- [ ] Each learner noted the counts of lesson 4.1; they are compared in lesson 4.4.
- [ ] Chapter 14 of the installation book read once in full by the trainer; it is long, and learners will ask about
      their own starting version.
- [ ] If you can, an old demo database (without personal data) restored on a separate machine to show a real
      `ezsite_data` version row of an old release; otherwise teach with the training installation as described.
- [ ] Split the room for lesson 4.3: developers port code, administrators review settings.

## Timing plan

| Block | Minutes |
|---|---|
| Lesson 4.1 | 45 |
| Lesson 4.2 | 60 |
| Break | 15 |
| Lesson 4.3 | 60 |
| Lesson 4.4 | 45 |
| Total | 225 (3 hours 30 minutes of lessons, plus a 15-minute break) |

## Related pages

- [Migrating to Exponential: course overview](../README.md)
- [Chapter 14: Migrating from the 4.x line](../../../../install/14-migrating-from-4x.md): the chapter this module
  teaches from.
- [Older releases of this kernel](../../../../install/migration/older-releases.md): which chapter fits which release.
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
