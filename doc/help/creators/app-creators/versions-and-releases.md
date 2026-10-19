---
title: Versions and releases of your extension
audience: app-creator, developer
category: app-creation
order: 460
summary: Release an extension the way Exponential's own extensions are released - choosing the next number from what is published, bumping ezinfo.php and extension.xml before the tag, annotated tags, release notes, and why a published version is never changed.
keywords: version, release, tag, semantic versioning, ezinfo.php, extension.xml, changelog, release notes, immutable tags, GitHub release, Packagist, app creator
type: help
---
# Versions and releases of your extension

This page is for application creators who publish releases. The rules are those Exponential's own extensions follow;
they keep installations able to tell which code they run.

## Contents

1. [A published version is permanent](#a-published-version-is-permanent)
2. [Choosing the number](#choosing-the-number)
3. [The release, step by step](#the-release-step-by-step)
4. [Release notes](#release-notes)

## A published version is permanent

A tag pushed to a public repository, and the Packagist version made from it, cannot be corrected: installations that
resolved it keep its contents. Never move, delete or re-cut a published tag; when a release is wrong, publish the next
one and say in its notes what was wrong with the previous.

## Choosing the number

- List what is published first: `git fetch --tags`, `git tag -l 'v*' --sort=version:refname | tail -5`, and the
  releases on the repository host. A plain lexical sort puts `1.0.10` before `1.0.9`; always sort by version.
- Only the last position counts on for fixes: `1.0.9` is followed by `1.0.10`.
- A change of behavior or API that others build on gets a higher position, as decided by the maintainer.

## The release, step by step

1. Working tree clean, tests pass ([Testing applications](testing-applications.md)).
2. Bump the version in `ezinfo.php` (`'Version' => "1.0.10"`), `extension.xml` (`<version>1.0.10</version>`), and any
   other version field the repository keeps, in one commit.
3. Write the release notes (changelog).
4. Tag that commit with an annotated tag (`v1.0.10`) and push the tag.
5. Create the release on the repository host with the notes.
6. Check Packagist shows the version ([Composer packages and Packagist](composer-and-packagist.md)).
7. Installations update their constraint and the about page (`/ezinfo/about`) shows the new number.

Never tag first and bump afterwards: the tag would carry the old number forever.

## Release notes

Group changes under **Added**, **Updated**, **Removed**, **Renamed** (the categories the kernel's changelogs use), say
what a site must do (new settings, schema steps, cron lines), and name breaking changes plainly.

## Related pages

- [Packaging your application as an extension](packaging-as-an-extension.md)
- [Source control and releases standard](../../../standards/source-control-and-releases.md)
- [Compatibility, releases and deployment](../../../standards/architecture/compatibility-releases-deployment.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
