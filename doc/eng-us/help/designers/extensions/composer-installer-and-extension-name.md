---
title: "The Composer installer and the extension's folder name"
audience: designer, developer
category: theme-extensions
order: 310
summary: How the legacy extension installer decides the folder a theme lands in - extra.ezpublish-legacy-extension-name, else the package name - why installer-name is not read, and how to check the result before publishing.
keywords: composer installer, ezpublish-legacy-extension-name, installer-name, extension folder name, LegacyExtensionInstaller, exponential-legacy-installer, ezpublish-legacy-installer, install path
type: help
---
# The Composer installer and the extension's folder name

This page is for designers publishing a theme as a Composer package. The folder name matters: settings such as
`DesignExtensions[]=mytheme` and `ActiveExtensions[]=mytheme` name it, and a theme installed into a folder with another
name is not found.

## How the folder is chosen

The installer plugin (`se7enxweb/exponential-legacy-installer`, class `LegacyExtensionInstaller`) handles packages of
type `ezpublish-legacy-extension`. Its rule, from its source:

1. If `composer.json` has `extra.ezpublish-legacy-extension-name`, that is the folder name.
2. Otherwise the part of the package name after the vendor is the folder name.

The folder is `<legacy root>/extension/<folder name>`.

| `composer.json` | Folder |
|---|---|
| `"name": "acme/mytheme"`, no extra | `extension/mytheme` |
| `"name": "acme/my-theme"`, no extra | `extension/my-theme` (a dash: not a valid extension name for the wizards) |
| `"name": "acme/my-theme"`, `extra.ezpublish-legacy-extension-name: my_theme` | `extension/my_theme` |

## A trap in generated files

The [design extension wizard](design-extension-wizard.md) writes `"name": "<vendor>/<name with dashes>"`. From
Exponential 6.0.15 on it also writes `extra.ezpublish-legacy-extension-name`, so the folder is the extension name.
Older wizards wrote only `"extra": { "installer-name": "<name>" }`, and this installer does **not** read
`installer-name`: for a theme named `my_theme` the package installs into `extension/my-theme`. In such a file, add the
key the installer reads:

```json
"extra": {
    "ezpublish-legacy-extension-name": "my_theme"
}
```

You may keep `installer-name` for other installers; it does no harm.

## Two installer packages

Published themes require `se7enxweb/ezpublish-legacy-installer`; the Exponential root requires
`se7enxweb/exponential-legacy-installer`. Both are on Packagist and the root allows both as Composer plugins
(`config.allow-plugins`). Requiring one of them in your theme is enough for the type to be handled.

## Step by step: check it

1. Read your `extra` block: `grep -n -A3 '"extra"' composer.json`.
2. Install the theme into a test installation ([Testing a Composer install](testing-a-composer-install-locally.md)).
3. Check the folder: `ls -d extension/<expected name>` prints the folder.
4. With `composer -v`, the installer also prints the directory it uses.

## Related pages

- [composer.json for a theme](composer-json-for-a-theme.md)
- [Naming your theme extension](naming-your-theme-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
