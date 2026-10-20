---
title: Reading a changelog
audience: press
category: press-media
order: 102
summary: How an Exponential changelog page is built - header, In short, Upgrading, Added, Updated, Removed, Renamed, commit hashes, the uncounted changes - and how to turn its lines into accurate sentences without overstating them.
keywords: changelog, release notes, reading, Added, Updated, Removed, Renamed, commit, hash, In short, upgrading, translation, plain language
type: help
---
# Reading a changelog

This page is for reporters and creators who open an Exponential changelog for the first time. The changelogs are
written for people who run sites, so they are precise but dense. Here is how each part works and how to translate it
into plain language without adding anything the page does not say.

## Contents

1. [The parts of a changelog page](#the-parts-of-a-changelog-page)
2. [Commit hashes](#commit-hashes)
3. [From changelog line to sentence](#from-changelog-line-to-sentence)
4. [Words that need care](#words-that-need-care)
5. [Reading the development line](#reading-the-development-line)

## The parts of a changelog page

Take [the 6.0.14 changelog](../../../changelogs/6.0/6.0.14.md) as the model. Every released 6.0.x page has the same parts.

| Part | Example from 6.0.14 | What it tells you |
|---|---|---|
| Header | "Released 2026-06-07. Tag `v6.0.14`." | The date and the tag; cite both |
| Who should read it | "Read this page if you want to run on MongoDB ..." | The audience the release matters to most |
| In short | "MongoDB 8 is a database next to MySQL, PostgreSQL and SQLite ..." | The headline, written by the project; the safest summary to quote |
| Upgrading | "No action needed for MySQL, PostgreSQL and SQLite sites." | What a site owner must do |
| Added | New files, features, scripts | Something that did not exist before |
| Updated | Changes and fixes to what exists | Improvements, including bug fixes |
| Removed, Renamed | Things taken out or given new names | Possible work for site owners |
| Not listed above | "12 changes of this release bring no separate user benefit ..." | Changes that are counted but not described |
| Related pages | Previous and next release, month pages of the history | Where to read the story |

The four kinds Added, Updated, Removed and Renamed come from the commit message rules in
[CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), which also lists `Fixed:`; the changelogs fold fixes into Updated
(see the index of [extension release notes](../../../changelogs/extensions/README.md): "Updated (fixes included)").

## Commit hashes

Each line ends with one or more short hashes in backticks, for example `af5761fdd8`. A hash names the exact change in
the Git history. You do not need to print them, but they let you or your readers check a claim:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git show --stat af5761fdd8
```

On GitHub the same change opens at `https://github.com/se7enxweb/exponential/commit/<hash>`. If you print a hash,
copy it; never type it from memory.

## From changelog line to sentence

| Changelog line (6.0.14) | Accurate sentence | Overstatement to avoid |
|---|---|---|
| "The setup wizard has a database name field, to authenticate against a specific database." | "The installer can now connect to a named database." | "Installation was rebuilt." |
| "The PostgreSQL setup wizard can be run again on an existing database." | "The PostgreSQL installer can be rerun on an existing database." | "PostgreSQL support is new." (It was not.) |
| "Default installations use package version 6.0.14 instead of 6.0.10." | "New installations start from the 6.0.14 package." | "Everyone is upgraded automatically." |

Rules of thumb:

- Keep the scope: if the line names one database, do not generalize to all.
- Keep the tense: a fix makes something work; it does not prove the thing was broken everywhere.
- Prefer the "In short" wording when you summarize; quote it with quotation marks and the link
  ([Quotes policy](quotes-policy.md)).

## Words that need care

| Word in a changelog | What it means here |
|---|---|
| Updated | Changed or fixed; it is not automatically a new feature |
| Deprecated | Still works, planned to go away; not removed yet |
| Removed | Gone from this version on |
| Hardening | Security improvements; describe them as the changelog does, never as "now secure" ([Covering security fixes](covering-security-fixes.md)) |
| Required packages | The versions of other packages the kernel needs; changes there may be news for developers |
| Behavior change notes | Pages under `doc/eng-us/bc/6.0/` that explain what to check when upgrading |

## Reading the development line

The [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) is different: it begins "Not released yet", is organized by
period instead of by kind, and is long ("four months of work"). Read the "How this page is organised" list at its top
and pick one period. Everything on it must be reported as "in development"
([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).

## Related pages

- [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md)
- [Covering a release](covering-a-release.md)
- [Verifying a release yourself](verifying-a-release-yourself.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
