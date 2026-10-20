---
title: "composer.json for a theme"
audience: designer, developer
category: theme-extensions
order: 300
summary: Write the composer.json that makes a theme installable with composer require - name, description, type ezpublish-legacy-extension, license, authors, support, require, suggest and the extension name - with a complete example from a published theme.
keywords: composer.json, composer, Packagist, ezpublish-legacy-extension, type, extra, ezpublish-legacy-extension-name, license, authors, support, composer validate
type: help
---
# composer.json for a theme

This page is for designers who want users to install their theme with one command:
`composer require yourvendor/mytheme`.

## A complete example

`cjw_themes_jumper` publishes this (shortened only in `keywords`):

```json
{
    "name": "se7enxweb/cjw_themes_jumper",
    "description": "CJW Themes Jumper: the design starter for Exponential, served by the starter siteaccesses (port of the Nexus v2 starter look)",
    "type": "ezpublish-legacy-extension",
    "keywords": [ "Exponential", "Exponential 6", "legacy extension", "design", "theme" ],
    "homepage": "https://github.com/se7enxweb/cjw_themes_jumper",
    "license": "GPL-2.0-or-later",
    "authors": [
        { "name": "7x", "homepage": "https://se7enx.com", "email": "info@se7enx.com", "role": "Author" }
    ],
    "support": {
        "issues": "https://github.com/se7enxweb/cjw_themes_jumper/issues",
        "source": "https://github.com/se7enxweb/cjw_themes_jumper"
    },
    "require": {
        "php": ">=8.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "cjw_themes_jumper"
    }
}
```

## The fields

| Field | Why it matters |
|---|---|
| `name` | `vendor/package`, lower case. It is the name users require and Packagist lists |
| `description` | Shown on Packagist and in search results |
| `type: ezpublish-legacy-extension` | Makes the legacy installer place the package below `extension/` |
| `license` | An SPDX identifier, the same license as the `LICENSE` file |
| `authors`, `homepage`, `support` | Who made it and where to ask |
| `require` | PHP versions, the installer plugin, extensions the theme cannot work without |
| `suggest` | Extensions it works better with ([require and suggest](composer-require-and-suggest.md)) |
| `extra.ezpublish-legacy-extension-name` | The folder name below `extension/` ([The Composer installer and the folder name](composer-installer-and-extension-name.md)) |
| `version` | Usually left out: Packagist reads the version from the tag. If you keep it, keep it in sync |

## Step by step

1. Create `composer.json` at the theme's root, using the example with your values.
2. Validate it:

   ```bash
   composer validate composer.json
   ```

   Expected: `./composer.json is valid`. Warnings about a missing `version` or a `*` constraint can be read and
   accepted or fixed.
3. Commit it with the release that introduces it.
4. Test the install before you publish ([Testing a Composer install](testing-a-composer-install-locally.md)).

## Related pages

- [Submitting a theme to Packagist](../distribution/submitting-to-packagist.md)
- [Composer package or .ezpkg](composer-or-ezpkg.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
