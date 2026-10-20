---
title: "Module 9: Backups and maintenance"
audience: trainer, admin
category: training
order: 90
summary: The ninth module of Site administration, what a backup must contain, maintenance mode, a real backup and restore test, and the checks for missing files, orphan files and changed installation files. Four lessons, 2 hours 55 minutes.
keywords: site administration, module 9, backup, restore, restore test, maintenance mode, missing files, storage files, quarantine, file consistency
type: help
---
# Module 9: Backups and maintenance

Every site will one day need its backup. This module teaches administrators what a complete Exponential backup holds
(including the key files nothing else can replace), how to take a site offline cleanly, how to restore and prove the
restore worked, and how to check the files and storage afterwards.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Duration | 2 hours 55 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Hands-on; a SQLite training installation with administration and shell access per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [9.1 What to back up](what-to-back-up.md) | 40 min | Find the database, storage and key files; write a backup plan |
| [9.2 Maintenance mode](maintenance-mode.md) | 40 min | Take the site offline and back, from the page and the shell |
| [9.3 A backup and a restore test](backup-and-restore-test.md) | 55 min | Back up, change, restore and check a training site |
| [9.4 Checking files and storage](checking-files-and-storage.md) | 40 min | Find missing files, quarantine an orphan, verify the release files |

## Preparing this module

- One training installation per learner on SQLite (the default of `exp:install`), with the `sqlite3` command line
  tool installed, administration and shell access, and a few images in the content.
- Disk space for a backup of each installation outside its directory.
- The stop and start commands of the web server the training sites run on (Velocity: `exp:velocity stop` and
  `exp:velocity start`).
- For a demonstration of **Setup > Backup**: an installation with the extension `git_manager` on MySQL or MariaDB
  (it needs `mysqldump`); otherwise show its documentation only.
- Rehearse lesson 9.3 completely on your own machine and time it.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lessons 9.1 and 9.2 (80 min) |
| 1:20 | Break (15 min) |
| 1:35 | Lesson 9.3 (55 min) |
| 2:30 | Break (10 min) |
| 2:40 | Lesson 9.4 (40 min) |
| 3:20 | End (2 h 55 min of lessons plus breaks) |

## Related pages

- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
