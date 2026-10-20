---
title: admininterface.ini [AdditionalMenuSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdditionalMenuSettings] in admininterface.ini: ContextMenuTemplateArray, SubitemsContextMenuTemplateArray, SubMenuTemplateArray, ClassMenuTemplateArray, BookmarkMenuTemplateArray, AdvancedMenuTemplateArray, ExportMenuTemplateArray ..."
keywords: Einstellung, admininterface.ini, AdditionalMenuSettings, INI, setting, ContextMenuTemplateArray, SubitemsContextMenuTemplateArray, SubMenuTemplateArray, ClassMenuTemplateArray, BookmarkMenuTemplateArray, AdvancedMenuTemplateArray, ExportMenuTemplateArray, ExportSubMenuTemplateArray, ImportExportMenuTemplateArray
type: generated
generated: exp:help:examples
---
# admininterface.ini [AdditionalMenuSettings]

Der Block `[AdditionalMenuSettings]` von `settings/admininterface.ini`, mit 9 Einstellung(en).

> Einstellungen, um zusätzliche Menüpunkte anhand von Templates zu laden  

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ContextMenuTemplateArray

> Template(s) für die Einträge des Kontextmenüs  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "ContextMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ContextMenuTemplateArray[]
*/ ?>
```

## SubitemsContextMenuTemplateArray

> Template(s) für das Kontextmenü der Unterelemente  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "SubitemsContextMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SubitemsContextMenuTemplateArray[]
*/ ?>
```

## SubMenuTemplateArray

> Template(s) für Untermenüs des Popup-Menüs  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "SubMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SubMenuTemplateArray[]
*/ ?>
```

## ClassMenuTemplateArray

> Template(s) für das Popup-Menü der Klasse am großen Klassensymbol in der Knotenansicht  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "ClassMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassMenuTemplateArray[]
*/ ?>
```

## BookmarkMenuTemplateArray

> Template(s) für das Popup-Menü der Lesezeichen in der Lesezeichenbox rechts  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "BookmarkMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BookmarkMenuTemplateArray[]
*/ ?>
```

## AdvancedMenuTemplateArray

> Template(s) für das erweiterte Menü, Untermenü des Kontext-/Unterelemente-Menüs  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "AdvancedMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdvancedMenuTemplateArray[]
*/ ?>
```

## ExportMenuTemplateArray

> Template(s) für das Untermenü Export des erweiterten Menüs: die Einträge, die eine Erweiterung unter den Paketeinträgen hinzufügt  
> (xrowextract: die CSV-Einträge). Jeder Eintrag öffnet das Formular seines Werkzeugs, für den Knoten ausgefüllt (%nodeID%), nie einen Export  
> selbst, und wird nur einem Benutzer mit der Richtlinie des Werkzeugs angezeigt. Export erscheint, wenn es einen Eintrag hat.  
> Anleitung: doc/eng-us/bc/6.0/content-export-menu.md  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "ExportMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExportMenuTemplateArray[]
*/ ?>
```

## ExportSubMenuTemplateArray

> Template(s) für die Untermenüs dieser Einträge (eine Ebene unter Export), ausgegeben nach dem Menü Export  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "ExportSubMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExportSubMenuTemplateArray[]
*/ ?>
```

## ImportExportMenuTemplateArray

> Template(s) für die Import- und Auftragseinträge des Menüs Import/Export (nur Import/Export). Leer deklariert, damit  
> popup\_export\_items.tpl es ohne Fehler wegen fehlender Variable liest, wenn keine Erweiterung einen hinzufügt.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# ein leeres "ImportExportMenuTemplateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImportExportMenuTemplateArray[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
