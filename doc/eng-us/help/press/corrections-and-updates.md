---
title: Corrections and updates
audience: press
category: press-media
order: 136
summary: How newsrooms correct and update coverage of Exponential - the difference between a correction and an update, when a release, a renamed product or a newer count makes a story outdated, correction notes that name the source, and how to tell the project when its own documentation is wrong.
keywords: corrections, updates, errata, correction note, outdated, update note, accuracy, newsroom, editorial standards, archive
type: help
---
# Corrections and updates

This page is for editors and reporters who look after published coverage. Stories about software age quickly:
versions change, counts grow, features move from "in development" to "released". This page separates corrections
from updates and gives notes you can adapt to your house style.

## Contents

1. [Correction or update](#correction-or-update)
2. [Errors that need a correction](#errors-that-need-a-correction)
3. [Changes that need an update](#changes-that-need-an-update)
4. [Notes](#notes)
5. [When the documentation was wrong](#when-the-documentation-was-wrong)

## Correction or update

| | Correction | Update |
|---|---|---|
| When | The story was wrong when published | The story was right, but things changed |
| Example | "eZ Publish 6.0.14", "the 7x Foundation" | 6.0.15 was released after publication |
| Note | Required, says what was wrong | Recommended, says what changed and when |

## Errors that need a correction

- A wrong product or organization name ([Common errors in coverage](common-errors-in-coverage.md)).
- A version called released that was in development.
- A number without a source, or with the wrong conditions ([Verifying numbers](verifying-numbers.md)).
- A quote that was not said or written by the person ([Quotes policy](quotes-policy.md)).
- An implied association with eZ Systems or Ibexa ([Not associated with eZ Systems or Ibexa](not-associated-with-ez-systems-or-ibexa.md)).
- A security detail published before its fix ([Covering security fixes](covering-security-fixes.md)).

## Changes that need an update

| Change | Where you see it |
|---|---|
| A new release is tagged | `git tag -l 'v6.0.*' --sort=version:refname`; [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md) |
| A feature described as coming is now released | The release's changelog |
| A count has grown | [Exponential by the numbers](exponential-by-the-numbers.md) |
| An organization changes its self-description | Its website |

You are not obliged to update every old story; add an update note where readers are likely to rely on it, such as a
how-to or a buyer's guide.

## Notes

```text
Correction, <date>: An earlier version of this article called the software "eZ Publish 6.0.14". The
product has been called Exponential since August 2025; the release is Exponential 6.0.14.

Correction, <date>: An earlier version said Exponential Intelligence publishes articles. According to the
project's documentation, it writes drafts and never publishes on its own.

Update, <date>: Exponential 6.0.15, described here as in development, was released on <tag date>
(source: <link>).
```

## When the documentation was wrong

If you relied on this documentation and it was wrong, you are not alone in the error. Tell the project on the
[community issue tracker](https://github.com/se7enxweb/exponential-community/issues) or with **Edit on GitHub** on the
page; the fix shows up in the page's history, which you can cite in your correction note.

## Related pages

- [Correcting a video or post](correcting-a-video-or-post.md)
- [Fact-checking guide](fact-checking-guide.md)
- [Verifying dates](verifying-dates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
