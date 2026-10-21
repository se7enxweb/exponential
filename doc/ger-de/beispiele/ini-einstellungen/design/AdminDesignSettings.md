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

> admin4: die Zeile „Exponential bewerten / Mitglied der Exponential-Community werden“ in der Fußzeile.  
> enabled zeigt sie; disabled (der Standard) lässt sie weg. Ein Template kann auch $show\_community\_links setzen.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[AdminDesignSettings]
ShowCommunityLinks=disabled
*/ ?>
```

## TopMenuIcons

> admin4: die Symbole vor den Reitern des Hauptmenüs im Kopf (menu.ini \[Topmenu\_<tab>\] Icon, ein Name aus IconList\[\]).  
> enabled (Standard) zeichnet sie; disabled zeigt nur die Namen. Der Reiter einer Erweiterung setzt Icon= in ihrer eigenen  
> menu.ini.append.php, und ein Siteaccess oder eine Erweiterung kann dort das Icon jedes Reiters überschreiben.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[AdminDesignSettings]
TopMenuIcons=enabled
*/ ?>
```

## IconList

> admin4: die Symbole, die eine Einstellung nennen darf (menu.ini LinkIcons\[\] eines als Kacheln gezeichneten Menüs, die Setup-Übersicht). Jeder Name  
> hat eine Masken-Regel .a4-bi-<name> in components.css (ein SVG von Bootstrap Icons 1.13.2 in share/icons/bootstrap-icons/svg/);  
> ein hier nicht aufgeführter Name zeichnet das Stecker-Symbol. Eine Erweiterung, die ein eigenes Symbol mitliefert, fügt ihr SVG, eine Regel  
> .a4-bi-<name> in einem Stylesheet aus BackendCSSFileList\[\] und IconList\[\]=<name> in ihrer design.ini.append.php hinzu  
> (doc/eng-us/features/6.0/admin4-design.md, "Setup overview").  

Standard: eine Liste mit 141: `123`, `archive`, `arrow-90deg-up`, `arrow-bar-down`, `arrow-bar-up`, `arrow-clockwise`, `arrow-counterclockwise`, `arrow-down`, `arrow-down-up`, `arrow-down-right`, `arrow-up`, `arrow-up-circle` ....

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
# ... 121 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von design.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/design.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
