---
title: collect.ini [InfoSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [InfoSettings] in collect.ini: Type, TypeList, TypeAttribute"
keywords: Einstellung, collect.ini, InfoSettings, INI, setting, Type, TypeList, TypeAttribute
type: generated
generated: exp:help:examples
---
# collect.ini [InfoSettings]

Der Block `[InfoSettings]` von `settings/collect.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/collect.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/collect.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/collect.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/collect.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Type

> Der Standardtyp der Informationssammlung  

Standard: `form`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[InfoSettings]
Type=form
*/ ?>
```

## TypeList

> Ordnet Klassen-ID oder -Bezeichner einem Informationssammlungstyp zu  

Standard: ein Hash mit 3: `poll=poll`, `form=form`, `feedback=feedback`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[InfoSettings]
# ein leeres "TypeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeList[]
TypeList[poll]=poll
TypeList[form]=form
TypeList[feedback]=feedback
*/ ?>
```

## TypeAttribute

> Welches Attribut im Objekt den Typ bestimmt;  
> wenn gefunden, wird der Inhalt des Attributs als Typ verwendet  

Standard: `collection_type`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[InfoSettings]
TypeAttribute=collection_type
*/ ?>
```

## Siehe auch

- [Alle Blöcke von collect.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/collect.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
