---
title: Where changelogs and release notes live
audience: press
category: press-media
order: 101
summary: A map of every place that records what an Exponential version contains - the changelog pages, the release overview, GitHub Releases, the release notes of extensions, Velocity and the platform packages, the history and the ledger - and which one to cite for what.
keywords: changelog, release notes, release overview, GitHub Releases, history, ledger, extensions, Velocity, platform, sources, where to look
type: help
---
# Where changelogs and release notes live

This page is for reporters and creators who need to find the official record of a release quickly. Exponential keeps
its release information in the repository itself, next to the code, so every version can be checked by anyone. Here
is where each kind of record lives and what it is good for.

## Contents

1. [The kernel: Exponential 6](#the-kernel-exponential-6)
2. [Extensions, Velocity and the platform packages](#extensions-velocity-and-the-platform-packages)
3. [The history and the ledger](#the-history-and-the-ledger)
4. [Which source to cite for what](#which-source-to-cite-for-what)
5. [When two sources disagree](#when-two-sources-disagree)

## The kernel: Exponential 6

| Record | Where | What it gives you |
|---|---|---|
| Changelog of one version | `doc/changelogs/6.0/<version>.md`, for example [6.0.14](../../../../changelogs/6.0/6.0.14.md) | The release date, the tag, "In short", "Upgrading", and the changes grouped as Added, Updated, Removed, Renamed, each with its commit |
| The development line | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md) | "Not released yet"; what the line contains on the date the page names |
| Release overview | [doc/en/us/release-overview](../../release-overview) | One entry per version since 2002, with tag, date, release URL and changelog URL; for 6.0.x it also gives the GitHub "Release date" where it differs |
| GitHub Releases | [github.com/se7enxweb/exponential/releases](https://github.com/se7enxweb/exponential/releases) | The published release with its notes, one per tag |
| Older lines | Folders such as `doc/changelogs/4.7/` and `doc/changelogs/5.2/` in the repository | Text changelogs of the eZ Publish years, kept as history |

The changelog pages of the 6.0 line link to each other ("Releases before and after") and to the month pages of the
history that tell the same period as a story.

## Extensions, Velocity and the platform packages

Every repository that ships next to Exponential has its own release notes, collected in one index:
[Release notes of the extensions, themes, the web server and the platform packages](../../../../changelogs/extensions/README.md).

| Group | Example | Note |
|---|---|---|
| Extensions and themes | [eztags](../../../../changelogs/extensions/eztags.md), [cjw_newsletter](../../../../changelogs/extensions/cjw_newsletter.md) | Each lists its tagged releases newest first |
| Two extensions keep notes in their own repository | expai (Exponential Intelligence) and expqueue | The index links their `doc/changelog` folders |
| The web server | [exponential-velocity](../../../../changelogs/extensions/exponential-velocity.md) | From v0.0.4.43 on, the full notes are on [GitHub Releases](https://github.com/se7enxweb/exponential-velocity/releases) |
| Platform packages | [exponential-platform-nexus](../../../../changelogs/extensions/exponential-platform-nexus.md), [exponential-platform-dxp](../../../../changelogs/extensions/exponential-platform-dxp.md) | Only the se7enxweb team's changes; upstream history is told elsewhere |

More on these: [Covering an extension release](covering-an-extension-release.md),
[Covering Velocity releases](covering-velocity-releases.md),
[Covering Exponential Platform releases](covering-platform-releases.md).

## The history and the ledger

| Record | Where | What it gives you |
|---|---|---|
| The chronicle | [The history](../../history/README.md) | Month pages from December 2023 on: what changed, why it matters, with commits |
| The ledger | [Ledger of all repositories](../../history/ledger/README.md) | Every commit of every repository, with date, kind, size and the release tag that first contained it |
| The coverage page | [Coverage](../../history/coverage.md) | Proof, per repository and month, that every change is explained or listed |
| Velocity's own chronicle | [Velocity chronicle](../../history/velocity/README.md) | The web server from its first commit in July 2026 |

See [The history pages as a source](the-history-pages-as-a-source.md) for how to cite them.

## Which source to cite for what

| You want to print | Cite |
|---|---|
| "Version X was released on date Y" | The changelog page of X and the release overview |
| "Version X brings feature Z" | The changelog of X, plus the feature page it links |
| "Feature Z arrived in month M" | The month page of the history |
| "The project has made N releases" | The tag list (`git tag -l 'v6.0.*' --sort=version:refname`) or [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases) |
| "Extension E version V does ..." | The release notes page of E |

## When two sources disagree

It happens. Two real examples from the repository:

- The release overview gives two dates for some versions: 6.0.13 has the tag date 18 April 2026 and the GitHub
  "Release date" 20 April 2026 ([release overview](../../release-overview)). Say which one you mean
  ([Verifying dates](verifying-dates.md)).
- On 18 October 2026 the index of extension release notes named 4.1.15 as the latest cjw_newsletter release, while
  the [cjw_newsletter page](../../../../changelogs/extensions/cjw_newsletter.md) itself already listed 4.1.16. The page of
  the package and its tags are the more specific source.

When in doubt, the Git tags decide, because they are the release ([Verifying a release yourself](verifying-a-release-yourself.md)).

## Related pages

- [Reading a changelog](reading-a-changelog.md)
- [Covering a release](covering-a-release.md)
- [Where the facts live](../advocates/where-the-facts-live.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
