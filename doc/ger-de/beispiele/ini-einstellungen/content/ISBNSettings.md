---
title: content.ini [ISBNSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ISBNSettings] in content.ini: StrictValidation"
keywords: Einstellung, content.ini, ISBNSettings, INI, setting, StrictValidation
type: generated
generated: exp:help:examples
---
# content.ini [ISBNSettings]

Der Block `[ISBNSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StrictValidation

> Will not validate other than registered ISBN13 ranges when this option is set to true.  
> When the value is false, values that are outside the ISBN13 ranges, will only check the length and check digit.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ISBNSettings]
StrictValidation=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
