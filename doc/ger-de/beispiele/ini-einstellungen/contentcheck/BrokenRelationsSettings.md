---
title: contentcheck.ini [BrokenRelationsSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BrokenRelationsSettings] in contentcheck.ini: XmlDatatypes, ChunkSize, ScanLimit"
keywords: Einstellung, contentcheck.ini, BrokenRelationsSettings, INI, setting, XmlDatatypes, ChunkSize, ScanLimit
type: generated
generated: exp:help:examples
---
# contentcheck.ini [BrokenRelationsSettings]

Der Block `[BrokenRelationsSettings]` von `settings/contentcheck.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/contentcheck.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentcheck.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## XmlDatatypes

> The datatypes whose text is read for links and embeds (object\_id="..." and node\_id="..."  
> of <link>, <embed> and <embed-inline>)  

Standard: eine Liste mit 1: `ezxmltext`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[BrokenRelationsSettings]
# ein leeres "XmlDatatypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
XmlDatatypes[]
XmlDatatypes[]=ezxmltext
*/ ?>
```

## ChunkSize

> How many objects are checked with one set of queries (10 to 1000)  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[BrokenRelationsSettings]
ChunkSize=200
*/ ?>
```

## ScanLimit

> At most this many objects are checked for one page (ChunkSize to 100000); a page with fewer  
> findings than its size then ends early and the next page goes on from there  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[BrokenRelationsSettings]
ScanLimit=5000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentcheck.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
