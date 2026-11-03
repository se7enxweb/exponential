# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 3 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 6.6, 7.4, 12.3.
Meant for upstream (se7enxweb). Depends on `add-workflow-runtime`.

## Why

Reference cases 3 (AI draft with success and failure path) and 4 (publish at date X, expire at date
Y with a reminder) need transitions fired without a person: by the result of a job and by time.
expqueue has no completion callback, so the extension needs watchers and timers on top of it.

## What Changes

- Action registry: `expworkflow.ini [Actions] List[]` with `[Action_<name>] Class=`; interface
  `expWorkflowAction` (`validate()`, `run()`, `isJob()`); job actions run in the queue job type
  `expworkflow.action`, never in a web request.
- Job-result transitions: on entering a stage with a job action, enqueue it (idempotency key
  `expwf:{uuid}:{stage}:{n}`), store `wait_job`, enqueue the watcher `expworkflow.watch`, which defers
  while the job runs and fires `on: done` or `on: failed` (`dead`, `cancelled`). No `depends_on`.
- Timers: `expworkflow.timer` with `not_before = due`, re-check before firing, cancel on leaving the
  stage; reminders before a timer; attribute and duration forms with `fallback`.
- Cron part `expworkflow`: sweep overdue pending timers, recompute timers whose date attribute
  changed on a newer published version.
- Validation of action parameters through the action classes (replaces the name-only check).

## Capabilities

### New Capabilities
- `workflow/action`: the registry, action contract, job execution in workers.
- `workflow/job-transition`: watcher behavior for done and failed jobs.
- `workflow/timer`: scheduling, firing, cancelling, reminders, sweep.

### Modified Capabilities
- `workflow/definition`: action names are validated by their classes, including parameters.

## Impact

Job types in `settings/expqueue.ini.append.php`, a cron part, handlers. Requires expqueue workers.
`ezwaituntildate` stays untouched (decision in the build spec).

## Out of scope

The concrete actions `notify`, `hide`, `webhook`, `expai.*` (milestone 5); webhooks admin (milestone 7).
