---
title: upload.ini [BugFileUpload]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BugFileUpload] in upload.ini: NavigationPartIdentifier"
keywords: Einstellung, upload.ini, BugFileUpload, INI, setting, NavigationPartIdentifier
type: generated
generated: exp:help:examples
---
# upload.ini [BugFileUpload]

Der Block `[BugFileUpload]` von `settings/upload.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## NavigationPartIdentifier

Standard: `ezsetupnavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[BugFileUpload]
NavigationPartIdentifier=ezsetupnavigationpart
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
