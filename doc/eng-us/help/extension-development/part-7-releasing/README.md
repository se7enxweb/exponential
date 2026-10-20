---
title: "Part 7: Releasing your extension"
audience: developer, designer, app-creator
category: extension-development
order: 70
summary: Part 7 of the book: release an extension so others can rely on it - version numbers, permanent tags, metadata bumps, Composer and Packagist, changelogs, GitHub releases, packages, tests, and a checklist.
keywords: book, part 7, release, version, tag, Packagist, Composer, changelog, GitHub release, ezinfo, extension.xml, filelist.md5, ezpkg, checklist
type: help
---
# Part 7: Releasing your extension

This is the seventh part of [Extension development: the book](../README.md). A release turns "the code in my
repository" into "version 1.2.3, which you can install and rely on". It is a promise: that this version will always
mean exactly these files. This part shows how to make that promise carefully, because once a version is published,
it cannot be taken back.

## The chapters

| Chapter | What it answers |
|---|---|
| [Versioning rules](versioning-rules.md) | what the numbers mean, and how the last position counts on |
| [Choosing the next version number](choosing-the-next-version.md) | looking up what is already published, correctly sorted |
| [Tags and releases are permanent](tags-are-permanent.md) | why a pushed tag is never moved, and what to do instead |
| [Bumping ezinfo.php and extension.xml](bumping-ezinfo-and-extension-xml.md) | the version inside the extension, before the tag |
| [Composer and Packagist](composer-and-packagist.md) | registering the package so `composer require` finds it |
| [The Packagist webhook](the-packagist-webhook.md) | why a new tag does not show up, and how to fix it |
| [Changelogs](changelogs.md) | writing what changed, from the commit messages |
| [GitHub releases and release notes](github-releases-and-release-notes.md) | the release page users read |
| [filelist.md5 and the upgrade check](filelist-md5-and-the-upgrade-check.md) | checksums for extensions that ship them |
| [Packages (.ezpkg) and the package creator](packages-ezpkg.md) | the other way to distribute |
| [Testing before a release](testing-before-a-release.md) | what to run before you tag |
| [Announcing a release](announcing-a-release.md) | telling the people who use it |
| [The maintenance life cycle](the-maintenance-life-cycle.md) | from the first release to the last |
| [The release checklist](the-release-checklist.md) | the whole procedure on one page |

## The one rule

**Never move, delete or re-create a tag that has been pushed, and never replace the files of a published release.**
If a release is wrong, publish the next version with the fix and say what was wrong in its notes. The reasons are in
[Tags and releases are permanent](tags-are-permanent.md); everything else in this part is about getting it right the
first time.

## Related pages

- Previous: [Where the designers' help continues](../part-6-theme-developers/where-the-designers-help-continues.md)
- Next: [Versioning rules](versioning-rules.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Extension development: the book](../README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
