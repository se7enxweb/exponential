---
title: "Brand and naming rules: Exponential, 7x and the Foundation"
audience: sales-marketing, editor, developer
category: business
order: 100
summary: How to write the names of the product, its family, the company 7x and the Exponential Foundation correctly; when the old name eZ Publish may appear; which code identifiers never change; with a Do and Don't table and a checklist.
keywords: brand, naming, name, Exponential, 7x, se7enx, se7enxweb, Exponential Foundation, eZ Publish, product family, Velocity, Exponential Intelligence, Exponential Layouts, style, spelling, American English, rebranding
type: help
---
# Brand and naming rules: Exponential, 7x and the Foundation

This page is for everyone who writes about Exponential: sales and marketing, editors who write web pages and
newsletters, and developers who write README files, commit messages, release notes or interface texts. It answers:
what is each name, how is it written, when may the old name appear, and which names in the code must never be
"corrected"?

Every rule below is taken from a source in this documentation. Where the sources do not set a rule (for example on
logo use), the page says so.

## Contents

1. [The names in one table](#the-names-in-one-table)
2. [The product: Exponential](#the-product-exponential)
3. [The product family](#the-product-family)
4. [The company: 7x, se7enx and se7enxweb](#the-company-7x-se7enx-and-se7enxweb)
5. [The nonprofit: the Exponential Foundation](#the-nonprofit-the-exponential-foundation)
6. [The old name: eZ Publish, only as history](#the-old-name-ez-publish-only-as-history)
7. [Code identifiers stay as they are](#code-identifiers-stay-as-they-are)
8. [Other companies and products](#other-companies-and-products)
9. [Language and spelling](#language-and-spelling)
10. [Do and Don't](#do-and-dont)
11. [What the sources do not decide](#what-the-sources-do-not-decide)
12. [A checklist before you publish](#a-checklist-before-you-publish)

## The names in one table

| Name | What it is | Write it | Source |
|---|---|---|---|
| **Exponential** | The product: the CMS, CMF and e-commerce kernel | `Exponential`; with the major line: `Exponential 6`, `Exponential 6.0.x`; a release: `Exponential 6.0.14` | [Who is 7x?](../who-is-7x.md#the-names-brookins-consulting-7x-se7enx-and-exponential) |
| **7x** | The company that leads Exponential | `7x` (digit 7, lowercase x) | [Who is 7x?](../who-is-7x.md#the-names-brookins-consulting-7x-se7enx-and-exponential) |
| **se7enx** | How 7x is spelled out in its domain | `se7enx.com` | same |
| **se7enxweb** | 7x's GitHub account and Composer vendor name | `se7enxweb`, `se7enxweb/exponential` | same |
| **Exponential Foundation** | The nonprofit that operates and funds the project | `Exponential Foundation`; in a sentence `the Exponential Foundation` | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| **exponential.earth** | The website of the open source project | lowercase, as an address | [Who is 7x?](../who-is-7x.md#the-names-brookins-consulting-7x-se7enx-and-exponential) |
| **Brookins Consulting** | The earlier name of 7x | Only in history: "7x used to be Brookins Consulting" | [Who is 7x?](../who-is-7x.md#from-brookins-consulting-to-7x-in-three-stages) |
| **eZ Publish** | The name the code base had while eZ Systems developed it | Only in the past tense, as history | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#glossary) |

## The product: Exponential

From version 6.0.10 (August 2025) the software calls itself **Exponential**. The installer, the administration, the
command line scripts, the translations, the documentation and the package server all use the name
([The product is called Exponential](../../features/6.0/rebranding-to-exponential.md)).

Rules:

- Write **Exponential** with a capital E. It is a proper name and takes no article: "Exponential publishes ...",
  not "the Exponential publishes ...".
- When the major line matters, write **Exponential 6** or **Exponential 6.0.x**; for a release, the full number:
  **Exponential 6.0.15**. Versions have no `v` in running text; the `v` belongs to the Git tag (`v6.0.14`).
- The project website announced the name as "'Exponential' or 'Exponential (CMS)'"
  ([Who is 7x?](../who-is-7x.md#why-the-product-is-called-exponential)). "Exponential CMS" is used, for example, in
  `CONTRIBUTING.md`. Use it where "content management system" must be said for clarity, such as a first mention for
  an outside audience.
- The constant `EDITION` in `lib/version.php` reads `Exponential` ([The product is called Exponential](../../features/6.0/rebranding-to-exponential.md#what-changed)).

## The product family

Use the names exactly as the Foundation page writes them ([The product family at a glance](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance)).

| Product | Exact name | Short form (after first mention) | Package |
|---|---|---|---|
| The kernel | Exponential 6 | Exponential | `se7enxweb/exponential` |
| The simple CMS of the 2.x line | Exponential Basic | Basic | `se7enxweb/exponentialbasic` (repository) |
| Symfony-only platform | Exponential Platform 3.2.9 | Exponential Platform | `exponential-platform` |
| Dual kernel | Exponential Platform Legacy | Legacy | `exponential-platform-legacy` |
| Dual kernel with layouts and media site | Exponential Platform Nexus | Nexus | `exponential-platform-nexus` |
| Symfony-only DXP | Exponential Platform DXP | DXP | `exponential-platform-dxp` |
| Web and application server | Exponential Velocity | Velocity | `se7enxweb/exponential-velocity` |
| Writing assistant | Exponential Intelligence | Exponential Intelligence, or `expai` in technical text | `se7enxweb/expai` |
| Page builder | Exponential Layouts | Layouts, the layouts editor | `se7enxweb/explayouts` |
| Alternative administration | AdminUI | AdminUI | `exp_adminui` |
| The help | the help system | the help | part of the kernel |

Notes:

- **Velocity** is also called `vc` in commands and **qbix** is the engine name in Exponential's settings and the
  name of the upstream project it was forked from ([Glossary](../../glossary.md)). In marketing text, write
  **Exponential Velocity** or **Velocity**.
- Write package names in code format and lowercase (`expai`, `explayouts`, `cjw_newsletter`), exactly as in
  `composer.json`.
- "Legacy" has a specific meaning: "The Exponential 6 kernel when it runs beside a Symfony platform"
  ([Glossary](../who-is-the-exponential-foundation.md#glossary)). Do not use "legacy" to mean "old" when you speak
  about Exponential 6 itself; it is the maintained line.

## The company: 7x, se7enx and se7enxweb

- **7x** is the company's name. It "took over leadership of the project and it's development, support, adoption and
  community growth in 2023" (README, "Who is 7x").
- Its domain is `se7enx.com`, its GitHub account and Composer vendor `se7enxweb`.
- 7x is "the North American corporation" (README). In marketing text, "7x, the company that leads Exponential" is a
  safe description.
- **Brookins Consulting** is history: "Previously before 2022, 7x was called Brookins Consulting" (README). Use it
  only when you tell the history.

## The nonprofit: the Exponential Foundation

- Write **the Exponential Foundation**. It is "a 501(c)(3) Non-Profit Corporation founded in Seattle, Washington,
  USA" on 19 March 2026 ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#in-its-own-words)).
- 7x and the Foundation are **two organizations**. Never write as if they were one, and never write that the
  Foundation sells services: 7x is the company, the Foundation operates and funds the project
  ([The Foundation and 7x](../who-is-the-exponential-foundation.md#the-foundation-and-7x)).
- The copyright holder of the software is written **Exponential Foundation and 7x** (`COPYRIGHT.md`); the
  documentation carries the same notice, with no third holder: contributors assign their copyright to both through the
  CLA ([The license of this documentation](../../README-LICENSE.md)). Copy the notice exactly, never shorten it.

## The old name: eZ Publish, only as history

Exponential continues the code base of eZ Publish, a product its creator discontinued after almost 18 years.
The name **eZ Publish** belongs to that past ([Who is 7x?](../who-is-7x.md#the-ez-publish-years-1999-to-2021)).

Rules:

- Use "eZ Publish" only in the past tense, for history: "built while the software was still called eZ Publish",
  "the eZ Publish years", "a site built on eZ Publish 4".
- Never present "eZ Publish" as the current name, as a second name, or in a product title.
- When you describe a migration, say what 7x's services page says in substance: moving a site built while the
  software was still called eZ Publish onto the current Exponential 6 ([Who is 7x?](../who-is-7x.md#services-and-hosting)).
- Exponential is not associated with eZ Systems or Ibexa. The README states: "This project is not associated with
  the original eZ Publish software or its original developer, eZ Systems or Ibexa". Never suggest a partnership,
  endorsement or continuity of the companies.
- Some historic resources keep the old name in their own titles, for example eZpedia, "The free eZ Publish
  encyclopedia that anyone can edit", or the book "Learning eZ Publish". Quote such titles as they are.

## Code identifiers stay as they are

The rename "touched text, not identifiers" ([What did not change, on purpose](../../features/6.0/rebranding-to-exponential.md#what-did-not-change-on-purpose)).
Never "correct" these in code, documentation of code, or technical marketing:

- PHP classes with the `eZ` prefix: `eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`;
- database tables such as `ezcontentobject`, and `ezpublish-legacy` style paths;
- template operators, fetch functions, INI block and key names;
- extension package names such as `se7enxweb/ezwebin`, `ezflow`, `eztags`;
- script names such as `bin/php/ezcache.php`.

`eZ` alone is a code prefix, not the product name. The rule is: rename the **product**, never a symbol or a file.

### Names for new things

The written rule is [Naming prefixes: sevenx and exp](../../standards/naming-prefixes-sevenx-and-exp.md):

- `sevenx` is 7x's official public PHP code prefix. A class or identifier cannot start with a digit (among other
  technical reasons), so "7x" cannot be a prefix (`sevenx_authentication_2fa`, `sevenx_dse`).
- Extensions and PHP classes from 7x use `sevenx` or `exp`, as 7x needs.
- 7x uses `exp` strategically, with intent, to build the Exponential brand and to assign ownership of an extension or
  solution to the Exponential project and the Exponential Foundation (Exponential Intelligence as `expai`,
  Exponential Layouts as `explayouts`, `expqueue`, `exp_adminui`, console commands with the `exp:` prefix, see the
  [Glossary](../../glossary.md)).

Your own extensions use your own prefix. Existing `eZ` identifiers are never renamed.

## Other companies and products

The sources mention other companies factually. Keep that tone.

| Name | How the sources use it | Your rule |
|---|---|---|
| eZ Systems | Developer of eZ Publish; its copyright 1999 to 2014 stays in the code | Name only as history and in copyright notices |
| Ibexa, eZ Platform, Ibexa DXP OSS | The Symfony-based successors of eZ Publish; the product was renamed "At the specific request of Ibexa" | Factual statements only; no comparisons beyond what the sources say ([Comparing Exponential factually](comparing-exponential-factually.md)) |
| Netgen | Exponential Layouts brings "the Netgen Layouts concept to the Exponential kernel" | Credit them where their work is used |
| Mugo Web | LoveStack features rebuilt "with full credit to Mugo Web" | Credit them |
| CJW Network | `cjw_newsletter`, request-shield | Credit them; request-shield is "a project of CJW Network ... not of the Foundation" |

Sources: [Who is 7x?](../who-is-7x.md#contributors-and-friends-of-the-project),
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#companion-projects).

## Language and spelling

- **American English** in the interface and documentation: license (not licence), color, behavior, organization.
  The administration translation `eng-US` is written in American English ([Languages in Exponential](../../languages.md#8-adding-or-completing-an-administration-translation)).
- **Use the word the reader sees on screen.** The help style guide sets the rule "Use the product name the reader
  sees on screen, not an internal project or code name", and prefers interface terms such as "object", "content
  class", "draft", "trash" (`doc/eng-us/specifications/6.0/help-style-guide.md`, a proposal of 18 October 2026).
- Write names of menus and buttons as they appear: **Setup**, **Send for publishing**.

## Do and Don't

| Do | Don't |
|---|---|
| Exponential 6.0.15 | eZ Publish 6, eZ Exponential, Exponential Publish |
| Exponential is open source under the GNU GPL v2 or later | Exponential is freeware, Exponential is public domain |
| 7x leads Exponential | 7x owns Exponential (the copyright is shared, the license is GPL) |
| the Exponential Foundation, a nonprofit | the 7x Foundation, Exponential Inc. |
| Exponential continues the code base of eZ Publish | Exponential is the new eZ Publish, eZ Publish is now Exponential by eZ Systems |
| sites built in the eZ Publish years | eZ Publish sites (as if the product still had that name) |
| Exponential Velocity, Velocity | Exponential Qbix (Qbix is the upstream engine) |
| Exponential Intelligence, `expai` | Exponential AI, ExpAI, "the AI publishes your content" (it never publishes on its own) |
| Exponential Layouts, the layouts editor | Netgen Layouts for Exponential (it is a port of the concept, with its own name) |
| `eZContentObject`, `eZINI` in code | `ExponentialContentObject` (a class that does not exist) |
| license, color, behavior | licence, colour, behaviour |
| not associated with eZ Systems or Ibexa | official successor of eZ Publish by Ibexa |

## What the sources do not decide

The documentation used for this page does **not** set rules for:

- the logo: its files, colors, clear space or use by partners;
- trademark status of any name;
- a tagline or slogan;
- partner or reseller naming ("certified", "official partner").

Do not invent rules for these. Ask 7x or the Foundation through their websites, and keep their answer with your
brand files. The README notes that the platform line was rebranded "to avoid any trademark issues"; that is a
statement about why a name changed, not a trademark policy.

## A checklist before you publish

- [ ] Every product name is spelled as in [The product family](#the-product-family).
- [ ] "eZ Publish" appears only in the past tense, as history.
- [ ] No sentence suggests a link to eZ Systems or Ibexa.
- [ ] 7x and the Exponential Foundation are not confused.
- [ ] Code identifiers (`eZ*` classes, `ez*` files and tables) are unchanged.
- [ ] Copyright and license notices are copied exactly.
- [ ] American English spelling.
- [ ] Version numbers are written fully (`6.0.14`), with the date if they matter.
- [ ] Others' work (Netgen, Mugo Web, CJW Network) is credited where it is used.

## Related pages

- [Sales and marketing: start here](sales-and-marketing-start-here.md)
- [Positioning and value proposition](positioning-and-value-proposition.md)
- [Writing case studies and content marketing](case-studies-and-content-marketing.md)
- [Licensing explained for buyers](licensing-for-buyers.md)
- [What Exponential is: the product family](../leadership/what-exponential-is.md)
- [The product is called Exponential](../../features/6.0/rebranding-to-exponential.md)
- [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
