---
title: Estimate staff and training effort
audience: government, project-manager, trainer
category: public-sector
order: 140
summary: Step by step, estimate the internal staff time and training effort an Exponential site needs in a public body - roles, hours per routine task, training hours from the course catalogue, and turnover - with your own figures.
keywords: staff effort, training effort, internal cost, roles, site administrator, editors, hours, course lengths, turnover, budget, procurement task
type: help
---
# Estimate staff and training effort

**Who:** the project manager with the training coordinator. **When:** while building the budget. Internal time is
often the largest cost and the one that is not invoiced. Background: [Staff and skills](../../procurement/total-cost-of-ownership/staff-and-skills.md),
[Budgeting training effort](../../procurement/training/budgeting-training-effort.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Worksheet](#worksheet)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- The list of people who will work with the site, by role ([Roles to courses](../../procurement/training/roles-to-courses.md)).
- Your internal hourly cost per role (from finance).

## Steps

1. **List the roles** and the number of people in each: editors, approvers, site administrators, system administrators,
   developers.
2. **Estimate routine hours per role and month** from the tasks they do. For administrators, take the routine from
   [Keeping a site current over a multi-year contract](../../procurement/lifecycle/keeping-a-site-current.md) and the
   [Quarterly access review](../../checklists/quarterly-access-review.md).
3. **Take training hours from the catalogue**: Editors 101 about 19.5 hours, Editors 201 about 16, Site administration
   about 30, Hosting and operations about 20 ([Course catalogue](../../../trainers/course-catalogue.md)).
4. **Add turnover**: how many new people per year, each needing onboarding
   ([Onboarding new staff](../../procurement/training/onboarding-new-staff.md)).
5. **Add one-off work**: content migration by editors, acceptance testing, the exit drill.
6. **Multiply** by internal rates and put the total into the cost model.

## Worksheet

```text
Staff and training effort - <site>, per year            all values: your own estimates

Role                People  Routine h/month  Training h (yr 1)  New people/yr  Rate
Editors             <n>     <h>              19.5 (Ed. 101)     <n>            <r>
Approvers           <n>     <h>              +16 (Ed. 201)      <n>            <r>
Site administrators <n>     <h>              30 (Site admin)    <n>            <r>
System admins       <n>     <h>              20 (Hosting & ops) <n>            <r>

Routine cost   = sum(People x h/month x 12 x Rate)
Training cost  = sum(People x training h x Rate) + new people x training h x Rate
One-off work   = migration + acceptance + exit drill hours x Rate
```

## Result

A staff-time line and a training line for the budget, with assumptions you can revise.

## Pitfalls

- Leaving out participants' time in training.
- Forgetting that a supplier's work still needs your staff's time: reviews, approvals, acceptance tests.

## Related pages

- [Procurement and training tasks](README.md)
- [Training costs](../../procurement/total-cost-of-ownership/training-costs.md)
- [A training plan for a public body](../../procurement/training/training-plan-for-a-public-body.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
