---
title: Mark content under review with object states
audience: government, editor
category: public-sector
order: 560
summary: For editors - label a page as under review, approved, superseded or withdrawn with an object state on the Details tab (Content state, Set states), for one page or a whole subtree, without making a new version; and what states do and do not do.
keywords: object states, Content state, Set states, Details tab, under review, superseded, withdrawn, state group, subtree, NewState, label content
type: help
---
# Mark content under review with object states

This page is for editors who label content with a status: *Under review*, *Approved*, *Superseded*, *Withdrawn*.
Object states are labels from state groups your administrators create; setting one makes no new version and does not
change the text ([Object states, 7](../../../../guides/object-states.md#7-setting-states-on-content)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What states do](#what-states-do)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- A state group with the states you need exists (**Setup > States**, made by an administrator).
- Your role has `state/assign`; the `NewState` limitation may allow you only some states (for example not
  *Withdrawn*).

## Steps

**One page:**

1. Open the page, tab **Details**, box **Content state**.
2. In the select of the group, choose the state; press **Set states**.

**A page and everything below it:**

1. Same box; tick **Also for everything below this node** before **Set states**.
2. Large subtrees run as a background job after a confirmation; follow it at `/content/jobs`.

**While editing a draft** of a new page, the edit view has the same selects with **Set**.

## What states do

| States do | States do not |
|---|---|
| Label content; templates can show the label ("Superseded") | hide content by themselves |
| Let roles limit reading or editing by state (for example only reviewers read *Under review* content) | make a new version or change text |
| Appear in the audit | move content |

Whether visitors see a state, and whether a state hides content from them, depends on your templates and roles. Ask the
web team what each state does on your site.

## Check the result

- [ ] The **Content state** box shows the new state.
- [ ] The page shows the label where your templates display it (signed out, if it is a public label).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Expecting *Withdrawn* to hide the page | hide it too if it must disappear ([Hide a page temporarily](hide-a-page-temporarily.md)) |
| The select is disabled | you may set only the current state in that group |
| Groups whose identifier starts with `ez` | belong to the system (`ez_lock`) and are not for editorial states |

## Related pages

- [Publishing tasks](README.md)
- [Guidance for regulated parties](../../levels/agencies/guidance-for-regulated-parties.md)
- [Sections and object states](../../../creators/website-creators/sections-and-object-states.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
