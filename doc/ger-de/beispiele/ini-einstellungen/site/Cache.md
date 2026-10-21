---
title: site.ini [Cache]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Cache] in site.ini: CacheItems"
keywords: Einstellung, site.ini, Cache, INI, setting, CacheItems
type: generated
generated: exp:help:examples
---
# site.ini [Cache]

Der Block `[Cache]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CacheItems

> Array mit Cache-Einträgen zur Erweiterung von ezcache  
> Beispiel:  
> CacheItems\[\]=custom  
>
> \[Cache\_custom\]  
>
> \[optional\] Name des Cache-Eintrags; wenn nicht gesetzt, wird der Schlüssel (custom) in CamelCase verwendet  
> name=Custom cache  
> \[optional\] ID des Cache-Eintrags für die Kommandozeile; wenn nicht gesetzt, wird der Schlüssel (custom) verwendet  
> id=custom  
> \[optional\] Ob der Cache-Eintrag über den Cluster statt über den einfachen Datei-Handler geleert wird, Standard: false  
> isClustered=true  
> \[optional\] Tags, die das Leeren dieses Eintrags auslösen, Standard: leeres Array  
> tags\[\]=content  
> tags\[\]=custom  
> \[optional\] Wenn der Cache eZExpiryHandler verwendet, ist dies der Schlüssel für die Ablaufzeit  
> expiryKey=global-custom-cache  
> \[optional\] (bool) Ob dieser Cache aktiviert ist, Standard: true  
> enabled=false  
> \[optional\] Pfad, unter dem der Cache gespeichert wird, Verzeichnis oder Datei, wenn die Variablen class/purgeClass  
> nicht gesetzt sind; relativ zum aktuellen Cache-Verzeichnis  
> path=custom\_cache.php  
> \[optional\] eigene Funktion zum Leeren des Caches "<class>::clearCache()", die aufgerufen wird  
> class=eZSomeClassName  
> \[optional\] eigene Funktion zum endgültigen Löschen des Caches "<class>::purgeCache()", die aufgerufen wird  
> purgeClass=eZSomeClassName  

Standard: eine Liste mit 2: `rest`, `restRoutes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache]
# ein leeres "CacheItems[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CacheItems[]
CacheItems[]=rest
CacheItems[]=restRoutes
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
