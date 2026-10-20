---
title: ROI worksheet template
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 80
summary: A copy-ready worksheet for the business case of a website application on Exponential - one-time and running costs, benefits from sales, time saved and avoided downtime, net benefit, ROI and payback - with the formulas, a source column for every figure, and a short filled-in line to show the arithmetic.
keywords: ROI worksheet, return on investment, business case, total cost of ownership, payback, benefits, costs, template, spreadsheet, formulas
type: help
---
# ROI worksheet template

This page is for whoever prepares the business case for a site on Exponential. The worksheet below collects every
cost and benefit with its source, then computes the net benefit, the return on investment (ROI) and the payback
period. Copy it into a spreadsheet. The method is explained on
[Return on investment of stability](../costs/roi-of-stability.md) and the cost lines on
[Total cost of ownership](../costs/total-cost-of-ownership.md).

> **Note**
> The worksheet contains no figures. The one filled-in line in the example uses **Example assumptions** in a neutral
> currency unit only to show the arithmetic. Use your own figures, from quotes, invoices, payroll and your own
> systems, and write the source next to each.

## Contents

1. [Before you start](#before-you-start)
2. [The worksheet](#the-worksheet)
3. [Formulas](#formulas)
4. [Showing the arithmetic](#showing-the-arithmetic)
5. [Checking the result](#checking-the-result)

## Before you start

- [ ] Choose the period (for example 5 years) and use it on both sides.
- [ ] Collect written quotes for implementation, hosting and support
      ([Questions to ask a partner](../choosing/questions-to-ask-a-partner.md#costs)).
- [ ] Fill in the [Cost of downtime worksheet](../costs/cost-of-downtime-worksheet.md#the-blank-worksheet) if
      availability is part of the case.
- [ ] Remember the lines that are zero, with their source: license fee
      ([What you do not pay for](../costs/what-you-do-not-pay-for.md)).

## The worksheet

```text
ROI WORKSHEET - <project>   Period: <n> years   Prepared by: <name>   Date: <date>
Currency unit: <unit>

A. ONE-TIME COSTS                                  Amount     Source
A1 Implementation (design, build, migration)       ______     ______
A2 Training                                        ______     ______
A3 Content preparation                             ______     ______
A4 Other one-time: ______                          ______     ______
A  Total one-time                                  ______

B. RUNNING COSTS PER YEAR
B1 License fee for Exponential                     0          README "License"
B2 Hosting                                         ______     ______
B3 Operations and updates (hours x rate)           ______     ______
B4 Support contract                                ______     ______
B5 Third-party services (payment, mail, AI ...)    ______     ______
B6 Reserve for surprises (% of B2-B5)              ______     ______
B  Total running per year                          ______

C. BENEFITS PER YEAR
C1 Gross profit from new online sales (after fees) ______     ______
C2 Staff time saved (hours x cost x realization %) ______     ______
C3 Downtime avoided (hours x cost per hour)        ______     ______
C4 Costs no longer paid (old system, licenses)     ______     ______
C5 Other measurable benefit: ______                ______     ______
C  Total benefits per year                         ______

D. RESULTS
D1 Total cost over period      = A + B x n                    ______
D2 Total benefit over period   = C x n                        ______
D3 Net benefit                 = D2 - D1                      ______
D4 ROI                         = D3 / D1 x 100 %              ______
D5 Monthly net running benefit = (C - B) / 12                 ______
D6 Payback (months)            = A / D5                       ______

E. NOT IN THE FIGURES (describe in words)
E1 ________________________________________________
E2 ________________________________________________

Reviewed by: __________   Approved by: __________   Re-check after one year: <date>
```

## Formulas

| Result | Formula | Note |
|---|---|---|
| Total cost over the period | A + B x n | n = years |
| Total benefit | C x n | grow C over the years only if you can justify it |
| Net benefit | total benefit - total cost | |
| ROI | net benefit / total cost x 100 % | |
| Payback | one-time cost / monthly net running benefit | meaningful only when C is larger than B |

In a spreadsheet, put each line in its own cell and reference cells in the formulas, so a change in one input
updates the result.

## Showing the arithmetic

A one-line illustration with **Example assumptions**: A = 10,000; B = 2,000 a year; C = 6,000 a year; n = 5.

1. D1 = 10,000 + 2,000 x 5 = 10,000 + 10,000 = **20,000**.
2. D2 = 6,000 x 5 = **30,000**.
3. D3 = 30,000 - 20,000 = **10,000**.
4. D4 = 10,000 / 20,000 x 100 % = **50 %**.
5. D5 = (6,000 - 2,000) / 12 = about **333** a month.
6. D6 = 10,000 / 333 = about **30 months**.

Full worked examples: [small store](../costs/worked-example-small-store.md),
[intranet](../costs/worked-example-intranet.md), [nonprofit](../costs/worked-example-nonprofit.md).

## Checking the result

- [ ] Every figure has a source; no line is a guess presented as a fact.
- [ ] Benefits count only what the site adds, not what moves from another channel.
- [ ] Staff time saved has a realization factor you can defend.
- [ ] Comparisons with other products use written quotes, or stay qualitative
      ([Comparing Exponential factually](../../business/comparing-exponential-factually.md)).
- [ ] Someone outside the project has redone the arithmetic.
- [ ] A date is set to repeat the calculation with measured figures ([Annual review checklist](annual-review-checklist.md)).

## Related pages

- [Return on investment of stability](../costs/roi-of-stability.md)
- [Total cost of ownership](../costs/total-cost-of-ownership.md)
- [KPIs from logs and audit](../../leadership/kpis-from-logs-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
