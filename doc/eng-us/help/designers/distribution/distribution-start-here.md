---
title: "Distributing a theme: start here"
audience: designer, developer
category: theme-distribution
order: 10
featured: true
summary: The map for publishing a theme - GitHub repository, version numbers and permanent tags, releases and changelogs, Packagist, licensing choices, screenshots and demo sites, and the community sites share.exponential.earth, projects.exponential.earth and eZpedia.
keywords: distribution, publish theme, GitHub, Packagist, release, version, tag, changelog, license, screenshots, demo site, projects.exponential.earth, share.exponential.earth, ezpedia
type: help
---
# Distributing a theme: start here

This section is for designers whose theme extension works and who want others to find, install and trust it. Build
the extension first ([Theme extensions: start here](../extensions/theme-extensions-start-here.md)).

## The one rule to remember

**A published release is permanent.** A tag pushed to a public repository, and the Composer version Packagist reads
from it, can never be corrected. A mistake in a release is fixed by the next release. Read
[A pushed tag is permanent](a-pushed-tag-is-permanent.md) before your first release.

## A reading order

1. Where and how: [Choosing where to publish](choosing-where-to-publish.md),
   [Creating the GitHub repository](creating-the-github-repository.md),
   [A README that explains and sells](readme-that-explains-and-sells.md).
2. Licensing: [Licensing choices: an overview](licensing-choices-overview.md).
3. Versions and releases: [Choosing a version number](choosing-a-version-number.md),
   [Checking what is already published](checking-what-is-already-published.md),
   [Bumping the version before the tag](bumping-versions-before-the-tag.md), [GitHub releases](github-releases.md).
4. Composer: [Submitting a theme to Packagist](submitting-to-packagist.md),
   [Keeping Packagist up to date automatically](packagist-auto-update.md).
5. Being found: [Screenshots](screenshots-for-a-theme.md), [Demo sites](demo-sites.md),
   [projects.exponential.earth](listing-on-projects-exponential-earth.md),
   [share.exponential.earth](announcing-on-share-exponential-earth.md).
6. Final check: [Checklist: before you publish](distribution-checklist.md).

## Every page of this section

| Page | What it answers |
|---|---|
| [Choosing where to publish](choosing-where-to-publish.md) | GitHub, Packagist, project pages, your own site |
| [Creating the GitHub repository](creating-the-github-repository.md) | Step by step with `gh` |
| [Repository name, description and topics](repository-name-description-topics.md) | Being found on GitHub |
| [A README that explains and sells](readme-that-explains-and-sells.md) | The first page people read |
| [The first push and the default branch](first-push-and-default-branch.md) | `main` and the first push |
| [Choosing a version number](choosing-a-version-number.md) | Which number for which change |
| [After .9 comes .10](only-the-last-position-counts.md) | Only the last position counts |
| [Sorting tags correctly](sorting-tags-correctly.md) | `--sort=version:refname` |
| [Checking what is already published](checking-what-is-already-published.md) | Tags and releases first |
| [Bumping the version before the tag](bumping-versions-before-the-tag.md) | The release commit |
| [Annotated tags](annotated-tags.md) | Tags with a message |
| [A pushed tag is permanent](a-pushed-tag-is-permanent.md) | Why and what follows |
| [GitHub releases](github-releases.md) | `gh release create` |
| [Writing release notes](writing-release-notes.md) | What users need to know |
| [When a release goes wrong](when-a-release-goes-wrong.md) | Fix forward |
| [Changelog entries](changelog-prefixes.md) | Added, Updated, Removed, Renamed, Fixed |
| [Keeping a CHANGELOG.md](keeping-a-changelog-file.md) | The history file |
| [Submitting a theme to Packagist](submitting-to-packagist.md) | One-time setup |
| [Keeping Packagist up to date automatically](packagist-auto-update.md) | The GitHub hook |
| [Packagist shows an old version](stale-packagist-version.md) | Diagnosis |
| [Version constraints your users write](version-constraints-for-users.md) | `~`, `^` and pins |
| [File checksums](filelist-checksums.md) | `share/filelist.md5` |
| [Licensing choices: an overview](licensing-choices-overview.md) | Code, docs, images |
| [The GPL for theme code](gpl-for-theme-code.md) | Why most themes are GPL |
| [The GFDL for theme documentation](gfdl-for-theme-documentation.md) | Docs under the GFDL |
| [Creative Commons for images](creative-commons-for-images.md) | Images and graphics |
| [Third-party fonts, icons and libraries](third-party-fonts-icons-and-libraries.md) | Others' work inside yours |
| [Commercial terms next to the GPL](commercial-terms-and-gpl.md) | What can be sold, what cannot be restricted |
| [Copyright notices in files](copyright-notices-in-files.md) | Headers and notices |
| [Screenshots for a theme](screenshots-for-a-theme.md) | What to show |
| [Screenshot checklist](screenshot-checklist.md) | Before you upload |
| [Running a demo site](demo-sites.md) | A live preview |
| [Demo content for a theme](demo-content.md) | Content worth showing |
| [Listing on projects.exponential.earth](listing-on-projects-exponential-earth.md) | The community forge |
| [Announcing on share.exponential.earth](announcing-on-share-exponential-earth.md) | Forums and blogs |
| [Documenting on eZpedia](documenting-on-ezpedia.md) | The community wiki |
| [Issue tracker and support channel](issue-tracker-and-support-channel.md) | Where users ask |
| [A security policy for a theme](security-policy-for-a-theme.md) | Private reports |
| [Checklist: before you publish](distribution-checklist.md) | The final check |

## Related pages

- [Selling themes and services: start here](../sales/selling-start-here.md)
- [The maintenance life cycle: start here](../lifecycle/lifecycle-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
