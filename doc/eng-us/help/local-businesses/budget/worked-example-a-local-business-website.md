---
title: "Worked example: a local business website"
audience: local-business, businesses, customer
category: business-solutions
order: 72
summary: A three-year cost calculation for an invented local business website on Exponential - setup, hosting, updates, outside services and the owner's time - in which every number is an example assumption in neutral currency units, followed by a simple break-even check and what to change for your business.
keywords: worked example, cost calculation, example assumptions, break-even, small business, three years, hosting, setup, owner time, local business
type: help
---
# Worked example: a local business website

This page is for the owner of a small business who wants to see how a website budget is calculated. It follows an
invented business through three years.

> **Note**
> The business and every number below are **example assumptions**: round numbers in neutral currency units, chosen to
> make the arithmetic easy. They are not market prices, not prices of any provider and not results of a real business.
> Replace each one with your own quotes and figures, in the [budget worksheet](budget-worksheet.md).

## Contents

1. [The invented business](#the-invented-business)
2. [The inputs](#the-inputs)
3. [Step 1: the first year](#step-1-the-first-year)
4. [Step 2: the following years](#step-2-the-following-years)
5. [Step 3: three years in total](#step-3-three-years-in-total)
6. [Step 4: what the site must bring in](#step-4-what-the-site-must-bring-in)
7. [What to change for your business](#what-to-change-for-your-business)

## The invented business

A repair shop with one location. The site has a front page, services, opening hours with a map, a contact form with
CAPTCHA, a quote request form and a small "News" folder. No shop. One language. The owner edits texts and hours; a
local partner builds and keeps the site updated.

What it uses, all in the standard `composer.json`: `xrowmetadata`, `bcgooglesitemaps`, `ezgmaplocation` (with a Google
key) or the layouts Map block, `recaptcha` or `hcaptcha`. License fee: none (fact: GNU GPL,
[Licensing for buyers](../../business/licensing-for-buyers.md)).

## The inputs

| Input | Value |
|---|---|
| Example assumption: setup by a partner (theme adapted, classes, forms, map, sitemap) | 3,000 units, once |
| Example assumption: content, photos written and taken by the owner | 15 hours, once |
| Example assumption: domain | 20 units a year |
| Example assumption: hosting with backups and HTTPS | 30 units a month |
| Example assumption: updates and small support by the partner | 8 hours a year at 60 units an hour |
| Example assumption: map and CAPTCHA providers | 0 units (within a free allowance; check the providers' current terms) |
| Example assumption: owner's time for upkeep | 2 hours a month |
| Example assumption: value of an hour of the owner's time | 40 units |
| License fee | 0 (fact) |

## Step 1: the first year

1. Setup: **3,000**.
2. Owner's content work: 15 x 40 = **600**.
3. Domain: **20**.
4. Hosting: 30 x 12 = **360**.
5. Updates and support: 8 x 60 = **480**.
6. Outside services: **0**.
7. Owner's upkeep: 2 x 12 x 40 = **960**.
8. First year: 3,000 + 600 + 20 + 360 + 480 + 0 + 960 = **5,420**.

Of this, cash paid to others: 3,000 + 20 + 360 + 480 = **3,860**. The rest (1,560) is the owner's time.

## Step 2: the following years

1. Domain 20 + hosting 360 + updates 480 = **860** cash.
2. Owner's upkeep: **960** time.
3. Each following year: **1,820**.

## Step 3: three years in total

1. Year 1: 5,420.
2. Years 2 and 3: 1,820 x 2 = 3,640.
3. Three years: 5,420 + 3,640 = **9,060**, of which cash 3,860 + 860 x 2 = **5,580**.

Per month over three years: 9,060 / 36 = about **252**, of which cash about **155**.

## Step 4: what the site must bring in

A simple break-even check, again with example assumptions:

| Input | Value |
|---|---|
| Example assumption: average profit per new job | 60 units |
| Cost per month over three years (from step 3) | about 252 units |

New jobs a month the site must bring to pay for itself: 252 / 60 = about **4.2**, so about **5** a month.

Measure it by asking new customers how they found you ([Measuring visits](../getting-found/measuring-visits.md#ask-your-customers)).

## What to change for your business

| If | Change |
|---|---|
| You sell online | add the shop lines ([Worked example: a small store](../../businesses/costs/worked-example-small-store.md)) |
| You need two languages | add translation once and more upkeep hours |
| Your partner quotes a fixed yearly package | replace hosting and updates with that number |
| You do the updates yourself | replace partner hours with your own hours, and count learning time |
| You use a booking or ordering platform | add its fees ([What needs an outside service](what-needs-an-outside-service.md)) |

## Related pages

- [The cost picture](the-cost-picture.md)
- [ROI worksheet](../../customers/value/roi-worksheet.md)
- [ROI example assumptions](../../customers/value/roi-example-assumptions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
