---
title: "Module 5: Assessing the newsletter and migration courses"
audience: trainer, editor, admin, developer
category: training
order: 50
summary: Written tests with sourced answer keys and practical tasks with scoring rubrics for the Newsletters with CJW Newsletter and Migrating to Exponential courses, with a newsletter task that never sends mail. About 4 hours 45 minutes if all four are run.
keywords: assessments and practical exercises, newsletters, cjw_newsletter, migration, written test, practical task, rubric, file transport, example.invalid, content model mapping, redirects, remote id
type: help
---
# Module 5: Assessing the newsletter and migration courses

This module holds the assessments for two specialist courses: newsletters with CJW Newsletter, and migrating to
Exponential. The newsletter task runs a complete cycle without sending a single mail; the migration task is a planning
task with a small, repeatable import.

> **Note**
> These are the trainer's own assessments, not an official certification. Newsletter exercises use a training
> installation whose mail transports write to files, and test subscribers at `example.invalid` only.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Duration | 4 hours 45 minutes for all four; newsletter pair 2 hours 5 minutes, migration pair 2 hours 40 minutes |
| Lessons | 4 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [5.1 Newsletters, written test](newsletter-written-test.md) | 40 min | Thirteen questions on lists, opt-in, cron, transports, bounces |
| [5.2 Newsletters, practical task](newsletter-practical-task.md) | 85 min | A full cycle to files: subscribe, confirm, send by cron, unsubscribe, blacklist |
| [5.3 Migrating to Exponential, written test](migration-written-test.md) | 45 min | Fourteen questions on paths, mapping, redirects, rehearsal and rollback |
| [5.4 Migrating to Exponential, practical task](migration-practical-task.md) | 115 min | Inventory, mapping, redirects, plan, and a repeatable import by remote id |

## Preparing this module

- [ ] One training installation per learner with `Transport=file`, no crontab, and for lessons 5.1 and 5.2 the
      newsletter extension active with a newsletter system and a list.
- [ ] All three newsletter `TransportMethod*` set to `file` by the trainer before the session
      ([10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
- [ ] For lesson 5.4: a sample static site of 10 to 20 pages per learner (made for class, no personal data) and the
      import tool of your course installed.
- [ ] Printed questions and rubrics; answer keys kept separately.

## Timing plan

| Time | Part |
|---|---|
| 0:00 to 0:40 | Lesson 5.1 |
| 0:40 to 2:05 | Lesson 5.2 |
| 2:05 to 2:50 | Lesson 5.3 |
| 2:50 to 4:45 | Lesson 5.4 |
| Total | 4 hours 45 minutes, without breaks |

## Related pages

- [Module 1: Designing assessments](../module-01-designing-assessments/README.md)
- [Running an assessment session](../module-01-designing-assessments/running-an-assessment-session.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
