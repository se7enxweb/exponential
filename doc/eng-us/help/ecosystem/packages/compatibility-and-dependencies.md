---
title: Compatibility and dependencies in the ecosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 27
summary: How to check that an Exponential package fits your installation - PHP versions, the kernel version, databases, engines, extension loading order and Composer conflicts - and how to read a package's requirements before you install it.
keywords: compatibility, dependencies, composer.json, require, PHP version, kernel version, database, Velocity, extension loading order, conflicts, composer why, composer show
type: help
---
# Compatibility and dependencies in the ecosystem

## Contents

1. [Read the requirements first](#read-the-requirements-first)
2. [The dimensions to check](#the-dimensions-to-check)
3. [Useful commands](#useful-commands)
4. [When something does not fit](#when-something-does-not-fit)

## Read the requirements first

A Composer package states what it needs in its `composer.json` (`require`, `conflict`, `suggest`). Read it before you
install, and do not trust a description that is not backed by it. This applies to whole distributions too: the DXP,
for example, ships no layouts editor, workflow, scheduler or legacy kernel, which its requirements show
([Exponential Platform DXP](../products/platform-dxp.md)).

## The dimensions to check

| Dimension | Where to check | Page |
|---|---|---|
| PHP version | `require.php` of the package and of the kernel (PHP 7.4 to 8.5 for 6.0.15) | [PHP versions](../php-stack/php-versions.md) |
| Kernel version | `require."se7enxweb/exponential"` or the README | [The Exponential 6 kernel](../products/exponential-6-kernel.md) |
| Database | Whether the extension uses SQL that your database lacks | [Databases](../php-stack/databases.md) |
| Engine | Persistent workers keep classes in memory; restart after changes | [Exponential Velocity](../products/exponential-velocity.md) |
| Other extensions | Template overrides and settings can collide; the loading order decides | [Extension loading order](../../../features/6.0/extension-loading-order.md) |
| PHP extensions | `ext-*` requirements | [PHP versions and extensions](../../hosting/basics/php-versions-and-extensions.md) |

## Useful commands

```bash
composer show se7enxweb/exponential      # the installed kernel version
composer why <vendor/package>            # which package requires it
composer why-not <vendor/package> <ver>  # what blocks a version
./console exp:info --plain --no-logo     # every extension with its version, the engine, the caches
```

`exp:info` is described in [The system card](../../../features/6.0/system-card.md).

## When something does not fit

- Look for a newer release of the package.
- Check whether a fork is maintained ([Keeping a fork in sync](../../extension-development/part-5-extend-fork-contribute/keeping-a-fork-in-sync.md)).
- Port it and send the change back ([Contributing back with pull requests](../../extension-development/part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)).
- Ask in the forums whether someone already did.

## Related pages

- [Packages](README.md)
- [Finding an extension](finding-an-extension.md)
- [Part 4: Upgrading](../../extension-development/part-4-upgrading/README.md)
- [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
