---
title: Cronjob migrationpush.php
audience: developer, admin
category: examples
summary: "Migrationen: die Aufzeichnung der Läufe, deren Push gescheitert ist, erneut zum Remote des Migrations-Repositorys pushen."
keywords: Teil, migrationpush.php, migrationpush, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob migrationpush.php

Migrationen: die Aufzeichnung der Läufe, deren Push gescheitert ist, erneut zum Remote des Migrations-Repositorys pushen.

Einstiegspunkt `cronjobs/migrationpush.php`, Code in `kernel/private/classes/cronjobs/migrationpush.php`.

> Der Cronjob-Teil migrationpush (cronjobs/migrationpush.php; \[CronjobPart-migrationpush\] in cronjob.ini): pusht die  
> Commits und Tags der Migrationsläufe, deren Push gescheitert ist (exp\_migration\_run.push\_state pending), erneut zum  
> Remote des Migrations-Repositorys (expMigrationRepository::retryPushes()). Tut nichts, solange kein Lauf wartet.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `migrationpush`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php migrationpush
./console exp:runcronjobs migrationpush
# nur dieses Skript ausführen
php runcronjobs.php migrationpush
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-migrationpush]
Scripts[]=migrationpush.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/migrationpush.php`, `kernel/private/classes/cronjobs/migrationpush.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
