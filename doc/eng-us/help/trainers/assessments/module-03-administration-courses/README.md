---
title: "Module 3: Assessing the administration courses"
audience: trainer, admin
category: training
order: 30
summary: Written tests with sourced answer keys and practical tasks with scoring rubrics for the administration courses, Site administration, Search and Multisite. About 6 hours 25 minutes if all six are run.
keywords: assessments and practical exercises, site administration, search, multisite, written test, practical task, rubric, roles, sections, caches, delayed indexing, siteaccess
type: help
---
# Module 3: Assessing the administration courses

This module holds the assessments for the courses that administrators take: Site administration, Search, and
Multisite and site packages. Each course has a written test and a practical task. The tasks change settings, so
every learner needs a training installation of their own and a shell in it.

> **Note**
> These are the trainer's own assessments, not an official certification. Adapt the questions to the release and the
> site package of your training installations.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Duration | 6 hours 25 minutes for all six; each pair 1 hour 50 minutes to 2 hours 10 minutes |
| Lessons | 6 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [3.1 Site administrators, written test](site-administrators-written-test.md) | 45 min | Fourteen questions on roles, sections, caches, workflows and maintenance |
| [3.2 Site administrators, practical task](site-administrators-practical-task.md) | 85 min | Section, limited role, test user, a cache, a cronjob and a maintenance window |
| [3.3 Search, written test](search-written-test.md) | 40 min | Twelve questions on searching, indexing, rebuilding and Solr |
| [3.4 Search, practical task](search-practical-task.md) | 70 min | Search three ways, reproduce delayed indexing, index, rebuild and restore |
| [3.5 Multisite, written test](multisite-written-test.md) | 40 min | Twelve questions on siteaccesses, matching, proxies and per-site settings |
| [3.6 Multisite, practical task](multisite-practical-task.md) | 75 min | Host matching with test names, proof with curl, a recommendation, restore |

## Preparing this module

- [ ] One training installation per learner, freshly installed, served on a local port, with the mail transport set to
      `file`.
- [ ] Administrator access and a shell in the installation root for every learner.
- [ ] A site package with content in several folders (for lesson 3.4).
- [ ] The names of the public and admin siteaccesses of your site package (for lesson 3.6).
- [ ] Printed questions and rubrics; answer keys kept separately.
- [ ] A reset plan, because these tasks change settings
      ([Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).

## Timing plan

| Time | Part |
|---|---|
| 0:00 to 0:45 | Lesson 3.1 |
| 0:45 to 2:10 | Lesson 3.2 |
| 2:10 to 2:50 | Lesson 3.3 |
| 2:50 to 4:00 | Lesson 3.4 |
| 4:00 to 4:40 | Lesson 3.5 |
| 4:40 to 5:55 | Lesson 3.6 |
| 5:55 to 6:25 | Reset of the installations and results noted |
| Total | 6 hours 25 minutes, without breaks |

## Related pages

- [Module 1: Designing assessments](../module-01-designing-assessments/README.md)
- [Module 2: Assessing the editorial courses](../module-02-editorial-courses/README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
