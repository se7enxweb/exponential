---
title: Verifying a release yourself
audience: press
category: press-media
order: 105
summary: Three ways for a reporter to confirm that an Exponential release exists, when it was made and what it contains - GitHub Releases in a browser, a few Git commands, and Packagist - with expected output and what to do when the results disagree.
keywords: verify, verification, tag, git, GitHub Releases, Packagist, release date, ls-remote, version, fact check, release exists
type: help
---
# Verifying a release yourself

This page is for reporters, editors and fact-checkers who want to confirm a release with their own eyes instead of
trusting a post, a press release or this help. Everything here works without an account and without installing
Exponential. You need a browser, and for the second method a computer with Git.

## Contents

1. [Method 1: in a browser](#method-1-in-a-browser)
2. [Method 2: with Git](#method-2-with-git)
3. [Method 3: on Packagist](#method-3-on-packagist)
4. [Confirming what a release contains](#confirming-what-a-release-contains)
5. [When the results disagree](#when-the-results-disagree)

## Method 1: in a browser

1. Open [github.com/se7enxweb/exponential/releases](https://github.com/se7enxweb/exponential/releases).
2. Find the version. Each release shows its tag (for example `v6.0.14`) and the date GitHub published it.
3. Open the tag's page from the [release overview](../../release-overview), which lists a "Release url" for each
   6.0.x version, for example `https://github.com/se7enxweb/exponential/releases/tag/v6.0.14`.

Do not rely on the order of the list alone: confirm the newest version by its number with method 2.

## Method 2: with Git

The repository's own documentation gives these commands
([Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches)). Without cloning:

```bash
git ls-remote --tags https://github.com/se7enxweb/exponential.git 'v6*'
```

Inside a clone:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
```

Expected on 18 October 2026, the five newest tags, newest last:

```text
v6.0.10
v6.0.11
v6.0.12
v6.0.13
v6.0.14
```

The date of a tag's commit:

```bash
git log -1 --format='%ad' v6.0.14
```

Always sort with `--sort=version:refname`; a sort by name puts `v6.0.10` before `v6.0.8`
([How version numbers work](how-version-numbers-work.md#sorting-versions-correctly)).

## Method 3: on Packagist

The kernel is the Composer package
[se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential). Its page lists every version Composer
can install. Velocity is [se7enxweb/exponential-velocity](https://packagist.org/packages/se7enxweb/exponential-velocity).
A version appears there after Packagist has read the tag; the extensions guide notes that if a new version does not
appear, the repository's webhook to Packagist may be missing ([Extensions guide](../../guides/extensions.md#7-release-it)).

## Confirming what a release contains

| Question | How to check |
|---|---|
| Is feature X in version Y? | Search the changelog of Y ([Reading a changelog](reading-a-changelog.md)), then the ledger, whose "Release" column names "the release tag that first contained the change" ([history](../../history/README.md#how-to-read-the-ledger)) |
| Is a commit in a release? | `git tag --contains <hash>` in a clone lists every tag that includes it |
| What changed between two releases? | `git log --oneline v6.0.13..v6.0.14` |
| Which version does a site run? | Only its owner knows for sure; do not guess from screenshots |

## When the results disagree

| Disagreement | Likely reason | What to print |
|---|---|---|
| Tag date and GitHub release date differ | The release was published later than the tag; the release overview lists both, for example 6.0.13: tag 18 April 2026, release 20 April 2026 | The tag date, and say so ([Verifying dates](verifying-dates.md)) |
| Packagist does not show a tag that GitHub shows | Packagist has not read it yet | Wait, or say "tagged on GitHub" |
| Your clone does not show a tag | You have not fetched it | Run `git fetch --tags` |
| A blog says a version exists that you cannot find | It may be the development line or an error | Do not print it ([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)) |

## Related pages

- [Published versions never change](published-versions-never-change.md)
- [Verifying dates](verifying-dates.md)
- [Fact-checking guide](fact-checking-guide.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
