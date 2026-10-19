---
title: Fact-checking guide
audience: press, advocate
category: press-media
order: 130
summary: A method for fact-checking any story, script or post about Exponential - mark every claim, classify it, find its primary source, check it at the source, record the date, and decide what to do with what cannot be verified - with the sources ranked and a worked example.
keywords: fact-checking, fact check, verification, verify, sources, primary source, claims, accuracy, checking a story, method, worked example
featured: true
type: help
---
# Fact-checking guide

This page is for fact-checkers, editors, reporters and creators. Exponential is easier to check than most software
because almost everything is public in its Git repository. This guide gives you a method that works for a news
story, a video script or a social post.

## Contents

1. [The method in six steps](#the-method-in-six-steps)
2. [Kinds of claims and where to check them](#kinds-of-claims-and-where-to-check-them)
3. [A worked example](#a-worked-example)
4. [What cannot be verified](#what-cannot-be-verified)
5. [Keeping a record](#keeping-a-record)

## The method in six steps

1. **Mark every claim.** Names, numbers, dates, versions, "first", "only", "faster", "free", quotes.
2. **Classify each one**: fact, self-description, opinion or quote ([Attributing statements](attributing-statements.md)).
3. **Find the primary source** with [Where the facts live, for the press](where-the-facts-live-for-press.md).
4. **Check it at the source**, not in a summary: open the file, the tag or the page.
5. **Record the source and the date** you checked it.
6. **Decide**: keep, correct, attribute, or cut. A claim without a source is cut or reported as unverified.

## Kinds of claims and where to check them

| Claim | Check at | Page |
|---|---|---|
| A version or release date | The Git tags | [Verifying dates](verifying-dates.md), [Verifying a release yourself](verifying-a-release-yourself.md) |
| A count | The method given with it, or recount | [Verifying numbers](verifying-numbers.md) |
| A quote | The original text or your recording | Verifying quotes |
| A product name | The naming rules | [Naming quick reference for news editors](naming-quick-reference-for-editors.md) |
| What a feature does | Its feature page in `doc/features/6.0/` and the changelog | [Feature stories: index](feature-stories-index.md) |
| A history fact | The history pages and the timelines | [Timeline for reporters](timeline-for-reporters.md) |
| An organization fact | The organization's own website and the two profile pages | [7x for reporters](7x-for-reporters.md), [The Exponential Foundation for reporters](the-foundation-for-reporters.md) |
| A license | LICENSE, COPYRIGHT.md, the package's composer.json | [Licenses explained for reporters](licenses-explained-for-reporters.md) |
| A comparison | Both products' own documentation, same criteria | [Comparisons done fairly](comparisons-done-fairly.md) |

## A worked example

Draft sentence:

```text
"Released last month, eZ Publish 6.0.15 brings an AI that writes and publishes articles and makes sites
ten times faster, says 7x, which has maintained the CMS since 1998."
```

| Claim | Check | Result |
|---|---|---|
| "Released last month" | `git tag -l 'v6.0.*' --sort=version:refname` | No v6.0.15 tag: in development |
| "eZ Publish 6.0.15" | Naming rules | The product is Exponential |
| "an AI that writes and publishes" | [expai feature page](../../features/6.0/extensions/expai.md) | Writes drafts only, never publishes |
| "ten times faster" | README, Velocity section | No comparison published; only the documented figures with their conditions |
| "says 7x" | Is there a statement? | No; the facts come from the documentation |
| "maintained since 1998" | [7x in three stages](7x-in-three-stages.md) | Founded 2001 as Brookins Consulting; took over the code base in 2023 |

Corrected sentence:

```text
"Exponential 6.0.15, which is in development, adds Exponential Intelligence, a writing assistant that
writes drafts but never publishes them, and Velocity, a web server written in PHP, according to the
project's changelog. 7x has led the project since 2023."
```

## What cannot be verified

Some questions have no public answer: usage numbers, customers, budgets, governance details, future dates
([What the sources do not say](what-the-sources-do-not-say.md)). Ask the organization, attribute the answer, and if
there is none, say that the information is not published. Never fill the gap with an estimate.

## Keeping a record

Keep a simple log per story; it makes corrections easy ([Corrections and updates](corrections-and-updates.md)):

```text
CLAIM                                SOURCE                                   CHECKED      RESULT
6.0.14 released 7 June 2026          git tag v6.0.14                          2026-10-18   ok
Foundation founded 19 March 2026     foundation.exponential.earth             2026-10-18   ok
"writes drafts only"                 doc/features/6.0/extensions/expai.md     2026-10-18   ok, attributed
```

## Related pages

- Fact-checking checklist
- [Common errors in coverage](common-errors-in-coverage.md)
- [Honesty and accuracy](../advocates/honesty-and-accuracy.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
