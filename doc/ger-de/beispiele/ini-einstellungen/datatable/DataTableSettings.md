---
title: datatable.ini [DataTableSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DataTableSettings] in datatable.ini: CatalogueCacheDir, IgnoreClasses, InferRelations, ContentTabInferred, MongoSampleSize"
keywords: Einstellung, datatable.ini, DataTableSettings, INI, setting, CatalogueCacheDir, IgnoreClasses, InferRelations, ContentTabInferred, MongoSampleSize
type: generated
generated: exp:help:examples
---
# datatable.ini [DataTableSettings]

Der Block `[DataTableSettings]` von `settings/datatable.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/datatable.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datatable.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datatable.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datatable.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CatalogueCacheDir

> Wo der Katalog der persistenten Klassen zwischen Anfragen aufbewahrt wird, unterhalb des Cache-Verzeichnisses. Er wird neu aufgebaut, wenn  
> die Autoload-Arrays sich ändern.  

Standard: `datatable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
CatalogueCacheDir=datatable
*/ ?>
```

## IgnoreClasses

> Die persistenten Klassen, die der Katalog auslässt (Klassennamen), z. B. eine Klasse, deren definition() eine Anfrage braucht.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
# ein leeres "IgnoreClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IgnoreClasses[]
*/ ?>
```

## InferRelations

> Aus Spaltennamen (contentobject\_id, node\_id, ...) abgeleitete Beziehungen, wenn eine Definition keine angibt:  
> enabled (angezeigt als "inferred") oder disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
InferRelations=enabled
*/ ?>
```

## ContentTabInferred

> Der Tab "Datentabellen" der Inhaltsansicht listet die Zeilen der Tabellen von Erweiterungen auf, die über eine deklarierte Beziehung  
> ('relations' der Definition, eine foreign\_class, ein \[Relation\_\*\]-Block) auf den Knoten oder sein Objekt verweisen. enabled fragt auch  
> die aus Spaltennamen abgeleiteten Beziehungen ab (eine Abfrage mehr pro Beziehung und Knotenansicht).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
ContentTabInferred=disabled
*/ ?>
```

## MongoSampleSize

> Zeilen, die die Beschreibungsseite liest, um die Felder einer MongoDB-Collection zu ermitteln  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
MongoSampleSize=50
*/ ?>
```

## Siehe auch

- [Alle Blöcke von datatable.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatable.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
