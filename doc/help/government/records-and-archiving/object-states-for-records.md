---
title: Object states for records
audience: government, admin, editor
category: public-sector
order: 70
summary: For administrators of public sites - how to use Exponential's object states to mark the lifecycle of content (current, archived, under legal hold, cleared for publication), control who may see and change content in each state, and set states on whole subtrees.
keywords: object states, state groups, lifecycle, archived, legal hold, StateGroup limitation, NewState, state/assign, Content state, subtree, content job, expstate, records, public sector
type: help
---
# Object states for records

This page is for administrators. An **object state** is a label every object carries, one from each state group. States
change nothing by themselves; roles make them matter: a policy limited by a state group applies only to objects in the
chosen states ([Object states](../../../guides/object-states.md)). For records, states express where content is in its
lifecycle.

## Contents

1. [Groups for records](#groups-for-records)
2. [Setting it up](#setting-it-up)
3. [Policies that use states](#policies-that-use-states)
4. [Setting states in bulk](#setting-states-in-bulk)
5. [Related pages](#related-pages)

## Groups for records

| Group (example) | States | Use |
|---|---|---|
| `lifecycle` | Current (default), Archived | separate current from archived content without moving it |
| `hold` | None (default), Legal hold | protect content from editing and removal during a hold (see "Legal holds") |
| `clearance` | Draft (default), Cleared | only cleared content may be read by visitors |

The first state of a group is its default; when a group gets its first state, every existing object gets it.

## Setting it up

1. **Setup > States** (`/state/groups`): create the group (identifier must not start with `ez`), then its states in order.
2. Translate names and descriptions for each administration language.
3. Change roles (below).
4. Test with a test user before telling editors.

## Policies that use states

- `StateGroup_<group>` limits `content/read`, `content/edit`, `content/publish`, `content/manage_locations`,
  `content/remove` and `state/assign` to objects in the chosen states.
- `NewState` on `state/assign` lists which states a role may set.

Examples:

| Goal | Policy |
|---|---|
| Visitors see only current content | Anonymous: `content/read` with `StateGroup_lifecycle = Current` (in addition to its section limitation) |
| Nobody edits content under hold | editors' `content/edit` and `content/remove` limited to `StateGroup_hold = None` |
| Only records staff set or lift a hold | records role: `state/assign` with `NewState = hold/legal_hold, hold/none`; editors without it |

Policies of several roles add up: check every role an editor has.

## Setting states in bulk

- One object: the node's **Details** tab, **Content state**.
- A subtree: the same, with "Also for everything below this node" (a content job when large).
- From a shell or a remote client: the `expstate` services described in the guide.

State changes are recorded in the audit as `content.object.state` (on by default).

## Related pages

- [Object states](../../../guides/object-states.md)
- [Sections and object states for site builders](../../creators/website-creators/sections-and-object-states.md)
- [Legal holds](legal-holds.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
