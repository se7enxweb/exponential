---
title: velocity.ini [CacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheSettings] in velocity.ini: Enabled, DefaultTtl, Dir, APCu, APCuMaxSize, SweepEvery, SweepMaxAge, FileMode ..."
keywords: Einstellung, velocity.ini, CacheSettings, INI, setting, Enabled, DefaultTtl, Dir, APCu, APCuMaxSize, SweepEvery, SweepMaxAge, FileMode, DirMode, SkipCookies, StaleWhileRevalidate, NotFoundSeconds, HonorRequestNoCache, MinifyHtml, MiddleOutCompression
type: generated
generated: exp:help:examples
---
# velocity.ini [CacheSettings]

Der Block `[CacheSettings]` von `settings/velocity.ini`, mit 15 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Der Antwort-Cache des Servers, vor den Workern. Einen Treffer beantwortet  
> der Elternprozess: kein Fork, kein PHP, keine Datenbank. Gemessen auf dieser  
> Installation, anonyme Startseite: etwa 3.500 Anfragen/s aus dem Cache  
> gegenüber 27 gerenderten.  
>
> Die Einstellungen weiter unten, die früher in \[ServerSettings\] als  
> CacheSkipCookies, CacheStaleWhileRevalidate, CacheNotFoundSeconds,  
> MinifyCachedHtml und MiddleOutCompression standen, funktionieren dort weiterhin; ein Wert hier  
> gewinnt.  
>
> Braucht qbix-webserver v0.0.4.26 oder später: Ältere Versionen erkannten das Sitzungs-  
> cookie an seinem genauen Namen, erkannten eZSESSID<digest> nie und lieferten die Seiten  
> eines angemeldeten Besuchers an alle aus.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
Enabled=enabled
*/ ?>
```

## DefaultTtl

> Sekunden, die eine anonyme Seite behalten wird. Exponential sendet  
> Cache-Control: no-cache, must-revalidate, was der Server nicht als  
> Ablehnung behandelt; eine Seite wird also nur gespeichert, wenn sie eine Lebensdauer hat, ihr  
> eigenes max-age oder dieses. 0 cacht nur Antworten, die ihre eigene mitbringen.  
>
> Der Preis: Ein Besucher sieht eine Seite eventuell bis zu so alt, und eine Seite, die sich  
> zwischen anonymen Besuchern ohne ein sie unterscheidendes Cookie unterscheidet, würde  
> zwischen ihnen geteilt. Angemeldete Besucher werden nie aus dem Cache bedient.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
DefaultTtl=30
*/ ?>
```

## Dir

> Wo Einträge gehalten werden, die für den gemeinsamen Speicher zu groß sind. Relative Pfade gelten  
> ab dem Wurzelverzeichnis der Installation. Sich selbst überlassen, würde der Server sie in  
> files/cache/reverse des Verzeichnisses über dem Dokumentenverzeichnis legen.  

Standard: `var/cache/qbix-reverse`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
Dir=var/cache/qbix-reverse
*/ ?>
```

## APCu

> Kleine Antworten in APCu, gemeinsam für jeden Worker; größere auf der Festplatte.  
> APCuMaxSize ist die Größengrenze in Bytes, 0 belässt die 65536 des Servers.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
APCu=enabled
*/ ?>
```

## APCuMaxSize

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
APCuMaxSize=0
*/ ?>
```

## SweepEvery

> Abgelaufene Einträge von der Festplatte räumen, alle SweepEvery Sekunden (0 schaltet  
> es ab). SweepMaxAge entfernt alles, was älter ist, unabhängig von seiner eigenen  
> Lebensdauer; ohne es bleibt eine Antwort mit max-age=31536000 ein Jahr lang.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
SweepEvery=300
*/ ?>
```

## SweepMaxAge

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
SweepMaxAge=86400
*/ ?>
```

## FileMode

> Berechtigungen, oktal, wie in \[LogSettings\]. Eine gecachte Seite ist das, was ein anonymer  
> Besucher sieht, aber sonst muss sie auf dem Rechner niemand lesen.  

Standard: `0640`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
FileMode=0640
*/ ?>
```

## DirMode

Standard: `0750`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
DirMode=0750
*/ ?>
```

## SkipCookies

> Welche Cookies bedeuten „diese Antwort ist persönlich, nicht cachen“, als  
> Präfix verglichen. Leer bedeutet das Sitzungs-Cookie plus is\_logged\_in, das der Kernel  
> genau zu diesem Zweck für angemeldete Besucher setzt. Der Name des Sitzungs-Cookies  
> folgt \[Session\] SessionNameHandler in site.ini: Mit "default" benennt PHP  
> es (PHPSESSID); mit "custom" ist es SessionNamePrefix, das als  
> Präfix auch <prefix><digest> jedes Siteaccess abdeckt.  
>
> Um sie selbst zu benennen, führen Sie jedes auf; eine Liste ersetzt den Standard:  
>   SkipCookies\[\]  
>   SkipCookies\[\]=PHPSESSID  
>   SkipCookies\[\]=is\_logged\_in  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
SkipCookies=
*/ ?>
```

## StaleWhileRevalidate

> Sekunden, die eine abgelaufene Seite noch ausgeliefert werden darf, während eine Anfrage den  
> Ersatz rendert, damit ein Ablauf nicht alle Besucher auf einmal zu PHP schickt.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
StaleWhileRevalidate=60
*/ ?>
```

## NotFoundSeconds

> Sekunden, die eine 404 gemerkt wird, damit ein Crawler, der tote Links abläuft, nicht für jeden den  
> ganzen Renderweg ausführt. 0 (der Standard) merkt sich keine: Jede 404  
> erreicht PHP, wie unter Apache, sodass 404.log und der Anfrage-Klassifizierer  
> (security.log) jede sehen; eine aus dem Cache beantwortete 404 wurde höchstens einmal pro  
> Minute gesehen. Eine 404, die der Kernel als private, no-store markiert, wird ohnehin nie behalten.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
NotFoundSeconds=0
*/ ?>
```

## HonorRequestNoCache

> Eine Anfrage mit Cache-Control: no-cache (oder Pragma: no-cache) wird an  
> PHP geschickt, statt aus dem Cache beantwortet zu werden, und die Antwort wird erneut gespeichert.  
> Ein gewöhnliches Neuladen im Browser (max-age=0) wird weiterhin aus dem Cache beantwortet.  
> Braucht exponential-velocity mit Q.web.cache.honorRequestNoCache; ältere  
> Versionen ignorieren es.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
HonorRequestNoCache=enabled
*/ ?>
```

## MinifyHtml

> Die Einrückung der Templates in gespeicherten Seiten zusammenfassen: 27 % kleinere Dokumente.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
MinifyHtml=enabled
*/ ?>
```

## MiddleOutCompression

> Gespeicherte Einträge gegen ein gemeinsames Wörterbuch komprimieren. Gemessen und aus gelassen:  
> Einträge werden schon gzip-komprimiert gespeichert, und ein Wörterbuch kann das nicht verkleinern.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
MiddleOutCompression=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
