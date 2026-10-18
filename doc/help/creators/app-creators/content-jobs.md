---
title: Content jobs
audience: app-creator, developer, admin
category: app-creation
order: 320
summary: How the kernel runs large content operations in the background - removes, copies, moves, hides, sections, states, locations, package installs and audit exports - with their progress page, command, cronjob part, settings and what an application can use.
keywords: content jobs, background jobs, expcontentjob, content/job, content/jobs, ContentJobSettings, batch, large subtree, remove, copy, move, contentjobs cronjob, app creator
type: help
---
# Content jobs

This page is for application creators who work with large subtrees. The kernel turns large content operations into
**content jobs** that run in the background in batches, with a progress page, instead of one long request
([Content jobs](../../../bc/6.0/content-jobs.md)).

## Contents

1. [What becomes a job](#what-becomes-a-job)
2. [The command](#the-command)
3. [Settings](#settings)
4. [Reliability](#reliability)
5. [Using jobs from an application](#using-jobs-from-an-application)

## What becomes a job

Remove, copy, move, hide, reveal, section assignment, subtree states, adding and removing locations, package installs
and audit exports (`JobTypes[]`). Editors choose "now" or "in the background"; from `SynchronousLimit` items the
background is preselected, above `NowLimit` it is the only choice. Progress: `content/job/<id>`; list: `content/jobs`.

## The command

```bash
php bin/php/expcontentjob.php list [--all]
php bin/php/expcontentjob.php show <id>
php bin/php/expcontentjob.php remove <node>[,<node>...] [--trash|--delete] [--background]
php bin/php/expcontentjob.php copy <source> <new parent> [--background]
php bin/php/expcontentjob.php move <node> <new parent>
php bin/php/expcontentjob.php run <id> | resume <id> | cancel <id>
```

Also `php bin/php/console exp:expcontentjob ...`.

## Settings

`content.ini [ContentJobSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `SynchronousLimit` | 50 | from this many items the background is preselected |
| `NowLimit` | 1000 | above this many "now" is not offered |
| `BatchSize` | 50 | nodes per batch, one transaction each |
| `BatchPause` | 0 | milliseconds between batches (100 to 200 on SQLite) |
| `QueuedGrace` | 60 | seconds before the cron part starts a job nobody started |
| `MaxAttempts` | 5 | worker starts without progress before the job fails |
| `KeepDays` | 30 | finished jobs are removed after this many days |

## Reliability

The `contentjobs` cron part (in the frequent group) starts jobs whose worker never started and resumes jobs whose worker
died, so a job is not lost when the web server cannot start a worker. On a cluster, the job directory must be shared.

## Using jobs from an application

- For large operations on content, call the command (or its service) instead of looping over thousands of nodes in a
  request.
- For your own background work, use the job queue `expqueue`, which is separate from content jobs and built for
  application jobs ([Queues with expqueue](queues-with-expqueue.md)).
- Give supervisors `content/jobs` to see every user's jobs.

## Related pages

- [Background work: queues, cronjobs and content jobs](background-work-patterns.md)
- [Creating and updating content in code](creating-content-in-code.md)
- [The content model and editing: large operations](../../../guides/content-model-and-editing.md#8-large-operations-as-content-jobs)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
