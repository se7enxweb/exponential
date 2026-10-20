---
title: Cronjob cachecleanup.php
audience: developer, admin
category: examples
summary: Remove expired and old view cache and cache-block files from disk.
keywords: Teil, cachecleanup.php, cachecleanup, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob cachecleanup.php

Remove expired and old view cache and cache-block files from disk.

Einstiegspunkt `cronjobs/cachecleanup.php`, Code in `kernel/private/classes/cronjobs/cachecleanup.php`.

> The code of cronjobs/cachecleanup.php, moved into a class (#207 stage 1). The file cronjobs/cachecleanup.php is one call to it.  
> Guide: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `cache_cleanup`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php cache_cleanup
./console exp:runcronjobs cache_cleanup
# nur dieses Skript ausführen
php runcronjobs.php cachecleanup
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-cache_cleanup]
Scripts[]=cachecleanup.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/cachecleanup.php`, `kernel/private/classes/cronjobs/cachecleanup.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
