---
title: Positioning and value proposition
audience: sales-marketing
category: business
order: 20
summary: Where Exponential stands and why it matters to a buyer: the position statement, the differentiators with their sources, a value proposition canvas, and elevator pitches of 1, 3 and 10 sentences built only from documented facts.
keywords: positioning, value proposition, pitch, elevator pitch, differentiators, messaging, canvas, open source, CMS, CMF, e-commerce, GPL, self-hosting, vendor lock-in, Git history, databases, lineage, eZ Publish, 7x, Exponential Foundation
type: help
---
# Positioning and value proposition

This page is for sales and marketing people who need to say clearly what Exponential is, for whom, and why it is
worth choosing. It answers: what is our position, what sets the product apart, what value does it create for a
customer, and how do we say that in one, three or ten sentences.

Everything here is built from facts in the repository and the help. Each fact names its source. Where you add your
own company's services or prices, mark them as yours: they are not facts about the product.

## Contents

1. [The position in one paragraph](#the-position-in-one-paragraph)
2. [The facts the position rests on](#the-facts-the-position-rests-on)
3. [The differentiators](#the-differentiators)
4. [The value proposition canvas](#the-value-proposition-canvas)
5. [Elevator pitches](#elevator-pitches)
6. [Messages per audience](#messages-per-audience)
7. [Words to use and words to avoid](#words-to-use-and-words-to-avoid)
8. [Claims you may not make](#claims-you-may-not-make)
9. [A positioning worksheet for your offer](#a-positioning-worksheet-for-your-offer)

## The position in one paragraph

**Exponential is the open source content management system, application framework and online shop for
organizations that want to own their platform.** It is free to use under the GNU GPL, runs on the operating system,
web server and database the customer chooses, and comes with its full development history in Git. It continues a
code base of nearly thirty years, the code base of eZ Publish, a product its creator discontinued after almost 18
years, and is maintained today by the company 7x and operated by the nonprofit Exponential Foundation, with regular
6.0.x releases since 1 January 2024.

Sources: `README.md` ("What does Exponential provide", "License", "Requirements"),
[Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md).

## The facts the position rests on

| Fact | Source |
|---|---|
| "Exponential is a professional PHP application framework with advanced CMS (content management system) functionality. As a CMS its most notable feature is its fully customizable and extendable content model." | `README.md` |
| "Standard CMS functionality, like news publishing, e-commerce and forums is built in and ready for you to use." | `README.md` |
| "Exponential is database, platform and browser independent." | `README.md` |
| The project site calls it "an award-winning, enterprise-grade Open Source Content Management System and development framework with functionality for web publishing, media portals, intranets, e-commerce and extranets". | exponential.earth, quoted on [Who is 7x?](../who-is-7x.md#the-product-what-exponential-is) |
| "Using Exponential under the terms of the GNU GPL is free of charge. The Professional License is no longer available." | `README.md`, "License" |
| Copyright notices from 1999 (eZ Systems AS) and from 1998 (7x and the Exponential Foundation); Git history from 2 September 2002 | `COPYRIGHT.md`; [Who is 7x?](../who-is-7x.md#nearly-thirty-years-the-timeline) |
| Releases 6.0.0 (1 January 2024) to 6.0.14 (7 June 2026); 6.0.15 in development | [Who is 7x?](../who-is-7x.md#the-60x-releases) |
| Six supported databases: SQLite 3 (the default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | `README.md`, [Database support](../database-support.md) |
| "almost any" operating system "that runs PHP 8.0 or later" | [Operating system support](../os-support.md) |
| PHP 8.0 to 8.5 supported | `README.md`, "What version of PHP is required" |
| 355 locale files, 43 of them right to left; administration translations (the current totals are at the top of the languages page) | [Languages in Exponential](../../languages.md) |
| "The recommended way is to get the kernel and every se7enxweb extension as Git clones with their full history" | `README.md`, "Installation"; [Develop with Git](../develop-with-git.md) |
| "A published tag is never changed" | `SECURITY.md` |
| The documentation is free documentation under the GNU FDL 1.3 or later | [Free documentation](../free-documentation.md) |

> **Note**
> The project site's phrase "award-winning" names no award, and the sources used for this help do not name one
> either. Do not repeat "award-winning" in your own material unless you can name the award and its year.

## The differentiators

Each differentiator is a property of Exponential that a buyer can check. None of them depends on what other products
do; that keeps them true even when the market changes. For comparisons, see
[Comparing Exponential factually](comparing-exponential-factually.md).

| Differentiator | What it means for the buyer | Proof to show | Page to send |
|---|---|---|---|
| **Free and open license** | No license fee; the right to use, study, change and share the code under the GPL v2 or later | `README.md`, "License"; `LICENSE` | [Licensing explained for buyers](licensing-for-buyers.md) |
| **Self-hosting freedom** | Run it on your own servers, in any cloud, or with a hosting partner; change host without changing software | The tiers on [Operating system support](../os-support.md); the web servers in `README.md`, "How Exponential is served" | [Choosing hosting, database and operating system](../leadership/choosing-hosting-database-and-os.md) |
| **Choice of database** | Use the database your team already runs, or none at all with SQLite | [Database support](../database-support.md) | same |
| **Full Git history** | Every change since 2002 can be read, explained and audited; fixes can be used before they are released | [Develop with Git](../develop-with-git.md); the history pages ([The history of Exponential](../../history/README.md)) | [Running a team on Git](../leadership/running-a-team-on-git.md) |
| **No vendor lock-in** | Code under the GPL, packages on Composer and Packagist, documentation under the GFDL, any host: the customer can always continue with someone else | `README.md`; [Free documentation](../free-documentation.md); [Getting the code](../../install/03-getting-the-code.md) | [Licensing explained for buyers](licensing-for-buyers.md) |
| **Continuity of nearly thirty years** | A content model and a way of working that have lasted; existing sites can move forward | [Who is 7x?](../who-is-7x.md#nearly-thirty-years-the-timeline); the migration chapters [14](../../install/14-migrating-from-4x.md), [15](../../install/15-migrating-from-5x-legacy.md), [16](../../install/16-migrating-from-ez-platform-and-ibexa.md) | [Handling objections](objection-handling.md) |
| **CMS, framework and shop in one** | One installation for content, custom applications and an online store | [Who is 7x?](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one) | [Feature highlights](feature-highlights-for-customers.md) |
| **Many sites, many languages** | Several sites from one installation; content in 355 locales, right to left included | [Siteaccess host and language matching](../../features/6.0/siteaccess-host-and-language-matching.md), [Languages in Exponential](../../languages.md), [Right to left](../../features/6.0/right-to-left.md) | [Planning multisite and multilingual projects](../leadership/multisite-and-multilingual-planning.md) |
| **Editors in control** | Page building without template work; a writing assistant that only writes drafts | [The layouts editor](../layouts-editor.md); [Exponential Intelligence](../../features/6.0/extensions/expai.md) | [Demo scripts](demo-scripts.md) |
| **Security you can see** | A Content Security Policy in the administration, two-step sign-in, a tamper-evident audit trail, a hardening guide, private vulnerability reporting | [Content Security Policy](../../features/6.0/content-security-policy.md), [The audit trail](../../features/6.0/audit-trail.md), [Security hardening](../security-hardening.md), `SECURITY.md` | [Handling objections](objection-handling.md) |
| **Documentation inside the product** | The help is in the administration, searchable, per audience, downloadable as PDF, and free to adapt | [The help system](../../features/6.0/help-system.md), [Free documentation](../free-documentation.md) | this help |

## The value proposition canvas

The canvas has two sides. On the right, the **customer**: the jobs they need to get done, the pains in the way and the
gains they hope for. On the left, **Exponential**: what it offers, how it relieves the pains and how it creates the
gains. The left side lists only documented properties. The right side is typical; replace it with what your
customer actually tells you.

### The customer side (fill in from your discovery)

| Customer jobs | Pains | Gains |
|---|---|---|
| Publish structured content on one or more websites | Content locked into one page layout; copy and paste between sites | Content written once, shown in many places |
| Run sites in several languages and countries | Translations out of step; languages the system cannot handle | Every market served in its own language and direction |
| Let editors change pages quickly | Every page change needs a developer | Editors build and rearrange pages themselves |
| Sell online | A separate shop system to integrate | Shop and content in one place |
| Keep the platform secure and compliant | Unknown who changed what; unclear patch policy | A record of who did what; a clear way to get fixes |
| Control costs and the future of the platform | License fees; dependence on one vendor or one host | Freedom to choose and change provider |
| Keep an existing site alive | An old platform nobody maintains | A maintained continuation, with a path forward |
| *<your customer's job>* | *<their pain>* | *<their gain>* |

### The Exponential side

| Products and services | Pain relievers | Gain creators |
|---|---|---|
| Exponential 6.0.x: CMS, CMF and built-in e-commerce (`README.md`) | Free under the GPL: no license fee (`README.md`, "License") | User-defined content classes: one item, many views (`README.md`, "Main Exponential features") |
| Exponential Layouts, the visual page builder ([The layouts editor](../layouts-editor.md)) | Editors rearrange pages "without touching templates" (`README.md`) | Rules give each part of a site its own layout, by subtree, class, siteaccess or time (`README.md`) |
| Exponential Intelligence, the writing assistant ([Exponential Intelligence](../../features/6.0/extensions/expai.md)) | "Drafts only. It never publishes on its own" (`README.md`) | Fields filled from a brief, translation sync, alt text from images (`README.md`) |
| Multisite and 355 locales ([Languages in Exponential](../../languages.md)) | Right-to-left languages handled in content and administration ([Right to left](../../features/6.0/right-to-left.md)) | New markets added as siteaccesses of the same installation |
| Six databases, almost any OS, many web servers or the bundled Velocity server | No forced database server: SQLite needs none (`README.md`) | Choice of hosting, and freedom to move |
| The audit trail, CSP, two-step sign-in, hardening guide | "Every record carries the hash of the record before it" ([The audit trail](../../features/6.0/audit-trail.md)) | Proof for auditors of who did what |
| The release and security policy (`SECURITY.md`) | Fixes arrive as the next release; a published version never changes | Predictable upgrades along the 6.0.x line |
| Migration paths from earlier versions and related platforms (installation chapters 14 to 16) | An existing site does not have to be rewritten from scratch | The content model and the content are kept |
| Services by 7x: development, migration, support, managed hosting ([Services and hosting](../who-is-7x.md#services-and-hosting)) | A company that maintains, supports and hosts the product | One partner for the software and its operation, if the customer wants one |
| *<your company's services>* | *<what you relieve>* | *<what you add>* |

> **Tip**
> A good canvas has fewer rows, not more. After a discovery call, keep the three jobs that matter most to this
> customer and the three Exponential rows that answer them. Those six rows are your proposal's spine.

## Elevator pitches

Each sentence below can be backed by the source in brackets. Do not say the brackets aloud; have them ready if asked.

### One sentence

> Exponential is a free, open source content management system, framework and online shop that you can host
> anywhere and keep for good, continuing a code base of nearly thirty years. [README, LICENSE, Who is 7x?]

### Three sentences

> Exponential is an open source CMS, application framework and built-in online shop, free to use under the GNU GPL.
> [README] It runs on almost any operating system with PHP, on the database you choose, from SQLite to PostgreSQL
> and Oracle, so you are never tied to one host or vendor. [Operating system support, Database support] It continues
> the eZ Publish code base with its full history since 2002 and is maintained by the company 7x and the nonprofit
> Exponential Foundation, with regular 6.0.x releases since January 2024. [Who is 7x?, Foundation page]

### Ten sentences

> 1. Exponential is a professional PHP application framework with advanced content management, and its most notable
>    feature is a content model you define yourself. [README]
> 2. News publishing, e-commerce and forums are built in, and it serves several sites and languages from one
>    installation. [README]
> 3. Content can be written in 355 locales, right-to-left languages included, and the administration speaks many
>    languages, with the current totals at the top of the languages page. [Languages in Exponential]
> 4. Editors build pages themselves with Exponential Layouts, and Exponential Intelligence helps them write, but only
>    ever as drafts that a person publishes. [README]
> 5. It is free to use under the GNU General Public License, version 2 or later, and its documentation is free under
>    the GNU Free Documentation License. [README, Free documentation]
> 6. It runs on almost any operating system with PHP 8.0 or later, behind Apache, Nginx and other web servers, or on
>    its own Velocity server. [Operating system support, README]
> 7. It supports six databases, with SQLite as the default, so a site needs no database server at all.
>    [Database support]
> 8. Its code continues eZ Publish, a product its creator discontinued after almost 18 years, and the full Git
>    history back to 2002 comes with it. [Who is 7x?]
> 9. Since December 2023 the company 7x leads it, and since March 2026 the nonprofit Exponential Foundation operates
>    the project; releases 6.0.0 to 6.0.14 have shipped since January 2024. [Who is 7x?, Foundation page]
> 10. Security fixes arrive as the next release, published versions never change, and vulnerabilities are reported
>     privately with a first answer within one business day. [SECURITY.md]

## Messages per audience

| Audience | Lead message | Back it with |
|---|---|---|
| **Decision maker** (director, owner) | Free license, no lock-in, a platform with nearly thirty years of continuity | [Licensing explained for buyers](licensing-for-buyers.md), [Who is 7x?](../who-is-7x.md) |
| **Marketing or communications lead** | Editors build pages and publish in many languages without waiting for developers | [The layouts editor](../layouts-editor.md), [Languages](../languages.md) |
| **Editorial lead** | Drafts, versions, approval workflows and a writing assistant that never publishes on its own | [Publishing, versions and the trash](../publishing-and-versions.md), [Workflows](../../guides/workflows.md), [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| **IT and operations** | Your OS, your web server, your database; a documented hardening guide and audit trail | [Operating system support](../os-support.md), [Database support](../database-support.md), [Security hardening](../security-hardening.md) |
| **Security officer** | CSP, two-step sign-in, tamper-evident audit, private disclosure, clear release policy | [Content Security Policy](../../features/6.0/content-security-policy.md), [The audit trail](../../features/6.0/audit-trail.md), `SECURITY.md` |
| **Developer** | Git clones with full history, Composer packages, extensions, a REST API, PHP 8.0 to 8.5 | [Develop with Git](../develop-with-git.md), [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md), [Extensions](../../guides/extensions.md) |
| **Owner of an older site** | A maintained continuation, with documented migration paths | Installation chapters [11](../../install/11-upgrading.md), [14](../../install/14-migrating-from-4x.md), [15](../../install/15-migrating-from-5x-legacy.md), [16](../../install/16-migrating-from-ez-platform-and-ibexa.md) |

## Words to use and words to avoid

| Use | Avoid | Why |
|---|---|---|
| Exponential, Exponential 6.0.x | "eZ Publish" as the product's current name | The product was renamed in August 2025 ([Brand and naming rules](brand-and-naming-rules.md)) |
| open source, free to use under the GPL | "free trial", "community edition" | There is no paid edition: "The Professional License is no longer available" (`README.md`) |
| continues the code base of eZ Publish | "the new eZ Publish", "eZ Publish 6" in marketing | The product's name today is Exponential |
| maintained by 7x, operated by the Exponential Foundation | "official successor of" another vendor | "This project is not associated with the original eZ Publish software or its original developer, eZ Systems or Ibexa" (`README.md`) |
| self-hosted, hosted by a partner of your choice | "cloud only", "SaaS" for the software itself | The software is installed, not rented; hosting is a service around it |
| documented, sourced, reproducible | "best", "fastest", "most secure" | Superlatives cannot be sourced |

## Claims you may not make

The sources do **not** support these, so they stay out of every pitch, slide and web page:

- a number of customers, installations, users or a market share;
- named customers or quotes, unless the customer agreed in writing ([Writing case studies](case-studies-and-content-marketing.md));
- prices of the software (there are none) or of services you do not offer yourself;
- guaranteed response or resolution times beyond what `SECURITY.md` says (a first answer to a security report
  "within one business day");
- performance figures other than those in `README.md`, "What that measures as", and then only with the context the
  README gives (what was measured, on what machine, reproducible with scripts that ship with the project);
- certifications or compliance labels the sources do not name.

## A positioning worksheet for your offer

If you sell your own services around Exponential, write your position on top of the product's. Fill in the
placeholders and keep the product facts as they are.

```text
For          <target customer: industry, size, situation>
who          <the job they need done, the pain they feel>
<our offer>  is <your service: implementation, migration, hosting, support>
based on     Exponential, the open source CMS, framework and online shop
that         <key benefit for this customer, in their words>.
Because      Exponential is free under the GPL, runs on the OS, web server and database
             the customer chooses, and comes with its full Git history,
the customer <can change provider / keeps their data and code / owns the platform>.
We add       <your experience, team, service level you can actually deliver>.
Proof        <help pages, your own permitted references>
```

## Related pages

- [Sales and marketing: start here](sales-and-marketing-start-here.md)
- [Who Exponential is for: personas and industries](who-exponential-is-for.md)
- [Feature highlights mapped to customer needs](feature-highlights-for-customers.md)
- [Comparing Exponential factually](comparing-exponential-factually.md)
- [Handling objections: security, longevity and support](objection-handling.md)
- [What Exponential is: the product family](../leadership/what-exponential-is.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
