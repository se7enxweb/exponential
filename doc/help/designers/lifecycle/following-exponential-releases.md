---
title: "Following Exponential releases"
audience: designer, developer
category: theme-lifecycle
order: 40
summary: Where to watch for changes that affect a theme - the changelogs in doc/changelogs, release tags and GitHub releases of the repository, the upgrade notes in doc/bc, project news and RSS - and a weekly routine.
keywords: releases, changelog, watch, GitHub releases, RSS, upgrade notes, doc/bc, news, routine
type: help
---
# Following Exponential releases

This page is for designers who maintain themes. Changes in the kernel, the administration designs and the bundled
extensions can affect your templates; knowing early saves emergency fixes.

## Where changes are recorded

| Source | What it holds |
|---|---|
| `doc/changelogs/6.0/<version>.md` in the repository | The changelog of each release; the one in development (6.0.15) is updated as work lands |
| `doc/bc/6.0/` | Upgrade notes for changes that need action, for example [YUI removal](../../../bc/6.0/yui-removal.md) |
| `doc/features/6.0/` | What a new feature does, for example [jQuery 4 in the admin, and YUI gone](../../../features/6.0/jquery4-and-yui-removal.md) |
| Releases and tags of `se7enxweb/exponential` and of the extensions you use | Versions as published |
| [projects.exponential.earth](https://projects.exponential.earth/) news and RSS (`/rss/feed/projects-news`, `/rss/feed/git`) | Announcements and recent Git changes |
| [share.exponential.earth](https://share.exponential.earth/) blogs | Release announcements |

## Step by step: a weekly routine

1. Watch the repository's releases on GitHub (**Watch > Custom > Releases**), or subscribe to the RSS feeds above.
2. Once a week, read what changed in the current changelog since your last look.
3. Note every item that touches templates, design settings, JavaScript libraries, the Content Security Policy or
   PHP versions ([Reading a changelog as a designer](reading-a-changelog-as-a-designer.md)).
4. For each, open an issue in your theme's repository.
5. Before your next release, test against the newest Exponential ([Upgrade testing, step by step](upgrade-test-step-by-step.md)).

## Related pages

- [Upgrading](../../../guides/upgrading.md)
- [A maintenance calendar](maintenance-calendar.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
