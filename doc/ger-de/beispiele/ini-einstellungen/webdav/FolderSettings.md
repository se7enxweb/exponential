---
title: webdav.ini [FolderSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FolderSettings] in webdav.ini: FolderClass"
keywords: Einstellung, webdav.ini, FolderSettings, INI, setting, FolderClass
type: generated
generated: exp:help:examples
---
# webdav.ini [FolderSettings]

Der Block `[FolderSettings]` von `settings/webdav.ini`, mit 1 Einstellung(en).

> FolderClasses\[\]=article  
> FolderClasses\[\]=your\_custom\_class  
> VERALTET: WebDAV verwendet die Einstellungen aus upload.ini  
>
> Diese Einstellung verrät die ID der Ordnerklasse.  
> Außerdem bestimmt sie, welches  
> Attribut beim Anlegen, Benennen und Umbenennen des Ordners verwendet wird.  

Bearbeiten Sie `settings/webdav.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/webdav.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/webdav.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/webdav.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FolderClass

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/webdav.ini.append.php
[FolderSettings]
FolderClass=1
*/ ?>
```

## Siehe auch

- [Alle Blöcke von webdav.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/webdav.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
