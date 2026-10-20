---
title: "Talking points: longevity and history"
audience: advocate
category: advocacy
order: 24
summary: How to use Exponential's long history as a strength - a code base kept since 1999, a public history since 2002, 7x's stewardship since 2023, the Foundation since 2026 - with sourced points, the timeline and the honest limits about the future.
keywords: talking points, longevity, history, timeline, sustainability, eZ Publish years, 7x, Brookins Consulting, Exponential Foundation, releases, ledger, future, survival
type: help
---
# Talking points: longevity and history

This page is for advocates who meet the question "will it still be here?" and for those who like to tell the history.
Exponential's history is long and public. Use it as evidence, not as nostalgia, and never promise the future.

## Contents

1. [The points](#the-points)
2. [A timeline you can show](#a-timeline-you-can-show)
3. [How to frame the old name](#how-to-frame-the-old-name)
4. [The honest limits](#the-honest-limits)
5. [Make it concrete](#make-it-concrete)

## The points

| Say | Source |
|---|---|
| "The copyright notice starts in 1998 and 1999; the code base has been developed for more than 25 years." | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| "The repository history goes back to 2 September 2002, and anyone can read it." | [Who is 7x?, 7x in numbers](../who-is-7x.md#7x-in-numbers) |
| "'Exponential has made it beyond it's end of life in 2021 and survived.'" (quoted as written) | [Who is 7x?, In short](../who-is-7x.md#in-short) |
| "7x took over leadership in 2023 and has published fifteen 6.0.x releases since 1 January 2024." | [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases) |
| "A nonprofit, the Exponential Foundation, was founded in March 2026 to operate the project." | [What the Foundation is](../who-is-the-exponential-foundation.md#what-the-foundation-is) |
| "Every change since December 2023 is told month by month in the history, and recorded in ledgers." | [The history](../../history/README.md), [Documentation](../who-is-the-exponential-foundation.md#documentation) |
| "The GPL means the code can outlive any company. It already outlived its original vendor once." | [Handling objections, Longevity](../business/objection-handling.md#longevity-will-it-still-be-here-in-ten-years) |

## A timeline you can show

| When | What | Source |
|---|---|---|
| 1999 to 2014 | Copyright years of eZ Systems AS in the code | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| 2002 | Oldest commit in the repository | [7x in numbers](../who-is-7x.md#7x-in-numbers) |
| 2006 | eZpedia starts | [Timeline](../who-is-the-exponential-foundation.md#timeline) |
| 2021 | The platform reaches end of life; the code base carries on | same |
| December 2023 | 7x's project launches and takes over the code base | same |
| 1 January 2024 | 6.0.0 | same |
| August 2025 | The product becomes Exponential | same |
| 19 March 2026 | The Exponential Foundation is founded | same |
| 7 June 2026 | 6.0.14 | same |
| September and October 2026 | Velocity joins; Exponential Intelligence and the help in the 6.0.15 line | same |

The full table is on [Who is the Exponential Foundation?, Timeline](../who-is-the-exponential-foundation.md#timeline);
the long history on [Who is 7x?](../who-is-7x.md#nearly-thirty-years-the-timeline).

## How to frame the old name

- Say: "The code base was developed for many years under the name eZ Publish, a product its creator discontinued
  after almost eighteen years. Exponential carried it forward."
- Do not say: "Exponential is eZ Publish", "the new eZ Publish", or anything that suggests a link with eZ Systems or
  Ibexa today.

Details: [Writing about the history correctly](writing-about-the-history-correctly.md).

## The honest limits

- **Nobody can promise the future.** "The record and the license give you options; nobody can guarantee what
  happens."
- **Governance and budget of the Foundation are not documented** in the sources.
- **Age is not quality by itself.** Pair the history with the current evidence: releases, tests, security process.

## Make it concrete

Offer the listener a five-minute check:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git log --reverse --format='%ad %an: %s' | head -3   # the oldest changes
git tag -l 'v6.0.*' --sort=version:refname          # the 6.0.x releases
git log -5 --format='%ad %s'                        # this week's changes
```

Or point to a month in [the history](../../history/README.md) and let them read what changed.

## Related pages

- [The story of Exponential in two minutes](the-story-of-exponential-in-two-minutes.md)
- [Objection: "Is it still alive?"](objection-is-it-still-alive.md)
- [Writing history posts](writing-history-posts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
