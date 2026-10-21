---
title: Bundle release checklist
audience: bundler, partner
category: partnership
order: 440
summary: The checklist to work through before a bundle release goes out - versions chosen from what is published, pinned parts, licenses and notices, security defaults, tests, integrity, documentation, the source offer, naming, and publishing a release that will never be changed.
keywords: release checklist, bundle release, version, tag, immutable, lock file, licenses, notices, source offer, tests, integrity, documentation, naming, publish
type: help
---
# Bundle release checklist

This page is for whoever presses "publish" on a bundle. Work through it for every release; a published release
cannot be taken back, only followed by the next one.

## Contents

1. [Before you start](#before-you-start)
2. [Contents of the bundle](#contents-of-the-bundle)
3. [Licenses and notices](#licenses-and-notices)
4. [Safety](#safety)
5. [Tests](#tests)
6. [Documentation](#documentation)
7. [Publishing](#publishing)

## Before you start

- [ ] The next version number is chosen from what is **already published** (your tags and releases, listed with a
      version sort: `git tag -l 'v*' --sort=version:refname`), not from memory.
- [ ] The Exponential version is chosen from the published tags, also with a version sort
      ([Versions, tags and pinning](versions-tags-and-pinning.md#find-the-newest-release-correctly)).
- [ ] Release notes of every Exponential release since your last bundle are read, including upgrade notes.

## Contents of the bundle

- [ ] Kernel pinned to a tag (or a recorded commit); `composer.lock` committed; built with `composer install --no-dev`.
- [ ] Extensions added or removed on purpose and recorded ([Adding and removing extensions](adding-and-removing-extensions.md)).
- [ ] Site package `.ezpkg` files are the tested ones, with checksums ([Site packages in a bundle](site-packages-in-a-bundle.md)).
- [ ] Runtime pinned: base image digest, PHP version, Velocity version.
- [ ] Autoloads regenerated; no build caches shipped ([Caches and autoloads](caches-and-autoloads-after-building.md)).
- [ ] Your version is written into your files in the commit you tag.

## Licenses and notices

- [ ] `LICENSE`, `LICENSE.md`, `COPYRIGHT.md` from the shipped version included ([License texts and notices](license-texts-and-notices-to-ship.md)).
- [ ] Third-party list generated and checked ([Third-party components](third-party-components-and-their-licenses.md)).
- [ ] Your own parts have consistent license fields ([License fields](license-fields-in-your-own-packages.md)).
- [ ] The corresponding source is published or offered, and the release notes say where ([GPL obligations](gpl-obligations-when-distributing.md)).
- [ ] Documentation keeps its GFDL notices ([Documentation to ship](documentation-to-ship.md)).

## Safety

- [ ] No `settings/override/`, `settings/siteaccess/`, `kickstart.ini`, logs or `initial-admin-password` from the build.
- [ ] No fixed administrator password; hand-over documented ([Default credentials](default-credentials-and-first-sign-in.md)).
- [ ] No `EXP_DEMO_MODE` outside demo bundles ([Demo mode in bundles](demo-mode-in-bundles.md)).
- [ ] Hardened file modes and ownership ([File permissions](file-permissions-and-var-ownership.md)).
- [ ] Go-live items a bundle can settle are settled ([Security defaults](security-defaults-of-a-bundle.md)).

## Tests

- [ ] Test matrix run and logged ([Testing a bundle](testing-a-bundle.md)).
- [ ] Upgrade from the previous bundle release tested.
- [ ] `php bin/php/checkmanifest.php --all` result recorded ([Integrity checks](integrity-checks.md)).
- [ ] Working tree clean; the artifact matches the tagged sources.

## Documentation

- [ ] Getting started, addresses, password hand-over.
- [ ] What is inside, with versions.
- [ ] How to update and what the database steps are ([Updates and upgrade paths](updates-and-upgrade-paths.md)).
- [ ] Support boundaries ([Support boundaries](support-boundaries.md)).
- [ ] Changelog with Added, Updated, Removed, Renamed.

## Publishing

- [ ] The name and description follow the brand rules ([Naming and branding](naming-and-branding-a-bundle.md)).
- [ ] Checksums (`SHA256SUMS`) published with the artifacts.
- [ ] The tag is pushed **after** every check above passed. Never move, delete or re-cut it afterwards; a mistake
      is fixed by the next release, with a note about what was wrong.
- [ ] The release is announced to your recipients, and, if you are a partner, logged with the partner team
      ([Logging your partner activity](../partners/logging-partner-activity.md)).

## Related pages

- [Bundle worksheet](bundle-worksheet.md)
- [Testing a bundle](testing-a-bundle.md)
- [Versions, tags and pinning](versions-tags-and-pinning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
