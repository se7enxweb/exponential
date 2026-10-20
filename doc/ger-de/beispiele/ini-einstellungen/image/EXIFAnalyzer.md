---
title: image.ini [EXIFAnalyzer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EXIFAnalyzer] in image.ini: Handler, MIMEList"
keywords: Einstellung, image.ini, EXIFAnalyzer, INI, setting, Handler, MIMEList
type: generated
generated: exp:help:examples
---
# image.ini [EXIFAnalyzer]

Der Block `[EXIFAnalyzer]` von `settings/image.ini`, mit 2 Einstellung(en).

> Analyzer for EXIF data, currently supports JPEG and TIFF  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

Standard: `ezexif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[EXIFAnalyzer]
Handler=ezexif
*/ ?>
```

## MIMEList

Standard: eine Liste mit 2: `image/jpeg`, `image/tiff`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[EXIFAnalyzer]
# ein leeres "MIMEList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MIMEList[]
MIMEList[]=image/jpeg
MIMEList[]=image/tiff
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
