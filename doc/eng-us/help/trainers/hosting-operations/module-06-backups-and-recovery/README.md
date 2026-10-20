---
title: "Module 6: Backups and recovery"
audience: trainer, hosting, admin
category: training
order: 60
summary: The sixth module of Hosting and operations, what to back up, a backup policy and retention, restoring a site with a restore test, and the disk-full and restore runbooks. Four lessons, 2 hours 45 minutes.
keywords: hosting and operations, module 6, backup, restore, restore test, retention, backup policy, disk full, runbook, recovery
type: help
---
# Module 6: Backups and recovery

Backups are only worth what a restore proves. This module teaches what belongs in a backup of an Exponential site,
how to turn the documented rules into a policy, how to restore a complete site in the right order and record a
restore test, and how to handle a full disk and an incident restore without making things worse.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Duration | 2 hours 45 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Hands-on, with one workshop (6.2) and one tabletop exercise (6.4) |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [6.1 What to back up](what-to-back-up.md) | 40 min | Take a database and files backup in one window |
| [6.2 A backup policy and retention](backup-policy-and-retention.md) | 35 min | Write a policy table for a hosting plan |
| [6.3 Restoring a site and restore tests](restoring-and-restore-tests.md) | 50 min | Restore onto a second installation and record the test |
| [6.4 A full disk and restores in an incident](disk-full-and-incident-restores.md) | 40 min | Plan safe cleanup and answer restore decisions |

## Preparing this module

- A training installation per learner with content in a course folder and at least one image.
- A second training installation per learner of the same release and database engine for lesson 6.3.
- Paper or a shared document for the workshop and the tabletop.
- Check disk space on the training machines: a backup of each installation must fit inside `var/training-backup/`.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 6.1 (40 min) |
| 0:40 | Lesson 6.2 (35 min) |
| 1:15 | Break (15 min) |
| 1:30 | Lesson 6.3 (50 min) |
| 2:20 | Lesson 6.4 (40 min) |
| 3:00 | End (2 h 45 min of lessons plus one break) |

## Related pages

- [Hosting and operations: course overview](../README.md)
- [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)
- [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
