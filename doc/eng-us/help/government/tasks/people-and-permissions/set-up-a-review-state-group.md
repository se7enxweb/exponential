---
title: Set up a review state group
audience: government, admin
category: public-sector
order: 300
summary: Step by step - create a "Review" state group with Draft, In review and Approved, mark existing public content Approved first, then let visitors read only approved content and give authors and reviewers the right state changes.
keywords: object states, state group, review, Draft, In review, Approved, StateGroup, NewState, state/assign, Setup States, content review, approval
type: help
---
# Set up a review state group

This task is for administrators. At the end, content carries a review stage, visitors see only *Approved* content,
authors can send their drafts to review and reviewers approve. It follows
[Object states, sections 4 and 9](../../../../guides/object-states.md#9-a-worked-example-a-review-workflow). Background:
[Object states in permissions](../../operations/people-and-permissions/object-states-in-permissions.md).

## Contents
1. [Before you start](#before-you-start)
2. [Create the group and states](#create-the-group-and-states)
3. [Approve existing content first](#approve-existing-content-first)
4. [Change the roles](#change-the-roles)
5. [Check it worked](#check-it-worked)
6. [Related pages](#related-pages)

## Before you start

- `state/administrate` (groups and states), `state/assign` (setting them) and the `role` module; administrators
  have them.
- A quiet moment: step 3 runs over the whole site.
- Consider using the approval workflow instead if you only need sign-off before publishing
  ([Approval workflows overview](../../operations/editorial-workflows/approval-workflows-overview.md)).

## Create the group and states

1. **Setup > States** (`/state/groups`), **New state group**.
2. **Identifier** `review` (lower-case, digits and underscores, not starting with `ez`); name `Review` in the main
   language. **Create**.
3. On the group's page, **New state**: identifier `draft`, name `Draft`. **Create state**. The first state becomes
   the default and is given to every existing object.
4. Again: `in_review` / `In review`, then `approved` / `Approved`.

## Approve existing content first

Every object is now in *Draft*. Before limiting visitors:

1. Open the top node of the public site, tab **Details**, box **Content state**.
2. Choose *Approved*, tick **Also for everything below this node**, press **Set states**.
3. A large subtree becomes a content job after a confirmation; wait until it has finished.
4. Do the same for the media library if visitors see it.

## Change the roles

| Role | Change |
|---|---|
| Anonymous | its `content/read` policy: add `StateGroup_review = Approved` |
| Authors (department editors) | `content/edit` with `StateGroup_review = Draft`; `state/assign` with `Owner = Self`, `StateGroup_review = Draft`, `NewState = review/in_review` |
| Reviewers | `state/assign` with `NewState = review/draft, review/approved` |

Each change: the role, **Edit**, **New policy** or **Limitations**, choose the values, **OK**, **Save**.

## Check it worked

- **Setup > States**, the group: "Roles that use this group" lists Anonymous, the authors' and reviewers' roles.
- A new article stays invisible in a private window until a reviewer sets *Approved*.
- An author can set *In review* and then no longer edit; a reviewer can approve.
- State changes appear in the audit trail as `content.object.state`.

## Related pages
- [Review dates with object states](../../operations/editorial-workflows/review-dates-with-object-states.md)
- [Legal review step](../../operations/editorial-workflows/legal-review-step.md)
- [Object states guide](../../../../guides/object-states.md)
- [Content jobs](../../../../features/6.0/content-jobs.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
