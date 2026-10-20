---
title: menu.ini [Topmenu_content]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_content] in menu.ini: Icon, URL, NavigationPartIdentifier, Enabled, Shown, PolicyList"
keywords: Einstellung, menu.ini, Topmenu_content, INI, setting, Icon, URL, NavigationPartIdentifier, Enabled, Shown, PolicyList
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_content]

Der Block `[Topmenu_content]` von `settings/menu.ini`, mit 6 Einstellung(en).

> Example of adding a custom topmenu entry  
> See the commented Topmenu\_custom entry at the bottom  
> Tabs\[\]=custom  
> Here are the settings which controls behavour of each  
> menuitem in top admin menu.  
> URL\['ui\_context'\] - which view item points to in different contextes.  
> NavigationPartIdentifier  - Navigation part identifier of the menu item.  
> Name - name which is shown on the menu item, if it is not set the default value  
>        is used. Write it in English: it is translated like any other interface  
>        text, in the context TranslationContext names, and shown as it is when  
>        the current language has no translation for it.  
> Tooltip - tooltip which is shown for the menu item, if it is not set the default value  
>           is used. Translated the same way as Name.  
> TranslationContext - (optional) the translation context Name and Tooltip are  
>        looked up in, design/admin/pagelayout (the context of the default tab  
>        names) when it is not set. An extension that adds a tab puts the  
>        translations of its Name and Tooltip into its own translation.ts under  
>        this context.  
> Icon - (optional) the name of an icon admin4 draws before the tab's name: a name of design.ini  
>        \[AdminDesignSettings\] IconList\[\] (Bootstrap Icons); a name it does not know draws no icon.  
> Enabled\[ui\_context\] - sets if menuitem is clickable in current ui\_context.  
> Shown\[ui\_context\]  - sets if menuitem is clickable in current ui\_context  
> PolicyList\[\] - list of policies, either in "<node\_id>" or "<module>/<function>" form; a module without  
>                policy functions (visual, class, role, workflow ...) is "<module>/\*"  
>                In case of node id, content/read is checked!  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `diagram-3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
Icon=diagram-3
*/ ?>
```

## URL

Standard: ein Hash mit 2: `default=content/view/full/2`, `browse=content/browse/2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=content/view/full/2
URL[browse]=content/browse/2
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezcontentnavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
NavigationPartIdentifier=ezcontentnavigationpart
*/ ?>
```

## Enabled

> Name=Content structure  
> Tooltip=Manage the main content structure of the site.  

Standard: ein Hash mit 3: `default=true`, `browse=true`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
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
[Topmenu_content]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_content]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
