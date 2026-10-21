---
title: Building assets with Yarn and Encore
audience: dxp-developer
category: dxp-development
order: 24
summary: The two asset builds of a DXP project - the site's own CSS and JavaScript with Webpack Encore, and the administration interface - with the Yarn scripts, what to rebuild after which change, and how to deploy without Node.js on the server.
keywords: assets, Webpack Encore, Yarn, Node.js 20, yarn dev, yarn build, yarn watch, yarn ibexa:build, assets:install, encore_entry_link_tags, public/assets, corepack
type: help
---
# Building assets with Yarn and Encore

This page is for developers who build the stylesheets and scripts of a DXP project. There are two builds: the
site's own assets and the administration interface's. Both use Webpack Encore and Yarn under Node.js 20 LTS
(skeleton guide, sections 8 and 9).

## Contents

1. [Before any build](#before-any-build)
2. [The site build](#the-site-build)
3. [The administration build](#the-administration-build)
4. [What to rebuild after which change](#what-to-rebuild-after-which-change)
5. [Including the built files in Twig](#including-the-built-files-in-twig)
6. [Building for production](#building-for-production)

## Before any build

```bash
source ~/.nvm/nvm.sh && nvm use 20
corepack enable          # Yarn 1.22 as declared in package.json
yarn install             # first time, and after package.json changes
```

## The site build

Sources live in `assets/js/`, `assets/scss/` and `assets/styles/`; the output goes to `public/assets/`.

```bash
yarn dev       # development, with source maps
yarn build     # production, minified
yarn watch     # rebuild on change
```

## The administration build

```bash
php bin/console assets:install --symlink --relative public   # first: fills public/bundles/ and var/encore/
yarn ibexa:build     # production
yarn ibexa:dev       # development
yarn ibexa:watch     # rebuild on change
```

`assets:install` writes `var/encore/ibexa.config.js`, `ibexa.config.setup.js` and `ibexa.config.manager.js`, which
tell Webpack where each bundle's entry points are. Without them, `yarn ibexa:build` fails with "Module not found".

## What to rebuild after which change

| Changed | Run |
|---|---|
| `assets/js/**`, `assets/scss/**`, `assets/styles/**` | `yarn dev` (or `yarn watch`) |
| `package.json` | `yarn install && yarn dev` |
| `webpack.config.js` | `yarn dev`, and `yarn ibexa:build` |
| A new `se7enxweb/admin-ui` after `composer update` | `yarn ibexa:build` |
| A bundle's `Resources/public/` JavaScript or SCSS | `yarn ibexa:build` |
| Admin translation strings | `php bin/console bazinga:js-translation:dump public/assets --merge-domains` |

## Including the built files in Twig

The Webpack Encore bundle provides Twig functions for the entries of your `webpack.config.js`:

```twig
{{ encore_entry_link_tags('app') }}
{{ encore_entry_script_tags('app') }}
```

Use these in the page layout instead of writing `<link>` and `<script>` paths by hand; Encore adds the hashed file
names, so browsers get new files after a build without query strings on the URLs.

## Building for production

The administration assets are not built on `composer install`, so a production server needs no Node.js when you
build on a build machine or in CI and deploy `public/assets/` and `public/bundles/` with the code. If you build on
the server, use the same Node.js and Yarn versions as in development ([Deploying](deploying-dxp.md)).

## Related pages

- [The admin UI](the-admin-ui.md)
- [The standard design and your theme](the-standard-design-and-your-theme.md)
- [Versions you need](php-symfony-and-node-versions.md#nodejs-and-yarn)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
