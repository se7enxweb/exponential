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
> Standardmäßig lädt das System das Template /dashboard/\[block\_identifier\].tpl  
> z. B. lädt das System bei einer INI-Gruppe \[Dashboard\_example\] die Datei /dashboard/example.tpl  
> Ist die Variable Template angegeben, wird sie statt des Standardorts verwendet  
> Template=example.tpl  
> Priority bestimmt die Reihenfolge, in der die Blöcke gerendert werden  
> Priority=10  
> Liste der Richtlinien, in der Form "<node\_id>" oder "<module>/<function>"  
> Bei einer Knoten-ID wird content/read geprüft!  
> Der Block entfällt, außer der Benutzer hat jede davon.  
> PolicyList\[\]  
> Die Adressen, um die es im Block geht oder auf die er verlinkt, "<module>/<view>\[/<parameters>\]" oder ein URL-Alias.  
> Der Block entfällt, außer der Benutzer kann jede davon öffnen, geprüft wie der Kernel  
> die Anfrage prüft (expViewAccess: die Richtlinienfunktionen der Ansicht mit ihren Einschränkungen, ein Modul  
> ohne Funktionen als Ganzes, die Siteaccess-Regeln, die Einschränkung SiteAccess von user/login und der  
> Knoten oder das Objekt von content/view und content/edit). Verwenden Sie es für das, was PolicyList nicht sagen kann: eine Ansicht,  
> deren Funktionen „a oder b“ sind, eine Ansicht ohne Funktionen, das Modul einer Erweiterung.  
> ViewList\[\]  
> Steuert, wie viele Einträge im Dashboard-Block geholt und angezeigt werden  
> Wert verfügbar als Variable {$block.number\_of\_items}  
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
