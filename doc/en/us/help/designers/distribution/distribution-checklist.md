---
title: "Checklist: before you publish"
audience: designer
category: theme-distribution
order: 400
featured: true
summary: The final checklist before a theme release goes public - repository, licenses, version and tag, release notes, Packagist, screenshots, listings, support and security - each item linked to the page that explains it.
keywords: checklist, publish, release checklist, before publishing, distribution checklist, tag, Packagist, license
type: help
---
# Checklist: before you publish

This page is for designers about to publish a release. The theme itself should already pass
[Checklist: the theme extension is ready](../extensions/checklist-theme-extension-ready.md).

## Repository

- [ ] Public repository, default branch `main`, issues enabled ([Creating the GitHub repository](creating-the-github-repository.md)).
- [ ] Description and topics set ([Repository name, description and topics](repository-name-description-topics.md)).
- [ ] README with first screen, install, license, support ([A README that explains and sells](readme-that-explains-and-sells.md)).
- [ ] `SECURITY.md` present ([A security policy for a theme](security-policy-for-a-theme.md)).

## Licenses

- [ ] `LICENSE` for the code; documentation and image licenses stated ([Licensing choices](licensing-choices-overview.md)).
- [ ] Every third-party item listed with its license ([Third-party fonts, icons and libraries](third-party-fonts-icons-and-libraries.md)).

## Version and tag

- [ ] Published tags and releases checked ([Checking what is already published](checking-what-is-already-published.md)).
- [ ] Version written into every field and committed before the tag ([Bumping the version before the tag](bumping-versions-before-the-tag.md)).
- [ ] `CHANGELOG.md` has the release ([Keeping a CHANGELOG.md](keeping-a-changelog-file.md)).
- [ ] Working tree clean; theme installed and viewed from this commit.
- [ ] Annotated tag pushed alone ([Annotated tags](annotated-tags.md)); remember it is permanent.

## Release and Composer

- [ ] GitHub release with notes ([GitHub releases](github-releases.md), [Writing release notes](writing-release-notes.md)).
- [ ] Packagist shows the new version within minutes ([Keeping Packagist up to date automatically](packagist-auto-update.md)).
- [ ] `composer require yourvendor/mytheme` works in a clean installation.

## Being found and supported

- [ ] Screenshots current ([Screenshot checklist](screenshot-checklist.md)); demo updated ([Running a demo site](demo-sites.md)).
- [ ] Project page and news on projects.exponential.earth ([Listing](listing-on-projects-exponential-earth.md)).
- [ ] Announcement for notable releases ([Announcing on share.exponential.earth](announcing-on-share-exponential-earth.md)).
- [ ] A site package, if the theme has one, submitted by pull request to [se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) (strongly encouraged by 7x and the Exponential Foundation; [how](../../site-packages/contribute-your-site-package.md)).
- [ ] Support channels stated ([Issue tracker and support channel](issue-tracker-and-support-channel.md)).

## Related pages

- [Distributing a theme: start here](distribution-start-here.md)
- [Checklist: one maintenance cycle](../lifecycle/lifecycle-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
