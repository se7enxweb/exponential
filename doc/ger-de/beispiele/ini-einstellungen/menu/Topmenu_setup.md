---
title: menu.ini [Topmenu_setup]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_setup] in menu.ini: Icon, NavigationPartIdentifier, URL, Enabled, Shown, MegaMenuTemplate, MegaMenuSection"
keywords: Einstellung, menu.ini, Topmenu_setup, INI, setting, Icon, NavigationPartIdentifier, URL, Enabled, Shown, MegaMenuTemplate, MegaMenuSection
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_setup]

Der Block `[Topmenu_setup]` von `settings/menu.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `gear`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_setup]
Icon=gear
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezsetupnavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_setup]
NavigationPartIdentifier=ezsetupnavigationpart
*/ ?>
```

## URL

> Name=Setup  
> Tooltip=Configure settings and manage advanced functionality.  
> Die Setup-Übersicht (setup/menu): jede Funktion des Setup-Menüs als Kachel, gruppiert wie das linke Menü  

Standard: ein Hash mit 1: `default=setup/menu`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_setup]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=setup/menu
*/ ?>
```

## Enabled

Standard: ein Hash mit 3: `default=true`, `browse=false`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_setup]
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
[Topmenu_setup]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true
*/ ?>
```

## MegaMenuTemplate

> Keine PolicyList\[\]: setup/menu öffnet sich mit einer beliebigen seiner Funktionen (setup, administrate, managecache, managecronjobs,  
> preload, system\_info), wonach \[MenuAccessSettings\] CheckViewAccess fragt. \[TopAdminMenu\] SetupTabs\[\] verlassen das Haupt-  
> menü für dieselben Benutzer, der Reiter, der sie aufnimmt, verlangt also nichts weiter (setup/managecache nahm ihn einem Benutzer  
> mit nur system\_info, und die in Setup verschobenen Reiter waren nirgends).  
> Das Mega-Menü von admin4 (\[TopAdminMenu\] MegaMenuTabs\[\]=setup): das Template, das seine Links zeichnet, das Setup-Menü  
> (\[Leftmenu\_setup\] MegaMenuLinks\[\])  

Standard: `design:parts/setup/mega_menu.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_setup]
MegaMenuTemplate=design:parts/setup/mega_menu.tpl
*/ ?>
```

## MegaMenuSection

> Der Menüabschnitt, dessen MegaMenuLinks\[\] das Template verwendet, für die Prüfung seiner Schlüssel (unbekannte werden im  
> Debug-Bericht genannt, parts/a4\_mega\_menu\_check.tpl)  

Standard: `Leftmenu_setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_setup]
MegaMenuSection=Leftmenu_setup
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
