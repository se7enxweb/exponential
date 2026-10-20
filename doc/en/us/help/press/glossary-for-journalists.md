---
title: Glossary for journalists
audience: press, advocate
category: press-media
order: 170
summary: Plain-language definitions of the terms that appear in stories about Exponential - the products and organizations, the history, the technology (content model, siteaccess, extensions, web servers, databases), open source and licensing, security and releases - each with a link to the page that explains it in depth.
keywords: glossary, terms, definitions, dictionary, jargon, plain language, CMS, content model, siteaccess, extension, fork, GPL, GFDL, CSP, Velocity, legacy, DXP
featured: true
type: help
---
# Glossary for journalists

This page is for journalists and their readers. Each term is explained in one or two plain sentences, with a link to
the page that goes deeper. The project's own glossaries are [the glossary](../../glossary.md) for technical terms and
the [Foundation's glossary](../who-is-the-exponential-foundation.md#glossary) for the family and the history.

## Contents

1. [Products and organizations](#products-and-organizations)
2. [History](#history)
3. [Content and editing](#content-and-editing)
4. [Running a site](#running-a-site)
5. [Open source and licenses](#open-source-and-licenses)
6. [Security](#security)
7. [Releases and development](#releases-and-development)

## Products and organizations

| Term | Meaning |
|---|---|
| **Exponential** | A free, open source PHP content management system and application framework. [Exponential in brief](exponential-in-brief.md) |
| **Exponential 6, 6.0.x** | The current major line, since 1 January 2024 |
| **7x** | The company that leads Exponential; formerly Brookins Consulting. [7x for reporters](7x-for-reporters.md) |
| **se7enxweb** | 7x's name on GitHub and Packagist |
| **the Exponential Foundation** | The nonprofit that operates and funds the project, founded 19 March 2026. [The Exponential Foundation for reporters](the-foundation-for-reporters.md) |
| **Exponential Velocity (vc)** | A web server written in PHP that keeps the application loaded between requests. [Story: Exponential Velocity](story-velocity.md) |
| **Exponential Intelligence (expai)** | A writing assistant in the administration that writes drafts only. [Story: Exponential Intelligence](story-exponential-intelligence.md) |
| **Exponential Layouts (explayouts)** | A visual page builder: layouts, zones, blocks and rules. [Story: Exponential Layouts, the page builder](story-exponential-layouts.md) |
| **Exponential Platform** | The Symfony-based line: 3.2.9, Legacy, Nexus and DXP. [The product family for reporters](the-product-family-for-reporters.md) |
| **Legacy** | Exponential 6 running beside a Symfony platform; not "old" |
| **DXP** | Digital experience platform; here the Symfony-only Exponential Platform DXP |
| **AdminUI** | An alternative administration, a port of the Netgen AdminUI |
| **admin4** | The newest administration design, with light and dark themes |

## History

| Term | Meaning |
|---|---|
| **eZ Publish** | The software's name while eZ Systems developed it; a product its creator discontinued after almost 18 years. History only. [Writing about the eZ Publish years](writing-about-the-ez-publish-years.md) |
| **eZ Systems, Ibexa** | The original developer and its successor company; not associated with Exponential. [Not associated with eZ Systems or Ibexa](not-associated-with-ez-systems-or-ibexa.md) |
| **eZ Platform, Ibexa DXP OSS** | The Symfony-based successors of eZ Publish |
| **Fork** | A copy of a code base continued separately; Exponential's repository is a GitHub fork |
| **Stewardship** | The project's word for 7x's care of the code base since December 2023 |
| **LoveStack** | Mugo Web's distribution of the legacy kernel, whose features 6.0.15 rebuilt with credit |

## Content and editing

| Term | Meaning |
|---|---|
| **Content model** | The types of content a site defines and their fields |
| **Content class, object** | A type of content (class) and one item of it (object) |
| **Version, draft** | Every edit is a new version; a draft is one not yet published. [Story: editing content](story-editing-content.md) |
| **Section, state** | Ways to group content for permissions and workflow |
| **Siteaccess** | One way into an installation: a site, a language or the administration |
| **Multisite** | Several sites from one installation |
| **Extension** | A package that adds features. [Story: the extension ecosystem](story-extension-ecosystem.md) |

## Running a site

| Term | Meaning |
|---|---|
| **Web server** | The program that answers browser requests: Apache, Nginx, Velocity |
| **PHP-FPM** | The classic way to run PHP behind a web server |
| **FrankenPHP** | Caddy with PHP built in, an alternative engine |
| **SQLite** | A database in a file, needing no server; Exponential's default. [Story: SQLite by default](story-sqlite-by-default.md) |
| **Composer, Packagist** | PHP's package manager and its public directory |
| **Git** | The version control system the project uses; "Git first" installs with full history |

## Open source and licenses

| Term | Meaning |
|---|---|
| **Open source, free software** | Software anyone may use, study, change and share |
| **GPL** | GNU General Public License; the software's license, version 2 or later |
| **GFDL** | GNU Free Documentation License; the documentation's license, 1.3 or later |
| **MIT** | A short permissive license; Velocity's license |
| More | [Licenses explained for reporters](licenses-explained-for-reporters.md) |

## Security

| Term | Meaning |
|---|---|
| **CSP** | Content Security Policy: tells the browser which scripts a page may run. [Story: the Content Security Policy](story-content-security-policy.md) |
| **Audit trail** | A tamper-evident record of what happened in the administration |
| **Two-step sign-in (2FA)** | A second factor, such as a code from an app, at login |
| **Private disclosure** | Reporting a vulnerability privately so it is fixed before details are public. [Covering security fixes](covering-security-fixes.md) |

## Releases and development

| Term | Meaning |
|---|---|
| **Tag** | A named, fixed point in the code history; a release |
| **Changelog** | The list of changes of a release. [Reading a changelog](reading-a-changelog.md) |
| **Main branch** | Where development happens; public at all times |
| **Pull request** | A proposed change; the project merges every pull request |
| **CLA** | Contributor Licensing Agreement: what a kernel contributor signs once before the first merge; by signing and submitting, the contributor gives up control: 7x may use, change, sublicense, relicense and distribute the contribution in any way, perpetually and irrevocably, without asking or paying; formally the CLA is a license (§4). [Copyright and control of your contribution (CLA)](../../../../../CONTRIBUTING.md#copyright-and-control-of-your-contribution-cla) |
| **In development** | Not released yet, such as 6.0.15 on 18 October 2026 |

## Related pages

- [Reporter FAQ](reporter-faq.md)
- [The glossary](../../glossary.md)
- [Glossary for advocates](../advocates/glossary-for-advocates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
