---
title: "Lesson 4.6: Helping learners help themselves"
audience: trainer, editor
category: training
order: 46
summary: Teach learners the habits that keep them independent after the course, the "?" context help, the Help center search, keyboard shortcuts, the error reference, and how to ask the community a good question. 35 minutes.
keywords: self-help, context help, help menu, keyboard shortcuts, search, error reference, asking questions, independence, after the course
type: help
---
# Lesson 4.6: Helping learners help themselves

A course ends; the questions do not. The most valuable thing a trainer leaves behind is a set of habits: press "?",
search the help, read the error reference, ask a good question. This lesson builds those habits into every course.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 4: Delivering a course](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Beginner |
| Audience | Trainers; the exercise works for any learner |
| Prerequisites | [Lesson 1.2](../module-01-preparing-to-teach/the-help-center-as-your-textbook.md) |

## Learning objectives

After this lesson, learners can:

- answer a learner's question by showing where the answer is instead of giving it;
- use the "?" menu's **Help for this page** on any admin page;
- use the keys `/` and `Esc` in the administration;
- write a question for a forum with version, steps and the exact message, without private data.

## Trainer's notes

### How to present it

1. The "show, don't tell" rule (3 minutes): when a learner asks "how do I ...?", ask "what does the "?" show on this
   page?" first.
2. Show the keys (3 minutes): `/` jumps to the search field in the header; `Esc` closes menus; the first `Tab` shows
   **Skip to main content** ([Keyboard shortcuts](../../../keyboard-shortcuts.md)).
3. A good question (4 minutes): version (`php bin/php/console --version`), what you did, what you expected, the exact
   message, matching log lines, no passwords or personal data
   ([Troubleshooting, 12.14](../../../../install/12-troubleshooting.md#1214-getting-help)).

### Common questions

**Is this slower than answering?** At first. By the afternoon learners answer half their own questions.

**What if the help has no answer?** That is a gap to report (lesson 3.5).

### Pitfalls

- **Answering every question yourself.** Learners then depend on you.
- **Forum posts with logs full of data.** Teach masking before posting.

## Hands-on exercise

1. On three different admin pages (for example **Content structure**, **Users**, **Setup**), open the "?" menu and
   choose **Help for this page**. Note the title of each help page that opens.
2. On any admin page, press `/` and type a word; press `Esc`.
3. Press `Tab` once on a fresh page and note what appears.
4. Write a forum question about an invented problem ("the article I published does not show on the front page"):
   version, steps, expected, actual, what you tried. No personal data.
5. Exchange questions with a partner; each checks the other's for missing facts or private data.

## Expected result

- Three help page titles that match their admin pages.
- The learner used `/`, `Esc` and the skip link.
- A forum question with version, steps, expected and actual result, and no private data.

## Quiz

1. What does the first `Tab` on an admin page show?
2. Which key jumps to the search field in the header?
3. Name four things a good bug question contains.
4. A learner asks how to clear caches. What do you do first?

<details>
<summary>Answers</summary>

1. **Skip to main content** ([Keyboard shortcuts](../../../keyboard-shortcuts.md)).
2. `/`.
3. The version, the steps, what was expected, the exact message (and log lines without private data).
4. Ask them what the "?" or the Help center search shows for "clear cache", and let them find it.

</details>

## Further reading

- [Keyboard shortcuts](../../../keyboard-shortcuts.md)
- [When something goes wrong](../../../when-something-goes-wrong.md)
- [Answering questions well](../../../advocates/answering-questions-well.md)

## Related pages

- [Module 4: Delivering a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
