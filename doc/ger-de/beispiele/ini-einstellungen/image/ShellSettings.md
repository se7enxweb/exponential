---
title: image.ini [ShellSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ShellSettings] in image.ini: ConvertPath"
keywords: Einstellung, image.ini, ShellSettings, INI, setting, ConvertPath
type: generated
generated: exp:help:examples
---
# image.ini [ShellSettings]

Der Block `[ShellSettings]` von `settings/image.ini`, mit 1 Einstellung(en).

> -------- Einstellungen der Bildkonverter --------  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ConvertPath

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ShellSettings]
ConvertPath=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
