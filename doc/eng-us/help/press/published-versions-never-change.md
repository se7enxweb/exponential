---
title: Published versions never change
audience: press
category: press-media
order: 104
summary: Why an Exponential release, once tagged, is permanent - what the project says about it, why Packagist makes it so, what happens when a release has a mistake, and what that means for accurate reporting and for corrections.
keywords: immutable, tag, release, permanent, Packagist, fix, next version, re-cut, correction, reliability, pinning
type: help
---
# Published versions never change

This page is for reporters and creators who write about Exponential releases, fixes and reliability. One rule shapes
how the project publishes: a version that has been released is never altered afterwards. Knowing the rule helps you
describe fixes correctly and explains why a story about a version stays accurate.

## Contents

1. [The rule in the project's words](#the-rule-in-the-projects-words)
2. [Why it is so](#why-it-is-so)
3. [What happens when a release has a mistake](#what-happens-when-a-release-has-a-mistake)
4. [What it means for your coverage](#what-it-means-for-your-coverage)
5. [Phrases to use and avoid](#phrases-to-use-and-avoid)

## The rule in the project's words

| Statement | Source |
|---|---|
| "A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next release, not as a corrected copy of the old one." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| "A release tag never changes. Once published, a tag and the package version Packagist built from it are permanent; a fix always arrives as the next version." | [Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches) |
| "If a published version is wrong, do not move or delete its tag. Publish the next version with the fix and say in its notes what was wrong with the previous one." | [Extensions guide, Release it](../../guides/extensions.md#7-release-it) |
| "A tag is permanent: a bad release is corrected by the next one, never re-cut." | [Velocity release notes](../../../changelogs/extensions/exponential-velocity.md) |

The Foundation's page lists the rule among the project's commitments under
[Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers).

## Why it is so

Every Exponential package is published through Packagist, the Composer package directory. The extensions guide
explains the technical reason: "Packagist caches what it read, so a moved tag produces two different packages under
one version number" ([Extensions guide](../../guides/extensions.md#7-release-it)). Two different sets of files under
one number would make it impossible to know what a site runs. Keeping tags fixed means "You can pin a tag and rely on
getting the same files every time, which is what makes a pinned version the right choice for production"
([Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches)).

## What happens when a release has a mistake

The documented answer is always the next version. A real example: the Velocity release notes say three tags have no
release page, `v0.0.4.21`, `v0.0.4.22` and `v0.0.4.26`, because "their builds did not produce a usable artifact;
they are still installable versions on Packagist, so move to the next version above them"
([Velocity release notes](../../../changelogs/extensions/exponential-velocity.md)). The tags were left in place and the
fix came as later versions.

## What it means for your coverage

| Situation | How to report it |
|---|---|
| A bug is found in 6.0.14 | "The fix will be in a later version"; never "6.0.14 was updated" |
| A security fix is published | "Fixed in version X; earlier versions are fixed by upgrading" ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |
| A version had a packaging problem | Say which version, and which later version replaced it, with the release notes as source |
| You wrote about a version's contents | Your description stays valid; the files behind that number will not change |

Two consequences for accuracy:

- A version number is a precise reference. "Exponential 6.0.14" names exactly one set of files, so always print the
  full number ([How version numbers work](how-version-numbers-work.md)).
- Work after a tag belongs to the next version, never retroactively to the old one. Changes made after 7 June 2026
  are part of the 6.0.15 line, not of 6.0.14 ([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)).

## Phrases to use and avoid

| Avoid | Use |
|---|---|
| "a patched 6.0.14" | "the next version after 6.0.14" |
| "the release was pulled" | Only if a source says so; the documented practice is to leave tags in place |
| "6.0.14 was re-released" | Not how the project works; check the tag list ([Verifying a release yourself](verifying-a-release-yourself.md)) |
| "silently fixed" | "fixed in version X, as its changelog lists" |

## Related pages

- [How version numbers work](how-version-numbers-work.md)
- [Covering security fixes](covering-security-fixes.md)
- [Corrections and updates](corrections-and-updates.md) (the same principle for your own stories)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
