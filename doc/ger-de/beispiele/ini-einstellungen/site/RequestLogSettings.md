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

> A request for a page that does not exist (404, 410) is written as one line  
> in its own log beside error.log, with the reason the kernel found: an  
> undefined module or view, a module or view this siteaccess switches off, a  
> node or object that is not there. A request the request classifier names a  
> probe or attack is written to security.log instead (requestclassifier.ini),  
> never to both. Answers 401 and 403 stay notices in notice.log.  
> enabled (the default): 404s go to NotFoundLogFile and no longer to error.log,  
> which keeps the real faults. disabled: error.log as before.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RequestLogSettings]
NotFoundLog=enabled
*/ ?>
```

## NotFoundLogFile

> The file in the log directory (var/log, or the site's log directory with  
> \[FileSettings\] UseGlobalLogDir=disabled). Rotated as the other logs are.  

Standard: `404.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RequestLogSettings]
NotFoundLogFile=404.log
*/ ?>
```

## MaskIP

> enabled (the default): the address of the client is written with its host  
> part hidden (203.0.x.x, 2001:db8:x:x), never in full.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RequestLogSettings]
MaskIP=enabled
*/ ?>
```

## MaxURILength

> The longest address written; a longer one is cut and ends in "...". The  
> values of the query are always written as \*\*\*.  

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
