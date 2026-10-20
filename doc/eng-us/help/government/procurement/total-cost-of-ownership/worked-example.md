---
title: A worked TCO example with labeled assumptions
audience: government, project-manager
category: public-sector
order: 110
summary: A worked total cost of ownership calculation for an invented public body's website on Exponential, in neutral currency units, where every number is an example assumption - to show the arithmetic and the structure, never to predict your costs.
keywords: worked example, TCO example, cost calculation, example assumption, business case, budget model, five-year cost, comparison
type: help
---
# A worked TCO example with labeled assumptions

This page is for project managers who want to see a total cost of ownership model filled in before they fill in
their own. The organization, the figures and the currency are invented. **Every number on this page is an example
assumption**, chosen only to make the arithmetic easy to follow. None of them is a price, an estimate or a
benchmark for Exponential, for hosting or for any supplier. Replace each one with your own quotes and records.

## Contents
1. [The invented scenario](#the-invented-scenario)
2. [Assumptions](#assumptions)
3. [One-off costs](#one-off-costs)
4. [Recurring costs per year](#recurring-costs-per-year)
5. [The total](#the-total)
6. [What the example teaches](#what-the-example-teaches)

## The invented scenario

The Department of Example runs one public website in two languages with a small editorial team spread over
departments. It plans for a horizon of five years, hosts with an external provider, and buys implementation and
support from one supplier. Amounts are in "CU", an invented currency unit.

## Assumptions

| # | Assumption | Value |
|---|---|---|
| A1 | Horizon | 5 years (example assumption) |
| A2 | Internal hourly rate, all roles | 50 CU (example assumption) |
| A3 | Supplier day rate | 800 CU (example assumption) |
| A4 | Kernel license fee | 0 CU - this one is a fact: GPL, no license fee ([License costs](license-costs.md)) |
| A5 | Implementation effort | 60 supplier days (example assumption) |
| A6 | Content migration effort | 15 supplier days (example assumption) |
| A7 | Accessibility audit of delivered site | 6 supplier days (example assumption) |
| A8 | Penetration test before launch | 5 supplier days (example assumption) |
| A9 | Hosting, production and staging | 3,000 CU per year (example assumption) |
| A10 | Support contract | 6,000 CU per year (example assumption) |
| A11 | Applying releases and updates | 8 supplier days per year (example assumption) |
| A12 | Editors | 12 people, 4 extra hours per month each (example assumption) |
| A13 | Site administrator | 1 person, 16 hours per month (example assumption) |
| A14 | Initial training | 2 sessions of Editors 101 at 3 trainer days each, plus 1 site administration course of 5 trainer days (example assumption for the session count; course lengths from the [Course catalogue](../../../trainers/course-catalogue.md)) |
| A15 | Onboarding of new staff | 2 trainer days per year (example assumption) |
| A16 | Exit at end of horizon | 10 supplier days (example assumption) |
| A17 | Risk allowance | 10 % of all other costs (example assumption) |

## One-off costs

| Line | Calculation | CU |
|---|---|---|
| Implementation | A5 60 days x A3 800 | 48,000 |
| Migration | A6 15 x 800 | 12,000 |
| Accessibility audit | A7 6 x 800 | 4,800 |
| Penetration test | A8 5 x 800 | 4,000 |
| Initial training (trainer) | A14 (3 + 3 + 5) = 11 days x 800 | 8,800 |
| Initial training (participants) | 12 editors x 3 days x 8 h x A2 50 + 1 admin x 5 days x 8 h x 50 | 16,400 |
| Exit | A16 10 x 800 | 8,000 |
| **One-off total** | | **102,000** |

## Recurring costs per year

| Line | Calculation | CU per year |
|---|---|---|
| License | A4 | 0 |
| Hosting | A9 | 3,000 |
| Support contract | A10 | 6,000 |
| Updates | A11 8 x 800 | 6,400 |
| Editors' extra time | A12 12 x 4 h x 12 months x 50 | 28,800 |
| Site administrator | A13 16 h x 12 x 50 | 9,600 |
| Onboarding | A15 2 x 800 | 1,600 |
| **Recurring per year** | | **55,400** |

## The total

```text
One-off                      102,000 CU
Recurring  55,400 x 5 years  277,000 CU
Subtotal                     379,000 CU
Risk allowance (A17, 10 %)    37,900 CU
TCO over 5 years             416,900 CU     (all inputs are example assumptions)
```

## What the example teaches

These lessons come from the structure, not from the invented numbers:

- **The license line is zero, and it barely matters.** In this structure, staff time and services dominate. A bid
  comparison on license price alone would miss almost everything.
- **Internal staff time is a large line** even though it never appears in a bid. Count it ([Staff and skills](staff-and-skills.md)).
- **Exit is a one-off line** that belongs in the model from the start ([Exit plans](../exit-plans/README.md)).
- **Updates recur every year**; a model that stops after launch understates the cost
  ([Upgrade and maintenance costs](upgrade-and-maintenance-costs.md)).
- **Sensitivity**: change one assumption at a time (for example A12) and see how much the total moves. The
  assumptions that move it most deserve the best evidence.

## Related pages
- [Total cost of ownership: overview](README.md)
- [TCO worksheet template](tco-worksheet-template.md)
- [The structure of a TCO model](tco-model-structure.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
