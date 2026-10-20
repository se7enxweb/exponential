---
title: content.ini [RelationGroupSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RelationGroupSettings] in content.ini: Groups, DefaultGroup, ImagesClassList, FilesClassList"
keywords: Einstellung, content.ini, RelationGroupSettings, INI, setting, Groups, DefaultGroup, ImagesClassList, FilesClassList
type: generated
generated: exp:help:examples
---
# content.ini [RelationGroupSettings]

Der Block `[RelationGroupSettings]` von `settings/content.ini`, mit 4 Einstellung(en).

> ClassSpecificAssignment\[\]=image;33,37;37  
> ClassSpecificAssignment\[\]=match;node1,node2;main\_node  

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Groups

> A list of defined relation groups, each entry  
> will have a class definition setting suffixed with ClassList  
> e.g. a group called images would have the setting ImagesClassList  

Standard: eine Liste mit 2: `images`, `files`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationGroupSettings]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=images
Groups[]=files
*/ ?>
```

## DefaultGroup

> The group were all non-match classes are placed  
> this is usually related folders and articles  

Standard: `objects`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationGroupSettings]
DefaultGroup=objects
*/ ?>
```

## ImagesClassList

> A list of class identifiers that should be placed in the  
> related image group  

Standard: eine Liste mit 1: `image`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationGroupSettings]
# ein leeres "ImagesClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImagesClassList[]
ImagesClassList[]=image
*/ ?>
```

## FilesClassList

> A list of class identifiers that should be placed in the  
> related files group  

Standard: eine Liste mit 1: `file`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationGroupSettings]
# ein leeres "FilesClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FilesClassList[]
FilesClassList[]=file
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
