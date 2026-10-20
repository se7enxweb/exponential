---
title: Cronjob contentcheck.php
audience: developer, admin
category: examples
summary: Build the reference index of the content checks (unused assets), for installations where a page may not build it (contentcheck.ini BuildOnRequest=false).
keywords: Teil, contentcheck.php, contentcheck, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob contentcheck.php

Build the reference index of the content checks (unused assets), for installations where a page may not build it (contentcheck.ini BuildOnRequest=false).

Einstiegspunkt `cronjobs/contentcheck.php`, Code in `kernel/private/classes/cronjobs/contentcheck.php`.

> The code of cronjobs/contentcheck.php: builds the reference index of the content checks and keeps it in the cache  
> (expContentCheckReferences). Run it as the cronjob part contentcheck (php runcronjobs.php contentcheck) more often  
> than contentcheck.ini \[UnreferencedSettings\] IndexTTL when BuildOnRequest is false. With \[MissingFileSettings\]  
> CronjobReport=enabled it also counts the missing files and alias problems of the whole tree and writes the count  
> to the cronjob output (a warning when there are any); it never resolves anything.  
> Guide: doc/eng-us/features/6.0/content-checks.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `contentcheck`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php contentcheck
./console exp:runcronjobs contentcheck
# nur dieses Skript ausführen
php runcronjobs.php contentcheck
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-contentcheck]
Scripts[]=contentcheck.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/contentcheck.php`, `kernel/private/classes/cronjobs/contentcheck.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
