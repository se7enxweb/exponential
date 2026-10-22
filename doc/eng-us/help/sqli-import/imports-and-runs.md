---
title: Imports and runs in 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 20
summary: What an import and a run are in 7x SQLI Import, how the job queue processes a run in chunks, the statuses and outcomes, the Imports dashboard, the run page, dry runs, cancel and retry of failed rows.
keywords: sqliimport, import, run, dry run, retry, cancel, chunk, checkpoint, job queue, expqueue, created, updated, unchanged, skipped, failed, processed, dashboard, run page, sqliimport/list, sqliimport/run
type: help
---
# Imports and runs in 7x SQLI Import

## Contents

1. [Imports](#imports)
2. [Runs](#runs)
3. [The dashboard](#the-dashboard)
4. [The run page](#the-run-page)
5. [From the command line](#from-the-command-line)

## Imports

An import says where the data comes from (a CSV, XML, RSS/Atom or JSON source, or a 1.x handler that reads its own
data), what processes it (the no-code mapping or a handler), where new content goes, and how existing content is
treated (update it, only create, or only update).

In 2.0.0-alpha.1 imports are created by the migration from 1.x, from the 1.x handlers in `sqliimport.ini`, or in PHP.
The import editor of the administration comes with 2.0.0-beta.1.

## Runs

A run is one execution of an import. It is processed by the job queue, not by the page or command that starts it:

- a start job makes a snapshot of the source and counts the records;
- chunk jobs process 200 records each (`[RunSettings] ChunkSize`), one after another, in the order of the source;
- a finish job clears the view caches of the changed objects, indexes them and writes the summary.

One import runs only once at a time; different imports run in parallel. A chunk saves a checkpoint every 20
records, so a worker that dies is followed by an attempt that continues there.

| Status | Meaning |
|---|---|
| Queued | Waiting for a worker of the job queue |
| Running | Being processed |
| Completed | Ended, no record failed |
| Completed with errors | Ended, some records failed |
| Failed | Stopped, for example because the source could not be read |
| Cancelled | Cancelled by a user |

Every record gets a row with its outcome: **created**, **updated** (a field changed, so a new version was
published), **unchanged** (nothing to publish), **skipped** (for example, the object is in the trash), **failed**
(with the message), or **processed** (a 1.x handler processed it).

A **dry run** publishes nothing. It reads the first 20 records (`[RunSettings] DryRunRows`) and shows, per record,
which fields would change, with the current and the new value.

## The dashboard

**Imports** (`sqliimport/list`) shows:

- key figures: imports, runs today, runs running or queued, runs failed in the last 24 hours, the next scheduled run;
- a **Run now** toolbar;
- the imports with their last and next run, and **Run now** and **Dry run** per import;
- the runs, filtered by status (all, running, failed, completed, cancelled).

The left menu also links the 1.x pages (import requests, scheduled imports, a new 1.x request) and the job queue.

## The run page

`sqliimport/run/<id>` shows the progress, the outcomes as figures (each one filters the records), the records with
their object, version and message, and the run's log. Two buttons act on the run:

- **Cancel** stops a run that has not ended. Records already processed stay as they are.
- **Retry failed rows** starts a new run that processes only the failed records of an ended run. It is not available
  for 1.x handlers, whose rows carry no record; run their import again instead.

## From the command line

```bash
./console ext:sqliimport:run <identifier>          # queue a run
./console ext:sqliimport:run <identifier> --wait   # process it here and print the result
./console ext:sqliimport:status <run id>           # the run with its log
./console ext:sqliimport:cancel <run id>
./console ext:sqliimport:retry <run id>
```

Ended runs older than 90 days (`[RunSettings] KeepDays`) are removed with their rows and log by the cron part.

Next: [Schedules and cron](schedules-and-cron.md).
