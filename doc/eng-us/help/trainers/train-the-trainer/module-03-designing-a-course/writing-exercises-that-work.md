---
title: "Lesson 3.4: Writing exercises that work"
audience: trainer
category: training
order: 34
summary: Write hands-on exercises that every learner can finish alone, real menu paths and button names, one action per step, invented data, a visible expected result, and a rehearsal on the same version. 45 minutes.
keywords: exercises, hands-on, lab instructions, steps, expected result, rehearsal, button names, invented data, training material
type: help
---
# Lesson 3.4: Writing exercises that work

An exercise works when a learner can do it without asking the trainer. That needs precise steps, the real names of
buttons and menus, data that is safe to use, and a result the learner can see. This lesson teaches the rules the
exercises in these courses follow.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 3: Designing a course](README.md) |
| Duration | 45 minutes (10 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Trainers who write exercises |
| Prerequisites | [Lesson 3.1](writing-learning-objectives.md); a training installation |

## Learning objectives

After this lesson, learners can:

- write exercise steps with one action each and the real interface names in bold;
- choose invented, harmless data for exercises;
- write an expected result the learner and trainer can check;
- rehearse an exercise on the version the course uses and fix it.

## Trainer's notes

### How to present it

1. Show a bad and a good step (5 minutes):
   - bad: "Create a user and give them rights."
   - good: "Open **Users**, then the group **Editors**. Under **Sub items**, press **Create new** and choose
     **User**."
2. Rules (5 minutes): one action per step; bold for what is on screen; code formatting for what is typed;
   invented data ("Annual Bike Day", `@example.invalid`); an **Expected result** that names what is visible.

### Common questions

**What if the interface changes?** The help changes with the code; so should your exercises. Rehearse before every
course and fix what changed.

**Screenshots?** Useful, but they go out of date and must never show personal data. Text steps with real labels age
better.

### Pitfalls

- **Steps that depend on your own data.** "Open the article from yesterday" fails on a fresh installation.
- **Actions that send mail.** Test accounts at `example.invalid`, never real addresses.
- **Destructive steps without a warning.** Removing, emptying the trash or reinstalling gets a warning callout.

## Hands-on exercise

1. Pick a task from the guide [Object states](../../../../guides/object-states.md) or
   [Sections](../../../../guides/sections.md).
2. Write an exercise of six to ten steps following the rules above.
3. Write the **Expected result** in two or three bullets.
4. Rehearse it on your training installation exactly as written. Mark every step where you had to guess.
5. Fix the marked steps.
6. Give it to a partner who has not seen it; they do it without asking you. Note where they stopped.

## Expected result

- An exercise of six to ten steps with bold interface names and an expected result.
- The partner finished it without help, or the stop points are fixed.

## Quiz

1. How many actions should one step contain?
2. Which formatting do these courses use for button names, and which for typed text?
3. Which e-mail domain do test accounts use?
4. Why rehearse on the exact version of the course?

<details>
<summary>Answers</summary>

1. One.
2. Bold for what is on screen (**Send for publishing**); code formatting for what is typed (`learner01`).
3. `example.invalid`.
4. Interface names and steps change between versions; only a rehearsal shows it.

</details>

## Further reading

- [Writing a tutorial that works](../../../advocates/writing-a-tutorial-that-works.md)
- [Creating and editing content](../../../editing-content.md): a help page written step by step.
- [Live demo safety](../../../advocates/live-demo-safety.md): what must never be on screen.

## Related pages

- [Module 3: Designing a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
