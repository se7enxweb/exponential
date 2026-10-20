---
title: ezoe.ini [SystemSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SystemSettings] in ezoe.ini (ezoe): RelativeURL"
keywords: Einstellung, ezoe.ini, SystemSettings, INI, setting, RelativeURL
type: generated
generated: exp:help:examples
---
# ezoe.ini [SystemSettings]

Der Block `[SystemSettings]` von `extension/ezoe/settings/ezoe.ini` (Erweiterung ezoe), mit 1 Einstellung(en).

Bearbeiten Sie `extension/ezoe/settings/ezoe.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezoe.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezoe.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezoe.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RelativeURL

> Relative URLs (ohne Host) im Editor verwenden (betrifft in OE 5.x nur Bilder).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[SystemSettings]
RelativeURL=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezoe.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/settings/ezoe.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
