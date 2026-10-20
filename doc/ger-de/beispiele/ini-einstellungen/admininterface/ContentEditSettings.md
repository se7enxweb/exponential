---
title: admininterface.ini [ContentEditSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentEditSettings] in admininterface.ini: LeftMenuParts"
keywords: Einstellung, admininterface.ini, ContentEditSettings, INI, setting, LeftMenuParts
type: generated
generated: exp:help:examples
---
# admininterface.ini [ContentEditSettings]

Der Block `[ContentEditSettings]` von `settings/admininterface.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LeftMenuParts

> Templates to include in the left menu of content/edit  
> for instance  
> LeftMenuParts\[\]=content/parts/advanced.tpl  
> will include "design:content/parts/advanced.tpl"  
> from design/admin/templates/content/edit\_menu.tpl  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[ContentEditSettings]
# ein leeres "LeftMenuParts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LeftMenuParts[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
