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

> Wenn auf enabled gesetzt, wird das Baummenü der Administration dynamisch  
> zur Laufzeit geholt und aufgebaut. Erfordert einen Webbrowser mit AJAX-Unterstützung.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
Dynamic=disabled
*/ ?>
```

## UseCache

> Wenn auf enabled gesetzt, werden die JSON-Dokumente auch auf dem Server gecacht.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
UseCache=enabled
*/ ?>
```

## ShowClasses

> welche Klassen angezeigt werden  

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
> Wurzelknoten des Menüs der Inhaltsstruktur (der linke Baum des Reiters  
> Inhaltsstruktur in der Administrationsoberfläche).  
> "1" ist die eigentliche Spitze des Inhaltsbaums, der virtuelle Knoten, an dem jeder Teilbaum der  
> obersten Ebene hängt: Inhalte (2), Benutzer (5), Medien (43), Setup (48), Design  
> (58) und jeder andere Knoten der obersten Ebene erscheinen dann als seine Unterknoten, sodass sich der  
> ganze Baum von einer Stelle aus durchsuchen lässt. Knoten 1 hat kein Objekt; er ist  
> mit „Knoten der obersten Ebene“ (übersetzt) beschriftet, und ein Klick darauf öffnet das Dashboard.  
> Setzen Sie ihn auf 2, um das Menü wie in älteren Releases beim Knoten Inhalte zu beginnen,  
> oder auf eine andere Knoten-ID, um es dort zu beginnen.  
> Die Reiter Medien und Benutzer behalten ihre eigenen Bäume, mit den Wurzeln  
> \[NodeSettings\] MediaRootNode und UserRootNode in content.ini.  
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

> Höchstzahl der unter dem Wurzelknoten des Menüs angezeigten Ebenen.  
> "0" bedeutet unbegrenzt: Jede Ebene des Baums lässt sich öffnen, bis zum  
> tiefsten Knoten.  
> "1" zeigt nur die Unterknoten des Wurzelknotens, "2" auch deren Unterknoten und  
> so weiter. Ein Knoten auf der letzten erlaubten Ebene wird ohne das Element zum  
> Öffnen/Schließen angezeigt.  
> Mit aktiviertem 'Dynamic' werden die Ebenen einzeln geladen, wenn ein Knoten  
> geöffnet wird, eine unbegrenzte Tiefe macht das Menü also nicht langsamer; MaxNodes begrenzt weiterhin,  
> wie viele Unterknoten eine Ebene auflistet.  
> Die Tiefe wird ab der Wurzel jedes Baums gezählt, sie gilt also ebenso  
> für die Bäume Medien und Benutzer.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
MaxDepth=0
*/ ?>
```

## MaxNodes

> Höchstzahl der zu holenden Knoten  
> "0" bedeutet unbegrenzt  

Standard: `150`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
MaxNodes=150
*/ ?>
```

## SortBy

> der Wert ist ein Paar "type/order".  
> zum Beispiel:  published/ascending,  name/descending, ...  
> mit der "array"-Schreibweise lässt sich nach mehreren Kriterien  
> sortieren:  
>    SortBy\[\]  
>    SortBy\[\]=name/ascending  
>    SortBy\[\]=published/descending  
> bei einem leeren Array wird stattdessen die für jeden Knoten festgelegte Sortierung verwendet (aus dem Reiter Sortierung)  

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

> URL, auf die beim Klick auf einen Menüeintrag weitergeleitet wird  
> wenn leer, wird die Standardaktion (Ansicht des Objekts) ausgeführt  
> Beispiel: /content/browse, /content/view/full usw.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
ItemClickAction=
*/ ?>
```

## ClassIconsSize

> Größe der Symbole im Menü: small, normal, large usw.  
> Die verfügbaren Größen stehen in der Einstellung 'Sizes' in icon.ini  

Standard: `small`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
ClassIconsSize=small
*/ ?>
```

## PreloadClassIcons

> Steuert das Vorladen der Klassensymbole, verwenden Sie  
> enabled oder disabled. Wenn das Vorladen aktiviert ist,  
> werden die Symbole aller Klassen geladen, bevor  
> das Menü angezeigt wird. Wenn deaktiviert, werden die Symbole  
> bei Bedarf geladen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
PreloadClassIcons=enabled
*/ ?>
```

## AutoopenCurrentNode

> Verwenden Sie enabled oder disabled. Wenn enabled, wird der aktuelle Knoten  
> automatisch aufgeklappt (seine Unterknoten werden  
> angezeigt).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
AutoopenCurrentNode=enabled
*/ ?>
```

## MenuPersistence

> Cookies verwenden, um geöffnete Pfade zu merken und sie  
> geöffnet zu halten, bis der Benutzer sie schließt.  
> Wenn deaktiviert, wird nur der aktuelle Pfad geöffnet.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
MenuPersistence=enabled
*/ ?>
```

## ChapterClasses

> welche Klassen Kapitel sind  

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
> Das Menü „Hier anlegen“ beim Klick auf das Knotensymbol in der Administrationsoberfläche.  
>   disabled   - kein Menü.  
>   full       - das Menü mit den nötigen Berechtigungsprüfungen; das verlangsamt die Oberfläche wahrscheinlich.  
>   simplified - das Menü ohne die nötigen Berechtigungsprüfungen  
>                (das heißt: Das Menü enthält alle Klassen, unabhängig davon,  
>                 ob der angemeldete Benutzer alle diese Klassen an genau dieser Stelle anlegen darf).  
>                Manche Benutzer erhalten dann ZUGRIFF VERWEIGERT, aber für die häufigsten Fälle kann es genügen  
>                (wenn nur ein Administrator oder einige Administratoren ohne Einschränkungen die Oberfläche verwenden).  

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
