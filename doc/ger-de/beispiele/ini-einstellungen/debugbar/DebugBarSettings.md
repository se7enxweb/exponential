---
title: debugbar.ini [DebugBarSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DebugBarSettings] in debugbar.ini: Settings, Groups, GroupNames, DiscoverBlocks, ExtensionDiscovery, DiscoveryPattern, DiscoveryExclude, Presets ..."
keywords: Einstellung, debugbar.ini, DebugBarSettings, INI, setting, Settings, Groups, GroupNames, DiscoverBlocks, ExtensionDiscovery, DiscoveryPattern, DiscoveryExclude, Presets, Thresholds, Panels, RenderTree, Theme, LogFile, LogListLimit
type: generated
generated: exp:help:examples
---
# debugbar.ini [DebugBarSettings]

Der Block `[DebugBarSettings]` von `settings/debugbar.ini`, mit 14 Einstellung(en).

Bearbeiten Sie `settings/debugbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debugbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debugbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debugbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Settings

> The settings, in the order the bar lists them inside a group  

Standard: eine Liste mit 37: `debug_output`, `debug_mode`, `debug_redirection`, `display_debug_warnings`, `debug_log_only`, `display_included_files`, `always_log`, `deprecated_log`, `script_debug_output`, `debug_toolbar`, `debug_bar_theme`, `debug_by_ip` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "Settings[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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
# ... 17 weitere in der ausgelieferten Datei
*/ ?>
```

## Groups

> Groups, in the order the bar shows them  

Standard: eine Liste mit 9: `output`, `access`, `templates`, `sql`, `regional`, `mail`, `conditions`, `caching`, `extensions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: ein Hash mit 9: `output=Debug output`, `access=Who gets debug`, `templates=Templates`, `sql=Database`, `regional=Translations`, `mail=Mail`, `conditions=Debug conditions`, `caching=Caching`, `extensions=Extensions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "GroupNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: eine Liste mit 1: `debug.ini;GeneralCondition;conditions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "DiscoverBlocks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DiscoverBlocks[]
DiscoverBlocks[]=debug.ini;GeneralCondition;conditions
*/ ?>
```

## ExtensionDiscovery

> Extension debug switches: in every active extension's settings/\*.ini and \*.ini.append.php, a  
> variable whose name matches DiscoveryPattern and whose value is enabled/disabled or true/false  
> becomes a bool setting of the group extensions, unless the registry already has it.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
ExtensionDiscovery=enabled
*/ ?>
```

## DiscoveryPattern

Standard: `/debug/i`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
DiscoveryPattern=/debug/i
*/ ?>
```

## DiscoveryExclude

> <extension> or <extension>;<file> (e.g. ezjscore;ezjscore.ini) to leave out  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "DiscoveryExclude[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DiscoveryExclude[]
*/ ?>
```

## Presets

> Presets, applied from the bar (each value a separate, undoable write)  

Standard: eine Liste mit 4: `template_work`, `sql_tuning`, `everything`, `off`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "Presets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: ein Hash mit 8: `time_ms=500;1500`, `sql_count=100;300`, `sql_ms=200;800`, `memory_mb=64;192`, `templates=150;400`, `querycache_hit_rate=50;10`, `opcache_memory=80;95`, `apcu_memory=75;90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "Thresholds[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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
> class decides who may see the panel. Guide: doc/eng-us/bc/6.0/debug-bar.md, "Panels of extensions".  
>   Panels\[\]=mypanel  
>   \[Panel\_mypanel\]  
>   Class=myExtensionDebugBarPanel  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
# ein leeres "Panels[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Panels[]
*/ ?>
```

## RenderTree

> The template tree of the Rendering tab: every template the page rendered, nested, with the file the overrides chose  
> and its time (at most 2000). Recorded only while debug output is on; disabled leaves it out.  

Standard: `enabled`.

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

Standard: `default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
Theme=default
*/ ?>
```

## LogFile

> The write log, in var/<site>/log/ (one JSON object per line)  

Standard: `debugbar.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
LogFile=debugbar.log
*/ ?>
```

## LogListLimit

> Entries the bar lists  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
LogListLimit=20
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debugbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debugbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
