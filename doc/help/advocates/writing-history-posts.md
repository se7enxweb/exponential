---
title: Writing history posts
audience: advocate
category: advocacy
order: 54
summary: How to write "this month in Exponential history" and other history posts from the project's own chronicle and ledger - choose a month or theme, cite commits, explain what became possible, use the old name correctly - with a template.
keywords: history, this month in history, chronicle, ledger, commits, anniversary, timeline, eZ Publish years, storytelling, posts, template
type: help
---
# Writing history posts

This page is for advocates who like stories. Exponential has an unusually complete, public history: every change since
December 2023 is told month by month in the chronicle and recorded in the ledger, and the repository history goes
back to 2002. History posts show that the project is alive and explain why features exist.

## Contents

1. [The sources](#the-sources)
2. [Post ideas](#post-ideas)
3. [How to write one](#how-to-write-one)
4. [The template](#the-template)
5. [Rules for history](#rules-for-history)

## The sources

| Source | What it gives you |
|---|---|
| [The history](../../history/README.md) | "The story of every change ... since the se7enxweb era began in December 2023", month by month |
| [The ledger](../../history/ledger/README.md) | The complete record of commits per repository |
| [The ecosystem](../../history/ecosystem.md) | Changes across the related repositories |
| [Who is 7x?, Nearly thirty years](../who-is-7x.md#nearly-thirty-years-the-timeline) | The long timeline |
| [Who is 7x?, The eZ Publish years](../who-is-7x.md#the-ez-publish-years-1999-to-2021) | The years before 2021 |
| [Timeline](../who-is-the-exponential-foundation.md#timeline) | Key dates with sources |
| `git log` in a clone | Every commit since 2002 |

Each month page opens with **In short** and ends with related pages; every change carries its commit hash, which
readers can open with `git show <hash>` ([The history, In short](../../history/README.md#in-short)).

## Post ideas

| Idea | Start from |
|---|---|
| "This month two years ago" | The month page of two years ago |
| "How SQLite came to Exponential" | [January 2024](../../history/2024/2024-01a.md) |
| "The month the product got its name" | [August 2025](../../history/2025/2025-08.md) |
| "From PHP 7 to PHP 8.5" | [April 2025](../../history/2025/2025-04.md), [December 2025](../../history/2025/2025-12.md) |
| "The story of Velocity" | [Velocity chronicle](../../history/velocity/README.md) |
| "A year of releases" | [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases) |
| "The first outside contribution" | [December 2023](../../history/2023/2023-12.md) |

## How to write one

1. Read the month page fully, then the commits you will mention (`git show <hash>`).
2. Pick three changes that a reader can still use today.
3. For each: what changed, why it mattered, what you can do now. Link the feature page.
4. Add one sentence of context: what else happened in the project then.
5. End with a link to the month page so readers can read the rest.

## The template

```text
# This month in Exponential history: <Month Year>

<One sentence: what the month brought, from the "In short" of the month page.>

## <Change 1>
<What changed and why it mattered.> You can still use it: <feature page>. Commit: `<hash>`.

## <Change 2>
...

## Read more
The full month: <link to the month page>. Every change of that time is in the ledger: <link>.

<Disclosure line.>
```

## Rules for history

- **Use the chronicle's facts**, not your memory of the time.
- **The old name in the past tense only**: "while the software was still called eZ Publish"
  ([Writing about the history correctly](writing-about-the-history-correctly.md)).
- **Credit people only as the sources do.** Contributors named in the history and on
  [Who is 7x?](../who-is-7x.md#contributors-and-friends-of-the-project) may be named as there; do not add private details.
- **No speculation** about why someone did something, unless a source says it.
- **Respect earlier companies.** eZ Systems' work is part of the code; speak of it with respect
  ([Respecting other projects and companies](respecting-other-projects-and-companies.md)).

## Related pages

- [Talking points: longevity and history](talking-points-longevity-and-history.md)
- [Writing about a release](writing-about-a-release.md)
- [The history of Exponential](../../history/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
