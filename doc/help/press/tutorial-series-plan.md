---
title: Tutorial series plan
audience: press
category: press-media
order: 168
summary: A ready plan for a tutorial series about Exponential in three tracks - editors, site builders and administrators - with twelve episodes, each tied to a help page and a demo script, the version to state, prerequisites, and how to keep a series correct as releases change the screens.
keywords: tutorial series, course, episodes, playlist, curriculum, editors, site builders, administrators, developers, learning path, creator, video series
type: help
---
# Tutorial series plan

This page is for creators who want to make a series rather than one video: a playlist, a course, a weekly stream. It
gives a plan of twelve episodes in three tracks, each tied to a help page so viewers can read on and you can check
every step. Adapt the order to your audience; keep the rule of one task per episode
([Writing a tutorial that works](../advocates/writing-a-tutorial-that-works.md)).

## Contents

1. [Decide the version once](#decide-the-version-once)
2. [Track A: editors](#track-a-editors)
3. [Track B: site builders](#track-b-site-builders)
4. [Track C: administrators](#track-c-administrators)
5. [An episode template](#an-episode-template)
6. [Keeping the series correct](#keeping-the-series-correct)

## Decide the version once

A series must show one version throughout, or say clearly when it changes. Most screens in the help belong to the
6.0.15 development line, which has no release date; the newest release is 6.0.14 (7 June 2026). State the version in
the series title or the first episode, and in every description. When 6.0.15 is tagged, check each episode against
its final changelog ([6.0.15 changelog](../../changelogs/6.0/6.0.15.md)).

## Track A: editors

| # | Episode | Length | Help page | Script |
|---|---|---|---|---|
| A1 | Signing in and finding your way | 6 min | [Getting started](../getting-started.md), [Logging into the Editor](../logging-into-the-editor.md) | [Editor tour, part 1](demo-script-editor-tour.md#part-1-finding-your-way-2-minutes) |
| A2 | Editing an item: drafts and the field bar | 8 min | [Creating and editing content](../editing-content.md) | [Editor tour, part 2](demo-script-editor-tour.md#part-2-editing-a-folder-4-minutes) |
| A3 | Preview, publish, versions and going back | 6 min | [Publishing, versions and the trash](../publishing-and-versions.md) | [Editor tour, part 3](demo-script-editor-tour.md#part-3-versions-1-minute-30) |
| A4 | Images and files | 6 min | [Media and uploads](../media-and-uploads.md) | Your own, from the help page |
| A5 | Translating content; the interface in other languages | 6 min | [Languages](../languages.md) | [Editor tour, part 4](demo-script-editor-tour.md#part-4-languages-and-right-to-left-2-minutes-30) |

## Track B: site builders

| # | Episode | Length | Help page | Script |
|---|---|---|---|---|
| B1 | Install from zero on SQLite | 10 min | [Install in one command](../../features/6.0/install-in-one-command.md) | [Install from zero](demo-script-install-from-zero.md) |
| B2 | Layouts, zones and blocks | 8 min | [The layouts editor](../layouts-editor.md) | [Exponential Layouts](demo-script-layouts.md) |
| B3 | Content types: classes and attributes | 8 min | [The content model and editing content](../../guides/content-model-and-editing.md) | [Administrator tour, part 1](demo-script-administrator-tour.md#part-1-the-content-model-2-minutes) |
| B4 | Working with Git: your own branch, updates and a first pull request | 10 min | [Develop with Git](../develop-with-git.md) | Your own, from the help page |

## Track C: administrators

| # | Episode | Length | Help page | Script |
|---|---|---|---|---|
| C1 | Running the site under Velocity | 8 min | [Velocity](../../features/6.0/velocity-persistent-worker-server.md) | [Velocity](demo-script-velocity.md) |
| C2 | Roles, two-step sign-in and the audit trail | 8 min | [Security hardening](../security-hardening.md) | [Administrator tour, part 4](demo-script-administrator-tour.md#part-4-security-3-minutes) |
| C3 | Logs, upgrade check and caches | 8 min | [Operating a site](../../guides/operating-a-site.md) | [Administrator tour, part 3](demo-script-administrator-tour.md#part-3-operations-3-minutes) |

An optional fourth track can show Exponential Intelligence ([its demo script](demo-script-exponential-intelligence.md))
once you have a provider key you can revoke.

## An episode template

```text
EPISODE <#> - <title>
Series:      <name>, episode <#> of <n>          Version: Exponential <version>
Promise:     After this episode you can <task>.
Before:      Episode <#> (or: a demo installation, see <link>)
Disclosure:  <line>

0:00  Recap of the last episode (15 seconds), promise, disclosure
0:30  The task, step by step (names exactly as on screen)
...   The result
...   What can go wrong, and the fix
...   Next episode; the help page; where to ask (forums, Telegram)

DESCRIPTION: help page link, commands shown, chapters, license of the video
```

## Keeping the series correct

| When | Do |
|---|---|
| A release is tagged | Read its changelog for the screens of every episode; list what changed |
| A step no longer works | Pin a correction on the episode; fix the description; re-record if the step is central |
| An episode is obsolete | Say so in its title; link the replacement |
| A viewer reports a mistake | Check it against the help; thank them; correct in the open ([Correcting a video or post](correcting-a-video-or-post.md)) |

Keep a small table of your episodes with the version and the date you last checked each one.

## Related pages

- [Writing a tutorial that works](../advocates/writing-a-tutorial-that-works.md)
- [Video walkthrough guide](video-walkthrough-guide.md)
- Podcast episode outlines
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
