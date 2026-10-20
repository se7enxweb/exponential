---
title: Cronjob querycache_cleanup.php
audience: developer, admin
category: examples
summary: Remove the result files of the SQL query cache that are older than MaxAge or from before the last clear.
keywords: Teil, querycache_cleanup.php, querycache_cleanup, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob querycache_cleanup.php

Remove the result files of the SQL query cache that are older than MaxAge or from before the last clear.

Einstiegspunkt `cronjobs/querycache_cleanup.php`, Code in `kernel/private/classes/cronjobs/querycache_cleanup.php`.

> The code of cronjobs/querycache\_cleanup.php. The file cronjobs/querycache\_cleanup.php is one call to it.  
> Guide: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  
>
> Removes the result files of the SQL query cache (querycache.ini Storage=file, or auto without APCu) that can no  
> longer be answered: older than MaxAge or from before the last clear. A lookup never answers them anyway; without  
> this run they stay on disk until the same statement is stored again.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `cache_cleanup`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php cache_cleanup
./console exp:runcronjobs cache_cleanup
# nur dieses Skript ausführen
php runcronjobs.php querycache_cleanup
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-cache_cleanup]
Scripts[]=querycache_cleanup.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/querycache_cleanup.php`, `kernel/private/classes/cronjobs/querycache_cleanup.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
