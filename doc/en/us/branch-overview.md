---
title: Branch history
audience: developer, admin
category: development
order: 40
summary: How the Exponential repositories use branches and tags today, which release lines are maintained, which branch or tag to build a site on, and the branches of the original SVN repository from 2003 to 2006.
keywords: branch, branches, main, master, tag, release, release line, 6.0, pull request, merge, CI, Velocity, Nexus, Platform, DXP, extension, SVN, history
type: guide
---
# Branch history

This page explains how the Exponential repositories use branches and tags: where development happens, how a release
is made, which lines are maintained, and which branch or tag to build a site on. The last section keeps the branches
of the original SVN repository, from the years when the software was called eZ Publish.

Every branch and tag named here was checked against the repositories on GitHub on 2026-10-18. Branches and tags move
on; the commands under each heading show the current state.

## In short

| Question | Answer |
|---|---|
| Where does development happen? | On `main` of [se7enxweb/exponential](https://github.com/se7enxweb/exponential), the only long-lived branch |
| How do changes arrive? | On short-lived branches, merged into `main` through pull requests, each as a merge commit |
| What is a release? | A tag `v6.0.<n>` on `main`. The newest is `v6.0.14`; `main` carries 6.0.15 |
| Does a tag ever change? | No. A fix always arrives as the next version |
| What do I build a production site on? | A release tag (Composer: a version such as `~6.0.14`) |
| Where are the extensions? | Each in its own repository, with its own default branch and release tags |

## The kernel repository

[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential) holds the kernel, the administration
designs, the bundled extensions (`ezjscore`, `ezoe`, `ezformtoken`, `expservices`) and this documentation.

### main, the only long-lived branch

`main` is the default branch and the development line of 6.0. It identifies itself as 6.0.15 (`lib/version.php`) and
carries the changes of [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md). For Composer it is `dev-main`, with the branch
alias `6.0.x-dev`, so packages that require the kernel with a version constraint such as `^6.0.14` resolve against it.

All work reaches `main` in the same way:

1. A change starts on a short-lived branch (a feature, a fix, or one wave of related work), in a fork or in the
   repository.
2. The branch is proposed as a pull request against `main`.
3. Every pull request is merged, as a merge commit that keeps the contributor's commits as written. What a review
   finds is fixed in further commits on top, in the same push, or made optional, so `main` stays safe.
4. The branch is deleted or simply left behind; nothing else is merged from it.

`git log --merges origin/main` lists the merges. Commits by project authors start with `Added:`, `Updated:`, `Fixed:`,
`Removed:` or `Renamed:`, which is what the changelogs are assembled from.

### The checks on every push and pull request

GitHub Actions runs the same blocking checks on a push to `main` and on a pull request (on the merge of the branch
into `main`). [Quality checks](features/6.0/quality-checks.md#ci) describes each one; in short:

| Workflow | What it checks |
|---|---|
| Quality | PHP and JavaScript syntax of the changed files, the file manifest `share/filelist.md5`, the commit messages, PHPStan, the code style sniffs, and a job on PHP 8.0 (syntax of every file, a production install, a boot test). Code style is a warning |
| PHPUnit | The test suites on PHP 8.1, 8.2, 8.3, 8.4 and 8.5, and the database tests on MySQL and PostgreSQL |
| Performance | Benchmarks, when PHP files, templates, settings or the benchmarks change |
| CI health | A scheduled report on the state of the workflows |

On a pull request, a stale manifest line and a contributor's commit message are warnings; the person who merges
refreshes the manifest on `main`. [Before you push](features/6.0/quality-checks.md#before-you-push) lists the commands
that run the blocking checks locally.

### Release tags

A release is a tag on a commit of `main`, named `v6.0.<n>`. Each one has a changelog page in `doc/changelogs/6.0`
(the newest: [Changelog 6.0.14](../../changelogs/6.0/6.0.14.md)) and a release on
[GitHub Releases](https://github.com/se7enxweb/exponential/releases):

| Tags | Count | Notes |
|---|---|---|
| `v6.0.0` to `v6.0.14` | 15 | The 6.0 line, from 2024-01-01 to 2026-06-07 |
| `2.9-3` to `4.4.0` | 160 | The releases of 2002 to 2010, imported with the history; no `v` in their names |

A tag is permanent. Composer and Packagist read it once, and a "corrected" tag would give two different packages the
same version number. A mistake in a release is fixed by the next release. Sort tags by version, never by name
(`v6.0.10` sorts before `v6.0.8` by name):

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
```

### Release branches

Next to `main`, the repository keeps one branch per release from 6.0.7 on: `6.0.7` to `6.0.14`. From `6.0.8` on,
each points at the commit of its tag; `6.0.7` has later commits than `v6.0.7`. They are a record of each release, not
a place where development continues; a fix goes to `main` and into the next release.

A few older branches are left from the start of the 6.0 line (`6.0.0-beta2`, `6.0.1-beta1`, `6.0.8-devel`), and some
topic branches from pull requests that were merged long ago. None of them is maintained.

```bash
git ls-remote --heads https://github.com/se7enxweb/exponential.git
```

## Release lines

| Line | Status | Where |
|---|---|---|
| 6.0.x | Maintained: development on `main`, releases `v6.0.0` to `v6.0.14` | se7enxweb/exponential |
| 5.x (5.0 to 5.4, the 5.90 line of 2017.08 to 2019.03) | Not maintained. 6.0 continues this code base: 6.0.0 started from the last open source release of the legacy kernel | The archived upstream repository; [Changelog 6.0.0](../../changelogs/6.0/6.0.0.md) explains the move |
| 4.x | Not maintained. Its tags up to `4.4.0` are in the kernel repository | se7enxweb/exponential (tags) |
| 3.x | Not maintained. Its tags and the SVN branches below are kept as history | se7enxweb/exponential (tags) |

The [release history](release-overview) lists the releases from 2.9 to 6.0.15 with their dates and sources.

## Extension repositories

Each extension that Exponential installs with Composer lives in its own repository under
[github.com/se7enxweb](https://github.com/se7enxweb). On 2026-10-18 the kernel's `composer.json` required 66 of them:

- 34 have `master` as their default branch and 32 have `main`; it is the development line in both cases.
- Releases are tags, mostly with a `v` (`eztags` `v2.4.21`, `explayouts` `v1.4.39`); a few have none
  (`cjw_newsletter` `4.2.12`, `enhancedselection2` `2.1.8`, `birthday` `1.3.3`, `ngclasslist` `1.1.4`,
  `exponential-legacy-installer` `2.2.4`).
- The kernel requires each one with a tilde constraint on its current release, such as `~2.4.21`, which also accepts
  the later releases of the same line (`2.4.22` and on).
- Each release raises the version in the extension's `ezinfo.php` and `extension.xml` to the release's number in the
  commit that is tagged, so an installed copy says which release it is.

The extensions bundled with the kernel carry their own version: `ezjscore` is released at the same time as its mirror
[se7enxweb/ezjscore](https://github.com/se7enxweb/ezjscore) (both `1.8.4`).

To see an extension's branches and tags:

```bash
git ls-remote --heads --tags https://github.com/se7enxweb/eztags.git
```

## Velocity and the platform distributions

| Repository | Default branch | Releases |
|---|---|---|
| [exponential-velocity](https://github.com/se7enxweb/exponential-velocity), the web server | `main` | Tags `v0.0.4.<n>`; the newest is `v0.0.4.53` (2026-10-15). The last position counts on: `v0.0.4.9` was followed by `v0.0.4.10`. Feature and fix work happens on short-lived `feat/...` and `fix/...` branches |
| [exponential-platform-nexus](https://github.com/se7enxweb/exponential-platform-nexus), the Symfony platform with Exponential 6 as its legacy kernel | `master` | Several lines, each with a branch: `1.0.0.x`, `1.1.0.x`, `1.2.0.x`, `1.3.0.x` and `2.5.0.x` (tags up to `v2.5.0.7`); older numbered branches from `1.7` to `2.5.0.1` |
| [exponential-platform-legacy](https://github.com/se7enxweb/exponential-platform-legacy) | `master` | Branches `3.x`, `4.6.x` and `5.x` among older ones; the newest release is `v5.0.3.1` (2026-10-05) |
| [exponential-platform-dxp](https://github.com/se7enxweb/exponential-platform-dxp) | `master` | Branches `3.3` and `4.0` to `4.6`, plus `4.6.x-LB` and `5.x-LB`; release `v0.0.0.2` |
| [exponential-platform-dxp-skeleton](https://github.com/se7enxweb/exponential-platform-dxp-skeleton) | `master` | Branch `4.6`; releases up to `v2.0.0.2` |
| [exponential-platform](https://github.com/se7enxweb/exponential-platform), the Symfony-only platform 3.2 | `3.2` | Releases up to `v3.2.9` |
| [exponential-starter](https://github.com/se7enxweb/exponential-starter), [exponential-platform-nexus-starter](https://github.com/se7enxweb/exponential-platform-nexus-starter), [exponential-platform-legacy-starter](https://github.com/se7enxweb/exponential-platform-legacy-starter), [exponential-platform-dxp-starter](https://github.com/se7enxweb/exponential-platform-dxp-starter) | `main` | Ready-to-run distributions with their own release tags (`1.0.0.0` and on) |

Read a repository's default branch and newest releases with the GitHub CLI:

```bash
gh api repos/se7enxweb/exponential-platform-nexus --jq .default_branch
gh release list -R se7enxweb/exponential-platform-nexus --limit 5
```

## Which branch or tag to build a site on

| You want | Use |
|---|---|
| A production site that stays the same until you decide to update | A release: Composer version `~6.0.14`, or the tag `v6.0.14` in a clone |
| The newest fixes before they are released, or PHP 8.0 | `main` (`dev-main` for Composer). No published tag accepts PHP 8.0 yet |
| A fix of one extension before its release | That extension's default branch, or a branch with the fix, in a clone |
| Your own changes to the kernel or an extension | Your own branch on top of a tag or of `main`, so updates merge with your work |

[Develop with Git](help/develop-with-git.md) shows how to install the kernel and the extensions as Git clones, start a
branch for your site, keep it up to date and send a fix back as a pull request.
[Versions, tags and branches](install/03-getting-the-code.md#31-versions-tags-and-branches) lists which release needs
which PHP version.

## Historical branches (original SVN, 3.x)

> **Note:** This section is inherited from the documentation of the original software. It was written by eZ Systems
> AS and its contributors between 2003 and 2006, when the software was called eZ Publish, and it is kept here
> unchanged in substance for reference. Its original text is distributed under the license it came with, the GNU
> General Public License, version 2 (Copyright (C) 1999 - 2014 eZ Systems AS and contributors). The text above it is
> new documentation under the GNU Free Documentation License 1.3 or later.

The original repository was SVN, where a branch was a directory: `stable/3.x` for the maintenance of a release line
and `unstable/...` for development work. A branch started at a revision ("Revision") and, when it was closed, ended at
a final revision. These branches are not in the Git repository; the release tags of the same years are.

### Stable branches

| Branch | Started at revision | Final revision | Date |
|---|---|---|---|
| `stable/3.0` | 2303 | not recorded | 2003-04-09 |
| `stable/3.1` | 2708 | not recorded | 2003-06-12 |
| `stable/3.2` | 3240 | not recorded | 2003-08-26 |
| `stable/3.3` | 4648 | not recorded | 2004-01-02 |
| `stable/3.4` | 6744 | not recorded | 2004-06-02 |
| `stable/3.5` | 10325 | 10454 | 2004-12-10 |
| `stable/3.6` | 11989 | not recorded | 2005-05-26 |

### Development branches

| Branch | Started at revision | End revision | Date |
|---|---|---|---|
| `unstable/datatype-development-20030530` | 2628 | 2711 | 2003-05-30 |
| `unstable/2005-02-17-no-references` | 10879 | not recorded | 2005-02-17 |
| `unstable/2006-02-10-multilang-v2` | 14785 | not recorded | 2006-02-20 |

The datatype branch was merged into the trunk in revision 2714; the
[release history](release-overview) has its record and the releases of the 3.x and 4.x lines.
