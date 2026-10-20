---
title: logviewer.ini [RotationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RotationSettings] in logviewer.ini: MaxSize, Keep, Compress, MaxAgeDays"
keywords: Einstellung, logviewer.ini, RotationSettings, INI, setting, MaxSize, Keep, Compress, MaxAgeDays
type: generated
generated: exp:help:examples
---
# logviewer.ini [RotationSettings]

Der Block `[RotationSettings]` von `settings/logviewer.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxSize

> The limits of each log for rotation, pruning and compression on demand (./console exp:logs --rotate, --prune,  
> --compress; the log viewer's actions) and by the cronjob part "logs" (php runcronjobs.php logs; in no default part).  
> Guide: doc/eng-us/features/6.0/log-rotation.md. A log without an entry here takes site.ini \[FileSettings\]:  
>   MaxSize\[<key>\]     bytes; the cronjob part rotates a log bigger than this     (LogFileMaxSize, 2097152)  
>   Keep\[<key>\]        archives kept, .1 .. .N                                    (LogFileRotateCount, 3)  
>   Compress\[<key>\]    enabled | disabled: archives from .2 on compressed to .gz  (LogFileCompress, disabled)  
>   MaxAgeDays\[<key>\]  a prune removes archives older than this; 0: no age limit (LogFileMaxAge, 0)  
> The key is the log's key in the viewer's permalinks: error, debug, storage, site-storage ... e.g.  
>   Keep\[debug\]=1  
>   MaxSize\[debug\]=10485760  
> The request logs (doc/eng-us/features/6.0/request-logs.md) keep more and older archives than the kernel default. The writer  
> of 404.log and security.log still rotates at site.ini \[FileSettings\] LogFileMaxSize while it writes.  
> The size-triggered rotation of eZDebug and eZLog while a log is written uses the site.ini values only.  

Standard: ein Hash mit 3: `404=10485760`, `security=10485760`, `csp=10485760`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RotationSettings]
# ein leeres "MaxSize[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MaxSize[]
MaxSize[404]=10485760
MaxSize[security]=10485760
MaxSize[csp]=10485760
*/ ?>
```

## Keep

Standard: ein Hash mit 3: `404=5`, `security=5`, `csp=5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RotationSettings]
# ein leeres "Keep[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Keep[]
Keep[404]=5
Keep[security]=5
Keep[csp]=5
*/ ?>
```

## Compress

Standard: ein Hash mit 3: `404=enabled`, `security=enabled`, `csp=enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RotationSettings]
# ein leeres "Compress[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Compress[]
Compress[404]=enabled
Compress[security]=enabled
Compress[csp]=enabled
*/ ?>
```

## MaxAgeDays

Standard: ein Hash mit 3: `404=30`, `security=90`, `csp=30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RotationSettings]
# ein leeres "MaxAgeDays[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MaxAgeDays[]
MaxAgeDays[404]=30
MaxAgeDays[security]=90
MaxAgeDays[csp]=30
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
