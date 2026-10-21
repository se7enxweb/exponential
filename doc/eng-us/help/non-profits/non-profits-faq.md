---
title: Non-profits FAQ
audience: non-profit, businesses, government
category: business-solutions
order: 90
summary: Short answers, each with its source, to the questions directors, fundraisers, volunteer coordinators and treasurers of non-profits ask about running their site on Exponential - cost, donations, grants, volunteers, accessibility, transparency, newsletters, events, languages, hosting, data protection and partners.
keywords: FAQ, questions, non-profit, charity, cost, license fee, donations, donate button, recurring giving, grants, volunteers, accessibility, annual report, minutes, newsletter, events, languages, hosting, SQLite, Velocity, GDPR, partner discount
type: help
---
# Non-profits FAQ

Short answers to the questions non-profits ask most. Each answer names its source and links to the page with the
details. If your question is not here, start at [Non-profits: start here](non-profits-start-here.md).

## Contents

1. [Cost and license](#cost-and-license)
2. [Donations](#donations)
3. [Grants](#grants)
4. [Volunteers](#volunteers)
5. [Accessibility and transparency](#accessibility-and-transparency)
6. [Newsletters, events and languages](#newsletters-events-and-languages)
7. [Hosting and running the site](#hosting-and-running-the-site)
8. [Data protection](#data-protection)
9. [Partners and help](#partners-and-help)

## Cost and license

**Do we pay a license fee?**
No. "Using Exponential under the terms of the GNU GPL is free of charge" (`README.md`, "License"). You pay for hosting
and for any work you buy ([Licensing for buyers](../business/licensing-for-buyers.md)).

**Is there a per-editor or per-language fee?**
No. There is no license, so there is nothing to count. Your costs are hosting, a domain, and people's time
([Budget lines for hosting and maintenance](grants/budget-lines-for-hosting-and-maintenance.md)).

**Are the newsletter, forms and landing pages extra purchases?**
No. `cjw_newsletter`, the information collector and Exponential Layouts are installed with the product through
`composer.json`.

## Donations

**Is there a donation module?**
No. A search of the code and the documentation finds no donation datatype, no "amount of your choice" field and no
tax-receipt feature ([Donations and fundraising pages](../businesses/nonprofits/donations-and-fundraising-pages.md)).

**So how do we take donations?**
Either link to an external donation platform (most small non-profits do this), or sell fixed donation items through
the built-in shop with the `ezpaypal` or `ezauthorize` payment gateway. Both are compared in
[Donation options at a glance](donations/donation-options-at-a-glance.md).

**Can donors give a monthly amount?**
Recurring payments are not documented for the shop or its gateways. Use the recurring giving of an external platform.

**Can Exponential issue tax receipts?**
No. The shop's order receipts are receipts for an order, not tax or donation receipts
([Order receipts](../../features/6.0/order-receipts.md)). Ask your accountant what your country requires.

## Grants

**Can Exponential manage our grant applications?**
No, there is no grant management module. The site helps with what funders ask for afterward: evidence of outputs
(pages, publications, events), usage figures and a record of who did what. See [Grants](grants/README.md).

**Can we put the website itself into a grant budget?**
Many funders allow it; that depends on the funder. [Budget lines for hosting and maintenance](grants/budget-lines-for-hosting-and-maintenance.md)
lists the lines to price.

## Volunteers

**Can volunteers sign up on the site?**
Yes, with a form built on the information collector, protected by a form token and a CAPTCHA
([Volunteer sign-up form, step by step](volunteers/volunteer-sign-up-form-step-by-step.md)).

**Can volunteers edit the site without being able to break it?**
Yes: one account per person, a role limited to a section, optional approval before publishing, versions to undo, and
the audit trail ([Volunteers as editors](volunteers/volunteers-as-editors.md)).

**What happens when a volunteer leaves?**
You deactivate the account and move it out of the editors' group; their content stays
([When volunteers leave](operations/when-volunteers-leave.md)).

## Accessibility and transparency

**Is Exponential accessible?**
The software gives you the means (template guidance, required alternative texts, an accessible admin design); whether
your public site is accessible depends on its design and content
([What Exponential provides for accessibility](../government/accessibility/what-exponential-provides.md)).

**Can we publish our annual report and financial statements?**
Yes, as file downloads in a dated structure ([Annual publications as downloads](transparency/annual-publications-as-downloads.md)).

**Can we show what changed on a page and who changed it?**
Every object keeps its versions (45 by default), and the audit trail records changes
([Versions and the audit trail](transparency/versions-and-the-audit-trail.md)).

## Newsletters, events and languages

**Is there a newsletter?**
Yes, `cjw_newsletter`, with double opt-in, sending by cron and bounce handling
([Supporter newsletter](communications/supporter-newsletter-with-cjw-newsletter.md)).

**Is there an event calendar?**
Yes. `ezwebin` ships the `event`, `event_calendar` and `multicalendar` classes. There is no ticketing, no seat limit
and no iCal export ([Events and calendars](communications/events-and-calendars-for-non-profits.md)).

**How many languages can one site have?**
Up to 62 content languages per installation on 64-bit PHP
([The 62-language limit](../government/languages/the-62-language-limit.md)).

## Hosting and running the site

**What is the smallest hosting that works?**
Shared hosting with Apache or LiteSpeed can run a small site with SQLite; a small virtual server gives more control
([Low-budget hosting](operations/low-budget-hosting.md)).

**Do we need a database server?**
Not with SQLite, the default ([SQLite database support](../../features/6.0/sqlite-database.md)). With many editors
publishing at once, choose a database server instead.

**What is Velocity?**
The web server bundled with Exponential, which "needs nothing installed beyond PHP" (`README.md`). It usually does
not run on shared hosting ([SQLite and Velocity for small teams](operations/sqlite-and-velocity-for-small-teams.md)).

## Data protection

**Where is donor and volunteer data stored?**
In the form submissions, user accounts, orders and newsletter subscriptions of your installation, and in your
exports and backups ([Personal data in Exponential](../businesses/security/personal-data-in-exponential.md)).

**Who can read volunteer applications?**
Only people with the `infocollector/read` permission. Give it to coordinators, not to every editor
([Donor and volunteer data protection](operations/donor-and-volunteer-data-protection.md)).

## Partners and help

**Is there special partner pricing for non-profits?**
No. The partner program publishes one set of plans and prices and names no non-profit discount
([Partner plans and prices](../partners/partner-plans-and-prices.md)). Ask a partner about their own terms.

**Where do we get help?**
From a partner, from your hosting company, or from the project's public channels
([Working with a partner](operations/working-with-a-partner.md), [Reporting problems](../customers/healthy/reporting-problems.md)).

## Related pages

- [Non-profits: start here](non-profits-start-here.md)
- [Non-profits glossary](non-profits-glossary.md)
- [Businesses FAQ](../businesses/businesses-faq.md)
- [Customers FAQ](../customers/customers-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
