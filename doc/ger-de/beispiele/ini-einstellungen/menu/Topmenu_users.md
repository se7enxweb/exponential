---
title: menu.ini [Topmenu_users]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_users] in menu.ini: Icon, NavigationPartIdentifier, Name, URL, Enabled, Shown, PolicyList, MegaMenuTemplate ..."
keywords: Einstellung, menu.ini, Topmenu_users, INI, setting, Icon, NavigationPartIdentifier, Name, URL, Enabled, Shown, PolicyList, MegaMenuTemplate, MegaMenuSection
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_users]

Der Block `[Topmenu_users]` von `settings/menu.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `people`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
Icon=people
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezusernavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
NavigationPartIdentifier=ezusernavigationpart
*/ ?>
```

## Name

Standard: `Users`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
Name=Users
*/ ?>
```

## URL

> Tooltip=Manage users, user groups and permission settings.  

Standard: ein Hash mit 2: `default=content/view/full/5`, `browse=content/browse/5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=content/view/full/5
URL[browse]=content/browse/5
*/ ?>
```

## Enabled

Standard: ein Hash mit 3: `default=true`, `browse=true`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[default]=true
Enabled[browse]=true
Enabled[edit]=false
*/ ?>
```

## Shown

Standard: ein Hash mit 3: `default=true`, `navigation=true`, `browse=true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=5
*/ ?>
```

## MegaMenuTemplate

> admin4's mega menu (\[TopAdminMenu\] MegaMenuTabs\[\]=users): the template that draws its links, a choice of  
> \[Leftmenu\_user\_mega\] (see there)  

Standard: `design:parts/user/mega_menu.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
MegaMenuTemplate=design:parts/user/mega_menu.tpl
*/ ?>
```

## MegaMenuSection

> The menu section whose MegaMenuLinks\[\] the template uses, for the check of its keys (unknown ones are named in the  
> debug report, parts/a4\_mega\_menu\_check.tpl)  

Standard: `Leftmenu_user_mega`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_users]
MegaMenuSection=Leftmenu_user_mega
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
