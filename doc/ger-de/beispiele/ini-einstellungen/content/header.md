---
title: content.ini [header]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [header] in content.ini: UseStrictHeaderRule, AvailableClasses, CustomAttributes"
keywords: Einstellung, content.ini, header, INI, setting, UseStrictHeaderRule, AvailableClasses, CustomAttributes
type: generated
generated: exp:help:examples
---
# content.ini [header]

Der Block `[header]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## UseStrictHeaderRule

> UseStrictHeaderRule steuert, ob Überschriften-Tags  
> beliebig oder nur korrekt verschachtelt verwendet werden dürfen (Überschrift 2 muss in  
> Überschrift 1 liegen usw.)  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[header]
UseStrictHeaderRule=false
*/ ?>
```

## AvailableClasses

> Klassen für Überschriften  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[header]
# ein leeres "AvailableClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableClasses[]
*/ ?>
```

## CustomAttributes

> AvailableClasses\[\]=headerRed  
> AvailableClasses\[\]=headerBlue  
> Liste der eigenen Attribute  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[header]
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
