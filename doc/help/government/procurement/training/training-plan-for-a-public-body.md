---
title: A training plan for a public body
audience: government, trainer, project-manager
category: public-sector
order: 20
summary: How a public body plans Exponential training for the whole contract - who needs which course, in which order, at which moment of the project, and how to write that into the procurement - built on the free trainer courses of this help.
keywords: training plan, curriculum, editors, administrators, courses, onboarding, public sector, government, course catalogue, schedule, procurement
type: help
---
# A training plan for a public body

This page is for the project manager or training coordinator of a public body that introduces Exponential, and for
the procurement officer who has to describe the training in a tender. It answers: which groups need training, which
course fits each group, when in the project each course runs, and what to write down so the plan survives the
contract. The courses and their lengths come from the [Course catalogue](../../../trainers/course-catalogue.md).

## Contents

1. [Why a plan, not a one-off course](#why-a-plan-not-a-one-off-course)
2. [The groups in a public body](#the-groups-in-a-public-body)
3. [The courses available](#the-courses-available)
4. [When each course runs](#when-each-course-runs)
5. [Who delivers it](#who-delivers-it)
6. [A plan template](#a-plan-template)
7. [Pitfalls](#pitfalls)

## Why a plan, not a one-off course

A public website lives longer than the project that builds it. Editors change departments, new staff arrive, the
contractor who built the site leaves. A plan answers three questions for the whole term: who is trained before go-live,
how new people are trained later, and who keeps the training material current when the software changes.

The material to build on already exists and is free: every trainer course in this help is free documentation under
the GNU FDL 1.3, so your organization or your supplier may copy and adapt it, keeping the notices
([Free documentation](../../../free-documentation.md)).

## The groups in a public body

| Group | What they do in Exponential | Typical size |
|---|---|---|
| Editors | write and update pages, news, documents | the largest group, spread over departments |
| Senior editors and approvers | review, translate, organize, approve | a few per department |
| Site administrators | users, roles, sections, caches, cronjobs, logs, upgrades | a small central team |
| System administrators or the host | servers, PHP, database, backups, Velocity | central IT or the hosting company |
| Developers and designers | templates, extensions, the public design | in-house or the implementation partner |
| Trainers | teach the others | one or two people, if you train in-house |
| Decision makers | understand what the system can and cannot do | managers, the project board |

The matrix that maps each group to courses is on the page "Roles to courses" in this folder.

## The courses available

| Course | For | Length (lessons, without breaks) |
|---|---|---|
| [Editors 101](../../../trainers/editors-101/README.md) | new editors | 27 lessons, about 19.5 hours |
| [Editors 201](../../../trainers/editors-201/README.md) | editors who publish every week | 23 lessons, about 16 hours |
| [Exponential Layouts](../../../trainers/layouts/README.md) | editors and site builders who build pages with blocks | 23 lessons, about 17.5 hours |
| [Site administration](../../../trainers/site-administrators/README.md) | people who run a site | 48 lessons, about 30 hours |
| [Hosting and operations](../../../trainers/hosting-operations/README.md) | system administrators | 28 lessons, about 20 hours |
| [Search](../../../trainers/search/README.md) | administrators and developers | 20 lessons, about 10 hours |
| [Multisite and site packages](../../../trainers/multisite-and-packages/README.md) | administrators of several sites | 26 lessons, about 22 hours |
| [Newsletters](../../../trainers/newsletter/README.md) | editors and administrators who send newsletters | 20 lessons, about 16 hours |
| [Developers](../../../trainers/developers/README.md) | PHP developers | 52 lessons, about 44 hours |
| [Theme design](../../../trainers/theme-designers/README.md) | designers and front-end developers | 30 lessons, about 24 hours |
| [Migrating to Exponential](../../../trainers/migration/README.md) | migration project teams | 25 lessons, about 22.5 hours |
| [Train the trainer](../../../trainers/train-the-trainer/README.md) | people who will teach | 34 lessons, about 21 hours |
| [Workshops and bootcamps](../../../trainers/workshops/README.md) | groups that need a compact program | half-day to five-day programs |

The lengths are those the catalogue states. Each course overview also lists shorter schedules built from its modules.

## When each course runs

| Project moment | Training |
|---|---|
| Before the tender | a half-day for decision makers ([Decision makers' half day](../../../trainers/workshops/module-02-one-day-agendas/decision-makers-half-day.md)) so the panel knows the product |
| During the build | Site administration for the central team; Developers and Theme design for in-house developers |
| Two to four weeks before go-live | Editors 101 for every editor who will publish at launch; the content migration often doubles as practice |
| Shortly after go-live | Exponential Layouts for page builders; Newsletters if the site sends them |
| A few months after go-live | Editors 201 for the editors who publish every week |
| Every year | refresher sessions; the "Annual review" checklist of the government checklists section |
| Whenever someone joins | the onboarding path on the page "Onboarding new staff" in this folder |

Train editors close to the day they start publishing. A course taken months before go-live is forgotten by launch.

## Who delivers it

Three options, which can be combined:

1. **The implementation partner** trains as part of the project. Write the courses and the number of sessions into the
   contract (see the training clause in the tender templates part of this section).
2. **A training partner** delivers the courses ([Training partners](../../../partners/training-partners.md)). Note that
   what makes a course "official" is not yet published by the project; ask for the course outline, not a label.
3. **Your own trainers**, prepared with [Train the trainer](../../../trainers/train-the-trainer/README.md), teach the
   courses from this help. This keeps the knowledge in-house after the contract ends.

## A plan template

```text
Training plan - <site name>                         version <x>, <date>, owner <role>

Group                Course(s)                     Delivered by       When              Seats
Editors              Editors 101                   <supplier/in-house> <weeks before go-live> <n>
Approvers            Editors 201 (modules <list>)  <...>              <...>             <n>
Site administrators  Site administration           <...>              <during build>    <n>
System administrators Hosting and operations       <...>              <before go-live>  <n>
New staff            onboarding path               in-house           within <n> weeks  as needed

Lab: <training installation, see the lab quick card>   Material: free documentation (GFDL 1.3), adapted by <owner>
Assessment: <written test / practical task>            Review of this plan: every <period>
```

Build the lab with the [Lab quick card](../../../trainers/lab-quick-card.md); a training installation on SQLite needs
no database server ([Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)).

## Pitfalls

- **Training on the production site.** Use a separate training installation; exercises create and delete content.
- **Training on a different version.** Rehearse every exercise on the version you will use
  ([Course catalogue](../../../trainers/course-catalogue.md#how-to-use-a-course)).
- **No plan for new staff.** The launch group is trained; the people who arrive a year later are not.
- **Training material owned by the supplier.** Ask in the contract that adapted material stays usable by you; the
  help it is built on is already free documentation.

## Related pages

- [Training in procurement](README.md)
- [Course catalogue](../../../trainers/course-catalogue.md)
- [Trainers: start here](../../../trainers/trainers-start-here.md)
- [Running an Exponential training course](../../../partners/running-a-training-course.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
