---
title: Building an in-house training team
audience: government, trainer, project-manager
category: public-sector
order: 70
summary: How a public body trains its own Exponential trainers with the Train the trainer course, builds and keeps a training lab, adapts the free course material and keeps it current, so training continues after the supplier's contract ends.
keywords: in-house trainer, train the trainer, training team, lab, course material, GFDL, knowledge transfer, sustainability, public sector
type: help
---
# Building an in-house training team

This page is for the training coordinator of a public body that wants to keep Exponential knowledge inside the
organization. An in-house trainer can onboard new staff at any time and does not leave when a contract ends. The
courses and their content come from [Train the trainer](../../../trainers/train-the-trainer/README.md) and
[Trainers: start here](../../../trainers/trainers-start-here.md).

## Contents

1. [When it is worth it](#when-it-is-worth-it)
2. [Choosing the trainers](#choosing-the-trainers)
3. [The training path for trainers](#the-training-path-for-trainers)
4. [The lab](#the-lab)
5. [Adapting and maintaining the material](#adapting-and-maintaining-the-material)
6. [Steps](#steps)

## When it is worth it

| Situation | In-house trainers |
|---|---|
| Many editors spread over departments | worth it: onboarding is continuous |
| Regular staff turnover | worth it |
| A small team of a few editors | a mentor may be enough |
| Several suppliers over the years | worth it: knowledge does not move with the supplier |

## Choosing the trainers

Pick people who already use the system every week: a senior editor for editorial courses, a site administrator for
the administration course. They need browser and terminal skills for Train the trainer
([Course catalogue](../../../trainers/course-catalogue.md)), and time: trainers prepare and rehearse, not only teach.

## The training path for trainers

| Step | Course | Length |
|---|---|---|
| 1 | [Train the trainer](../../../trainers/train-the-trainer/README.md): preparing, building the lab, designing a course, delivering, accessibility and inclusion, assessment and follow-up | 34 lessons, about 21 hours |
| 2 | the subject course they will teach, taken as a participant (for example [Editors 101](../../../trainers/editors-101/README.md)) | see the catalogue |
| 3 | teach that course back to the group, as the catalogue suggests for new trainers | |
| 4 | [Assessments and practical exercises](../../../trainers/assessments/README.md), if you assess learners | about 6 hours |

## The lab

A training installation is separate from the live site. The [Lab quick card](../../../trainers/lab-quick-card.md)
lists every command to build, serve, check, reset and remove one. The quickest start is the console installer on
SQLite, which needs no database server:

```bash
./console exp:install
```

([Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)). The generated
administrator password is printed once and written to `var/log/initial-admin-password`; change it after the first
login.

Build the lab on the same Exponential version as your live site, and reset it between courses. Never use copies of
the live database with real personal data for training; use test content.

## Adapting and maintaining the material

- The courses are free documentation under the GNU FDL 1.3 or later. You may adapt them for your organization if you
  keep the notices and license the result the same way ([Free documentation](../../../free-documentation.md)).
- Add your own content model, section names and editorial rules to the exercises.
- After every upgrade of the live site, rehearse the exercises again and update screenshots or steps that changed
  ([Writing exercises that work](../../../trainers/train-the-trainer/module-03-designing-a-course/writing-exercises-that-work.md)).
- Use the reusable checklists and templates in the [Trainer toolkit](../../../trainers/trainer-toolkit.md).
- Improvements that help everyone can go back to the project as documentation contributions
  ([Contributing docs and translations](../../../partners/contributing-docs-and-translations.md)).

## Steps

1. Name one or two trainers and agree their time with their managers.
2. Send them through Train the trainer, delivered by a partner or self-paced.
3. Build the lab and a reset routine.
4. Adapt Editors 101 to your site; rehearse it.
5. Run a pilot course with a few friendly participants; collect feedback
   ([Collecting feedback](../../../trainers/train-the-trainer/module-06-assessment-and-follow-up/collecting-feedback.md)).
6. Put the course into the onboarding routine (see "Onboarding new staff" in this folder).
7. Review the material after every upgrade and once a year.

## Related pages

- [Training in procurement](README.md)
- [Train the trainer: course overview](../../../trainers/train-the-trainer/README.md)
- [Trainers: start here](../../../trainers/trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
