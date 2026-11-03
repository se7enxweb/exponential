# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 2 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 5 (pinning, migration), 6.1–6.5, 6.7, 9, 12.2.
Meant for upstream (se7enxweb). Depends on `add-workflow-definitions`.

## Why

Definitions and bindings exist after milestone 1, but nothing runs them. Reference case 1 (editorial
approval: draft → review → approved → published, with send-back) needs instances, transitions fired
by people, the publish gate and tasks in the collaboration inbox.

## What Changes

- Instances: start at `start` via the gate (6.2a) or explicitly (`expWorkflowEngine::start()`, 6.2b);
  one running instance per content version; the instance pins its definition version.
- `expWorkflowEngine::fire()`: one code path for all triggers, compare-and-set on the instance row,
  permission check, history, entering the new stage.
- Object states: one state group `wf_<identifier>` (or the named `state_group`) with one state per
  stage, assigned as the configured system user (`expworkflow.ini [Runtime] SystemUserID`); manual
  state changes recorded as drift.
- Publish gate: event type `event_expworkflowgate` on `content/publish/before`, mirroring `ezapprove`
  (accepted, deferred to cron, cancelled with version back to draft on send-back);
  `./console ext:expworkflow:install-gate` (`--check`, idempotent, appends to an existing trigger workflow).
- Collaboration handler `expworkflow` with `inboxState()` and the person transitions of the current stage.
- Admin action "Move running instances to vN" (stage mapping by id, refusing missing stages).
- Policies `expworkflow/transition` (limitations Workflow, Transition, Class, Section, Subtree) and `expworkflow/start`.
- A CLI to import a definition JSON file and fire transitions, so case 1 runs before the editor exists.

## Capabilities

### New Capabilities
- `workflow/instance`: starting, pinning, migrating and finishing instances.
- `workflow/transition`: firing a transition, permissions, history, stale-fire refusal.
- `workflow/publish-gate`: how a stage kind decides the publish operation.
- `workflow/object-state`: stages as object states, system user, drift.
- `workflow/inbox`: tasks for assignees and authors in the collaboration inbox.

### Modified Capabilities
- `workflow/definition`: none expected; listed for the review of `state_group`.

## Impact

New classes, event type, collaboration handler, module `expworkflow` with two policies, settings in
`workflow.ini.append.php`, `collaboration.ini.append.php`, `expworkflow.ini [Runtime]`. No kernel changes.
Publishing of bound classes now waits for the cron part `workflow` after the last approval (documented latency).

## Out of scope

Job and timer transitions (milestone 3), the Workflow tab and editor (milestone 4), review options
(milestone 6), starting automatically on version creation (6.2c, follow-up ticket).
