# Per-site settings inside extensions (multi-site hosting)

One Exponential installation can serve many sites. Since January 2026 an
extension that holds a site can carry **override settings that are active only
while one of its siteaccesses runs**, and clearing the INI cache clears it for
every project on the installation.

## The problem it solves

Hosting ten sites from one installation used to mean one big `settings/override`
directory for all of them, or ten siteaccess directories under `settings/`. A
site that lives in its own extension (`extension/site_projectname`) could ship
`settings/siteaccess/<name>/` but had no place for settings that apply to all of
its siteaccesses, or to a language group of them.

## Where the files go

For an extension `site_example` with a siteaccess `example`, or siteaccesses of
a group written `example__eng` and `example__ger`, these directories of the
extension are read while one of **its** siteaccesses is active. The list is in
order of priority, highest first:

| Directory | Applies to |
|---|---|
| `extension/site_example/settings/override/` | every siteaccess of the extension |
| `extension/site_example/settings/override__<group>/` | every siteaccess whose name starts with `<group>__` |
| `extension/site_example/settings/siteaccess/<siteaccess>/` | that siteaccess only (as before) |

So a setting in `override/` beats the same setting in a siteaccess directory of
the same extension, like the global `settings/override/` beats everything. The
installation's own `settings/override/` still has the last word over all three.

Files are named like the normal ones (`site.ini.append.php` and so on). The two
new directories are looked up only when the extension has a
`settings/siteaccess/<active siteaccess>` directory, so unrelated extensions cost
nothing.

A short example:

```ini
# extension/site_example/settings/override/site.ini.append.php
<?php /*
[SiteSettings]
SiteName=Example, all languages

[DesignSettings]
SiteDesign=example
*/ ?>
```

## Optional speed-up for installations with many site extensions

If you define the constant `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` as
`true` (for example in `config.php`), the override directories of all other
extensions whose directory begins with `extension/site_` are dropped from the
list for the current request. With dozens of site extensions this saves a few
file-existence checks per INI file. Leave it off unless you host many sites.

## One INI cache directory for everybody

`eZINI` used to compute its cache directory relative to its own file. It now
takes it from `$GLOBALS['eZINI_CONFIG_CACHE_DIR']`, computing the old default
only when nothing set it. A multi-site wrapper can set the global before
bootstrapping so every project on the installation uses the same cache
directory; clearing the INI cache for one project then clears it for all of
them. If you do not set it, nothing changes.

## Check that it works

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

then load a page of the siteaccess with INI debugging on (see the
[debug bar](../../bc/6.0/debug-bar.md)) and look for the extension's
`settings/override/` among the loaded files of `site.ini`. The placement name
of such a setting is `ext-siteaccess-override:<extension>`.

## Related

[Specification: INI override placements](../../specifications/6.0/ini-override-placements.md),
[Console: exp:ini](exp-ini-command.md),
[Chronicle: January 2026](../../history/2026/2026-01.md).

## See also

[Changelog 6.0.12](../../changelogs/6.0/6.0.12.md); [Chronicle: January 2026](../../history/2026/2026-01.md).

## Related pages

- [Extensions in more than one folder](additional-extension-directories.md)
