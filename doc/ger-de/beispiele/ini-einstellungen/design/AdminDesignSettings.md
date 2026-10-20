---
title: design.ini [AdminDesignSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminDesignSettings] in design.ini: ShowCommunityLinks, TopMenuIcons, IconList"
keywords: Einstellung, design.ini, AdminDesignSettings, INI, setting, ShowCommunityLinks, TopMenuIcons, IconList
type: generated
generated: exp:help:examples
---
# design.ini [AdminDesignSettings]

Der Block `[AdminDesignSettings]` von `settings/design.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/design.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/design.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/design.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/design.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ShowCommunityLinks

> admin4: the footer's "Evaluate Exponential / Become a member of the Exponential Community" line.  
> enabled shows it; disabled (the default) leaves it out. A template can also set $show\_community\_links.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[AdminDesignSettings]
ShowCommunityLinks=disabled
*/ ?>
```

## TopMenuIcons

> admin4: the icons before the tabs of the header's main menu (menu.ini \[Topmenu\_<tab>\] Icon, a name of IconList\[\]).  
> enabled (default) draws them; disabled shows the names alone. An extension's tab sets Icon= in its own  
> menu.ini.append.php, and a siteaccess or extension may override any tab's Icon there.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[AdminDesignSettings]
TopMenuIcons=enabled
*/ ?>
```

## IconList

> admin4: the icons a setting may name (menu.ini LinkIcons\[\] of a menu drawn as tiles, the Setup overview). Each name  
> has a mask rule .a4-bi-<name> in components.css (a Bootstrap Icons 1.13.2 SVG in share/icons/bootstrap-icons/svg/);  
> a name not listed here draws the plug icon. An extension that ships an icon of its own adds its SVG, a rule  
> .a4-bi-<name> in a stylesheet of BackendCSSFileList\[\] and IconList\[\]=<name> in its design.ini.append.php  
> (doc/eng-us/features/6.0/admin4-design.md, "Setup overview").  

Standard: eine Liste mit 137: `123`, `archive`, `arrow-90deg-up`, `arrow-bar-down`, `arrow-bar-up`, `arrow-clockwise`, `arrow-counterclockwise`, `arrow-down`, `arrow-down-up`, `arrow-down-right`, `arrow-up`, `arrow-up-circle` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[AdminDesignSettings]
# ein leeres "IconList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IconList[]
IconList[]=123
IconList[]=archive
IconList[]=arrow-90deg-up
IconList[]=arrow-bar-down
IconList[]=arrow-bar-up
IconList[]=arrow-clockwise
IconList[]=arrow-counterclockwise
IconList[]=arrow-down
IconList[]=arrow-down-up
IconList[]=arrow-down-right
IconList[]=arrow-up
IconList[]=arrow-up-circle
IconList[]=arrow-up-right
IconList[]=arrows-collapse-vertical
IconList[]=arrows-expand-vertical
IconList[]=arrows-move
IconList[]=asterisk
IconList[]=bank
IconList[]=bar-chart-line
IconList[]=bell
# ... 117 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von design.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/design.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
