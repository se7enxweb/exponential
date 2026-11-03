# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 1 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 3, 4, 5 and 12.1.
Meant for upstream (se7enxweb): a new extension `expworkflow` for any Exponential 6 installation.

## Why

Editorial workflows in Exponential are assembled today from separate extensions (ezapprove,
ezwaituntildate, ezapprove2, OWEditorial, xrowworkflow …), each with its own model and none
drawable. `expworkflow` replaces them with one stage-and-transition model. Everything later
(runtime, editor, importers) reads and writes a workflow definition and chooses one per content
version, so the definition format, its validation, its storage and the binding rules come first.

## What Changes

- New extension `extension/expworkflow/` with the extension skeleton, `expworkflow.ini` and docs
  stubs in the expqueue layout. No kernel file changes.
- A versioned workflow definition as JSON (section 3 of the build spec): stages with `kind`
  (`draft`, `waiting`, `published`, `closed`), transitions with `by` (`person`, `job`, `timer`),
  `start`, optional `state_group`, `layout`.
- Validation of a definition with one error per finding, each tied to a stage, a transition or
  the definition, so a later editor can show it at the node or edge.
- Definition versions: save as a draft version, publish one version per identifier (the previous
  published version becomes archived), read the published version.
- Binding rules: which published definition a content version gets, most specific rule wins.
- Storage for all tables of the extension (definitions, bindings, instances, history, timers,
  votes, reviewers, hooks, deliveries) on MySQL/MariaDB, PostgreSQL, SQLite, Oracle and MongoDB,
  installed and upgraded by `./console ext:expworkflow:install-schema` (`--check`,
  `--print=<engine>`, `--write-files`).
- A test suite `expworkflow` in the root `phpunit.xml`.

## Capabilities

### New Capabilities
- `workflow/definition`: the definition format, its validation and its version lifecycle
  (draft, published, archived).
- `workflow/binding`: choosing the published definition for a content version by class, subtree,
  section and language.
- `workflow/storage`: installing, checking and upgrading the extension's tables on every
  supported database engine.

### Modified Capabilities
<!-- none: no specs exist yet -->

## Impact

- New: `extension/expworkflow/` (classes, settings, `bin/php/install-schema.php`, `share/db_schema.dba`,
  `sql/{mysql,postgresql,sqlite,oracle}/schema.sql`, `sql/mongodb/schema.json`, tests, docs).
- Changed: root `phpunit.xml` (suite `expworkflow`), the CI workflow that runs it.
- No existing installation is affected: the extension is new and not activated anywhere by this
  change. Activating it creates tables only when `install-schema` runs.
- Depends on the kernel's DBA schema handlers and on the patterns of `expQueueSchema`; no
  dependency on expqueue or expai yet.

## Out of scope

- Instances, firing transitions, the publish gate, object states, the inbox → `add-workflow-runtime`
  (milestone 2). Pinning a running instance to a definition version and migrating running
  instances to a new version also belong there; this change only stores versions.
- Action classes and their parameter validation → `add-workflow-jobs-and-timers` (milestone 3).
  Here an action name is only checked against the configured list of action names.
- Drawflow converter, editor, list, overview → `add-workflow-admin-ui` (milestone 4).
- Review options `quorum`, `author_selects`, `reviewers_may_edit` are accepted and checked for
  shape here; their behavior → `add-workflow-review-options` (milestone 6).
- Policies (`expworkflow/*`) → milestones 2 and 4; this change has no web surface.
