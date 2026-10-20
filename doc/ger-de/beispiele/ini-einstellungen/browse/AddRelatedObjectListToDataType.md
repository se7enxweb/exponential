---
title: browse.ini [AddRelatedObjectListToDataType]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AddRelatedObjectListToDataType] in browse.ini: StartNode, SelectionType, ReturnType"
keywords: Einstellung, browse.ini, AddRelatedObjectListToDataType, INI, setting, StartNode, SelectionType, ReturnType
type: generated
generated: exp:help:examples
---
# browse.ini [AddRelatedObjectListToDataType]

Der Block `[AddRelatedObjectListToDataType]` von `settings/browse.ini`, mit 3 Einstellung(en).

> Action for finding objects to related object list datatype  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartNode

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AddRelatedObjectListToDataType]
StartNode=content
*/ ?>
```

## SelectionType

Standard: `multiple`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AddRelatedObjectListToDataType]
SelectionType=multiple
*/ ?>
```

## ReturnType

Standard: `ObjectID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[AddRelatedObjectListToDataType]
ReturnType=ObjectID
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
