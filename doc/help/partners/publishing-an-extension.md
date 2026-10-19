---
title: Publishing an extension as a partner
audience: partner
category: partnership
order: 420
summary: Step by step from a working extension in a customer project to a published, maintained package - separating customer code, metadata, license, README, repository, Composer and Packagist, versioning and release rules, announcing it and logging the points.
keywords: publish extension, release, Composer, Packagist, GitHub, ezinfo.php, extension.xml, version, license, README, tag, announce
type: help
---
# Publishing an extension as a partner

This page is for developers at partner companies who want to publish an extension. The technical basis is the
developers' guide [Extensions](../../guides/extensions.md); this page adds the partner's checklist around it.

## 1. Separate it from the customer project

- [ ] Remove customer names, URLs, credentials, content and business rules.
- [ ] Make customer-specific behavior a setting with a neutral default.
- [ ] Get the customer's written agreement to publish the generic part (if it was paid work).

## 2. Give it its metadata

From [Extensions, "Release it"](../../guides/extensions.md#7-release-it):

- [ ] `ezinfo.php` with `'Version'` (no `v` prefix), name, copyright, license and info URL.
- [ ] `extension.xml` with the same `<version>`, `<license>` and `<info_url>`.
- [ ] `composer.json` if you publish on Packagist; keep its version in step if you keep one.
- [ ] `php -l ezinfo.php` and `xmllint --noout extension.xml` pass.

## 3. License and notices

- [ ] A `LICENSE` file; for code built on Exponential, a GPL-compatible license (the project uses GPL v2 or later).
- [ ] Copyright lines naming your company and any earlier authors.
- [ ] Third-party code listed with its license.

## 4. README

```text
# <Extension name>
What it does in two sentences.
Requirements: Exponential <version range>, PHP <range>, other extensions.
Install: composer require <vendor>/<name>  (or git clone ...)
Activate: Setup > Extensions, or ActiveExtensions[] in settings
Configure: <settings>
Support: <issues URL>, paid support by <company> (optional)
License: GPL v2 or later.  Credits: <original authors, sponsors>
```

## 5. Repository and package

- [ ] A public Git repository with the full history you can publish.
- [ ] Issues enabled.
- [ ] Registered on Packagist, with the webhook so new tags appear.

## 6. Release

- [ ] Choose the version from the published tags, sorted by version (`git tag -l 'v*' --sort=version:refname`).
- [ ] Commit the version bump first, then tag that commit.
- [ ] Never move or delete a published tag; publish the next version instead.

## 7. Announce it

- A forum post on <https://share.exponential.earth/forums> and a project page on <https://projects.exponential.earth>.
- A short article or tutorial (500 points as an article, 500 as a tutorial of 1,500+ words,
  [The points system](the-points-system.md)).
- Log it ([Logging your partner activity](logging-partner-activity.md)).

## 8. Maintain it

- Test it against each new Exponential release ([Testing releases](testing-releases.md)).
- Answer issues; merge useful pull requests.
- When you stop maintaining it, say so in the README.

## Related pages

- [Extension and theme partners](extension-and-theme-partners.md)
- [Contributing extensions back upstream](contributing-extensions-back.md)
- [Extensions](../../guides/extensions.md)
- [Contribute extensions back](../contribute-extensions-back.md): publish an extension and what the GPL requires when you distribute it
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
