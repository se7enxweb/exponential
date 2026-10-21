---
title: querycache.ini [QueryCacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [QueryCacheSettings] in querycache.ini: Mode, Storage, ApcuReserve, MaxAge, MaxRows, ExcludeTables"
keywords: Einstellung, querycache.ini, QueryCacheSettings, INI, setting, Mode, Storage, ApcuReserve, MaxAge, MaxRows, ExcludeTables
type: generated
generated: exp:help:examples
---
# querycache.ini [QueryCacheSettings]

Der Block `[QueryCacheSettings]` von `settings/querycache.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/querycache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/querycache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/querycache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/querycache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Mode

> off      nichts wird behalten (der Standard)  
> request  ein Merkzettel für die Dauer einer Anfrage: Ein identisches SELECT wird  
>          in derselben Anfrage nicht zweimal ausgeführt  
> shared   der Merkzettel und über Anfragen hinweg behaltene Ergebnisse (siehe Storage); jeder  
>          andere Prozess (Skripte, der Administrations-Host, Velocity) macht sie  
>          über die gemeinsame Zustandsdatei in var/<site>/cache/querycache/ ungültig  

Standard: `off`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
Mode=off
*/ ?>
```

## Storage

> Wo der gemeinsame Modus Ergebnisse über Anfragen hinweg behält:  
> auto  APCu, wo dieser PHP-Prozess es hat, sonst Dateien (der Standard); auf der  
>       Kommandozeile (Cronjobs, Skripte) ohne APCu nur der Merkzettel der Anfrage  
> apcu  nur APCu; ohne APCu arbeitet shared wie request  
> file  Dateien in var/<site>/cache/querycache/entries/, eine pro Ergebnis, auch  
>       mit APCu: Jeder Webserver, der var/ einhängt (ein Cluster auf NFS oder einem  
>       SAN), teilt sie. Der Cronjob-Teil cache\_cleanup  
>       (cronjobs/querycache\_cleanup.php) entfernt die Dateien, die älter als MaxAge sind;  
>       das Leeren des Caches entfernt sie ebenfalls.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
Storage=auto
*/ ?>
```

## ApcuReserve

> APCu ist ein Speicher für jede Website des PHP-FPM-Pools. Voll mit gültigen  
> Einträgen leert es sich vollständig: Jeder andere Cache und Zähler darin  
> geht verloren. Ist weniger als dieser Anteil von APCu frei (Prozent), fügt der gemeinsame Modus  
> APCu keine Ergebnisse mehr hinzu und behält sie nur für die Anfrage, bis Einträge  
> ablaufen (unter der Reserve löscht der Cache seine abgelaufenen Einträge aus APCu,  
> einmal pro Minute für den Pool). 0 hört nie auf. Nur mit dem Speicher APCu.  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
ApcuReserve=25
*/ ?>
```

## MaxAge

> Längste Lebensdauer eines Ergebnisses, auch wenn kein Schreibzugriff seine Tabellen berührt hat, in Sekunden:  
> die Grenze für einen Schreibzugriff außerhalb von Exponential (ein anderes Programm, eine von Hand ausgeführte  
> Anweisung), den der Cache nicht sehen kann.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
MaxAge=300
*/ ?>
```

## MaxRows

> Ergebnisse mit mehr Zeilen werden nicht behalten.  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
MaxRows=5000
*/ ?>
```

## ExcludeTables

> Tabellen, deren SELECTs nie gecacht werden.  

Standard: eine Liste mit 3: `ezsession`, `ezpending_actions`, `ezcollab_notification_rule`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
# ein leeres "ExcludeTables[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludeTables[]
ExcludeTables[]=ezsession
ExcludeTables[]=ezpending_actions
ExcludeTables[]=ezcollab_notification_rule
*/ ?>
```

## Siehe auch

- [Alle Blöcke von querycache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/querycache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
