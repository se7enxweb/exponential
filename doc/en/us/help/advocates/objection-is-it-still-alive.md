---
title: "Objection: \"Is it still alive?\""
audience: advocate
category: advocacy
order: 31
summary: How to answer "Isn't Exponential dead?" or "Isn't that the discontinued product?" with checkable evidence - releases, commits, CI, the history, the organizations behind it - plus reply templates and what not to say.
keywords: objection, dead, abandoned, discontinued, end of life, maintained, releases, activity, eZ Publish, still alive, evidence, history
type: help
---
# Objection: "Is it still alive?"

This page is for advocates who hear "Isn't that dead?", "Wasn't eZ Publish discontinued?" or "The last time I looked,
nothing happened." It is the most common doubt and the easiest to answer, because the evidence is public and anyone can
check it in minutes.

## Contents

1. [Where the doubt comes from](#where-the-doubt-comes-from)
2. [The evidence](#the-evidence)
3. [Let them check](#let-them-check)
4. [Reply templates](#reply-templates)
5. [Do not say](#do-not-say)

## Where the doubt comes from

The doubt is reasonable. The code base was developed for many years under the name eZ Publish, and its creator
discontinued that product after almost eighteen years
([Exponential 6](../who-is-the-exponential-foundation.md#exponential-6-the-kernel)). The project's own sources say
"The platform originally known as eZ Publish reached end-of-life in 2021. We kept going"
([Timeline](../who-is-the-exponential-foundation.md#timeline)). Someone who stopped following in those years has
every reason to ask.

## The evidence

| Evidence | Source |
|---|---|
| 6.0.0 on 1 January 2024 to 6.0.14 on 7 June 2026: fifteen releases, with 6.0.15 in development | [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases) |
| "6556 ledger changes in 53 repositories since December 2023" | [Handling objections, Is it maintained?](../business/objection-handling.md#is-it-maintained) |
| Tests run on every push and pull request, on PHP 8.1 to 8.5 | [CI](../../bc/6.0/ci.md), [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| What the current line adds: Velocity, admin4, audit trail, layouts, Exponential Intelligence, the help | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md) |
| Every month of work is told in the history | [The history](../../history/README.md) |
| A company leads it (7x) and a nonprofit operates it (the Exponential Foundation, founded 2026) | [The Foundation and 7x](../who-is-the-exponential-foundation.md#the-foundation-and-7x) |
| Pull requests come from several organizations and are merged | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows) |

## Let them check

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git tag -l 'v6.0.*' --sort=version:refname   # the releases
git log -10 --format='%ad %s'                 # the latest changes, with dates
```

Or, without a terminal: the [releases on GitHub](https://github.com/se7enxweb/exponential/releases) and the most
recent month of [the history](../../history/README.md).

## Reply templates

```text
SHORT (social media)
It's very much alive: fifteen 6.0.x releases since Jan 2024, 6.0.15 in development, tests on every push.
Releases: https://github.com/se7enxweb/exponential/releases

FORUM
Fair question - the code base was developed for years as eZ Publish, which its creator discontinued.
7x took over leadership in 2023, and since 1 January 2024 there have been fifteen 6.0.x releases
(6.0.0 to 6.0.14), with 6.0.15 in development. Every month is documented in the history pages in doc/history,
and you can check the tags yourself: git tag -l 'v6.0.*' --sort=version:refname
Since 2026 a nonprofit, the Exponential Foundation, operates the project.

IN PERSON
"The original vendor stopped, but the code is free, so it carried on. There have been fifteen releases
since January 2024 - I can show you the list on my phone."
```

## Do not say

- "It is more alive than ever." Vague; give the facts instead.
- "eZ Publish is now Exponential by eZ Systems." Wrong: no association with eZ Systems or Ibexa
  ([Who is 7x?, In short](../who-is-7x.md#in-short)).
- "Thousands of companies still use it." No source counts users.
- "Releases every month." Give the actual dates.

## Related pages

- [Talking points: longevity and history](talking-points-longevity-and-history.md)
- [The story of Exponential in two minutes](the-story-of-exponential-in-two-minutes.md)
- [Answering objections as an advocate](answering-objections-as-an-advocate.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
