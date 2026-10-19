---
title: Immutable releases and versions
audience: government, admin, project-manager, developer
category: public-sector
order: 30
summary: Why a released Exponential version never changes after publication, how versions are numbered and compared, how to pin and verify the version you run, and what this means for procurement records, audits and reproducible deployments.
keywords: immutable release, permanent tags, version number, versioning, Composer, Packagist, pin version, reproducible, software inventory, SBOM, procurement record, audit, composer.lock
type: help
---
# Immutable releases and versions

This page is for administrators, project managers and procurement or audit staff who need to know exactly which
software runs a public website and be sure it is the same code everyone else calls by that version. The policy is in
the [Source control and releases standard](../../../../standards/source-control-and-releases.md#releases).

## Contents
1. [The rule](#the-rule)
2. [How versions are numbered](#how-versions-are-numbered)
3. [Knowing and pinning what you run](#knowing-and-pinning-what-you-run)
4. [What it means for public bodies](#what-it-means-for-public-bodies)
5. [Related pages](#related-pages)

## The rule

"A tag, and the GitHub release made from it, is immutable once pushed." Composer and Packagist read a tag once and
cache it; a moved tag would give two different packages under one version number. Therefore:

- a pushed tag is never deleted, moved or re-cut, and a published release's assets are never replaced;
- a bad release is followed by the next version, whose notes say what was wrong with the previous one.

The same holds for extensions released by the project, which carry their version in `ezinfo.php` and
`extension.xml` matching the release.

## How versions are numbered

- The core line is 6.0.x; the published tags run from `v6.0.0` to `v6.0.14`, and 6.0.15 is in development on `main`
  (`6.0.x-dev`) ([standard, "Releases"](../../../../standards/source-control-and-releases.md#releases)).
- The last position keeps counting: after `.9` comes `.10`. Composer and Packagist compare each position as a number.
  A plain alphabetical sort gets this wrong; use `--sort=version:refname` or `sort -V` in scripts.
- The root `composer.json` has no `version` field; Composer takes the version from the tag.

## Knowing and pinning what you run

| Question | Where |
|---|---|
| Which Exponential version? | **Setup > System information**; the `ezpublish-version` row of `ezsite_data` |
| Which extension versions? | **Setup > Extensions** (each card), **Setup > Updates and packages**, Installed packages |
| Exactly which packages? | `composer.lock` in the installation; `composer show se7enxweb/exponential` |
| Did anyone change the code? | **Setup > Upgrade check**, **Check file consistency**; `php bin/php/checkmanifest.php --all` |

Keep `composer.lock` in your version control: it pins every package to an exact version and commit, so staging and
production install the same code.

## What it means for public bodies

- **Procurement and inventory**: a version number in a contract or a software inventory names one fixed set of
  files. Record the version and the date you deployed it.
- **Audit**: the file consistency check compares your files with the release's manifest (`share/filelist.md5`), so
  you can show the code is unchanged, or list what was changed on purpose.
- **Security fixes** arrive as new versions; plan for regular small upgrades rather than patches to old versions
  ([Security updates and disclosure](security-updates-and-disclosure.md)).
- **Signed tags**: the standard records that commits and tags are mostly unsigned ("Does not comply"); if your policy
  requires signature verification, note this gap and rely on checksums and the manifest instead.

## Related pages
- [Reading release notes](reading-release-notes.md)
- [Upgrade check before and after](upgrade-check-before-and-after.md)
- [File consistency check](../../../../features/6.0/file-consistency-check.md)
- [Licensing for hosts](../../../hosting/business/licensing-for-hosts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
