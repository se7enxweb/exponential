---
title: admininterface.ini [AdditionalMenuSettings]
audience: developer, admin
category: examples
summary: "The settings of [AdditionalMenuSettings] in admininterface.ini: ContextMenuTemplateArray, SubitemsContextMenuTemplateArray, SubMenuTemplateArray, ClassMenuTemplateArray, BookmarkMenuTemplateArray, AdvancedMenuTemplateArray, ExportMenuTemplateArray, ExportSubMenuTemplateArray ..."
keywords: admininterface.ini, AdditionalMenuSettings, INI, setting, ContextMenuTemplateArray, SubitemsContextMenuTemplateArray, SubMenuTemplateArray, ClassMenuTemplateArray, BookmarkMenuTemplateArray, AdvancedMenuTemplateArray, ExportMenuTemplateArray, ExportSubMenuTemplateArray, ImportExportMenuTemplateArray
type: generated
generated: exp:help:examples
---
# admininterface.ini [AdditionalMenuSettings]

The block `[AdditionalMenuSettings]` of `settings/admininterface.ini`, with 9 setting(s).

> Settings to load additional menu items based on templates  

Never edit `settings/admininterface.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/admininterface.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in an extension (read when the extension is active).

## ContextMenuTemplateArray

> Context menu items template(s)  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "ContextMenuTemplateArray[]" first replaces the shipped list; without it the items are added
ContextMenuTemplateArray[]
*/ ?>
```

## SubitemsContextMenuTemplateArray

> Subitems context menu template(s)  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "SubitemsContextMenuTemplateArray[]" first replaces the shipped list; without it the items are added
SubitemsContextMenuTemplateArray[]
*/ ?>
```

## SubMenuTemplateArray

> Template(s) for submenus of the popupmenu  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "SubMenuTemplateArray[]" first replaces the shipped list; without it the items are added
SubMenuTemplateArray[]
*/ ?>
```

## ClassMenuTemplateArray

> Template(s) for class popup menu on large class icon in node view  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "ClassMenuTemplateArray[]" first replaces the shipped list; without it the items are added
ClassMenuTemplateArray[]
*/ ?>
```

## BookmarkMenuTemplateArray

> Template(s) for bookmark popup menu on right side bookmark box  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "BookmarkMenuTemplateArray[]" first replaces the shipped list; without it the items are added
BookmarkMenuTemplateArray[]
*/ ?>
```

## AdvancedMenuTemplateArray

> Template(s) for advance menu, sub menu of Context/Subitems menu  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "AdvancedMenuTemplateArray[]" first replaces the shipped list; without it the items are added
AdvancedMenuTemplateArray[]
*/ ?>
```

## ExportMenuTemplateArray

> Template(s) for the Export submenu of the advance menu: the items an extension adds below the package entries  
> (xrowextract: the CSV entries). Each item opens its tool's form filled in for the node (%nodeID%), never an export  
> itself, and is shown only to a user with the tool's policy. Export is shown when it has an item.  
> Guide: doc/bc/6.0/content-export-menu.md  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "ExportMenuTemplateArray[]" first replaces the shipped list; without it the items are added
ExportMenuTemplateArray[]
*/ ?>
```

## ExportSubMenuTemplateArray

> Template(s) for the submenus of those items (a level below Export), output after the Export menu  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "ExportSubMenuTemplateArray[]" first replaces the shipped list; without it the items are added
ExportSubMenuTemplateArray[]
*/ ?>
```

## ImportExportMenuTemplateArray

> Template(s) for the import and job items of the Import/Export menu (Import/Export only). Declared empty so the  
> popup\_export\_items.tpl reads it without a missing-variable error when no extension adds one.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[AdditionalMenuSettings]
# an empty "ImportExportMenuTemplateArray[]" first replaces the shipped list; without it the items are added
ImportExportMenuTemplateArray[]
*/ ?>
```

## Related

- [All blocks of admininterface.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/admininterface.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
