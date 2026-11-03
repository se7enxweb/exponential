---
title: Planned changes with OpenSpec
audience: developer
category: standards
order: 95
summary: How a larger change to Exponential is planned before it is built - the OpenSpec directory, the plan-driven schema, the order proposal, specs, design, tasks, plan, apply and verify, and where finished changes and their specs go.
keywords: OpenSpec, specification, proposal, design, tasks, plan, verify, change, archive, plan-driven, schema, handoff, workflow designer, admin search
type: reference
---
# Planned changes with OpenSpec

This page is for developers who plan a larger change to Exponential, or build one somebody else planned. It explains
the `openspec/` directory at the root of the repository and how a change moves through it. Small fixes do not need
it; a change that adds a feature, a module or an extension, or that touches several designs, should start there.

OpenSpec ([github.com/Fission-AI/OpenSpec](https://github.com/Fission-AI/OpenSpec)) keeps the plan of a change as
plain Markdown next to the code, so the plan is reviewed in the same pull request as the code and stays in the
history. Nothing in `openspec/` is loaded by Exponential at run time.

## The directory

| Path | What it holds |
|---|---|
| `openspec/config.yaml` | The project context and the rules every artifact follows: architecture rules, the checks a change cites, the platform matrix of databases and search engines |
| `openspec/schemas/plan-driven/` | The schema: which artifacts a change has, in which order, and the template of each. `SOURCE` says where the shared schema is maintained |
| `openspec/changes/<name>/` | A change in progress: `proposal.md`, `specs/`, `design.md`, `tasks.md`, `plan.md`, `verify.md` |
| `openspec/changes/archive/<date>-<name>/` | A change that is finished and merged, kept as it was |
| `openspec/specs/` | The current behavior, one spec per capability; an archived change merges its specs here |
| `openspec/handoff/` | A longer specification of a ticket that changes refer to as their design source, with its mockups |

## The steps of a change

The schema `plan-driven` orders the artifacts as **proposal, specs, design, tasks, plan, apply, verify**:

1. **Proposal**: why the change is needed, what changes, which ticket it implements and what is out of scope.
2. **Specs**: the observable behavior as requirements with scenarios (*WHEN* ... *THEN* ...); no class names or
   file paths. Where behavior differs per siteaccess, role or search engine, a scenario says so.
3. **Design**: the extension that carries the change (and why any edit in `lib/` or `kernel/` is needed), the INI
   files and keys, the policies and limitations, the database tables and how an existing installation gets them.
4. **Tasks**: groups of work; each group lands its own tests and documentation.
5. **Plan**: the tasks as executable steps, test first.
6. **Apply**: the work, in a separate worktree, never in the main checkout.
7. **Verify**: the exact phpunit run line and result of each affected suite, and the other project checks.

When the change is merged it moves to `openspec/changes/archive/` and its specs update `openspec/specs/`.

## Planned and finished changes

| Change | State |
|---|---|
| `add-workflow-definitions`, `add-workflow-runtime`, `add-workflow-jobs-and-timers`, `add-workflow-admin-ui`, `add-workflow-review-options`, `add-workflow-integrations`, `add-workflow-webhooks-and-importers`, `add-workflow-reference-cases` | Planned: the graphical editorial workflows of the extension `expworkflow`; design source `openspec/handoff/expworkflow-16702-spec.md` |
| `archive/2026-11-02-add-admin-search` | Finished: search suggestions while typing and the search page with its filter bar; specs in `openspec/specs/admin/` |

## Related pages

- [Testing and continuous integration](testing-and-ci.md)
- [Source control and releases](source-control-and-releases.md)
- [Documentation and publishing](documentation-and-publishing.md)
- [Coding standards](coding-standards.md)
