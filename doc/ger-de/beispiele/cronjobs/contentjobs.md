---
title: Cronjob contentjobs.php
audience: developer, admin
category: examples
summary: Resume content jobs whose worker died and start queued jobs nobody started.
keywords: Teil, contentjobs.php, contentjobs, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob contentjobs.php

Resume content jobs whose worker died and start queued jobs nobody started.

Einstiegspunkt `cronjobs/contentjobs.php`, Code in `kernel/private/classes/cronjobs/contentjobs.php`.

> The cronjob part contentjobs (cronjobs/contentjobs.php, frequent group): starts the content jobs that need a  
> worker and have none — a running job whose worker died (kill -9, a reboot, a memory limit) and a queued job  
> nobody started within content.ini \[ContentJobSettings\] QueuedGrace seconds (the spawn from the web request  
> failed) — and removes finished jobs older than KeepDays.  
>
> The worker is started detached (bin/php/expcontentjob.php run <id>), as from the web request, so a long job  
> never holds up the other cronjob parts; started as root, it becomes the site user. A job resumed again and  
> again without finishing a batch is failed by the worker after MaxAttempts starts instead of looping.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`, dem Teil `contentjobs`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php contentjobs
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=contentjobs.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/contentjobs.php`, `kernel/private/classes/cronjobs/contentjobs.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
