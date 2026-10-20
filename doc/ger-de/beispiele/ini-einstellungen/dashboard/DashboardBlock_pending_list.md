---
title: dashboard.ini [DashboardBlock_pending_list]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardBlock_pending_list] in dashboard.ini: Priority, NumberOfItems, PolicyList, ViewList"
keywords: Einstellung, dashboard.ini, DashboardBlock_pending_list, INI, setting, Priority, NumberOfItems, PolicyList, ViewList
type: generated
generated: exp:help:examples
---
# dashboard.ini [DashboardBlock_pending_list]

Der Block `[DashboardBlock_pending_list]` von `settings/dashboard.ini`, mit 4 Einstellung(en).

> DashboardBlocks\[\]=community\_activity  
> DashboardBlocks\[\]=wishlist  
> \[DashboardBlock\_example\]  
> By default system will load /dashboard/\[block\_identifier\].tpl template  
> e.g if INI group is called \[Dashboard\_example\] then system will load /dashboard/example.tpl file  
> If Template variable is specified then it will be used instead of default location  
> Template=example.tpl  
> Priority determines the order in which the blocks will be rendered  
> Priority=10  
> List of policies, either in "<node\_id>" or "<module>/<function>" form  
> In case of node id, content/read is checked!  
> The block is left out unless the user has every one of them.  
> PolicyList\[\]  
> The addresses the block is about or links to, "<module>/<view>\[/<parameters>\]" or a URL alias.  
> The block is left out unless the user can open every one of them, checked the way the kernel  
> checks the request (expViewAccess: the view's policy functions with their limitations, a module  
> without functions as a whole, the siteaccess rules, the user/login SiteAccess limitation, and the  
> node or object of content/view and content/edit). Use it for what PolicyList cannot say: a view  
> whose functions are "a or b", a view without functions, an extension's module.  
> ViewList\[\]  
> Controls how many items are fetched and displayed in the dashboard block  
> Value available as a {$block.number\_of\_items} variable  
> NumberOfItems=10  

Bearbeiten Sie `settings/dashboard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dashboard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dashboard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dashboard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Priority

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_pending_list]
Priority=10
*/ ?>
```

## NumberOfItems

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_pending_list]
NumberOfItems=10
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `content/pendinglist`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_pending_list]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=content/pendinglist
*/ ?>
```

## ViewList

Standard: eine Liste mit 1: `content/pendinglist`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_pending_list]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=content/pendinglist
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dashboard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dashboard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
