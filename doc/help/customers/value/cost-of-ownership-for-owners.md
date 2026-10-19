---
title: Cost of ownership for owners
audience: customer, businesses, project-manager
category: customer-success
order: 40
summary: Tracking what a running Exponential site really costs - the cost lines an owner pays after launch, where each one is billed, a yearly cost register, the costs that creep in unnoticed, and the questions that keep invoices honest.
keywords: cost of ownership, running costs, TCO, hosting costs, maintenance costs, updates, staff time, third-party services, cost register, budget, invoices
type: help
---
# Cost of ownership for owners

This page is for the owner or manager who pays the bills of a site that is already running. Before launch, total
cost of ownership is an estimate ([Total cost of ownership](../../businesses/costs/total-cost-of-ownership.md)). After
launch it is a record: what you actually pay, to whom, and for what. This page helps you keep that record and spot
costs that grow without anyone deciding it.

## The cost lines after launch

| Line | Billed by | How often | Note |
|---|---|---|---|
| License for Exponential | Nobody | Never | "Using Exponential under the terms of the GNU GPL is free of charge." (`README.md`, "License") |
| Hosting | Your host | Monthly or yearly | Servers or a plan, backups, monitoring ([Hosting cost drivers](../../businesses/costs/hosting-cost-drivers.md)) |
| Updates to new 6.0.x releases | Partner, or own staff | Several times a year | Fixes come with the next 6.0.x release (`SECURITY.md`) |
| Extension, PHP and server updates | Partner or host | As they are published | ([Budgeting for upgrades and maintenance](../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)) |
| Support and changes | Partner | Contract or per request | A warranty or guaranteed response time is something a contract adds; the GPL has none (`LICENSE.md`, sections 11 and 12) |
| Content work | Own staff | Ongoing | Usually the largest line on any platform |
| Third-party services | Each provider | Monthly or per use | Payment fees, mail delivery, CAPTCHA, a hosted AI provider if you use one |
| Training new staff | Own staff or partner | On staff change | Editors start with [Creating and editing content](../../editing-content.md) |
| Voluntary project support | The project | If you choose | Sponsorship and donations are optional (`README.md`) |

## A yearly cost register

Keep one row per invoice line or recurring cost. Copy:

```text
COST REGISTER - [site]     Year: ______     Kept by: ______

Line                        | Supplier | Contract or quote | Per month | Per year | Notes
----------------------------|----------|-------------------|-----------|----------|------
Hosting                     |          |                   |           |          |
Partner support             |          |                   |           |          |
Updates and upgrades        |          |                   |           |          |
Changes and new features    |          |                   |           |          |
Payment provider fees       |          |                   |           |          |
Mail delivery               |          |                   |           |          |
AI provider (if used)       |          |                   |           |          |
Other services              |          |                   |           |          |
Own staff time (estimate)   |          |                   |           |          |
License for Exponential     | none     | README "License"  | 0         | 0        |
TOTAL                       |          |                   |           |          |
```

## Costs that creep in

| Cost | How it grows | What to do |
|---|---|---|
| Hosting | Disk fills with media, logs and backups | Ask the host for usage each quarter; tidy unused media ([The content checks](../../../features/6.0/content-checks.md)) |
| AI provider | Editors use the assistant more | Exponential Intelligence has a usage log with the estimated cost of each request, and limits per user, day, month and site (`README.md`, "Exponential Intelligence") |
| Payment fees | Grow with sales, and with provider price changes | Compare statements yearly |
| Change requests | Many small requests, each billed separately | Collect them and ask for one quote a quarter |
| Skipped updates | An update skipped for a year becomes a larger job | Keep updates in the plan ([Budgeting for upgrades and maintenance](../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)) |
| Staff time | Manual routines that a feature could do | See [Features customers often miss](features-customers-often-miss.md) |

## Questions that keep invoices honest

For your partner:

- Which hours were spent on updates, which on changes, which on fixing problems?
- Which recurring tasks could we do ourselves, with the help pages?
- What would make next year's updates smaller?

For your host:

- What does our plan include: backups, monitoring, restore tests, mail delivery?
- How much disk and traffic do we use, and how close are we to the next price step?
- Which costs would change if we served the site with Velocity, which needs nothing installed beyond PHP
  (`README.md`, "Requirements")?

## When to look again

- Every invoice: does it match the register?
- Every quarter: hosting usage, AI usage if any, open change requests.
- Every year: the full register, in the [annual value review](annual-value-review.md).

## Related pages

- [Total cost of ownership](../../businesses/costs/total-cost-of-ownership.md)
- [What you do not pay for](../../businesses/costs/what-you-do-not-pay-for.md)
- [Budgeting for upgrades and maintenance](../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)
- [Measuring ROI](measuring-roi.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
