---
title: file.ini [PassThroughSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PassThroughSettings] in file.ini: DefaultContentDisposition, ContentDisposition"
keywords: Einstellung, file.ini, PassThroughSettings, INI, setting, DefaultContentDisposition, ContentDisposition
type: generated
generated: exp:help:examples
---
# file.ini [PassThroughSettings]

Der Block `[PassThroughSettings]` von `settings/file.ini`, mit 2 Einstellung(en).

> Optional DB table name caches should be stored to  
> This is useful for staging use cases where the storage entries and DB are shared  
> across different Exponential installations, but the caches should not.  
> MetaDataTableNameCache=  

Bearbeiten Sie `settings/file.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/file.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/file.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/file.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultContentDisposition

> The default value for mime types not specified using ContentDisposition  
> is "attachment". Possible values are attachment or inline.  

Standard: `attachment`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[PassThroughSettings]
DefaultContentDisposition=attachment
*/ ?>
```

## ContentDisposition

Standard: ein Hash mit 4: `application/x-shockwave-flash=inline`, `video/ogg=inline`, `video/mp4=inline`, `video/webm=inline`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[PassThroughSettings]
# ein leeres "ContentDisposition[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ContentDisposition[]
ContentDisposition[application/x-shockwave-flash]=inline
ContentDisposition[video/ogg]=inline
ContentDisposition[video/mp4]=inline
ContentDisposition[video/webm]=inline
*/ ?>
```

## Siehe auch

- [Alle Blöcke von file.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
