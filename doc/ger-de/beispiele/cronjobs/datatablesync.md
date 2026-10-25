---
title: Cronjob datatablesync.php
audience: developer, admin
category: examples
summary: Start the data table synchronisations that are due (scheduled and live, by their interval).
keywords: Teil, datatablesync.php, datatablesync, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob datatablesync.php

Start the data table synchronisations that are due (scheduled and live, by their interval).

Einstiegspunkt `cronjobs/datatablesync.php`, Code in `kernel/private/classes/cronjobs/datatablesync.php`.

> The cronjob part datatablesync (cronjobs/datatablesync.php, frequent group; doc/eng-us/features/6.0/data-tables.md,  
> "Synchronisation"): starts each enabled scheduled or live data table synchronisation whose next run is due, through  
> the transfer runner (expqueue, content jobs, or this process), and moves its next run by its interval. Does nothing  
> on an installation without the sync tables.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`, dem Teil `datatable`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php datatablesync
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=datatablesync.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/datatablesync.php`, `kernel/private/classes/cronjobs/datatablesync.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
