# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 5 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 2 (case 2), 7, 7.4, 12.5.
Meant for upstream (se7enxweb). Depends on `add-workflow-jobs-and-timers`.

## Why

The registry of milestone 3 has no concrete actions yet. Reference case 2 (machine translation per
language, then human review) and the notifications of case 1 need them.

## What Changes

- Actions `notify` (notification event like ezapprove, reminders on the same path), `hide`, `unhide`,
  `webhook` (sends to matching hooks; the hook admin follows in milestone 7), `expai.draft`
  (`expAiQueue::request()` or a chain), `expai.translation.check` (`expAiTranslationSync`).
- **First task: verify** whether expai publishes translations through the publish operation (then
  case 2 works through a language binding) or writes them another way (then the action starts the
  instance explicitly). The result decides the design and is recorded in design.md.
- A separate small PR in the expai package so `expAiApprovalCheck` knows the gate (not part of this
  extension).

## Capabilities

### New Capabilities
- `workflow/builtin-actions`: behavior of each built-in action.
- `workflow/translation-flow`: one instance per language version, the source never waits.

### Modified Capabilities
- `workflow/action`: none expected.

## Impact

Optional dependency on expai (actions report "expai not active" in validation when it is missing).

## Out of scope

Per-step notification options (`notify` `on` = comment/vote/transition, milestone 6); webhook admin,
signing and delivery log (milestone 7).
