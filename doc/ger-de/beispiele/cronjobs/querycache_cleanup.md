---
title: Cronjob querycache_cleanup.php
audience: developer, admin
category: examples
summary: Entfernt die Ergebnisdateien des SQL-Abfrage-Caches, die älter als MaxAge sind oder von vor dem letzten Leeren stammen.
keywords: Teil, querycache_cleanup.php, querycache_cleanup, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob querycache_cleanup.php

Entfernt die Ergebnisdateien des SQL-Abfrage-Caches, die älter als MaxAge sind oder von vor dem letzten Leeren stammen.

Einstiegspunkt `cronjobs/querycache_cleanup.php`, Code in `kernel/private/classes/cronjobs/querycache_cleanup.php`.

> Der Code von cronjobs/querycache\_cleanup.php. Die Datei cronjobs/querycache\_cleanup.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  
>
> Entfernt die Ergebnisdateien des SQL-Abfrage-Caches (querycache.ini Storage=file, oder auto ohne APCu), die nicht mehr  
> beantwortet werden können: älter als MaxAge oder von vor dem letzten Leeren. Eine Abfrage beantwortet sie ohnehin nie; ohne  
> diesen Lauf bleiben sie auf der Festplatte, bis dieselbe Anweisung erneut gespeichert wird.  

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
