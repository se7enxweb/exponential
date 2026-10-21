---
title: transform.ini [Extensions]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Extensions] in transform.ini: Commands"
keywords: Einstellung, transform.ini, Extensions, INI, setting, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [Extensions]

Der Block `[Extensions]` von `settings/transform.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Commands

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[Extensions]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
