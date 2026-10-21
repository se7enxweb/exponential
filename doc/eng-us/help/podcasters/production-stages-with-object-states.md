---
title: Production stages with object states
audience: podcaster
category: podcasting-streaming
order: 31
summary: How a show tracks episodes through production stages such as recorded, edited, notes written and ready with Exponential's object states - a state group of your own, policies that depend on the stage, keeping unfinished episodes off the site, and the checks that the feed stays clean.
keywords: object states, production stages, workflow stages, recorded, edited, ready, review, state group, state/assign, StateGroup limitation, NewState, roles, episode status
type: help
---
# Production stages with object states

This page is for producers and administrators of shows with a team: a host records, an editor cuts, someone writes
notes, someone signs off. **Object states** let every episode carry its stage, and roles can depend on it. The full
reference is [Object states](../../guides/object-states.md); this page applies it to a show.

## Contents

1. [What states do](#what-states-do)
2. [A state group for a show](#a-state-group-for-a-show)
3. [Roles by stage](#roles-by-stage)
4. [Setting the stage](#setting-the-stage)
5. [Keeping unfinished episodes off the site](#keeping-unfinished-episodes-off-the-site)
6. [States or approval?](#states-or-approval)

## What states do

An object state is a label every content object carries, one from each state group. States change nothing by
themselves; roles make them matter. A policy limited by `StateGroup_<group>` applies only to objects in the chosen
states, and the `NewState` limitation of `state/assign` says which states a user may set
([Object states](../../guides/object-states.md#8-states-in-roles-and-policies)). Groups and states are managed under
**Setup > States**.

## A state group for a show

Example group `production`, with its states in order (the first is the default for new objects):

| State | Meaning |
|---|---|
| `planned` | the episode item exists as a placeholder |
| `recorded` | raw audio is in |
| `edited` | the final audio is uploaded |
| `notes` | show notes, guests and transcript are done |
| `ready` | approved for release |

The first state you create in a new group is given to every object that exists. If the site already has published
content, set that content to the final state first, as the object states guide's worked example does
([A worked example](../../guides/object-states.md#9-a-worked-example-a-review-workflow)).

## Roles by stage

| Role | Policies (examples) |
|---|---|
| Host | `content/edit` with `StateGroup_production` = planned, recorded; `state/assign` with `NewState` = production/recorded |
| Audio editor | `content/edit` with `StateGroup_production` = recorded, edited; `state/assign` with `NewState` = production/edited |
| Notes writer | `content/edit` with `StateGroup_production` = edited, notes; `state/assign` with `NewState` = production/notes |
| Producer | `content/edit` without state limit; `state/assign` with `NewState` = production/ready and back to any earlier state |

Combine these with a **Subtree** limitation to the show's folder, so a role for one show cannot touch another
([Roles and policies](../../guides/roles-and-policies.md)).

## Setting the stage

On an episode, open the **Details** tab and set the **Content state**. A whole season can be set at once with "Also
for everything below this node" ([Setting states on content](../../guides/object-states.md#7-setting-states-on-content)).

## Keeping unfinished episodes off the site

States alone do not unpublish anything. Three ways keep unfinished episodes away from listeners:

1. **Do not publish them.** Work in drafts until the episode is complete; this is the simplest.
2. **Limit the Anonymous role's `content/read`** with `StateGroup_production = ready`: visitors see only ready
   episodes; everything else is invisible to them and not listed in fetches.
3. **Use approval** for the final step ([Approvals before release](approvals-before-release.md)).

If you choose the second way, open the podcast feed signed out after each change and check that only ready episodes
are listed, before you rely on it.

## States or approval?

| | Object states | Approval workflow |
|---|---|---|
| Shows the stage at a glance | yes | only "pending" |
| Blocks publishing | only through policies | yes |
| Several steps | yes, as many states as you need | one approval per event |
| Needs the workflow cronjob | no | yes |

Many shows use both: states for the steps of production, approval for the final sign-off.

## Related pages

- [Object states](../../guides/object-states.md)
- [A release calendar](a-release-calendar.md)
- [Roles for your team](../customers/day-to-day/roles-for-your-team.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
