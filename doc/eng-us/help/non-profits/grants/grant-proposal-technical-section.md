---
title: The technical section of a grant proposal
audience: non-profit, businesses, project-manager, sales-marketing
category: non-profits
order: 30
summary: How to write the technology part of a grant proposal for an Exponential website in plain language a reviewer understands - platform, license and ownership, hosting, accessibility, data protection, languages and sustainability - with sentences you can adapt and the source page that backs each claim.
keywords: grant proposal, technical section, platform description, open source, GPL, sustainability, hosting, accessibility, data protection, languages, reviewer, funder
type: help
---
# The technical section of a grant proposal

This page is for grant writers. Reviewers are rarely technologists; they want to know that the platform is sound, that
the organization will own what it builds, and that the site will still work after the grant. The building blocks below
are written so you can adapt them. Each one names its source, so you can show a reviewer where it comes from.

> **Tip**
> Claim only what your site will actually do. If you will not use Velocity, do not mention it. If accessibility
> testing is not in the budget, do not promise a conformance level.

## Contents

1. [Platform](#platform)
2. [License and ownership](#license-and-ownership)
3. [Hosting](#hosting)
4. [Accessibility](#accessibility)
5. [Data protection](#data-protection)
6. [Languages](#languages)
7. [Sustainability](#sustainability)
8. [What not to write](#what-not-to-write)

## Platform

> The site will be built on Exponential, a free and open-source content management system written in PHP, with
> browser-based editing, role-based permissions, content approval and scheduled publication, multi-language and
> multi-site support.

Source: `README.md`, "Main Exponential features" ("Fine grained role based permissions system", "Content approval and
scheduled publication", "Advanced multi-lingual support", "Multi-site support"). The project is led by 7x and operated
by the nonprofit Exponential Foundation ([Who is 7x?](../../who-is-7x.md),
[Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md)).

## License and ownership

> The software is licensed under the GNU General Public License, version 2 or later; there is no license fee. The
> organization owns its content, design and data, and can move them to another provider at any time.

Source: `README.md`, "License"; [Licensing for buyers](../../business/licensing-for-buyers.md);
[What you own](../../customers/what-you-own.md).

## Hosting

> The site will run on [shared hosting / a small virtual server / managed hosting] with the SQLite database, which
> needs no separate database server. Backups of the database and file storage are taken [daily] and a restore is
> tested [each quarter].

Source: [SQLite database support](../../../features/6.0/sqlite-database.md),
[Low-budget hosting](../operations/low-budget-hosting.md), [Backups and restores](../../creators/website-creators/backups-and-restores.md).
Fill in the brackets with what you will really do.

## Accessibility

> Templates will be built following the project's accessibility guidance; images require alternative text; the site
> will be tested with automated tools and by keyboard before launch, and an accessibility statement will be published.

Source: [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md),
[Testing accessibility](../../creators/website-creators/testing-accessibility.md),
[Writing an accessibility statement](../../government/accessibility/writing-an-accessibility-statement.md). Name a
target such as WCAG 2.2 AA only if testing against it is in the budget.

## Data protection

> Forms collect only the data needed; access to submissions is limited to named coordinators; submissions are
> deleted on a fixed schedule. The newsletter uses double opt-in. Administrator accounts use two-factor login. A
> tamper-evident audit trail records administrative actions.

Source: [Volunteers and sign-up forms](../../businesses/nonprofits/volunteers-and-sign-up-forms.md),
[cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md),
[Two-factor login](../../../guides/two-factor-login.md), [The audit trail](../../../features/6.0/audit-trail.md).

## Languages

> The site will be published in [languages]. The platform supports up to 62 content languages per installation, and
> right-to-left scripts.

Source: [The 62-language limit](../../government/languages/the-62-language-limit.md),
[Right-to-left sites](../../creators/website-creators/right-to-left-sites.md).

## Sustainability

> After the grant period the site will be maintained by [named role], with hosting and updates funded from [budget
> line]. Because there is no license fee, the continuing costs are hosting and staff or contractor time. Editors are
> trained during the project, and the setup, accounts and routine are documented.

Source: [Budget lines for hosting and maintenance](budget-lines-for-hosting-and-maintenance.md),
[Handing over to editors](../../creators/website-creators/handing-over-to-editors.md).

## What not to write

| Do not write | Why |
|---|---|
| "Fully accessible" or "WCAG certified" | Conformance belongs to your site, after testing, not to the software ([What Exponential provides](../../government/accessibility/what-exponential-provides.md)) |
| "Built-in donation system" | There is none ([Donations](../donations/README.md)) |
| "GDPR compliant" | Software cannot be compliant on its own; your processing is ([GDPR basics](../../businesses/security/gdpr-basics.md)) |
| Performance figures for your site | Measure your own; do not quote benchmarks as promises |
| A partner discount for non-profits | None is published ([Working with a partner](../operations/working-with-a-partner.md)) |

## Related pages

- [Grants and the website](README.md)
- [Grant-funded website project plan](grant-funded-website-project-plan.md)
- [Who Exponential is for](../../business/who-exponential-is-for.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
