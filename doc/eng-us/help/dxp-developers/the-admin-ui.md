---
title: The admin UI
audience: dxp-developer
category: dxp-development
order: 16
summary: The administration interface of the DXP at /adminui/ for developers - what editors and administrators do there, what the se7enxweb fork changes, how its assets are built and translated, and what to rebuild after an update.
keywords: admin UI, adminui, administration interface, se7enxweb/admin-ui, admin-ui-assets, yarn ibexa:build, bazinga translations, content tree, content types, roles, branding
type: help
---
# The admin UI

This page is for developers who install, extend or support the administration interface of a DXP project. Editors
and administrators work at `/adminui/`. It is the Platform administration interface, not the Exponential 6
administration designs.

## Contents

1. [What happens there](#what-happens-there)
2. [What the fork changes](#what-the-fork-changes)
3. [Building its assets](#building-its-assets)
4. [Translations](#translations)
5. [What needs a rebuild](#what-needs-a-rebuild)
6. [Supporting editors](#supporting-editors)

## What happens there

From [The Exponential Platform administration interface](../../features/6.0/platform-admin-ui-fork.md): the content
tree, content editing, search, content types, users and roles, and the media library. Administrators also manage
sections, languages and system information there (`se7enxweb/system-info`).

## What the fork changes

`se7enxweb/admin-ui` replaces `ibexa/admin-ui` and `ezsystems/ezplatform-admin-ui`. It changes three things:

1. **Branding**: the Exponential Platform DXP logo, favicons, page titles, welcome texts and the password reset mail.
2. **Package identity**: it is installed under the se7enxweb name and declares the upstream names as replaced.
3. **Build paths**: Webpack Encore reads from `vendor/se7enxweb/...`, so the interface builds with only se7enxweb
   packages installed.

Release history: [admin UI releases](../../../changelogs/extensions/admin-ui.md).

## Building its assets

The admin assets are built apart from the site assets, and not on `composer install`, so production servers need no
Node.js if you deploy built assets.

```bash
php bin/console assets:install --symlink --relative public    # populates public/bundles/ and var/encore/
yarn ibexa:build      # production
yarn ibexa:dev        # development, with source maps
yarn ibexa:watch      # rebuild on change
```

All `ibexa:*` Yarn scripts go through the project's `webpack.config.js` with `--config-name ibexa`, which points the
`@ibexa-admin-ui` alias to `vendor/se7enxweb/admin-ui` (skeleton guide, section 9). If the build cannot find a file
under `vendor/ibexa/...`, an upstream package is installed next to the fork
([The se7enxweb forks and `replace`](the-se7enxweb-forks-and-replace.md#the-rule-never-both)).

## Translations

The interface's JavaScript translations are dumped to `public/assets`:

```bash
php bin/console bazinga:js-translation:dump public/assets --merge-domains
```

Only the English strings were changed to the new product name; other languages may still show the upstream name
([Platform administration interface, Limits](../../features/6.0/platform-admin-ui-fork.md#limits)).

## What needs a rebuild

| Change | Rebuild |
|---|---|
| `composer update` brought a new `se7enxweb/admin-ui` | `yarn ibexa:build` |
| JavaScript or SCSS under a bundle's `Resources/public/` | `yarn ibexa:build` |
| `webpack.config.js` or `ibexa.webpack.config.manager.js` | `yarn ibexa:build` |
| The rich text editor configuration | `yarn ibexa:build` |
| Translation strings | the `bazinga:js-translation:dump` command |

## Supporting editors

- Give editors their own accounts and roles; never share the administrator account.
- The installer's administrator password is public: change it at once
  ([Installing with the skeleton](installing-with-the-skeleton.md#after-the-install)).
- Editing tasks in the Platform interface differ from Exponential 6; the Exponential 6 editor help in this Help
  center does not describe `/adminui/`.

## Related pages

- [The Exponential Platform administration interface](../../features/6.0/platform-admin-ui-fork.md)
- [Building assets with Yarn and Encore](building-assets-with-yarn-and-encore.md)
- [Siteaccesses](siteaccesses-site-and-admin.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
