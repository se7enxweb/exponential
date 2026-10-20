---
title: Cronjob clusterpurge.php
audience: developer, admin
category: examples
summary: Purge expired cluster storage entries from the database backend.
keywords: Teil, clusterpurge.php, clusterpurge, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob clusterpurge.php

Purge expired cluster storage entries from the database backend.

Einstiegspunkt `cronjobs/clusterpurge.php`, Code in `kernel/private/classes/cronjobs/clusterpurge.php`.

> The code of cronjobs/clusterpurge.php, moved into a class (#207 stage 1). The file cronjobs/clusterpurge.php is one call to it.  
> Guide: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `cluster_maintenance`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php cluster_maintenance
./console exp:runcronjobs cluster_maintenance
# nur dieses Skript ausführen
php runcronjobs.php clusterpurge
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-cluster_maintenance]
Scripts[]=clusterpurge.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/clusterpurge.php`, `kernel/private/classes/cronjobs/clusterpurge.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
