---
title: logviewer.ini [SearchSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SearchSettings] in logviewer.ini: MaxBytesPerRequest, TimeLimit, MaxResults, MaxPatternLength, BacktrackLimit, Rotations, MaxGzipBytes, RequestWindow"
keywords: Einstellung, logviewer.ini, SearchSettings, INI, setting, MaxBytesPerRequest, TimeLimit, MaxResults, MaxPatternLength, BacktrackLimit, Rotations, MaxGzipBytes, RequestWindow
type: generated
generated: exp:help:examples
---
# logviewer.ini [SearchSettings]

Der Block `[SearchSettings]` von `settings/logviewer.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxBytesPerRequest

> The most bytes one search request reads; a search over more continues with "More results" from where it stopped.  

Standard: `33554432`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxBytesPerRequest=33554432
*/ ?>
```

## TimeLimit

> The longest a search request reads, in milliseconds (also the regular expression's time budget).  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
TimeLimit=2000
*/ ?>
```

## MaxResults

> The most matching entries one request returns.  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxResults=200
*/ ?>
```

## MaxPatternLength

> The longest search text or regular expression.  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxPatternLength=200
*/ ?>
```

## BacktrackLimit

> The backtracking limit of a regular expression search (pcre.backtrack\_limit while it runs): a pattern that  
> needs more on one line is stopped and reported as too expensive.  

Standard: `100000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
BacktrackLimit=100000
*/ ?>
```

## Rotations

> Search the rotations (.1 .. .N and .gz) of a file too, when the page asks for it.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
Rotations=enabled
*/ ?>
```

## MaxGzipBytes

> The most bytes of a compressed rotation one search unpacks.  

Standard: `67108864`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxGzipBytes=67108864
*/ ?>
```

## RequestWindow

> The seconds before and after an entry that count as the same request (related lines).  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
RequestWindow=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
