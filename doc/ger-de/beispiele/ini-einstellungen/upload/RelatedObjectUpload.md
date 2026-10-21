---
title: upload.ini [RelatedObjectUpload]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RelatedObjectUpload] in upload.ini: ReturnType"
keywords: Einstellung, upload.ini, RelatedObjectUpload, INI, setting, ReturnType
type: generated
generated: exp:help:examples
---
# upload.ini [RelatedObjectUpload]

Der Block `[RelatedObjectUpload]` von `settings/upload.ini`, mit 1 Einstellung(en).

> Beispiel einer Aktion; die Gruppe heißt genauso wie die Aktion  
> \[ExampleAction\]  
> # The navigation part to use for the page (optional)  
> NavigationPartIdentifier=ezcontentnavigationpart  
> # The type of the return values, possible values are:  
> #   NodeID - Returns the node ids of the selected items  
> #   ObjectID - Returns the object ids of the selected items  
> ReturnType=NodeID  
> # An array with nodes where the object should be placed  (optional)  
> # Each entry is either an alias or the node ID.  
> # Aliases can be defined in the AliasList variable at the top of this file  
> # or one of the system defined aliases can be used.  
> # The system defined aliases are:  
> # content, users, media, setup  
> ParentNodes\[\]  
> ParentNodes\[\]=media  
> Aktion beim Hochladen einer neuen Datei als verknüpftes Objekt  

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ReturnType

Standard: `ObjectID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[RelatedObjectUpload]
ReturnType=ObjectID
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
