---
title: Version numbers and immutable tags
audience: software-engineer, developer
category: software-engineering
order: 610
summary: The two release rules every Exponential engineer must know - the last position of a version keeps counting (0.0.4.9 is followed by 0.0.4.10), and a published tag or release is never moved, deleted, re-cut or replaced - with why, how to choose the next number from what is published, the sorting trap, and how Composer constraints behave.
keywords: version numbers, versioning, last position, 0.0.4.10, tags, immutable, permanent, re-cut, sort -V, version:refname, Composer, tilde constraint, Packagist, semantic versioning, software engineer
type: help
---
# Version numbers and immutable tags

This page is for engineers who choose version numbers or push tags for Exponential or any of its packages. Two rules
apply to every package of the project, and both are easy to break by habit.

## Contents

1. [Rule 1: the last position keeps counting](#rule-1-the-last-position-keeps-counting)
2. [Rule 2: a published tag never changes](#rule-2-a-published-tag-never-changes)
3. [Choosing the next number](#choosing-the-next-number)
4. [The sorting trap](#the-sorting-trap)
5. [How Composer sees it](#how-composer-sees-it)

## Rule 1: the last position keeps counting

After `x.y.9` comes `x.y.10`, never `x.(y+1).0`. The Velocity history shows `0.0.4.9` followed by `0.0.4.10`. There is
no "running out" of digits: the last position is an integer. Moving a higher position is a separate decision about
what the release means, never a consequence of reaching nine ([Source control and releases](../../standards/source-control-and-releases.md#choosing-the-next-version-number)).

## Rule 2: a published tag never changes

A tag, and the GitHub release made from it, is immutable once pushed:

- never delete, move, re-point or re-cut a pushed tag, not even to fix a typo or add a forgotten file;
- never replace the assets of a published release;
- a bad release is followed by the next version, whose notes say what was wrong.

**Why.** Composer and Packagist read a tag once and cache the version they found. A moved tag gives two different
packages under one version number: installations that resolved it earlier keep the old contents, new ones get the new
contents, and nobody can tell from the outside which they have.

## Choosing the next number

Always from what is already published, never from memory or from the working tree:

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
gh release list --limit 10
```

Compare both lists: a tag can exist without a release, and a release can exist for a tag the local clone has not
fetched.

## The sorting trap

By name, `v0.0.4.10` sorts before `v0.0.4.8`:

```bash
git tag -l 'v0.0.4.*' | tail -1                 # wrong: a name sort
git tag -l 'v0.0.4.*' --sort=version:refname    # right
git tag -l 'v0.0.4.*' | sort -V | tail -1       # right
```

Any script that picks "the newest tag" must sort by version.

## How Composer sees it

Composer and Packagist compare each position numerically, so `0.0.4.10` is newer than `0.0.4.9`. A tilde constraint
floats the last position: `~0.0.4.9` means `>=0.0.4.9 <0.0.5.0`, which already accepts `0.0.4.10`. The root
`composer.json`, for example, requires `se7enxweb/exponential-velocity` at `~0.0.4.57`. Repinning to a higher minimum
is for clarity, and must wait until the version is on Packagist ([Dependency management with Composer](dependency-management-with-composer.md)).

## Related pages

- [Release engineering](release-engineering.md)
- [Tags are permanent](../extension-development/part-7-releasing/tags-are-permanent.md)
- [Choosing the next version](../extension-development/part-7-releasing/choosing-the-next-version.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
