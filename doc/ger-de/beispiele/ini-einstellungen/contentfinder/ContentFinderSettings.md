---
title: contentfinder.ini [ContentFinderSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentFinderSettings] in contentfinder.ini: Enabled, SiteAccessList, ResultLimit, FileRowLimit, PartialAliasSearch, MinimumPartLength, PartRowLimit"
keywords: Einstellung, contentfinder.ini, ContentFinderSettings, INI, setting, Enabled, SiteAccessList, ResultLimit, FileRowLimit, PartialAliasSearch, MinimumPartLength, PartRowLimit
type: generated
generated: exp:help:examples
---
# contentfinder.ini [ContentFinderSettings]

Der Block `[ContentFinderSettings]` von `settings/contentfinder.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/contentfinder.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentfinder.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentfinder.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentfinder.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> true | false. With false the finder answers 404 on every siteaccess.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
Enabled=true
*/ ?>
```

## SiteAccessList

> The siteaccesses the finder answers on. Empty (the default): every siteaccess whose design  
> list (\[DesignSettings\] SiteDesign, AdditionalSiteDesignList\[\]) has an administration design  
> (admin, admin2, admin3, admin4, admin4l, adminui, or any other design starting with admin).  
> Every other siteaccess answers 404.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
# ein leeres "SiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteAccessList[]
*/ ?>
```

## ResultLimit

> At most this many objects are shown for one search (1 to 100)  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
ResultLimit=25
*/ ?>
```

## FileRowLimit

> At most this many file rows are read for one file path or prefix (1 to 500)  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
FileRowLimit=50
*/ ?>
```

## PartialAliasSearch

> enabled | disabled. When no URL alias, siteaccess prefix or last part of an alias matches, the  
> finder looks for alias elements that contain the text ("product" finds test-product). That is  
> one LIKE '%...%' over the whole of ezurlalias\_ml: disable it where the table is very large.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
PartialAliasSearch=enabled
*/ ?>
```

## MinimumPartLength

> The shortest text (in characters, without the separators) searched for as a part (1 to 100)  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
MinimumPartLength=3
*/ ?>
```

## PartRowLimit

> At most this many alias elements are read for one part; the closest matches are shown first  
> (the element itself, then elements starting with it, then whole words, then inside a word)  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
PartRowLimit=2000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentfinder.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentfinder.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
