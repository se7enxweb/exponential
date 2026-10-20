---
title: Briefing for technical press
audience: press
category: press-media
order: 123
summary: A background briefing for developer and IT media on Exponential - the stack, PHP versions, databases, engines, installation, the development process, APIs and the product family - every fact with its source, plus the questions technical readers will ask.
keywords: technical press, developer media, PHP, databases, SQLite, PostgreSQL, MongoDB, Velocity, FrankenPHP, Composer, Git, CI, REST API, architecture, briefing
type: help
---
# Briefing for technical press

This page is for developer magazines, IT news sites, technical podcasts and engineering bloggers. It gives you the
technical facts about Exponential in one place, each with its source, so you can write with confidence and spend
your interview time on design decisions. Facts are as of 18 October 2026; check them before you publish.

## Contents

1. [The stack in one table](#the-stack-in-one-table)
2. [Databases](#databases)
3. [Engines and web servers](#engines-and-web-servers)
4. [Getting and installing it](#getting-and-installing-it)
5. [How it is developed](#how-it-is-developed)
6. [Story angles for technical readers](#story-angles-for-technical-readers)
7. [Technical claims to avoid](#technical-claims-to-avoid)

## The stack in one table

| Topic | Fact | Source |
|---|---|---|
| What it is | "a professional PHP application framework with advanced CMS (content management system) functionality", with a "fully customizable and extendable content model" | [Exponential 6, the kernel](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) |
| Main features | Content classes, versions, multilingual, search, roles, approval and scheduled publication, multisite, e-commerce, workflows, templates, a headless REST API, clustering, a plugin API | Same section (from the README) |
| PHP | 8.0 is the oldest supported version on the development line; 8.1 to 8.5 supported; PHP 7.4 only up to 6.0.7 | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| Testing | CI runs the test suite on PHP 8.1 to 8.5 and checks that 8.0 boots, on every push to `main` and every pull request | Same section |
| License | GNU GPL v2 or later; documentation GNU FDL 1.3 or later | [Who is 7x?, Open source licenses](../who-is-7x.md#open-source-licenses) |
| Packages | The kernel is the Composer package `se7enxweb/exponential`; its `composer.json` requires 66 se7enxweb packages | [7x in numbers](../who-is-7x.md#7x-in-numbers) |
| Code age | Oldest commit 2 September 2002; 27,242 commits on `main` on 18 October 2026 | Same table |

## Databases

"Supported databases (Exponential 6.0.x), in order of preference: 1. SQLite 3 (the default) · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL" ([Database support](../database-support.md#the-supported-databases-in-order)).

| Point | Source |
|---|---|
| SQLite needs no database server; "the whole content repository is one file" | [Database support](../database-support.md#the-supported-databases-in-order) |
| MongoDB works "through an adapter that translates the kernel's SQL-shaped calls into MongoDB operations", with documented limits; it arrived with 6.0.14 | Same page; [6.0.14 changelog](../../changelogs/6.0/6.0.14.md) |
| Microsoft SQL Server has no driver; it is "known to be possible with additional work" | Same page |

## Engines and web servers

| Engine | Status in the documentation | Source |
|---|---|---|
| Exponential Velocity | "recommended for every stage; tier 1"; PHP 8.1+ | [Operating system support, Web servers](../os-support.md#web-servers) |
| Apache httpd 2.4 with PHP-FPM | tier 1, "the classic setup and shared hosting" | Same table |
| FrankenPHP | "production-ready alternative" | Same table |
| nginx with PHP-FPM | tier 2 | Same table |
| PHP's built-in server | "development only" | Same table |

Operating systems are documented in four tiers, from "Tested here" to "Not possible today"; tier 3 is "a reasoned
expectation, not a test result" ([Operating system support, The four tiers](../os-support.md#the-four-tiers)).
Velocity's own facts and figures, with their measuring conditions, are on [Covering Velocity releases](covering-velocity-releases.md).

## Getting and installing it

- **Git first.** "The recommended way is to get the kernel and every se7enxweb extension as Git clones with their full
  history" ([Git first](../who-is-the-exponential-foundation.md#git-first)).
- **Three ways to get the code**: `git clone` plus `composer install` (recommended), `composer create-project`, or
  `composer require` ([Getting the code](../../install/03-getting-the-code.md#32-three-ways-to-get-the-code)).
- **One-command install** on the development line: `./console exp:install` builds a complete site on SQLite
  ([Install in one command](../../features/6.0/install-in-one-command.md)).
- **Published tags never change** ([Published versions never change](published-versions-never-change.md)).

## How it is developed

| Practice | Source |
|---|---|
| Commit messages start with `Added:`, `Updated:`, `Fixed:`, `Removed:` or `Renamed:`, checked by a hook and CI | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| "Every pull request is merged, and its commits are kept as the contributor wrote them" | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows) |
| Administration templates carry no inline styles, so a strict Content Security Policy can be enforced | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), [Security headers](../who-is-the-exponential-foundation.md#security-headers-and-the-content-security-policy) |
| Every change since December 2023 is explained in the history and recorded in the ledger | [The history pages as a source](the-history-pages-as-a-source.md) |

## Story angles for technical readers

| Angle | Start from |
|---|---|
| A PHP code base from 2002 on PHP 8.5 | [The 6.0.x releases](../who-is-7x.md#the-60x-releases), [December 2025](../../history/2025/2025-12.md) |
| SQLite as the default database of a CMS | [Database support](../database-support.md) |
| A web server written in PHP | [Covering Velocity releases](covering-velocity-releases.md) |
| An AI assistant that only writes drafts | [Talking points: Exponential Intelligence](../advocates/talking-points-exponential-intelligence.md) |
| Merging every pull request | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows) |

## Technical claims to avoid

| Avoid | Because |
|---|---|
| "Runs on any hosting" | Operating systems and servers are documented in tiers; shared hosting has limits |
| "Faster than X" | No published comparison exists ([Comparisons done fairly](comparisons-done-fairly.md)) |
| "A fork of Ibexa" or "by eZ Systems" | Not associated with either ([Who is 7x?, In short](../who-is-7x.md#in-short)) |
| "Built on Symfony" (for Exponential 6) | The Symfony line is Exponential Platform ([Covering Exponential Platform releases](covering-platform-releases.md)) |

## Related pages

- [Briefing for business press](briefing-for-business-press.md)
- [Talking points for developers](../advocates/talking-points-for-developers.md)
- [Preparing a briefing](preparing-a-briefing.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
