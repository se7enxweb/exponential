# Content jobs: large removes, copies and moves in the background

Removing, copying or moving a big subtree used to run inside one web request and could fail on the time limit,
the memory limit or one enormous transaction. From 6.0.15 the admin interface offers to run such an operation
as a **content job**: a background worker that works in batches, takes a checkpoint after each batch and resumes
exactly where it stopped, even after a `kill -9`.

Added 2026-10-02. Full guide, including how to add your own job type:
[doc/bc/6.0/content-jobs.md](../../bc/6.0/content-jobs.md).

## What an editor sees

1. Start a remove, copy, move, hide or section assignment as before. The confirmation page now shows what the
   operation touches (counts, languages, objects that keep other locations) and a choice: **run now** or
   **in the background**. The choice offered first follows the size: below 50 nodes "now", from 50 on "job".
2. In the background, the browser follows `content/job/<id>`: who started it, what it does, progress with time
   left, batches, where it runs, and buttons to cancel, resume, remove a partial copy, download the log.
3. `content/jobs` lists all jobs with filters by state, operation and user (policy `content/jobs`).

Two more operations were added the same day: **states for a whole subtree** (the Details tab's states form has
"Also for everything below this node") and **removing many locations / adding a location for many items** (the
sub items list's "More actions" has "Add a location for selected").

## From the command line

```bash
./console exp:expcontentjob list --allow-root-user              # active and failed jobs; --all lists every job
./console exp:expcontentjob show <id> --allow-root-user
./console exp:expcontentjob run <id> --allow-root-user         # run a job in the foreground
./console exp:expcontentjob resume <id> --allow-root-user
./console exp:expcontentjob cancel <id> --allow-root-user
```

The same command creates jobs: `remove`, `copy`, `move`, `hide`, `reveal`, `section`, `state`, `addlocation`,
`removelocation`, in the foreground or with `--background`. `php bin/php/expcontentjob.php` is the same script.
The cronjob part `contentjobs` (group `frequent`) restarts a worker whose process died, starts a queued job
nobody started, and removes finished jobs after `KeepDays`.

## Settings (settings/content.ini, `[ContentJobSettings]`)

| Key | Default | Meaning |
|---|---|---|
| `SynchronousLimit` | `50` | below this many nodes "now" is offered first; `0` = always "job" |
| `NowLimit` | `1000` | "now" is refused above this; `0` = no ceiling |
| `BatchSize` | `50` | nodes per batch (one transaction each) |
| `BatchPause` | `0` | seconds between batches |
| `QueuedGrace` / `QueuedTimeout` | `60` / `600` | seconds before a queued job is started by cron / given up |
| `MaxAttempts` | `5` | worker restarts before a job fails |
| `KeepDays` | `30` | finished jobs are removed after this |
| `PhpBinary` | empty | PHP binary used to start workers |
| `JobTypes[<type>]` | nine built-in classes | `remove`, `copy`, `move`, `hide`, `reveal`, `section`, `state`, `addlocation`, `removelocation` |

Scope: installation (override in `settings/override/content.ini.append.php` or a siteaccess). Jobs live under
`var/<site var dir>/jobs/content/`; a cluster needs a shared var directory.

## Good to know

- A subtree a job is working on is refused for another job (locks).
- The audit records `content.job.start` as the parent of everything the batches did; see
  [the audit trail](audit-trail.md).
- Remote clients start the same jobs through the [expservices content domain](remote-services-expservices.md).
- A bug fixed on the same day: the "run now or in the background" choice of a browse page (move and copy) was
  never sent with the browse form, so the automatic decision always won; it now is.

Related: [trash: who and where](trash-who-and-where.md), [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [audit trail specification](../../specifications/6.0/audit-event-model.md), [runnable classes](../../specifications/6.0/runnable-commands-cronjobs-views.md).
