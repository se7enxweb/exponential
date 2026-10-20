---
title: content.ini [RelationAssignmentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RelationAssignmentSettings] in content.ini: DefaultAssignment, ClassSpecificAssignment"
keywords: Einstellung, content.ini, RelationAssignmentSettings, INI, setting, DefaultAssignment, ClassSpecificAssignment
type: generated
generated: exp:help:examples
---
# content.ini [RelationAssignmentSettings]

Der Block `[RelationAssignmentSettings]` von `settings/content.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultAssignment

> Default assignment for new related objects, possible values are  
> - A Node ID - Place all new objects under this node  
> - A node path - The placement of the parent node by a path, e.g. media/files  
> - root - The root of the tree  
> - users - The root of the user tree  
> - media - The root of the media tree  
> - none - DEPRECATED  
>
> Multiple assignments are not allowed.  

Standard: `root`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationAssignmentSettings]
DefaultAssignment=root
*/ ?>
```

## ClassSpecificAssignment

> Automatic assignments for specific classes.  
>
> Each line consists of three or two values separated by semi-colon.  
> First two values are lists of elements which are comma separated.  
>
> First value is the class identifier, second is the assignment (see above for  
> values). An optional third is the main node in case you upload to many  
> locations. If the third parameter is not set, then the first element of  
> the value 2 will be used as main node.  
>
> Classes are either specified with identifier, ID or group\_ID  
> The items are searched in order so the first to match will be used,  
> this means that class IDs should come before class group IDs.  
>
> If the same class is specified in many lines, then will be used the first  
> match where user has an access to all listed nodes.  

Standard: eine Liste mit 8: `user,user_group;users/guest_accounts`, `image;media/images`, `video;media/multimedia`, `file;media/files`, `quicktime;media/multimedia`, `windows_media;media/multimedia`, `real_video;media/multimedia`, `flash;media/multimedia`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationAssignmentSettings]
# ein leeres "ClassSpecificAssignment[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassSpecificAssignment[]
ClassSpecificAssignment[]=user,user_group;users/guest_accounts
ClassSpecificAssignment[]=image;media/images
ClassSpecificAssignment[]=video;media/multimedia
ClassSpecificAssignment[]=file;media/files
ClassSpecificAssignment[]=quicktime;media/multimedia
ClassSpecificAssignment[]=windows_media;media/multimedia
ClassSpecificAssignment[]=real_video;media/multimedia
ClassSpecificAssignment[]=flash;media/multimedia
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
