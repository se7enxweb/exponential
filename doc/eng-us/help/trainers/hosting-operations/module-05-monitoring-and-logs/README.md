---
title: "Module 5: Monitoring and logs"
audience: trainer, hosting, admin
category: training
order: 50
summary: The fifth module of Hosting and operations, monitoring and health checks, the log viewer and rotation, request logs and the security log, and runbooks for a site that is down or slow. Four lessons, 2 hours 55 minutes.
keywords: hosting and operations, module 5, monitoring, health check, logs, log viewer, rotation, 404.log, security.log, runbook, site down, slow site
type: help
---
# Module 5: Monitoring and logs

A host learns about problems from monitoring and solves them from logs. This module teaches the four layers of
monitoring and the status commands behind them, the log viewer and its rotation, the request and security logs, and
ends with a practical: diagnosing prepared faults with the site-down and slow-site runbooks.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Duration | 2 hours 55 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Hands-on; lesson 5.4 in pairs |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [5.1 Monitoring and health checks](monitoring-and-health-checks.md) | 45 min | Run the status commands, read `/Q/health`, see a maintenance 503 |
| [5.2 The log viewer and log rotation](log-viewer-and-rotation.md) | 45 min | Follow and search logs, use permalinks, preview a prune |
| [5.3 Request logs and the security log](request-and-security-logs.md) | 40 min | Read 404 and probe lines, draft a classifier exclude |
| [5.4 Runbooks for a site that is down or slow](runbooks-site-down-and-slow.md) | 45 min | Diagnose a prepared fault and write an incident note |

## Preparing this module

- A training installation per learner, served by Velocity, with an administrator account.
- For lesson 5.4: one prepared fault per pair (maintenance mode left on, the database made unreachable, `vendor/`
  renamed, or view caching switched off in an override). Write each fault down and undo it after the lesson.
- Tell learners that the probe requests of lesson 5.3 go to their own installation only.
- Rehearse a benchmark on the training machines and choose a `--requests` value that does not slow the others down.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 5.1 (45 min) |
| 0:45 | Lesson 5.2 (45 min) |
| 1:30 | Break (15 min) |
| 1:45 | Lesson 5.3 (40 min) |
| 2:25 | Lesson 5.4 (45 min) |
| 3:10 | End (2 h 55 min of lessons plus one break) |

## Related pages

- [Hosting and operations: course overview](../README.md)
- [Monitoring overview](../../../hosting/operations/monitoring-overview.md)
- [Runbooks](../../../hosting/runbooks/README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
