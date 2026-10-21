---
title: Day-to-day commands
audience: dxp-developer
category: dxp-development
order: 37
summary: The commands a DXP developer runs every day, grouped by situation - after pulling code, after a template or configuration change, after a content type change, after a stylesheet change, when something looks stale - on one page.
keywords: day to day, commands, cheat sheet, cache clear, warmup, reindex, yarn, assets, graphql schema, migrations, symfony server, routine
type: help
---
# Day-to-day commands

This page is for developers who want the right command for the moment without reading a guide. Every command is
explained on its own page; follow the links when in doubt.

## Contents

1. [After pulling code](#after-pulling-code)
2. [After a change](#after-a-change)
3. [When something looks stale](#when-something-looks-stale)
4. [Services](#services)
5. [Looking things up](#looking-things-up)

## After pulling code

```bash
composer install
php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console cache:clear
yarn install && yarn dev                 # if package.json or assets changed
```

## After a change

| You changed | Run |
|---|---|
| A Twig template | Nothing in `dev`; `cache:clear` in `prod` |
| YAML configuration or a service | `php bin/console cache:clear` |
| A content type or field type | `php bin/console ibexa:graphql:generate-schema`, `cache:clear`, and a reindex if search uses the fields |
| Image variation settings | `php bin/console liip:imagine:cache:remove`, `cache:clear` |
| Site stylesheets or scripts | `yarn dev` (or keep `yarn watch` running) |
| Admin assets or a new admin-ui version | `yarn ibexa:build` |
| `.env.local` | `cache:clear` |
| A Doctrine entity | `doctrine:migration:diff`, review, `doctrine:migration:migrate` |

## When something looks stale

```bash
php bin/console cache:clear
php bin/console cache:pool:clear cache.tagaware.filesystem      # or your CACHE_POOL
php bin/console fos:httpcache:invalidate:path / --all
php bin/console exponential:reindex                             # search results
php bin/console liip:imagine:cache:remove                       # images
```

Then reload the browser without its cache. If it is still stale, read [Troubleshooting](troubleshooting-dxp.md).

## Services

```bash
symfony server:start -d && symfony server:log          # development server
systemctl reload php8.3-fpm                            # after PHP settings changed (your version)
systemctl reload nginx                                 # or apache2
php bin/console messenger:stop-workers                 # after a deploy, if workers run
```

## Looking things up

```bash
php bin/console list exponential                       # the core's commands
php bin/console debug:router                           # routes
php bin/console debug:twig                             # Twig functions, filters, namespaces
php bin/console debug:config ibexa                     # resolved configuration
php bin/console exponential:debug:config-resolver languages --siteaccess=site
composer why <package>                                 # why a package is installed
```

## Related pages

- [Console commands and renames](console-commands-and-renames.md)
- [Building assets](building-assets-with-yarn-and-encore.md)
- [Troubleshooting](troubleshooting-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
