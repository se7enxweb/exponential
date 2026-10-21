---
title: setup.ini [image_conversion]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [image_conversion] in setup.ini: TestList, Require"
keywords: Einstellung, setup.ini, image_conversion, INI, setting, TestList, Require
type: generated
generated: exp:help:examples
---
# setup.ini [image_conversion]

Der Block `[image_conversion]` von `settings/setup.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TestList

Standard: `imagegd_extension;imagemagick_program`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[image_conversion]
TestList=imagegd_extension;imagemagick_program
*/ ?>
```

## Require

Standard: `one`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[image_conversion]
Require=one
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
