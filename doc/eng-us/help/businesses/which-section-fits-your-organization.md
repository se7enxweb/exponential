---
title: Which section fits your organization
audience: businesses, project-manager
category: business-solutions
order: 20
summary: A short decision guide that matches the kind of organization and the kind of web application to the right part of the Businesses section, with the questions to answer first and typical combinations.
keywords: decision guide, which section, organization type, store, intranet, extranet, member site, newsletter, library, non-profit, association, multisite, multilingual, mobile, headless, government, combinations, questions
type: help
---
# Which section fits your organization

Most organizations need more than one kind of web application: a store also sends a newsletter, an association also
has members, a brand with several sites also sells in several languages. This page helps you find the sections that
fit, in the order you will need them.

## Contents

1. [Five questions to answer first](#five-questions-to-answer-first)
2. [Match by what you do](#match-by-what-you-do)
3. [Match by who uses the site](#match-by-who-uses-the-site)
4. [Typical combinations](#typical-combinations)
5. [Sections every organization needs](#sections-every-organization-needs)
6. [When Exponential 6 is not the right product](#when-exponential-6-is-not-the-right-product)

## Five questions to answer first

Write down short answers before you read further. They decide which sections matter and how much effort each needs.

1. **Who uses the site?** The public, signed-in customers, staff, partners, members, or several of these.
2. **How does it earn money or save money?** Direct sales, leads, donations, membership fees, saved staff time,
   fewer support calls.
3. **What does an hour of unavailability cost?** Use the [cost of downtime worksheet](costs/cost-of-downtime-worksheet.md).
4. **How many sites, brands and languages?** One, or several now or later.
5. **Who will run it?** Your own staff, a hosting company, a partner, or a mix ([Choosing hosting](choosing/choosing-hosting.md)).

## Match by what you do

| You | Main section | Often also |
|---|---|---|
| Sell physical or digital products | [Online stores](stores/README.md) | [Newsletters](newsletters/README.md), [Mobile and headless](mobile-and-headless/README.md) |
| Publish a newsletter or magazine | [Newsletters](newsletters/README.md) | [Member sites](members/README.md) |
| Lend, list or archive items | [Libraries and catalogs](libraries/README.md) | [Member sites](members/README.md) |
| Raise funds and organize volunteers | [Non-profits](nonprofits/README.md) | [Newsletters](newsletters/README.md), [Small groups and associations](associations/README.md) |
| Run a club or a professional body | [Member sites](members/README.md) | [Small groups and associations](associations/README.md) |
| Inform and equip staff | [Intranets](intranets/README.md) | [Security](security/README.md) |
| Work with dealers, suppliers or customers behind a sign-in | [Extranets and partner portals](extranets/README.md) | [Intranets](intranets/README.md) |
| Run several brands or countries | [Multi-site brands](multisite/README.md) | [Multilingual businesses](multilingual/README.md) |
| Feed an app or other systems with content | [Mobile and headless](mobile-and-headless/README.md) | [Reliability](reliability/README.md) |
| Serve the public as a government body | The separate [Governments](../government/government-start-here.md) section | [Security](security/README.md) |

## Match by who uses the site

| Users | What it needs | Read |
|---|---|---|
| Anonymous visitors | Fast pages, search, good URLs | [Reliability](reliability/README.md), [Website creators](../creators/website-creators/website-creators-start-here.md) |
| Customers with an account | Registration, order history, privacy | [Customer accounts and guest checkout](stores/customer-accounts-and-guest-checkout.md), [GDPR basics](security/gdpr-basics.md) |
| Staff | Roles per department, approval, search | [Departments, roles and sections](intranets/departments-roles-and-sections.md) |
| Partners | Approved registration, separate areas | [Registration and account approval](extranets/registration-and-account-approval.md) |
| Members | Members-only content, communications | [Members-only content](members/members-only-content.md) |
| Volunteers who edit | Simple roles, handover | [Shared editing with volunteers](associations/shared-editing-with-volunteers.md) |
| Apps and other systems | A stable API | [Headless with the REST API](mobile-and-headless/headless-with-the-rest-api.md) |

The permission system behind all of these is the same: roles with policies, limited by sections and subtrees
([Roles, policies and unactivated users](../../guides/roles-and-policies.md), [Sections](../../guides/sections.md)).
That is why one installation can serve the public, staff and partners at the same time.

## Typical combinations

These are patterns, not reports about real customers.

| Pattern | Sections in order |
|---|---|
| A small store that grows | [Stores](stores/README.md), [Newsletters](newsletters/README.md), [Reliability](reliability/README.md), then [Mobile and headless](mobile-and-headless/README.md) |
| A company with a public site, an intranet and a partner portal on one installation | [Intranets](intranets/README.md), [Extranets](extranets/README.md), [Multi-site brands](multisite/README.md), [Security](security/README.md) |
| A membership organization | [Member sites](members/README.md), [Newsletters](newsletters/README.md), [Small groups and associations](associations/README.md) |
| A brand selling in several countries | [Multi-site brands](multisite/README.md), [Multilingual businesses](multilingual/README.md), [Stores](stores/README.md) |
| A library or archive with a public catalog | [Libraries and catalogs](libraries/README.md), [Reliability](reliability/README.md) |

## Sections every organization needs

Whatever you run, read these before going live:

- [Reliability](reliability/README.md): backups, monitoring, upgrades.
- [Costs](costs/README.md): what it costs to run for years.
- [Security and compliance basics](security/README.md).
- [Choosing hosting and a partner](choosing/README.md).
- [Templates and checklists](templates/README.md), in particular the [go-live checklist](templates/go-live-checklist.md).

## When Exponential 6 is not the right product

Exponential 6 is one member of a product family. The Foundation's own decision table points some needs to other
products, for example Exponential Platform DXP for a Symfony project with REST and GraphQL and no legacy kernel, or
Exponential Basic for a small, simple site to learn with ([Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you)).
If your needs match those better, read [What Exponential is: the product family](../leadership/what-exponential-is.md).

## Related pages

- [Businesses: start here](businesses-start-here.md)
- [Businesses reading path](businesses-reading-path.md)
- [Who Exponential is for](../business/who-exponential-is-for.md)
- [Planning an Exponential project](../leadership/planning-an-exponential-project.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
