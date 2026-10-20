---
title: datatype.ini [ViewSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ViewSettings] in datatype.ini: GroupedInput, CustomActionMap"
keywords: Einstellung, datatype.ini, ViewSettings, INI, setting, GroupedInput, CustomActionMap
type: generated
generated: exp:help:examples
---
# datatype.ini [ViewSettings]

Der Block `[ViewSettings]` von `settings/datatype.ini`, mit 2 Einstellung(en).

> Settings related to content view and datatypes  

Bearbeiten Sie `settings/datatype.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datatype.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datatype.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datatype.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## GroupedInput

> A list of datatypes that needs to group output data  
> when displayed in view mode  
> NOTE: The datatype may override this using PHP code  

Standard: eine Liste mit 6: `ezinisetting`, `ezmultioption`, `ezoption`, `ezrangeoption`, `ezpackage`, `ezuser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatype.ini.append.php
[ViewSettings]
# ein leeres "GroupedInput[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupedInput[]
GroupedInput[]=ezinisetting
GroupedInput[]=ezmultioption
GroupedInput[]=ezoption
GroupedInput[]=ezrangeoption
GroupedInput[]=ezpackage
GroupedInput[]=ezuser
*/ ?>
```

## CustomActionMap

> List of custom datatype actions and corresponding <module>/<view> or  
> just <view> in case of content module where they should be executed  
> eg: CustomActionMap\[ActionAddToBasket\]=shop/basket  
> eg: CustomActionMap\[ActionCollectInformation\]=collectinformation  
> Note: Action definition should be defined in contentActionList function  
> of the datatype, see ez\[multi\]pricetype.php for code example.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatype.ini.append.php
[ViewSettings]
# ein leeres "CustomActionMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CustomActionMap[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von datatype.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatype.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
