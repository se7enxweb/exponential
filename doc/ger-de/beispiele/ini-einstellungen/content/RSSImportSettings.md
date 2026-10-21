---
title: content.ini [RSSImportSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RSSImportSettings] in content.ini: DraftTimeout"
keywords: Einstellung, content.ini, RSSImportSettings, INI, setting, DraftTimeout
type: generated
generated: exp:help:examples
---
# content.ini [RSSImportSettings]

Der Block `[RSSImportSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DraftTimeout

Standard: `7200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportSettings]
DraftTimeout=7200
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
