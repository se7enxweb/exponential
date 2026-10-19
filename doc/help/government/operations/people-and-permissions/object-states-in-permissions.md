---
title: Object states in permissions
audience: government, admin, editor
category: public-sector
order: 40
summary: How object states (for example Draft, In review, Approved, or Legal cleared) let a public body tie staff and visitor access to the stage a piece of content has reached, with the limitations StateGroup and NewState and an example for a review process.
keywords: object states, state groups, StateGroup, NewState, state/assign, review, approval, legal clearance, embargo, content lifecycle, permissions
type: help
---
# Object states in permissions

This page is for administrators who want access rights to depend on where a piece of content stands in its life: in
review, approved, cleared by legal, archived. Exponential's object states do exactly this. The facts come from
[Object states](../../../../guides/object-states.md); this page shows how a public body uses them.

## Contents
1. [What states add to roles](#what-states-add-to-roles)
2. [The two limitations](#the-two-limitations)
3. [Example: a review group](#example-a-review-group)
4. [States, approvals and the audit trail](#states-approvals-and-the-audit-trail)
5. [Limits](#limits)
6. [Related pages](#related-pages)

## What states add to roles

Sections say where content belongs; the tree says what it sits under; **states** say how far along it is. Every
object carries one state from every state group. Exponential ships one group, `ez_lock` (Not locked, Locked), and you
add your own under **Setup > States** (`/state/groups`). States change nothing by themselves: roles make them matter
([Object states, "In short"](../../../../guides/object-states.md#in-short)).

Typical public-sector state groups (examples; name them for your own process):

| Group | States | Purpose |
|---|---|---|
| Review | Draft, In review, Approved | only approved content is public |
| Legal | Not needed, Pending legal, Cleared | legal sign-off before publication |
| Accessibility check | Not checked, Checked | track a manual accessibility check |
| Retention | Current, Due for review, Archived | mark pages to look at again |

## The two limitations

| Limitation | Offered by | Effect |
|---|---|---|
| `StateGroup_<identifier>` | `content/read`, `edit`, `publish`, `manage_locations`, `remove`, `state/assign` | the policy applies only to objects in the chosen states |
| `NewState` | `state/assign` | which states the user may set (`group/state`) |

A `content/read` policy with `StateGroup_review = Approved` lets its role read approved objects only; everything else
of that group is invisible to it, in pages and in fetches
([Object states, section 8](../../../../guides/object-states.md#8-states-in-roles-and-policies)). Groups whose
identifier starts with `ez` belong to Exponential and cannot be used in policies.

## Example: a review group

The worked example of the guide, mapped to public-sector roles
([Object states, section 9](../../../../guides/object-states.md#9-a-worked-example-a-review-workflow)):

| Role | Policy | Limitations |
|---|---|---|
| Anonymous (visitors) | `content/read` | Section Standard, `StateGroup_review = Approved` |
| Department author | `content/create`, `content/edit` | Owner Self, `StateGroup_review = Draft` |
| Department author | `state/assign` | Owner Self, `StateGroup_review = Draft`, `NewState = review/in_review` |
| Reviewer | `content/read`, `content/edit` | (subtree of the department) |
| Reviewer | `state/assign` | `NewState = review/draft, review/approved` |

Before you limit the Anonymous role, set the content that is already public to Approved, on the top node with "Also
for everything below this node"; otherwise the whole site disappears for visitors at once, because every existing
object starts in the group's first state.

## States, approvals and the audit trail

- Each state change on an object is recorded in the audit trail as `content.object.state`, with the states before
  and after ([Object states, section 12](../../../../guides/object-states.md#12-workflows-events-audit-and-search)).
  That record is the evidence a reviewer approved something.
- States and the approval workflow are two different tools. The workflow holds a version back until an approver
  decides ([Approval workflows overview](../editorial-workflows/approval-workflows-overview.md)); states keep a
  stage on the object across versions. Many organizations use the workflow for publishing and a state for a separate
  check such as legal clearance ([Legal review step](../editorial-workflows/legal-review-step.md)).
- A workflow can run on state changes once `workflow.ini [OperationSettings] AvailableOperationList[]=content_updateobjectstate`
  is set; **Setup > Triggers** then offers the operation.

## Limits

- A state is not a version: setting a state creates no new version, and a later edit does not reset the state
  unless your process or a workflow does so.
- States do not hide content by themselves; only a read policy with a state limitation does.
- Exponential has no date field on a state; a "due for review" state is set by people or by your own code, not by a
  timer. See [Review dates with object states](../editorial-workflows/review-dates-with-object-states.md).

## Related pages
- [Set up a review state group](../../tasks/people-and-permissions/set-up-a-review-state-group.md)
- [Roles, policies and limitations](roles-policies-and-limitations.md)
- [Separation of duties](separation-of-duties.md)
- [Object states guide](../../../../guides/object-states.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
