---
title: "Configuration: INI files instead of YAML, PHP arrays or .env"
audience: web-framework-developer, developer
category: web-frameworks
order: 170
summary: How Exponential is configured, for developers used to config/*.yaml, config/*.php, settings.py or application.properties - INI files in layers (defaults, extensions, siteaccesses, override), the order in which they win, arrays that add up, reading values with eZINI, changing them with exp:ini, the INI cache, and the mistakes framework developers make most.
keywords: configuration, settings, INI, eZINI, ini.append.php, override, siteaccess, extension settings, exp:ini, YAML, config, settings.py, application.properties, INI cache, clear-tag, web framework developer
type: help
---
# Configuration: INI files instead of YAML, PHP arrays or .env

This page is for developers who configure an application in YAML, PHP arrays, `settings.py` or
`application.properties`. All configuration of Exponential is in INI files, read through `eZINI`
(`lib/ezutils/classes/ezini.php`). The defaults ship in `settings/<file>.ini`; everything you change lives in layers
above them, so updates never overwrite your settings.

## Contents

1. [Files and blocks](#files-and-blocks)
2. [The layers and their order](#the-layers-and-their-order)
3. [Arrays](#arrays)
4. [Reading settings](#reading-settings)
5. [Changing settings](#changing-settings)
6. [Caches](#caches)
7. [Compared with other frameworks](#compared-with-other-frameworks)

## Files and blocks

Each concern has a file (`site.ini`, `content.ini`, `module.ini`, `design.ini`, `override.ini`, `rest.ini`,
`velocity.ini` ...), each file has blocks (`[DatabaseSettings]`), each block has keys. Files you add in a layer are
named `<file>.ini.append.php` and wrap their content in a PHP comment, so the web server never shows them:

```ini
<?php /* #?ini charset="utf-8"?
[DebugSettings]
DebugOutput=enabled
*/ ?>
```

Your extension can also ship a file of its own, `extension/expdemo/settings/expdemo.ini`, with every key documented
([Settings and INI files in your application](../creators/app-creators/settings-and-ini-files.md)).

## The layers and their order

`eZINI` reads the default file, then the override directories in this order, a later file winning
([The INI settings cascade](../../standards/architecture/kernel-architecture.md#the-ini-settings-cascade)):

| Order | Scope | Directories |
|---|---|---|
| 0 | defaults | `settings/<file>.ini` (for your own file, the extension's `settings/<file>.ini`) |
| 1 | siteaccess extension | `extension/<ext>/settings/siteaccess/<siteaccess>/` and related |
| 2 | siteaccess | `settings/siteaccess/<siteaccess>/` |
| 3 | extension | `extension/<ext>/settings/` of each active extension |
| 4 | override | `settings/override/` of the installation |

Note the order: an extension's global `settings/` wins over `settings/siteaccess/<siteaccess>/`, and
`settings/override/` wins over everything. A value meant for one siteaccess only therefore belongs in a siteaccess
directory of an extension or in `settings/siteaccess/`, not in an extension's global settings. The full reference is
[INI override directories and placements](../../specifications/6.0/ini-override-placements.md). With `EXP_ENV` set,
each file can also have an environment variant read right after it ([Environments and secrets](environments-and-secrets.md)).

Extensions are switched on in `site.ini [ExtensionSettings] ActiveExtensions[]` (global) and
`ActiveAccessExtensions[]` (per siteaccess); these two cannot be set from inside an extension.

## Arrays

Keys ending in `[]` are arrays and add up across layers. An empty `Key[]` line clears what came before, so a layer can
replace a list instead of extending it:

```ini
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias
```

Associative entries are written `Implementation[<class>]=<subclass>`.

## Reading settings

```php
$ini = eZINI::instance( 'expdemo.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'DefaultLimit' )
    ? (int)$ini->variable( 'ListSettings', 'DefaultLimit' )
    : 10;
$classes = $ini->variable( 'ListSettings', 'ClassList' );   // an array
```

In templates: `ezini( 'ListSettings', 'DefaultLimit', 'expdemo.ini' )`. Values are strings; cast them, and give a safe
default when a key may be missing. Never parse INI files yourself.

## Changing settings

```bash
./console exp:ini get expdemo.ini/ListSettings/DefaultLimit
./console exp:ini set expdemo.ini/ListSettings/DefaultLimit 20 global
./console exp:ini where expdemo.ini/ListSettings/DefaultLimit site
```

`exp:ini where` lists every file that sets a value for a siteaccess, which answers "why is this value not the one I
wrote" in seconds ([Change settings from the command line](../../features/6.0/exp-ini-command.md)). **Setup >
Settings** shows and edits settings in the browser.

## Caches

Settings are cached. After a change: `php bin/php/ezcache.php --clear-tag=ini` (not `--clear-id=ini`, which misses the
global INI cache), and restart Velocity's workers with `./console exp:velocity deploy`, because the workers keep the
settings they loaded.

## Compared with other frameworks

| Framework | Configuration | Exponential |
|---|---|---|
| Symfony | `config/packages/*.yaml`, `when@prod`, parameters | INI files; environment variants with `EXP_ENV` |
| Laravel | `config/*.php` reading `env()` | INI files; secrets in `settings/override/` |
| Django | `settings.py`, split settings modules | the override layer and siteaccess layers |
| Rails | `config/environments/*.rb`, credentials | `EXP_ENV` variants; `settings/override/` |
| Spring | `application.yml` with profiles | variants per environment; siteaccess layers per site |

There is no expression language, no service parameters and no typed configuration tree: a key is a string or an array
of strings.

## Related pages

- [Settings and INI files in your application](../creators/app-creators/settings-and-ini-files.md)
- [Console exp:ini](../../bc/6.0/console-exp-ini.md)
- [Settings page](../../guides/settings-page.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
