---
title: menu.ini [Topmenu_dashboard]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_dashboard] in menu.ini: Icon, NavigationPartIdentifier, URL, Enabled, Shown, PolicyList, MegaMenuTemplate, MegaMenuSection ..."
keywords: Einstellung, menu.ini, Topmenu_dashboard, INI, setting, Icon, NavigationPartIdentifier, URL, Enabled, Shown, PolicyList, MegaMenuTemplate, MegaMenuSection, MegaMenuLayout
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_dashboard]

Der Block `[Topmenu_dashboard]` von `settings/menu.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `speedometer2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
Icon=speedometer2
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezmynavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
NavigationPartIdentifier=ezmynavigationpart
*/ ?>
```

## URL

> Name=My account  
> Tooltip=Manage items and settings that belong to your account.  

Standard: ein Hash mit 1: `default=content/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=content/dashboard
*/ ?>
```

## Enabled

Standard: ein Hash mit 3: `default=true`, `browse=false`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
*/ ?>
```

## Shown

Standard: ein Hash mit 3: `default=true`, `navigation=true`, `browse=true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `content/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=content/dashboard
*/ ?>
```

## MegaMenuTemplate

> Das Mega-Menü von admin4 (\[TopAdminMenu\] MegaMenuTabs\[\]=dashboard): das Template, das seine Links zeichnet, eine Auswahl aus  
> \[Leftmenu\_my\_mega\] (siehe dort)  

Standard: `design:parts/my/mega_menu.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
MegaMenuTemplate=design:parts/my/mega_menu.tpl
*/ ?>
```

## MegaMenuSection

> Der Menüabschnitt, dessen MegaMenuLinks\[\] das Template verwendet, für die Prüfung seiner Schlüssel (unbekannte werden im  
> Debug-Bericht genannt, parts/a4\_mega\_menu\_check.tpl)  

Standard: `Leftmenu_my_mega`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
MegaMenuSection=Leftmenu_my_mega
*/ ?>
```

## MegaMenuLayout

> Das Layout des Panels: wide setzt die Gruppen in bis zu vier ausgeglichene Spalten (drei bei 960px, darunter zwei), für eine Auswahl  
> vieler Links, die in den drei Standardspalten der Reiter Setup und Benutzer nicht in die Höhe des Fensters passen würden  

Standard: `wide`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_dashboard]
MegaMenuLayout=wide
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
