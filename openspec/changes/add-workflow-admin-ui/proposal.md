# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 4 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 8.1–8.4, 9, 12.4; mockup
`openspec/handoff/workflow-designer.png`.
Meant for upstream (se7enxweb). Depends on `add-workflow-jobs-and-timers`.

## Why

Editors and administrators should draw workflows in admin4 and see, in one place, everything that
happens around publishing. Without the UI the extension is usable only from the CLI.

## What Changes

- Drawflow 0.0.60 (MIT) vendored under `design/admin4/javascript/vendor/drawflow/`, its license next
  to it. **First task: measure Drawflow under the admin CSP** in Chrome, Firefox and Safari; a needed
  patch stays small, documented and guarded by a test.
- Converter Drawflow export ↔ definition JSON (positions to `layout`); nodes built with
  `createElement`/`textContent`, no `innerHTML` with markup, no inline styles.
- `expworkflow/list` (definitions, published version, bindings, instances per stage, Edit/Duplicate/Archive).
- `expworkflow/edit/<identifier>`: canvas, side panel, palette, binding chips, Validate, Discard,
  "Save as vN"; findings shown at nodes and edges.
- Content tab "Workflow": mini graph, allowed transitions with comment, history, timers, waiting job,
  "Start workflow"; POSTs through ezjscore with `X-CSRF-Token`.
- `expworkflow/overview`: definitions and bindings with warnings, kernel workflows and triggers,
  gate conflicts, job types with dead letters, running instances, overdue timers, drift.
- Menu group `workflow` links, icons, policy `expworkflow/design`.

## Capabilities

### New Capabilities
- `workflow/editor`: drawing, converting, validating and saving definitions.
- `workflow/content-tab`: the per-content Workflow tab.
- `workflow/overview`: the read-only administrator overview.

### Modified Capabilities
- none expected.

## Impact

Templates and JavaScript in `design/admin4/`, `admininterface.ini`, `menu.ini.append.php`,
`design.ini` icons, `ezjscore.ini`. CSP rule tests like expqueue's `ScriptRulesTest`/`StylingRulesTest`.

## Out of scope

Editorial dashboard (milestone 6), webhook admin (milestone 7), list of replaceable extensions in the
overview (milestone 7).
