---
title: contentcheck.ini [UnreferencedSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UnreferencedSettings] in contentcheck.ini: AssetClassList, StorageParentClassList, MaxTrashBatch, IndexTTL, BuildOnRequest, ScanChunkSize, CandidateLimit, ObjectColumns ..."
keywords: Einstellung, contentcheck.ini, UnreferencedSettings, INI, setting, AssetClassList, StorageParentClassList, MaxTrashBatch, IndexTTL, BuildOnRequest, ScanChunkSize, CandidateLimit, ObjectColumns, NodeColumns, NodeListColumns
type: generated
generated: exp:help:examples
---
# contentcheck.ini [UnreferencedSettings]

Der Block `[UnreferencedSettings]` von `settings/contentcheck.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/contentcheck.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentcheck.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AssetClassList

> Die Klassen, die die Liste abdeckt: Assets, die nur zählen, wenn etwas sie verwendet. Eine Seite, ein  
> Ordner, ein Benutzer oder ein Einstellungsobjekt wird über den Baum erreicht (Navigation, Menüs,  
> Suche), ob etwas darauf verlinkt oder nicht, und gilt daher nie als nicht referenziert.  

Standard: eine Liste mit 8: `image`, `file`, `video`, `audio`, `flash`, `quicktime`, `windows_media`, `real_video`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# ein leeres "AssetClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AssetClassList[]
AssetClassList[]=image
AssetClassList[]=file
AssetClassList[]=video
AssetClassList[]=audio
AssetClassList[]=flash
AssetClassList[]=quicktime
AssetClassList[]=windows_media
AssetClassList[]=real_video
*/ ?>
```

## StorageParentClassList

> Ein Asset wird nur aufgeführt, wenn der Elternknoten jeder Platzierung zu einer dieser Klassen gehört (ein Ordner,  
> der es nur aufbewahrt). Unter jedem anderen Elternknoten (eine Galerie, ein Artikel, eine Veranstaltung) zeigt es das  
> Template des Elternknotens, es gilt also als verwendet.  

Standard: eine Liste mit 1: `folder`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# ein leeres "StorageParentClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
StorageParentClassList[]
StorageParentClassList[]=folder
*/ ?>
```

## MaxTrashBatch

> Höchstens so viele Objekte werden in einer Bestätigung in den Papierkorb verschoben (1 bis 200)  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
MaxTrashBatch=50
*/ ?>
```

## IndexTTL

> Der Referenzindex: jede ID, Remote-ID und jeder Pfad, den Felder, Links, Layouts, Einstellungen,  
> Templates, Rollen und die Tabellen unten verwenden. Er wird so viele Sekunden behalten (0 bis 604800).  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
IndexTTL=600
*/ ?>
```

## BuildOnRequest

> true: Eine Seite baut den Index, wenn er älter als IndexTTL ist (das Verschieben in den Papierkorb baut  
> ihn immer). false (sehr große Installationen): nur bin/php/contentcheck.php --build-index  
> oder der Cronjob-Teil cronjobs/contentcheck.php baut ihn; die Liste zeigt dann den letzten  
> Index, und das Verschieben in den Papierkorb braucht einen, der jünger als IndexTTL ist.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
BuildOnRequest=true
*/ ?>
```

## ScanChunkSize

> Pro Abfrage gelesene Felder beim Aufbau des Index (100 bis 20000)  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
ScanChunkSize=2000
*/ ?>
```

## CandidateLimit

> Höchstens so viele Kandidaten (Assets unter dem Knoten) werden für eine Liste beurteilt (100 bis  
> 500000); bei mehr bittet die Liste um einen kleineren Teilbaum oder weniger Klassen  

Standard: `20000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
CandidateLimit=20000
*/ ?>
```

## ObjectColumns

> Tabellenspalten, die eine Objekt-ID, eine Knoten-ID oder eine Liste oder einen Pfad von Knoten-IDs enthalten. Eine Zeile dort  
> bewahrt das Objekt oder den Knoten davor, als nicht referenziert zu gelten. Eine Tabelle oder Spalte, die nicht  
> existiert, wird übersprungen, die Tabellen von Erweiterungen können also hier aufgeführt werden.  
> der Shop: Warenkörbe, Wunschlisten und Bestellungen enthalten Produktsammlungen  
> gesammelte Informationen (Formulare) gehören zu ihrem Objekt  
> Indizierung und andere ausstehende Aktionen  
> Rollenzuweisungen an Benutzer und Gruppen  
> der Newsletter: Artikel von Ausgaben, Versände, verfolgte Links, Zeitpläne, Freigaben, Listen  

Standard: eine Liste mit 14: `ezproductcollection_item.contentobject_id`, `ezinfocollection.contentobject_id`, `ezpending_actions.param`, `ezuser_role.contentobject_id`, `cjwnl_edition_article.contentobject_id`, `cjwnl_edition_send.edition_contentobject_id`, `cjwnl_edition_send.list_contentobject_id`, `cjwnl_link.contentobject_id`, `cjwnl_schedule.template_edition_contentobject_id`, `cjwnl_schedule.list_contentobject_id`, `cjwnl_approval.edition_contentobject_id`, `cjwnl_approval.list_contentobject_id` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# ein leeres "ObjectColumns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ObjectColumns[]
ObjectColumns[]=ezproductcollection_item.contentobject_id
ObjectColumns[]=ezinfocollection.contentobject_id
ObjectColumns[]=ezpending_actions.param
ObjectColumns[]=ezuser_role.contentobject_id
ObjectColumns[]=cjwnl_edition_article.contentobject_id
ObjectColumns[]=cjwnl_edition_send.edition_contentobject_id
ObjectColumns[]=cjwnl_edition_send.list_contentobject_id
ObjectColumns[]=cjwnl_link.contentobject_id
ObjectColumns[]=cjwnl_schedule.template_edition_contentobject_id
ObjectColumns[]=cjwnl_schedule.list_contentobject_id
ObjectColumns[]=cjwnl_approval.edition_contentobject_id
ObjectColumns[]=cjwnl_approval.list_contentobject_id
ObjectColumns[]=cjwnl_subscription.list_contentobject_id
ObjectColumns[]=cjwnl_article_pool.list_contentobject_id
*/ ?>
```

## NodeColumns

> Teilbaum-Benachrichtigungen und Lesezeichen  
> wo Entwürfe und ausstehende Versionen platziert werden  

Standard: eine Liste mit 3: `ezsubtree_notification_rule.node_id`, `ezcontentbrowsebookmark.node_id`, `eznode_assignment.parent_node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# ein leeres "NodeColumns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NodeColumns[]
NodeColumns[]=ezsubtree_notification_rule.node_id
NodeColumns[]=ezcontentbrowsebookmark.node_id
NodeColumns[]=eznode_assignment.parent_node
*/ ?>
```

## NodeListColumns

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# ein leeres "NodeListColumns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NodeListColumns[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentcheck.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
