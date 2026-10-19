---
title: "Lesson 4.1: What object states are"
audience: trainer, editor
category: training
order: 41
summary: Learners explain object states and state groups, tell them apart from version states, and read an item's states in the Content state box of the Details tab. 30 minutes.
keywords: editors 201, object states, state group, content state, lock, ez_lock, review, details tab, default state, permissions
type: help
---
# Lesson 4.1: What object states are

Some sites give content stages of their own: *Draft*, *In review*, *Approved*, or *Embargoed* and *Released*. In
Exponential these are **object states**. They change nothing by themselves; roles make them matter. This lesson
explains states and how editors read them.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 4: Object states](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Editors on sites that use object states |
| Prerequisites | [Module 3](../module-03-organizing-content/README.md) |

## Learning objectives

After this lesson, learners can:

- explain what an object state and a state group are;
- tell object states apart from the states of a version (draft, pending, published, archived);
- read an item's states in the **Content state** box of the **Details** tab;
- explain that roles give states their meaning, and name the shipped group (lock).

## Trainer's notes

### How to present it

Source: [Object states](../../../../guides/object-states.md#in-short).

1. **States and groups** (4 minutes). An object state is a label every item carries, one from each **state group**.
   Exponential ships one group, the lock group (*Not locked*, *Locked*). Sites add their own, for example a group
   *review* with *Draft*, *In review* and *Approved*.
2. **Not version states** (3 minutes). A version is a draft, pending, published or archived (Editors 101). An object
   state belongs to the item as a whole and is set by hand (or by a workflow), independently of versions. An article
   can be published and in the state *In review*.
3. **The default** (2 minutes). The first state of a group is its default: every new item gets it.
4. **Roles** (4 minutes). A policy can be limited by a state group, for example "visitors may read only *Approved*
   content" or "authors may edit only *Draft* content". So a state can hide content from visitors or take an editor's
   edit rights away. Show the worked example of the guide as a picture on the board.
5. **Where to read it** (2 minutes). The **Details** tab, box **Content state**: one list per group (**State group**,
   **Available states**).

### Common questions

**Is the state *Draft* the same as a draft version?** No. Only the name is the same on sites that choose it. An
item in the state *Draft* can be published.

**Why can I not change a state?** Your role may not set it, or not to that state. A list you may not change is shown
disabled.

**Why does my site have no state groups?** States are optional; many sites use only the lock group.

### Pitfalls

- **Confusing the two kinds of draft.** Write both on the board side by side.
- **Expecting states to do something by themselves.** They only matter through roles, workflows and templates.
- **The Editor siteaccess.** It does not offer the state pages; states are set in the administration.

## Hands-on exercise

Use the training installation (the administration, not the Editor). The trainer created a state group (for example
*Review*) before the class.

1. Open your article *Our team, two places* and its **Details** tab.
2. Find the box **Content state**. Write down each **State group** and the state the article is in.
3. Open the list of one group without changing it, and write down its **Available states**.
4. Create a new article in your course folder and publish it. Read its states: it has the default (first) state of
   each group.
5. Compare with an image of the media library: which states does it have?
6. Write down, in your own words, what the trainer's group is for on this training site (the trainer explains which
   roles use it).

## Expected result

- The learner's notes list the lock group and the trainer's group with the article's current states.
- The new article has the first state of each group.
- The learner can say which role limits by the trainer's group and what that means for visitors or editors.
- The trainer checks by asking two learners to explain the difference between a draft version and the state
  *Draft*.

## Quiz

1. What is a state group?
2. True or false: an object state is part of a version.
3. Which state does a new item get in a group?
   A. The last one B. None C. The first one, the group's default D. The one chosen by the visitor
4. What gives a state its effect on the site?
5. Where do you read an item's states?

<details>
<summary>Answers</summary>

1. A set of states; every item carries one state from each group
   ([Object states](../../../../guides/object-states.md#in-short)).
2. **False.** It belongs to the item as a whole.
3. **C.**
4. Roles (policies limited by the state group), and also workflows and templates that ask for it.
5. On the **Details** tab, box **Content state**.

</details>

## Further reading

- [Object states](../../../../guides/object-states.md): the full guide, with a worked review example.
- [Roles and policies](../../../../guides/roles-and-policies.md): state limitations, for administrators.

## Related pages

- [Module 4: Object states](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
