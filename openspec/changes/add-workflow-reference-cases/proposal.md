# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 8 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 2, 10, 12.8 and "Acceptance".
Meant for upstream (se7enxweb). Depends on all earlier `add-workflow-*` changes.

## Why

The extension is done when all four reference cases are drawn in the editor and run end to end on a
real database, and when docs, translations and CI are complete. This change proves that and closes
the gaps it finds.

## What Changes

- The four reference cases as fixtures (definitions drawn in the editor and exported), run end to end
  on a throwaway database with a short measured report.
- An acceptance test per item of the build spec's "Acceptance" list.
- Docs complete: `README.md`, `INSTALL.md`, `USAGE.md`, `AGENTS.md`, `doc/index.md` (format, runtime
  and gate, actions, editor, overview, database, CLI, troubleshooting, changelog).
- Translations: contexts `extension/expworkflow` and `extension/expworkflow/editor` in
  `untranslated`, `eng-GB`, `ger-DE`.
- CI step for the suite, including `--group database`.

## Capabilities

### New Capabilities
- none: this change verifies the capabilities of the earlier changes.

### Modified Capabilities
- Only where a reference case shows a gap; each such delta names the case that found it.

## Impact

None on behavior unless a gap is found.

## Out of scope

Parallel paths, BPMN, automatic start on version creation, removing old setups (section 11).
