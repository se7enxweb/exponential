---
title: texttoimage.ini [ImageSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ImageSettings] in texttoimage.ini: UseCache"
keywords: Einstellung, texttoimage.ini, ImageSettings, INI, setting, UseCache
type: generated
generated: exp:help:examples
---
# texttoimage.ini [ImageSettings]

Der Block `[ImageSettings]` von `settings/texttoimage.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## UseCache

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[ImageSettings]
UseCache=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
