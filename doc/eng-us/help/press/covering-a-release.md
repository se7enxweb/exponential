---
title: Covering a release
audience: press
category: press-media
order: 100
summary: How journalists and creators cover a new version of Exponential - confirm the tag, read the changelog and the history, pick the news for your audience, name the version correctly, and never print a date for a release that has not happened.
keywords: release, new version, changelog, tag, release notes, coverage, news, what is new, 6.0.14, 6.0.15, version, upgrade
featured: true
type: help
---
# Covering a release

This page is for journalists, news editors, bloggers and video creators who want to report on a new version of
Exponential. It walks you from "I heard there is a new version" to a story whose every fact has a source. The rules
are the same ones the project applies to itself: a release exists when its tag exists, and a published version is
never changed afterwards.

## Contents

1. [What counts as a release](#what-counts-as-a-release)
2. [The state on 18 October 2026](#the-state-on-18-october-2026)
3. [Five steps from tip to story](#five-steps-from-tip-to-story)
4. [Choosing the news for your audience](#choosing-the-news-for-your-audience)
5. [What a release story must not say](#what-a-release-story-must-not-say)
6. [Related pages](#related-pages)

## What counts as a release

A version of Exponential is released when its Git tag (for example `v6.0.14`) is published in the repository
[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential). The tag is also the version Composer
installs from Packagist. Work that is merged but not tagged belongs to the **development line**, which you may
report on only if you say so plainly ([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).

The project states that "A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag
reaches you as the next release, not as a corrected copy of the old one"
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). For you this means a release story stays true: the files behind a version
number will not change after you write about them ([Published versions never change](published-versions-never-change.md)).

## The state on 18 October 2026

| Fact | Value | Source |
|---|---|---|
| Newest released version of Exponential | 6.0.14, tag `v6.0.14`, 7 June 2026 | [6.0.14 changelog](../../../changelogs/6.0/6.0.14.md), [release overview](../../release-overview) |
| First release of the 6.0 line | 6.0.0, 1 January 2024 | [6.0.0 changelog](../../../changelogs/6.0/6.0.0.md) |
| Number of 6.0.x releases | 15 (6.0.0 to 6.0.14) | [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases) |
| The development line | 6.0.15, not released, no date | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |

Check these again on the day you publish; a newer tag may exist by then
([Verifying a release yourself](verifying-a-release-yourself.md)).

## Five steps from tip to story

1. **Confirm the tag.** Look at [GitHub Releases](https://github.com/se7enxweb/exponential/releases) or run the
   commands on [Verifying a release yourself](verifying-a-release-yourself.md). No tag, no release.
2. **Read the changelog of that version.** Each 6.0.x version has a page in `doc/changelogs/6.0/`, for example
   [6.0.14](../../../changelogs/6.0/6.0.14.md). Read "In short" first, then "Upgrading"
   ([Reading a changelog](reading-a-changelog.md)).
3. **Read the history for context.** The [history](../../history/README.md) tells month by month what each change
   makes possible, with the commit behind it ([The history pages as a source](the-history-pages-as-a-source.md)).
4. **Open the feature page** of each item you want to describe. The changelog links them.
5. **Ask for comment if you want a quote.** Quotes come only from 7x or the Exponential Foundation, in writing
   ([Quotes policy](quotes-policy.md), [Requesting an interview](requesting-an-interview.md)).

## Choosing the news for your audience

| Your audience | What to look for in the changelog | Example from 6.0.14 |
|---|---|---|
| General or business readers | One headline change and what it means | "MongoDB 8 is a database next to MySQL, PostgreSQL and SQLite" |
| Technical press | Databases, PHP versions, engines, APIs, upgrade steps | The setup wizard's database name field; portable SQL for SQLite and PostgreSQL |
| Site owners and administrators | The "Upgrading" section | "No action needed for MySQL, PostgreSQL and SQLite sites" |
| Developers | Added files, scripts, template operators | `bin/shell/generatefilelist.sh` and `share/filelist.md5` |

All four examples are quoted from the [6.0.14 changelog](../../../changelogs/6.0/6.0.14.md). Templates for each kind of
story are on [Release story templates](release-story-templates.md).

## What a release story must not say

| Do not write | Write instead |
|---|---|
| "6.0.15 will ship on ..." | "6.0.15 is in development; the project has not announced a date." |
| "Version 6.0.14 was updated to fix ..." | "The fix is in the next version" (published versions never change) |
| "eZ Publish 6.0.14" | "Exponential 6.0.14" ([Brand and naming rules](../business/brand-and-naming-rules.md#the-product-exponential)) |
| A user count, market share or speed multiple | Leave it out; the sources do not give one ([Claims you must not make](../advocates/claims-you-must-not-make.md#numbers)) |
| A quote you composed from the notes | Quote the changelog word for word with its link, or ask for a statement |

Before you publish, run the [Release coverage checklist](release-coverage-checklist.md).

## Related pages

- [Release coverage checklist](release-coverage-checklist.md)
- [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md)
- [How version numbers work](how-version-numbers-work.md)
- [Writing about a release](../advocates/writing-about-a-release.md) (the advocate's version of this page)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
