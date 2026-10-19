---
title: "Module 6: Cronjobs, workflows and notifications"
audience: trainer, admin
category: training
order: 60
summary: The sixth module of Site administration, cronjob parts and the crontab, the cronjobs page, workflows and approvals, notifications and the e-mail preferences gate. Five lessons, 3 hours 30 minutes.
keywords: site administration, module 6, cronjobs, crontab, workflows, approval, triggers, notifications, e-mail preferences, mail gate
type: help
---
# Module 6: Cronjobs, workflows and notifications

Everything a site does in the background depends on cronjobs: notifications, approvals, content jobs, the audit
trail and the cleanup of mail consent records. This module teaches administrators to see what runs and when, to run
and pause parts safely, to set up an approval workflow that really finishes, and to check notifications and the mail
gate without ever sending a mail in class.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Duration | 3 hours 30 minutes (five lessons) |
| Lessons | 5 |
| Level | Intermediate |
| Format | Hands-on; a training installation with administration and shell access per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [6.1 Cronjob parts and the crontab](cronjob-parts-and-the-crontab.md) | 40 min | List parts, run a part and a script, write a crontab line |
| [6.2 The cronjobs page](the-cronjobs-page.md) | 45 min | Run, follow, pause and resume parts on Setup > Cronjobs |
| [6.3 Workflows and approvals](workflows-and-approvals.md) | 50 min | Build an approval, approve it, cancel a process |
| [6.4 Running notifications](running-notifications.md) | 40 min | Preview and rehearse a notification run into files |
| [6.5 E-mail preferences and the mail gate](email-preferences-and-the-mail-gate.md) | 35 min | Check the gate, the suppression list and the sender details |

## Preparing this module

- One training installation per learner with administration and shell access (see
  [The console install](../../../../install/07-console-install.md)), served for example with `exp:velocity start`.
- An editor test account per learner with an address at `example.invalid`, for lesson 6.3.
- The mail transport of every training installation set to `file` (`site.ini [MailSettings] Transport=file`), so a
  mistake cannot send mail. Check it before class.
- Rehearse lesson 6.3 on your own installation: the button names of the workflow edit form, and where your site
  package lets a user follow a folder (lesson 6.4, step 4).
- Optional: `php bin/php/console exp:collaborationsampledata --dry-run`, to show sample approvals.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lessons 6.1 and 6.2 (85 min) |
| 1:25 | Break (15 min) |
| 1:40 | Lesson 6.3 (50 min) |
| 2:30 | Break (10 min) |
| 2:40 | Lessons 6.4 and 6.5 (75 min) |
| 3:55 | End (3 h 30 min of lessons plus breaks) |

## Related pages

- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
