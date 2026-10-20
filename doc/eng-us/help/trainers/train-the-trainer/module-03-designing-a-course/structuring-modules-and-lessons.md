---
title: "Lesson 3.2: Structuring modules and lessons"
audience: trainer
category: training
order: 32
summary: Turn a list of topics into a course, group lessons into modules of one to three hours, order them so each builds on the last, and write module overviews with a timing plan. 45 minutes.
keywords: course structure, modules, lessons, sequencing, prerequisites, module overview, timing plan, course design
type: help
---
# Lesson 3.2: Structuring modules and lessons

A good course feels like a staircase: each step is small and rests on the one before. The courses in this help are
built as courses, modules and lessons for that reason. This lesson shows trainers how to build that staircase for a
course of their own, using the existing help as the material.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 3: Designing a course](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Trainers who build their own course |
| Prerequisites | [Lesson 3.1](writing-learning-objectives.md) |

## Learning objectives

After this lesson, learners can:

- group topics into modules of one to three hours with one clear outcome each;
- order lessons by dependency (what must a learner be able to do first?);
- write a module overview with a lesson table and a timing plan;
- list the prerequisites of a course.

## Trainer's notes

### How to present it

1. Show the module overview of [Module 2](../module-02-building-the-lab/README.md) (5 minutes): **At a glance**,
   **Lessons**, **Preparing this module**, **Timing plan**.
2. Explain dependency ordering with an editor example (5 minutes): you cannot publish before you can create; you
   cannot translate before you can publish a first version; you cannot assign a role before the user exists.
3. Explain module size (5 minutes): one to three hours, one outcome, a natural break at the end.

### Common questions

**Should every course start with installation?** Only courses whose learners will install. Editors start signed in.

**Can modules be optional?** Yes. Mark them as optional in the course overview and make sure no later module depends
on them.

### Pitfalls

- **Alphabetical order.** The menu order of the administration is not a learning order.
- **The "everything" module.** A module that covers roles, caches and cronjobs has no single outcome. Split it.
- **Hidden prerequisites.** If lesson 4 needs a user group created in lesson 2, say so in lesson 4's
  **Prerequisites**.

## Hands-on exercise

Design a one-day course "Running our intranet" for administrators.

1. Open the help guides list ([Guides](../../../../guides/README.md)) and pick eight topics an intranet administrator
   needs (for example roles and policies, sections, caches, cronjobs, two-factor login, upgrading, system
   information, maintenance mode).
2. Write each topic on a card (paper or a text file).
3. Group the cards into three or four modules, each with one outcome sentence.
4. Order the modules and the lessons inside them by dependency. For each lesson, write what it needs first.
5. Estimate durations (exercise included) and check the day fits into six hours of lessons.
6. Write one module overview with the sections of a module README: at a glance, lessons, preparing, timing plan.

## Expected result

- A course of three or four modules, each with an outcome sentence and ordered lessons.
- A module overview with a timing plan that adds up.
- No lesson depends on something taught later.

## Quiz

1. How long should a module be?
2. Which four sections does a module overview in these courses have?
3. Name one sign that a module should be split.
4. Where do you write that a lesson needs something from an earlier lesson?

<details>
<summary>Answers</summary>

1. One to three hours, with one outcome.
2. At a glance, Lessons, Preparing this module, Timing plan.
3. It has more than one outcome, or it covers unrelated areas.
4. In the lesson's **Prerequisites** row.

</details>

## Further reading

- [Guides](../../../../guides/README.md): the guide index, a good source of course topics.
- [Writing learning objectives](writing-learning-objectives.md)
- [Hands-on workshop outline](../../../advocates/hands-on-workshop-outline.md): two finished timetables.

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
