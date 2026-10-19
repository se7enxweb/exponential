---
title: debugbar.ini [DebugBarSettings]
audience: developer, admin
category: examples
summary: "The settings of [DebugBarSettings] in debugbar.ini: Settings, Groups, GroupNames, DiscoverBlocks, ExtensionDiscovery, DiscoveryPattern, DiscoveryExclude, Presets ..."
keywords: debugbar.ini, DebugBarSettings, INI, setting, Settings, Groups, GroupNames, DiscoverBlocks, ExtensionDiscovery, DiscoveryPattern, DiscoveryExclude, Presets, Thresholds, Panels, RenderTree, Theme, LogFile, LogListLimit
type: generated
generated: exp:help:examples
---
# debugbar.ini [DebugBarSettings]

The block `[DebugBarSettings]` of `settings/debugbar.ini`, with 14 setting(s).

Never edit `settings/debugbar.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/debugbar.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in an extension (read when the extension is active).

## Settings

> The settings, in the order the bar lists them inside a group  

Default: a list of 37: `debug_output`, `debug_mode`, `debug_redirection`, `display_debug_warnings`, `debug_log_only`, `display_included_files`, `always_log`, `deprecated_log`, `script_debug_output`, `debug_toolbar`, `debug_bar_theme`, `debug_by_ip` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "Settings[]" first replaces the shipped list; without it the items are added
Settings[]
Settings[]=debug_output
Settings[]=debug_mode
Settings[]=debug_redirection
Settings[]=display_debug_warnings
Settings[]=debug_log_only
Settings[]=display_included_files
Settings[]=always_log
Settings[]=deprecated_log
Settings[]=script_debug_output
Settings[]=debug_toolbar
Settings[]=debug_bar_theme
Settings[]=debug_by_ip
Settings[]=debug_ip_list
Settings[]=debug_by_user
Settings[]=debug_user_list
Settings[]=template_debug
Settings[]=template_show_xhtml_code
Settings[]=template_show_used_templates
Settings[]=template_path_comments
Settings[]=template_show_method_debug
# ... 17 more in the shipped file
*/ ?>
```

## Groups

> Groups, in the order the bar shows them  

Default: a list of 9: `output`, `access`, `templates`, `sql`, `regional`, `mail`, `conditions`, `caching`, `extensions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "Groups[]" first replaces the shipped list; without it the items are added
Groups[]
Groups[]=output
Groups[]=access
Groups[]=templates
Groups[]=sql
Groups[]=regional
Groups[]=mail
Groups[]=conditions
Groups[]=caching
Groups[]=extensions
*/ ?>
```

## GroupNames

Default: a hash of 9: `output=Debug output`, `access=Who gets debug`, `templates=Templates`, `sql=Database`, `regional=Translations`, `mail=Mail`, `conditions=Debug conditions`, `caching=Caching`, `extensions=Extensions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "GroupNames[]" first replaces the shipped list; without it the items are added
GroupNames[]
GroupNames[output]=Debug output
GroupNames[access]=Who gets debug
GroupNames[templates]=Templates
GroupNames[sql]=Database
GroupNames[regional]=Translations
GroupNames[mail]=Mail
GroupNames[conditions]=Debug conditions
GroupNames[caching]=Caching
GroupNames[extensions]=Extensions
*/ ?>
```

## DiscoverBlocks

> Blocks whose every variable is a bool setting of the bar: <file>;<block>;<group>  

Default: a list of 1: `debug.ini;GeneralCondition;conditions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "DiscoverBlocks[]" first replaces the shipped list; without it the items are added
DiscoverBlocks[]
DiscoverBlocks[]=debug.ini;GeneralCondition;conditions
*/ ?>
```

## ExtensionDiscovery

> Extension debug switches: in every active extension's settings/\*.ini and \*.ini.append.php, a  
> variable whose name matches DiscoveryPattern and whose value is enabled/disabled or true/false  
> becomes a bool setting of the group extensions, unless the registry already has it.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
ExtensionDiscovery=enabled
*/ ?>
```

## DiscoveryPattern

Default: `/debug/i`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
DiscoveryPattern=/debug/i
*/ ?>
```

## DiscoveryExclude

> <extension> or <extension>;<file> (e.g. ezjscore;ezjscore.ini) to leave out  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "DiscoveryExclude[]" first replaces the shipped list; without it the items are added
DiscoveryExclude[]
*/ ?>
```

## Presets

> Presets, applied from the bar (each value a separate, undoable write)  

Default: a list of 4: `template_work`, `sql_tuning`, `everything`, `off`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "Presets[]" first replaces the shipped list; without it the items are added
Presets[]
Presets[]=template_work
Presets[]=sql_tuning
Presets[]=everything
Presets[]=off
*/ ?>
```

## Thresholds

> The colours of the live summary: <warn>;<high>  
> SQL query cache hit rate of the request in percent: amber BELOW the first, red below the second  
> (also amber when a write made every result stale, red in shared mode without APCu)  
> Memory of OPcache and APCu in use, in percent (shown only to users with setup or cache rights; APCu is shared by  
> every site of the PHP pool, and the query cache stops adding results below 25 % free, its ApcuReserve)  

Default: a hash of 8: `time_ms=500;1500`, `sql_count=100;300`, `sql_ms=200;800`, `memory_mb=64;192`, `templates=150;400`, `querycache_hit_rate=50;10`, `opcache_memory=80;95`, `apcu_memory=75;90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "Thresholds[]" first replaces the shipped list; without it the items are added
Thresholds[]
Thresholds[time_ms]=500;1500
Thresholds[sql_count]=100;300
Thresholds[sql_ms]=200;800
Thresholds[memory_mb]=64;192
Thresholds[templates]=150;400
Thresholds[querycache_hit_rate]=50;10
Thresholds[opcache_memory]=80;95
Thresholds[apcu_memory]=75;90
*/ ?>
```

## Panels

> Panels of extensions: a tab each after Memory, a chip in the summary and data in the page's JSON. An  
> extension adds an id here and names its class (expDebugBarPanelInterface) in \[Panel\_<id>\] Class=; the  
> class decides who may see the panel. Guide: doc/bc/6.0/debug-bar.md, "Panels of extensions".  
>   Panels\[\]=mypanel  
>   \[Panel\_mypanel\]  
>   Class=myExtensionDebugBarPanel  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# an empty "Panels[]" first replaces the shipped list; without it the items are added
Panels[]
*/ ?>
```

## RenderTree

> The template tree of the Rendering tab: every template the page rendered, nested, with the file the overrides chose  
> and its time (at most 2000). Recorded only while debug output is on; disabled leaves it out.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
RenderTree=enabled
*/ ?>
```

## Theme

> The looks of the bar: default (light chips) or toolbar (a dark, compact toolbar with an icon in each block, the  
> request's HTTP status and module/view in the first block, and a block's details in a popup above it). The panel  
> above the bar is the same in both. Any other value is default.  

Default: `default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
Theme=default
*/ ?>
```

## LogFile

> The write log, in var/<site>/log/ (one JSON object per line)  

Default: `debugbar.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
LogFile=debugbar.log
*/ ?>
```

## LogListLimit

> Entries the bar lists  

Default: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
LogListLimit=20
*/ ?>
```

## Related

- [All blocks of debugbar.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/debugbar.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
