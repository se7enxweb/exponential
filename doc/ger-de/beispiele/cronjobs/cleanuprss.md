---
title: Cronjob cleanuprss.php
audience: developer, admin
category: examples
summary: Kürzt die vom RSS-Import angelegten Inhalte und behält die neuesten Einträge jedes Feeds.
keywords: Teil, cleanuprss.php, cleanuprss, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob cleanuprss.php

Kürzt die vom RSS-Import angelegten Inhalte und behält die neuesten Einträge jedes Feeds.

Einstiegspunkt `cronjobs/cleanuprss.php`, Code in `kernel/private/classes/cronjobs/cleanuprss.php`.

> Der Code von cronjobs/cleanuprss.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/cleanuprss.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `cleanuprss`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php cleanuprss
./console exp:runcronjobs cleanuprss
# nur dieses Skript ausführen
php runcronjobs.php cleanuprss
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-cleanuprss]
Scripts[]=cleanuprss.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/cleanuprss.php`, `kernel/private/classes/cronjobs/cleanuprss.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
