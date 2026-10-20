---
title: upload.ini [UploadSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UploadSettings] in upload.ini: DefaultReturnType, AliasList"
keywords: Einstellung, upload.ini, UploadSettings, INI, setting, DefaultReturnType, AliasList
type: generated
generated: exp:help:examples
---
# upload.ini [UploadSettings]

Der Block `[UploadSettings]` von `settings/upload.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultReturnType

Standard: `NodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[UploadSettings]
DefaultReturnType=NodeID
*/ ?>
```

## AliasList

> A map of node aliases to node ids, here it's possible to define custom alias names  
> It maps from the alias name to a node ID  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[UploadSettings]
# ein leeres "AliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
