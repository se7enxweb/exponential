---
title: How Exponential releases happen
audience: community, admin, developer, hosting
category: community
order: 52
summary: How a change travels from a pull request to a tagged Exponential release, why published tags never change, how version numbers count, which lines are supported, and where community members can help along the way.
keywords: release, tag, version, main, dev-main, 6.0.x, immutable tags, changelog, Packagist, supported versions, upgrade, version numbers
type: help
---
# How Exponential releases happen

## Contents

1. [The path of a change](#the-path-of-a-change)
2. [Published versions never change](#published-versions-never-change)
3. [Version numbers](#version-numbers)
4. [Which lines are supported](#which-lines-are-supported)
5. [Where you can help](#where-you-can-help)

## The path of a change

```text
pull request  ->  review rounds  ->  squash and rebase  ->  merged on main (dev-main)
      ->  tested by maintainers and community  ->  tagged release (v6.0.x)  ->  Packagist  ->  your site
```

1. A change arrives as a pull request and goes through review ([Reviewing and being reviewed](../contributing/reviewing-and-being-reviewed.md)).
2. It is merged on `main`, the development line ("the 6.0.15 line, `dev-main`", `SECURITY.md`).
3. A release is tagged from `main` and published; every package is a GitHub repository and a Composer package
   ([Releases and version numbers](../../who-is-the-exponential-foundation.md#releases-and-version-numbers)).
4. Sites upgrade to it ([Update with Git](../../update-with-git.md), [Upgrading](../../../install/11-upgrading.md)).

The changes of each release are in `doc/changelogs/6.0/`, for example the
[6.0.14 changelog](../../../../changelogs/6.0/6.0.14.md).

## Published versions never change

"A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next
release, not as a corrected copy of the old one" (`SECURITY.md`). For you this means: a version number always means
the same code, and the way to get a fix is to upgrade.

## Version numbers

- "After `0.1.9` the next release is `0.1.10`; only the last position counts up unless you decide otherwise"
  ([Releases and version numbers](../../who-is-the-exponential-foundation.md#releases-and-version-numbers)).
- Sort versions with `git tag --sort=version:refname` or `sort -V`; a plain sort puts `0.0.4.10` before `0.0.4.8`.
- Each extension states its version in `ezinfo.php` and `extension.xml`
  ([Versioning rules](../../extension-development/part-7-releasing/versioning-rules.md)).

## Which lines are supported

| Version | Supported |
|---|---|
| 6.0.x, current line (`main`, `dev-main`, and the next tag cut from it) | Yes |
| 6.0.x released tags | Fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | No |

Source: `SECURITY.md`, "Supported Versions".

## Where you can help

| Stage | Help |
|---|---|
| Pull request | Review and test it ([Reviewing others](../contributing/reviewing-and-being-reviewed.md#reviewing-others)) |
| On `main` | Test the development line on your setup ([Testing releases](../contributing/testing-releases.md)) |
| After the release | Confirm fixes in their issues, upgrade, write about it ([Writing about a release](../../advocates/writing-about-a-release.md)) |

## Related pages

- [How the community works](README.md)
- [Staying informed](../joining/staying-informed.md)
- [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)
- [How updates work](../../customers/healthy/how-updates-work.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
