---
title: Cronjob explogrotate.php
audience: developer, admin
category: examples
summary: "Logs: rotiert die Logs über ihrer MaxSize, bereinigt und komprimiert ihre Archive (aus, solange kein Teil ihn ausführt)."
keywords: Teil, explogrotate.php, explogrotate, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob explogrotate.php

Logs: rotiert die Logs über ihrer MaxSize, bereinigt und komprimiert ihre Archive (aus, solange kein Teil ihn ausführt).

Einstiegspunkt `cronjobs/explogrotate.php`, Code in `kernel/private/classes/cronjobs/explogrotate.php`.

> Der Code von cronjobs/explogrotate.php: der Cronjob-Teil der Log-Rotation (\[CronjobPart-logs\], in keinem Standardteil, er  
> läuft also nur, wenn eine Website ihn einem Teil hinzufügt, den sie ausführt, oder php runcronjobs.php logs aufruft). Anleitung:  
> doc/eng-us/features/6.0/log-rotation.md, "As a cronjob part".  
>
> Jeder Lauf, über expLogManager: rotiert jedes Log über seiner MaxSize (logviewer.ini \[RotationSettings\] MaxSize\[<key>\],  
> sonst site.ini \[FileSettings\] LogFileMaxSize), bereinigt die Archive über Keep und MaxAgeDays hinaus und komprimiert die  
> unkomprimierten Archive ab .2 der Logs, deren Compress aktiviert ist. Jede Änderung wird im Audit-Trail festgehalten.  

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
