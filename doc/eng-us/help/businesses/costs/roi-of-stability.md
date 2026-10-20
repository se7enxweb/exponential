---
title: Return on investment of stability
audience: businesses, project-manager, admin
category: business-solutions
order: 30
summary: How to express the value of a stable platform as a return on investment - avoided downtime, fewer emergency fixes, predictable upgrades - with the facts about Exponential that support each benefit and a worked calculation built only from example assumptions.
keywords: return on investment, ROI, stability, downtime, payback, benefits, maintenance, upgrades, release line, published versions, business case
type: help
---
# Return on investment of stability

This page is for managers who must justify spending on a website platform, and for the people who write the
business case. It explains how stability turns into money you keep, which facts about Exponential support each
benefit, and how to calculate a return on investment (ROI) that a finance team can check.

> **Note**
> The calculation below uses only **Example assumptions**: round, illustrative numbers in a neutral currency unit.
> They are not measurements of any real site. Replace them with your own figures, and keep the source of each.

## Contents

1. [The formula](#the-formula)
2. [Where stability saves money](#where-stability-saves-money)
3. [The facts behind each benefit](#the-facts-behind-each-benefit)
4. [A worked example](#a-worked-example)
5. [Payback period](#payback-period)
6. [Making the case credible](#making-the-case-credible)

## The formula

ROI compares what an investment brings with what it costs, over the same period:

```text
ROI = (benefits - costs) / costs x 100 %
Payback period (months) = one-time cost / monthly net benefit
```

Benefits are money gained or money not lost. Costs are what you spend to get them. Use the same period (usually one
year, or the five years of your [total cost of ownership](total-cost-of-ownership.md)) on both sides.

## Where stability saves money

| Benefit | How it shows up in the budget |
|---|---|
| Fewer outages | Sales, bookings, donations or work hours not lost ([Cost of downtime worksheet](cost-of-downtime-worksheet.md)) |
| Fewer emergency fixes | Fewer unplanned hours of developers and administrators, often at higher rates |
| Predictable upgrades | Upgrades planned in normal hours, not rushed projects when a platform is retired |
| Less rework after updates | Templates and extensions keep working across releases in one line |
| Faster recovery | Tested restores and clear logs shorten every incident |
| Lower risk of data loss | Fewer costs for incident response and notification |

## The facts behind each benefit

| Benefit | Fact about Exponential | Source |
|---|---|---|
| Predictable upgrades | 6.0 is the maintained line; security fixes "published with the next 6.0.x release" | `SECURITY.md` |
| No surprises in a version | "A published tag is never changed" | `SECURITY.md` |
| Long life | "Exponential has made it beyond it's end of life in 2021 and survived" | `README.md`, "Exponential Project Status" |
| Current PHP | PHP 8.0 to 8.5 supported; the test suite runs on PHP 8.1 to 8.5 on every push | `README.md`, [Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#the-support-commitment) |
| Fewer outages | Velocity can be guarded by a watchdog that restarts it after a crash | [SLA options](../../hosting/business/sla-options.md) |
| Planned work without errors | Maintenance mode shows visitors a friendly page and answers 503 | [Maintenance mode](../../../features/6.0/maintenance-mode.md) |
| Faster diagnosis | The log viewer and error references find a fault quickly | [Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md) |
| Proof after an incident | A tamper-evident audit trail records who did what | [The audit trail](../../../features/6.0/audit-trail.md) |
| Safe updates | The upgrade check compares the files with their checksums and the database with the schema, and changes nothing | [Upgrade check](../../../guides/upgrade-check.md) |

The [Reliability](../reliability/README.md) section explains each of these practices for businesses.

## A worked example

A small business compares its year before and after moving to a stable, well-run Exponential site. Every input is
an **Example assumption**.

| Input | Value |
|---|---|
| Example assumption: hours offline per year before | 10 hours |
| Example assumption: hours offline per year after | 2 hours |
| Example assumption: cost of one hour offline | 300 currency units (from your [downtime worksheet](cost-of-downtime-worksheet.md)) |
| Example assumption: emergency fix hours per year before | 40 hours |
| Example assumption: emergency fix hours per year after | 10 hours |
| Example assumption: rate for emergency work | 80 currency units an hour |
| Example assumption: yearly cost of the stability practices (monitoring, restore tests, planned updates) | 2,000 currency units |

Step by step:

1. Downtime avoided: 10 - 2 = 8 hours. Value: 8 x 300 = **2,400**.
2. Emergency hours avoided: 40 - 10 = 30 hours. Value: 30 x 80 = **2,400**.
3. Total benefits per year: 2,400 + 2,400 = **4,800**.
4. Costs per year: **2,000**.
5. Net benefit: 4,800 - 2,000 = **2,800**.
6. ROI: 2,800 / 2,000 x 100 % = **140 %**.

Read it as: for every currency unit spent on stability, the example business keeps 2.4 units (4,800 / 2,000).
Your result will differ; the method stays the same.

## Payback period

If the stability work starts with a one-time cost, such as a migration or setting up monitoring, add a payback
period:

1. Example assumption: one-time cost of the move: **7,000** currency units.
2. Monthly net benefit from the example above: 2,800 / 12 = about **233** a month.
3. Payback: 7,000 / 233 = about **30 months**.

A payback inside the planned life of the site (for example five years, 60 months) means the investment returns its
money and then keeps saving.

## Making the case credible

- [ ] Take downtime hours from your own monitoring or incident notes, not from memory.
- [ ] Take hourly rates from real invoices or salary figures.
- [ ] Show the calculation, not only the result; finance will redo it.
- [ ] Count only benefits you can name; leave reputation out of the figure and mention it in words.
- [ ] Do not quote other products' prices unless you have a written quote; compare qualitatively otherwise
      ([Comparing Exponential factually](../../business/comparing-exponential-factually.md)).
- [ ] Repeat the calculation after a year with measured figures ([Annual review checklist](../templates/annual-review-checklist.md)).

## Related pages

- [Why stability matters](../reliability/why-stability-matters.md)
- [Release line and support policy](../reliability/release-line-and-support-policy.md)
- [ROI worksheet template](../templates/roi-worksheet-template.md)
- [KPIs from logs and audit](../../leadership/kpis-from-logs-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
