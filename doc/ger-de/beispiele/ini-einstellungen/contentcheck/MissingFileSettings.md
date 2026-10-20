---
title: contentcheck.ini [MissingFileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MissingFileSettings] in contentcheck.ini: Handlers, Tables, AliasList, ChunkSize, ScanLimit, ListLimit, ListCacheTTL, MaxFixItems ..."
keywords: Einstellung, contentcheck.ini, MissingFileSettings, INI, setting, Handlers, Tables, AliasList, ChunkSize, ScanLimit, ListLimit, ListCacheTTL, MaxFixItems, FixBatchSize, MaxReplaceSize, CronjobReport
type: generated
generated: exp:help:examples
---
# contentcheck.ini [MissingFileSettings]

Der Block `[MissingFileSettings]` von `settings/contentcheck.ini`, mit 11 Einstellung(en).

Bearbeiten Sie `settings/contentcheck.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentcheck.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handlers

> Missing files and image aliases (the target "Missing files" of contentcheck/brokenrelations,  
> doc/eng-us/features/6.0/content-checks.md, "Missing files"). The class that says which files a field  
> of a datatype stands for: one implementing expContentCheckFileHandler. An extension adds its  
> file datatype here.  

Standard: ein Hash mit 3: `ezimage=expContentCheckFileImage`, `ezbinaryfile=expContentCheckFileTable`, `ezmedia=expContentCheckFileTable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
# ein leeres "Handlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Handlers[]
Handlers[ezimage]=expContentCheckFileImage
Handlers[ezbinaryfile]=expContentCheckFileTable
Handlers[ezmedia]=expContentCheckFileTable
*/ ?>
```

## Tables

> The table of each datatype handled by expContentCheckFileTable (the columns of ezbinaryfile:  
> contentobject\_attribute\_id, version, filename, original\_filename, mime\_type)  

Standard: ein Hash mit 2: `ezbinaryfile=ezbinaryfile`, `ezmedia=ezmedia`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
# ein leeres "Tables[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tables[]
Tables[ezbinaryfile]=ezbinaryfile
Tables[ezmedia]=ezmedia
*/ ?>
```

## AliasList

> The aliases of image.ini an image is expected to have when "Alias never made" is ticked; empty:  
> every alias of image.ini. Aliases are made when an image is first shown in them, so a  
> missing one is normal and only listed when asked for.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
# ein leeres "AliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasList[]
*/ ?>
```

## ChunkSize

> How many objects are checked with one set of queries (10 to 1000)  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ChunkSize=200
*/ ?>
```

## ScanLimit

> At most this many objects are checked for one page (ChunkSize to 100000)  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ScanLimit=5000
*/ ?>
```

## ListLimit

> The list of missing files has a total and pages: it walks every object below the node, at  
> most this many (100 to 1000000), and keeps the findings this many seconds for its pages  
> (0 to 3600; 0: walked again for every page). A resolution forgets them.  

Standard: `50000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ListLimit=50000
*/ ?>
```

## ListCacheTTL

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ListCacheTTL=60
*/ ?>
```

## MaxFixItems

> At most this many field versions in one resolution (1 to 1000)  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
MaxFixItems=200
*/ ?>
```

## FixBatchSize

> How many field versions one request resolves; the page then shows the progress and goes on  
> with the next batch (1 to 200)  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
FixBatchSize=10
*/ ?>
```

## MaxReplaceSize

> The largest file "Re-upload or replace" takes, in bytes; 0: what PHP takes (upload\_max\_filesize)  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
MaxReplaceSize=0
*/ ?>
```

## CronjobReport

> enabled: the cronjob part contentcheck also counts the missing files and alias problems of  
> the whole tree and writes the count to its output (it never resolves anything)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
CronjobReport=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentcheck.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
