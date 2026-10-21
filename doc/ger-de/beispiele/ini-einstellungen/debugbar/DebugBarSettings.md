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

> Die Einstellungen, in der Reihenfolge, in der die Leiste sie innerhalb einer Gruppe auflistet  

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

> Gruppen, in der Reihenfolge, in der die Leiste sie zeigt  

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

> Blöcke, deren jede Variable eine bool-Einstellung der Leiste ist: <file>;<block>;<group>  

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

> Debug-Schalter von Erweiterungen: In settings/\*.ini und \*.ini.append.php jeder aktiven Erweiterung wird eine  
> Variable, deren Name auf DiscoveryPattern passt und deren Wert enabled/disabled oder true/false ist,  
> zu einer bool-Einstellung der Gruppe extensions, außer das Register hat sie schon.  

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

> <extension> oder <extension>;<file> (z. B. ezjscore;ezjscore.ini), die ausgelassen werden  

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

> Vorlagen, aus der Leiste angewendet (jeder Wert ein eigener, rückgängig machbarer Schreibvorgang)  

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

> Die Farben der Live-Zusammenfassung: <warn>;<high>  
> Trefferquote des SQL-Abfrage-Caches der Anfrage in Prozent: gelb UNTER dem ersten, rot unter dem zweiten  
> (auch gelb, wenn ein Schreibvorgang jedes Ergebnis veraltet gemacht hat, rot im gemeinsamen Modus ohne APCu)  
> Belegter Speicher von OPcache und APCu in Prozent (nur für Benutzer mit Setup- oder Cache-Rechten angezeigt; APCu teilen sich  
> alle Websites des PHP-Pools, und der Abfrage-Cache fügt unter 25 % frei keine Ergebnisse mehr hinzu, seine ApcuReserve)  

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

> Panels von Erweiterungen: je ein Reiter nach Speicher, ein Chip in der Zusammenfassung und Daten im JSON der Seite. Eine  
> Erweiterung fügt hier eine ID hinzu und nennt ihre Klasse (expDebugBarPanelInterface) in \[Panel\_<id>\] Class=; die  
> Klasse entscheidet, wer das Panel sehen darf. Anleitung: doc/eng-us/bc/6.0/debug-bar.md, "Panels of extensions".  
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

> Der Template-Baum des Reiters Rendern: jedes Template, das die Seite gerendert hat, verschachtelt, mit der Datei, die die Overrides gewählt haben,  
> und ihrer Zeit (höchstens 2000). Nur aufgezeichnet, solange die Debug-Ausgabe an ist; disabled lässt ihn weg.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
RenderTree=enabled
*/ ?>
```

## Theme

> Das Aussehen der Leiste: default (helle Chips) oder toolbar (eine dunkle, kompakte Werkzeugleiste mit einem Symbol in jedem Block, dem  
> HTTP-Status der Anfrage und Modul/Ansicht im ersten Block und den Details eines Blocks in einem Popup darüber). Das Panel  
> über der Leiste ist in beiden gleich. Jeder andere Wert ist default.  

Standard: `default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
Theme=default
*/ ?>
```

## LogFile

> Das Schreibprotokoll, in var/<site>/log/ (ein JSON-Objekt pro Zeile)  

Standard: `debugbar.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debugbar.ini.append.php
[DebugBarSettings]
LogFile=debugbar.log
*/ ?>
```

## LogListLimit

> Einträge, die die Leiste auflistet  

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
