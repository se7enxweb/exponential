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

> Einstellungen zur Ansicht von Inhalten und zu Datentypen  

Bearbeiten Sie `settings/datatype.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datatype.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datatype.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datatype.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## GroupedInput

> Eine Liste der Datentypen, die ihre Ausgabedaten gruppieren müssen,  
> wenn sie im Ansichtsmodus angezeigt werden  
> HINWEIS: Der Datentyp kann dies per PHP-Code überschreiben  

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

> Liste eigener Aktionen von Datentypen und des zugehörigen <module>/<view> oder,  
> beim Modul content, nur <view>, wo sie ausgeführt werden sollen  
> z. B.: CustomActionMap\[ActionAddToBasket\]=shop/basket  
> z. B.: CustomActionMap\[ActionCollectInformation\]=collectinformation  
> Hinweis: Die Definition der Aktion gehört in die Funktion contentActionList  
> des Datentyps; ein Codebeispiel steht in ez\[multi\]pricetype.php.  

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
