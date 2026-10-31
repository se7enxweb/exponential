# Content jobs: large removes, copies and moves in the background

This page is for editors and administrators who remove, copy or move big subtrees. Such an operation used to run inside
one web request and could fail on the time limit, the memory limit or one enormous transaction. From 6.0.15 the admin
offers to run it as a **content job**: a background worker that works in batches, takes a checkpoint after each batch,
and resumes exactly where it stopped, even after a `kill -9`. Added 2026-10-02. Full guide, including how to add your
own job type: [Content jobs](../../bc/6.0/content-jobs.md). Since 2026-10-12 the same engine also installs packages
in the background (`packageinstall`) and writes large audit exports to a file (`auditexport`); see
[Job types of other parts of the kernel](../../bc/6.0/content-jobs.md#job-types-of-other-parts-of-the-kernel). Queued
work of extensions (Exponential Intelligence, for example) runs in the separate job queue
[expqueue](extensions/expqueue.md).

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
`removelocation`, in the foreground or with `--background`. `section` takes `--scope=subtree|inherited|object`, which
objects below the node get the section ([Sections](../../guides/sections.md#85-which-objects-below-get-the-section)).
`php bin/php/expcontentjob.php` is the same script.

The cronjob part `contentjobs` (group `frequent`) restarts a worker whose process died, starts a queued job nobody
started, resumes a job that failed because the database stayed busy, and removes finished jobs after `KeepDays`.

`list` shows how long ago each job's worker last wrote its heartbeat and marks a job `stalled` when it waits without
a worker. `list --resume` resumes every stalled or failed job in the background:

```bash
php bin/php/console exp:expcontentjob list
php bin/php/console exp:expcontentjob list --resume
```

## A busy database

A batch that meets a busy database is not lost. On SQLite this happens when another process holds the write
lock for longer than `site.ini [DatabaseSettings] SQLiteTransactionWait`; on MySQL it is a lock wait timeout or a
deadlock. The worker then:

1. rolls the batch back, so nothing of it stays in the database;
2. reads the job again from its last checkpoint and lets the job type rebuild its state, as a resume does;
3. waits 1, 2, 4 ... seconds (at most `BusyBackoffMax`), writing the heartbeat meanwhile;
4. tries the batch again, up to `BusyRetries` times.

If the database is still busy after that, the job fails with a temporary error. The cronjob part resumes it on
its own, at most `MaxAttempts` times in a row; every batch done starts that count again. Each item is done exactly
once, however often a batch is tried. Before this change the worker of a help manual build could stop on a busy
SQLite database and leave the job `running` without a worker; it had to be resumed by hand.

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
| `MaxAttempts` | `5` | Worker restarts before a job fails; also the automatic resumes in a row of a job that failed for a busy database |
| `BusyRetries` | `8` | Tries again of a batch that met a busy or locked database; `0` = fail at once |
| `BusyBackoffMax` | `60` | Longest pause between those tries, in seconds (the pause doubles from 1) |
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
- [Object states](../../guides/object-states.md#72-a-whole-subtree): setting a state on a subtree, now or as a job
- [Audit trail specification](../../specifications/6.0/audit-event-model.md)
- [Runnable classes](../../specifications/6.0/runnable-commands-cronjobs-views.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
