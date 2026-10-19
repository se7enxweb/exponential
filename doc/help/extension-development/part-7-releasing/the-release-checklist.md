---
title: The release checklist
audience: developer, designer, app-creator
category: extension-development
order: 140
summary: The whole release of an extension on one page, in order - choose the number, bump the files, changelog, test, tag, push, GitHub release, Packagist, announce - each step linked to its chapter.
keywords: release checklist, release procedure, version, tag, ezinfo, extension.xml, changelog, tests, push, GitHub release, Packagist, announce
type: help
---
# The release checklist

Part 7, chapter 14, the last of this part. Every step of a release, in order. Copy it into an issue or your notes for
each release, and tick it off.

## Before

- [ ] Every change of this release is committed and pushed to the main branch; CI is green for the last commit.
- [ ] Fetch the tags and list the newest five, sorted as versions:
      `git fetch --tags && git tag -l 'v*' --sort=version:refname | tail -5`
      ([Choosing the next version number](choosing-the-next-version.md)).
- [ ] Compare with `gh release list` and the Packagist page.
- [ ] Choose the next number: patch, minor or major ([Versioning rules](versioning-rules.md)). After `.9` comes `.10`.

## The release commit

- [ ] `ezinfo.php` `'Version'` and `extension.xml` `<version>` set to the new number, without `v`
      ([Bumping ezinfo.php and extension.xml](bumping-ezinfo-and-extension-xml.md)).
- [ ] `composer.json` `version` too, if the file has one.
- [ ] `share/filelist.md5` refreshed, if the extension ships one ([filelist.md5 and the upgrade check](filelist-md5-and-the-upgrade-check.md)).
- [ ] The changelog entry written, upgrade notes first ([Changelogs](changelogs.md)).
- [ ] Committed: `Updated: Version 1.2.4`.

## Check

- [ ] `git status` is clean; `git log -1` is the release commit.
- [ ] `php -l ezinfo.php`, `xmllint --noout extension.xml`, `composer validate`.
- [ ] Lint and tests on the oldest and newest supported PHP ([Testing before a release](testing-before-a-release.md)).
- [ ] A clean install from the repository on a test site, following the README.
- [ ] Every feature used once; no errors, warnings, deprecations or CSP reports from the extension.
- [ ] An update from the previous release works with the upgrade notes as written.

## Tag and publish

- [ ] `git tag -a v1.2.4 -m "Version 1.2.4"` on the release commit.
- [ ] `git push origin main` and `git push origin v1.2.4` (the tag by name).
- [ ] From here the version is permanent ([Tags and releases are permanent](tags-are-permanent.md)).
- [ ] `gh release create v1.2.4 --title "1.2.4" --notes-file ...` ([GitHub releases and release notes](github-releases-and-release-notes.md)).
- [ ] The Packagist page shows 1.2.4 within minutes; if not, check the hook ([The Packagist webhook](the-packagist-webhook.md)).

## After

- [ ] Install the released version (`composer require your-account/myext:1.2.4`) on a test site and open the about page:
      the version shows 1.2.4.
- [ ] Announce where it matters ([Announcing a release](announcing-a-release.md)).
- [ ] Close the issues the release fixed, with a link to the release.
- [ ] If something is wrong: do not touch the tag or the release; fix it and release 1.2.5, saying what was wrong
      with 1.2.4.

## Related pages

- Previous: [The maintenance life cycle](the-maintenance-life-cycle.md)
- Next: [Part 8: Reference of built-in features for extension authors](../part-8-reference/README.md)
- [Part 7: Releasing your extension](README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
