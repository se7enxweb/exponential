---
title: webdav.ini [GeneralSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GeneralSettings] in webdav.ini: EnableWebDAV, Logging, StartNode, FolderClasses"
keywords: Einstellung, webdav.ini, GeneralSettings, INI, setting, EnableWebDAV, Logging, StartNode, FolderClasses
type: generated
generated: exp:help:examples
---
# webdav.ini [GeneralSettings]

Der Block `[GeneralSettings]` von `settings/webdav.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/webdav.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/webdav.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/webdav.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/webdav.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EnableWebDAV

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/webdav.ini.append.php
[GeneralSettings]
EnableWebDAV=false
*/ ?>
```

## Logging

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/webdav.ini.append.php
[GeneralSettings]
Logging=disabled
*/ ?>
```

## StartNode

> Custom start node for WebDAV operations.  
> By default it is /site/Content (NodeID 2).  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/webdav.ini.append.php
[GeneralSettings]
StartNode=2
*/ ?>
```

## FolderClasses

> List of classes that should always be seen as a folder.  
> By default, if a class contains an image attribute it is seen as an image.  

Standard: eine Liste mit 1: `folder`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/webdav.ini.append.php
[GeneralSettings]
# ein leeres "FolderClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FolderClasses[]
FolderClasses[]=folder
*/ ?>
```

## Siehe auch

- [Alle Blöcke von webdav.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/webdav.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
