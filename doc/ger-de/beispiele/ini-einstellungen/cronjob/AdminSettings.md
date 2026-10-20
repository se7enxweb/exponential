---
title: cronjob.ini [AdminSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminSettings] in cronjob.ini: ForbiddenParts, PhpCliPath, LogFile, ErrorFile"
keywords: Einstellung, cronjob.ini, AdminSettings, INI, setting, ForbiddenParts, PhpCliPath, LogFile, ErrorFile
type: generated
generated: exp:help:examples
---
# cronjob.ini [AdminSettings]

Der Block `[AdminSettings]` von `settings/cronjob.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ForbiddenParts

> Settings for the cronjobs console at Setup > Cronjobs, which launches a  
> cronjob part from the administration interface.  
> Parts that must not be launchable from a browser. Both defaults are  
> destructive and belong to an administrator on a shell: cluster\_maintenance  
> purges cluster storage, and unlock releases locks other processes hold.  

Standard: eine Liste mit 2: `cluster_maintenance`, `unlock`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
# ein leeres "ForbiddenParts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ForbiddenParts[]
ForbiddenParts[]=cluster_maintenance
ForbiddenParts[]=unlock
*/ ?>
```

## PhpCliPath

> Full path to the php command line binary used to run runcronjobs.php. Left  
> empty the usual locations are tried, which is enough on a normal installation.  
> It has to be a command line binary: the one running the web request cannot run  
> a script.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
PhpCliPath=
*/ ?>
```

## LogFile

> Where a launched cronjob's output and errors are written, relative to the var  
> directory of the siteaccess. The console reads these, and follows them as they  
> grow while a job runs.  

Standard: `cronjobs/output.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
LogFile=cronjobs/output.log
*/ ?>
```

## ErrorFile

Standard: `cronjobs/error.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
ErrorFile=cronjobs/error.log
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
