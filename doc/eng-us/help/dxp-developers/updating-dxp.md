---
title: Updating a DXP project
audience: dxp-developer
category: dxp-development
order: 39
summary: Keeping a DXP project current - updating Composer and Node packages, what to run after each update, reading the fork release notes, pinning versions when the skeleton allows development branches, and recipe updates.
keywords: update, composer update, yarn upgrade, release notes, se7enxweb forks, minimum-stability, pinning, recipes:update, migrations, reindex, security updates
type: help
---
# Updating a DXP project

This page is for developers who keep a DXP project up to date. The guide's section 16 gives the commands; this page
adds the order of work and the checks that keep an update from surprising you.

## Contents

1. [Before an update](#before-an-update)
2. [Updating Composer packages](#updating-composer-packages)
3. [Updating Node packages](#updating-node-packages)
4. [Recipes](#recipes)
5. [Development branches and pinning](#development-branches-and-pinning)
6. [What to read](#what-to-read)

## Before an update

- Work on a branch and a copy of the database.
- Note the current versions: `composer show "se7enxweb/*" "ibexa/*" > var/before-update.txt`.

## Updating Composer packages

```bash
composer update                                              # everything within constraints
composer update se7enxweb/exponential-platform-dxp-core     # a single package

php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console cache:clear
php bin/console exponential:reindex                          # if the content model schema may have changed
```

Then rebuild the admin assets if `se7enxweb/admin-ui` changed ([The admin UI](the-admin-ui.md#what-needs-a-rebuild)),
run the tests, and commit `composer.lock`:

```bash
git add composer.lock && git commit -m "Updated: Composer dependencies"
```

## Updating Node packages

```bash
yarn upgrade
yarn dev
git add yarn.lock && git commit -m "Updated: Node dependencies"
```

## Recipes

A package update can come with a newer Flex recipe. `composer recipes` lists the ones with updates;
`composer recipes:update <package>` applies one and shows the diff. Review it: it touches your configuration.

## Development branches and pinning

The skeleton sets `minimum-stability: dev` and `prefer-stable: false`, and several packages are required at
`5.0.x-dev` branches ([Reading the metapackage](reading-the-metapackage.md#the-skeleton-section-by-section)). A
`composer update` can therefore bring new commits, not only new tags. For production:

- commit `composer.lock` and deploy only with `composer install`;
- update on purpose, on a branch, and test before merging;
- where a se7enxweb fork has tags, you may require a tag range in your own `composer.json`.

## What to read

| Source | What it tells you |
|---|---|
| [exponential-platform-dxp release notes](../../../changelogs/extensions/exponential-platform-dxp.md) | Changes of the metapackage |
| [Admin UI releases](../../../changelogs/extensions/admin-ui.md) | Changes of the administration fork |
| [Platform package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md) | Steps when moving from upstream names |
| [Platform package map](../../specifications/6.0/platform-package-map.md) | Newest tags of every fork |

## Related pages

- [Deploying](deploying-dxp.md)
- [Doctrine migrations](doctrine-migrations.md)
- [Troubleshooting](troubleshooting-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
