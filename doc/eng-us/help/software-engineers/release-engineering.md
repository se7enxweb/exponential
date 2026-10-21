---
title: Release engineering
audience: software-engineer, developer
category: software-engineering
order: 600
summary: How releases of Exponential and its extensions are made - the main branch and its 6.0.x-dev alias, where the kernel version lives, the extension release procedure with ezinfo.php and extension.xml, the file manifest, checks before tagging, GitHub releases and Packagist, and what to do when a release turns out bad.
keywords: release engineering, release, tag, version, lib/version.php, ezinfo.php, extension.xml, filelist.md5, GitHub release, Packagist, webhook, release checklist, software engineer
type: help
---
# Release engineering

This page is for engineers who cut releases of the kernel or of an extension, and for those who need to know what a
release guarantees. The rules come from [Source control and releases](../../standards/source-control-and-releases.md)
and [Compatibility, releases and deployment](../../standards/architecture/compatibility-releases-deployment.md); the
course for extension maintainers is [Releasing](../extension-development/part-7-releasing/README.md).

## Contents

1. [Lines and versions](#lines-and-versions)
2. [Before you tag](#before-you-tag)
3. [Releasing an extension](#releasing-an-extension)
4. [Publishing](#publishing)
5. [When a release is bad](#when-a-release-is-bad)

## Lines and versions

- `main` is the development line. `composer.json` aliases it as `6.0.x-dev` (`extra.branch-alias`), and CI runs on it.
- The kernel version lives in `lib/version.php` (`VERSION_MAJOR`, `VERSION_MINOR`, `VERSION_RELEASE`) and in the
  database row `ezsite_data` `ezpublish-version`, which the update SQL writes. The root `composer.json` has no
  `version` field; Composer takes the version from the tag.
- Kernel releases are tagged `v6.0.<n>`. Tags `v6.0.0` to `v6.0.14` exist; the 6.0.15 line is in development.
- Extensions are separate Composer packages with their own versions.

Choosing a number: [Version numbers and immutable tags](version-numbers-and-immutable-tags.md).

## Before you tag

A tag is cheap to get right and impossible to correct. Before tagging:

- [ ] The working tree is clean.
- [ ] Quality and PHPUnit are green on the commit you will tag ([CI gates](ci-gates.md)).
- [ ] The version fields are bumped in the commit the tag points to.
- [ ] `share/filelist.md5` matches the files (`php bin/php/checkmanifest.php --all`).
- [ ] The changelog page for the version exists ([Changelogs and upgrade notes](changelogs-and-bc-notes.md)).
- [ ] Every upgrade-relevant change has its page in `doc/eng-us/bc/`.
- [ ] Build artifacts committed to the repository match their sources.

## Releasing an extension

Every release writes its number into the extension, in the commit the tag points to:

1. Choose the number from what is already published.
2. Set `'Version'` in `ezinfo.php` and `<metadata><version>` in `extension.xml` to it, without the `v` prefix (tag
   `v1.8.4` gives `1.8.4`). Never leave `//autogentag//` or `dev-master`. Both files also state the license and an
   `info_url` on the extension's public repository.
3. Move any other version field the repository keeps (`version` in its `composer.json`, the header of its own
   `share/filelist.md5`) and refresh that manifest's checksums.
4. Check: `php -l ezinfo.php` and `xmllint --noout extension.xml`.
5. Commit, **then** tag that commit, then push both. Never tag first and bump afterwards.

Extensions shipped inside the kernel repository carry the kernel's version. The about page reads both files
([About and package pages](../../features/6.0/about-and-package-pages.md), [Extension metadata](../../specifications/6.0/extension-metadata.md)).

## Publishing

- **GitHub release**: made from the tag, with notes taken from the changelog ([GitHub releases and release notes](../extension-development/part-7-releasing/github-releases-and-release-notes.md)).
- **Packagist**: each `se7enxweb/*` repository is a Composer package. A webhook on the GitHub repository tells
  Packagist about new tags; a package without it stays at an old version until updated by hand
  ([The Packagist webhook](../extension-development/part-7-releasing/the-packagist-webhook.md)).
- **Repinning the root**: release the extension, see the version on Packagist, then commit the root's new constraint.
  The push gate refuses a constraint Packagist cannot satisfy yet, because CI's install would fail on every leg.

## When a release is bad

Do not move, delete or re-cut the tag, and do not replace the release's assets. Publish the next version with the fix,
and say plainly in its notes what was wrong with the previous one. Consumers who pin ranges pick up the new version;
nobody is helped by a hole in the history.

## Related pages

- [The release checklist](../extension-development/part-7-releasing/the-release-checklist.md)
- [Versions and releases, for application creators](../creators/app-creators/versions-and-releases.md)
- [Dependency management with Composer](dependency-management-with-composer.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
