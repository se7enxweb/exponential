---
title: Publishing extensions from a bundle
audience: bundler, partner, developer
category: partnership
order: 480
summary: How a bundler turns the generic extensions of a bundle into published extensions - separating them from customer work, metadata and license, a public repository and Packagist, releasing with versions that never move, and announcing and maintaining them.
keywords: publish extension, Packagist, Composer, ezinfo.php, extension.xml, license, README, release, tag, version, webhook, announce, maintain, bundle
type: help
---
# Publishing extensions from a bundle

This page is for bundlers whose bundle contains extensions that others could use. The partner's checklist is
[Publishing an extension as a partner](../partners/publishing-an-extension.md); this page follows it and adds what a
bundler should consider.

## Contents

1. [Which extensions to publish](#which-extensions-to-publish)
2. [The steps](#the-steps)
3. [Releases that never move](#releases-that-never-move)
4. [After publishing](#after-publishing)

## Which extensions to publish

| Extension in your bundle | Publish? |
|---|---|
| A generic feature (an operator, a datatype, an integration) | yes, it helps others and gets you fixes from them |
| Bundle glue (your installer hooks, your branding) | usually no; keep it in the bundle's repository |
| Customer-specific code | no, unless the customer agrees and you remove everything customer-specific |
| A fork of someone else's extension | send changes upstream first ([Contributing extensions back](../partners/contributing-extensions-back.md)) |

## The steps

From [Publishing an extension as a partner](../partners/publishing-an-extension.md):

1. **Separate it from the customer project**: no customer names, URLs, credentials, content or business rules;
   customer-specific behavior becomes a setting with a neutral default.
2. **Metadata**: `ezinfo.php` with `'Version'` (no `v` prefix), name, copyright, license and info URL;
   `extension.xml` with the same `<version>`, `<license>` and `<info_url>`; `composer.json` if you publish on
   Packagist; `php -l ezinfo.php` and `xmllint --noout extension.xml` pass.
3. **License and notices**: a `LICENSE` file; "for code built on Exponential, a GPL-compatible license (the project
   uses GPL v2 or later)"; third-party code listed with its license.
4. **README**: what it does, requirements, install, activate, configure, support, license.
5. **Repository and package**: a public Git repository, issues enabled, registered on Packagist "with the webhook so
   new tags appear".
6. **Release** (below).
7. **Announce** it: forum post, project page.

Name it with your own prefix, never `exp` ([Naming and branding a bundle](naming-and-branding-a-bundle.md#naming-your-code)).

## Releases that never move

- "Choose the version from the published tags, sorted by version (`git tag -l 'v*' --sort=version:refname`)."
- "Commit the version bump first, then tag that commit."
- "Never move or delete a published tag; publish the next version instead."

Packagist reads a tag once; a moved tag gives two different packages with one version number. Your bundle then pins
the published version like any other ([Versions, tags and pinning](versions-tags-and-pinning.md)).

## After publishing

- Require the published package in your bundle instead of a private copy.
- "Test it against each new Exponential release"; "Answer issues; merge useful pull requests"; "When you stop
  maintaining it, say so in the README."
- Log it with the partner team if you are a partner ([Logging your partner activity](../partners/logging-partner-activity.md)).

## Related pages

- [Extension bundles](extension-bundles.md)
- [Extensions (developer guide)](../../guides/extensions.md)
- [Extension and theme partners](../partners/extension-and-theme-partners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
