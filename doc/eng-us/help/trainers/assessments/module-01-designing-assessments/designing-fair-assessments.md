---
title: "Lesson 1.1: Designing fair assessments"
audience: trainer
category: training
order: 11
summary: Plan an end-of-course assessment that is fair, aligned with the course objectives, honest about its status as the trainer's own material and run on a training installation with the same starting state for every learner. 45 minutes.
keywords: assessments and practical exercises, fair assessment, alignment, learning objectives, validity, reliability, pass mark, trainer's own assessment, no official certification
type: help
---
# Lesson 1.1: Designing fair assessments

An assessment tells a learner, and often an employer, what the learner can now do. It is only worth something when
it is fair: it checks what the course taught, every learner gets the same chance, and nobody is misled about what it
is. This lesson gives the plan that every assessment in this course follows.

> **Note**
> Every test, task and rubric in this course is the trainer's own assessment, not an official certification; the
> partner program's certifications are separate and their details are not published
> ([Certifications](../../../partners/certifications.md)). Present your assessment that way, and never as preparation
> for, or a replacement of, any official program.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 1: Designing assessments](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers who assess learners at the end of a course |
| Prerequisites | [Quizzes and practical tasks](../../train-the-trainer/module-06-assessment-and-follow-up/quizzes-and-practical-tasks.md) in Train the trainer |

## Learning objectives

After this lesson, learners can:

- derive the content of an assessment from the objectives of the course it closes;
- name the three parts used in this course: a written test, a practical task and, where it fits, a capstone project;
- set a pass mark and the rules of the assessment before the course starts;
- write the first line of an assessment sheet that states who issues it;
- check that every learner starts the practical part from the same installation state.

## Trainer's notes

### How to present it

1. **Why assess at all** (3 minutes). Not every course needs an assessment. Assess when someone needs evidence of a
   skill: an employer deciding who may publish, a team lead deciding who runs the backups. A course that ends with a
   good exercise and a short quiz is often enough.
2. **Alignment** (5 minutes). Every question and every task checks one learning objective of the course. Lay the
   course's objectives next to the test on the projector and draw a line from each question to its objective. A
   question without a line is cut; an objective without a question is a gap.
3. **The three parts** (4 minutes).
   - A **written test** checks knowledge: what a draft is, which cache holds what, why a cronjob must run. Each answer
     in the key cites the help page that proves it ([Lesson 1.2](writing-written-tests.md)).
   - A **practical task** checks skill on a training installation, with a result the trainer can check
     ([Lesson 1.3](building-scoring-rubrics.md) gives the rubric).
   - A **capstone project** joins several skills in a realistic job over a day or more (Module 6).
4. **Rules decided before the course** (4 minutes). Pass mark, time allowed, which help pages may be open, whether
   learners may ask each other, how a retake works. Tell learners on the first day, not on the last.
5. **Same start for everyone** (4 minutes). A practical task depends on the installation. Every learner starts from
   the same state, built with the same command and the same site package
   ([One installation per learner](../../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md),
   [Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).

### Common questions

**Is an open-book test fair?** Yes, and for this product it is closer to real work: editors and administrators look
things up in the help every day. Write questions that need understanding, not page hunting, and set a time limit.

**How hard should it be?** As hard as the objectives say, no harder. If the course objective is "publish an article",
the task is to publish an article, not to configure an approval workflow.

**What pass mark?** Choose it from what the result is used for. A common choice is a share of the points plus "no
criterion at the lowest level" in the rubric, so that one missing skill is not hidden by other strengths. Whatever
you choose, write it down before the course.

**Can I call the result a certificate?** You can issue a certificate of completion in your own name
([Lesson 1.7](completion-certificates-you-issue.md)). You cannot call it an official Exponential certification.

### Pitfalls

- **Testing what was not taught.** If a topic was skipped for time, take its questions out.
- **Trick questions.** They measure attention to wording, not skill.
- **Installation-dependent answers.** A question about "the third tab" fails on another design. Name the design
  (these pages describe admin4) and check the labels on your training installation.
- **Real data.** Assessments run on a training installation with test accounts at `example.invalid`, never on a
  live site and never with real personal data.

## Hands-on exercise

Plan the assessment for a course you teach. Use any course of the help, for example
[Editors 101](../../editors-101/README.md).

1. Copy the learning objectives of every lesson of the course into one list.
2. Mark each objective **K** (knowledge, suits a written question) or **S** (skill, suits a practical task).
3. Choose the objectives to assess: every module needs at least one, and the most important skills need a task.
4. Write the rules: time for the written test, time for the practical task, open help or not, pass mark, retake.
5. Write the starting state of the practical task: which install command, which site package, which accounts.
6. Write the first line of the assessment sheet: "Assessment by `<your name or organization>` for the course
   `<course>`. This is the trainer's own assessment, not an official certification."
7. Exchange your plan with a neighbor. Each checks the other's plan for one gap (an objective without a question) and
   one surplus (a question without an objective).

## Expected result

- A one-page plan: objectives marked K or S, the chosen ones, the rules, the starting state and the issuer line.
- The neighbor found at most one gap and one surplus, and the plan was corrected.
- The trainer checks two plans on the projector and asks where the pass mark came from.

## Quiz

1. What must every question of an assessment be linked to?
2. Which part of the assessment suits the objective "learners can clear a single cache"? A. written test B. practical
   task C. neither D. capstone only
3. When do learners hear the pass mark?
4. True or false: an assessment from this course may be called "Exponential certified".
5. Why does every learner need the same starting installation for a practical task?

<details>
<summary>Answers</summary>

1. One learning objective of the course
   ([Quizzes and practical tasks](../../train-the-trainer/module-06-assessment-and-follow-up/quizzes-and-practical-tasks.md)).
2. **B.** It is a skill with a checkable result; a written question can add the "why"
   ([Caches](../../../../guides/caches.md)).
3. At the start of the course, together with the other rules.
4. **False.** It is the trainer's own assessment, not an official certification; the partner program's
   certifications are separate and their details are not published
   ([Certifications](../../../partners/certifications.md),
   [Claims you must not make](../../../advocates/claims-you-must-not-make.md)).
5. So that the result depends on the learner's skill, not on differences between installations
   ([Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).

</details>

## Further reading

- [Writing learning objectives](../../train-the-trainer/module-03-designing-a-course/writing-learning-objectives.md):
  objectives that can be assessed.
- [Writing exercises that work](../../train-the-trainer/module-03-designing-a-course/writing-exercises-that-work.md):
  the same rules make practical tasks work.
- [Certifications](../../../partners/certifications.md): what the partner program publishes, and what it does not.

## Related pages

- [Module 1: Designing assessments](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
