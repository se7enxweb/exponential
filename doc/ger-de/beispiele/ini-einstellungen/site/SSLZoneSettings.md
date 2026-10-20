---
title: site.ini [SSLZoneSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SSLZoneSettings] in site.ini: SSLZones, SSLSubtrees, ModuleViewAccessMode"
keywords: Einstellung, site.ini, SSLZoneSettings, INI, setting, SSLZones, SSLSubtrees, ModuleViewAccessMode
type: generated
generated: exp:help:examples
---
# site.ini [SSLZoneSettings]

Der Block `[SSLZoneSettings]` von `settings/site.ini`, mit 3 Einstellung(en).

> Settings for SSL zones  
> For more information please look at doc/eng-us/features/3.8/ssl\_zones.txt.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SSLZones

> Enable/disable the SSL zones functionality.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SSLZoneSettings]
SSLZones=disabled
*/ ?>
```

## SSLSubtrees

> Content subtrees we must use SSL for.  
> (currently, only content/view and content/edit respect this setting)  
>
> Examples:  
> SSLSubtrees\[\]=/news  
> SSLSubtrees\[\]=/polls  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SSLZoneSettings]
# ein leeres "SSLSubtrees[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SSLSubtrees[]
*/ ?>
```

## ModuleViewAccessMode

> Default access mode is plain HTTP.  
>
> Define a view as 'ssl' to force HTTPS access mode for this view.  
> If a view is defined as 'keep' then access mode is unknown  
> for this view, and the previous access mode is kept.  
>
> Define a view as 'nossl' to force HTTP for this module. Further  
> SSL redirections will be ignored  
>
> Examples:  
> ModuleViewAccessMode\[shop/add\]=ssl  
> ModuleViewAccessMode\[shop/basket\]=ssl  
>
> Do not remove this line unless you know what you're doing:  

Standard: ein Hash mit 2: `content/*=keep`, `layout/*=keep`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SSLZoneSettings]
# ein leeres "ModuleViewAccessMode[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ModuleViewAccessMode[]
ModuleViewAccessMode[content/*]=keep
ModuleViewAccessMode[layout/*]=keep
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
