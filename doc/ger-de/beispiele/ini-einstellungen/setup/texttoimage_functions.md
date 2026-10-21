---
title: setup.ini [texttoimage_functions]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [texttoimage_functions] in setup.ini: Functions, Require"
keywords: Einstellung, setup.ini, texttoimage_functions, INI, setting, Functions, Require
type: generated
generated: exp:help:examples
---
# setup.ini [texttoimage_functions]

Der Block `[texttoimage_functions]` von `settings/setup.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Functions

Standard: `imagettftext;imagettfbbox`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[texttoimage_functions]
Functions=imagettftext;imagettfbbox
*/ ?>
```

## Require

Standard: `all`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[texttoimage_functions]
Require=all
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
