---
title: Glossary for advocates
audience: advocate
category: advocacy
order: 143
summary: Plain-language definitions of the terms advocates meet and use - product names, organizations, technical terms like siteaccess, extension, override and draft, community and license terms - with links to the full glossary and the pages that explain them.
keywords: glossary, terms, definitions, siteaccess, extension, override, content class, draft, version, layout, zone, block, GPL, GFDL, pull request, advocate
type: help
---
# Glossary for advocates

This page is for advocates who explain Exponential to others. Each term has a plain definition and a link. The full
technical glossary is [the glossary](../../glossary.md); the product and organization terms come from
[Who is the Exponential Foundation?, Glossary](../who-is-the-exponential-foundation.md#glossary).

## Contents

1. [Product and organizations](#product-and-organizations)
2. [Working in the administration](#working-in-the-administration)
3. [Building and running](#building-and-running)
4. [Community and licenses](#community-and-licenses)

## Product and organizations

| Term | Plain meaning |
|---|---|
| Exponential | The CMS and framework; formerly called eZ Publish ([Naming rules quick card](naming-rules-quick-card.md)) |
| Exponential 6, 6.0.x | The current major line, since 1 January 2024 |
| 7x | The company that leads Exponential; formerly Brookins Consulting |
| se7enxweb | 7x's GitHub account and Composer vendor name |
| `sevenx` | 7x's official public PHP code prefix; "7x" cannot be one, because a code name cannot start with a digit ([Naming prefixes](../../standards/naming-prefixes-sevenx-and-exp.md)) |
| `exp` | The code prefix 7x uses with intent to build the Exponential brand and to assign an extension or solution to the Exponential project and the Exponential Foundation |
| Exponential Foundation | The nonprofit that operates and funds the project |
| Velocity | Exponential's own PHP web and application server |
| Exponential Intelligence (expai) | The writing assistant that writes drafts |
| Exponential Layouts | The visual page builder |
| Exponential Platform (DXP, Legacy, Nexus) | The Symfony-based family members ([Knowing the product family](knowing-the-product-family.md)) |
| Legacy | Exponential 6 when it runs beside a Symfony platform; not "old" |

## Working in the administration

| Term | Plain meaning |
|---|---|
| Object, item | A piece of content: an article, an image, a folder |
| Content class | The type of an object and its fields, defined in **Setup > Classes** |
| Draft | An unpublished version you are working on |
| Version | Each saved state of an object; older ones are kept |
| Publish | Make a draft the version visitors see |
| Trash | Where removed items wait and can be restored |
| Layout, zone, block | A page arrangement, its areas, and the pieces in them ([The layouts editor](../layouts-editor.md)) |
| Section, role, policy | How access is organized and granted |

## Building and running

| Term | Plain meaning |
|---|---|
| Siteaccess | One way into an installation: a site, a language or the administration |
| Extension | A package that adds modules, classes, templates or settings ([Extensions](../../guides/extensions.md)) |
| Override | A template that replaces another for certain pages ([Templates and design](../../guides/templates-and-design.md)) |
| INI settings | Configuration files such as `site.ini` |
| Engine | What serves the site: Velocity, FrankenPHP, or a web server with PHP-FPM |
| CSP | Content Security Policy: tells the browser which scripts and styles a page may use |
| Audit trail | The tamper-evident record of who did what |

## Community and licenses

| Term | Plain meaning |
|---|---|
| GPL | GNU General Public License, version 2 or later: the code's license |
| GFDL | GNU Free Documentation License 1.3 or later: the documentation's license |
| Pull request | A proposed change sent on GitHub ([Develop with Git](../develop-with-git.md)) |
| CLA | Contributor Licensing Agreement: signed once before a code contribution to the kernel is merged; by signing and submitting, the contributor assigns the copyright to 7x and gives up control: 7x may use, change, relicense and distribute the contribution in any way, without asking or paying, and it cannot be withdrawn; own extensions are not affected ([Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla)) |
| Issue | A report or proposal in a tracker ([Which tracker for which report](which-tracker-for-which-report.md)) |
| Fork, clone | Your copy of a repository on GitHub; a local copy with its history |
| Tag, release | A fixed, published version; tags never change |
| Share | The community portal at share.exponential.earth |
| eZpedia | The community encyclopedia |
| Advocate | A person who helps others find, use and contribute to Exponential ([What an advocate is](what-an-advocate-is.md)) |

**Demo mode** - How a public demo keeps its published administrator sign-in working: with `EXP_DEMO_MODE=1` nobody can change or reset an administrator password. Never on a real site. [Demo mode FAQ](../demo-mode-faq.md).

## Related pages

- [The glossary](../../glossary.md)
- [Naming rules quick card](naming-rules-quick-card.md)
- [Newcomer FAQ](newcomer-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
