---
title: Who Exponential is for: personas and industries
audience: sales-marketing, project-manager
category: business
order: 30
summary: The organizations, industries and people Exponential serves according to its own sources, as personas with their needs, the product of the family that fits each, qualification questions, and the signals that another product or more preparation is needed.
keywords: personas, target audience, ideal customer, industries, segments, qualification, discovery, publishers, media, intranet, extranet, e-commerce, agencies, education, public sector, migration, eZ Publish sites, buyer, editor, administrator, developer
type: help
---
# Who Exponential is for: personas and industries

This page is for sales and marketing people who decide whom to approach, and for project managers who check whether a
project fits the product. It answers: which kinds of organizations and which people Exponential is made for, according
to its own sources; which product of the Exponential family fits each; and which questions tell you whether a
prospect is a good fit.

The sources describe their audiences in general terms. They do **not** publish customer lists, market shares or
statistics about who uses the product, and neither does this page. The personas below are working models built from
what the sources say the product does and whom they say it serves. Test them against your own conversations.

## Contents

1. [What the sources say about the audience](#what-the-sources-say-about-the-audience)
2. [Industries and uses](#industries-and-uses)
3. [Which product of the family fits](#which-product-of-the-family-fits)
4. [Buyer personas](#buyer-personas)
5. [User personas inside the customer](#user-personas-inside-the-customer)
6. [Qualification questions](#qualification-questions)
7. [Signals to slow down](#signals-to-slow-down)
8. [A persona card to copy](#a-persona-card-to-copy)

## What the sources say about the audience

| Who | Quote | Source |
|---|---|---|
| Uses the product suits | "functionality for web publishing, media portals, intranets, e-commerce and extranets" | exponential.earth, quoted on [Who is 7x?](../who-is-7x.md#the-product-what-exponential-is) |
| Organizations (Exponential 6) | "Organizations that run structured content, several sites or several languages from one installation, and developers who build applications on its framework." | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) |
| People the Foundation serves | "end users, designers, developers, coders, no-code developers, system administrators, programmers and website builders" | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#what-the-foundation-is) |
| Developers | "It is also suitable as a platform for general PHP development, allowing you to develop professional Internet applications, fast." | `README.md` |
| Organizations (Exponential Platform DXP) | "publishers, associations, universities, public bodies and companies that run several sites, brands or languages from one content repository" | `README.md`, "What is Exponential Platform DXP?", "Who it is for" |
| Teams leaving other platforms (DXP) | "Teams leaving eZ Platform or Ibexa OSS who want to stay on the same content model" | same |
| Writers and managers (Exponential Intelligence) | "Great for project managers and content writers!" | repository description, quoted on [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-intelligence) |
| Learners and small sites (Exponential Basic) | "for everyone, educational purposes, personal use and worldwide general" | repository description, quoted on [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-basic) |
| Owners of older sites | 7x offers "CMS Upgrades: Migration from eZ Publish to Exponential v6" | [Who is 7x?](../who-is-7x.md#services-and-hosting) |

> **Note**
> The DXP quote describes **Exponential Platform DXP**, the Symfony-only member of the family, not the Exponential 6
> kernel this help belongs to. Use it when you talk about the family, and say which product you mean
> ([What Exponential is: the product family](../leadership/what-exponential-is.md)).

## Industries and uses

The table pairs each use named by the sources with the documented features that serve it. It names no customers.

| Industry or use | Typical needs | Exponential features that answer them | Pages to show |
|---|---|---|---|
| **Web publishing and media portals** (publishers, news, magazines) | Structured articles, many authors, approval, feeds, many sections | User-defined content classes; version control; content approval and scheduled publication; RSS, ATOM, iTunes and OPML feeds (`README.md`, "Main Exponential features") | [The content model and editing content](../../guides/content-model-and-editing.md), [Workflows](../../guides/workflows.md), [RSS feeds](../../guides/rss-feeds.md) |
| **Intranets** | Signed-in users, fine-grained rights per department, search | Role-based permissions with section and subtree limitations; built-in search engine | [Roles, policies and unactivated users](../../guides/roles-and-policies.md), [Sections](../../guides/sections.md), [Searching](../searching.md) |
| **Extranets** (partners, members, customers) | Registration, approval of accounts, separate areas | User registration with administrator approval; sections; siteaccesses | [Roles, policies and unactivated users](../../guides/roles-and-policies.md), [Siteaccess host and language matching](../../features/6.0/siteaccess-host-and-language-matching.md) |
| **E-commerce** | Products, baskets, orders, VAT, currencies, payments | "Advanced e-Commerce Stores (Built in)"; PayPal through `ezpaypal` | [Order receipts](../../features/6.0/order-receipts.md), [Store dashboard](../../features/6.0/store-dashboard.md), [Who is 7x?](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one) |
| **Multi-brand and multi-country organizations** | Several sites, several languages, shared content | Multisite from one installation; 268 locale files; right to left | [Planning multisite and multilingual projects](../leadership/multisite-and-multilingual-planning.md), [Languages in Exponential](../../languages.md) |
| **Associations, education, public bodies** | Long-lived sites, many editors, accessibility, data protection questions | Roles and workflows; admin4 design with tested contrast and target sizes; audit trail | [Accessibility, security and compliance checklists](../leadership/accessibility-security-and-compliance-checklists.md) |
| **Agencies and integrators** | A platform to build many customer sites on, without license fees | GPL license; extensions; Git clones; the Nexus distribution with demo content and layouts | [Develop with Git](../develop-with-git.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-nexus) |
| **Custom web applications** | A framework with content, users and permissions already built | MVC, modules and views, templates, REST API, CLI scripts | [Templates and design](../../guides/templates-and-design.md), [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) |
| **Sites built in the eZ Publish years** | Keep content and investment; move to current PHP | Migration chapters for 4.x, 5.x and eZ Platform or Ibexa sites | [14. Migrating from the 4.x line](../../install/14-migrating-from-4x.md), [15. Migrating from the 5.x legacy stack](../../install/15-migrating-from-5x-legacy.md), [16. Migrating from eZ Platform and Ibexa](../../install/16-migrating-from-ez-platform-and-ibexa.md) |
| **Learning, personal and small sites** | Something simple to start with | Exponential 6 with SQLite and no database server; or Exponential Basic | [Getting started](../getting-started.md), <https://basic.exponential.earth> |

## Which product of the family fits

The Foundation's own decision table, in short ([Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you)):

| The customer wants | Offer | Read |
|---|---|---|
| A CMS with a shop, multisite and many languages, on PHP only, no database server needed | Exponential 6 with SQLite | [Getting started](../getting-started.md), [Database support](../database-support.md) |
| The fastest way to serve it | Exponential Velocity | [Persistent-worker server](../../features/6.0/velocity-persistent-worker-server.md) |
| Editors who build pages themselves | Exponential Layouts | [The layouts editor](../layouts-editor.md) |
| Help writing and translating content | Exponential Intelligence | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| A Symfony project with REST and GraphQL, no legacy kernel | Exponential Platform DXP | [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md) |
| A complete demo site with layouts on the Symfony platform | Exponential Platform Nexus | [Nexus starter](../../features/6.0/platform-nexus-starter.md) |
| To keep an Exponential 6 site and add Symfony next to it | Exponential Platform Legacy | [Legacy bridge](../../features/6.0/legacy-bridge.md) |
| A small, simple CMS to learn with | Exponential Basic | <https://basic.exponential.earth> |

## Buyer personas

Buyers are the people who decide or influence the decision. Each persona lists what they care about, what to show and
what to send. The quotes in "What they care about" are typical concerns, not quotes from real people.

### 1. The digital lead

| | |
|---|---|
| **Role** | Head of digital, web or communications |
| **Situation** | Runs one or more websites; editors wait for developers; languages and brands multiply |
| **What they care about** | Speed of change, editor independence, many sites and languages, total cost |
| **Lead with** | Editors build pages with Exponential Layouts; several sites and 268 locales from one installation; no license fee |
| **Show** | [Demo scripts](demo-scripts.md), the 15-minute script: dashboard, editing, layouts editor, languages |
| **Send** | [Feature highlights mapped to customer needs](feature-highlights-for-customers.md), [Positioning and value proposition](positioning-and-value-proposition.md) |

### 2. The IT and operations manager

| | |
|---|---|
| **Role** | Head of IT, infrastructure or hosting |
| **Situation** | Has standard operating systems, a preferred database, security rules, an upgrade calendar |
| **What they care about** | Where it runs, which database, how it is patched, how it is secured and monitored |
| **Lead with** | Almost any OS with PHP 8.0 or later; six databases; any common web server or Velocity; a clear release and security policy |
| **Show** | **Setup > System information**, **Setup > Log files**, the **Audit** tab |
| **Send** | [Operating system support](../os-support.md), [Database support](../database-support.md), [Security hardening](../security-hardening.md), [Choosing hosting, database and operating system](../leadership/choosing-hosting-database-and-os.md) |

### 3. The security and compliance officer

| | |
|---|---|
| **Role** | Information security officer, data protection officer, auditor |
| **Situation** | Must approve new systems; asks for evidence |
| **What they care about** | Access control, audit, vulnerability handling, data flows (also of the writing assistant) |
| **Lead with** | Roles and policies; the tamper-evident audit trail; the Content Security Policy; private vulnerability reporting; Exponential Intelligence's data policy and its printable summary for a data protection officer (`README.md`, "Safety") |
| **Show** | The **Audit** tab; a role page with its policies as sentences |
| **Send** | [The audit trail](../../features/6.0/audit-trail.md), [Content Security Policy](../../features/6.0/content-security-policy.md), [Accessibility, security and compliance checklists](../leadership/accessibility-security-and-compliance-checklists.md), `SECURITY.md` |

### 4. The owner of an older site

| | |
|---|---|
| **Role** | Site owner or IT lead of a site built while the software was called eZ Publish, or on eZ Platform or Ibexa |
| **Situation** | The site works but runs on old PHP or an unmaintained version |
| **What they care about** | Keeping the content and the investment; risk of the move; who maintains it afterwards |
| **Lead with** | Exponential continues that code base; documented migration chapters; regular 6.0.x releases since January 2024 |
| **Show** | The release list on [Who is 7x?](../who-is-7x.md#the-60x-releases); the migration chapter that matches their version |
| **Send** | [Upgrading](../../guides/upgrading.md), installation chapters [14](../../install/14-migrating-from-4x.md), [15](../../install/15-migrating-from-5x-legacy.md), [16](../../install/16-migrating-from-ez-platform-and-ibexa.md), [Handling objections](objection-handling.md) |

### 5. The agency owner or integrator

| | |
|---|---|
| **Role** | Runs a web agency or a system integrator |
| **Situation** | Builds and maintains sites for many clients |
| **What they care about** | No license fees per client, reuse across projects, a community, the ability to fix things themselves |
| **Lead with** | GPL license; extensions and Composer packages; Git clones with full history; pull requests are welcome |
| **Show** | The extension list ([The default extension distribution](../../features/6.0/default-extension-distribution.md)); [Develop with Git](../develop-with-git.md) |
| **Send** | [The community and ecosystem as a selling point](community-and-ecosystem.md), [Licensing explained for buyers](licensing-for-buyers.md) |

### 6. The procurement or finance contact

| | |
|---|---|
| **Role** | Buyer, finance, legal |
| **What they care about** | License terms, total cost, exit options |
| **Lead with** | Free of charge under the GPL; no Professional License any more; services are bought separately and from whom the customer chooses |
| **Send** | [Licensing explained for buyers](licensing-for-buyers.md) (not legal advice; their legal team decides) |

## User personas inside the customer

These are the people who will work with Exponential every day. Winning them over in a demo often decides the sale.

| Persona | Daily work | What matters to them | Where it is in the product | Help page |
|---|---|---|---|---|
| **Editor** | Writes, edits, translates, publishes | A clear dashboard, drafts, preview, undo | **Dashboard**, **Content structure**, versions | [Getting started](../getting-started.md), [Publishing, versions and the trash](../publishing-and-versions.md) |
| **Page builder or designer** | Arranges pages and campaigns | Building pages without code | **Layouts** tab | [The layouts editor](../layouts-editor.md) |
| **Translator** | Translates content | Side-by-side translations; their own interface language | Translations tab; interface language | [Languages](../languages.md) |
| **Content writer** | Drafts new content | Help with first drafts, summaries, alt text | **Intelligence** tab, edit form actions | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| **Administrator** | Users, rights, settings, logs | Control and overview | **Users**, **Setup**, **Audit** | [Roles, policies and unactivated users](../../guides/roles-and-policies.md), [Operating a site](../../guides/operating-a-site.md) |
| **Developer** | Builds features and integrations | Git, Composer, templates, extensions, APIs | Code and settings | [Develop with Git](../develop-with-git.md), [Extensions](../../guides/extensions.md) |
| **Keyboard and assistive technology user** | Any of the above | Working without a mouse; readable contrast | Keyboard shortcuts; admin4 design | [Keyboard shortcuts](../keyboard-shortcuts.md), [The admin4 design](../../features/6.0/admin4-design.md) |

## Qualification questions

Ask these in discovery. The right column says which answer points to a good fit and where to look next.

| Question | A good fit when | Look next |
|---|---|---|
| What content do you publish, and does it have a structure (articles, products, events, people)? | Structured content of several kinds | [Running content modeling workshops](../leadership/content-modeling-workshops.md) |
| How many sites, brands and languages, now and in two years? | More than one of any | [Planning multisite and multilingual projects](../leadership/multisite-and-multilingual-planning.md) |
| Who changes pages today, and how long does it take? | Editors wait for developers | [The layouts editor](../layouts-editor.md) |
| Do you sell online, or plan to? | Yes, close to the content | [Feature highlights](feature-highlights-for-customers.md) |
| Where must the site run: own servers, a cloud, a hosting partner? | They want the choice, or have a fixed standard | [Choosing hosting, database and operating system](../leadership/choosing-hosting-database-and-os.md) |
| Which database and operating system does your IT run? | One of the six databases; an OS with PHP 8.0 or later | [Database support](../database-support.md), [Operating system support](../os-support.md) |
| How important are license costs and the freedom to change provider? | Important | [Licensing explained for buyers](licensing-for-buyers.md) |
| Do you run a site built while the software was called eZ Publish, or on eZ Platform or Ibexa? | Yes | [Upgrading](../../guides/upgrading.md), migration chapters 14 to 16 |
| Who will run the site after launch: your IT, an agency, a hosting company? | Someone is named | [Working together](../leadership/working-together.md) |
| What do your security and data protection officers require? | Requirements can be listed | [Accessibility, security and compliance checklists](../leadership/accessibility-security-and-compliance-checklists.md) |
| Do your developers know PHP and Git, or will a partner build it? | Either, as long as it is decided | [Estimating and scoping with extensions](../leadership/estimating-and-scoping-with-extensions.md) |

## Signals to slow down

Not every prospect is ready, and saying so builds trust. These signals do not rule a customer out; they mean the
question must be answered before you promise anything.

| Signal | Why it matters | What to do |
|---|---|---|
| "We want a service where we never think about servers." | Exponential is software that is installed; someone has to host and run it | Name who will host it: the customer's IT, a partner, or a managed hosting service such as 7x offers ([Services and hosting](../who-is-7x.md#services-and-hosting)) |
| "We only have shared hosting." | Velocity needs a long-lived process; "Shared hosting usually will not" allow one (`README.md`) | Plan Apache or LiteSpeed with `.htaccess`, as the README advises |
| "We need features X, Y and Z" that no help page describes | Nothing undocumented may be promised | Hand it to the project manager as a scoping question ([Estimating and scoping with extensions](../leadership/estimating-and-scoping-with-extensions.md)) |
| "We need a certification or a guaranteed support time." | The sources name no certifications; `SECURITY.md` promises only a first answer to security reports within one business day | Say what the sources say; service levels are a matter of your own contract |
| "We need Microsoft SQL Server." | It is "known to be possible with additional work", not supported today (`README.md`) | Read [Database support](../database-support.md) together with their IT |
| "Our site runs on PHP 7." | PHP 7.4 is supported only up to 6.0.7 (`README.md`) | Plan the PHP upgrade as part of the project |
| "We want a Symfony-only stack." | The Exponential 6 kernel is not Symfony | Look at Exponential Platform DXP ([Which product of the family fits](#which-product-of-the-family-fits)) |

## A persona card to copy

```text
PERSONA CARD

Name of the persona:   <e.g. "The digital lead at a regional publisher">
Organization type:     <industry, size, number of sites and languages>
Role and situation:    <what they are responsible for, what is in the way>
Jobs to be done:       1. <job>  2. <job>  3. <job>
Pains:                 1. <pain> 2. <pain> 3. <pain>
Gains hoped for:       1. <gain> 2. <gain> 3. <gain>
Exponential answers:   <feature> -> <help page>
                       <feature> -> <help page>
                       <feature> -> <help page>
Product of the family: <Exponential 6 | Platform Legacy | Nexus | DXP | Basic>
Demo to give:          <5 | 15 | 45 minute script, which parts>
Qualification answers: <short notes, no personal data>
Open questions:        <for the project manager>
```

## Related pages

- [Sales and marketing: start here](sales-and-marketing-start-here.md)
- [Positioning and value proposition](positioning-and-value-proposition.md)
- [Feature highlights mapped to customer needs](feature-highlights-for-customers.md)
- [Demo scripts](demo-scripts.md)
- [What Exponential is: the product family](../leadership/what-exponential-is.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
