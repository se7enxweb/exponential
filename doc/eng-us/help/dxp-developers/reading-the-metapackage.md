---
title: Reading the metapackage
audience: dxp-developer
category: dxp-development
order: 5
summary: A walk through the composer.json of se7enxweb/exponential-platform-dxp and of the skeleton - what each requirement is for, the conflicts, the license field, the Flex recipe endpoints and minimum stability - so you know what a DXP project really installs.
keywords: metapackage, composer.json, se7enxweb/exponential-platform-dxp, skeleton, require, conflict, Symfony Flex, recipes, minimum-stability, branch-alias, packages
type: help
---
# Reading the metapackage

This page is for developers who want to know exactly what a DXP project installs and why. It walks through the two
`composer.json` files that define a project: the metapackage `se7enxweb/exponential-platform-dxp` and the skeleton
`se7enxweb/exponential-platform-dxp-skeleton`.

## Contents

1. [Two files, two jobs](#two-files-two-jobs)
2. [The metapackage, section by section](#the-metapackage-section-by-section)
3. [The skeleton, section by section](#the-skeleton-section-by-section)
4. [Questions the files answer](#questions-the-files-answer)
5. [Reading them on your own project](#reading-them-on-your-own-project)

## Two files, two jobs

| File | Package type | Job |
|---|---|---|
| `se7enxweb/exponential-platform-dxp` | `metapackage` | Lists every package of the DXP. It has no code of its own. |
| `se7enxweb/exponential-platform-dxp-skeleton` | `project` | The starting point of your project: requires the metapackage, sets up Flex, autoloading and scripts. After `create-project` it is your project's `composer.json`. |

## The metapackage, section by section

**`require`.** PHP `>=8.3`, `ext-ctype` and `ext-iconv`, then four groups:

- the content packages (se7enxweb forks and upstream `ibexa/*` packages), grouped on
  [What the DXP ships](what-the-dxp-ships-and-what-it-does-not.md#what-it-ships);
- Symfony 7.4 components and bundles (`symfony/framework-bundle`, `symfony/security-bundle`, `symfony/form`,
  `symfony/mailer`, `symfony/twig-bundle`, `symfony/webpack-encore-bundle` and others);
- Doctrine (`doctrine/doctrine-bundle`, `doctrine/doctrine-migrations-bundle`);
- tooling bundles (`lexik/jwt-authentication-bundle`, `overblog/graphiql-bundle`, `knplabs/knp-menu-bundle`,
  `friendsofsymfony/jsrouting-bundle`, `monolog/monolog`, `twig/extra-bundle`).

Several content packages are required at a development branch (for example `~5.0.x-dev`); the se7enxweb forks are
required at tagged versions where one exists (`se7enxweb/admin-ui` `^5.0.5.0`, `se7enxweb/exponential-platform-dxp-core`
`^5.0.1.0`).

**`conflict`.** Known bad versions that Composer must never install, for example `doctrine/doctrine-bundle` `2.6.3`,
`lexik/jwt-authentication-bundle` `2.12.0 || 2.18.0` and `symfony/webpack-encore-bundle` `1.14.0`. Do not remove them
in your project.

**`license`.** `(GPL-2.0-or-later or proprietary)` ([Licensing](licensing.md)).

**`extra.branch-alias`.** `dev-master` is aliased to `1.3.x-dev`.

## The skeleton, section by section

**`require`.** PHP `>=8.3`, the extensions `ctype`, `iconv`, `gd`, `curl`, `json`, `xsl`, `xml`, `intl`, `mbstring`,
then `se7enxweb/exponential-platform-dxp` at `dev-master`, `symfony/flex`, `symfony/runtime` and `nyholm/psr7`.
**`require-dev`** adds the Symfony debug bundle, stopwatch and web profiler.

**`config.allow-plugins`.** Only `ibexa/post-install`, `symfony/flex` and `symfony/runtime` may run as Composer
plugins; every other plugin is refused (`"*": false`). Keep this list short in your project.

**`autoload`.** Your code lives under the `App\` namespace in `src/`, tests under `App\Tests\` in `tests/`.

**`scripts`.** After install and update, `cache:clear` and `assets:install --symlink --relative` run automatically.

**`extra.symfony.endpoint`.** Flex reads recipes from the `ibexa/recipes` index, from
`se7enxweb/sevenx-recipes` and from the Flex defaults. The recipes write your configuration under `config/`
([sevenx-recipes](../../features/6.0/extensions/sevenx-recipes.md)).

**`minimum-stability` and `prefer-stable`.** The skeleton sets `minimum-stability` to `dev` and `prefer-stable` to
`false`, because several packages are required at development branches. Before a production release, decide which
packages you pin to tags in your own `composer.json` and commit `composer.lock`
([Updating](updating-dxp.md)).

**`conflict`.** `symfony/symfony` is in conflict: install Symfony as separate components, never the monolith.

## Questions the files answer

| Question | Where to look |
|---|---|
| Is GraphQL included? | `ibexa/graphql` in the metapackage: yes |
| Is a layouts editor included? | Only `se7enxweb/layouts-core`: no editor |
| Which PHP version? | `"php": ">=8.3"` in both files |
| Which Symfony version? | The `symfony/*` requirements: `^7.4` |
| Why does Composer refuse a version? | The `conflict` section |

## Reading them on your own project

```bash
composer show se7enxweb/exponential-platform-dxp      # version installed and its requirements
composer depends ibexa/rest                           # who requires a package
composer why-not php 8.2                              # why an older PHP is refused
composer outdated "se7enxweb/*"                       # newer releases of the forks
```

## Related pages

- [The se7enxweb forks and `replace`](the-se7enxweb-forks-and-replace.md)
- [Platform package map](../../specifications/6.0/platform-package-map.md)
- [exponential-platform-dxp release notes](../../../changelogs/extensions/exponential-platform-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
