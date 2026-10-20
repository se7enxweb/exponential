---
title: "Exponential Intelligence: course overview"
audience: trainer, editor, admin
category: training
order: 1
summary: The course on Exponential Intelligence (expai), the writing assistant, 7 modules and 21 lessons, about 15.5 hours of lessons, from the three promises through the Intelligence tab, the edit form, Vector and chains to responsible use and administration, ending in a capstone task.
keywords: exponential intelligence, expai, ai writing assistant, course overview, intelligence tab, edit form, field actions, alt text, vector, staged review, chains, log, privacy, personal data, reviewing ai output, cost, providers, models, ei summary
type: help
---
# Exponential Intelligence: course overview

Exponential Intelligence (the extension **expai**) is the writing assistant of Exponential's administration. It
writes drafts, never publishes on its own, and works with the signed-in editor's permissions. This course teaches
editors to use it well and responsibly, and administrators to run it safely.

## Who it is for

- Editors who will use the assistant in the Intelligence tab, the edit form, Vector and chains.
- Chief editors and approvers who review what it wrote.
- Administrators who switch it on, choose providers and models, and set limits.
- Data protection officers who need to know what goes where (lessons 1.1, 6.1 and 7.3).

## Outcome

After the course, learners can explain what the assistant does and never does; write safe briefs without personal
data; work with the Intelligence tab and its drafts; use field actions, Fill the fields, presets and alt text in the
edit form; plan and review bulk work in Vector; run an AI chain to its human checkpoint; read the log; review AI
output with a checklist; keep cost within limits; and, for administrators, read providers and models, switch the
assistant on with keys, limits and budgets, and produce **EI: Summary**.

## Format and length

| | |
|---|---|
| Lessons | 21 lessons in 7 modules |
| Lesson time | 15 hours 30 minutes (presenting and exercises) |
| With breaks | about 16 hours 20 minutes, as in the modules' timing plans |
| Format | Classroom with a projector, or online with screen sharing; some pair work |
| Group size | Up to about 12 learners per trainer |
| Level | Beginner to advanced (module 7 lessons 7.1 to 7.3 are for administrators) |

## Prerequisites

- Learners: Editors 101 or experience as an editor. Editors 201 helps (approvals, accessibility).
- Administrators in module 7: shell access to a training installation of their own.
- Trainer: experience with the assistant on the version they teach, and administrator rights on the training
  installation ([Train the trainer: course overview](../train-the-trainer/README.md)).

## Lab setup

- A training installation with expai and expqueue active and configured (see lesson 7.2 and
  [expai: Switch it on](../../../features/6.0/extensions/expai.md#switch-it-on)), the job queue running, and a provider
  account meant for training or a local model. Never a production key, never real customer content.
- One account per learner with `expai/write` and `ezjscore/call`, plus `expai/vector` (staged, limited to the training
  folder) and `expai/chain` for modules 4 and 5; e-mail addresses at `example.invalid`.
- Usage limits per training user (for example a daily cost limit) and, optionally, a monthly site budget.
- Each learner's course folder with a few short articles and neutral sample images (no people).
- Build and reset the lab: [Module 2: Building the lab](../train-the-trainer/module-02-building-the-lab/README.md) of
  Train the trainer, and [7. The console install](../../../install/07-console-install.md).

## Modules

| Module | Lessons | Duration | Topics |
|---|---|---|---|
| [Module 1: Introducing Exponential Intelligence](module-01-introducing-exponential-intelligence/README.md) | 3 | 1 h 45 min | The three promises, the dashboard, permissions |
| [Module 2: The Intelligence tab](module-02-the-intelligence-tab/README.md) | 3 | 2 h 10 min | A first conversation, drafts, history and records |
| [Module 3: The edit form](module-03-the-edit-form/README.md) | 3 | 2 h 25 min | Field actions, Fill the fields, presets, Improve, alt text |
| [Module 4: Vector](module-04-vector/README.md) | 3 | 2 h 40 min | Threads and scope, plans and staged review, checkpoints |
| [Module 5: Chains and the log](module-05-chains-and-the-log/README.md) | 2 | 1 h 30 min | AI chains and the checkpoint, the log |
| [Module 6: Responsible use](module-06-responsible-use/README.md) | 3 | 2 h 25 min | Privacy and personal data, reviewing output, cost and limits |
| [Module 7: Administration](module-07-administration/README.md) | 4 | 3 h 25 min | Providers and models, switching on, limits, EI: Summary, capstone |

## Reading path

For a trainer preparing the course, in this order:

1. [expai: Exponential Intelligence](../../../features/6.0/extensions/expai.md): the overview for editors,
   administrators and developers.
2. In the administration, **EI: Help**: the usage guide, then the pages on the Intelligence tab, the edit form, presets
   and alt text (modules 2 and 3).
3. **EI: Help**: security, data policy, permissions and FAQ (modules 1 and 6). Read the security page's section on
   prompt injection twice.
4. **EI: Help**: Vector, threads, review, plan mode and checkpoints (module 4); chains (module 5); dashboard and usage
   tracking (modules 1 and 5).
5. **EI: Help**: the installation guide, configuration, providers, probe, budgets and the EI: Summary page (module 7).
6. [expqueue](../../../features/6.0/extensions/expqueue.md), [Workflows](../../../guides/workflows.md) and
   [Roles and policies](../../../guides/roles-and-policies.md).
7. Your organization's rules on AI use and data protection, to quote in module 6.
8. Then the modules in order: each module's overview first (its preparation list and timing plan), then its lessons.
   Run every exercise once yourself and note what it cost.

## Suggested schedules

Lesson times add up to 15 hours 30 minutes; the schedules add breaks.

### Editors: two days

Modules 1 to 6 and lesson 7.4 (13 hours 15 minutes of lessons, two long days):

| Day | Morning | Afternoon |
|---|---|---|
| 1 | Module 1, Module 2 | Module 3, Module 6 (lesson 6.1) |
| 2 | Module 4, Module 5 | Module 6 (lessons 6.2 and 6.3), lesson 7.4 |

### Administrators: one day

Lessons 1.1, 1.3, 6.1, 6.3, 7.1, 7.2 and 7.3 (4 hours 30 minutes of lessons), plus lesson 2.1 to see the editor's side.

### A short introduction (half a day)

Lessons 1.1, 2.1, 2.2, 3.1 and 6.1 (3 hours 30 minutes of lessons): enough to use the Intelligence tab and the field
actions safely. Schedule module 6 in full within a month.

## Assessing learners

- Each lesson's quiz and expected result check understanding during the course.
- Lesson 7.4 (the capstone) has a seven-point checklist for a final practical check.
- The assessments course (Assessments and practical exercises, in this section of the help) has further material.
  All assessments are the trainer's own material, not an official certification; the partner program's
  certifications are separate and their details are not published
  ([Certifications](../../partners/certifications.md)).

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
