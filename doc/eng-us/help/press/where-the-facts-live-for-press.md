---
title: Where the facts live, for the press
audience: press
category: press-media
order: 20
summary: A source map for reporters - which repository file, help page or public website is the primary source for each kind of fact about Exponential, how primary and secondary sources rank, and what to do when two sources disagree.
keywords: sources, primary source, source map, where to check, README, SECURITY.md, COPYRIGHT.md, changelog, release overview, history, websites, verification, conflicting sources
type: help
---
# Where the facts live, for the press

This page is for reporters who want the primary source for a fact, not a summary. Exponential is unusual in that
almost everything is written down in its public Git repository. Use this map to go to the source, and to decide which
source wins when two disagree.

## Contents

1. [The ranking of sources](#the-ranking-of-sources)
2. [Fact and source](#fact-and-source)
3. [The repository files](#the-repository-files)
4. [The websites](#the-websites)
5. [When two sources disagree](#when-two-sources-disagree)

## The ranking of sources

| Rank | Source | Why |
|---|---|---|
| 1 | The Git repository: tags, commits, `composer.json`, `SECURITY.md`, `COPYRIGHT.md` | It is what was actually published, with dates you can check |
| 2 | The documentation in `doc/` (this help, the changelogs, the history) | Written with the code, sourced, versioned |
| 3 | The organizations' websites: exponential.earth, foundation.exponential.earth, se7enx.com | Self-descriptions and announcements |
| 4 | Press releases and posts | The organizations' statements at a point in time |
| 5 | Third-party articles, including other press | Useful leads; check them against ranks 1 to 3 |

## Fact and source

| You need | Primary source |
|---|---|
| What Exponential is | [README.md](https://github.com/se7enxweb/exponential/blob/main/README.md), "What is Exponential?" |
| Release numbers and dates | Git tags (`git tag -l 'v6.0.*' --sort=version:refname`); [release overview](../../release-overview) |
| What changed in a release | [Changelogs](../../../../changelogs/README.md); the 6.0.15 changelog for the line in development |
| The month-by-month story | [The history](../../history/README.md) |
| Copyright holders | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| License | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE); [The license of this documentation](../../README-LICENSE.md) |
| Security process | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| How to contribute | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| PHP versions, packages | [composer.json](https://github.com/se7enxweb/exponential/blob/main/composer.json); README, "Requirements" |
| Databases, operating systems | [Database support](../database-support.md); [Operating system support](../os-support.md) |
| A feature's behavior | The feature page in `doc/en/us/features/6.0/`, for example [Content Security Policy](../../features/6.0/content-security-policy.md) |
| Terms | [The glossary](../../glossary.md) |
| 7x | <https://se7enx.com/about>; [Who is 7x?](../who-is-7x.md) |
| The Foundation | <https://foundation.exponential.earth/>; [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| The rename | <https://exponential.earth/about/project>; [August 2025](../../history/2025/2025-08.md) |
| Press kit, earlier coverage | [The press kit](the-press-kit.md); [Earlier press releases and coverage](earlier-press-releases-and-coverage.md) |

The advocates keep a similar map for talking points: [Where the facts live](../advocates/where-the-facts-live.md).

## The repository files

You do not need to install anything to read them; open them on GitHub. To check dates and counts, clone it:

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
cat COPYRIGHT.md SECURITY.md | less
git log -1 --format='%ad %s' v6.0.14
```

## The websites

| Site | Good for | Be careful with |
|---|---|---|
| exponential.earth | Announcements, the press kit, contacts, the rename notice | Dated material such as the press kit's key facts ([The press kit](the-press-kit.md#facts-in-the-kit-that-are-out-of-date)) |
| foundation.exponential.earth | The Foundation's self-description and mission | Goals mentioned but not listed |
| se7enx.com | 7x's services and self-description | Prices and offers change; cite the date |
| doc.exponential.earth | The official documentation site | Check that a page matches the current release |

## When two sources disagree

1. **Prefer the higher rank.** A tag beats a press release about the same version.
2. **Prefer the newer dated source** within the same rank.
3. **Explain, do not choose silently**, when both are legitimate. Example: the years 2014, 2022 and 2023 for 7x mark
   different stages ([7x in three stages](7x-in-three-stages.md)).
4. **Ask** when the sources cannot settle it, and report the answer with attribution.
5. **Tell the project** about a contradiction in its own documents on the
   [community tracker](https://github.com/se7enxweb/exponential-community/issues).

## Related pages

- [Fact-checking guide](fact-checking-guide.md)
- [What the sources do not say](what-the-sources-do-not-say.md)
- [Help pages by topic, for the press](help-pages-by-topic-for-press.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
