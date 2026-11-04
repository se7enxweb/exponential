# Exponential Competition

This book compares Exponential with the other ways to run a website today: proprietary digital experience platforms
(DXPs) and software as a service (SaaS), the PHP content management systems and frameworks, and the systems written in
every other language. It is written for one reader in particular: an organization or a person **without a budget for
the website** who still needs it to do modern things, to keep working for many years, and to be bent to almost any
purpose at **zero marginal cost**.

Each page names the products of its group, says what each one is, how it is licensed and paid for, what it does well,
where it fails that reader in the long run, and what Exponential does instead. Every claim about another product is
taken from the product's own documentation, license or pricing page at the time of writing (November 2026), and each
page lists its sources. Prices and plans change; check the source before deciding.

## Contents

- [1. Who this is for](#1-who-this-is-for)
- [2. The criteria](#2-the-criteria)
- [3. Free software values](#3-free-software-values)
- [4. The summary matrix](#4-the-summary-matrix)
- [5. The pages of this book](#5-the-pages-of-this-book)
- [6. How to read this book fairly](#6-how-to-read-this-book-fairly)
- [7. What Exponential brings](#7-what-exponential-brings)
- [Sources](#sources)

## 1. Who this is for

- **No funding, real needs.** A club, a school, a nonprofit, a small business, a public office or a single developer
  who needs a site with content types, several languages, editors with permissions, search and integrations, but
  cannot pay a license, a subscription per seat or a metered API bill.
- **Long life.** A site that must still run, be updated and stay secure in ten or twenty years, without a forced
  re-platforming because a vendor changed its plans, its prices or its product.
- **Freedom to change anything.** The right to read, change and redistribute the code, to host it anywhere, and to
  take every byte of content and data along when leaving.

A well funded team with different priorities may weigh the same facts differently. The criteria below make the
comparison checkable either way.

## 2. The criteria

| Criterion | The question asked of every product |
|---|---|
| **Cost at zero funding** | Can it run in production, with all its features, for the cost of a server alone? |
| **License freedom** | May anyone use, study, change and redistribute it (the four freedoms)? |
| **Lock-in and exit** | Can all content, structure and data be exported and run elsewhere without the vendor? |
| **Metering** | Do seats, records, API calls, bandwidth, page views or "AI credits" decide the price? |
| **Hosting needs** | Does it run on ordinary PHP hosting, on a single small server, on SQLite, or only in the vendor's cloud? |
| **Upgrade stability** | Has its line been kept upgradable for 10 to 20 years, or did it break or end its versions? |
| **Extensibility** | Can new content types, modules, views, workflows and integrations be added without permission or fees? |
| **Multi-site and languages** | Are several sites and many languages part of the product, or paid add-ons? |
| **Security record** | Are fixes public, and can the owner apply them without a vendor? |
| **Data ownership** | Who holds the database, the files, the logs and the audit trail? |

## 3. Free software values

Free software is defined by four freedoms: to run the program for any purpose, to study and change it, to
redistribute copies, and to distribute changed versions ([What is free software?](https://www.gnu.org/philosophy/free-sw.html),
[Free Software Foundation](https://www.fsf.org/about/what-is-free-software)). Exponential is free software under the
[GNU General Public License version 2 or any later version](../../../LICENSE.md)
([Copyright and copyleft](../help/copyright-and-copyleft.md)).

- **Copyleft.** The GPL keeps those freedoms attached to the code: whoever distributes Exponential, changed or not,
  must pass the same freedoms on. No later owner can close it.
- **Zero marginal cost.** Another site, another editor, another language or another million page views costs no
  license fee. The cost is the server and the people, both of which the owner chooses.
- **Data ownership.** The database and the files are the owner's, in standard formats (SQL databases, MongoDB, plain
  files), on the owner's machines.
- **Exit costs.** A system that can be left is a system that can be trusted. Exponential documents its own exit
  ([Leaving Exponential](../install/migration/leaving-exponential.md)).
- **Metered SaaS economics.** A hosted product priced by seats, records, API calls, bandwidth or usage turns growth
  into cost and makes the vendor's pricing decision part of the site's budget every year.

## 4. The summary matrix

How each group answers the criteria in general. The pages give the details per product, with sources.

| Group | Cost at zero funding | License freedom | Exit | Metered | Runs on own server |
|---|---|---|---|---|---|
| [Proprietary DXPs](proprietary-dxp-and-saas.md#1-enterprise-dxps) | No: license or subscription, mostly quoted on request | No | Partial | Often | Some, under license |
| [Headless CMS as a service](proprietary-dxp-and-saas.md#2-headless-cms-as-a-service) | Free tiers with limits | No (some open source editors) | Partial, through APIs | Yes | No |
| [Website builders](proprietary-dxp-and-saas.md#3-website-builders-and-hosted-shops) | Free or low tiers with limits and branding | No | Limited | Yes | No |
| [PHP CMS and frameworks](php-cms-and-frameworks.md) | Mostly yes | Mostly free software | Yes | No (except hosted editions) | Yes |
| [Other languages](other-languages.md) | Varies | Varies | Varies | Varies | Varies |
| **Exponential** | **Yes** | **Yes, GPL 2 or later** | **Yes, documented** | **No** | **Yes, from SQLite on one small server to clusters** |

## 5. The pages of this book

| Page | Covers |
|---|---|
| [Proprietary DXPs and SaaS](proprietary-dxp-and-saas.md) | Adobe Experience Manager, Adobe ColdFusion, Sitecore, Optimizely, Acquia, Bloomreach, Ibexa DXP, Salesforce Experience Cloud, Microsoft SharePoint; Contentful, Contentstack, Sanity, Storyblok, Kontent.ai, Hygraph, Prismic; Squarespace, Wix, Webflow, Shopify, HubSpot CMS, Duda |
| [PHP CMS and frameworks](php-cms-and-frameworks.md) | WordPress, Drupal, Joomla, TYPO3 and the other PHP systems and frameworks, grouped by license |
| [Other languages](other-languages.md) | Python, JavaScript and TypeScript, Ruby, Java, .NET, Go, Rust, C and C++, Swift, Kotlin, Elixir, CFML and the static site generators |

## 6. How to read this book fairly

- **Facts, not opinions about people.** Each statement about a product is about its license, its business model, its
  documented limits or its version history, and is linked to the source it came from.
- **One honest line each.** Every product gets a line on what it does well. Many of them are excellent at what they
  were built for.
- **Dated.** Plans, prices and products change. The pages were checked in November 2026; the sources show the
  current state.
- **The reader decides.** The criteria are the reader's priorities as described in [section 1](#1-who-this-is-for).
  Different priorities can lead to a different choice.

## 7. What Exponential brings

Each point links to the page that documents it.

- **Free software**, GPL 2 or later, with no paid edition and no feature held back ([LICENSE.md](../../../LICENSE.md)).
- **Runs anywhere PHP runs:** PHP 8.0 to 8.5 supported, 7.4 usable ([Distributions of Exponential](../DISTRIBUTIONS.md),
  [Why Exponential supports PHP 7.4](../help/why-exponential-supports-php-7-4.md)).
- **Five databases:** SQLite, MySQL and MariaDB, PostgreSQL, MongoDB and Oracle
  ([SQLite](../features/6.0/sqlite-database.md), [MongoDB](../features/6.0/mongodb-database-support.md)).
- **Its own application server, Velocity**, with persistent workers and a response cache
  ([Velocity](../features/6.0/velocity-persistent-worker-server.md)).
- **Multi-site and many languages:** several sites from one installation, and administration translations in many
  languages ([Languages in Exponential](../languages.md), [Multi-site settings](../features/6.0/multi-site-ini-overrides.md)).
- **Content in and out:** migrations, packages, GraphQL and REST, and static publishing to any static host
  ([Migrations](../features/6.0/migrations.md), [GraphQL](../features/6.0/graphql.md),
  [Static publishing](../features/6.0/static-publishing.md), [Integrating With Exponential 6.0](../INTEGRATING.md)).
- **Accountability:** an audit trail of who did what ([Audit trail](../features/6.0/audit-trail.md)).
- **Documentation in the product:** this help center, in English and German ([Help system](../features/6.0/help-system.md)).
- **A documented way in and out:** [Switching to Exponential](../help/businesses/choosing/switching-to-exponential.md),
  [Migrating from another system](../help/adopters/migrating/from-another-system.md),
  [Leaving Exponential](../install/migration/leaving-exponential.md).

## Sources

- Free Software Foundation, [What is free software?](https://www.gnu.org/philosophy/free-sw.html) and
  [What is free software (FSF)](https://www.fsf.org/about/what-is-free-software).
- GNU, [GNU General Public License, version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).
- The sources of each product are listed on its page.
