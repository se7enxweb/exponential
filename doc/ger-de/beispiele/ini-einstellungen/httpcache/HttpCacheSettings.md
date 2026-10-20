---
title: httpcache.ini [HttpCacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HttpCacheSettings] in httpcache.ini: Enabled, CachedSiteAccesses, MaxAge, StaleWhileRevalidate, TagHeader, APCu, MaxBodySize, QueryStringParameters ..."
keywords: Einstellung, httpcache.ini, HttpCacheSettings, INI, setting, Enabled, CachedSiteAccesses, MaxAge, StaleWhileRevalidate, TagHeader, APCu, MaxBodySize, QueryStringParameters, ContentChangePurges
type: generated
generated: exp:help:examples
---
# httpcache.ini [HttpCacheSettings]

Der Block `[HttpCacheSettings]` von `settings/httpcache.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/httpcache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/httpcache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/httpcache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/httpcache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> enabled oder disabled. Der frühe Ausstieg und Velocity liefern nur aus, was existiert, und  
> solange dies disabled ist, wird nichts gespeichert.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
Enabled=disabled
*/ ?>
```

## CachedSiteAccesses

> Siteaccesses, deren Seiten gecacht werden. Erkannt über den Host, über die URI (URIMatchType  
> element oder map) oder über Host und URI (host\_uri); ein Siteaccess, der über eine andere  
> Regel gefunden wird (port, servervar, index, die Typen text und regexp), wird nie früh  
> ausgeliefert, weil sich das nicht wissen lässt, bevor der Kernel startet.  

Standard: eine Liste mit 1: `site`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
# ein leeres "CachedSiteAccesses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedSiteAccesses[]
CachedSiteAccesses[]=site
*/ ?>
```

## MaxAge

> Sekunden, die eine gecachte Seite höchstens ausgeliefert werden darf, auch wenn nichts sie gelöscht hat.  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
MaxAge=3600
*/ ?>
```

## StaleWhileRevalidate

> Sekunden, die eine abgelaufene (nicht gelöschte) Seite noch ausgeliefert werden darf, als STALE markiert, während  
> eine Anfrage sie neu rendert. 0 liefert nichts Abgelaufenes aus.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
StaleWhileRevalidate=60
*/ ?>
```

## TagHeader

> Die Lösch-Tags jeder Seite (ihre Knoten, Objekte und Layouts) in einem Antwort-  
> Header, für einen vorgelagerten löschenden Proxy, der sie liest, oder um sie beim  
> Debuggen zu sehen: xkey (Varnish-xkey-vmod), Surrogate-Key (Fastly), Cache-Tag  
> (Cloudflare) oder ein anderer Header-Name. disabled (der Standard) sendet keinen:  
> Die Tags nennen interne IDs, also sollte sie sonst kein Besucher sehen.  
> ProxyHeaders=enabled, die Einstellung vor dieser, bedeutet weiterhin xkey.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
TagHeader=disabled
*/ ?>
```

## APCu

> Häufig genutzte Einträge in APCu vor den Dateien halten (wenn APCu nutzbar ist).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
APCu=enabled
*/ ?>
```

## MaxBodySize

> Größter Seiteninhalt in Bytes, der gespeichert wird.  

Standard: `2097152`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
MaxBodySize=2097152
*/ ?>
```

## QueryStringParameters

> Query-Parameter, die eine gecachte Seite tragen darf. Eine Anfrage mit einem anderen  
> Parameter wird nicht gecacht (jede zufällige Query-Zeichenkette würde einen Eintrag hinzufügen).  
> Exponential übergibt View-Parameter im Pfad, /(offset)/10.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
# ein leeres "QueryStringParameters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
QueryStringParameters[]
*/ ?>
```

## ContentChangePurges

> Was eine Inhaltsänderung löscht. Eine ganze Seite enthält auch, was der View-Cache  
> nie enthielt (die Menüs des Pagelayouts, Layout-Blöcke, die Abfragen ausführen), und die  
> Tags einer Änderung können all das noch nicht benennen.  
>   all  - jede gecachte Seite (sicher; der Standard)  
>   tags - nur Seiten, die mit den geänderten Objekten, ihren Knoten und Elternknoten getaggt sind,  
>          und Seiten mit Layout-Blöcken, die Abfragen ausführen (ihre Einträge sind getaggt)  
> Änderungen im Layout-Editor löschen immer jede Seite.  

Standard: `all`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/httpcache.ini.append.php
[HttpCacheSettings]
ContentChangePurges=all
*/ ?>
```

## Siehe auch

- [Alle Blöcke von httpcache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/httpcache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
