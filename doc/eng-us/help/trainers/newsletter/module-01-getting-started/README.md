---
title: "Module 1: Getting started"
audience: trainer, editor, admin
category: training
order: 10
summary: The map of CJW Newsletter, a training installation that cannot send mail, and the newsletter dashboard. Two hours.
keywords: newsletters with cjw newsletter, getting started, overview, training installation, file transport, dashboard
type: help
---
# Module 1: Getting started

This module gives learners the map of the newsletter system, makes the training installation safe (every mail is
written to a file), and teaches them to read the dashboard before they act. No edition is written yet.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Duration | 2 hours |
| Lessons | 3 |
| Level | Beginner to intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [1.1 What a newsletter system does](what-a-newsletter-system-does.md) | 30 minutes | Tour the newsletter area and draw the tree of system, list, edition and article |
| [1.2 Preparing a safe training installation](preparing-a-safe-training-installation.md) | 60 minutes | Set every mail transport to files, check the result, choose test addresses at `example.invalid` |
| [1.3 The newsletter dashboard](the-newsletter-dashboard.md) | 30 minutes | Read the dashboard section by section and compare it with the status command |

## Preparing this module

- [ ] A training installation with `cjw_newsletter` active, its tables created and a newsletter tree with at least
      one system and one list (see the extension's `INSTALL` file and the
      [feature page](../../../../features/6.0/extensions/cjw_newsletter.md)).
- [ ] `site.ini [MailSettings] Transport=file` and all three `TransportMethod*` keys set to `file`, checked in the
      settings viewer (lesson 1.2 done by the trainer beforehand).
- [ ] One admin training account per learner; shell access for the administrators in the room.
- [ ] No real subscriber anywhere in the database: only addresses at `example.invalid`.
- [ ] The dashboard shows no problem except the hint that mails are written to files.

## Timing plan

| Time | Activity |
|---|---|
| 0:00 to 0:30 | Lesson 1.1 |
| 0:30 to 1:30 | Lesson 1.2 |
| 1:30 to 2:00 | Lesson 1.3 |

## Related pages

- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
