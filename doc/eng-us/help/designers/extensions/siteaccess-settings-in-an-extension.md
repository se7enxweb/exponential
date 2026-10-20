---
title: "Siteaccess settings inside a theme"
audience: designer, developer
category: theme-extensions
order: 150
summary: Ship settings that apply only while one siteaccess runs, in extension/<theme>/settings/siteaccess/<siteaccess>/, and settings for all of a theme's siteaccesses in settings/override/ - with the example of cjw_themes_jumper.
keywords: siteaccess, siteaccess settings, settings/siteaccess, per-site settings, multi-site, ext-siteaccess-override, cjw_starter
type: help
---
# Siteaccess settings inside a theme

This page is for designers whose theme comes with its own site (siteaccess), or who host several sites from one
installation, each in its own extension.

## Two places

| Folder in the theme | Applies |
|---|---|
| `settings/siteaccess/<siteaccess>/` | Only while that siteaccess runs |
| `settings/override/` | While any siteaccess that has a `settings/siteaccess/<name>/` folder in this extension runs (since January 2026) |

The second place is described in [Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md).
Its placement name, as the debug output shows it, is `ext-siteaccess-override:<extension>`.

## A real example

`cjw_themes_jumper` ships `settings/siteaccess/cjw_starter/` with `collect.ini`, `explayouts.ini`, `module.ini`,
`override.ini` and `site.ini` append files. Its information collection settings, for example, are read only in the
starter siteaccess, so the forms of the theme do not change any other site of the installation.

## Step by step

1. Create the folder: `mkdir -p extension/mytheme/settings/siteaccess/mysite`.
2. Add `site.ini.append.php` with what only this site needs:

   ```php
   <?php /* #?ini charset="utf-8"?

   [DesignSettings]
   SiteDesign=mytheme

   */ ?>
   ```

3. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
4. Check the value for that siteaccess:

   ```bash
   ./console exp:ini where site.ini/DesignSettings/SiteDesign -s mysite --allow-root-user
   ```

   Expected: your file among the files, and `mytheme` in effect.
5. Open the siteaccess in a browser and check that the design is used.

## Things to know

- The siteaccess itself (its name in `site.ini [SiteAccessSettings]`, its host or URL matching) is set up by the
  installation, not by the theme. Your README explains the lines to add.
- Do not ship a siteaccess folder named like a common siteaccess (`site`, `admin`) unless the theme really is meant
  for it; it would change sites the user did not expect.

## Related pages

- [Designs in multisite](../theme-design/designs-in-multisite.md)
- [Settings files in an extension](settings-files-in-an-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
