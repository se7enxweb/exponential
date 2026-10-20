---
title: "Module 7: Logs and the audit trail"
audience: trainer, admin
category: training
order: 70
summary: The seventh module of Site administration, the log files, the log viewer, finding an error by its reference, the audit trail and investigations with it. Five lessons, 3 hours 15 minutes.
keywords: site administration, module 7, logs, log viewer, error reference, 404.log, security.log, audit trail, audit console, investigation
type: help
---
# Module 7: Logs and the audit trail

Logs tell an administrator what went wrong; the audit trail tells who did what. This module teaches both: where the
logs are and how they rotate, how to read and search them in **Setup > Log files**, how to turn a visitor's error
reference into the cause, and how to read, verify and export the tamper-evident audit record after an incident.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Duration | 3 hours 15 minutes (five lessons) |
| Lessons | 5 |
| Level | Intermediate |
| Format | Hands-on; a training installation with administration and shell access per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [7.1 The log files](the-log-files.md) | 35 min | Find the logs, read a 404 line, check rotation settings |
| [7.2 The log viewer](the-log-viewer.md) | 45 min | Follow, search and share a log; read Error digest and Log health |
| [7.3 Finding an error by its reference](finding-an-error-by-its-reference.md) | 35 min | Follow a reference or a time window to the request |
| [7.4 The audit trail](the-audit-trail.md) | 45 min | Find their sign-in, filter the console, verify the chain |
| [7.5 Investigating with the audit trail](investigating-with-the-audit-trail.md) | 35 min | Find a removal, export events, build an auditor role |

## Preparing this module

- One training installation per learner with administration and shell access, served for example with
  `exp:velocity start`.
- For lesson 7.3: an error reference from your own projector installation, or a check that the learners' **Error
  digest** shows one; otherwise teach the time-window search only.
- Mail transport set to `file` on every training installation, so audit alerts cannot send mail.
- A test user group and a test user at `example.invalid` per learner for lesson 7.5.
- Never use a production site's logs or audit in class: they hold real people's data.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lessons 7.1 and 7.2 (80 min) |
| 1:20 | Break (15 min) |
| 1:35 | Lesson 7.3 (35 min) |
| 2:10 | Break (10 min) |
| 2:20 | Lessons 7.4 and 7.5 (80 min) |
| 3:40 | End (3 h 15 min of lessons plus breaks) |

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
