---
title: Composer packages and Packagist
audience: app-creator, developer
category: app-creation
order: 450
summary: Publish an extension as a Composer package - the package name and type, version constraints, tags, Packagist and its webhook, installing a package into an installation, and updating safely.
keywords: Composer, Packagist, composer.json, package, version constraint, tilde, tag, webhook, composer require, composer update, ezpublish-legacy-extension, app creator
type: help
---
# Composer packages and Packagist

This page is for application creators who publish their extension so installations can `composer require` it.
Exponential and every extension it ships are Composer packages on [Packagist](https://packagist.org/packages/se7enxweb/exponential).

## Contents

1. [Package basics](#package-basics)
2. [Versions and constraints](#versions-and-constraints)
3. [Publishing on Packagist](#publishing-on-packagist)
4. [Installing into an installation](#installing-into-an-installation)
5. [Updating safely](#updating-safely)

## Package basics

| Key | Value |
|---|---|
| `name` | `vendor/name` in lower case |
| `type` | `ezpublish-legacy-extension` |
| `extra.ezpublish-legacy-extension-name` | the directory name under `extension/` |
| `license` | an SPDX id, for example `GPL-2.0-or-later` |
| `require` | PHP version and libraries |

## Versions and constraints

- Versions come from Git tags (`v1.2.3`). A published tag is permanent: never move or delete it; publish the next
  version instead ([Versions and releases](versions-and-releases.md)).
- `~1.2.3` means `>=1.2.3 <1.3.0`: the last position given floats. This is how the root `composer.json` pins extensions
  (`"se7enxweb/eztags": "~2.4.11"`).

## Publishing on Packagist

1. Push the repository to a public host (the se7enxweb extensions are on GitHub).
2. Submit the repository URL on Packagist.
3. Add Packagist's webhook to the repository, so new tags appear without a manual update; a package that stays on an
   old version on Packagist usually has no webhook.
4. Tag a release; check that Packagist shows it.

## Installing into an installation

```bash
composer require yourvendor/expdemo:~1.0.0
php bin/php/ezpgenerateautoloads.php -e
```

Then activate it ([3.5 Extensions are Composer packages](../../../install/03-getting-the-code.md#35-extensions-are-composer-packages)).

## Updating safely

- Update one package deliberately (`composer update yourvendor/expdemo`), after reading its release notes; not
  `composer update` on everything on a live site.
- A hand-made `git clone` in place of a package Composer installed as an archive is replaced by the next update, history
  included; let Composer make clones (`preferred-install`) if you develop in place.
- Test the update on staging first ([A deployment pipeline](deployment-pipeline.md)).

## Related pages

- [Packaging your application as an extension](packaging-as-an-extension.md)
- [PHP and Composer standard](../../../standards/php-and-composer.md)
- [Getting the code](../../../install/03-getting-the-code.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
