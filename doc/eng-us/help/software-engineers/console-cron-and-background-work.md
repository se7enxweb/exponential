---
title: Console, cronjobs and background work
audience: software-engineer, developer, admin
category: software-engineering
order: 190
summary: How work runs outside a web request in Exponential - the console and its command discovery, eZScript, cronjob parts run by runcronjobs.php, content jobs for large content operations, the expqueue job queue for extensions, and the rules that keep commands and jobs safe to rerun.
keywords: console, bin/php/console, commands, eZScript, cronjobs, runcronjobs.php, cronjob parts, content jobs, expqueue, job queue, background work, idempotent, exit codes, software engineer
type: help
---
# Console, cronjobs and background work

This page is for engineers who design work that should not run inside a web request: imports, cleanups, mail,
indexing, large content operations. Exponential has three mechanisms for it, plus the console that runs them all.

## Contents

1. [The console](#the-console)
2. [Cronjob parts](#cronjob-parts)
3. [Content jobs](#content-jobs)
4. [The job queue: expqueue](#the-job-queue-expqueue)
5. [Which one to use](#which-one-to-use)
6. [Rules](#rules)

## The console

`bin/php/console` is one entry point for every command (installations usually add a `./console` shortcut in the root).
It discovers commands at run time:

| Source | Command name |
|---|---|
| `bin/php/<name>.php` | `exp:<name>` (plus names declared with `@alias` in the docblock, such as `exp:help`) |
| `bin/shell/<name>.sh` | `shell:<name>` |
| `extension/<ext>/bin/php/<name>` | `ext:<ext>:<name>` |
| a cronjob part | `cron:<part>` |

The code of a kernel command is an `Exponential\Runnable\Command` class in `kernel/private/classes/commands/`; the file
in `bin/php/` is a one-line entry point. Scripts boot through `eZScript`, not `ezpKernelWeb`. Reference:
[Exponential Console](../../bc/6.0/console.md); for authors, [Console commands](../creators/app-creators/console-commands.md).

```bash
php bin/php/console list              # every command
php bin/php/console list cron         # cronjob parts
php bin/php/console help exp:preload  # the help of one command
```

## Cronjob parts

`runcronjobs.php` runs the parts configured in `settings/cronjob.ini`: `[CronjobSettings] Scripts[]` for the default
part and `[CronjobPart-<name>] Scripts[]` for named parts such as `frequent` and `infrequent`. The code of a part is an
`Exponential\Runnable\CronjobPart` class in `kernel/private/classes/cronjobs/`.

```bash
php runcronjobs.php frequent
```

The `frequent` group matters on every installation: it contains, among others, the `audit` part that indexes, delivers,
evaluates alert rules and rotates the audit trail ([The audit trail](../../features/6.0/audit-trail.md)). Cronjobs can be
listed, run and paused from the console and the administration ([Cronjobs console](../../features/6.0/cronjobs-console.md),
[Cronjob pause](../../features/6.0/cronjob-pause.md)).

## Content jobs

Large removes, copies, moves, package installs and audit exports run as content jobs: the request queues the job and
returns, and the work proceeds in batches in the background ([Content jobs](../../bc/6.0/content-jobs.md),
[Content jobs feature page](../../features/6.0/content-jobs.md)).

## The job queue: expqueue

`expqueue` is a database-backed job queue for extensions, required by `composer.json` from 6.0.15 on and inactive
until activated. Producers queue a job with one call; workers run it on the command line, from cron or as a systemd
service, never inside a web request. It offers priority classes, retries with backoff, a dead letter, idempotency keys
and leases with a reaper ([expqueue: the job queue](../../features/6.0/extensions/expqueue.md),
[Queues with expqueue](../creators/app-creators/queues-with-expqueue.md)).

## Which one to use

| Need | Use |
|---|---|
| Periodic maintenance (cleanup, digest mail, index updates) | a cronjob part |
| A large operation on content started by an editor | a content job |
| Work queued by your code, with retries and priorities | expqueue |
| An operator task run by hand or from a deploy script | a console command |

## Rules

- A cronjob part **must** be safe to run twice and to be interrupted.
- A command **must** keep its `--help` text and exit codes stable, because scripts and cron depend on them, and should
  offer `--dry-run` when it changes data.
- Shared logic goes into a service class that views, commands and parts all call
  ([Background work patterns](../creators/app-creators/background-work-patterns.md)).
- Long-running workers see a class change only after they restart, like Velocity's workers.

## Related pages

- [Cronjobs](../../guides/cronjobs.md)
- [CLI scripts](../creators/app-creators/cli-scripts.md)
- [Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
