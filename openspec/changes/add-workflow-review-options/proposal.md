# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 6 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 3 (review options), 7
(ezapprove2, ezapprovetanta, oceditorialstuff), 8.5, 12.6.
Meant for upstream (se7enxweb). Depends on `add-workflow-integrations` and `add-workflow-admin-ui`.

## Why

Sites that use ezapprove2, ezapprovetanta or oceditorialstuff need multi-approver review,
notifications on every review step and an editorial board before they can drop those extensions.

## What Changes

- `quorum` (`min` distinct votes, votes per instance and stage, reset on re-entry), `veto` on a
  competing transition, `author_selects` (author picks reviewers from groups on submit),
  `reviewers_may_edit` (**verify** ezapprove2's behavior first; a reviewer's edit resets the votes).
- `notify` with `on` = `enter`, `comment`, `vote`, `transition`.
- Editorial dashboard `expworkflow/dashboard`: one board per definition, a column per stage, cards
  with time in stage, due timer, votes; filters; paginated over instances; only readable content;
  available transitions as buttons, no drag and drop. Policy `expworkflow/dashboard`.

## Capabilities

### New Capabilities
- `workflow/review`: quorum, veto, author-selected reviewers, reviewer edits.
- `workflow/dashboard`: the editorial board.

### Modified Capabilities
- `workflow/transition`: a quorum transition fires only after enough votes.
- `workflow/builtin-actions`: `notify` options per review step.

## Impact

Tables `expwf_vote`, `expwf_reviewer` (created in milestone 1) come into use.

## Out of scope

Importing ezapprove2 settings (milestone 7).
