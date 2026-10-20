---
title: contentstructuremenu.ini [TreeMenu]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TreeMenu] in contentstructuremenu.ini: Dynamic, UseCache, ShowClasses, RootNodeID, MaxDepth, MaxNodes, SortBy, ToolTips ..."
keywords: Einstellung, contentstructuremenu.ini, TreeMenu, INI, setting, Dynamic, UseCache, ShowClasses, RootNodeID, MaxDepth, MaxNodes, SortBy, ToolTips, ItemClickAction, ClassIconsSize, PreloadClassIcons, AutoopenCurrentNode, MenuPersistence, ChapterClasses, CreateHereMenu
type: generated
generated: exp:help:examples
---
# contentstructuremenu.ini [TreeMenu]

Der Block `[TreeMenu]` von `settings/contentstructuremenu.ini`, mit 15 Einstellung(en).

Bearbeiten Sie `settings/contentstructuremenu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentstructuremenu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentstructuremenu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentstructuremenu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Dynamic

> If set to enabled, the admin tree menu is fetched and built dynamically on  
> fly. Requires a web browser with AJAX support.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
Dynamic=disabled
*/ ?>
```

## UseCache

> If set to enabled, the JSON documents will be also cached on the server.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
UseCache=enabled
*/ ?>
```

## ShowClasses

> which classes to show  

Standard: eine Liste mit 4: `folder`, `forum`, `gallery`, `user_group`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
# ein leeres "ShowClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ShowClasses[]
ShowClasses[]=folder
ShowClasses[]=forum
ShowClasses[]=gallery
ShowClasses[]=user_group
*/ ?>
```

## RootNodeID

> ShowClasses\[\]=foo\_folder  
> Root node of the content structure menu (the left tree of the Content  
> structure tab in the administration interface).  
> "1" is the real top of the content tree, the virtual node every top-level  
> subtree hangs from: Content (2), Users (5), Media (43), Setup (48), Design  
> (58) and any other top-level node are then shown as its children, so the  
> whole tree can be browsed from one place. Node 1 has no object; it is  
> labelled "Top Level Nodes" (translated) and clicking it opens the dashboard.  
> Set it to 2 to start the menu at the Content node, as older releases did,  
> or to any other node ID to start it there.  
> The Media and Users tabs keep their own trees, rooted at  
> \[NodeSettings\] MediaRootNode and UserRootNode in content.ini.  
> RootNodeID=118  
> RootNodeID=2  

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
RootNodeID=1
*/ ?>
```

## MaxDepth

> Maximum number of levels shown below the root node of the menu.  
> "0" means unlimited: every level of the tree can be opened, down to the  
> deepest node.  
> "1" shows only the children of the root node, "2" also their children, and  
> so on. A node at the last allowed level is shown without the open/close  
> control.  
> With 'Dynamic' enabled the levels are loaded one at a time when a node is  
> opened, so an unlimited depth does not make the menu slower; MaxNodes still  
> limits how many children one level lists.  
> The depth is counted from the root of each tree, so it applies in the same  
> way to the Media and Users trees.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
MaxDepth=0
*/ ?>
```

## MaxNodes

> max nodes count to fetch  
> "0" means unlimited  

Standard: `150`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
MaxNodes=150
*/ ?>
```

## SortBy

> the value is a pair of "type/order".  
> for example:  published/ascending,  name/descending, ...  
> using "array" notation it's possible to sort by multiple sort  
> criteria:  
>    SortBy\[\]  
>    SortBy\[\]=name/ascending  
>    SortBy\[\]=published/descending  
> if an empty array, it uses the sorting defined for each node instead (from the Ordering tab)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
# ein leeres "SortBy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SortBy[]
*/ ?>
```

## ToolTips

> enabled/disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
ToolTips=enabled
*/ ?>
```

## ItemClickAction

> url to redirect when clicking on menu item  
> if empty then default action(viewing of object) will be performed  
> example: /content/browse, /content/view/full, etc.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
ItemClickAction=
*/ ?>
```

## ClassIconsSize

> Size of icons in menu: small, normal, large, etc.  
> Check 'Sizes' setting in icon.ini for available sizes  

Standard: `small`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
ClassIconsSize=small
*/ ?>
```

## PreloadClassIcons

> Controls the pre-loading of class-icons, use  
> enabled or disabled. When pre-loading is enabled  
> icons of all classes will be loaded before  
> menu will be shown. If disabled icons will be loaded  
> on demand.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
PreloadClassIcons=enabled
*/ ?>
```

## AutoopenCurrentNode

> Use enabled or disabled. If enabled then current node  
> will be unfolded( children will be shown )  
> automatically.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
AutoopenCurrentNode=enabled
*/ ?>
```

## MenuPersistence

> Use cookies to remember opened paths and keep then  
> open until closed by user.  
> If disabled only current path will be opened.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
MenuPersistence=enabled
*/ ?>
```

## ChapterClasses

> which classes are chapter  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
# ein leeres "ChapterClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ChapterClasses[]
*/ ?>
```

## CreateHereMenu

> ChapterClasses\[\]=chapter  
> ChapterClasses\[\]=lighttoc  
> The "Create here" menu when clicking the node icon in the administrator interface.  
>   disabled   - Not having the menu.  
>   full       - Having the menu with the necessary permission checks - this will most likely slow down the interface.  
>   simplified - Having the menu without the necessary permission checks  
>                (in other words: the menu will include all classes regardless  
>                 if the logged in user is allowed to create all those classes at that exact location).  
>                Some users might get ACCESS DENIED, but it might just work for the most typical/usual cases  
>                (where only one admin or a bunch of admins without limitations are using the interface).  

Standard: `simplified`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
CreateHereMenu=simplified
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentstructuremenu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentstructuremenu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
