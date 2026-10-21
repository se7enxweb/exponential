---
title: content.ini [DataTypeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DataTypeSettings] in content.ini: RepositoryDirectories, ExtensionDirectories, AvailableDataTypes, UserDataTypes"
keywords: Einstellung, content.ini, DataTypeSettings, INI, setting, RepositoryDirectories, ExtensionDirectories, AvailableDataTypes, UserDataTypes
type: generated
generated: exp:help:examples
---
# content.ini [DataTypeSettings]

Der Block `[DataTypeSettings]` von `settings/content.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectories

> Eine Liste der Verzeichnisse, in denen nach Datentypen gesucht wird  

Standard: eine Liste mit 1: `kernel/classes/datatypes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[DataTypeSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/datatypes
*/ ?>
```

## ExtensionDirectories

> Eine Liste der Erweiterungen, die Datentypen für Inhaltsobjekte haben  
> Üblicherweise legt man in der Erweiterung eine Datei settings/content.ini.append  
> an und trägt den Namen der Erweiterung ein, um automatisch  
> die Datentypen der Erweiterung zu erhalten, wenn sie eingeschaltet ist.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[DataTypeSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## AvailableDataTypes

Standard: eine Liste mit 35: `ezstring`, `eztext`, `ezxmltext`, `ezdate`, `ezdatetime`, `eztime`, `ezboolean`, `ezinteger`, `ezfloat`, `ezenum`, `ezobjectrelation`, `ezobjectrelationlist` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[DataTypeSettings]
# ein leeres "AvailableDataTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableDataTypes[]
AvailableDataTypes[]=ezstring
AvailableDataTypes[]=eztext
AvailableDataTypes[]=ezxmltext
AvailableDataTypes[]=ezdate
AvailableDataTypes[]=ezdatetime
AvailableDataTypes[]=eztime
AvailableDataTypes[]=ezboolean
AvailableDataTypes[]=ezinteger
AvailableDataTypes[]=ezfloat
AvailableDataTypes[]=ezenum
AvailableDataTypes[]=ezobjectrelation
AvailableDataTypes[]=ezobjectrelationlist
AvailableDataTypes[]=ezimage
AvailableDataTypes[]=ezbinaryfile
AvailableDataTypes[]=ezmedia
AvailableDataTypes[]=ezauthor
AvailableDataTypes[]=ezurl
AvailableDataTypes[]=ezemail
AvailableDataTypes[]=ezoption
AvailableDataTypes[]=ezmultioption
# ... 15 weitere in der ausgelieferten Datei
*/ ?>
```

## UserDataTypes

> Datentypen, die Benutzerkonten enthalten  
> mehrere Aktionen an Objekten mit Benutzerkonten (Entfernen, Verschieben usw.)  
> erfordern das Leeren des Caches der Benutzerrichtlinien  

Standard: eine Liste mit 1: `ezuser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[DataTypeSettings]
# ein leeres "UserDataTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UserDataTypes[]
UserDataTypes[]=ezuser
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
