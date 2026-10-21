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

> Die standardmäßig angezeigten Spalten, in dieser Reihenfolge, überall, wo kein Block \[Defaults\_\*\] unten passt und  
> der Benutzer keine eigene Wahl gespeichert hat. Das sind die früheren Standards der Liste für den  
> Inhaltsbaum (admininterface.ini \[SubItems\] VisibleColumns\[ezcontentnavigationpart\]).  

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

> enabled: Die Tabellenoptionen bieten außerdem eine Spalte pro Attribut der Klassen an, die unter den  
> Unterknoten des Elternknotens vorkommen (Schlüssel attr:<class>/<attribute>, Gruppe „Attribute: <class name>“), ohne  
> INI. Der Wert ist der Titel des Attributs, sonst sein Text; sortierbar, wenn der Datentyp sortiert.  
> disabled: nur die Spalten aus subitemscolumns.ini.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
AttributeColumns=enabled
*/ ?>
```

## AttributeColumnsExcludedDataTypes

> Datentypen, die nie als Attributspalten angeboten werden. Der Text von ezuser enthält Login, E-Mail und  
> Passwort-Hash, daher ist er ausgeschlossen; die Benutzerspalten aus subitemscolumns.ini zeigen, was sicher ist.  

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

> Richtlinien, die eine Attributspalte eines Datentyps braucht, als module/function, mehrere durch ; getrennt  
> (alle müssen gewährt sein). Ohne Eintrag braucht eine Attributspalte nichts außer content/read  
> auf den Zeilen.  
> Beispiel: Nur Benutzer, die Benutzer bearbeiten dürfen, sehen E-Mail-Attribute  
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

> enabled: Die Tabellenoptionen haben „CSV exportieren“, die Ansicht content/subitemsexport/<node id> existiert.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
CSVExport=enabled
*/ ?>
```

## CSVLimit

> Die meisten Zeilen, die ein CSV-Export schreibt.  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[SubitemsSettings]
CSVLimit=5000
*/ ?>
```

## PageSizes

> Die Seitengrößen, die die Tabellenoptionen anbieten (eine eigene Größe lässt sich weiterhin eingeben).  

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
