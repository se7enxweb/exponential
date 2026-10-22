# Transfer runs: one engine and one run history for every import and export tool (6.0.15)

Read this page if you write or maintain an import or export tool, or a page that lists their runs. The tools
themselves are declared in the [transfer registry](transfer-registry.md); users find their runs on the run list and
the Statistics page of Imports.

## In short

| | |
|---|---|
| What it is | One way for every tool of the transfer registry to run its work in the background, on whichever engine the installation has, and one table that records every run of every tool. |
| Engines | **expqueue** (the job queue) when it is active and a worker is alive; otherwise the kernel's **content jobs**, which every installation has; otherwise the tool's own **process**, as before 6.0.15. The tool's code is the same for all three. |
| History | The table `exp_transfer_run`: one row per run with the tool, direction, kind, scope, user, state, counts, duration, errors, file reference, the engine and its job, and the link to the tool's own page of the run. |
| Where runs show | The run list and the Statistics page of Imports (`sqliimport/list`, `sqliimport/statistics`) have a tool filter: Imports, Export, CIE, or all import and export tools. Each tool's own pages (Export's Jobs and History, CIE's export page) keep their lists and link to it. No new tab. |
| Classes | `expKernelTransferRunner`, `expKernelTransferTask`, `expKernelTransferContext`, `expKernelTransferHistory`, `expKernelTransferRun`, `expKernelTransferRunSchema`, `expKernelTransferQueueHandler`, `expContentJobTransfer` |
| Settings | `transfer.ini [TransferSettings]`; per tool `Engine=`, `QueueJobType=`, `HistoryAllPermission=` in its registry block |

## Which engine runs a tool's run

`expKernelTransferRunner::engine( $toolID )` decides for each run:

1. The engine asked for: `transfer.ini [TransferSettings] ToolEngine[<tool id>]`, else the tool's `Engine=` in its
   registry block, else `[TransferSettings] Engine`. `auto` (the default) means "the best one available".
2. **expqueue** is available when the extension is active, its tables are installed, the tool names its job type
   (`QueueJobType=`) and declares it in `expqueue.ini` with `Handler=expKernelTransferQueueHandler`, and a queue worker
   is alive. Without a worker a run would wait until one starts, so it goes to the next engine
   (`QueueNeedsWorker=disabled` queues it anyway).
3. **contentjob** is available when `content.ini [ContentJobSettings] JobTypes[transfer]=expContentJobTransfer` is
   set, which it is by default. The content job starts a worker process of its own for the run; the cronjob part
   `contentjobs` starts it again when that worker died.
4. **process**: `submit()` returns `engine => process` and starts nothing; the tool starts its work the way it did
   before.

A named engine that is not available falls back in the same order, so a setting never stops a tool from working.

## Using it in a tool

### The task

The work of one run is a class implementing `expKernelTransferTask`:

```php
class bccieTransferTask implements expKernelTransferTask
{
    public function run( array $params, expKernelTransferContext $context )
    {
        $job = bccieJob::load( $params['job'] );
        foreach ( ... as $i => $row )
        {
            ...
            $context->progress( $i + 1, $total );
            $context->throwIfCancelled();
        }
        return array( 'rows_done' => $total, 'bytes' => filesize( $file ), 'file_ref' => basename( $file ), 'format' => 'csv' );
    }
}
```

- `run()` is called once, in a worker process, as the user who asked for the run, outside any database transaction
  (the content job runs it in its `prepare()` step, before its batch transaction, for that reason).
- The parameters are what the tool passed to `submit()`: plain JSON values, never a secret (the queue's admin pages
  show them). Pass the tool's own job id and keep everything else in the tool's job file or table.
- It returns the run's result, which goes into the history row: any column (`rows_done`, `rows_failed`,
  `rows_total`, `bytes`, `format`, `file_ref`, `title`, `detail_url`, `error`), `errors` (a list of messages, at
  most 20 are kept), `detail` (an array) and `state` (`failed` for a run that ended without an exception but did not
  do its work).
- It throws `expKernelTransferCancelledException` when it stopped because the run was cancelled, and any other
  exception when it failed. A failed run is not retried by itself: the history row says why, and the tool's page
  can start it again.
- After a killed worker the same run may start again: the task continues or starts over cleanly.

### Starting a run

```php
if ( class_exists( 'expKernelTransferRunner' ) )
{
    $run = expKernelTransferRunner::submit( 'transfer.bccie', 'bccieTransferTask', array( 'job' => $jobID ), array(
        'title' => 'Form 42 as CSV',
        'history' => array( 'direction' => 'export', 'kind' => 'export', 'scope' => 'data', 'scope_ref' => '42',
                            'format' => 'csv', 'tool_ref' => $jobID, 'detail_url' => 'bccie/export/42/(job)/' . $jobID ) ) );
    // $run: engine (expqueue, contentjob, process), job_ref, run_id, error
    if ( $run['engine'] === 'process' )
        ...start it as before...
}
```

`submit()` writes the history row (state `queued`), hands the run to the engine and stores the engine and its job in
the row. Keep `engine`, `job_ref` and `run_id` in the tool's own job record: the tool's pages need them to cancel the
run and to find its row. Guard the call with `class_exists()` so the tool keeps working on an older kernel.

### Declaring the job type for the queue

In the tool's `settings/expqueue.ini.append.php`, so the queue's dashboard lists its runs under their own name:

```ini
[JobType_bccie.export]
Handler=expKernelTransferQueueHandler
Class=bulk
MaxAttempts=1
Timeout=3600
Description=An export of CIE (form submissions)
```

and in its registry block in `kernelservices.ini.append.php`:

```ini
QueueJobType=bccie.export
HistoryAllPermission=bccie/remove
```

`expKernelTransferQueueHandler` exists only where expqueue is active; without it the declaration is inert and the
content jobs take the runs. Use the `bulk` priority class for exports and imports, so a large run never holds up the
editors' jobs.

### Cancelling

`expKernelTransferRunner::cancel( $engine, $jobRef )` cancels through the engine: a queued run at once, a running one
at the task's next `throwIfCancelled()`. Map the tool's own cancel to it, and the task's `isCancelled()` to the
tool's own cancel flag where the tool has one.

### Runs that do not go through the runner

A scheduled export of a cronjob, a download made in the request, or the tool's own process (engine `process`)
writes its row directly:

```php
$id = expKernelTransferHistory::start( 'transfer.xrowextract', array( 'direction' => 'export', 'title' => ..., 'tool_ref' => $jobID ) );
expKernelTransferHistory::progress( $id, $done, $total );          // at most every 2 seconds
expKernelTransferHistory::finish( $id, 'done', array( 'rows_done' => $n, 'bytes' => $bytes ) );

expKernelTransferHistory::record( 'transfer.bccie', array( ...a run that is over... ) );
expKernelTransferHistory::findByToolRef( 'transfer.bccie', $jobID ); // the row of the tool's own job
```

or runs its task in-process with `expKernelTransferRunner::execute( $taskClass, $params, $context )`, which keeps the
row exactly as the engines do. A write never throws and never breaks the tool: without the table, or on a database
error, it does nothing (and leaves a debug warning).

## The history

### The table and the upgrade

`exp_transfer_run` is declared in `share/db_schema.dba` (which installs every engine: MySQL and MariaDB, PostgreSQL,
SQLite, Oracle with ezoracle, MongoDB) and in the kernel schemas of MySQL, PostgreSQL and SQLite. An installation
made before 6.0.15 gets it from the update files `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (guarded,
so they can run again), or on any engine from

```bash
php update/common/scripts/6.0/createtransferruntable.php --dry-run   # says whether it is missing
php update/common/scripts/6.0/createtransferruntable.php
```

Until the table exists the tools run as before and write no history.

| Column | What it holds |
|---|---|
| `tool` | The registry id: `transfer.xrowextract`, `transfer.bccie`, `transfer.sqliimport` ... |
| `direction`, `kind` | `import` or `export`; `export`, `import`, `dry_run`, `retry` or `undo` |
| `title`, `scope`, `scope_ref`, `format` | What the run did: "Articles as CSV", `class` / `article`, `csv` |
| `user_id`, `siteaccess` | Who ran it, from where |
| `state` | `queued`, `running`, `done`, `failed`, `cancelled` |
| `created`, `started`, `finished`, `duration`, `modified` | Timestamps; the duration in seconds |
| `rows_total`, `rows_done`, `rows_failed`, `bytes` | The counts (`rows_done` includes the failed rows) |
| `file_ref` | The result file, relative to the tool's job (never a path) |
| `engine`, `job_ref` | `expqueue` (the job's uuid), `contentjob` (its id), `process`, `inline` |
| `tool_ref`, `detail_url` | The tool's own job or run, and the link to its page (a module URL of this installation) |
| `error`, `detail` | A short error; JSON with up to 20 error messages and whatever the tool adds |

Finished runs are kept `[TransferSettings] KeepDays` days (365); the cronjob part `contentjobs` removes older ones.
The tools keep their own records (rows, logs, files) as long as their own settings say.

### Who sees which runs

`expKernelTransferHistory::visibleFilter()` gives a user the runs of the tools the registry lists for them (the
tool is active and its policy is granted). Of those, a user sees every user's runs only when the tool's
`HistoryAllPermission=` is granted (Export: `xrowextract/all_jobs`), and otherwise only their own.

### Reading it

```php
expKernelTransferHistory::listVisible( array( 'tool' => 'transfer.bccie', 'from' => $from, 'to' => $to ), 50 );
expKernelTransferHistory::statisticsRows( array( 'from' => $from, 'to' => $to ) );   // what the user may see
expKernelTransferHistory::toolOptions();                                              // for a tool filter
expKernelTransferHistory::listRuns( $filter, $offset, $limit );                       // no rights applied
expKernelTransferHistory::count( $filter );
```

A filter takes `tool` (one id or a list), `direction`, `kind`, `state` (one or a list), `user_id`, `from` (included)
and `to` (excluded) on the start. `statisticsRows()` returns the rows in the shape of a run of Imports
(`id`, `import_id` 0, `tool`, `user_id`, `run_kind`, `status` as `completed`, `completed_with_errors`, `failed`,
`cancelled` or `running`, `started_at`, `finished_at`, and the `*_count` counters with `processed_count` the rows
done without the failed ones and `failed_count` the failed rows), so the Statistics page counts them with the same
functions as its own runs.

## Tests

`expKernelTransferRunsTest` (without a database): the table is the same in every schema and update file, Oracle's
identifier limits, the SQLite update run twice, the values a row can hold, the statistics row, the filters, the
engine choice and its fallback, the payload check, a task run with its progress, a cancel and a failure, the new
registry keys and who sees which runs, the settings, and the content job type.
