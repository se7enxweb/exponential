# Per-site settings inside extensions (multi-site hosting)

This page is for administrators and developers who host several sites from one Exponential installation, each site in
its own extension. Since January 2026 an extension that holds a site can carry **override settings that are active
only while one of its siteaccesses runs**, and clearing the INI cache clears it for every project on the installation.

Before, hosting ten sites from one installation meant one big `settings/override` directory for all of them, or ten
siteaccess directories under `settings/`. A site that lives in its own extension (`extension/site_projectname`) could
ship `settings/siteaccess/<name>/`, but had no place for settings that apply to all of its siteaccesses, or to a
language group of them.

## Set it up

1. Make sure the extension has a `settings/siteaccess/<siteaccess>/` directory for its siteaccess. The new directories
   are looked up only when it does, so unrelated extensions cost nothing.
2. Put settings for every siteaccess of the extension in `settings/override/`:

   ```ini
   # extension/site_example/settings/override/site.ini.append.php
   <?php /*
   [SiteSettings]
   SiteName=Example, all languages

   [DesignSettings]
   SiteDesign=example
   */ ?>
   ```

3. Clear the INI cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

4. Load a page of the siteaccess with INI debugging on (see the [debug bar](../../bc/6.0/debug-bar.md)), and look for
   the extension's `settings/override/` among the loaded files of `site.ini`. The placement name of such a setting is
   `ext-siteaccess-override:<extension>`.

## Where the files go

For an extension `site_example` with a siteaccess `example`, or siteaccesses of a group written `example__eng` and
`example__ger`, these directories of the extension are read while one of **its** siteaccesses is active. Highest
priority first:

| Directory | Applies to |
|---|---|
| `extension/site_example/settings/override/` | every siteaccess of the extension |
| `extension/site_example/settings/override__<group>/` | every siteaccess whose name starts with `<group>__` |
| `extension/site_example/settings/siteaccess/<siteaccess>/` | that siteaccess only (as before) |

So a setting in `override/` beats the same setting in a siteaccess directory of the same extension, just as the global
`settings/override/` beats everything. The installation's own `settings/override/` still has the last word over all
three. Files are named like the normal ones (`site.ini.append.php` and so on).

## One INI cache directory for every project

`eZINI` used to compute its cache directory relative to its own file. It now takes it from
`$GLOBALS['eZINI_CONFIG_CACHE_DIR']`, and computes the old default only when nothing set it. A multi-site wrapper can
set the global before bootstrapping, so every project on the installation uses the same cache directory; clearing the
INI cache for one project then clears it for all of them. If you do not set it, nothing changes.

## Optional speed-up for many site extensions

If you define the constant `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` as `true` (for example in `config.php`), the
override directories of all other extensions whose directory begins with `extension/site_` are dropped from the list
for the current request. With dozens of site extensions this saves a few file-existence checks per INI file. Leave it
off unless you host many sites.

## Related pages

- [Specification: INI override placements](../../specifications/6.0/ini-override-placements.md)
- [Console: exp:ini](exp-ini-command.md), [extensions in more than one folder](additional-extension-directories.md)
- [Changelog 6.0.12](../../changelogs/6.0/6.0.12.md)
- [Chronicle: January 2026](../../history/2026/2026-01.md)
