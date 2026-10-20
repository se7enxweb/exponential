---
title: content.ini [link]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [link] in content.ini: AvailableViewModes, AvailableClasses, CustomAttributes"
keywords: Einstellung, content.ini, link, INI, setting, AvailableViewModes, AvailableClasses, CustomAttributes
type: generated
generated: exp:help:examples
---
# content.ini [link]

Der Block `[link]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AvailableViewModes

> AvailableViewModes is possible views for the view parameter (used by oe)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[link]
# ein leeres "AvailableViewModes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableViewModes[]
*/ ?>
```

## AvailableClasses

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[link]
# ein leeres "AvailableClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableClasses[]
*/ ?>
```

## CustomAttributes

> AvailableClasses\[\]=linkRed  
> AvailableClasses\[\]=linkBlue  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[link]
# ein leeres "CustomAttributes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CustomAttributes[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
