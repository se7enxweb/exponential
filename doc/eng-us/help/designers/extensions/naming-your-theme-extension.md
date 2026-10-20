---
title: "Naming your theme extension"
audience: designer
category: theme-extensions
order: 30
summary: The rules for the folder name of a theme extension, its design name, its class prefix and its Composer package name, with the conventions real themes follow and the names to avoid.
keywords: naming, extension name, folder name, design name, package name, composer name, vendor, prefix, exp, sevenx_themes, cjw_themes
type: help
---
# Naming your theme extension

This page is for designers starting a new theme. A name is cheap to choose and expensive to change: it is the
folder name, the design name in settings, the prefix of classes and the Composer package name. Change it after a
release and every installation that uses the theme has to follow.

## The names a theme has

| Name | Example | Where it appears |
|---|---|---|
| Extension (folder) name | `mytheme_jumper` | `extension/<name>/`, `ActiveExtensions[]`, `DesignExtensions[]`, the class `<name>Info` in `ezinfo.php` |
| Design name | `jumper` | `design/<design>/`, `site.ini [DesignSettings] SiteDesign` |
| Human name | `My Theme Jumper` | `ezinfo.php` `Name`, `extension.xml` `<name>`, the About page |
| Composer package | `yourvendor/mytheme_jumper` | `composer.json` `name`, Packagist |
| Repository | `github.com/yourvendor/mytheme_jumper` | `Info_url`, `homepage` |

The extension name and the design name may differ. `cjw_themes_jumper` carries the design `starter`;
`sevenx_themes_simple` carries `simple`; `sevenx_themes_media` carries `media`.

## The rules

1. **Extension name**: lower case letters, digits and underscores, starting with a letter. The RAD wizards enforce
   exactly this, with three to forty-one characters ([The design extension wizard](design-extension-wizard.md)).
2. **Design name**: the same characters. It must not collide with a design that already exists in the installation
   (`standard`, `admin`, `admin3`, `admin4`, `admin4l`, `base`, `ezwebin` and the designs of other themes you plan to
   combine with).
3. **Composer package name**: lower case `vendor/name`. Dashes and underscores are both allowed by Composer; the
   folder name it installs into is decided as described in
   [The Composer installer and the extension's folder name](composer-installer-and-extension-name.md).

## Good practice

- **Prefix with your organization** so two themes never collide: `acme_themes_harbor`, not `harbor`.
- **Keep the product name out of your theme's name.** A theme is not "Exponential Harbor"; it is "Harbor, a theme for
  Exponential". See [Names and trademarks](../sales/names-and-trademarks.md) and
  [Brand and naming rules](../../business/brand-and-naming-rules.md).
- **`sevenx` and `exp` are 7x's prefixes.** 7x's own themes are `sevenx_themes_*` because `sevenx` is 7x's official
  public PHP code prefix (a name cannot start with a digit, so "7x" cannot be one); 7x uses `exp` with intent for work
  it assigns to the Exponential project and the Exponential Foundation. Your theme uses your own prefix, and never
  renames existing kernel identifiers such as `eZINI` or `eZTemplate`. See [Naming prefixes: sevenx and exp](../../../standards/naming-prefixes-sevenx-and-exp.md).
- **Search before you choose**: Packagist, GitHub and
  [projects.exponential.earth](https://projects.exponential.earth/) (browse by type).

## Step by step

1. Write down the five names of the table above.
2. Check the extension name: `ls extension/ | grep -x <name>` prints nothing.
3. Check the package name: open `https://packagist.org/packages/<vendor>/<name>`; a "not found" page means it is free.
4. Check the design name: `ls design/ extension/*/design/ | grep -x <design>` prints nothing.
5. Use the names consistently in every file from the first commit on.

## Related pages

- [The folder structure of a theme extension](extension-folder-structure.md)
- [composer.json for a theme](composer-json-for-a-theme.md)
- [Rebranding a fork, fairly](../lifecycle/rebranding-a-fork.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
