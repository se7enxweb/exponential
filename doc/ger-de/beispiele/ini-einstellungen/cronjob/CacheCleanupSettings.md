---
title: cronjob.ini [CacheCleanupSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheCleanupSettings] in cronjob.ini: MaxAge, IterationSleep, RenameAfterClear"
keywords: Einstellung, cronjob.ini, CacheCleanupSettings, INI, setting, MaxAge, IterationSleep, RenameAfterClear
type: generated
generated: exp:help:examples
---
# cronjob.ini [CacheCleanupSettings]

Der Block `[CacheCleanupSettings]` von `settings/cronjob.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxAge

> Seconds after which a file is removed whatever its expiry. A cold entry that  
> was still valid costs one regeneration. 0 removes only expired files.  

Standard: `172800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CacheCleanupSettings]
MaxAge=172800
*/ ?>
```

## IterationSleep

> Microseconds to sleep after every 1000 files, to go easy on the disk. 0 = none.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CacheCleanupSettings]
IterationSleep=0
*/ ?>
```

## RenameAfterClear

> After a global clear of a cache, move its whole directory aside in one rename  
> and delete it from there, instead of looking at every file. Entries stored  
> between the clear and the run are lost and generated again. disabled removes  
> file by file. A clear in the administration interface or with  
> bin/php/ezcache.php already moves the directory aside itself (site.ini  
> \[FileSettings\] RenameExpiredCaches) and records that here, so this run does  
> not move it a second time.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CacheCleanupSettings]
RenameAfterClear=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
