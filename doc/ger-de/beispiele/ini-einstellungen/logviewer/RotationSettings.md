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

> Die Grenzen jedes Logs für Rotation, Bereinigung und Komprimierung auf Anforderung (./console exp:logs --rotate, --prune,  
> --compress; die Aktionen des Log-Viewers) und durch den Cronjob-Teil "logs" (php runcronjobs.php logs; in keinem Standardteil).  
> Anleitung: doc/eng-us/features/6.0/log-rotation.md. Ein Log ohne Eintrag hier nimmt site.ini \[FileSettings\]:  
>   MaxSize\[<key>\]     Bytes; der Cronjob-Teil rotiert ein Log, das größer ist     (LogFileMaxSize, 2097152)  
>   Keep\[<key>\]        behaltene Archive, .1 .. .N                                (LogFileRotateCount, 3)  
>   Compress\[<key>\]    enabled | disabled: Archive ab .2 zu .gz komprimiert       (LogFileCompress, disabled)  
>   MaxAgeDays\[<key>\]  eine Bereinigung entfernt ältere Archive; 0: keine Altersgrenze (LogFileMaxAge, 0)  
> Der Schlüssel ist der Schlüssel des Logs in den Permalinks des Viewers: error, debug, storage, site-storage ... z. B.  
>   Keep\[debug\]=1  
>   MaxSize\[debug\]=10485760  
> Die Anfrage-Logs (doc/eng-us/features/6.0/request-logs.md) behalten mehr und ältere Archive als der Standard des Kernels. Der Schreiber  
> von 404.log und security.log rotiert weiterhin bei site.ini \[FileSettings\] LogFileMaxSize, während er schreibt.  
> Die größengesteuerte Rotation von eZDebug und eZLog beim Schreiben eines Logs verwendet nur die Werte aus site.ini.  

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
