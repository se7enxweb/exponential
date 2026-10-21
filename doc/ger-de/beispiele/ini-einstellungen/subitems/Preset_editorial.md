---
title: subitems.ini [Preset_editorial]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Preset_editorial] in subitems.ini: Name, Columns"
keywords: Einstellung, subitems.ini, Preset_editorial, INI, setting, Name, Columns
type: generated
generated: exp:help:examples
---
# subitems.ini [Preset_editorial]

Der Block `[Preset_editorial]` von `settings/subitems.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/subitems.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitems.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitems.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitems.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Editorial`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Preset_editorial]
Name=Editorial
*/ ?>
```

## Columns

Standard: eine Liste mit 6: `name`, `type`, `modifier`, `modified`, `published`, `objectstate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Preset_editorial]
# ein leeres "Columns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Columns[]
Columns[]=name
Columns[]=type
Columns[]=modifier
Columns[]=modified
Columns[]=published
Columns[]=objectstate
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitems.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitems.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
