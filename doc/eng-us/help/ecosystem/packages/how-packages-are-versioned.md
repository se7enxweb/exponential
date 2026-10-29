---
title: How packages are versioned in the ecosystem
audience: ecosystem, developer, admin, partner
category: ecosystem
order: 23
summary: The versioning rules that hold across the Exponential ecosystem - published tags never change, the last position counts on past nine, version sorting, the tilde constraint, and version fields inside an extension - and what they mean for someone who depends on a package.
keywords: versioning, version numbers, tags, immutable tags, semantic, tilde constraint, sort -V, version:refname, ezinfo.php, extension.xml, Packagist
type: help
---
# How packages are versioned in the ecosystem

## Contents

1. [Published versions never change](#published-versions-never-change)
2. [The last position counts on](#the-last-position-counts-on)
3. [Constraints in composer.json](#constraints-in-composerjson)
4. [Versions inside an extension](#versions-inside-an-extension)
5. [What this means for you](#what-this-means-for-you)

## Published versions never change

"A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next
release, not as a corrected copy of the old one" (`SECURITY.md`). The same holds for extensions
([Tags are permanent](../../extension-development/part-7-releasing/tags-are-permanent.md)).

## The last position counts on

"After `0.1.9` the next release is `0.1.10`; only the last position counts up unless you decide otherwise"
([Releases and version numbers](../../who-is-the-exponential-foundation.md#releases-and-version-numbers)). A plain
alphabetical sort puts `0.0.4.10` before `0.0.4.8`; sort with `git tag --sort=version:refname` or `sort -V`.
Composer and Packagist compare each position as a number.

## Constraints in composer.json

The kernel's constraints use `~`, which "lets the last position float: `~6.0.8` accepts `6.0.9` and `6.0.10` but not
`6.1.0`" ([The default extension distribution](../../../features/6.0/default-extension-distribution.md)). Velocity, for
example, is required at `~0.0.4.57` (`README.md`).

## Versions inside an extension

An extension states its release number in `ezinfo.php` and `extension.xml`
([Bumping ezinfo.php and extension.xml](../../extension-development/part-7-releasing/bumping-ezinfo-and-extension-xml.md)),
so the about page shows which release is installed ([About and package pages](../../../features/6.0/about-and-package-pages.md)).

## What this means for you

- A version number always means the same code.
- To get a fix, upgrade to the next version.
- When you choose a version, look at the published tags sorted by version, not at memory.
- Your own constraints with `~` pick up fixes in the last position automatically on `composer update`.

## Related pages

- [Packages](README.md)
- [Versioning rules](../../extension-development/part-7-releasing/versioning-rules.md)
- [How releases happen](../../community/how-it-works/how-releases-happen.md)
- [Choosing the next version](../../extension-development/part-7-releasing/choosing-the-next-version.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
