---
title: binaryfile.ini [PDFHandlerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PDFHandlerSettings] in binaryfile.ini: TextExtractionTool"
keywords: Einstellung, binaryfile.ini, PDFHandlerSettings, INI, setting, TextExtractionTool
type: generated
generated: exp:help:examples
---
# binaryfile.ini [PDFHandlerSettings]

Der Block `[PDFHandlerSettings]` von `settings/binaryfile.ini`, mit 1 Einstellung(en).

> The path to the text extraction tool to use to  
> fetch the information in PDF files  

Bearbeiten Sie `settings/binaryfile.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/binaryfile.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/binaryfile.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/binaryfile.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TextExtractionTool

Standard: `pstotext`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/binaryfile.ini.append.php
[PDFHandlerSettings]
TextExtractionTool=pstotext
*/ ?>
```

## Siehe auch

- [Alle Blöcke von binaryfile.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/binaryfile.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
