---
title: image.ini [medium]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [medium] in image.ini: Reference, Filters"
keywords: Einstellung, image.ini, medium, INI, setting, Reference, Filters
type: generated
generated: exp:help:examples
---
# image.ini [medium]

Der Block `[medium]` von `settings/image.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Reference

Standard: `reference`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[medium]
Reference=reference
*/ ?>
```

## Filters

Standard: eine Liste mit 1: `geometry/scaledownonly=200;200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[medium]
# ein leeres "Filters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Filters[]
Filters[]=geometry/scaledownonly=200;200
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
