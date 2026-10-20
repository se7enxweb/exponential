---
title: logfile.ini [AccessLogFileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AccessLogFileSettings] in logfile.ini: StorageDir, LogFileName, SitePrefix, PathPrefix"
keywords: Einstellung, logfile.ini, AccessLogFileSettings, INI, setting, StorageDir, LogFileName, SitePrefix, PathPrefix
type: generated
generated: exp:help:examples
---
# logfile.ini [AccessLogFileSettings]

Der Block `[AccessLogFileSettings]` von `settings/logfile.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/logfile.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logfile.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logfile.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logfile.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StorageDir

> Storage of the log file, for example /var/log/httpd  
> Do not add trailing slash  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=
*/ ?>
```

## LogFileName

> The name of log file, for example mytest.log  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
LogFileName=
*/ ?>
```

## SitePrefix

> If using site match by URL, the site may like http://siteurl/mysite and  
> apache log will start with for example mysite/content/view/full/node\_id. To  
> remove this prefix in apache log before analysis the log, add mysite to  
> SitePrefix. Example: SitePrefix\[\]=mysite  

Standard: eine Liste mit 1: ``.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
# ein leeres "SitePrefix[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SitePrefix[]
SitePrefix[]=
*/ ?>
```

## PathPrefix

> If any site accesses use the PathPrefix setting, enter them here  

Standard: eine Liste mit 1: ``.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
# ein leeres "PathPrefix[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PathPrefix[]
PathPrefix[]=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logfile.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logfile.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
