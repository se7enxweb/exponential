---
title: site.ini [RequestLogSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RequestLogSettings] in site.ini: NotFoundLog, NotFoundLogFile, MaskIP, MaxURILength"
keywords: Einstellung, site.ini, RequestLogSettings, INI, setting, NotFoundLog, NotFoundLogFile, MaskIP, MaxURILength
type: generated
generated: exp:help:examples
---
# site.ini [RequestLogSettings]

Der Block `[RequestLogSettings]` von `settings/site.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## NotFoundLog

> Eine Anfrage nach einer Seite, die es nicht gibt (404, 410), wird als eine Zeile  
> in ein eigenes Log neben error.log geschrieben, mit dem Grund, den der Kernel gefunden hat: ein  
> nicht definiertes Modul oder eine nicht definierte Ansicht, ein Modul oder eine Ansicht, die dieser Siteaccess abschaltet, ein  
> Knoten oder Objekt, das nicht da ist. Eine Anfrage, die der Anfrage-Klassifizierer als  
> Sondierung oder Angriff einstuft, wird stattdessen in security.log geschrieben (requestclassifier.ini),  
> nie in beide. Antworten 401 und 403 bleiben notices in notice.log.  
> enabled (der Standard): 404 gehen nach NotFoundLogFile und nicht mehr nach error.log,  
> das die echten Fehler behält. disabled: error.log wie bisher.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RequestLogSettings]
NotFoundLog=enabled
*/ ?>
```

## NotFoundLogFile

> Die Datei im Log-Verzeichnis (var/log oder das Log-Verzeichnis der Website mit  
> \[FileSettings\] UseGlobalLogDir=disabled). Wird wie die anderen Logs rotiert.  

Standard: `404.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RequestLogSettings]
NotFoundLogFile=404.log
*/ ?>
```

## MaskIP

> enabled (der Standard): Die Adresse des Clients wird mit verborgenem Host-  
> teil geschrieben (203.0.x.x, 2001:db8:x:x), nie vollständig.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RequestLogSettings]
MaskIP=enabled
*/ ?>
```

## MaxURILength

> Die längste geschriebene Adresse; eine längere wird abgeschnitten und endet auf "...". Die  
> Werte der Query werden immer als \*\*\* geschrieben.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RequestLogSettings]
MaxURILength=300
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
