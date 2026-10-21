---
title: image.ini [AnimatedGIF]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AnimatedGIF] in image.ini: MIMEType, Match, OverrideMIMEType, DisallowedFilters, ExtraFilters"
keywords: Einstellung, image.ini, AnimatedGIF, INI, setting, MIMEType, Match, OverrideMIMEType, DisallowedFilters, ExtraFilters
type: generated
generated: exp:help:examples
---
# image.ini [AnimatedGIF]

Der Block `[AnimatedGIF]` von `settings/image.ini`, mit 5 Einstellung(en).

> Ausweichlösung, wenn jpeg als Ausgabe deaktiviert ist.  
> ConversionRules\[\]=image/jpeg;image/png  
> Override für animierte GIFs; nötig, um  
> Dateien vom Typ image/gif mit mehreren Bildern richtig zu behandeln.  
> Es schaltet alle Geometriefilter ab, damit die Bilder unverändert bleiben.  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MIMEType

> Der MIME-Typ, für den dieses Override gilt  

Standard: `image/gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
MIMEType=image/gif
*/ ?>
```

## Match

> Eine Liste von Bedingungen, die erfüllt sein müssen, damit dieses Override verwendet wird  

Standard: ein Hash mit 1: `is_animated=1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
# ein leeres "Match[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Match[]
Match[is_animated]=1
*/ ?>
```

## OverrideMIMEType

> Alternativer MIME-Typ für das Bild, derzeit unbenutzt.  

Standard: `animation/gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
OverrideMIMEType=animation/gif
*/ ?>
```

## DisallowedFilters

> Eine Liste von Filtern, die auf dieses Bild nicht angewendet werden dürfen;  
> Platzhalter sind ebenfalls möglich.  

Standard: eine Liste mit 1: `*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
# ein leeres "DisallowedFilters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DisallowedFilters[]
DisallowedFilters[]=*
*/ ?>
```

## ExtraFilters

> Eine Liste von Filtern, die auf dieses Bild angewendet werden dürfen;  
> Platzhalter sind ebenfalls möglich. Wenn angegeben, sind nur die Filter  
> in dieser Liste erlaubt  
> Beachten Sie, dass das Skalieren animierter GIFs mit GD2 nicht unterstützt wird, zumindest nicht vor PHP 5.3.  
> Siehe: http://www.libgd.org/FAQ\_PHP  
> AllowedFilters\[\]  
> Eine Liste zusätzlicher Filter für dieses Bild.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnimatedGIF]
# ein leeres "ExtraFilters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtraFilters[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
