---
title: site.ini [TimeZoneSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TimeZoneSettings] in site.ini: TimeZone"
keywords: Einstellung, site.ini, TimeZoneSettings, INI, setting, TimeZone
type: generated
generated: exp:help:examples
---
# site.ini [TimeZoneSettings]

Der Block `[TimeZoneSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TimeZone

> Override this value to set timezone to other than OS default.  
> See /usr/share/zoneinfo/zone.tab for possible values ( 3rd column ).  
> Example: TimeZone=America/Antigua  
>
> Notice: This will not work on threaded web servers or on Windows.  
>         Use only with Apache 1.3 or 2.0 prefork on \*nix.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TimeZoneSettings]
TimeZone=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
