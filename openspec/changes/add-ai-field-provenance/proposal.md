# Proposal

Redmine #16741 (https://pm.team.in-mv.com/issues/16741), change 5 of 7.
Binding design source: `openspec/handoff/marketing-automation-16741.md`, sections 2 and 5.5.
Meant for upstream (se7enxweb). The code lands in the package `se7enxweb/expai`; this change is the plan.
No dependency.

## Why

Campaign content is increasingly drafted with the AI service: teasers, subject lines, short descriptions for
search engines and channels. expai marks AI-written drafts and packages, but once a draft is edited and
published, nothing shows which field still holds an unchecked AI text. A plausible but wrong sentence then goes
out in a newsletter or a channel post as if an editor had written it.

## What Changes

- expai records, per content object, version, language and attribute, that the current value was written by
  the AI service.
- The edit form and the full view in admin4 show an "AI — check" mark at each such field.
- A person's change to the field, or an explicit "Checked" on it, removes the mark.
- A later AI run may replace a field that still carries the mark; it SHALL NOT replace a field a person has
  written or checked.
- The mark is carried to a new version when the field is not changed.
- AI actions that fill or rewrite fields are offered only to users who may edit the version; read access alone is
  not enough.

## Capabilities

### New Capabilities
- `ai/field-provenance`: recording, showing and clearing the mark, protecting human input, the edit-access rule.

### Modified Capabilities
- none in this repository's specs.

## Impact

- Package `se7enxweb/expai`: a table or an extension of the existing draft log (decided in design.md after the
  verification in handoff section 5.5), templates of the admin4 edit form and full view, the policy check.
- This repository: the `composer.json` constraint once the release exists; the change is archived then.

## Out of scope

- Marks in the public site design.
- Detecting AI text that was pasted in by hand.
- The workflow action that asks a person to check AI fields (a stage in a workflow definition does that).
