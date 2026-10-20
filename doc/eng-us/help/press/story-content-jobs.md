---
title: "Story: content jobs"
audience: press
category: press-media
order: 87
summary: A feature story about content jobs in Exponential's 6.0.15 line - large removes, copies and moves of content run in the background in batches with checkpoints, resuming after a crash, and the same engine installing packages and writing large audit exports - with facts, sources and claims to avoid.
keywords: story, content jobs, background jobs, batch, checkpoint, resume, large operations, subtree, move, copy, remove, packages, reliability
type: help
---
# Story: content jobs

This page is for technical journalists who write about reliability. Content jobs solve an old problem of web
applications: an operation too big to finish inside one web request.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [Facts you can print](#facts-you-can-print)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

Removing, copying or moving a large part of a site's content tree "used to run inside one web request and could fail
on the time limit, the memory limit or one enormous transaction". From the 6.0.15 line the administration offers to
run such an operation as a **content job**: "a background worker that works in batches, takes a checkpoint after each
batch, and resumes exactly where it stopped, even after a `kill -9`" ([Content jobs](../../features/6.0/content-jobs.md)).
Since 12 October 2026 the same engine also installs packages in the background and writes large audit exports to a file.

## Facts you can print

| Fact | Source |
|---|---|
| Content jobs were added on 2 October 2026 | [Content jobs](../../features/6.0/content-jobs.md) |
| Batches, a checkpoint after each batch, resume after interruption | same |
| Jobs can be started and watched from the administration and the command line | [From the command line](../../features/6.0/content-jobs.md#from-the-command-line) |
| Package installation and audit exports use the same engine (since 12 October 2026) | [Content jobs](../../features/6.0/content-jobs.md) |
| Extensions' queued work (for example Exponential Intelligence) runs in a separate job queue, `expqueue` | [expqueue](../../features/6.0/extensions/expqueue.md) |
| Developers can add their own job types | [Content jobs (developers)](../../bc/6.0/content-jobs.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Operations of any size finish instantly" | They run in the background, in batches |
| "Released" | In the 6.0.15 line, in development |

## Angles and headline ideas

- "When a click would take an hour: background jobs for big content changes."
- "Crash-safe content operations in an open source CMS."

## Related pages

- [Story: operations tools for administrators](story-maintenance-mode-and-operations.md)
- [Story: the audit trail](story-audit-trail.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
