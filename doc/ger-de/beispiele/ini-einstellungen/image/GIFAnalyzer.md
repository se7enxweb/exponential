---
title: image.ini [GIFAnalyzer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GIFAnalyzer] in image.ini: Handler, MIMEList"
keywords: Einstellung, image.ini, GIFAnalyzer, INI, setting, Handler, MIMEList
type: generated
generated: exp:help:examples
---
# image.ini [GIFAnalyzer]

Der Block `[GIFAnalyzer]` von `settings/image.ini`, mit 2 Einstellung(en).

> GIF-Analysator, ermittelt Details zu GIF-Bildern  
> Wird vor allem verwendet, um GIF-Animationen zu erkennen.  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

Standard: `ezgif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[GIFAnalyzer]
Handler=ezgif
*/ ?>
```

## MIMEList

Standard: eine Liste mit 1: `image/gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[GIFAnalyzer]
# ein leeres "MIMEList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MIMEList[]
MIMEList[]=image/gif
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
