---
title: menu.ini [TopAdminMenu]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TopAdminMenu] in menu.ini: HiddenTabs, SetupTabs, MegaMenuTabs, Tabs"
keywords: Einstellung, menu.ini, TopAdminMenu, INI, setting, HiddenTabs, SetupTabs, MegaMenuTabs, Tabs
type: generated
generated: exp:help:examples
---
# menu.ini [TopAdminMenu]

Der Block `[TopAdminMenu]` von `settings/menu.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HiddenTabs

> Tabs the top menu leaves out, whatever Tabs\[\] says: a siteaccess cannot take a tab  
> out of Tabs\[\] that an extension appends (extension settings are read after the  
> siteaccess's). The editor siteaccess hides the administration's tabs with it.  
>
> Fewer tabs, nothing lost (admin4, doc/eng-us/features/6.0/admin4-design.md "Moving a tab into Setup"):  
> design and audit have a group of the Setup menu and overview (\[Leftmenu\_setup\] GroupTabs\[\]),  
> shown only while their tab is not in the top menu; their pages then mark the Setup tab and  
> draw the Setup menu. Without the dashboard tab the logo leads to the dashboard. In a  
> siteaccess or override menu.ini.append.php:  
>   HiddenTabs\[\]  
>   HiddenTabs\[\]=dashboard  
>   HiddenTabs\[\]=design  
>   HiddenTabs\[\]=audit  
> or restate the whole list (Tabs\[\] with no value, then the tabs to keep); an extension's tab  
> can only be left out with HiddenTabs\[\].  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "HiddenTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HiddenTabs[]
*/ ?>
```

## SetupTabs

> Tabs that move into the Setup menu for a user who may open the Setup overview (setup/menu):  
> such a user finds the tab's group there (\[Leftmenu\_setup\] GroupTabs\[\]) and has no tab; a user  
> who may not open the overview (a role that holds only the tab's own policy) keeps the tab.  
> Empty by default, e.g. SetupTabs\[\]=audit in a siteaccess or override menu.ini.append.php.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "SetupTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SetupTabs[]
*/ ?>
```

## MegaMenuTabs

> admin4 (prototype): tabs that get a mega menu, a panel under the header with the groups and  
> links of the tab's menu, opened by a small button beside the tab (the tab stays a link).  
> A tab takes part when its \[Topmenu\_<tab>\] names a MegaMenuTemplate: setup (the Setup menu,  
> \[Leftmenu\_setup\]), users (\[Leftmenu\_user\_mega\]) and dashboard (\[Leftmenu\_my\_mega\]) do; a name without one is  
> ignored (named in the debug report). The  
> panel holds the menu's links with their names, icons (LinkIcons\[\]), descriptions  
> (LinkDescriptions\[\]) and access checks, a short choice of them (MegaMenuLinks\[\] of the menu's  
> section), loaded from user/megamenu/<tab> when it is first opened. A tab name is lower case  
> letters, digits and \_; a tab with a mega menu should be access checked (PolicyList\[\], or its URL under  
> CheckViewAccess). Shown from 576px, where  
> the main menu is one line (not in the phone menu). Empty by default: no button, no script. To switch it on, in a  
> siteaccess or override menu.ini.append.php:  
>   MegaMenuTabs\[\]=setup  
>   MegaMenuTabs\[\]=users  
>   MegaMenuTabs\[\]=dashboard  
> (doc/eng-us/features/6.0/admin4-design.md, "Mega menu")  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "MegaMenuTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MegaMenuTabs[]
*/ ?>
```

## Tabs

> This list contains menuitems of the top menu in admin interface  
> Hidden by default as of 4.3  
> Tabs\[\]=shop  
> Unhidden by default as of 6.0  
> Hidden by default as of 4.3  
> Tabs\[\]=design  
> Unhidden by default as of 6.0.6  
> The audit console (doc/eng-us/bc/6.0/audit.md): shown only with the policy audit/read  

Standard: eine Liste mit 8: `dashboard`, `content`, `media`, `users`, `shop`, `design`, `setup`, `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "Tabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tabs[]
Tabs[]=dashboard
Tabs[]=content
Tabs[]=media
Tabs[]=users
Tabs[]=shop
Tabs[]=design
Tabs[]=setup
Tabs[]=audit
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
