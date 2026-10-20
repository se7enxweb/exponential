---
title: Fact sheet for journalists
audience: press, advocate, sales-marketing
category: press-media
order: 4
summary: A one-page fact sheet about Exponential for newsrooms - what it is, who leads and operates it, the history in dates, releases, license, platforms, headline features, security process, languages and contacts - with a source for every line and the date the facts were checked.
keywords: fact sheet, facts, figures, backgrounder, newsroom, one pager, key facts, Exponential, 7x, Exponential Foundation, release, license, GPL, PHP, databases, contacts
featured: true
type: help
---
# Fact sheet for journalists

This page is for reporters and editors who need the facts in one place. Each line has a source below. The facts were
checked on **19 October 2026**. Before you publish, check the lines that change with releases (marked *) on the
pages linked in [Sources](#sources).

## Contents

1. [The fact sheet](#the-fact-sheet)
2. [Sources](#sources)
3. [Lines that change](#lines-that-change)
4. [Using the fact sheet](#using-the-fact-sheet)

## The fact sheet

```text
EXPONENTIAL - FACT SHEET FOR THE PRESS                                  facts checked 19 October 2026

PRODUCT        Exponential: a free and open source PHP content management system and application framework
               "with advanced CMS functionality"; its most notable feature is a fully customizable content model.
WHAT IT DOES   Structured content, versions and approval, multisite, many languages (incl. right to left),
               built-in shop, workflows, REST API, page builder (Exponential Layouts).
LEADS IT       7x, a North American web design, development, support and hosting company (formerly
               Brookins Consulting); took over leadership of the project in 2023.
OPERATES IT    The Exponential Foundation, a 501(c)(3) nonprofit founded 19 March 2026 in Seattle,
               Washington, USA.
COPYRIGHT      "7x & Exponential Foundation" (1998 - 2026); "eZ Systems AS" (1999 - 2014) stays in the code.
HISTORY        Code base developed since 1999, first as eZ Publish, a product its creator discontinued after
               almost 18 years. 7x: idea 2014 (se7enx.com registered), project start 2022, launch December 2023.
               Renamed Exponential on 11/12 August 2025.
RELEASES *     6.0.0 on 1 January 2024 ... 6.0.14 on 7 June 2026 (15 releases). Current line: 6.0.15,
               in development (lib/version.php), no release tag on 19 October 2026.
LICENSE        Software: GNU GPL v2 or later, free of charge. Documentation: GNU FDL 1.3 or later.
               Exponential Velocity: MIT.
RUNS ON *      PHP 8.0 - 8.5. SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL.
               Linux, BSD, macOS, Windows (documented tiers). Apache, Nginx and others, or Velocity.
NEW IN 6.0.15* Exponential Velocity (PHP web server), Exponential Layouts (page builder), Exponential
               Intelligence (writes drafts only), admin4 design, audit trail, help system.
SECURITY       Private reports to security@se7enx.com; "a first answer within one business day";
               fixes ship in the next 6.0.x release; published versions are never changed.
LANGUAGES *    268 locale files (40 right to left); 33 administration translations, 19 complete (19 Oct).
NOT            Not associated with eZ Systems or Ibexa.
CODE           github.com/se7enxweb/exponential        Packages: Composer / Packagist, vendor se7enxweb
WEB            exponential.earth   foundation.exponential.earth   se7enx.com   doc.exponential.earth
PRESS          info@se7enx.com   press kit: exponential.earth/about/press-kit
```

## Sources

| Line | Source |
|---|---|
| Product, what it does | [README.md](https://github.com/se7enxweb/exponential/blob/main/README.md), "What is Exponential?" and "Main Exponential features"; [Exponential 6, the kernel](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) |
| Leads it | <https://se7enx.com/about>; README, "Who is 7x"; [Who is 7x?](../who-is-7x.md#in-short) |
| Operates it | <https://foundation.exponential.earth/>; [What the Foundation is](../who-is-the-exponential-foundation.md#what-the-foundation-is) |
| Copyright | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| History | [Who is 7x?, timeline](../who-is-7x.md#nearly-thirty-years-the-timeline); [7x in three stages](7x-in-three-stages.md) |
| Releases | [The 6.0.x releases](../who-is-7x.md#the-60x-releases); `git tag -l 'v6.0.*' --sort=version:refname` |
| License | README, "License"; [Open source licenses](../who-is-7x.md#open-source-licenses) |
| Runs on | [PHP versions](../who-is-the-exponential-foundation.md#php-versions); [Database support](../database-support.md); [Operating system support](../os-support.md) |
| New in 6.0.15 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md) |
| Security | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md); [Security fixes](../who-is-the-exponential-foundation.md#security-fixes) |
| Languages | [Languages in Exponential](../../languages.md) |
| Not associated | README, "Exponential Project Notice : 2025.08.12" |
| Press contact | footer of <https://exponential.earth/>; [Press contacts](press-contacts.md) |

## Lines that change

| Line | Check it with |
|---|---|
| Releases | [The 6.0.x releases at a glance](the-6-0-x-releases-at-a-glance.md), or the tags on GitHub |
| Runs on | The README section "Requirements" |
| New in 6.0.15 | The changelog; once 6.0.15 is tagged, it is no longer "in development" |
| Languages | [Languages in Exponential](../../languages.md) |

## Using the fact sheet

- Copy any line; name "Exponential documentation" or link this page as the source.
- Keep the date: "as of 19 October 2026". If you update a line yourself, change the date and your source.
- Do not add figures that are not here. Counts of installations, users or customers are not published
  ([What the sources do not say](what-the-sources-do-not-say.md)).
- The advocates keep a shorter version for handouts: [One-page fact sheet](../advocates/one-page-fact-sheet.md).

## Related pages

- [Exponential in brief](exponential-in-brief.md)
- [Exponential by the numbers](exponential-by-the-numbers.md)
- [Fact-checking guide](fact-checking-guide.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
