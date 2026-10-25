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

> Where the catalogue of persistent classes is kept between requests, below the cache directory. It is rebuilt when  
> the autoload arrays change.  

Standard: `datatable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
CatalogueCacheDir=datatable
*/ ?>
```

## IgnoreClasses

> The persistent classes the catalogue leaves out (class names), e.g. a class whose definition() needs a request.  

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

> Relations inferred from column names (contentobject\_id, node\_id, ...) when a definition states none:  
> enabled (shown as "inferred") or disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
InferRelations=enabled
*/ ?>
```

## ContentTabInferred

> The "Data tables" tab of the content view lists the rows of extensions' tables that point at the node or its object  
> through a declared relation ('relations' of the definition, a foreign\_class, a \[Relation\_\*\] block). enabled also asks  
> the relations inferred from column names (one query more per relation and node view).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[DataTableSettings]
ContentTabInferred=disabled
*/ ?>
```

## MongoSampleSize

> Rows the describe page reads to work out the fields of a MongoDB collection  

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
