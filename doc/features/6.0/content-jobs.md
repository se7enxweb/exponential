# Content jobs: large removes, copies and moves in the background

This page is for editors and administrators who remove, copy or move big subtrees. Such an operation used to run inside
one web request and could fail on the time limit, the memory limit or one enormous transaction. From 6.0.15 the admin
offers to run it as a **content job**: a background worker that works in batches, takes a checkpoint after each batch,
and resumes exactly where it stopped, even after a `kill -9`. Added 2026-10-02. Full guide, including how to add your
own job type: [Content jobs](../../bc/6.0/content-jobs.md).

## Run an operation as a job

1. Start a remove, copy, move, hide or section assignment as before. The confirmation page now shows what the
   operation touches (counts, languages, objects that keep other locations) and a choice: **run now** or **in the
   background**. The choice offered first follows the size: below 50 nodes "now", from 50 on "job".
2. Choose **in the background**. The browser follows `content/job/<id>`: who started it, what it does, progress with
   time left, batches, where it runs, and buttons to cancel, resume, remove a partial copy, or download the log.
3. Open `content/jobs` to see all jobs, with filters by state, operation and user (policy `content/jobs`).

Two more operations were added the same day:

- **States for a whole subtree**: the Details tab's states form has "Also for everything below this node".
- **Locations for many items**: the sub-items list's "More actions" has "Add a location for selected", and many
  locations can be removed at once.

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

The cronjob part `contentjobs` (group `frequent`) restarts a worker whose process died, starts a queued job nobody
started, and removes finished jobs after `KeepDays`.

## Settings

All keys are in `settings/content.ini`, block `ContentJobSettings`. Scope: installation (override in
`settings/override/content.ini.append.php` or a siteaccess).

| Key | Default | Meaning |
|---|---|---|
| `SynchronousLimit` | `50` | Below this many nodes "now" is offered first; `0` = always "job" |
| `NowLimit` | `1000` | "Now" is refused above this; `0` = no ceiling |
| `BatchSize` | `50` | Nodes per batch (one transaction each) |
| `BatchPause` | `0` | Seconds between batches |
| `QueuedGrace` / `QueuedTimeout` | `60` / `600` | Seconds before a queued job is started by cron / given up |
| `MaxAttempts` | `5` | Worker restarts before a job fails |
| `KeepDays` | `30` | Finished jobs are removed after this |
| `PhpBinary` | empty | PHP binary used to start workers |
| `JobTypes[<type>]` | nine built-in classes | `remove`, `copy`, `move`, `hide`, `reveal`, `section`, `state`, `addlocation`, `removelocation` |

Jobs live under `var/<site var dir>/jobs/content/`; a cluster needs a shared var directory.

## Good to know

- A subtree a job is working on is refused for another job (locks).
- The audit records `content.job.start` as the parent of everything the batches did; see
  [the audit trail](audit-trail.md).
- Remote clients start the same jobs through the [expservices content domain](remote-services-expservices.md).
- Fixed the same day: the "run now or in the background" choice of a browse page (move and copy) was never sent with
  the browse form, so the automatic decision always won. It is sent now.

## Related pages

- [The trash: who and where](../../bc/6.0/trash.md)
- [Audit trail specification](../../specifications/6.0/audit-event-model.md)
- [Runnable classes](../../specifications/6.0/runnable-commands-cronjobs-views.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
