---
title: Cronjob notification.php
audience: developer, admin
category: examples
summary: Verarbeitet anstehende Benachrichtigungsereignisse und verschickt sie an die Abonnenten.
keywords: Teil, notification.php, notification, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob notification.php

Verarbeitet anstehende Benachrichtigungsereignisse und verschickt sie an die Abonnenten.

Einstiegspunkt `cronjobs/notification.php`, Code in `kernel/private/classes/cronjobs/notification.php`.

> Der Code von cronjobs/notification.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/notification.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`, dem Teil `notification`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php notification
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=notification.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/notification.php`, `kernel/private/classes/cronjobs/notification.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
