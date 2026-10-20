---
title: Feature highlights mapped to customer needs
audience: sales-marketing, project-manager
category: business
order: 40
summary: The features of Exponential 6.0.x mapped to the needs customers bring: what to say, where to show it in the administration, the documentation that proves it, and the limits to state honestly.
keywords: features, highlights, customer needs, sales, presales, discovery, multisite, languages, locales, right to left, layouts, Exponential Intelligence, expai, accessibility, security, Content Security Policy, audit trail, Velocity, performance, help system, PDF, EPUB, e-commerce, REST API, databases
type: help
---
# Feature highlights mapped to customer needs

This page is for sales and marketing people, and for project managers in presales, who meet a prospect with a list of
needs and want to answer each one with a real feature of Exponential. For every need it gives the feature that answers
it, the place in the administration where you can show it, the page of this help that proves it, and the limit you
should mention before the customer finds it. Nothing here is a promise beyond what the linked pages document: when a
customer asks for more than a page says, the honest answer is "the documentation does not state that; we will check".

## Contents

1. [How to use this page](#how-to-use-this-page)
2. [The map: need, feature, where to show it, proof](#the-map-need-feature-where-to-show-it-proof)
3. [Editors build pages themselves](#editors-build-pages-themselves)
4. [Several sites from one installation](#several-sites-from-one-installation)
5. [Many languages, including right to left](#many-languages-including-right-to-left)
6. [Help with writing, under human control](#help-with-writing-under-human-control)
7. [Accessibility of the administration](#accessibility-of-the-administration)
8. [Security, audit and compliance questions](#security-audit-and-compliance-questions)
9. [Speed](#speed)
10. [Help and documentation inside the product](#help-and-documentation-inside-the-product)
11. [PDF and e-books](#pdf-and-e-books)
12. [Structured content, approval and versions](#structured-content-approval-and-versions)
13. [Shop, API and integration](#shop-api-and-integration)
14. [Freedom of hosting, database and operating system](#freedom-of-hosting-database-and-operating-system)
15. [Running the site day to day](#running-the-site-day-to-day)
16. [A discovery worksheet](#a-discovery-worksheet)
17. [What not to claim](#what-not-to-claim)

## How to use this page

1. **Start from the customer's words**, not from the feature list. Write their needs down in their language first
   (the [discovery worksheet](#a-discovery-worksheet) below helps).
2. **Find each need in the map.** Every row names one feature, one place to show it and one page to send afterwards.
3. **Show, do not tell.** Every "where to show it" is a real place in the administration of an installation you
   control. The [demo scripts](demo-scripts.md) turn the rows into 5, 15 and 45 minute walk-throughs.
4. **Say the limit yourself.** Each section ends with "Limits to state". A customer who hears a limit from you trusts
   the rest of what you say; one who finds it later does not.
5. **Send the proof.** After the meeting, send the pages linked in the rows the customer cared about. Every help page
   can be printed or downloaded as a PDF (see [PDF and e-books](#pdf-and-e-books)).

> **Note**
> The features below are those of the 6.0.15 line, which is in development; the newest tag is 6.0.14
> ([Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases)). When a customer will run a released version,
> check the [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md) for what is in which version before you promise a date.

## The map: need, feature, where to show it, proof

| The customer needs | Feature that answers it | Where to show it | Proof to send |
|---|---|---|---|
| Editors who arrange pages without a developer | Exponential Layouts: layouts, zones, blocks and rules | **Layouts** tab, **Open** a layout | [The layouts editor](../layouts-editor.md) |
| Several sites, brands or countries from one installation | Siteaccesses (one per site, language or the administration), per-site settings, cross-site links | The globe button in the header, **Open the site or another siteaccess** | [Siteaccess host and language matching](../../features/6.0/siteaccess-host-and-language-matching.md), [Per-site settings](../../features/6.0/multi-site-ini-overrides.md), [Cross-site links](../../features/6.0/multisite-cross-site-links.md) |
| Content in many languages | 268 locale files (261 languages and countries) as content languages; translations per item | An item's language badges and **Translations** tab | [Languages in Exponential](../../languages.md), [Languages](../languages.md) |
| An administration in the editors' own language | 32 administration translations, ten of them complete | Right column, **Interface** box, **Interface language** | [Languages in Exponential, support levels](../../languages.md#2-support-levels) |
| Hebrew, Arabic, Persian or Urdu | Right-to-left in content and administration (`dir="rtl"`) | Choose a right-to-left interface language | [Right to left](../../features/6.0/right-to-left.md) |
| Faster writing, with a human in charge | Exponential Intelligence (`expai`): writes drafts only | The **Intelligence** tab of an item; the edit form | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| An accessible administration | admin4: skip link, landmarks, keyboard patterns, contrast and target-size tests | Press `Tab` once on any page; use the tree with arrow keys | [Keyboard shortcuts](../keyboard-shortcuts.md), [The admin4 design](../../features/6.0/admin4-design.md) |
| Proof of who did what | Tamper-evident audit trail | **Audit** tab (`/audit/dashboard`) | [The audit trail](../../features/6.0/audit-trail.md) |
| Protection against script injection | Content Security Policy, report-only on the administration by default | `site.ini [ContentSecurityPolicy]` (show the doc page) | [Content Security Policy](../../features/6.0/content-security-policy.md) |
| Stronger sign-in | Two-step sign-in with an authenticator app or an e-mail code | `/user2fa/setup` | [Two-step sign-in](../../guides/two-factor-login.md) |
| A hardening plan for production | Security hardening guide and checklist | (a document, not a screen) | [Security hardening](../security-hardening.md) |
| A fast site | Exponential Velocity and the cache fixes of 6.0.15 | `./console exp:velocity status`; `exp:benchmark` | [Velocity](../../features/6.0/velocity-persistent-worker-server.md), [Benchmarking](../../guides/benchmarking.md) |
| Help for staff inside the tool | The help system: dashboard, search, context help, print and PDF | The **Help** button in the header | [Help system](../../features/6.0/help-system.md) |
| PDF output of content | PDF export with your own footer and TrueType fonts | **Setup > PDF export** | [PDF export](../../features/6.0/pdf-export.md) |
| Manuals as e-books | EPUB 3 and PDF of help manuals kept as content | A manual's page in the help, **Download EPUB** | [Help maintained as content](../../features/6.0/help-content.md) |
| Their own content types | Content classes with attributes | **Setup > Classes** | [The content model and editing content](../../guides/content-model-and-editing.md) |
| Review before publishing | Drafts, versions, approval workflows | **Manage versions** on an item; **My pending items** | [Publishing, versions and the trash](../publishing-and-versions.md), [Workflows](../../guides/workflows.md) |
| An online shop | The built-in shop with a dashboard and order statuses | **Store** tab | [Store dashboard](../../features/6.0/store-dashboard.md) |
| Content for apps and other systems | REST API v1 and v2 | (show a request in a terminal) | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) |
| Their preferred database | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | **Setup > System information**, Database card | [Database support](../database-support.md) |
| Their preferred hosting | Almost any operating system with PHP 8.0 or later; many web servers | (a document) | [Operating system support](../os-support.md) |
| Fewer surprises in operations | Log viewer, error references, content checks, upgrade check | **Setup > Log files**, **Setup > Upgrade check** | [Log viewer](../../features/6.0/log-viewer.md), [Content checks](../../features/6.0/content-checks.md), [Upgrade check](../../guides/upgrade-check.md) |
| Big changes that do not time out | Content jobs in the background | Remove or move a large subtree | [Content jobs](../../features/6.0/content-jobs.md) |

## Editors build pages themselves

**What to say.** Exponential Layouts is "a visual page builder for Exponential. It brings the Netgen Layouts concept to
the Exponential kernel: a layout is a saved arrangement of blocks in zones, and a rule decides which pages get it"
(`README.md`, "Exponential Layouts"). Editors drag blocks into zones, change them in a **Content** and a **Design**
tab, and work on a draft that the site does not show until they publish
([The layouts editor](../layouts-editor.md)).

**What to show.** The [layouts walk-through of the tour](../admin-tour.md#in-action-the-layouts-editor): open a
layout, select a block, add a **Title** block, move it, show a linked zone, then **Discard** so nothing changes.

**Why it matters to the customer.**

- Shared layouts keep header and footer in one place: other layouts *link* a zone to them instead of copying blocks.
- Mappings (rules) decide which pages use a layout, by location, subtree, content type or siteaccess, and the first
  enabled mapping by priority wins ([Which pages use a layout](../layouts-editor.md#which-pages-use-a-layout)).

**Limits to state.**

- The **Layouts** tab is shown only to roles that may work with layouts; access is set by an administrator.
- Changing mappings changes whole parts of the site; the help itself advises editors to ask a site builder.
- Exponential Platform DXP does not ship a layouts editor; Legacy and Nexus do
  ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance)).

## Several sites from one installation

**What to say.** Multisite is in the README's list of main features. Each site is a *siteaccess* with its own host,
path, language and design ([Who is 7x?, The product](../who-is-7x.md#the-product-what-exponential-is)). A site that
lives in its own extension can carry settings that apply only to its siteaccesses
([Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md)). Host names can be matched by
their start, so test and live hosts reach the same site, and the start page can send visitors to their language
([Siteaccess by host prefix](../../features/6.0/siteaccess-host-and-language-matching.md)). Links between sites of one
content tree go to the right site with the `ezurlmultisite` extension
([Cross-site links](../../features/6.0/multisite-cross-site-links.md)).

**What to show.** The globe button in the header (**Open the site or another siteaccess**) and, on an installation
made with `./console exp:install`, the sites of the multisite package
([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)).

**Limits to state.**

- One Velocity server process serving several sites "is a routing convenience, not a security boundary"; for
  operating-system isolation run one instance per site (`README.md`, "When a traditional server is still the right
  answer").
- An installation can have at most 62 content languages (30 on 32-bit PHP)
  ([section 11 of Languages](../../languages.md#11-the-limit-of-62-content-languages)).

## Many languages, including right to left

**What to say.** "268 locale files (261 languages and countries ...) can be chosen as content languages and
siteaccess locales; 32 administration translations exist, ten of them complete" ([Languages in Exponential](../../languages.md)).
Each translation of an item is published on its own ([Languages](../languages.md#translate-an-item)). When the interface
language is written right to left, "the administration turns around" and content in a right-to-left language is marked
the same way wherever it is shown and edited ([Right to left](../../features/6.0/right-to-left.md)).

**What to show.** Translate an item with its dashed language badge; switch the **Interface language** in the right
column to German, then to Hebrew or Arabic, and back.

**Limits to state.**

| Level | What the customer gets | Source |
|---|---|---|
| Full | Administration and content in the language: `eng-US`, `ger-DE`, `esl-ES`, `esl-MX`, `fre-FR`, `heb-IL`, `ara-SA`, `lat-VA`, `tlh-QO`, `ukr-UA` (eight of them machine-assisted) | [Support levels](../../languages.md#2-support-levels) |
| Partial interface | 21 older translations cover 16 to 39% of today's strings; the rest shows in English | same |
| Content only | Every other locale: content, dates, numbers and currency, with an English administration | same |

> **Tip**
> If a customer's language is only "content only", the honest offer is a translation project. The way to add or
> complete an administration translation is documented in
> [section 8 of Languages](../../languages.md#8-adding-or-completing-an-administration-translation).

## Help with writing, under human control

**What to say.** Exponential Intelligence "writes drafts, as the signed-in editor and with that editor's own
permissions", never publishes on its own, and works with hosted providers in the US or the EU or with a local
open-weight model ([Exponential Intelligence](../../features/6.0/extensions/expai.md); `README.md`).

| Customer concern | Documented answer |
|---|---|
| "Will it publish something wrong?" | Drafts only; **Send for approval** runs the normal publish operation, so an approval workflow holds the version. |
| "Where does our content go?" | A data policy per provider: region `us`, `eu` or `local`, retention, rules per section and class; every request records where it went. |
| "What will it cost?" | A usage log with the estimated cost of each request, limits per user, day, month, site and provider, and budgets. |
| "Can we switch it off?" | `[AISettings] Enabled` turns it off everywhere or per siteaccess. |
| "Can our data protection officer review it?" | **EI: Summary** is one printable page for a data protection officer. |
| "Is it audited?" | Every request, draft and state change is an event of the audit channel `ai`; prompts, answers and keys are never recorded. |

**Limits to state.** It needs a provider key or a local model and "sends nothing anywhere until you add a provider
key" (`README.md`). Provider prices are the provider's, not the project's.

## Accessibility of the administration

**What to say.** admin4 gives screen readers and keyboards a skip link, named landmarks, the WAI-ARIA tabs and tree
patterns, named form fields and calendar buttons, and tests in `tests/tests/design/` check each of them
([The admin4 design, Landmarks and names](../../features/6.0/admin4-design.md#landmarks-and-names)). The color tokens
pass a contrast test in light and dark mode, and targets are at least 24 by 24 pixels
([Who is the Exponential Foundation?, Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility)).
The help pages have one `h1`, named landmarks and a visible focus ring
([Help system](../../features/6.0/help-system.md)).

**What to show.** Press `Tab` once (the **Skip to main content** link), press `/` to jump to search, open the content
tree with the arrow keys, switch the color mode with the sun button ([Keyboard shortcuts](../keyboard-shortcuts.md)).

**Limits to state.** These are properties of the administration. The public site's accessibility depends on the
templates the project builds; the sources do not state a conformance level for public sites. Treat it as a project
requirement and plan the testing for it.

## Security, audit and compliance questions

| Customer question | Feature | Proof |
|---|---|---|
| "Who changed or removed this?" | The audit trail records sign-ins, content changes, settings writes, cache clears, deploys, orders and VAT changes; on by default | [The audit trail](../../features/6.0/audit-trail.md) |
| "Can the record be tampered with?" | Each record carries the hash of the one before; `verify` tells whether anything was edited or cut | same |
| "How do you protect against script injection?" | A Content Security Policy; report-only on the administration by default, off on the sites, with documented steps to enforce | [Content Security Policy](../../features/6.0/content-security-policy.md) |
| "Can we require a second factor?" | TOTP apps or e-mail codes | [Two-step sign-in](../../guides/two-factor-login.md) |
| "Do you see attacks?" | `security.log` classifies probes and attacks; nothing is blocked by the classifier itself | [Request logs](../../features/6.0/request-logs.md) |
| "What is your hardening guidance?" | A guide "secure by default in the OpenBSD sense" with a checklist | [Security hardening](../security-hardening.md), [chapter 13](../../install/13-security-hardening.md) |
| "How do you handle vulnerabilities?" | Private reporting with a first answer "within one business day"; fixes on `main` and in the next 6.0.x release | `SECURITY.md` |

**Limits to state.** The CSP is not enforced by default; enforcing it is a project step. The sources give no legal or
certification statements (for example on GDPR): turn those into questions for the customer's own counsel.

## Speed

**What to say.** Velocity is "a web server written in PHP, bundled with Exponential, that keeps the application loaded
between requests", and it is the recommended engine for every stage; FrankenPHP is the production-ready alternative
([Velocity](../../features/6.0/velocity-persistent-worker-server.md)). Equally important: "Most of the speed came from
fixing Exponential and the cache, not from the server process model", so 6.0.15 is "fast under load on Apache or Nginx
too" (`README.md`, "The part that matters even if you never run Velocity").

**Figures.** `README.md` documents 0.36 ms per cached page and 8,941 requests per second sustained, measured on a
12-core machine, and says the figures are reproducible with scripts that ship with the project. Quote them only with
that context, and offer to measure on the customer's own hardware with `exp:benchmark`
([Benchmarking](../../guides/benchmarking.md)).

**Limits to state.** Shared hosting usually does not allow a long-running process; there Apache or LiteSpeed with
`.htaccess` is the answer. After changing a template, setting or class, a Velocity server must be restarted
(`README.md`).

## Help and documentation inside the product

**What to say.** The help is the repository's own documentation, shown in the administration: a dashboard, search,
pages by audience, a **Help** button that opens the page belonging to the screen you are on, and a **Report an error**
page that prepares an issue ([Help system](../../features/6.0/help-system.md)). The documentation is free
documentation under the GNU FDL 1.3, so a customer may copy, translate and adapt it for their own staff
([Free documentation](../free-documentation.md)).

**What to show.** Press **Help** on the dashboard, search for "publish", open a page and press **Download as PDF**.

## PDF and e-books

| Output | What it does | Source |
|---|---|---|
| PDF export of content | **Setup > PDF export**: files that open, text that reads correctly, images that fit, UTF-8 with a TrueType font, your own footer line | [PDF export](../../features/6.0/pdf-export.md) |
| A help page as PDF | **Download as PDF** on every help page, drawn on the server by the kernel's own PDF library, no outside service | [Help system, Print and PDF](../../features/6.0/help-system.md#print-and-pdf) |
| A help manual as EPUB 3 or PDF | Manuals kept as content (phase 1 in 6.0.15), **Download EPUB** and **Download PDF** on the manual's page | [Help maintained as content](../../features/6.0/help-content.md) |

**Limits to state.** EPUB is documented for help manuals kept as content, not as a general export of site content.
Arabic letters in help PDFs are drawn in their basic forms, without joining
([Help system](../../features/6.0/help-system.md#print-and-pdf)).

## Structured content, approval and versions

**What to say.** "As a CMS its most notable feature is its fully customizable and extendable content model"
(`README.md`). Content classes define the fields of each content type; every edit is a draft, every publish keeps the
previous version, and an approval workflow can hold a version until someone approves it
([Content model](../../guides/content-model-and-editing.md), [Publishing and versions](../publishing-and-versions.md),
[Workflows](../../guides/workflows.md)). Fine-grained roles and policies decide who may do what
([Roles and policies](../../guides/roles-and-policies.md)).

**What to show.** The [folder walk-through](../admin-tour.md#in-action-editing-a-folder): edit, store a draft, preview
at phone width, publish, compare versions.

## Shop, API and integration

- **Shop.** "Advanced e-Commerce Stores (Built in)" is in the README's feature list; the **Store** tab opens a
  dashboard with order statuses ([Store dashboard](../../features/6.0/store-dashboard.md)); PayPal is an extension
  ([Who is 7x?, extensions](../who-is-7x.md#the-extension-ecosystem)).
- **API.** REST v1 and v2 come from one extension, with OAuth2 tokens and personal API keys
  ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)).
- **Extensions.** `composer.json` requires 66 `se7enxweb/*` packages; many are inactive until switched on
  ([Default extension distribution](../../features/6.0/default-extension-distribution.md)).

**Limits to state.** The sources do not list certified payment providers beyond the PayPal extension, nor named
third-party integrations. Treat each integration as a scoping question
([Estimating and scoping with extensions](../leadership/estimating-and-scoping-with-extensions.md)).

## Freedom of hosting, database and operating system

**What to say.** Six databases are supported, "in order of preference: 1. SQLite 3 (the default) · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL" ([Database support](../database-support.md)). The operating system can
be "almost any that runs PHP 8.0 or later", sorted into four honest tiers ([Operating system support](../os-support.md)).
A site can move to another database with documented tools
([Moving a site to another database](../database-support.md#moving-a-site-to-another-database)).

**Limits to state.** SQLite serves a site from one machine; several web servers need a database server. MariaDB and
Oracle are not run in the project's CI ([Test coverage](../database-support.md#test-coverage-ci)). Microsoft SQL Server
has no driver in 6.0.x.

## Running the site day to day

| Need | Feature | Proof |
|---|---|---|
| Find the error behind a message | An error page shows a reference; the log viewer finds that request | [When something goes wrong](../when-something-goes-wrong.md), [Log viewer](../../features/6.0/log-viewer.md) |
| Tidy up unused files | Unreferenced objects, broken relations, storage files | [Content checks](../../features/6.0/content-checks.md) |
| Know whether files drifted before an upgrade | **Setup > Upgrade check** | [The upgrade check](../../guides/upgrade-check.md) |
| Take the site offline for work | Maintenance mode | [Maintenance mode](../../features/6.0/maintenance-mode.md) |
| Find content by an id or a path | **Setup > Content finder** | [The content finder](../../features/6.0/content-finder.md) |

## A discovery worksheet

Copy it into your meeting notes and fill it in with the customer.

```text
Customer: <organization>            Date: <date>          Contact role: <role, not a name>

1. Sites: how many sites, brands or countries?        -> <answer>   -> multisite rows
2. Languages: which, and is any written right to left? -> <answer>   -> languages rows
3. Who edits pages, and do they need developers today? -> <answer>   -> layouts rows
4. Approval: who must approve before publishing?       -> <answer>   -> workflows rows
5. AI: wanted? which region may content go to?         -> <answer>   -> expai rows
6. Security and audit duties (internal or legal)?       -> <answer>   -> security rows
7. Accessibility duties for staff or the public?        -> <answer>   -> accessibility rows
8. Database and hosting rules of their IT?              -> <answer>   -> freedom rows
9. Shop, API, integrations?                             -> <answer>   -> shop and API rows
10. Performance expectations, and how they measure it?  -> <answer>   -> speed rows
Pages to send afterwards: <list of links from the map>
Open questions the documentation does not answer: <list>
```

## What not to claim

- No prices, SLAs, response times, customer names, user counts or market shares: none is in the sources. Prices and
  terms of 7x's services are on 7x's own website, not here ([Who is 7x?, Services and hosting](../who-is-7x.md#services-and-hosting)).
- No feature that a page here does not describe. "On the roadmap" is only true when a page says so (for example the
  later phases in [Help maintained as content](../../features/6.0/help-content.md)).
- No comparison with a named competitor; see [Comparing Exponential factually](comparing-exponential-factually.md).
- No current use of the old product name: Exponential continues the code base of eZ Publish, a product its creator
  discontinued after almost 18 years ([Who is 7x?](../who-is-7x.md#the-ez-publish-years-1999-to-2021)).

## Related pages

- [Sales and marketing: start here](sales-and-marketing-start-here.md)
- [Positioning and value proposition](positioning-and-value-proposition.md)
- [Demo scripts](demo-scripts.md)
- [Handling objections: security, longevity and support](objection-handling.md)
- [Comparing Exponential factually](comparing-exponential-factually.md)
- [What Exponential is: the product family](../leadership/what-exponential-is.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
