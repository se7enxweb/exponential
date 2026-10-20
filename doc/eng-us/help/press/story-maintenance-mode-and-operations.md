---
title: "Story: operations tools for administrators"
audience: press
category: press-media
order: 92
summary: A feature story about the tools Exponential gives the people who run sites - maintenance mode, the log viewer, request logs, content checks, the file consistency check, cronjob controls, the debug bar and the INI command - with facts, sources and the claims to avoid.
keywords: story, operations, administrators, maintenance mode, log viewer, request logs, content checks, cronjobs, debug bar, exp:ini, sysadmin, DevOps
type: help
---
# Story: operations tools for administrators

This page is for journalists who write for system administrators. Much of the 6.0.x work is not visible to visitors:
it is tooling for the people who keep sites running.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [The tools](#the-tools)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

Maintenance mode "takes the whole public site offline, shows visitors a friendly page, and brings it back with one
command or one button", and the installers use it so that "a site that is being installed never serves a half-built
page" ([Maintenance mode](../../features/6.0/maintenance-mode.md)). Around it, the 6.0.x line adds a log viewer, request
logs, content checks, a file consistency check, cronjob controls, a debug bar and a console command for settings, each
documented with its own page.

## The tools

| Tool | What it does | Page |
|---|---|---|
| Maintenance mode | Takes the site offline and back, from the console or the administration | [Maintenance mode](../../features/6.0/maintenance-mode.md) |
| Log viewer | Reads the logs from the administration | [Log viewer](../../features/6.0/log-viewer.md) |
| Request logs | Records requests for diagnosis | [Request logs](../../features/6.0/request-logs.md) |
| Log rotation | Keeps log files in bounds | [Log rotation](../../features/6.0/log-rotation.md) |
| Content checks | Finds problems in content | [Content checks](../../features/6.0/content-checks.md) |
| File consistency check | Compares files with the release manifest | [File consistency check](../../features/6.0/file-consistency-check.md) |
| Cronjobs | Pause, filter and run scheduled jobs | [Cronjobs console](../../features/6.0/cronjobs-console.md), [Cronjob pause](../../features/6.0/cronjob-pause.md) |
| Debug bar | Debug information for developers | [Exp debug bar](../../features/6.0/exp-debug-bar.md) |
| `exp:ini` | Reads and changes settings from the console | [exp:ini command](../../features/6.0/exp-ini-command.md) |
| Repair from the browser | Fixes known problems without a shell | [Repair from the browser](../../features/6.0/repair-from-the-browser.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Zero downtime upgrades" | Maintenance mode shows a friendly page during work; it is not a zero-downtime mechanism |
| "Self-healing" | Repair tools fix documented problems on request |
| All of these "released" | Several belong to the 6.0.15 line; check each page and the changelog |

## Angles and headline ideas

- "The unglamorous features that keep sites up."
- "A CMS that tells administrators what it is doing."

## Related pages

- [Story: the audit trail](story-audit-trail.md)
- [Story: the Velocity control panel](story-velocity-control-panel.md)
- [Story: content jobs](story-content-jobs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
