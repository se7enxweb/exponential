---
title: image.ini [OutputSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OutputSettings] in image.ini: AllowedOutputFormat"
keywords: Einstellung, image.ini, OutputSettings, INI, setting, AllowedOutputFormat
type: generated
generated: exp:help:examples
---
# image.ini [OutputSettings]

Der Block `[OutputSettings]` von `settings/image.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AllowedOutputFormat

> Eine Liste von MIME-Typen, die als Ausgabetyp erlaubt sind  
> Dies bestimmt, welche Formate Ihre Webseite anzeigen soll  

Standard: eine Liste mit 4: `image/jpeg`, `image/png`, `image/webp`, `image/gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[OutputSettings]
# ein leeres "AllowedOutputFormat[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedOutputFormat[]
AllowedOutputFormat[]=image/jpeg
AllowedOutputFormat[]=image/png
AllowedOutputFormat[]=image/webp
AllowedOutputFormat[]=image/gif
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
