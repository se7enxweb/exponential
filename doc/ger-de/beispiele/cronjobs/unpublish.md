---
title: Cronjob unpublish.php
audience: developer, admin
category: examples
summary: Führt geplante Aktionen zum Zurückziehen von Inhaltsobjekten aus.
keywords: Teil, unpublish.php, unpublish, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob unpublish.php

Führt geplante Aktionen zum Zurückziehen von Inhaltsobjekten aus.

Einstiegspunkt `cronjobs/unpublish.php`, Code in `kernel/private/classes/cronjobs/unpublish.php`.

> Der Code von cronjobs/unpublish.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/unpublish.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Standardteil (`[CronjobSettings]`, `runcronjobs.php` ohne Teil).

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php
./console exp:runcronjobs
# nur dieses Skript ausführen
php runcronjobs.php unpublish
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=unpublish.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/unpublish.php`, `kernel/private/classes/cronjobs/unpublish.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
