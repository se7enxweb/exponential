---
title: Cronjob explogrotate.php
audience: developer, admin
category: examples
summary: "Logs: rotate the logs over their MaxSize, prune and compress their archives (off unless a part runs it)."
keywords: Teil, explogrotate.php, explogrotate, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob explogrotate.php

Logs: rotate the logs over their MaxSize, prune and compress their archives (off unless a part runs it).

Einstiegspunkt `cronjobs/explogrotate.php`, Code in `kernel/private/classes/cronjobs/explogrotate.php`.

> The code of cronjobs/explogrotate.php: the log rotation cronjob part (\[CronjobPart-logs\], in no default part, so it  
> runs only when a site adds it to a part it runs or calls php runcronjobs.php logs). Guide:  
> doc/eng-us/features/6.0/log-rotation.md, "As a cronjob part".  
>
> Each run, through expLogManager: rotates every log over its MaxSize (logviewer.ini \[RotationSettings\] MaxSize\[<key>\],  
> else site.ini \[FileSettings\] LogFileMaxSize), prunes the archives beyond Keep and MaxAgeDays, and compresses the plain  
> archives from .2 on of the logs whose Compress is enabled. Every change is recorded in the audit trail.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `logs`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php logs
./console exp:runcronjobs logs
# nur dieses Skript ausführen
php runcronjobs.php explogrotate
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-logs]
Scripts[]=explogrotate.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/explogrotate.php`, `kernel/private/classes/cronjobs/explogrotate.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
