---
title: Grants and the website
audience: non-profit, businesses, government, project-manager
category: business-solutions
order: 10
summary: How a non-profit fits its Exponential website into grant work - a website project paid for by a grant, the technical section of a proposal, honest budget lines for hosting and maintenance, and outcome reports backed by evidence the site records itself. There is no grant management module; this folder says what the site does and does not supply.
keywords: grants, grant-funded project, proposal, budget, hosting budget, maintenance budget, outcome report, evidence, funder, foundation, non-profit
type: help
---
# Grants and the website

This folder is for grant writers, directors and project leads. A website can be the subject of a grant (a new site, a
relaunch, a translation into a new language) or a tool that delivers and documents a grant-funded program. Both need
a plan, a budget that does not stop on launch day, and evidence for the final report.

> **Note**
> Exponential has **no grant management module**: no application tracker, no funder database, no reporting
> generator. A search of the code and the documentation finds none. What the site does supply is evidence: the pages
> you published, the files people downloaded, the forms they sent, the figures Exponential records and the audit
> trail. This folder shows how to use them.

## Contents

1. [The pages of this folder](#the-pages-of-this-folder)
2. [Three kinds of grant](#three-kinds-of-grant)
3. [What the site can supply as evidence](#what-the-site-can-supply-as-evidence)
4. [Where to start](#where-to-start)

## The pages of this folder

| Page | What it answers |
|---|---|
| [Grant-funded website project plan](grant-funded-website-project-plan.md) | Phases, deliverables, roles and a timeline a funder can follow |
| [The technical section of a grant proposal](grant-proposal-technical-section.md) | How to describe the platform, its license, hosting, accessibility and sustainability |
| [Budget lines for hosting and maintenance](budget-lines-for-hosting-and-maintenance.md) | Which costs to plan for, during and after the grant |
| [Grant outcomes for funders](grant-outcomes-for-funders.md) | Turning what the site records into an outcome report |
| [Measuring outcomes with site data](measuring-outcomes-with-site-data.md) | What Exponential counts itself, and what needs a service you add |

Worksheets: [Grant budget worksheet](../templates/grant-budget-worksheet.md).

## Three kinds of grant

| Kind | Example | What matters most |
|---|---|---|
| The site is the project | A relaunch, an accessible redesign, a new language | Deliverables, launch date, who maintains it afterward |
| The site delivers the project | Course materials, a resource library, an information service | Content plan, usage evidence, permanence of the material |
| The site documents the project | A project blog, events, publications | Regular publishing, archive, download figures |

Funders often ask how the work continues after the grant ends. For a site that means hosting, updates and an owner.
Exponential has no license fee to renew ([Licensing for buyers](../../business/licensing-for-buyers.md)), so the
sustainability question is mostly about people and hosting; see
[Budget lines for hosting and maintenance](budget-lines-for-hosting-and-maintenance.md).

## What the site can supply as evidence

| Evidence | Where it comes from | Source |
|---|---|---|
| Pages and publications produced | The content tree, versions with dates | [Versions of an object](../../../guides/content-history.md) |
| Who published what, and when | The audit trail (`content` channel) | [The audit trail](../../../features/6.0/audit-trail.md) |
| Sign-ups and requests received | Collected information, with CSV export | [Collected information](../../../guides/collected-information.md) |
| Newsletter reach | `cjw_newsletter` lists and the send archive | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Page views | View counts (cron part `updateviewcount`) or an analytics service | [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md) |
| What people searched for | Search statistics, off by default | same page |

## Where to start

1. Read the [Grant-funded website project plan](grant-funded-website-project-plan.md) before you write the proposal.
2. Fill in the [Grant budget worksheet](../templates/grant-budget-worksheet.md) with your own quotes.
3. Before the project starts, switch on the measurements you will need for the report
   ([Measuring outcomes with site data](measuring-outcomes-with-site-data.md)): figures that were never recorded
   cannot be reported later.

## Related pages

- [Non-profits: start here](../non-profits-start-here.md)
- [Non-profit site planning](../../businesses/nonprofits/nonprofit-site-planning.md)
- [Worked example: a nonprofit](../../businesses/costs/worked-example-nonprofit.md)
- [Project managers: start here](../../leadership/project-managers-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
