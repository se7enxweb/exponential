# The Exponential Platform administration interface

This page is for developers and administrators of Exponential Platform v5 (the Symfony based platform) and its older
2.x and 3.x lines. It describes the administration interface editors use at `/adminui/`: content tree, content
editing, search, content types, users and roles, the media library. It is **not** about the legacy administration
design `admin3` of Exponential 6 (see [the responsive admin](admin3-responsive-admin.md)).

| | |
|---|---|
| Repositories | `se7enxweb/admin-ui` (Platform v5; repository `admin-ui-7x`, also seen as `admin-ui-ibexa`), `se7enxweb/admin-ui-assets`, `se7enxweb/fieldtype-richtext`; for the 3.x line `se7enxweb/ezplatform-admin-ui`, `se7enxweb/ezplatform-admin-ui-assets`, `se7enxweb/ezplatform-richtext`, `se7enxweb/ezplatform-design-engine`, `se7enxweb/ezplatform-standard-design`, `se7enxweb/ezplatform-alloyeditor-element-width` |
| History | [admin-ui-7x](../../history/ecosystem/admin-ui-7x.md) · [admin-ui-ibexa](../../history/ecosystem/admin-ui-ibexa.md) · [admin-ui-assets](../../history/ecosystem/admin-ui-assets.md) · [fieldtype-richtext-ibexa](../../history/ecosystem/fieldtype-richtext-ibexa.md) · [ezplatform-admin-ui](../../history/ecosystem/ezplatform-admin-ui.md) · [ezplatform-richtext](../../history/ecosystem/ezplatform-richtext.md) |

## What a user gets

- An administration interface that shows Exponential Platform DXP, not another product name, in the browser tab, on
  the login page and on the welcome page ("Get to know Exponential Platform DXP"), and sends password reset mails with
  the same name.
- A build that works offline from the se7enxweb packages: `yarn ibexa:build` finds the shared assets in
  `vendor/se7enxweb/admin-ui-assets`.
- Rich text editing that finds its XSL stylesheets and webpack alias inside the fork (`se7enxweb/fieldtype-richtext`,
  release `5.0.0` on 2026-04-19).

## Install and build

The admin UI is a dependency of the metapackage `se7enxweb/exponential-platform-dxp`, so you do not require it
yourself. After `composer install`, build the assets:

```bash
php bin/console assets:install --symlink --relative public
source ~/.nvm/nvm.sh && nvm use 22      # the Nexus v5 starter; the DXP skeleton guide says Node 20
yarn install
yarn ibexa:build
```

Open `https://<your host>/adminui/` and sign in with the administrator account of your installation
([DXP skeleton](platform-dxp-skeleton.md), [Nexus starter](platform-nexus-starter.md)).

**If the build cannot find a file under `vendor/ibexa/...`**, the project still has the upstream package installed
next to the fork. Run `composer why ibexa/admin-ui-assets` to find what requires it, and check that the fork's `replace`
list is in effect (see the [package map](../../specifications/6.0/platform-package-map.md)).

## What the fork changes

1. **Branding.** Exponential Platform DXP logo, favicons, page titles, welcome texts and password reset mail.
2. **Package identity.** The fork is installed as `se7enxweb/admin-ui` and declares the upstream names as replaced, so
   nothing in a project can pull the upstream copy a second time.
3. **Build paths.** The Webpack Encore configuration points into `vendor/se7enxweb/...` instead of `vendor/ibexa/...`,
   so the interface builds when only the se7enxweb packages are installed.

The remaining history (more than a thousand commits) is upstream work that the fork carries: the move to Symfony 6
(`b9575cf69`, 2025-02-04), PHP 8 type hints and strict types throughout (IBX-9727, 2025), icon mapping (`1026f2c42`,
2025-06-05), removal of Sass deprecations (`c0adbf5b1`, 2025-05-22), removal of the `class_alias` BC layer
(`26f130a90`, 2024-06-05) and a standalone universal discovery widget package (`15704ef6f`, 2023-12-14). The month
tables in the [repository page](../../history/ecosystem/admin-ui-7x.md) list the busiest changes of every month.

## Releases made by the se7enxweb team

| Release | Date | What it contains |
|---|---|---|
| `v5.0.5.0` | 2026-03-21 | Package renamed to `se7enxweb/admin-ui`, `replace` statement for the upstream packages, license expression changed to GPL 2.0 or later, Exponential Platform DXP logo (SVG) and favicons, templates for login, account and layout use the logo and name |
| `v5.0.5.1` | 2026-03-22 | `src/bundle/Resources/encore/ibexa.config.setup.js` resolves internal paths in the forked vendor storage |
| `v5.0.5.2` | 2026-03-25 | Missing label translation strings for the sub-items view controls |
| `v5.0.5.3` | 2026-04-16 | Encore configs (`ibexa.css.config.js`, `ibexa.js.config.js`, `ibexa.webpack.libs.config.js`) use `vendor/se7enxweb/admin-ui-assets`; English translation strings say Exponential Platform DXP |
| `v5.0.4` (repository `admin-ui-7x`) | 2026-04-17 | se7enxweb favicons (16x16, 32x32, ico) |

Changelog: [admin UI releases](../../changelogs/extensions/admin-ui.md).

## The 3.x line

`ezplatform-admin-ui` (Exponential Platform Admin v2) received the same treatment in September 2025 and April 2026:

- package names and licenses in `composer.json`, webpack asset paths with se7enxweb package names;
- branding replacement of "Ibexa DXP" in the HTML title, and a logo colour fix;
- encore configs reference the installed `ezsystems/ezplatform-admin-ui-assets` dependency;
- SCSS migrated from `@import` to `@use` / `@forward`, with deprecated colour and math functions replaced (so it
  builds with current Sass);
- PHP constraint extended to `^8.5`, and a `replace` shim for the ezsystems counterpart.

## Limits

- The interface needs a Node.js version that matches the project (20 or 22), Yarn 1.22, and enough memory for the
  Webpack build.
- Translation strings other than English were not changed; they may still show the upstream product name.

## Related pages

- Platform features: [DXP skeleton](platform-dxp-skeleton.md), [Layouts on the platform](platform-layouts-core-fork.md), [Nexus starter](platform-nexus-starter.md), [PHP 8.5 framework forks](platform-php85-framework-forks.md), [site bundles](platform-site-bundles.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [legacy bridge](legacy-bridge.md), [AdminNeo database manager](adminneo-database-manager.md)
- Specifications: [platform console command names](../../specifications/6.0/platform-console-commands.md), [platform package map](../../specifications/6.0/platform-package-map.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md), [legacy bridge bundle](../../specifications/6.0/legacy-bridge-bundle.md)
- Upgrade notes: [package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md)
