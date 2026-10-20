---
title: Non-profits
audience: businesses, project-manager, editor, sales-marketing
category: business-solutions
order: 1
summary: The landing page of the non-profits section - what charities, foundations and community organizations can build with Exponential at no license cost, what exists for donations, volunteers and campaigns and what does not, the pages of the section and the order to read them.
keywords: non-profit, nonprofit, charity, foundation, NGO, community organization, donations, volunteers, campaigns, accessibility, low budget
type: help
---
# Non-profits

This section is for charities, foundations, community organizations and other non-profits that run their public
website, campaigns and volunteer recruitment on Exponential. It explains what is available for donations, volunteer
sign-up, campaigns and accessibility, how to keep running costs low, and it says plainly where you will need an outside
service. Government bodies have their own section:
[Government: start here](../../government/government-start-here.md).

## Contents

1. [Why non-profits look at Exponential](#why-non-profits-look-at-exponential)
2. [What exists and what does not](#what-exists-and-what-does-not)
3. [The pages of this section](#the-pages-of-this-section)
4. [Suggested reading order](#suggested-reading-order)
5. [Neighboring sections](#neighboring-sections)

## Why non-profits look at Exponential

- **No license fee.** "Using Exponential under the terms of the GNU GPL is free of charge" (README, "License"). Money
  goes to work and hosting you choose, not to licenses
  ([Licensing for buyers](../../business/licensing-for-buyers.md#no-license-fees-and-what-buyers-do-pay-for)).
- **Small hosting needs possible.** SQLite 3 is the first supported database and "a complete Exponential site runs from
  one file on disk, with no database server to install, configure or secure"
  ([SQLite database support](../../../features/6.0/sqlite-database.md)).
- **Forms, newsletters and landing pages are included.** The information collector, `cjw_newsletter` and Exponential
  Layouts come with the product (`composer.json`).
- **The project itself has a nonprofit home**: it is operated by the Exponential Foundation, a nonprofit
  ([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md)).

## What exists and what does not

| Need | What exists | Page |
|---|---|---|
| Take donations on the site | No dedicated donation module. Options: link to an external donation page, or sell fixed "donation" items through the built-in shop with the `ezpaypal` gateway | [Donations and fundraising pages](donations-and-fundraising-pages.md) |
| Recurring donations | Not documented for the shop or `ezpaypal`; use the external platform's recurring giving | same page |
| Volunteer sign-up | Forms with the information collector; files with `enhancedezbinaryfile`; CAPTCHA | [Volunteers and sign-up forms](volunteers-and-sign-up-forms.md) |
| Campaign pages | Exponential Layouts, with time-limited rules | [Campaigns and landing pages](campaigns-and-landing-pages.md) |
| Supporter newsletter | `cjw_newsletter` with double opt-in | [Newsletters](../newsletters/README.md) |
| Accessible site | Template guidance, required alternative texts, an accessible admin design | [Accessibility for non-profits](accessibility-for-nonprofits.md) |
| Low running cost | SQLite, Velocity without a separate web server, no license fee | [Low-budget operations](low-budget-operations.md) |

> **Note**
> Everything in this section is documented in the repository and cited on each page. Where this section says
> something is not there, a search of the code and documentation found no such feature; ask your developer to confirm
> before you plan around it.

## The pages of this section

| Page | What it answers |
|---|---|
| [Non-profit site planning](nonprofit-site-planning.md) | Goals, audiences, the pages a non-profit needs, roles and a planning worksheet |
| [Donations and fundraising pages](donations-and-fundraising-pages.md) | The honest options for taking donations, and how to build a good giving page |
| [Volunteers and sign-up forms](volunteers-and-sign-up-forms.md) | Volunteer forms, who reads them, spam protection, personal data |
| [Campaigns and landing pages](campaigns-and-landing-pages.md) | Campaign pages with Exponential Layouts, switching on and off by date, measuring |
| [Accessibility for non-profits](accessibility-for-nonprofits.md) | Why it matters, what templates and editors do, how to test |
| [Low-budget operations](low-budget-operations.md) | Keeping hosting, maintenance and volunteer time low without risking the site |
| [Non-profit checklist](nonprofit-checklist.md) | One checklist from planning to launch and the routine afterwards |

## Suggested reading order

1. [Non-profit site planning](nonprofit-site-planning.md)
2. [Low-budget operations](low-budget-operations.md)
3. [Donations and fundraising pages](donations-and-fundraising-pages.md)
4. [Volunteers and sign-up forms](volunteers-and-sign-up-forms.md)
5. [Campaigns and landing pages](campaigns-and-landing-pages.md)
6. [Accessibility for non-profits](accessibility-for-nonprofits.md)
7. [Non-profit checklist](nonprofit-checklist.md)

## Neighboring sections

- [Associations and small groups](../associations/README.md): volunteer-run clubs and groups.
- [Newsletters](../newsletters/README.md): supporter updates by mail.
- [Members](../members/README.md): member areas and registration.
- [Costs](../costs/README.md), including a [worked example for a non-profit](../costs/worked-example-nonprofit.md).
- [Security and data protection](../security/README.md): GDPR basics, consent and forms.
- [Government: start here](../../government/government-start-here.md) for public bodies.

## Related pages

- [Businesses: start here](../businesses-start-here.md)
- [Which section fits your organization?](../which-section-fits-your-organization.md)
- [What you can build](../../creators/website-creators/what-you-can-build.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
