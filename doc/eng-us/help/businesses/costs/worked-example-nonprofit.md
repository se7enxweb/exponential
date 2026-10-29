---
title: "Worked example: a nonprofit"
audience: businesses, project-manager, website-creator
category: business-solutions
order: 60
summary: A step-by-step cost and benefit calculation for a small nonprofit site run partly by volunteers, with online donations and a newsletter, using only labeled example assumptions; shows how to count volunteer time and keep running costs low.
keywords: nonprofit, charity, association, worked example, donations, volunteers, low budget, TCO, ROI, SQLite, newsletter, hosting
type: help
---
# Worked example: a nonprofit

This page is for boards, treasurers and coordinators of nonprofits who must decide whether a website is worth the
money and the volunteer time. It works through a small, invented organization step by step: what the site costs,
what it brings in, and how to count the hours volunteers give.

> **Note**
> The organization and every figure below are **Example assumptions**: round numbers in a neutral currency unit.
> They are not fundraising statistics, not prices and not results of any real organization. Replace each one with
> your own figures.

## Contents

1. [What keeps the costs low](#what-keeps-the-costs-low)
2. [The inputs](#the-inputs)
3. [Step 1: the cash costs](#step-1-the-cash-costs)
4. [Step 2: the donations the site adds](#step-2-the-donations-the-site-adds)
5. [Step 3: ROI](#step-3-roi)
6. [Step 4: counting volunteer time](#step-4-counting-volunteer-time)
7. [What to change for your organization](#what-to-change-for-your-organization)

## What keeps the costs low

| Fact | Effect on the budget | Source |
|---|---|---|
| No license fee | No subscription to renew from a tight budget | `README.md`, "License" |
| SQLite 3 is the default and needs no database server | A small hosting plan or one small server is enough for one site on one machine | `README.md`, "Requirements"; [Database support](../../database-support.md) |
| Runs on almost any operating system with PHP 7.4 or later | Wide choice of hosts | [Operating system support](../../os-support.md) |
| Newsletters through `cjw_newsletter`, required by `composer.json` | No separate newsletter product needed | [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md) |
| Shared editing with roles and approval | Volunteers can edit without full access | [Roles and policies](../../../guides/roles-and-policies.md) |
| Free documentation under the GNU FDL | Training material you may copy and adapt | [Free documentation](../../free-documentation.md) |

> **Note**
> The repository has no dedicated donation module. A donation page is built with the built-in shop and a payment
> gateway, or with a link to your payment provider's own donation page; see
> [Donations and fundraising pages](../nonprofits/donations-and-fundraising-pages.md). The provider's fees apply.

## The inputs

| Input | Value |
|---|---|
| Example assumption: setup by a freelancer or partner | 3,000 currency units, once |
| Example assumption: hosting | 20 currency units a month |
| Example assumption: paid help for updates | 8 hours a year at 50 currency units an hour |
| Example assumption: online donations | 30 a month |
| Example assumption: average donation | 25 currency units |
| Example assumption: payment provider fees | 3 % of donations |
| Example assumption: share of online donations that would not have come in otherwise | 50 % |
| Example assumption: volunteer editing time | 4 hours a week, 48 weeks a year |
| License fee | 0 (fact) |

## Step 1: the cash costs

1. Setup: **3,000**.
2. Hosting per year: 20 x 12 = **240**.
3. Paid help per year: 8 x 50 = **400**.
4. Running cost per year: 240 + 400 = **640**.
5. First year: 3,000 + 640 = **3,640**.
6. Five years: 3,000 + (640 x 5) = 3,000 + 3,200 = **6,200**.

## Step 2: the donations the site adds

1. Donations per year: 30 x 25 x 12 = **9,000**.
2. Payment fees: 9,000 x 3 % = **270**.
3. After fees: 9,000 - 270 = **8,730**.
4. Only the new share counts: 8,730 x 50 % = **4,365** a year.

## Step 3: ROI

First year:

1. Net benefit: 4,365 - 3,640 = **725**.
2. ROI: 725 / 3,640 x 100 % = about **20 %**.

Five years:

1. Benefits: 4,365 x 5 = **21,825**.
2. Costs: **6,200**.
3. Net benefit: 21,825 - 6,200 = **15,625**.
4. ROI: 15,625 / 6,200 x 100 % = about **252 %**.

## Step 4: counting volunteer time

Volunteer hours cost no cash, but they are not free: they could go to other work. Show them next to the cash figures,
not inside them.

1. Hours per year: 4 x 48 = **192**.
2. Example assumption: a shadow value of 20 currency units an hour.
3. Shadow value: 192 x 20 = **3,840** a year.

Present it as: "The site costs 640 in cash a year and about 192 volunteer hours." The board can then decide whether
those hours are well spent. Shared editing with clear roles and a written handover keep the hours down
([Handover when volunteers change](../associations/handover-when-volunteers-change.md)).

## What to change for your organization

- [ ] Your donation figures from last year's accounts, and a realistic share that the site adds.
- [ ] Your payment provider's fee structure from its contract.
- [ ] Any grant or in-kind hosting you receive; ask hosts and partners whether they offer it.
- [ ] Accessibility work, which a nonprofit often must provide ([Accessibility for nonprofits](../nonprofits/accessibility-for-nonprofits.md)).
- [ ] Data protection basics for donor and member data ([Security and data protection](../security/README.md)).
- [ ] A plan for the low-budget years: [Low-budget operations](../nonprofits/low-budget-operations.md).

## Related pages

- [Nonprofits](../nonprofits/README.md)
- [Nonprofit site planning](../nonprofits/nonprofit-site-planning.md)
- [What you do not pay for](what-you-do-not-pay-for.md)
- [ROI worksheet template](../templates/roi-worksheet-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
