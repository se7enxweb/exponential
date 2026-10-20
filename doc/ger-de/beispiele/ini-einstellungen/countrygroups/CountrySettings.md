---
title: countrygroups.ini [CountrySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CountrySettings] in countrygroups.ini: GroupList, RepeatInAllCountries, GroupName_common, GroupCountries_common"
keywords: Einstellung, countrygroups.ini, CountrySettings, INI, setting, GroupList, RepeatInAllCountries, GroupName_common, GroupCountries_common
type: generated
generated: exp:help:examples
---
# countrygroups.ini [CountrySettings]

Der Block `[CountrySettings]` von `settings/countrygroups.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/countrygroups.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/countrygroups.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/countrygroups.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/countrygroups.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## GroupList

> The groups shown before "All countries", in this order. Each group needs  
> GroupName\_<group> (its label, translated in the context kernel/country)  
> and GroupCountries\_<group>\[\] (ISO 3166-1 alpha-2 codes, ranked).  
> An empty list leaves only "All countries".  

Standard: eine Liste mit 1: `common`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/countrygroups.ini.append.php
[CountrySettings]
# ein leeres "GroupList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupList[]
GroupList[]=common
*/ ?>
```

## RepeatInAllCountries

> Whether "All countries" lists the countries of the groups above again  
> (enabled), so the A-Z list stays complete, or leaves them out (disabled).  
> A select that allows several countries never repeats them.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/countrygroups.ini.append.php
[CountrySettings]
RepeatInAllCountries=enabled
*/ ?>
```

## GroupName_common

Standard: `Most used countries`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/countrygroups.ini.append.php
[CountrySettings]
GroupName_common=Most used countries
*/ ?>
```

## GroupCountries_common

> North America, South America, Europe, the United Kingdom, Asia,  
> New Zealand, Australia, Africa  

Standard: eine Liste mit 37: `US`, `CA`, `MX`, `BR`, `AR`, `CO`, `CL`, `PE`, `DE`, `FR`, `IT`, `ES` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/countrygroups.ini.append.php
[CountrySettings]
# ein leeres "GroupCountries_common[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupCountries_common[]
GroupCountries_common[]=US
GroupCountries_common[]=CA
GroupCountries_common[]=MX
GroupCountries_common[]=BR
GroupCountries_common[]=AR
GroupCountries_common[]=CO
GroupCountries_common[]=CL
GroupCountries_common[]=PE
GroupCountries_common[]=DE
GroupCountries_common[]=FR
GroupCountries_common[]=IT
GroupCountries_common[]=ES
GroupCountries_common[]=NL
GroupCountries_common[]=PL
GroupCountries_common[]=SE
GroupCountries_common[]=BE
GroupCountries_common[]=CH
GroupCountries_common[]=AT
GroupCountries_common[]=IE
GroupCountries_common[]=PT
# ... 17 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von countrygroups.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/countrygroups.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
