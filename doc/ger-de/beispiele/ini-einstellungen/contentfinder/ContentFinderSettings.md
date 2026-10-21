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

> true | false. Mit false antwortet der Finder in jedem Siteaccess mit 404.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
Enabled=true
*/ ?>
```

## SiteAccessList

> Die Siteaccesses, in denen der Finder antwortet. Leer (der Standard): jeder Siteaccess, dessen Design-  
> Liste (\[DesignSettings\] SiteDesign, AdditionalSiteDesignList\[\]) ein Administrations-Design enthält  
> (admin, admin2, admin3, admin4, admin4l, adminui oder jedes andere Design, das mit admin beginnt).  
> Jeder andere Siteaccess antwortet mit 404.  

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

> Höchstens so viele Objekte werden für eine Suche angezeigt (1 bis 100)  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
ResultLimit=25
*/ ?>
```

## FileRowLimit

> Höchstens so viele Dateizeilen werden für einen Dateipfad oder ein Präfix gelesen (1 bis 500)  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
FileRowLimit=50
*/ ?>
```

## PartialAliasSearch

> enabled | disabled. Wenn kein URL-Alias, kein Siteaccess-Präfix und kein letzter Teil eines Alias passt, sucht der  
> Finder nach Alias-Elementen, die den Text enthalten ("product" findet test-product). Das ist  
> ein LIKE '%...%' über die ganze Tabelle ezurlalias\_ml: Deaktivieren Sie es, wo die Tabelle sehr groß ist.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
PartialAliasSearch=enabled
*/ ?>
```

## MinimumPartLength

> Der kürzeste Text (in Zeichen, ohne die Trennzeichen), nach dem als Teil gesucht wird (1 bis 100)  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentfinder.ini.append.php
[ContentFinderSettings]
MinimumPartLength=3
*/ ?>
```

## PartRowLimit

> Höchstens so viele Alias-Elemente werden für einen Teil gelesen; die nächsten Treffer stehen zuerst  
> (das Element selbst, dann Elemente, die damit beginnen, dann ganze Wörter, dann innerhalb eines Wortes)  

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
