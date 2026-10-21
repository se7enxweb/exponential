---
title: Budget lines for hosting and maintenance
audience: non-profit, businesses, project-manager, hosting
category: business-solutions
order: 40
summary: The budget lines a non-profit should plan for an Exponential website, during a grant and in the years after it - what is zero (license), what is never zero (hosting, domain, updates, backups, people's time), what is optional (partner support, translation, mail delivery, payment fees) - with no invented prices, and links to the cost pages for the arithmetic.
keywords: budget, budget lines, hosting cost, maintenance cost, grant budget, running costs, domain, backups, updates, partner support, translation, volunteer time, in-kind, treasurer
type: help
---
# Budget lines for hosting and maintenance

This page is for treasurers and grant writers. It lists the lines a website budget needs, says which of them can be
zero, and points to the pages that do the arithmetic. It names **no prices**: get quotes, and fill in the
[Grant budget worksheet](../templates/grant-budget-worksheet.md).

## Contents

1. [Lines that are zero](#lines-that-are-zero)
2. [Lines that are never zero](#lines-that-are-never-zero)
3. [Lines that depend on your choices](#lines-that-depend-on-your-choices)
4. [One-off and recurring](#one-off-and-recurring)
5. [Counting volunteer time](#counting-volunteer-time)
6. [The year after the grant](#the-year-after-the-grant)
7. [Budget checklist](#budget-checklist)

## Lines that are zero

| Line | Why it is zero | Source |
|---|---|---|
| Software license | GPL, "free of charge" | `README.md`, "License" |
| Per-editor, per-site, per-language fees | There is no license to count against | [Licensing for buyers](../../business/licensing-for-buyers.md) |
| Newsletter, forms, landing page builder | Installed with the product (`cjw_newsletter`, information collector, Exponential Layouts) | `composer.json` |
| Database server license | SQLite needs no server | [SQLite database support](../../../features/6.0/sqlite-database.md) |

## Lines that are never zero

| Line | What it covers | Read |
|---|---|---|
| Hosting | A shared account, a small virtual server or managed hosting | [Low-budget hosting](../operations/low-budget-hosting.md), [Hosting cost drivers](../../businesses/costs/hosting-cost-drivers.md) |
| Domain name | Yearly registration | your registrar |
| Updates | Applying Exponential, extension and PHP updates, tested on a copy | [Updates and upgrades](../../creators/website-creators/updates-and-upgrades.md) |
| Backups and restore tests | Storage for backups and the time to test a restore | [Backups and updates routine](../operations/backups-and-updates-routine.md) |
| A responsible person | Someone who checks the site every month | [Monthly checklist](../checklists/monthly-checklist.md) |

Even if a volunteer does the work, the time is real. Budget it, or name it as an in-kind contribution if your funder
accepts that.

## Lines that depend on your choices

| Line | Needed when | Read |
|---|---|---|
| Design and build | You do not have a skilled volunteer | [Choosing a partner](../../businesses/choosing/choosing-a-partner.md) |
| Partner support agreement | You want guaranteed response times | [Working with a partner](../operations/working-with-a-partner.md), [Support agreement template](../../customers/partners-and-hosts/support-agreement-template.md) |
| Mail delivery service | Newsletters to many subscribers | [Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md) |
| Payment provider fees | Donations through the shop or a platform | [Donation options at a glance](../donations/donation-options-at-a-glance.md) |
| Translation | More than one language | [Multilingual sites](../communications/multilingual-non-profit-sites.md) |
| Analytics service | Visitor figures beyond what Exponential counts | [Measuring outcomes with site data](measuring-outcomes-with-site-data.md) |
| Accessibility audit | An outside audit before launch | [Auditing your public design](../../government/accessibility/auditing-your-public-design.md) |
| Writing assistant provider | Exponential Intelligence with a paid provider | [Exponential Intelligence](../../customers/growth/exponential-intelligence.md) |
| Training | Editors new to the system | [Training volunteer editors](../volunteers/training-volunteer-editors.md) |

## One-off and recurring

| One-off (project) | Recurring (every year) |
|---|---|
| Discovery and planning | Hosting |
| Design and build | Domain |
| Content writing and translation | Updates and the yearly upgrade check |
| Accessibility testing | Backups and restore tests |
| Training | A responsible person's time |
| Launch | Partner support, if any |

Funders often fund the left column; your own budget must carry the right one. Say in the proposal which is which.

## Counting volunteer time

The worked example for a nonprofit shows how to count volunteer hours honestly, with example assumptions
([Worked example: a nonprofit](../../businesses/costs/worked-example-nonprofit.md)). The short version: list each
recurring task, estimate the hours a month, and multiply by a rate your funder accepts for in-kind work.

## The year after the grant

Plan at least the recurring column for the first year after the grant. Without it, the most likely failure is not a
broken site on the first day, but missed updates and untested backups over the following months; see
[Budgeting for upgrades and maintenance](../../businesses/costs/budgeting-for-upgrades-and-maintenance.md).

## Budget checklist

- [ ] License line shown as zero, with the reason.
- [ ] Hosting, domain, updates, backups and a responsible person budgeted every year.
- [ ] Optional lines decided one by one, not forgotten.
- [ ] Volunteer time counted or shown as in kind.
- [ ] The first year after the grant covered.
- [ ] Quotes attached for every line with a price.

## Related pages

- [Grants and the website](README.md)
- [Grant budget worksheet](../templates/grant-budget-worksheet.md)
- [Total cost of ownership](../../businesses/costs/total-cost-of-ownership.md)
- [What you do not pay for](../../businesses/costs/what-you-do-not-pay-for.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
