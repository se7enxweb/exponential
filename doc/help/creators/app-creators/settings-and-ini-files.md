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

A value is read in this order; the last one wins:

1. `settings/<file>.ini` (the kernel's defaults; for your own file, the extension's `settings/<file>.ini`)
2. active extensions' `settings/<file>.ini.append.php`
3. `settings/siteaccess/<siteaccess>/` and extensions' `settings/siteaccess/<siteaccess>/`
4. extensions' `settings/override/` for their own siteaccesses ([Per-site settings](../../../features/6.0/multi-site-ini-overrides.md))
5. `settings/override/<file>.ini.append.php` of the installation

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
Environment-specific values: Environment settings and secrets.

## Caches

Settings are cached. After a change: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` (not `--clear-id=ini`,
which misses the global INI cache), and restart Velocity's workers (`exp:velocity deploy`).

## Related pages

- [Anatomy of an extension](extension-anatomy.md)
- [Console exp:ini](../../../bc/6.0/console-exp-ini.md)
- [INI files keep their comments](../../../bc/6.0/eZINI_PRESERVES_COMMENTS.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
