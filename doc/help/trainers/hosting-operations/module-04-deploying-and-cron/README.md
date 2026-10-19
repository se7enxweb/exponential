---
title: "Module 4: Deploying and cron"
audience: trainer, hosting, admin
category: training
order: 40
summary: The fourth module of Hosting and operations, deploying a change with exp:velocity deploy, cron on the server, the cronjob console and upgrading customer sites. Four lessons, 2 hours 55 minutes.
keywords: hosting and operations, module 4, deploy, exp:velocity deploy, cron, crontab, cronjob console, pause, upgrade, upgrading customer sites
type: help
---
# Module 4: Deploying and cron

Most incidents after a change come from steps done in the wrong order or not at all. This module teaches the routines
that keep a site current and its scheduled work running: one command for a PHP change, a correct crontab, the
cronjob console for support staff without a shell, and the upgrade routine for many customer sites.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Duration | 2 hours 55 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Hands-on; one training installation and a shell per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [4.1 Deploying a change](deploying-a-change.md) | 45 min | Read a dry run, deploy an INI change, clear for a template change |
| [4.2 Cron on the server](cron-on-the-server.md) | 45 min | List parts, run one by hand, install a crontab as the site's user |
| [4.3 The cronjob console](the-cronjob-console.md) | 40 min | Run, follow, pause and resume parts in the administration |
| [4.4 Upgrading customer sites](upgrading-customer-sites.md) | 45 min | Take an inventory, run the checks, write an upgrade plan |

## Preparing this module

- A training installation per learner, served by Velocity, with a shell as the user that owns its files.
- An administrator account on each installation for lessons 4.3 and 4.4.
- A second copy of the installation per learner for lesson 4.4 (a fresh `exp:install` is enough).
- Check that learners may edit their own crontab on the training machines.
- Rehearse the deploy of lesson 4.1 on the same release the learners use; the number of steps it prints depends on
  the release.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 4.1 (45 min) |
| 0:45 | Lesson 4.2 (45 min) |
| 1:30 | Break (15 min) |
| 1:45 | Lesson 4.3 (40 min) |
| 2:25 | Lesson 4.4 (45 min) |
| 3:10 | End (2 h 55 min of lessons plus one break) |

## Related pages

- [Hosting and operations: course overview](../README.md)
- [Operations](../../../hosting/operations/README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
