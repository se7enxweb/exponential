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

> Settings to load additional menu items based on templates  

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ContextMenuTemplateArray

> Context menu items template(s)  

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

> Subitems context menu template(s)  

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

> Template(s) for submenus of the popupmenu  

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

> Template(s) for class popup menu on large class icon in node view  

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

> Template(s) for bookmark popup menu on right side bookmark box  

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

> Template(s) for advance menu, sub menu of Context/Subitems menu  

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

> Template(s) for the Export submenu of the advance menu: the items an extension adds below the package entries  
> (xrowextract: the CSV entries). Each item opens its tool's form filled in for the node (%nodeID%), never an export  
> itself, and is shown only to a user with the tool's policy. Export is shown when it has an item.  
> Guide: doc/eng-us/bc/6.0/content-export-menu.md  

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

> Template(s) for the submenus of those items (a level below Export), output after the Export menu  

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

> Template(s) for the import and job items of the Import/Export menu (Import/Export only). Declared empty so the  
> popup\_export\_items.tpl reads it without a missing-variable error when no extension adds one.  

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
