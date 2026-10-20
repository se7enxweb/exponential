---
title: Budgeting training effort
audience: government, trainer, project-manager
category: public-sector
order: 50
summary: How to estimate the training effort for an Exponential project in a public body from the published course lengths and your own head counts and day rates - a worksheet with formulas and no invented prices.
keywords: training budget, training cost, effort, person days, course length, day rate, seats, estimate, worksheet, total cost of ownership, public sector
type: help
---
# Budgeting training effort

This page is for the project manager who has to put a training line into a budget or a total cost of ownership
model. It gives a method and a worksheet. The only figures it uses are the course lengths published in the
[Course catalogue](../../../trainers/course-catalogue.md); every price, rate and head count is yours to fill in.

## Contents

1. [What training costs consist of](#what-training-costs-consist-of)
2. [Hours from the catalogue](#hours-from-the-catalogue)
3. [The worksheet](#the-worksheet)
4. [Recurring training](#recurring-training)
5. [Costs that are often forgotten](#costs-that-are-often-forgotten)

## What training costs consist of

| Item | Paid to | Depends on |
|---|---|---|
| Trainer time (preparation and delivery) | a partner, or your own trainer's time | course length, number of sessions |
| Participants' time | your organization (staff not doing their normal work) | course length times participants |
| Training installation (lab) | your IT or the supplier | one installation; SQLite needs no database server |
| Material adaptation | the trainer | how much you adapt the free material |
| Rooms, equipment, travel | your organization | in person or online |
| Assessment and certificates | the trainer | whether you assess |

The course material itself costs nothing: it is free documentation under the GNU FDL 1.3
([Free documentation](../../../free-documentation.md)). Exponential has no license fee per user or per seat
([Licensing questions](../../../project/licensing-faq.md)).

## Hours from the catalogue

| Course | Lessons | Hours (without breaks) |
|---|---|---|
| Editors 101 | 27 | about 19.5 |
| Editors 201 | 23 | about 16 |
| Exponential Layouts | 23 | about 17.5 |
| Site administration | 48 | about 30 |
| Hosting and operations | 28 | about 20 |
| Newsletters | 20 | about 16 |
| Search | 20 | about 10 |
| Train the trainer | 34 | about 21 |

Source: [Course catalogue](../../../trainers/course-catalogue.md). Many courses offer shorter schedules built from
selected modules; the one-day agendas of the [Workshops](../../../trainers/workshops/README.md) are another option.
Add breaks to the hours when you plan days.

## The worksheet

```text
Training effort - <project>                                   all values are your own inputs

Course            Hours   Sessions  Participants  Trainer prep factor
Editors 101       19.5    <s1>      <p1>          <f1>
Site admin        30      <s2>      <p2>          <f2>
...

Trainer hours     = sum over courses of Hours x Sessions x (1 + prep factor)
Participant hours = sum over courses of Hours x Participants
Trainer cost      = Trainer hours x <your trainer rate, or partner's quoted rate>
Staff time cost   = Participant hours x <your internal hourly cost>
Lab               = <set-up hours> x <IT rate>  (+ hosting, if any)
Other             = rooms + travel + material adaptation
Total             = Trainer cost + Staff time cost + Lab + Other
```

The preparation factor is your estimate of how long a trainer prepares per hour of teaching; a trainer who teaches the
same course often needs less. Ask a partner to quote per session, and compare quotes on the same hours.

## Recurring training

The first year carries the launch training. Later years carry:

- onboarding of new staff (estimate from your staff turnover; see "Onboarding new staff" in this folder);
- refreshers when the site gains features or after an upgrade changes the administration;
- training of a new supplier's staff, if the contract changes hands.

Put a yearly line in the budget for these, not only the launch training.

## Costs that are often forgotten

- **Participants' time** is often the largest item and is not invoiced; count it anyway.
- **Rehearsal** on the version you will use, so exercises work
  ([Writing exercises that work](../../../trainers/train-the-trainer/module-03-designing-a-course/writing-exercises-that-work.md)).
- **Accessibility accommodations** for participants
  ([Accommodations and inclusive practice](../../../trainers/train-the-trainer/module-05-accessibility-and-inclusion/accommodations-and-inclusive-practice.md)).
- **Translation** of material, if staff train in another language than the help offers.

## Related pages

- [Training in procurement](README.md)
- [A training plan for a public body](training-plan-for-a-public-body.md)
- The total cost of ownership part of this procurement section ([Procurement](../README.md))
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
