---
title: The se7enxweb forks and replace
audience: dxp-developer
category: dxp-development
order: 6
summary: Why a DXP project installs se7enxweb packages under upstream names, how Composer's replace makes third-party bundles keep working, how to check which package provides a class, and the one rule - never install a fork and its upstream together.
keywords: forks, replace, se7enxweb, ibexa, ezsystems, netgen, Composer, composer why, package map, autoloader, upstream, vendor
type: help
---
# The se7enxweb forks and replace

This page is for developers who see `se7enxweb/*` packages in `composer.lock` and `Ibexa\` namespaces in the code and
want to understand how the two fit together. It explains the forks, the `replace` mechanism and how to check your
project.

## Contents

1. [Why there are forks](#why-there-are-forks)
2. [Which DXP packages are forks](#which-dxp-packages-are-forks)
3. [How replace works](#how-replace-works)
4. [Checking your project](#checking-your-project)
5. [The rule: never both](#the-rule-never-both)
6. [Namespaces stay the same](#namespaces-stay-the-same)

## Why there are forks

The DXP continues the eZ Platform and Ibexa OSS line. Where an upstream package needed a change for this line,
7x forked it under the `se7enxweb` vendor: product naming in the administration, build paths that work with only
se7enxweb packages installed, PHP 8.4 compatibility, and the `exponential:` command names
([Platform package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)).
Where no change was needed, the DXP requires the upstream package as it is.

## Which DXP packages are forks

| se7enxweb package in the metapackage | Replaces | What the fork changes |
|---|---|---|
| `se7enxweb/exponential-platform-dxp-core` | `ibexa/core` | Product naming in user facing templates, `exponential:` command names ([command names](../../specifications/6.0/platform-console-commands.md)) |
| `se7enxweb/admin-ui` | `ibexa/admin-ui`, `ezsystems/ezplatform-admin-ui` | Branding, package identity, build paths ([Platform administration interface](../../features/6.0/platform-admin-ui-fork.md)) |
| `se7enxweb/admin-ui-assets` | `ibexa/admin-ui-assets`, `ezsystems/ezplatform-admin-ui-assets` | Shared admin assets under the se7enxweb vendor |
| `se7enxweb/fieldtype-richtext` | `ibexa/fieldtype-richtext`, `ezsystems/ezplatform-richtext` | XSL stylesheets and webpack alias inside the fork |
| `se7enxweb/system-info` | `ibexa/system-info`, `ezsystems/ez-support-tools` | System information view |
| `se7enxweb/layouts-core` | `netgen/layouts-core` | Getters that Twig needs on PHP 8.4 ([Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md)) |

Every other `ibexa/*` package in the metapackage is the upstream package. The full table of the family's forks is the
[Platform package map](../../specifications/6.0/platform-package-map.md).

## How replace works

A package that lists another under `replace` tells Composer that it provides that package. Composer then installs
only the fork, and every package that requires the upstream name is satisfied by it. Since 2026-04-12 the forks
declare `replace` with the wildcard `*`, so the fork satisfies any version constraint another package puts on the
upstream name. One exception you can see in an installed project: `se7enxweb/fieldtype-richtext` replaces
`ibexa/fieldtype-richtext` with `self.version` and `ezsystems/ezplatform-richtext` with `*` (its `composer.json`)
([Step 1](../../bc/6.0/platform-package-forks-and-command-renames.md#step-1-packages-install-from-the-se7enxweb-vendor)).

The result: a third-party bundle that requires `ibexa/admin-ui` installs in a DXP project and uses `se7enxweb/admin-ui`.

## Checking your project

```bash
composer why ibexa/admin-ui                              # expect: se7enxweb/admin-ui replaces it
composer why netgen/layouts-core                         # expect: se7enxweb/layouts-core
composer show | grep -E "^(ibexa|ezsystems|netgen)/"     # upstream packages actually installed
```

If an upstream package whose fork you have is listed as installed, something requires a version the fork does not
cover, or the lock file is older than the fork. Update on a copy of the project and read the plan Composer prints.

## The rule: never both

A project must never have a fork and its upstream installed together. Both define the same classes in the same
namespaces, and the autoloader would load one of them unpredictably. Symptoms are classes that "lose" methods, admin
builds that read files from `vendor/ibexa/...` instead of `vendor/se7enxweb/...`, and errors that appear only on some
requests ([The se7enxweb forks, never install both](../../bc/6.0/platform-package-forks-and-command-renames.md#step-1-packages-install-from-the-se7enxweb-vendor)).

## Namespaces stay the same

The forks keep the upstream PHP namespaces (`Ibexa\...`), service ids and Twig namespaces. Code written for the
upstream API works unchanged; only the Composer package names and some user facing strings differ. That is why
these pages name `ibexa/*` packages and `Ibexa\` classes: they are real identifiers, not the product's name.

## Related pages

- [Reading the metapackage](reading-the-metapackage.md)
- [Contributing to the forks](contributing-to-the-forks.md)
- [Platform package forks, command renames and version rules](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
