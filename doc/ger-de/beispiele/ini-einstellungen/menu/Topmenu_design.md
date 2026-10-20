---
title: menu.ini [Topmenu_design]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_design] in menu.ini: Icon, NavigationPartIdentifier, URL, Enabled, Shown, PolicyList"
keywords: Einstellung, menu.ini, Topmenu_design, INI, setting, Icon, NavigationPartIdentifier, URL, Enabled, Shown, PolicyList
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_design]

Der Block `[Topmenu_design]` von `settings/menu.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `palette`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_design]
Icon=palette
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezvisualnavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_design]
NavigationPartIdentifier=ezvisualnavigationpart
*/ ?>
```

## URL

> Name=Design  
> Tooltip=Manage templates, menus, toolbars and other things related to appearence.  
> URL\[default\]=visual/toolbarlist  
> The design top node (content.ini \[NodeSettings\] DesignRootNode) by its node id, not by its URL alias  
> "Design/", which a renamed node or another language's alias would break  

Standard: ein Hash mit 1: `default=content/view/full/58`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_design]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=content/view/full/58
*/ ?>
```

## Enabled

Standard: ein Hash mit 3: `default=true`, `browse=false`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_design]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
*/ ?>
```

## Shown

Standard: ein Hash mit 3: `navigation=true`, `default=true`, `browse=true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_design]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[navigation]=true
Shown[default]=true
Shown[browse]=true
*/ ?>
```

## PolicyList

> The design root node (content.ini \[NodeSettings\] DesignRootNode) must be readable  

Standard: eine Liste mit 1: `58`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_design]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=58
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
