---
title: "Lesson 4.3: Run, stop, checkpoints and What changed"
audience: trainer, editor
category: training
order: 43
summary: Learners run, stop and queue messages in a thread, read its cost, restore it to a checkpoint with a preview, and use What changed and Revert on a draft written directly. 45 minutes.
keywords: exponential intelligence, vector, run, stop, queue, steer, cost, checkpoints, restore, preview, what changed, revert, revert field, direct review
type: help
---
# Lesson 4.3: Run, stop, checkpoints and What changed

Editors stay in control of a running thread: they can stop it, queue the next message, see what it costs, and turn the
thread back to an earlier point. For drafts written without staged review, **What changed** shows the difference and
takes parts back. This lesson practices the controls.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 4: Vector](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Experienced editors with the `expai/vector` policy |
| Prerequisites | [Lesson 4.2](plans-and-staged-review.md) |

## Learning objectives

After this lesson, learners can:

- **Run**, **Stop** and **Queue** messages, and steer a running turn;
- read the thread's cost: spent, tokens, requests, model, lane;
- restore a thread to a checkpoint after reading the preview;
- open **What changed** on a draft and **Revert** a part or a field.

## Trainer's notes

### How to present it

Sources: the Vector, checkpoints and review pages under **EI: Help** (`extension/expai/doc/vector.md`,
`extension/expai/doc/checkpoints.md`, `extension/expai/doc/review.md`).

1. **Run, Stop, Queue, Steer** (4 minutes). **Run** (Ctrl/Cmd+Enter) sends; **Stop** (the button, or Esc twice)
   stops calling the next step (a call already sent finishes first); **Queue** keeps a message until the turn ends;
   **Steer** (Alt+Enter while running) sends a note the model reads at its next step.
2. **Cost** (2 minutes). The right pane's **Cost**: spent, tokens, requests, model, lane.
3. **Checkpoints** (5 minutes). A checkpoint is written at the start of each turn, before accepted changes are written
   and before an approved plan runs. **Restore** first previews: drafts discarded, drafts set back, what is left alone
   (drafts already published or sent), staged changes set aside, exchanges removed, queued messages dropped. Only the
   owner restores, and only while nothing runs. A restore writes its own checkpoint, so it can be undone (a discarded
   draft does not come back).
4. **What changed** (3 minutes). On a draft written directly (Intelligence tab, or a thread with **Direct** review),
   **What changed** shows the diff against the version it was based on; **Revert** takes a part back, **Revert
   field** a field, into the same draft. Nothing is published.

### Common questions

**Does Stop undo what was done?** No. It stops further steps; use a checkpoint to go back.

**Can I restore after I published a draft?** Published or sent drafts are left alone by a restore.

**Why is Direct review not offered to me?** The role allows only staged review (lesson 1.3).

### Pitfalls

- **Restoring without the preview.** Read what will be discarded.
- **Pressing Stop too late.** A call already sent finishes first; wait for it.
- **Direct mode for many items.** It is limited to one item in one language for a reason.

## Hands-on exercise

Use your thread from lesson 4.2 and your course folder.

1. In your thread, send: `Suggest a better title for each article in this folder. Stage the changes.` Press **Run**.
2. While it runs, type `Keep titles under 60 characters.` and send it with **Steer** (or **Queue** if steering is not
   offered).
3. Press **Stop** once you see the first step finish. Read the thread's state.
4. Open **Cost** in the right pane. Write down the spent amount, tokens and requests.
5. Find the checkpoint strip under the conversation. Press **Restore** on the checkpoint before your last message and
   read the preview. Confirm the restore if the preview only discards what you expect.
6. In the Intelligence tab of one article, ask for a small direct change, for example `Fix the grammar of the intro.`
   Then open **What changed** on the draft it wrote.
7. **Revert** one part, then publish or discard the draft.

## Expected result

- The learner stopped a run, read its cost, and restored the thread to a checkpoint after reading the preview.
- One direct draft was reviewed with **What changed**, partly reverted, and published or discarded.
- The trainer checks by asking two learners what the restore preview listed.

## Quiz

1. What is the difference between **Stop** and restoring a checkpoint?
2. When is a checkpoint written?
3. What does a restore leave alone?
   A. Every draft B. Drafts already published or sent for approval C. The conversation D. Nothing
4. Where do you see what a direct draft changed?
5. True or false: **Revert** publishes the reverted text.

<details>
<summary>Answers</summary>

1. **Stop** stops further steps; a restore takes the thread back to an earlier state.
2. At the start of each turn, before accepted changes are written to drafts, and before an approved plan runs.
3. **B.**
4. With **What changed** on the draft.
5. **False.** It writes into the same draft; nothing is published.

</details>

## Further reading

- The checkpoints, Vector and review pages under **EI: Help**.

## Related pages

- [Module 4: Vector](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
