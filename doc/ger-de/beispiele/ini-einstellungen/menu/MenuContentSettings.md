---
title: menu.ini [MenuContentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MenuContentSettings] in menu.ini: TopIdentifierList, LeftIdentifierList"
keywords: Einstellung, menu.ini, MenuContentSettings, INI, setting, TopIdentifierList, LeftIdentifierList
type: generated
generated: exp:help:examples
---
# menu.ini [MenuContentSettings]

Der Block `[MenuContentSettings]` von `settings/menu.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TopIdentifierList

> This list contains the identifiers of the classse  
> that are allowed to be shown in top menues  

Standard: eine Liste mit 4: `folder`, `feedback_form`, `gallery`, `forum`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[MenuContentSettings]
# ein leeres "TopIdentifierList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TopIdentifierList[]
TopIdentifierList[]=folder
TopIdentifierList[]=feedback_form
TopIdentifierList[]=gallery
TopIdentifierList[]=forum
*/ ?>
```

## LeftIdentifierList

> This list contains the identifiers of the classse  
> that are allowed to be shown in left menues  

Standard: eine Liste mit 4: `folder`, `feedback_form`, `gallery`, `forum`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[MenuContentSettings]
# ein leeres "LeftIdentifierList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LeftIdentifierList[]
LeftIdentifierList[]=folder
LeftIdentifierList[]=feedback_form
LeftIdentifierList[]=gallery
LeftIdentifierList[]=forum
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
