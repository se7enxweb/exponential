---
title: Composer-based bundles
audience: bundler, developer
category: partnership
order: 80
summary: How to build a bundle as a Composer project of your own - the root composer.json, the installer plugin, allowing it, your own extensions and repositories, constraints in the style the project uses, the post-install autoload step, and what never to do with Composer in a bundle.
keywords: Composer, composer.json, bundle, project, require, repositories, allow-plugins, exponential-legacy-installer, ezpublish-legacy, post-install-cmd, ezpgenerateautoloads, --no-dev, tilde, constraints
type: help
---
# Composer-based bundles

This page is for bundlers who manage the bundle as a Composer project: an agency starter kit, a distribution of their
own, an image build. Composer is the way Exponential and all its extensions are delivered, so a bundle that speaks
Composer gets updates the same way the project publishes them.

## Contents

1. [The shape of a Composer bundle](#the-shape-of-a-composer-bundle)
2. [A root composer.json](#a-root-composerjson)
3. [The installer plugin](#the-installer-plugin)
4. [Your own extensions](#your-own-extensions)
5. [Scripts after install](#scripts-after-install)
6. [Rules](#rules)

## The shape of a Composer bundle

```text
your-bundle/
    composer.json        your root package: requires se7enxweb/exponential and your parts
    composer.lock        committed: the exact versions of this bundle release
    kickstart.ini.template  or the exp:install options your build uses
    packages/            your site package(s), if any
    README.md, LICENSE   your notices
```

After `composer install` the kernel lands in the project root (`.`), next to your files, because the package type
`ezpublish-legacy` is installed there by the installer plugin
([3.2 C](../../install/03-getting-the-code.md#c-composer-require-into-an-empty-directory)).

## A root composer.json

A minimal example; replace the names and versions with yours and check each package exists before you use it:

```json
{
    "name": "example/exponential-bundle",
    "description": "Example bundle, based on Exponential",
    "license": "GPL-2.0-or-later",
    "type": "project",
    "require": {
        "se7enxweb/exponential": "~6.0.14",
        "example/example_theme": "~1.2.0"
    },
    "repositories": [
        { "type": "vcs", "url": "https://git.example.com/example/example_theme.git" }
    ],
    "config": {
        "allow-plugins": {
            "se7enxweb/exponential-legacy-installer": true
        }
    },
    "scripts": {
        "post-install-cmd": [ "@php bin/php/ezpgenerateautoloads.php" ],
        "post-update-cmd": [ "@php bin/php/ezpgenerateautoloads.php" ]
    }
}
```

- `license`: name your bundle's license with an SPDX identifier. Code that is combined with Exponential and
  distributed with it must be compatible with the GPL; ask counsel ([GPL obligations](gpl-obligations-when-distributing.md)).
- The `~` constraints follow the project's own style: the last position floats
  ([Versions, tags and pinning](versions-tags-and-pinning.md#tilde-constraints)).
- The `scripts` mirror what the kernel's own `composer.json` runs (`"legacy-scripts": ["@php bin/php/ezpgenerateautoloads.php"]`
  on `post-install-cmd` and `post-update-cmd`), because "Composer runs only the scripts of the root package, which is
  now yours".

## The installer plugin

Composer 2.2 and later asks whether to trust `se7enxweb/exponential-legacy-installer`. Allow it in `composer.json`
(as above) or with:

```bash
composer config allow-plugins.se7enxweb/exponential-legacy-installer true
```

"If you answer no, the kernel lands in `vendor/se7enxweb/exponential/` and the extensions in `vendor/` as well, where
nothing finds them." In a build pipeline nobody answers, so put the setting in the file.

## Your own extensions

- Publish each extension as its own Composer package (a `composer.json` with a name, a license and the type your
  existing extensions use), in a repository you control.
- Give each release a tag, and write the same version into `ezinfo.php` and `extension.xml` before tagging
  ([Extension metadata](../../specifications/6.0/extension-metadata.md#release-rule)).
- Activate the extensions in your site package, or in `settings/override/site.ini.append.php`
  (`[ExtensionSettings] ActiveExtensions[]`), as the [default extension distribution](../../features/6.0/default-extension-distribution.md#add-an-extension)
  page shows.
- Extensions of your own can live in a second extension root (`site.ini [ExtensionSettings]
  AdditionalExtensionDirectories[]`, empty by default), apart from third-party ones
  ([Additional extension directories](../../bc/6.0/AdditionalExtensionDirectories.md)).

## Scripts after install

After every `composer install` or `update`, the bundle needs fresh autoload arrays and caches. The script above does
the first; the rest is on [Caches and autoloads after building](caches-and-autoloads-after-building.md).

## Rules

- [ ] Build with `composer install` from your committed `composer.lock`; run `composer update` only when you prepare a
      new bundle release.
- [ ] Add `--no-dev` for anything that goes to production.
- [ ] Never edit files inside `vendor/` or inside a Composer-installed extension: the next install replaces them. Put
      changes in your own extension or send them upstream.
- [ ] Installations made of Git checkouts of the extensions must not run `composer update` blindly: "it replaces the
      checkouts" ([Default extension distribution](../../features/6.0/default-extension-distribution.md#leave-one-out)).
- [ ] Record the output of `composer show` with every bundle release.

## Related pages

- [Lock files and reproducible builds](lock-files-and-reproducible-builds.md)
- [Adding and removing extensions](adding-and-removing-extensions.md)
- [Agency starter kits](agency-starter-kits.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
