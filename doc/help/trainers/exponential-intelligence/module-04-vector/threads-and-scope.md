---
title: "Lesson 4.1: Vector threads and their scope"
audience: trainer, editor
category: training
order: 41
summary: Learners open Vector, start a thread, set what it may touch with the scope (nodes, subtrees, classes, languages), choose mode, review, model and lane, and find threads on the Active, Settled and Archived shelves. 45 minutes.
keywords: exponential intelligence, vector, thread, new thread, scope, subtree, classes, languages, mode, plan, write, review, staged, direct, model, lane, shelves, settled, archived
type: help
---
# Lesson 4.1: Vector threads and their scope

**Vector** is the controlled way to command changes with the assistant: a workspace of **threads** where the editor
first decides what a thread may touch, then how it works. This lesson sets up a thread on the learner's own folder.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 4: Vector](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Experienced editors with the `expai/vector` policy |
| Prerequisites | [Module 2](../module-02-the-intelligence-tab/README.md) |

## Learning objectives

After this lesson, learners can:

- open **EI: Vector** and start a thread with **+ New thread** (or the key `N`);
- set the scope with **Change**: nodes, subtrees, classes and languages;
- choose the mode (**Plan** or **Write**), the review (**Staged** or **Direct**), the model and the lane;
- explain why bulk work always starts with a plan and is always staged;
- find threads with **Filter threads** and on the **Active**, **Settled** and **Archived** shelves.

## Trainer's notes

### How to present it

Source: the Vector page under **EI: Help** (`extension/expai/doc/vector.md`).

1. **The page** (3 minutes). Dashboard tab > **EI: Vector** (with `expai/write` and `expai/vector`). On an empty page,
   **Start a thread**: *Choose what it may touch (nodes, subtrees, classes, languages), then say what to do. Bulk work
   starts with a plan.* Three panes: threads on the left, the thread in the middle, **Plan, changes and cost** on the
   right (a drawer on narrower screens). **Search threads, commands, nodes...** (Ctrl+K) is the command palette.
2. **Scope** (4 minutes). **Change** (Alt+S) opens the scope dialog: search nodes by name or number (only what you may
   read), make a node a **subtree**, tick **classes** and **languages**. The role's limitations apply (lesson 1.3).
3. **Mode and review** (4 minutes):

   | | Plan | Write |
   |---|---|---|
   | Staged | reads and proposes a plan; writes nothing | proposes changes that you accept or reject per field and part, then writes drafts |
   | Direct | (as plan) | writes drafts at once (never published); one item in one language only, where the role allows |

   Bulk work (more than one item or language, or any subtree or class in the scope) starts in **Plan** and is always
   **Staged**.
4. **Model and lane** (2 minutes). Only models you may use, grouped by provider; "reads only" for models that may not
   write. The lane matters for background runs (lesson 7.2).
5. **Shelves** (2 minutes). The thread list has sections such as *Working*, *Needs you*, *Pinned* and *Recent*, and
   the shelves **Active**, **Settled**, **Archived**. Threads are private to their owner.

### Common questions

**What is the difference between Vector and the Intelligence tab?** The tab is a conversation on one item with direct
drafts for small edits; Vector sets a scope, plans bulk work, stages changes for review and shows the cost.

**Can I see a colleague's thread?** No; threads are private. Approvers of a version a thread wrote may read it.

**Why can I not choose Direct?** Your role allows only staged review, or the scope is bulk work.

### Pitfalls

- **Scopes that are too wide.** A whole site as subtree invites a huge plan. Start with one folder.
- **Forgetting the language.** A translation thread needs the target language in the scope.
- **Narrow screens.** The right pane becomes a drawer below about 1040 pixels; maximize the window.

## Hands-on exercise

Use the training installation, a role with `expai/vector`, and your course folder.

1. Open **EI: Vector** in the Dashboard tab's left column. Read the **Start a thread** text.
2. Press **+ New thread**.
3. Press **Change** to set the scope. Search for your course folder and add it as a **subtree**. Tick the class
   *Article* and your site's main language.
4. Set the mode to **Plan** and the review to **Staged**.
5. Keep the default model; read the list of models and note which say "reads only".
6. Give the thread a title such as `Course folder tidy-up` (if the title is editable) and leave it without a message.
7. Open the command palette with Ctrl+K (Cmd+K on a Mac), type `thread` and close it with Esc.
8. Use **Filter threads** to find your thread, and look at the **Settled** and **Archived** shelves.

## Expected result

- Each learner has a thread whose scope is their course folder (subtree), *Article* and one language, in **Plan** mode
  with **Staged** review.
- No request was sent yet.
- The trainer checks by opening one learner's thread header on the projector.

## Quiz

1. Which four kinds of entries can a scope have?
2. What makes work "bulk work"?
3. In which mode and review does bulk work always start?
   A. Write, Direct B. Plan, Staged C. Write, Staged D. Plan, Direct
4. Who can see your threads?
5. What does "reads only" next to a model mean?

<details>
<summary>Answers</summary>

1. Nodes, subtrees, classes and languages.
2. More than one item or more than one language, or any subtree or class in the scope.
3. **B.**
4. Only you; approvers of a version a thread wrote may read that thread.
5. The model may answer but may not write drafts.

</details>

## Further reading

- The Vector, threads and permissions pages under **EI: Help** (`extension/expai/doc/vector.md`,
  `extension/expai/doc/threads.md`, `extension/expai/doc/permissions.md`).

## Related pages

- [Module 4: Vector](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
