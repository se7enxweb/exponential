---
title: Cronjob httpcache_cleanup.php
audience: developer, admin
category: examples
summary: Remove expired, purged and orphaned entries from the role-aware HTTP cache.
keywords: Teil, httpcache_cleanup.php, httpcache_cleanup, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob httpcache_cleanup.php

Remove expired, purged and orphaned entries from the role-aware HTTP cache.

Einstiegspunkt `cronjobs/httpcache_cleanup.php`, Code in `kernel/private/classes/cronjobs/httpcache_cleanup.php`.

> The code of cronjobs/httpcache\_cleanup.php, moved into a class (#207 stage 1). The file cronjobs/httpcache\_cleanup.php is one call to it.  
> Guide: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `cache_cleanup`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php cache_cleanup
./console exp:runcronjobs cache_cleanup
# nur dieses Skript ausführen
php runcronjobs.php httpcache_cleanup
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-cache_cleanup]
Scripts[]=httpcache_cleanup.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/httpcache_cleanup.php`, `kernel/private/classes/cronjobs/httpcache_cleanup.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
