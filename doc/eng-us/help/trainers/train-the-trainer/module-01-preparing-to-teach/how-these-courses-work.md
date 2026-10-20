---
title: "Lesson 1.1: How these courses work"
audience: trainer
category: training
order: 11
summary: How the trainer courses are built (courses, modules, lessons), what every lesson page contains, and the one rule behind all of them, every fact from the documentation, the code or a real installation. 30 minutes.
keywords: train the trainer, course structure, modules, lessons, lesson page, learning objectives, trainer notes, exercise, quiz, sources, facts
type: help
---
# Lesson 1.1: How these courses work

Before you teach any of the Exponential courses in this help, you need to know how they are put together, so that
you can take them apart again: run a whole course, pick single lessons for a short session, or mix lessons from
several courses into your own programme. This first lesson of the train-the-trainer course explains the building
blocks and the rule that keeps them trustworthy.

> **Note**
> This lesson is about the material, not about Exponential itself. You can present it in a meeting room without
> any computer; the exercise needs one machine with a browser that can open the administration of an Exponential
> installation.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 1: Preparing to teach Exponential](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Beginner |
| Audience | People who will run Exponential courses: trainers, team leads, consultants, teachers |
| Prerequisites | An account that can open the Help center (any administrator; on a new installation also the Editor role) |

## Learning objectives

After this lesson, learners can:

- name the three levels of the trainer material (course, module, lesson) and what each one is for;
- list the sections every lesson page has and say which ones are for the trainer and which for the learners;
- explain where the facts in a lesson come from and what to do when a fact cannot be verified;
- find a course's overview page, a module page and a lesson page in the help.

## Trainer's notes

### How to present it

1. Start with the problem these pages solve (3 minutes). A course about a content management system goes out of date
   when the software changes. The courses here live in the same repository as Exponential's code and the rest of the
   documentation (`doc/en/us/help/trainers/`), so they change with it and can be corrected by anyone with **Edit on
   GitHub**.
2. Draw the structure on a board (5 minutes):

   ```text
   Course  (a README "course overview" with the reading path and schedules)
     Module  (a README "module overview" with the lesson list and the timing plan)
       Lesson  (one page: objectives, notes, exercise, expected result, quiz, reading)
   ```

3. Open one lesson on the projector and walk down its sections (5 minutes). Point out that the **Trainer's notes**
   are for you and the **Hands-on exercise** and **Quiz** are for the learners.
4. Explain the source rule (2 minutes): every statement comes from the documentation in `doc/`, the code, or the
   real interface of an installation. When a course and the software disagree, the software wins, and the course
   page gets fixed.

### Common questions

**May I change the lessons for my own course?** Yes. The pages are free documentation under the GNU Free
Documentation License 1.3; you may adapt them, as long as your version keeps the license and says that it was
changed. Lesson 1.4 covers the details.

**Is there an official Exponential certification?** The Exponential Partner Program names certifications for
partner teams, but their syllabus, exams and examiners are not published
([Certifications](../../../partners/certifications.md)). The assessments in these courses are your own material as
a trainer; never present them as an official certification.

**Why do the lessons have exact button names?** So that learners can follow them alone. If your installation shows
a different label (an older version, a different administration design), note the difference on your handout.

### Pitfalls

- **Teaching from memory.** The administration changed a lot in Exponential 6.0 (the Admin 4 design, the Help
  center, the layouts editor). Rehearse every exercise on the version you will teach.
- **Mixing designs.** The lessons describe the Admin 4 design unless they say otherwise. If your learners use the
  classic admin, check the exercise there first.
- **Over-long sessions.** A lesson's duration includes the exercise. Do not add three "quick extra topics".

## Hands-on exercise

Use any installation where you can sign in to the administration.

1. Sign in to the administration (for example `https://<your-site>/admin`).
2. Click the **Help menu** button (the "?" in the header), then **Open the Help center**.
3. In the field **Search the help**, type `trainers` and press **Search**.
4. Open the page **Trainers: start here** and find the link to the **Course catalogue**.
5. In the catalogue, open the course overview of **Train the trainer**.
6. From the overview, open **Module 1: Preparing to teach Exponential**, then this lesson.
7. On this lesson page, find the sections **At a glance**, **Trainer's notes**, **Hands-on exercise**,
   **Expected result**, **Quiz** and **Further reading** in the page's table of contents.
8. Write down, for this lesson: its duration, its prerequisites and its first further-reading link.

## Expected result

- The learner reached the lesson from the Help center in three or four clicks, through the start page, the
  catalogue, the course overview and the module overview.
- Their notes say: 30 minutes; an account that can open the Help center; the help system feature page.
- The trainer checks the notes and asks one learner to show the path on the projector.

## Quiz

1. Which page lists the lessons of a module together with a timing plan?
   A. The course overview B. The module overview C. The lesson page D. The course catalogue
2. Which section of a lesson page is written for the trainer rather than for the learners?
3. A lesson says to press a button that your installation does not show. What do you do?
4. True or false: the assessments in these courses are an official Exponential certification.
5. Under which license may you adapt these pages for your own course?

<details>
<summary>Answers</summary>

1. **B.** The module overview (`README.md` of the module folder) has the lesson table and the timing plan.
2. **Trainer's notes**: how to present it, common questions and pitfalls.
3. Check the version and the administration design of your installation, note the difference on your handout, and
   report or fix the page with **Edit on GitHub** if the lesson is wrong for the current version.
4. **False.** The assessments are the trainer's own material; the partner program's certifications are a separate
   matter whose details are not published ([Certifications](../../../partners/certifications.md)).
5. The GNU Free Documentation License 1.3 or any later version
   ([Free documentation](../../../free-documentation.md)).

</details>

## Further reading

- [The help system](../../../../features/6.0/help-system.md): what the Help center shows and how it finds pages.
- [Free documentation](../../../free-documentation.md): what the license lets you do with these pages.
- [Hands-on workshop outline](../../../advocates/hands-on-workshop-outline.md): the advocates' short workshop formats,
  a good comparison for your own courses.

## Related pages

- [Module 1: Preparing to teach Exponential](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
