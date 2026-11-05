---
title: site.ini [DesignSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DesignSettings] in site.ini: StandardDesign, SiteDesign, AdditionalSiteDesignList, SwitchableDesigns, SwitchableDesignLabel, SwitchableDesignChain, DesignSwitchCookie, DesignLocationCache"
keywords: Einstellung, site.ini, DesignSettings, INI, setting, StandardDesign, SiteDesign, AdditionalSiteDesignList, SwitchableDesigns, SwitchableDesignLabel, SwitchableDesignChain, DesignSwitchCookie, DesignLocationCache
type: generated
generated: exp:help:examples
---
# site.ini [DesignSettings]

Der Block `[DesignSettings]` von `settings/site.ini`, mit 8 Einstellung(en).

> # Set access policy to allowed  
> Rules\[\]=access;enable  
> # Special syntax which means any module  
> # This means that for now all modules are enabled  
> Rules\[\]=moduleall  
> # Set policy to denied  
> Rules\[\]=access;disable  
> # If content/search module use current policy (denied)  
> Rules\[\]=module;content/search  
> # If rss module use current policy (denied)  
> Rules\[\]=module;rss  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StandardDesign

> Das Standard-Design, verwendet als Ausweich-Design,  
> wenn das Element nicht im Website-Design ist  

Standard: `standard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
StandardDesign=standard
*/ ?>
```

## SiteDesign

> Das Design der Website  

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
SiteDesign=admin
*/ ?>
```

## AdditionalSiteDesignList

> AdditionalSiteDesignArray enthält ein Array  
> von Website-Designs. Diese Designs werden verwendet,  
> wenn das Design im Haupt-Website-Design  
> nicht gefunden wurde. StandardDesign ist das Ausweich-Design.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
# ein leeres "AdditionalSiteDesignList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdditionalSiteDesignList[]
*/ ?>
```

## SwitchableDesigns

> Ein Front-Siteaccess kann jeden Besucher mit einem Klick zwischen Designs wechseln lassen  
> (doc/eng-us/features/6.0/front-design-switch.md). Aus, solange die Liste  
> leer ist; nie in einem Administrations-Siteaccess. Das SiteDesign des Siteaccess ist  
> der Standard; ein Link mit dem Ansichtsparameter /(design)/<name> wählt ein anderes  
> und bewahrt es in einem Cookie. In der site.ini.append.php des Siteaccess:  
>   SwitchableDesigns\[\]  
>   SwitchableDesigns\[\]=doc2025  
>   SwitchableDesignLabel\[doc\]=Modern  
>   SwitchableDesignLabel\[doc2025\]=Classic  
>   SwitchableDesignChain\[doc2025\]=standard  
> SwitchableDesignChain\[<design>\] ist die Fallback-Kette eines Designs, Designs  
> getrennt durch „;“ (ohne sie bleibt die AdditionalSiteDesignList\[\] des Siteaccess  
> bestehen). fetch( 'uiswitch', 'front\_designs' ) gibt Templates die Auswahl.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
# ein leeres "SwitchableDesigns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SwitchableDesigns[]
*/ ?>
```

## SwitchableDesignLabel

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
# ein leeres "SwitchableDesignLabel[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SwitchableDesignLabel[]
*/ ?>
```

## SwitchableDesignChain

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
# ein leeres "SwitchableDesignChain[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SwitchableDesignChain[]
*/ ?>
```

## DesignSwitchCookie

> Das Cookie, das die Wahl des Besuchers aufbewahrt  

Standard: `exp_design`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
DesignSwitchCookie=exp_design
*/ ?>
```

## DesignLocationCache

> Ob der Ort der Design-Verzeichnisse gecacht wird  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DesignSettings]
DesignLocationCache=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
