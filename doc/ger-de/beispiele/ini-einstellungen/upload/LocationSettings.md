---
title: upload.ini [LocationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LocationSettings] in upload.ini: ClassList, RootNode, MaxDepth, MaxItems"
keywords: Einstellung, upload.ini, LocationSettings, INI, setting, ClassList, RootNode, MaxDepth, MaxItems
type: generated
generated: exp:help:examples
---
# upload.ini [LocationSettings]

Der Block `[LocationSettings]` von `settings/upload.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ClassList

> A list of class identifiers that should be included in the location dropdown  

Standard: eine Liste mit 1: `folder`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
# ein leeres "ClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassList[]
ClassList[]=folder
*/ ?>
```

## RootNode

> Which root node to fetch locations from  
> Either use a node ID or a node path (e.g. media )  
> - content - The content structure  
> - users   - The user tree  
> - media   - Media tree for images, files and media  

Standard: `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
RootNode=media
*/ ?>
```

## MaxDepth

> The maximum number of levels to look for objects  
> A depth of 1 means direct descendants  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
MaxDepth=3
*/ ?>
```

## MaxItems

> The maxium number of elements in the drop down  
> Setting it too high might slow down the system and make the dropdown hard to use.  
> A number between 10 to 30 is OK.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
MaxItems=30
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
