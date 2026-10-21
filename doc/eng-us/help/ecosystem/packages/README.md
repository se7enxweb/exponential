---
title: Packages of the Exponential ecosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 20
summary: The landing page of the packages part of the ecosystem section - the extensions bundled with the kernel, Composer and Packagist, how packages are versioned, site packages and the package server, themes and designs, finding an extension, and compatibility.
keywords: packages, extensions, Composer, Packagist, se7enxweb, versions, site packages, package server, themes, designs, compatibility, dependencies
type: help
---
# Packages of the Exponential ecosystem

Almost everything in the Exponential world is a package: the kernel, each extension, each theme, each server, each
fork of an upstream library. "Every package is a GitHub repository under se7enxweb and a Composer package"
([Releases and version numbers](../../who-is-the-exponential-foundation.md#releases-and-version-numbers)).

## Contents

1. [The pages of this part](#the-pages-of-this-part)
2. [Three kinds of package](#three-kinds-of-package)

## The pages of this part

| Page | What it answers |
|---|---|
| [The bundled extensions](the-bundled-extensions.md) | What arrives with a new installation and what is switched off |
| [Composer and Packagist](composer-and-packagist.md) | How packages are installed and published |
| [How packages are versioned](how-packages-are-versioned.md) | Version numbers, permanent tags, the `~` constraint |
| [Site packages and the package server](site-packages-and-the-package-server.md) | Ready-made sites for the setup wizard |
| [Themes and designs](themes-and-designs.md) | Site themes and admin designs as packages |
| [Finding an extension](finding-an-extension.md) | Where to look and how to judge what you find |
| [Compatibility and dependencies](compatibility-and-dependencies.md) | Checking that a package fits your installation |

## Three kinds of package

| Kind | Installed with | Example |
|---|---|---|
| Composer package (kernel, extension, theme, server) | `composer require` or a Git clone | `se7enxweb/exponential`, `se7enxweb/eztags` |
| Site package (`.ezpkg`) | The setup wizard, the kickstarter or the console | The 7x Simple site package |
| Fork of an upstream package | Composer, replacing the upstream name | The Symfony, Twig and Doctrine forks of the platform line |

## Related pages

- [Ecosystem: start here](../ecosystem-start-here.md)
- [The default extension distribution](../../../features/6.0/default-extension-distribution.md)
- [Extension development](../../extension-development/README.md)
- [Site packages](../../site-packages/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
