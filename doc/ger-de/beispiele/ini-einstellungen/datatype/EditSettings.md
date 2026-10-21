---
title: datatype.ini [EditSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EditSettings] in datatype.ini: GroupedInput"
keywords: Einstellung, datatype.ini, EditSettings, INI, setting, GroupedInput
type: generated
generated: exp:help:examples
---
# datatype.ini [EditSettings]

Der Block `[EditSettings]` von `settings/datatype.ini`, mit 1 Einstellung(en).

> Einstellungen zum Bearbeiten von Inhalten und zu Datentypen  

Bearbeiten Sie `settings/datatype.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datatype.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datatype.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datatype.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## GroupedInput

> Eine Liste der Datentypen, die ihre Eingabefelder gruppieren müssen,  
> wenn sie im Bearbeitungsmodus angezeigt werden  
> HINWEIS: Der Datentyp kann dies per PHP-Code überschreiben  

Standard: eine Liste mit 17: `ezauthor`, `ezbinaryfile`, `ezdate`, `ezdatetime`, `ezimage`, `ezinisetting`, `ezmatrix`, `ezmedia`, `ezoption`, `ezmultioption`, `ezobjectrelationlist`, `ezpackage` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatype.ini.append.php
[EditSettings]
# ein leeres "GroupedInput[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupedInput[]
GroupedInput[]=ezauthor
GroupedInput[]=ezbinaryfile
GroupedInput[]=ezdate
GroupedInput[]=ezdatetime
GroupedInput[]=ezimage
GroupedInput[]=ezinisetting
GroupedInput[]=ezmatrix
GroupedInput[]=ezmedia
GroupedInput[]=ezoption
GroupedInput[]=ezmultioption
GroupedInput[]=ezobjectrelationlist
GroupedInput[]=ezpackage
GroupedInput[]=ezrangeoption
GroupedInput[]=eztime
GroupedInput[]=ezurl
GroupedInput[]=ezuser
GroupedInput[]=ezprice
*/ ?>
```

## Siehe auch

- [Alle Blöcke von datatype.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatype.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
