---
title: Settings that live in an extension
audience: developer, designer, app-creator
category: extension-development
order: 40
summary: Keep a site's configuration in a settings extension under version control - the folder layout, global and per-siteaccess settings, moving existing settings in with exp:ini move-all, and what stays out (secrets).
keywords: settings extension, site extension, configuration, version control, move-all, create-extension, siteaccess settings in extension, override__group, secrets
type: help
---
# Settings that live in an extension

Part 2, chapter 4. A **settings extension** is an extension that holds nothing but settings: the configuration of
your site. It is the first extension most builders create, and the safest: it adds no code, so it cannot break
anything that settings could not already break. This chapter builds one.

## Why put settings in an extension

| Settings in `settings/override/` and `settings/siteaccess/` | Settings in an extension |
|---|---|
| Mixed with server-specific values and secrets | Only the site's configuration; secrets stay out |
| Hard to put under version control | One folder, one Git repository |
| Copied by hand to a second server | Installed like any extension |
| Lost or merged badly when someone reinstalls | Survive a reinstall: switch the extension on again |

## The layout

```text
extension/mysite_settings/
    ezinfo.php
    extension.xml
    settings/
        site.ini.append.php            settings for every siteaccess
        content.ini.append.php
        image.ini.append.php
        siteaccess/
            mysite/                    one site's own settings (makes the extension its owner)
                site.ini.append.php
                override.ini.append.php
            mysite_admin/
                site.ini.append.php
```

The metadata files are the same as for any extension; see ezinfo.php
and extension.xml.

## Build it in one command

`exp:ini move-all` moves every settings file of one scope into another and can create the extension on the way:

```bash
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --dry-run
```

The dry run prints what would move. When it is right:

```bash
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --activate
```

`--create-extension` writes `extension.xml`, `ezinfo.php` and `settings/` if the extension does not exist;
`--activate` adds it to `ActiveExtensions[]` in the global override. A move is rolled back if a value in effect would
change, unless you pass `--force`; `--files=site,content` limits it to some files. Afterwards:

```bash
./console exp:ini where site.ini/DesignSettings/SiteDesign -s mysite
```

shows the value now coming from `ext-siteaccess:mysite_settings`.

## Or by hand

```bash
mkdir -p extension/mysite_settings/settings/siteaccess/mysite
```

Create `extension/mysite_settings/settings/siteaccess/mysite/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=My site

[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

*/ ?>
```

Add the metadata files, switch the extension on, and clear the INI cache:

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_settings global
php bin/php/ezcache.php --clear-tag=ini
```

## What does not belong in it

- **Database settings and passwords** (`[DatabaseSettings]`), mail server credentials, API keys, secrets of any
  kind. They stay in `settings/override/`, which is never committed.
- **Values that differ between your development and live servers** (host names, paths, debug switches). Keep them in
  each server's `settings/override/`.
- **Another extension's files.** Override its settings; never copy and edit its files.

The `.gitignore` of a settings extension can be empty; there is nothing secret in it, by design. If you are unsure,
`exp:ini` masks values of variables named like `Password`, `Secret`, `Token`, `Salt` or `...Key` when it prints them,
which is a good hint for what to keep out.

## Order matters

A settings extension that should win over other extensions must be listed **before** them in `ActiveExtensions[]`
(an earlier extension has the higher priority). The Extensions page in the administration lets you move it up, and
shows the loading order the kernel really uses; see [Extension loading order](../../../features/6.0/extension-loading-order.md).

## Exercise

1. Run the `move-all` dry run for one of your siteaccesses.
2. Read what it would move. Is there anything secret in it? If so, plan to keep that block in `settings/override/`
   (`exp:ini move` of single blocks gives you that control).
3. Do the move on your development installation and put the new extension under Git: `git -C extension/mysite_settings init`.

## Related pages

- Previous: [Siteaccess settings](siteaccess-settings.md)
- Next: [Changing settings with exp:ini](the-exp-ini-command.md)
- [Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md)
- With RAD: a settings-only extension
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
