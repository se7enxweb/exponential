---
title: "Lesson 4.2: Setting states in a review flow"
audience: trainer, editor
category: training
order: 42
summary: Learners set an item's state with Set states, set states on a whole subtree, and play author and reviewer in a review flow built on object states. 45 minutes.
keywords: editors 201, object states, set states, also for everything below this node, review, approve, author, reviewer, state assign, content job
type: help
---
# Lesson 4.2: Setting states in a review flow

States are set by hand on the **Details** tab, for one item or a whole subtree. On a site that uses states for
reviews, setting a state is how an author hands a text over and a reviewer approves it. This lesson practices both
roles.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 4: Object states](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors on sites that use object states |
| Prerequisites | [Lesson 4.1](what-object-states-are.md); the trainer's review group and roles |

## Learning objectives

After this lesson, learners can:

- set an item's state with the **Content state** box and **Set states**;
- set a state on a whole subtree with **Also for everything below this node**;
- explain that setting a state makes no new version;
- describe a review flow with states from the author's and the reviewer's side.

## Trainer's notes

### How to present it

Sources: [Setting states on content](../../../../guides/object-states.md#7-setting-states-on-content) and
[A worked example: a review workflow](../../../../guides/object-states.md#9-a-worked-example-a-review-workflow).

1. **One item** (3 minutes). **Details** tab, box **Content state**: choose the state in the group's list and press
   **Set states**. The item is saved with the new state at once; no new version is made. A group in which you may set
   only the current state shows a disabled list.
2. **A subtree** (3 minutes). Tick **Also for everything below this node** before **Set states**: the state is set on
   the item and everything below it, skipping items you may not give the state. A large subtree becomes a background
   content job after a confirmation (lesson 3.4).
3. **The review flow** (6 minutes). Draw the table of the guide's example:

   | Who | Does | The article's state |
   |---|---|---|
   | Author | Creates and publishes the article | Draft (the default); invisible to visitors |
   | Author | Content state: In review, **Set states** | In review; the author can no longer edit it |
   | Reviewer | Reads it, sends it back | Draft; the author edits again |
   | Reviewer | Approves | Approved; visitors see it |

   The roles of your training installation decide what each person may do. Approval with object states differs from
   the approval workflow of module 5: here the content is published and only hidden by state; there the version waits
   unpublished.

### Common questions

**Which flow should our site use, states or the approval workflow?** That is a decision for the site's administrators;
both are documented. States suit "published but not yet visible"; the approval workflow suits "not published until
approved".

**Can I undo a state change?** Set the previous state again; there is no version to go back to.

**Why did the subtree change skip some items?** You may not give the state to them.

### Pitfalls

- **Roles not prepared.** The flow only works with the roles of the guide's example. Rehearse with two accounts.
- **Subtree changes on shared content.** Use only learners' folders.
- **Expecting a version.** Learners look in **Manage versions** for the state change; it is not there.

## Hands-on exercise

Use the training installation (the administration). Work in pairs: one learner is the author, the other the
reviewer; the trainer gave the reviewer accounts a reviewer role.

1. Author: create an article `Review me` in your course folder and publish it.
2. Author: open a private browser window and look for the article on the public site (it should be hidden if the
   visitors' role reads only approved content).
3. Author: on the **Details** tab, in **Content state**, choose the state for review (for example *In review*) and
   press **Set states**.
4. Author: press **Edit** on the article and note whether your role still allows it.
5. Reviewer: open `Review me` (the author tells you where), read it, and set the state back to the first state with
   **Set states** to send it back.
6. Author: edit and publish a correction, then set the review state again.
7. Reviewer: set the approved state.
8. Author: check the public site in the private window again.
9. Both: on your course folder's **Details** tab, tick **Also for everything below this node**, choose the first
   state and press **Set states**. Read the confirmation if one appears.

## Expected result

- `Review me` went through the states and is visible on the site only after the approved state was set (on a site
  whose visitors' role reads only approved content).
- **Manage versions** of the article shows only the versions made by editing, not the state changes.
- Step 9 set the state on the whole course folder.
- The trainer checks by opening `Review me`'s **Details** tab on the projector.

![The object states of an item on its Details tab, with Set states](../../../images/trainers/e201-states.webp)

## Quiz

1. Which button sets the state chosen in the **Content state** box?
2. True or false: setting a state makes a new version.
3. How do you set a state on a folder and everything below it?
4. In the review example, why can the author no longer edit the article after setting *In review*?
5. What is the difference between approval with object states and the approval workflow?

<details>
<summary>Answers</summary>

1. **Set states** ([Object states](../../../../guides/object-states.md#71-one-object-in-the-browser)).
2. **False.**
3. Tick **Also for everything below this node** before **Set states**.
4. The author's edit policy is limited to the state *Draft*.
5. With states the version is published and hidden by its state; with the approval workflow the version waits
   unpublished until approved.

</details>

## Further reading

- [Object states](../../../../guides/object-states.md#7-setting-states-on-content): setting states.
- [Object states](../../../../guides/object-states.md#9-a-worked-example-a-review-workflow): the review example with
  the roles.
- [Workflows](../../../../guides/workflows.md): the approval workflow, for comparison.

## Related pages

- [Module 4: Object states](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
