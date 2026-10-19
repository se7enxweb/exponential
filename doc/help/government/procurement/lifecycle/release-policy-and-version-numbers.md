---
title: Release policy and version numbers
audience: government, project-manager, admin
category: public-sector
order: 20
summary: What a version number of Exponential promises to a public body - permanent tags, the next release as the only fix, the last position counting on, changelogs per version - and how to use that in a contract and an asset register.
keywords: release policy, version number, tag, immutable, permanent, changelog, 6.0.x, semantic versioning, Packagist, release, contract, asset register, public sector
type: help
---
# Release policy and version numbers

This page is for project managers and contract managers in a public body who must state which version of the software
they run, and understand what a new version means. It explains the release policy of Exponential in plain words. The
source is [Source control and releases](../../../../standards/source-control-and-releases.md) and
[`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md).

## Contents

1. [In short](#in-short)
2. [A published version never changes](#a-published-version-never-changes)
3. [How the numbers are built](#how-the-numbers-are-built)
4. [Where each change is recorded](#where-each-change-is-recorded)
5. [Using this in a contract](#using-this-in-a-contract)
6. [Gaps to know](#gaps-to-know)

## In short

- Exponential 6.0 is the maintained line; `main` is its development branch and new versions are tags cut from it.
- A tag, and the release made from it, is never changed after it is published. A fix always arrives as the next version.
- Every version has a changelog in `doc/changelogs/`.
- Extensions carry their own version numbers, written into their own files in the release.

## A published version never changes

"A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next
release, not as a corrected copy of the old one" (`SECURITY.md`). The reason is technical as well as a matter of
trust: Composer and Packagist read a tag once and cache what they found, so a moved tag would produce two different
packages under one version number ([Source control and releases](../../../../standards/source-control-and-releases.md#tags-are-permanent)).

For a public body this means:

- the version recorded in your asset register identifies exactly one set of files;
- an auditor can fetch that version later and see the same code;
- a vulnerability in a version is fixed by moving to a newer one, never by a silent replacement.

## How the numbers are built

| Part | Meaning | Example |
|---|---|---|
| Major and minor | the product line | 6.0 |
| Last position | the release within the line | 6.0.14, then 6.0.15 |

The last position counts on: after `.9` comes `.10`, never a jump of a higher position; raising a higher position is
a separate decision about what the release means ([Source control and releases](../../../../standards/source-control-and-releases.md#choosing-the-next-version-number)).
The kernel's version is in `lib/version.php`. Published kernel tags run from `v6.0.0` to `v6.0.14`, and 6.0.15 is in
development ([Source control and releases](../../../../standards/source-control-and-releases.md#the-kernels-versions)).

To see which version a site runs, open **Setup > System information** (`/setup/info`)
([System information](../../../../guides/system-information.md)), or the about page, which also lists the version of
every extension ([About and package pages](../../../../features/6.0/about-and-package-pages.md)).

## Where each change is recorded

| Record | Where |
|---|---|
| Kernel changes per version | `doc/changelogs/<series>/<version>.md`, for example the [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md) |
| Extension changes | `doc/changelogs/extensions/<name>.md` |
| Changes that need action at upgrade | pages in `doc/bc/6.0/`, linked from the changelog |
| The story behind changes since December 2023 | [the history](../../../../history/README.md) |

Changelog entries are grouped by Added, Updated, Removed and Renamed, assembled from the commit history. A supplier can
use them to tell you what an upgrade changes ([Reading a changelog](../../../press/reading-a-changelog.md) explains
how to read one).

## Using this in a contract

- Name the version the supplier delivers and the version at acceptance, as tags (`v6.0.x`) or, for development work,
  as a commit.
- Require the supplier to say, at each upgrade, which changelog entries affect your site.
- Require that custom code is kept in your own repository at a known commit, so the delivered state can be rebuilt.
- Do not accept "patched" copies of a published version from a supplier; ask for the next release plus your own
  changes on top.

## Gaps to know

The standards page names them ([Source control and releases](../../../../standards/source-control-and-releases.md#known-gaps)):

- commits and tags are **not signed**;
- the merge and no-force-push rules are policy only; `main` has no branch protection;
- older kernel tags (`v6.0.10` to `v6.0.12`) are lightweight tags without messages.

If your security policy requires signed releases, state it as a gap in your evaluation and ask how a supplier would
compensate (for example by verifying checksums against `share/filelist.md5` with **Setup > Upgrade check**,
[The upgrade check](../../../../guides/upgrade-check.md)).

## Related pages

- [The product lifecycle](README.md)
- [Supported versions](supported-versions.md)
- [Source control and releases](../../../../standards/source-control-and-releases.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
