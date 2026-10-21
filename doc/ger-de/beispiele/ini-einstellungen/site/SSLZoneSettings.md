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

> Einstellungen für SSL-Zonen  
> Weitere Informationen in doc/eng-us/features/3.8/ssl\_zones.txt.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SSLZones

> Die Funktion der SSL-Zonen aktivieren/deaktivieren.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SSLZoneSettings]
SSLZones=disabled
*/ ?>
```

## SSLSubtrees

> Inhalts-Teilbäume, für die SSL verwendet werden muss.  
> (derzeit beachten nur content/view und content/edit diese Einstellung)  
>
> Beispiele:  
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

> Der Standard-Zugriffsmodus ist einfaches HTTP.  
>
> Definieren Sie eine Ansicht als 'ssl', um für diese Ansicht den Zugriffsmodus HTTPS zu erzwingen.  
> Ist eine Ansicht als 'keep' definiert, ist der Zugriffsmodus für diese  
> Ansicht unbekannt, und der vorherige Zugriffsmodus bleibt.  
>
> Definieren Sie eine Ansicht als 'nossl', um für dieses Modul HTTP zu erzwingen. Weitere  
> SSL-Weiterleitungen werden ignoriert  
>
> Beispiele:  
> ModuleViewAccessMode\[shop/add\]=ssl  
> ModuleViewAccessMode\[shop/basket\]=ssl  
>
> Entfernen Sie diese Zeile nur, wenn Sie wissen, was Sie tun:  

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
