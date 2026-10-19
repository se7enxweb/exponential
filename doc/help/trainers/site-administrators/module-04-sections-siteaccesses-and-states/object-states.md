---
title: "Lesson 4.3: Object states"
audience: trainer, admin
category: training
order: 43
summary: Learners create a state group with its states, set states on content, and make states matter with a StateGroup limitation, building a small review workflow. 45 minutes.
keywords: site administration, object states, state group, default state, ez_lock, StateGroup limitation, NewState, state/assign, review workflow, content state, details tab
type: help
---
# Lesson 4.3: Object states

An object state is a label every content object carries, one from each state group: a stage of your own such as
Draft, In review and Approved. States change nothing by themselves; roles make them matter. This lesson builds the
smallest useful example: a review group, and visitors who see approved content only.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 4: Sections, siteaccesses and object states](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Site administrators |
| Prerequisites | [Lesson 4.1](sections.md); [Module 3](../module-03-roles-and-policies/README.md) |

## Learning objectives

After this lesson, learners can:

- read **Setup > States** and explain groups, states and the default state;
- create a state group and its states in the right order;
- set a state on one object and on a subtree from the **Details** tab;
- limit a policy with `StateGroup_<identifier>` and allow setting states with `NewState`;
- explain why the system group `ez_lock` cannot be edited or used in policies.

## Trainer's notes

### How to present it

Source: [Object states](../../../../guides/object-states.md), sections 1 to 4, 7, 8 and 9.

1. **The idea** (3 minutes). Sections group content by area; states group content by stage. A policy can use both.
2. **The page** (3 minutes). **Setup > States** (needs `state/administrate`): figures, then one card per group with
   its states as a chain, `default` on the first, the objects in each, the policy limitation and the roles that use
   it. The shipped group **Lock** (`ez_lock`, Not locked and Locked) is *System, protected*.
3. **The default** (2 minutes). The first state of a group is the default for new objects; the first state created
   in a new group is given to every existing object. So create the states in order, and set existing public content
   to the "approved" state **before** limiting visitors to it.
4. **Making it matter** (5 minutes). `StateGroup_review` on `content/read`, `content/edit` and others: the policy
   applies only to objects in the chosen states. `NewState` on `state/assign`: which states a user may set.
5. **Setting states** (2 minutes). Node **Details** tab, box **Content state**, a select per group, **Set states**;
   tick **Also for everything below this node** for a subtree. No new version is made.

### Common questions

**Do states replace workflows?** No. States are labels with permissions; workflows ([Module 6](../module-06-cronjobs-workflows-and-notifications/README.md)) run actions on
events and can react to a state change.

**Can I translate state names?** Yes, every group and state has a name and description per language.

**What happens to objects when I remove a state?** They move to the first state that remains; the page says what it
touches before you confirm.

### Pitfalls

- **Hiding the whole site.** Adding `StateGroup_review = Approved` to the Anonymous role before setting existing
  content to Approved hides everything from visitors. Follow the order in the exercise.
- **Identifiers starting with `ez`.** They are reserved; the form refuses them.
- **Large subtrees.** Setting states on a big subtree becomes a content job; on the lab it runs at once.

## Hands-on exercise

On your training installation, signed in as an administrator, with a private window for testing:

1. Open **Setup > States**. Read the card of the group **Lock**.
2. Press **New state group**. **Identifier**: `review`; name `Review`; a one-line description. Press **Create**.
3. On the group's page, press **New state** and create, in this order: `draft` (Draft), `in_review` (In review),
   `approved` (Approved).
4. Back on **Setup > States**, read the chain of the new group and how many objects are in each state.
5. Open the top node of the public site, the **Details** tab, box **Content state**. Choose **Approved** for
   **Review**, tick **Also for everything below this node**, and press **Set states**.
6. Create a new article `Waiting article` in the `News` folder. Check its state on the **Details** tab.
7. Open the role **Anonymous**, **Edit**, and change its `content` / `read` policy for section Standard: add the
   limitation `StateGroup_review` with the value **Approved**. **Save**.
8. Clear the content cache, then in the private window open the `News` folder. Is `Waiting article` listed?
9. As administrator, set `Waiting article` to **Approved** on its **Details** tab. Reload the private window.
10. Open the group **Review** on **Setup > States** and read **Roles that use this group**.
11. Undo step 7: remove the `StateGroup_review` limitation from the Anonymous policy and **Save**.

## Expected result

- The group **Review** has three states, **Draft** marked as default.
- Step 6: the new article is in **Draft**.
- Step 8: the article is hidden from visitors; after step 9 it appears.
- Step 10 lists the role **Anonymous** with its `content/read` policy.
- The trainer checks the group's card and the private window on one learner's site.

## Quiz

1. Which state does a new object get in a group?
   A. The last one B. The first one C. None D. The one chosen by the editor in the edit form, always
2. Why set existing content to Approved before limiting visitors to Approved?
3. What does the limitation `NewState` decide?
4. Can a policy be limited by the group `ez_lock`?
5. Does setting a state create a new version of the object?

<details>
<summary>Answers</summary>

1. **B.** The first state of each group is its default ([Object states](../../../../guides/object-states.md#in-short)).
2. Otherwise all existing content is in the default state Draft and disappears for visitors.
3. Which states a user may set with `state/assign`.
4. No. Groups starting with `ez` are system groups; policies cannot limit by them.
5. No. The object is saved with the new state at once, without a new version.

</details>

## Further reading

- [Object states](../../../../guides/object-states.md): the complete guide with the full review workflow.
- [Content jobs](../../../../features/6.0/content-jobs.md): states on large subtrees.
- [Sections](../../../../guides/sections.md): the other way to group content for permissions.

## Related pages

- [Module 4: Sections, siteaccesses and object states](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
