---
title: Cronjob staticpublish.php
audience: developer, admin
category: examples
summary: "Statisches Veröffentlichen: erneut pushen, was nicht gepusht werden konnte, die durch eine Veröffentlichung von Inhalten geänderten Seiten veröffentlichen (gebündelt) und die Ziele, deren Zeitplan fällig ist."
keywords: Teil, staticpublish.php, staticpublish, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob staticpublish.php

Statisches Veröffentlichen: erneut pushen, was nicht gepusht werden konnte, die durch eine Veröffentlichung von Inhalten geänderten Seiten veröffentlichen (gebündelt) und die Ziele, deren Zeitplan fällig ist.

Einstiegspunkt `cronjobs/staticpublish.php`, Code in `kernel/private/classes/cronjobs/staticpublish.php`.

> Der Cronjob-Teil staticpublish (cronjobs/staticpublish.php; \[CronjobPart-staticpublish\] in cronjob.ini): pusht die  
> Ziele des statischen Veröffentlichens erneut, deren Läufe nicht gepusht werden konnten (exp\_static\_run.push\_state pending)  
> (expStaticPublisher::retry(): der Branch mit jedem Commit und die Tags des Ziels, nie erzwungen); dann baut er für die  
> Ziele mit Triggers\[\]=publish die Seiten, die eine Veröffentlichung von Inhalten als veraltet markiert hat  
> (expStaticPublishQueue, sobald seit der letzten Änderung \[StaticPublishSettings\] PublishDebounce Sekunden vergangen  
> sind; über 200 Knoten ein vollständiger Build), und veröffentlicht sie; dann baut er vollständig und veröffentlicht die  
> Ziele mit Triggers\[\]=schedule, deren Schedule seit ihrem letzten geplanten Lauf zutraf (expStaticSchedule; gemerkt in  
> <WorkDir>/.schedule.json). Tut nichts, solange das statische Veröffentlichen abgeschaltet ist oder nichts wartet.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `staticpublish`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php staticpublish
./console exp:runcronjobs staticpublish
# nur dieses Skript ausführen
php runcronjobs.php staticpublish
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-staticpublish]
Scripts[]=staticpublish.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/staticpublish.php`, `kernel/private/classes/cronjobs/staticpublish.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
