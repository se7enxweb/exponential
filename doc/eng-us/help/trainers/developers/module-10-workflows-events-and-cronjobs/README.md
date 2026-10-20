---
title: "Module 10: Workflows, events and cronjobs"
audience: trainer, developer
category: training
order: 100
summary: The tenth module of the Developers course. Learners follow workflows, triggers and waiting processes, write a workflow event type that logs publishing, and write a cronjob part that cleans up their extension's table. 2 hours 40 minutes.
keywords: developers course, module 10, workflow, trigger, workflow process, event type, eZWorkflowEventType, workflow.ini, cronjob, cronjob part, runcronjobs.php, cron:
type: help
---
# Module 10: Workflows, events and cronjobs

Code does not only run when someone opens a page. This module covers the two other ways: as a step of a workflow
around a kernel operation such as publishing, and as a cronjob on a schedule. Learners connect a workflow with a
trigger, write an event type, and write a cronjob part for their extension.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 2 hours 40 minutes (with one 10-minute break) |
| Lessons | 3 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 10.1: Workflows and triggers for developers](workflows-and-triggers.md) | 45 minutes | Build a workflow, connect it to publishing, run the workflow cronjob |
| [Lesson 10.2: A workflow event type of your own](a-workflow-event-type.md) | 60 minutes | Write **Log publishing**, register it, use it after publishing |
| [Lesson 10.3: A cronjob part](a-cronjob-part.md) | 45 minutes | Write a clean-up script, give it a part, run it three ways |

## Preparing this module

- Each learner needs an administrator account on an installation of their own: the lessons change triggers that
  affect every publish on the installation.
- Lesson 10.3 needs the table `mysite_greeting` of lesson 9.3.
- Check that `runcronjobs.php` runs on the learners' machines as their own user.
- At the end of the module, check that every learner set the publish triggers back.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 10.1 | 45 |
| Lesson 10.2 | 60 |
| Break | 10 |
| Lesson 10.3 | 45 |
| **Total** | **160** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
