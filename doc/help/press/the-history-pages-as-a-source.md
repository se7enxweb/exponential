---
title: The history pages as a source
audience: press
category: press-media
order: 113
summary: How reporters use the Exponential history - the month-by-month chronicle, the ledger of every commit and the coverage page - to date a change, find its context and cite it, with the limits of what the history can prove.
keywords: history, chronicle, month pages, ledger, coverage, commits, dates, context, citation, ecosystem, extensions, Velocity
type: help
---
# The history pages as a source

This page is for journalists and long-form writers who want context: when something changed, why, and what it
made possible. The repository carries a written history of every change since December 2023. It is one of the
strongest sources you have, because each statement in it names a commit that anyone can open.

## Contents

1. [What the history is](#what-the-history-is)
2. [The three parts](#the-three-parts)
3. [Using it in a story](#using-it-in-a-story)
4. [How to cite it](#how-to-cite-it)
5. [What the history cannot prove](#what-the-history-cannot-prove)

## What the history is

The [history of Exponential](../../history/README.md) describes itself as "the story of every change made to
Exponential and to the repositories around it since the se7enxweb era began in December 2023". It starts with the
month "a dormant code base changed hands" ([December 2023](../../history/2023/2023-12.md)). For the years before, use
[Who is 7x?, The eZ Publish years, 1999 to 2021](../who-is-7x.md#the-ez-publish-years-1999-to-2021).

## The three parts

| Part | What it is | Best for |
|---|---|---|
| [The chronicle](../../history/README.md#the-chronicle-month-by-month) | One page per month (busy months split in two), each opening with "In short" | Context and the story of a period |
| [The ledger](../../history/ledger/README.md) | "the complete, machine-made record of every commit", with date, kind, change, files and the release tag that first contained it | Exact dates and which release contains a change |
| [The coverage page](../../history/coverage.md) | Proof that every change is explained, listed as small, or marked as without user benefit | Showing that nothing was left out |

Beside the main chronicle there are chronicles for the [platform ecosystem](../../history/ecosystem.md), the
[extensions](../../history/extensions/README.md) and [Velocity](../../history/velocity/README.md).

## Using it in a story

| Your question | Where to look | Example |
|---|---|---|
| When did the product get its name? | The month page | "The product was renamed to Exponential on 12 August 2025" ([August 2025](../../history/2025/2025-08.md)) |
| When did the fork start? | The first month page | "The first outside change of the fork was merged on 11 December 2023 (upstream pull request #40)" ([history](../../history/README.md)) |
| When did a technology arrive? | The "story so far" list | "June 2026: MongoDB 8 as a database, and 6.0.14" |
| How big was a period? | The month's own count | August 2026 brought "149 changes to the root repository on the 6.0.15 line, and no release tag" ([August 2026](../../history/2026/2026-08.md)) |
| Which commit did it? | The commit hash on the month page | `git show <hash>` in a clone |

Each month page ends with related pages: the feature pages, the specifications, the notes for upgrading and the
changelog of the release. Follow them to describe a feature correctly.

## How to cite it

```text
According to the project's documented history (doc/history, "August 2025"), the product was renamed to
Exponential on 12 August 2025. https://github.com/se7enxweb/exponential/blob/main/doc/history/2025/2025-08.md
```

- Name the month page, not just "the history".
- If you print a count from a month page, give the month and say what was counted (changes to which repository).
- Quote the month pages' sentences in quotation marks; they are free documentation and may be quoted with a link
  ([Free documentation](../free-documentation.md)).

## What the history cannot prove

| The history shows | It does not show |
|---|---|
| What was changed, when, and in which repository | How many sites use the change |
| The release that first contained a change | When a given site upgraded |
| The account that made a commit | Who the person behind an account is, unless a public page says so |
| Counts of changes | Effort, money or hours spent |

Two cautions:

- **Release status.** A month page can describe work on the development line. October 2026's headline says "no
  release tag; they belong to the 6.0.15 line". Do not turn a month's work into a release
  ([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).
- **Upstream history.** Forked repositories carry older history that the se7enxweb team did not make. The Who is 7x?
  page counts "6,556 ledger changes in 53 repositories since December 2023, 1,037 of them made by the se7enxweb team,
  the rest upstream history that the forks carry along" ([Who is 7x?](../who-is-7x.md#stewardship-from-a-dormant-code-base-to-exponential-6)).
  Do not credit 7x with all of it.

## Related pages

- [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md)
- [Verifying dates](verifying-dates.md)
- [Writing about the history correctly](../advocates/writing-about-the-history-correctly.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
