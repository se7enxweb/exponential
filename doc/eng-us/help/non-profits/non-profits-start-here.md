---
title: Non-profits: start here
audience: non-profit, businesses, government
category: non-profits
order: 1
summary: The landing page for charities, foundations, community organizations and other non-profits that run their website on Exponential - what exists for grants, donations, volunteers, accessibility, transparency, newsletters, events and languages, what does not exist and needs an outside service, how to keep costs low, and where every page of this section is.
keywords: non-profit, nonprofit, charity, foundation, NGO, community organization, start here, grants, donations, volunteers, accessibility, transparency, annual report, board minutes, newsletter, events, multilingual, low budget, SQLite, Velocity, data protection, partner
featured: true
type: help
---
# Non-profits: start here

This section is for people who run the website of a charity, a foundation, a community organization, an association
with a public mission or any other non-profit: directors, fundraisers, volunteer coordinators, communications staff,
treasurers and the volunteer or partner who looks after the technology. It explains, in plain language, what
Exponential gives a non-profit, what it does not give, what the site will cost to run and how to keep it healthy
with a small team.

> **Note**
> Every feature named in this section was checked against the Exponential repository (`composer.json`, the
> `extension/` directory, the settings) or an existing help page, and that page is linked. Where something does not
> exist, this section says so plainly. No customers, statistics or prices are invented; where a page needs numbers,
> you fill in your own.

## Contents

1. [Exponential for a non-profit in one minute](#exponential-for-a-non-profit-in-one-minute)
2. [What exists and what does not](#what-exists-and-what-does-not)
3. [How this section is organized](#how-this-section-is-organized)
4. [Reading paths by role](#reading-paths-by-role)
5. [Your first month](#your-first-month)
6. [Neighboring sections](#neighboring-sections)

## Exponential for a non-profit in one minute

- **No license fee.** "Using Exponential under the terms of the GNU GPL is free of charge" (`README.md`, "License").
  Money goes to hosting and to the work you choose to buy, not to licenses
  ([Licensing for buyers](../business/licensing-for-buyers.md)).
- **Small hosting is possible.** SQLite 3 is the default database, so a site can run without a database server
  ([SQLite database support](../../features/6.0/sqlite-database.md)), and the bundled Exponential Velocity server
  "needs nothing installed beyond PHP" (`README.md`, "Requirements").
- **Forms, newsletters and landing pages come with the product.** The information collector for forms,
  `cjw_newsletter` for supporter mail and Exponential Layouts for campaign pages are installed through
  `composer.json`.
- **Accountability is built in.** Every published change keeps its earlier versions, and a tamper-evident audit
  trail records who did what ([The audit trail](../../features/6.0/audit-trail.md)).
- **The project itself has a nonprofit home**: it is operated by the Exponential Foundation
  ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).

## What exists and what does not

| Need | What exists | Read |
|---|---|---|
| Take donations | **No dedicated donation module.** Link to an external donation platform, or sell fixed donation items in the built-in shop with `ezpaypal` or `ezauthorize` | [Donations](donations/README.md) |
| Recurring donations | Not documented for the shop; use the external platform | [Donation options at a glance](donations/donation-options-at-a-glance.md) |
| Volunteer sign-up | Information collector forms, CAPTCHA (`hcaptcha`, `recaptcha`), file uploads (`enhancedezbinaryfile`) | [Volunteers](volunteers/README.md) |
| Volunteers who edit the site | Roles, sections, approval workflow, versions, two-factor login | [Volunteers as editors](volunteers/volunteers-as-editors.md) |
| Grant reporting | No grant module; the site supplies the evidence (pages, statistics, audit trail) | [Grants](grants/README.md) |
| Annual reports, board minutes | Files as downloads, folders by year, versions | [Transparency](transparency/README.md) |
| Supporter newsletter | `cjw_newsletter` with double opt-in | [Supporter newsletter](communications/supporter-newsletter-with-cjw-newsletter.md) |
| Events | `event`, `event_calendar` and `multicalendar` classes of `ezwebin`; no ticketing, no iCal export | [Events and calendars](communications/events-and-calendars-for-non-profits.md) |
| Several languages | Up to 62 content languages per installation | [Multilingual sites](communications/multilingual-non-profit-sites.md) |
| Accessibility | Template guidance, required alternative texts, an accessible admin design | [Accessibility](accessibility/README.md) |
| Low running costs | SQLite, Velocity, no license fee | [Low-budget hosting](operations/low-budget-hosting.md) |
| Non-profit pricing in the partner program | **None published.** The partner plans have one price list | [Working with a partner](operations/working-with-a-partner.md) |

The existing [Non-profits](../businesses/nonprofits/README.md) pages in the Businesses section cover site planning,
donations, volunteers, campaigns, accessibility and low-budget operations in a compact form. This section goes
further and adds grants, transparency, operations, checklists and worksheets; it links to those pages rather than
repeating them.

## How this section is organized

| Folder | What it covers |
|---|---|
| [Grants](grants/README.md) | Grant-funded website projects, the technical part of a proposal, budget lines, outcome reports |
| [Donations](donations/README.md) | The two practical options, step by step, the giving page and donor trust |
| [Volunteers](volunteers/README.md) | Sign-up forms, spam, processing applications, member areas, volunteers who edit |
| [Accessibility](accessibility/README.md) | The basics, accessible campaigns and documents, an accessibility statement |
| [Transparency](transparency/README.md) | Annual reports, financial documents, board pages, versions and audit, open data |
| [Communications](communications/README.md) | Newsletter, events, languages, campaigns and appeals |
| [Operations](operations/README.md) | Hosting on a budget, SQLite and Velocity, data protection, backups, departures, partners |
| [Checklists](checklists/README.md) | Launch, monthly, annual and data protection checklists |
| [Templates](templates/README.md) | Worksheets to fill in: grant budget, volunteer form, roles, annual report page |

Short answers are in the [FAQ](non-profits-faq.md), the words in the [glossary](non-profits-glossary.md), and the
full order of reading in the [reading path](non-profits-reading-path.md).

## Reading paths by role

| You are | Read first |
|---|---|
| Director or board member | This page, [FAQ](non-profits-faq.md), [Transparency](transparency/README.md), [Working with a partner](operations/working-with-a-partner.md) |
| Fundraiser | [Donations](donations/README.md), [Campaigns and appeals](communications/campaigns-and-appeals.md), [Grants](grants/README.md) |
| Grant writer | [Grant-funded website project plan](grants/grant-funded-website-project-plan.md), [Budget lines](grants/budget-lines-for-hosting-and-maintenance.md) |
| Volunteer coordinator | [Volunteers](volunteers/README.md), [Donor and volunteer data protection](operations/donor-and-volunteer-data-protection.md) |
| Communications staff | [Communications](communications/README.md), [Accessibility](accessibility/README.md) |
| Treasurer | [Budget lines](grants/budget-lines-for-hosting-and-maintenance.md), [Financial documents](transparency/financial-documents.md), [Low-budget hosting](operations/low-budget-hosting.md) |
| Technical volunteer or partner | [SQLite and Velocity for small teams](operations/sqlite-and-velocity-for-small-teams.md), [Backups and updates routine](operations/backups-and-updates-routine.md) |
| Someone new to websites | [Non-technical people: start here](../non-technical-people/non-technical-people-start-here.md) |

## Your first month

| Week | Do | Read |
|---|---|---|
| 1 | Write down what the site must achieve for your mission and who maintains it | [Grant-funded website project plan](grants/grant-funded-website-project-plan.md) |
| 2 | Decide how donations and volunteer sign-ups work | [Donation options](donations/donation-options-at-a-glance.md), [Volunteer form](volunteers/volunteer-sign-up-form-step-by-step.md) |
| 3 | Give every editor an account and a role; switch on two-factor login for administrators | [Roles and permissions worksheet](templates/roles-and-permissions-worksheet.md) |
| 4 | Set up backups and a monthly routine, and test a restore | [Backups and updates routine](operations/backups-and-updates-routine.md), [Monthly checklist](checklists/monthly-checklist.md) |

![The administration dashboard of Exponential](../images/admin-dashboard.webp)

## Neighboring sections

- [Businesses: start here](../businesses/businesses-start-here.md) and its [Non-profits](../businesses/nonprofits/README.md)
  and [Small groups and associations](../businesses/associations/README.md) pages.
- [Governments: start here](../government/government-start-here.md): public bodies share many needs with non-profits
  (accessibility, transparency, records).
- [Customers: start here](../customers/customers-start-here.md): owning and running a site day to day.
- [Local businesses: start here](../local-businesses/local-businesses-start-here.md) and
  [Individuals: start here](../individuals/individuals-start-here.md).

## Related pages

- [Non-profits FAQ](non-profits-faq.md)
- [Non-profits reading path](non-profits-reading-path.md)
- [Non-profits glossary](non-profits-glossary.md)
- [Non-profits in the Businesses section](../businesses/nonprofits/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
