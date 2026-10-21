---
title: The project directory layout
audience: dxp-developer
category: dxp-development
order: 14
summary: A map of a DXP project's folders - src, config, templates, assets, public, var, vendor - what goes where, what Flex writes, what you commit and what you never commit.
keywords: directory layout, project structure, src, config, templates, themes, assets, public, var, vendor, config/packages, Flex, gitignore, App namespace
type: help
---
# The project directory layout

This page is for developers who open a DXP project for the first time. It maps the folders from the skeleton
guide's section 2 ("Key Directories") and says what you change, what Flex manages, and what stays out of Git.

## Contents

1. [The map](#the-map)
2. [Where your work goes](#where-your-work-goes)
3. [What Flex writes](#what-flex-writes)
4. [What you commit](#what-you-commit)
5. [Finding where a setting comes from](#finding-where-a-setting-comes-from)

## The map

```text
project-root/
  src/                    your PHP code, namespace App\ (controllers, services, commands)
    Kernel.php            the application kernel
  config/
    packages/             bundle configuration (ibexa.yaml, security.yaml, ...)
    routes/               route definitions
    services.yaml         your service definitions
    jwt/                  the JWT keypair (generated, never committed)
  templates/
    themes/               Twig templates by theme
  assets/
    js/  scss/  styles/   site JavaScript and stylesheet sources
  public/                 the web root
    index.php             the only PHP entry point
    assets/               built site assets (Webpack Encore output)
    bundles/              bundle public files (symlinked by assets:install)
    var/                  stored files and image variations
  translations/           your translation files
  var/
    cache/  log/          Symfony cache and logs
    data_<env>.db         the SQLite database, if you use SQLite
  vendor/                 Composer packages
```

## Where your work goes

| You write | Put it in |
|---|---|
| A controller, a service, an event subscriber, a console command | `src/` (autoloaded as `App\`) |
| Configuration of a bundle | `config/packages/<bundle>.yaml` |
| Your routes | `config/routes/` |
| Twig templates of your theme | `templates/themes/<theme>/` ([Twig and the design engine](twig-templates-and-the-design-engine.md)) |
| Stylesheets and JavaScript | `assets/`, built by Encore ([Building assets](building-assets-with-yarn-and-encore.md)) |
| A database change of your own entities | a Doctrine migration ([Doctrine migrations](doctrine-migrations.md)) |

Never edit files under `vendor/`. To change a package, extend or decorate its services in `src/`, or contribute the
change to the package ([Contributing to the forks](contributing-to-the-forks.md)).

## What Flex writes

When a package with a recipe is installed, Symfony Flex adds its configuration under `config/`, its routes under
`config/routes/`, its environment defaults to `.env`, and records the recipe in `symfony.lock`. The DXP skeleton
reads recipes from `ibexa/recipes`, `se7enxweb/sevenx-recipes` and the Flex defaults
([Reading the metapackage](reading-the-metapackage.md#the-skeleton-section-by-section)). Commit what Flex writes;
it is your project's configuration from then on.

```bash
composer recipes                       # which recipes are installed, and which have updates
composer recipes:update <package>      # apply a newer recipe version (review the diff)
```

## What you commit

| Commit | Never commit |
|---|---|
| `src/`, `config/` (without `config/jwt/*.pem`), `templates/`, `assets/`, `translations/` | `.env.local` and other files with secrets |
| `composer.json`, `composer.lock`, `symfony.lock` | `vendor/`, `node_modules/` |
| `package.json`, `yarn.lock`, `webpack.config.js` | `var/`, `public/var/`, built `public/assets/` (unless you deploy them by Git on purpose) |
| `.env` (defaults only) | the JWT keys, database dumps |

## Finding where a setting comes from

```bash
php bin/console debug:config ibexa                     # the resolved configuration of the content packages
php bin/console exponential:debug:config-resolver <parameter> --siteaccess=site
php bin/console debug:router | grep adminui            # which routes exist
php bin/console debug:container <service id>           # where a service is defined
```

## Related pages

- [Siteaccesses](siteaccesses-site-and-admin.md)
- [Installing with the skeleton](installing-with-the-skeleton.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
