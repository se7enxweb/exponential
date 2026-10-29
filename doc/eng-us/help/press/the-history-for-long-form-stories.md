---
title: The history for long-form stories
audience: press, advocate
category: press-media
order: 7
summary: Material for features and long reads about Exponential's history - the arc from eZ Publish to Exponential 6, the chapters with their sources, the documented history pages month by month, angles that hold up, and the lines a long-form writer must not cross.
keywords: history, long form, feature story, long read, documentary, eZ Publish years, stewardship, fork, open source survival, history pages, ledger, chronicle, Velocity chronicle
type: help
---
# The history for long-form stories

This page is for feature writers, documentary makers and podcasters who tell the longer story. Exponential's history
is unusually well documented: the repository keeps every commit since 2002, and the project has written a month-by-month
history of every change since December 2023. This page shows you the arc, the chapters and where each one is told.

## Contents

1. [The arc in five sentences](#the-arc-in-five-sentences)
2. [The chapters and their sources](#the-chapters-and-their-sources)
3. [The history pages](#the-history-pages)
4. [Angles that hold up](#angles-that-hold-up)
5. [Lines not to cross](#lines-not-to-cross)
6. [Checking a detail](#checking-a-detail)

## The arc in five sentences

1. From 1999 the Norwegian company eZ Systems developed a content management system called eZ Publish, and hundreds
   of people contributed to it over the years.
2. eZ Publish was a product that its creator discontinued after almost 18 years of service; its last releases are
   listed until 2021, and its successors took other, Symfony-based roads.
3. Brookins Consulting, an American company that had worked with the software since its first versions, became 7x:
   the idea in 2014, the project in 2022, the launch in December 2023.
4. 7x released 6.0.0 on 1 January 2024 and fourteen more 6.0.x releases until June 2026, renamed
   the product Exponential in August 2025; in March 2026 the nonprofit Exponential Foundation was founded for the project.
5. In 2026 the line added a PHP web server of its own, a page builder, a writing assistant and a help system, while
   keeping sites built over two decades running.

Sources: [Who is 7x?](../who-is-7x.md), [The history of Exponential](../../history/README.md).

## The chapters and their sources

| Chapter | Years | What to read |
|---|---|---|
| The eZ Publish years | 1999 to 2021 | [The eZ Publish years, 1999 to 2021](../who-is-7x.md#the-ez-publish-years-1999-to-2021); the old changelogs in [Changelogs](../../../changelogs/README.md) |
| The company that stayed | 2000 to 2023 | [From Brookins Consulting to 7x](../who-is-7x.md#from-brookins-consulting-to-7x-in-three-stages); [7x in three stages](7x-in-three-stages.md) |
| The month the code changed hands | December 2023 | [December 2023](../../history/2023/2023-12.md); [Stewardship](../who-is-7x.md#stewardship-from-a-dormant-code-base-to-exponential-6) |
| The 6.0.x releases | 2024 to 2026 | [The 6.0.x releases](../who-is-7x.md#the-60x-releases) |
| A name of its own | August 2025 | [August 2025](../../history/2025/2025-08.md); [Why it is called Exponential](why-it-is-called-exponential.md) |
| A nonprofit for the project | March 2026 | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| A web server written in PHP | July to September 2026 | [Velocity chronicle](../../history/velocity/README.md) |
| The 6.0.15 line | 2026 | [October 2026](../../history/2026/2026-10.md); [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |

## The history pages

The [history](../../history/README.md) tells every change since December 2023, month by month. It opens: "This is
where the story of Exponential 6 begins: the month a dormant code base changed hands" ([December 2023](../../history/2023/2023-12.md)).

| Page | What it gives a writer |
|---|---|
| [The history](../../history/README.md) | The month pages, in order |
| [The ecosystem overview](../../history/ecosystem.md) | The repositories and how the work spreads over them |
| [The ledgers](../../history/ledger/README.md) | Every recorded change, one line each |
| [Coverage](../../history/coverage.md) | Proof that no change is missing from the ledgers |
| [Velocity chronicle](../../history/velocity/README.md) | The story of the web server from its first commit |

## Angles that hold up

| Angle | Why it holds up | Source |
|---|---|---|
| "The CMS that was kept alive" | The README states it "made it beyond it's end of life in 2021 and survived" | README, "Exponential Project Status" |
| Open source as insurance | The GPL let a new steward continue the code | [Licenses explained for reporters](licenses-explained-for-reporters.md) |
| Twenty-plus years of one code base | Commits since 2002, copyright since 1999 | [Who is 7x?, 7x in numbers](../who-is-7x.md#7x-in-numbers) |
| A small company and a new nonprofit | Two organizations with one project | [Two organizations, one project](two-organizations-one-project.md) |
| Old code, current PHP | PHP 8.0 to 8.5 (PHP 7.4 usable without support) supported | [Story: current PHP](story-current-php.md) |
| Credit across companies | Netgen, Mugo Web and CJW Network's work credited by name | [Crediting Netgen, Mugo Web and CJW Network](crediting-other-projects.md) |

## Lines not to cross

- **No drama the sources do not show.** The sources do not describe disputes between 7x and eZ Systems or Ibexa;
  the rename was made "At the specific request of Ibexa" and that is all they say ([Why it is called Exponential](why-it-is-called-exponential.md)).
- **No implied continuity of companies.** Exponential continues a code base, not a company; it is "not associated
  with" eZ Systems or Ibexa ([Not associated with eZ Systems or Ibexa](not-associated-with-ez-systems-or-ibexa.md)).
- **No invented people or quotes.** Name people only as the public sources name them, and quote only what is
  published or given to you ([Quotes policy](quotes-policy.md)).
- **No guessed numbers.** Use the counted figures with their dates ([Exponential by the numbers](exponential-by-the-numbers.md)).

## Checking a detail

Most details can be checked in a clone of the repository:

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
git log --reverse --format='%ad %s' | head -3        # the oldest commits
git log --since=2023-12-01 --until=2023-12-31 --oneline | tail -5   # the first month of the stewardship
git tag -l 'v6.0.*' --sort=version:refname           # the releases, in order
```

## Related pages

- [Timeline for reporters](timeline-for-reporters.md)
- [Writing about the eZ Publish years](writing-about-the-ez-publish-years.md)
- [Story: a code base kept alive](story-longevity.md)
- [Writing history posts](../advocates/writing-history-posts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
