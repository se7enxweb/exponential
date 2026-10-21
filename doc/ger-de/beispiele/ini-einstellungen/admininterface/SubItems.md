---
title: admininterface.ini [SubItems]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SubItems] in admininterface.ini: VisibleColumns"
keywords: Einstellung, admininterface.ini, SubItems, INI, setting, VisibleColumns
type: generated
generated: exp:help:examples
---
# admininterface.ini [SubItems]

Der Block `[SubItems]` von `settings/admininterface.ini`, mit 1 Einstellung(en).

> Beispieleinstellungen aus der Erweiterung OpenOffice.org  
> ContextMenuTemplateArray\[\]=node/oocontextmenu.tpl  
> SubitemsContextMenuTemplateArray\[\]=node/oosubitemscontextmenu.tpl  
> Standardeinstellung für die Tabelle der Unterelemente  

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## VisibleColumns

> Die Spaltennamen entsprechen den Attributnamen der Template-Variable $node  

Standard: ein Hash mit 3: `ezcontentnavigationpart=checkbox;crank;name;published_date;translations;priority`, `ezmedianavigationpart=checkbox;crank;thumbnail;name;hidden_status_string;class_name`, `ezusernavigationpart=checkbox;crank;name;creator;published_date`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SubItems]
# ein leeres "VisibleColumns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
VisibleColumns[]
VisibleColumns[ezcontentnavigationpart]=checkbox;crank;name;published_date;translations;priority
VisibleColumns[ezmedianavigationpart]=checkbox;crank;thumbnail;name;hidden_status_string;class_name
VisibleColumns[ezusernavigationpart]=checkbox;crank;name;creator;published_date
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
