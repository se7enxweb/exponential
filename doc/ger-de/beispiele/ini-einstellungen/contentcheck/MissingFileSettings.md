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

> Fehlende Dateien und Bild-Aliase (das Ziel „Fehlende Dateien“ von contentcheck/brokenrelations,  
> doc/eng-us/features/6.0/content-checks.md, "Missing files"). Die Klasse, die sagt, für welche Dateien ein Feld  
> eines Datentyps steht: eine, die expContentCheckFileHandler implementiert. Eine Erweiterung fügt ihren  
> Datei-Datentyp hier hinzu.  

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

> Die Tabelle jedes Datentyps, den expContentCheckFileTable behandelt (die Spalten von ezbinaryfile:  
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

> Die Aliase aus image.ini, die ein Bild haben soll, wenn „Alias nie erzeugt“ angekreuzt ist; leer:  
> jeder Alias aus image.ini. Aliase entstehen, wenn ein Bild zum ersten Mal in ihnen angezeigt wird, ein  
> fehlender ist also normal und wird nur aufgeführt, wenn danach gefragt wird.  

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

> Wie viele Objekte mit einem Satz von Abfragen geprüft werden (10 bis 1000)  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ChunkSize=200
*/ ?>
```

## ScanLimit

> Höchstens so viele Objekte werden für eine Seite geprüft (ChunkSize bis 100000)  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
ScanLimit=5000
*/ ?>
```

## ListLimit

> Die Liste der fehlenden Dateien hat eine Gesamtzahl und Seiten: Sie durchläuft jedes Objekt unter dem Knoten,  
> höchstens so viele (100 bis 1000000), und behält die Befunde so viele Sekunden für ihre Seiten  
> (0 bis 3600; 0: für jede Seite neu durchlaufen). Eine Behebung vergisst sie.  

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

> Höchstens so viele Feldversionen in einer Behebung (1 bis 1000)  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
MaxFixItems=200
*/ ?>
```

## FixBatchSize

> Wie viele Feldversionen eine Anfrage behebt; die Seite zeigt dann den Fortschritt und macht  
> mit dem nächsten Stapel weiter (1 bis 200)  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
FixBatchSize=10
*/ ?>
```

## MaxReplaceSize

> Die größte Datei, die „Neu hochladen oder ersetzen“ annimmt, in Bytes; 0: was PHP annimmt (upload\_max\_filesize)  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[MissingFileSettings]
MaxReplaceSize=0
*/ ?>
```

## CronjobReport

> enabled: Der Cronjob-Teil contentcheck zählt außerdem die fehlenden Dateien und Alias-Probleme  
> des ganzen Baums und schreibt die Zahl in seine Ausgabe (er behebt nie etwas)  

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
