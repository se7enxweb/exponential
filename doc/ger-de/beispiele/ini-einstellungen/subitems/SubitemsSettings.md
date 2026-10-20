---
title: subitems.ini [SubitemsSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SubitemsSettings] in subitems.ini: DefaultColumns, AttributeColumns, AttributeColumnsExcludedDataTypes, AttributeColumnsDataTypePolicy, CSVExport, CSVLimit, PageSizes"
keywords: Einstellung, subitems.ini, SubitemsSettings, INI, setting, DefaultColumns, AttributeColumns, AttributeColumnsExcludedDataTypes, AttributeColumnsDataTypePolicy, CSVExport, CSVLimit, PageSizes
type: generated
generated: exp:help:examples
---
# subitems.ini [SubitemsSettings]

Der Block `[SubitemsSettings]` von `settings/subitems.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/subitems.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitems.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitems.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitems.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultColumns

> The columns shown by default, in this order, wherever no \[Defaults\_\*\] block below matches and  
> the user has not saved a choice of their own. These are the list's former defaults for the  
> content tree (admininterface.ini \[SubItems\] VisibleColumns\[ezcontentnavigationpart\]).  

Standard: eine Liste mit 4: `name`, `published`, `translations`, `priority`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
# ein leeres "DefaultColumns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DefaultColumns[]
DefaultColumns[]=name
DefaultColumns[]=published
DefaultColumns[]=translations
DefaultColumns[]=priority
*/ ?>
```

## AttributeColumns

> enabled: Table options also offers one column per attribute of the classes present among the  
> parent's children (key attr:<class>/<attribute>, group "Attributes: <class name>"), no INI  
> needed. The value is the attribute's title, else its text; sortable when the datatype sorts.  
> disabled: only the columns of subitemscolumns.ini.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
AttributeColumns=enabled
*/ ?>
```

## AttributeColumnsExcludedDataTypes

> Datatypes never offered as attribute columns. ezuser's text holds the login, e-mail and  
> password hash, so it is excluded; the user columns of subitemscolumns.ini show what is safe.  

Standard: eine Liste mit 1: `ezuser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
# ein leeres "AttributeColumnsExcludedDataTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AttributeColumnsExcludedDataTypes[]
AttributeColumnsExcludedDataTypes[]=ezuser
*/ ?>
```

## AttributeColumnsDataTypePolicy

> Policies an attribute column of a datatype needs, as module/function, several separated by ;  
> (all must be granted). Without an entry an attribute column needs nothing beyond content/read  
> on the rows.  
> Example: only users who may edit users see e-mail attributes  
> AttributeColumnsDataTypePolicy\[ezemail\]=user/register  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
# ein leeres "AttributeColumnsDataTypePolicy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AttributeColumnsDataTypePolicy[]
*/ ?>
```

## CSVExport

> enabled: Table options has "Export CSV", the view content/subitemsexport/<node id> exists.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
CSVExport=enabled
*/ ?>
```

## CSVLimit

> The most rows one CSV export writes.  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
CSVLimit=5000
*/ ?>
```

## PageSizes

> The page sizes Table options offers (a custom size can still be typed in).  

Standard: eine Liste mit 4: `10`, `25`, `50`, `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
# ein leeres "PageSizes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PageSizes[]
PageSizes[]=10
PageSizes[]=25
PageSizes[]=50
PageSizes[]=100
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitems.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitems.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
