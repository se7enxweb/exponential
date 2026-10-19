---
title: Total cost of ownership
audience: businesses, project-manager, admin
category: business-solutions
order: 10
summary: The cost lines of a website application on Exponential over its whole life, which of them the software makes smaller, and a worked five-year calculation to copy with your own figures.
keywords: total cost of ownership, TCO, five years, cost lines, implementation, hosting, maintenance, support, training, exit cost, license fee, budget
type: help
---
# Total cost of ownership

This page is for owners, managers and finance staff who must say what a website application really costs, not only
in the first year. It lists every cost line a site on Exponential has over its life, shows which facts about the
software make a line smaller, and works through a five-year calculation you can copy.

> **Note**
> All numbers in the calculation are **Example assumptions**: round, illustrative figures in a neutral currency unit.
> They are not market prices and not quotes. Replace every one with your own figures before you use the result.

## Contents

1. [What TCO means here](#what-tco-means-here)
2. [The cost lines](#the-cost-lines)
3. [What makes each line smaller on Exponential](#what-makes-each-line-smaller-on-exponential)
4. [A worked five-year example](#a-worked-five-year-example)
5. [Costs people forget](#costs-people-forget)
6. [How to collect your own figures](#how-to-collect-your-own-figures)

## What TCO means here

Total cost of ownership is the sum of every cost of a site from the decision to build it until the day it is
replaced, divided by the years it runs. A cheap start with expensive years later is not cheap. Five years is a
common period for the comparison; choose the period your organization uses for other investments.

## The cost lines

| Line | One-time or recurring | Typical contents |
|---|---|---|
| License | recurring in many products | for Exponential: none (see below) |
| Implementation | one-time | planning, content model, design, templates, extensions, data migration |
| Hosting | recurring | servers or a hosting plan, backups, monitoring, TLS certificates, mail delivery |
| Operations | recurring | applying updates, watching logs, cronjobs, restore tests |
| Upgrades | periodic | moving to a newer release, PHP upgrades, testing |
| Support | recurring | a support contract, or the time of your own staff |
| Training | one-time and on staff change | editors, administrators |
| Content work | recurring | writing, translating, checking; often the largest line, and the same on every product |
| Exit | at the end | moving content and data out, or to another supplier |

## What makes each line smaller on Exponential

| Line | Fact | Source |
|---|---|---|
| License | "Using Exponential under the terms of the GNU GPL is free of charge." | `README.md`, "License" |
| License per server, site or user | "The act of running the Program is not restricted" | `LICENSE.md` section 0; [Licensing explained for buyers](../../business/licensing-for-buyers.md) |
| Implementation | Shop, multisite, multilingual content, workflows, a REST API, contact forms, feeds and an audit trail are built in | `README.md`, "Main Exponential features" |
| Hosting | SQLite 3, the default database, needs no database server; Velocity needs nothing installed beyond PHP | `README.md`, "Requirements" |
| Hosting | Runs on almost any operating system with PHP 8.0 or later | [Operating system support](../../os-support.md) |
| Operations | One command installs a site; maintenance mode, a log viewer and a health check ship with it | [Install in one command](../../../features/6.0/install-in-one-command.md), [Maintenance mode](../../../features/6.0/maintenance-mode.md), [Log viewer](../../../features/6.0/log-viewer.md) |
| Upgrades | One maintained line (6.0); fixes arrive as the next 6.0.x release | `SECURITY.md`; [Upgrading](../../../guides/upgrading.md) |
| Support | Free community channels exist; paid support can come from 7x or any other company | `README.md`, "Where to get more help"; [Who is 7x?](../../who-is-7x.md) |
| Exit | The GPL lets you take the code to any supplier; the full Git history is public | [Licensing explained for buyers](../../business/licensing-for-buyers.md), [Develop with Git](../../develop-with-git.md) |

None of this makes content work cheaper by itself: writing and checking content costs the same time on any system.
Count it anyway, because it is usually where most of the money goes.

## A worked five-year example

The example is a mid-sized company website with a contact form and a news section. Every input is an
**Example assumption**.

| Input | Value |
|---|---|
| Example assumption: implementation by a partner | 20,000 currency units, once |
| Example assumption: editor and administrator training | 1,000 currency units, once |
| Example assumption: hosting plan | 100 currency units a month |
| Example assumption: operations by own staff | 20 hours a year at 50 currency units an hour |
| Example assumption: support contract | 1,500 currency units a year |
| License fee | 0 (fact: `README.md`, "License") |

Step by step:

1. One-time costs: 20,000 + 1,000 = **21,000**.
2. Hosting per year: 100 x 12 = 1,200. Over five years: 1,200 x 5 = **6,000**.
3. Operations per year: 20 x 50 = 1,000. Over five years: 1,000 x 5 = **5,000**.
4. Support over five years: 1,500 x 5 = **7,500**.
5. License over five years: 0 x 5 = **0**.
6. Total cost of ownership: 21,000 + 6,000 + 5,000 + 7,500 + 0 = **39,500**.
7. Per year: 39,500 / 5 = **7,900**.

Now replace each example value with your own. If you compare with another product, add its license or subscription
line from that vendor's written quote, and keep every other line the same so the comparison is fair.

> **Tip**
> Add a line for one upgrade during the period, and one for the cost of downtime you expect. The
> [budgeting page](budgeting-for-upgrades-and-maintenance.md) and the
> [cost of downtime worksheet](cost-of-downtime-worksheet.md) show how.

## Costs people forget

- [ ] Staff time for restore tests ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)).
- [ ] PHP and operating system upgrades on the server, not only Exponential upgrades.
- [ ] Mail delivery for forms, receipts and newsletters ([Mail delivery](../../hosting/operations/mail-delivery.md)).
- [ ] Payment provider fees for a store; they are set by the provider, not by Exponential.
- [ ] Fees of a hosted AI provider, if you switch on Exponential Intelligence with a hosted model (`README.md`,
      "Requirements" names "an API key of a hosted AI provider, or a local open-weight model").
- [ ] Handover time when a staff member or volunteer leaves.
- [ ] Accessibility and data protection reviews.
- [ ] The cost of moving out at the end, even if you never do.

## How to collect your own figures

1. Ask two or three partners or hosts for written quotes on the same requirements
   ([Requirements checklist](../templates/requirements-checklist.md)).
2. Ask your own staff to estimate hours for operations, and check them against a month of real work.
3. Take hosting figures from the plan you would really buy; for sizing, see
   [Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md), which states only figures the
   repository gives.
4. Write every figure into the [ROI worksheet template](../templates/roi-worksheet-template.md) with its source.
5. Review the figures once a year with the [annual review checklist](../templates/annual-review-checklist.md).

## Related pages

- [What you do not pay for](what-you-do-not-pay-for.md)
- [Return on investment of stability](roi-of-stability.md)
- [Hosting cost drivers](hosting-cost-drivers.md)
- [Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
