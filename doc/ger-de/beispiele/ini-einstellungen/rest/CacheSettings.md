---
title: rest.ini [CacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheSettings] in rest.ini: ApplicationCache, ApplicationCacheDefault, DefaultCacheTTL, RouteApcCache, RouteApcCacheTTL"
keywords: Einstellung, rest.ini, CacheSettings, INI, setting, ApplicationCache, ApplicationCacheDefault, DefaultCacheTTL, RouteApcCache, RouteApcCacheTTL
type: generated
generated: exp:help:examples
---
# rest.ini [CacheSettings]

Der Block `[CacheSettings]` von `settings/rest.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ApplicationCache

> Globaler Schalter zum Aktivieren/Deaktivieren des REST-Anwendungs-Caches  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
ApplicationCache=enabled
*/ ?>
```

## ApplicationCacheDefault

> Standardwert, wenn für Ihren Controller/Ihre Aktion kein eigener Wert definiert ist  
> Wenn aktiviert, wird das Ergebnis jedes Dienstaufrufs gecacht  
> Sie können das mit Einstellungen für Ihren Controller/Ihre Aktion verfeinern  
> Das System sucht einen Block \[<controllerClass>\_<action>\_CacheSettings\], um zu prüfen,  
> ob der Cache verwendet werden darf und, wenn ja, welche TTL  
> Wird dieser Block nicht gefunden, sucht das System auf Ebene des Controllers,  
> also nach einem Block \[<controllerClass>\_CacheSettings\].  
> Weitere Informationen im Beispielblock unten  
> Im Grunde erlaubt diese Einstellung, den Cache für Ihre Controller/Aktionen einzeln zu aktivieren  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
ApplicationCacheDefault=enabled
*/ ?>
```

## DefaultCacheTTL

> Standard-TTL auf 10 Minuten setzen, in Sekunden  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
DefaultCacheTTL=600
*/ ?>
```

## RouteApcCache

> Beispiel für die Aktion "viewContent" in der Controller-Klasse "ezpRestContentController"  
> \[ezpRestContentController\_viewContent\_CacheSettings\]  
> ApplicationCache=enabled  
> CacheTTL=3600  
> Unten ein Beispiel für alle Aktionen der Controller-Klasse "ezpRestContentController"  
> \[ezpRestContentController\_CacheSettings\]  
> ApplicationCache=enabled  
> CacheTTL=1200  
> Schalter zum Aktivieren/Deaktivieren des Routen-Caches mit APC  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
RouteApcCache=enabled
*/ ?>
```

## RouteApcCacheTTL

> TTL des APC-Routen-Caches, in Sekunden  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[CacheSettings]
RouteApcCacheTTL=3600
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
