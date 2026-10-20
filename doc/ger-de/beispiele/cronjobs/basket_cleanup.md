---
title: Cronjob basket_cleanup.php
audience: developer, admin
category: examples
summary: Remove abandoned shopping baskets older than the configured threshold.
keywords: Teil, basket_cleanup.php, basket_cleanup, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob basket_cleanup.php

Remove abandoned shopping baskets older than the configured threshold.

Einstiegspunkt `cronjobs/basket_cleanup.php`, Code in `kernel/private/classes/cronjobs/basket_cleanup.php`.

> The code of cronjobs/basket\_cleanup.php, moved into a class (#207 stage 1). The file cronjobs/basket\_cleanup.php is one call to it.  
> Guide: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `infrequent`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php infrequent
./console exp:runcronjobs infrequent
# nur dieses Skript ausführen
php runcronjobs.php basket_cleanup
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=basket_cleanup.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/basket_cleanup.php`, `kernel/private/classes/cronjobs/basket_cleanup.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
