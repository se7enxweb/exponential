---
title: Settings and INI files in your application
audience: app-creator, developer
category: app-creation
order: 80
summary: Give your application settings the Exponential way - your own INI file with defaults, overrides per siteaccess and installation, reading them with eZINI, changing them with exp:ini, caches, and keeping secrets out of Git.
keywords: settings, INI, eZINI, ini.append.php, override, siteaccess, exp:ini, defaults, secrets, configuration, hasVariable, variable, app creator
type: help
---
# Settings and INI files in your application

This page is for application creators who make their application configurable. Exponential's settings are INI files
in layers; your extension ships its defaults and installations override them without touching your code.

## Contents

1. [Your own INI file](#your-own-ini-file)
2. [The layers](#the-layers)
3. [Reading settings in PHP and templates](#reading-settings-in-php-and-templates)
4. [Changing settings](#changing-settings)
5. [Secrets](#secrets)
6. [Caches](#caches)

## Your own INI file

Ship defaults in `extension/expdemo/settings/expdemo.ini` (a new file of your own) and document every key there:

```ini
#?ini charset="utf-8"?
# Settings of the expdemo extension.

[ListSettings]
# How many items the latest view shows when no limit is given
DefaultLimit=10
# Class identifiers listed
ClassList[]
ClassList[]=article
```

Changes to other INI files (`site.ini`, `module.ini`, `content.ini` ...) go in `*.ini.append.php` files of your
extension, which wrap the content in a PHP comment so the web server never shows them:

```ini
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
*/ ?>
```

## The layers

After `settings/<file>.ini` (the kernel's defaults), `eZINI::overrideDirs()` reads the override directories scope by
scope: `sa-extension`, `siteaccess`, `extension`, `override`. A value is read in this order; the last one wins:

1. `settings/<file>.ini`, the kernel's defaults
2. `sa-extension`: `settings/` of the extensions the siteaccess switches on in `ActiveAccessExtensions[]`
3. `siteaccess`: the per-siteaccess directories inside extensions (`settings/siteaccess/<siteaccess>/`, then
   `settings/override__<group>/` and `settings/override/`, read only for a siteaccess the extension has a
   `settings/siteaccess/<siteaccess>/` directory for; [Per-site settings](../../../features/6.0/multi-site-ini-overrides.md)),
   then the installation's `settings/siteaccess/<siteaccess>/`
4. `extension`: `settings/` of every extension in `ActiveExtensions[]`, so also the `<file>.ini` and
   `<file>.ini.append.php` your extension ships
5. `override`: `settings/override/` of the installation

So a value in an active extension's `settings/` wins over the same value in `settings/siteaccess/<siteaccess>/`; only
`settings/override/` wins over it, and that applies to every siteaccess. If a site must be able to set a key per
siteaccess, do not ship that key in your extension's `settings/` file; give its default in code with `hasVariable()`
(below). Inside one layer, an extension listed earlier in `ActiveExtensions[]` wins over a later one.
`./console exp:ini where <file>/<block>/<key> <siteaccess>` prints the files in this order for one setting.

Arrays add up across layers; an empty `Key[]` line clears what came before.

## Reading settings in PHP and templates

```php
$ini = eZINI::instance( 'expdemo.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'DefaultLimit' )
    ? (int)$ini->variable( 'ListSettings', 'DefaultLimit' )
    : 10;
$classes = $ini->variable( 'ListSettings', 'ClassList' );   // an array
```

In templates: `ezini( 'ListSettings', 'DefaultLimit', 'expdemo.ini' )`. Always give a safe default when a key may be
missing.

## Changing settings

`exp:ini` reads and writes every scope line by line, keeping comments
([Change settings from the command line](../../../features/6.0/exp-ini-command.md)):

```bash
./console exp:ini get expdemo.ini/ListSettings/DefaultLimit
./console exp:ini set expdemo.ini/ListSettings/DefaultLimit 20 global
./console exp:ini where expdemo.ini/ListSettings/DefaultLimit site
```

**Setup > Settings** shows and edits settings in the browser ([Settings page](../../../guides/settings-page.md)).

## Secrets

API keys, passwords and tokens never go into your extension's repository. Read them from a key the installation sets in
`settings/override/<file>.ini.append.php` (mode 0640, not committed), and fail with a clear message when it is empty.
Environment-specific values: [Environment settings and secrets](environment-settings-and-secrets.md).

## Caches

Settings are cached. After a change: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` (not `--clear-id=ini`,
which misses the global INI cache), and restart Velocity's workers (`exp:velocity deploy`).

## Related pages

- [Anatomy of an extension](extension-anatomy.md)
- [Console exp:ini](../../../bc/6.0/console-exp-ini.md)
- [INI files keep their comments](../../../bc/6.0/eZINI_PRESERVES_COMMENTS.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
