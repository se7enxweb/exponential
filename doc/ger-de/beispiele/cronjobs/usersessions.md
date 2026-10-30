---
title: Cronjob usersessions.php
audience: developer, admin
category: examples
summary: "Angemeldete Sitzungen ('Ihre Geräte'): beendet die Sitzungen, die für [Session] SessionTimeout nicht benutzt wurden, und entfernt beendete nach [UserSessionSettings] RetentionDays."
keywords: Teil, usersessions.php, usersessions, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob usersessions.php

Angemeldete Sitzungen ("Ihre Geräte"): beendet die Sitzungen, die für [Session] SessionTimeout nicht benutzt wurden, und entfernt beendete nach [UserSessionSettings] RetentionDays.

Einstiegspunkt `cronjobs/usersessions.php`, Code in `kernel/private/classes/cronjobs/usersessions.php`.

> Der Code von cronjobs/usersessions.php.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `infrequent`, dem Teil `usersessions`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php infrequent
./console exp:runcronjobs infrequent
# nur dieses Skript ausführen
php runcronjobs.php usersessions
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=usersessions.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/usersessions.php`, `kernel/private/classes/cronjobs/usersessions.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
