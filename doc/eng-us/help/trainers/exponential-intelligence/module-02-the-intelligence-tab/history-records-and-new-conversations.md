---
title: "Lesson 2.3: History, records and new conversations"
audience: trainer, editor
category: training
order: 23
summary: Learners use the conversation history, open the record of a turn, follow a run with Watch this run, continue after a failed step, and start a new conversation. 30 minutes.
keywords: exponential intelligence, history, expand all, collapse all, show older, record, watch this run, continue, new conversation, conversation lifetime, log
type: help
---
# Lesson 2.3: History, records and new conversations

A conversation keeps its history on the server, and every turn has a record: which model answered, what it looked up,
what it cost. This lesson shows learners how to read both, and when to start afresh.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 2: The Intelligence tab](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Beginner |
| Audience | Editors |
| Prerequisites | [Lesson 2.2](keeping-sending-and-discarding-drafts.md) |

## Learning objectives

After this lesson, learners can:

- open and close exchanges of the history, with **Expand all**, **Collapse all** and **Show older**;
- open the record of a turn: model, steps, tokens, cost;
- follow a running request with **Watch this run**;
- use **Continue** after a failed step and **New conversation** to start afresh.

## Trainer's notes

### How to present it

Source: the Intelligence tab page under **EI: Help** (`extension/expai/doc/full-view-panel.md`).

1. **The history** (3 minutes). One row per exchange: the time, your message and the start of the answer, the number
   of lookups and the drafts as links. Click a row to open it (your full message, every step, the full answer).
   **Expand all**, **Collapse all**, **Show older** (the previous 20). The last 200 exchanges are kept.
2. **The record** (3 minutes). Under each exchange, a collapsed line records the turn: the model that answered (and
   the one asked for, if a fallback replaced it, with the reason), the steps with duration, tools, tokens and cost,
   each linked to its request in the log.
3. **Watch this run** (2 minutes). As soon as a message is sent, a line shows its live state (queued, running step
   *k*, done, failed) with links to the log.
4. **Continue and New conversation** (2 minutes). **Continue** appears when a step failed (for example a timeout) and
   resumes from what was stored. **New conversation** removes this editor's conversation on this item; drafts and the
   log stay. A conversation without a message for a set time (seven days with the shipped settings) starts anew.

### Common questions

**Is my history visible to others?** A conversation belongs to one user and one item.

**When should I start a new conversation?** When the topic changes, or the conversation grew long: a fresh one is
cheaper and more focused.

**Why did another model answer?** The chosen one could not (for example it was stopped or failed), and the site's
default answered; the record says why.

**Does opening history rows send anything?** No. Opening and closing rows changes nothing on the server; the browser
only remembers which rows you opened.

### Pitfalls

- **Endless conversations.** Long histories make each request larger and costlier. Start new ones.
- **Reading cost per turn as exact.** Costs are estimates.

## Hands-on exercise

Use the training installation and your course folder's **Intelligence** tab.

1. Send a short follow-up request, for example `Suggest three titles for a third article on the same topic. Do not write
   it.`
2. In the history, press **Collapse all**, then open your first exchange by clicking its row.
3. Open the record line under the newest exchange. Write down the model, the number of steps, the tokens and the
   estimated cost.
4. Follow one of the record's links to the log and come back.
5. Press **New conversation**. Note that the drafts and your log entries are still there (check **Recent drafts** on
   the dashboard page).
6. If a step fails during the course (the trainer may show one), use **Continue**.

## Expected result

- The learner's notes have the model, steps, tokens and cost of a turn.
- After **New conversation** the history is empty, but drafts and log entries remain.
- The trainer checks by asking one learner to show a record on the projector.

## Quiz

1. How many exchanges does the history keep?
2. What does the record line of a turn show?
3. What does **New conversation** remove?
   A. The drafts B. The log C. This editor's conversation on this item D. The item
4. When does **Continue** appear?
5. Give one reason to start a new conversation.

<details>
<summary>Answers</summary>

1. The last 200.
2. The model that answered (and any fallback), the steps with duration, tools, tokens and cost, linked to the log.
3. **C.** Drafts and the log stay.
4. When a step failed, for example after a timeout.
5. A new topic, or a long conversation that makes requests larger and costlier.

</details>

## Further reading

- The Intelligence tab, threads and usage pages under **EI: Help** (`extension/expai/doc/full-view-panel.md`,
  `extension/expai/doc/usage-tracking.md`).

## Related pages

- [Module 2: The Intelligence tab](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
