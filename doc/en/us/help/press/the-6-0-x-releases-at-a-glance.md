---
title: The 6.0.x releases at a glance
audience: press
category: press-media
order: 14
summary: Every Exponential 6.0.x release with its date and its documented highlight, for reporters who need a release history - plus where each release's changelog is, how to check the list yourself, and how to write about 6.0.15 before it is released.
keywords: releases, release history, versions, 6.0.0, 6.0.10, 6.0.14, 6.0.15, changelog, tags, dates, release dates
type: help
---
# The 6.0.x releases at a glance

This page is for reporters who need the release history of Exponential 6. It lists every 6.0.x release with its date,
from [doc/eng-us/release-overview](../../release-overview) and the Git tags, as of **18 October 2026**.

## Contents

1. [The releases](#the-releases)
2. [The changelogs](#the-changelogs)
3. [Check the list yourself](#check-the-list-yourself)
4. [Writing about releases](#writing-about-releases)

## The releases

| Version | Date | Documented highlight |
|---|---|---|
| 6.0.0 | 1 January 2024 | The first stable release of the line; SQLite as a database |
| 6.0.1 | 29 January 2024 | |
| 6.0.2 | 29 February 2024 | |
| 6.0.3 | 2 April 2024 | |
| 6.0.4 | 5 September 2024 | The first release after admin3, the responsive administration design |
| 6.0.5 | 1 October 2024 | |
| 6.0.6 | 17 December 2024 | |
| 6.0.7 | 1 February 2025 | The last release for PHP 7.4 |
| 6.0.8 | 2 May 2025 | The first release after PHP 8.4 support began |
| 6.0.9 | 10 June 2025 | |
| 6.0.10 | 28 August 2025 | The first release named Exponential |
| 6.0.11 | 30 December 2025 | Released in the month PHP 8.5 support arrived |
| 6.0.12 | 10 February 2026 | |
| 6.0.13 | 18 April 2026 | |
| 6.0.14 | 7 June 2026 | MongoDB 8 as a database |
| 6.0.15 | not released | In development: Velocity, admin4, audit trail, Exponential Layouts, Exponential Intelligence, the help system and more |

Source: [The 6.0.x releases](../who-is-7x.md#the-60x-releases). An empty highlight cell means the history pages name
no single headline for that release; read its changelog.

Releases 6.0.0 to 6.0.9 were published while the product still carried its former name; refer to them as Exponential
releases in the past tense, for example "6.0.0, released on 1 January 2024" ([Version numbers in print](version-numbers-in-print.md)).

## The changelogs

| Release | Changelog |
|---|---|
| 6.0.0 | [6.0.0](../../../changelogs/6.0/6.0.0.md) |
| 6.0.10 | [6.0.10](../../../changelogs/6.0/6.0.10.md) |
| 6.0.14 | [6.0.14](../../../changelogs/6.0/6.0.14.md) |
| 6.0.15 (in development) | [6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Every release | [Changelogs](../../../changelogs/README.md) |

How to read them: [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md) and
[Reading a changelog](reading-a-changelog.md).

## Check the list yourself

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
git tag -l 'v6.0.*' --sort=version:refname
git log -1 --format=%ad v6.0.14
```

Sort by version, never by name: a plain name sort puts `v6.0.10` before `v6.0.2`
([How version numbers work](how-version-numbers-work.md)).

## Writing about releases

- A release is a Git tag; until the tag exists, the version is "in development" ([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).
- A published release is never changed; a fix comes as the next release ([Published versions never change](published-versions-never-change.md)).
- Write the version without "v" in running text: "Exponential 6.0.14".

## Related pages

- [Covering a release](covering-a-release.md)
- [Timeline for reporters](timeline-for-reporters.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
