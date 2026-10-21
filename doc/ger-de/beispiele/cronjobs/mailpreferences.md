---
title: Cronjob mailpreferences.php
audience: developer, admin
category: examples
summary: "Aufbewahrung der E-Mail-Einstellungen: Zeilen des Einwilligungsprotokolls von Personen, die nicht mehr da sind, abgelaufene Bestätigungen, alte Einträge der Sperrliste."
keywords: Teil, mailpreferences.php, mailpreferences, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob mailpreferences.php

Aufbewahrung der E-Mail-Einstellungen: Zeilen des Einwilligungsprotokolls von Personen, die nicht mehr da sind, abgelaufene Bestätigungen, alte Einträge der Sperrliste.

Einstiegspunkt `cronjobs/mailpreferences.php`, Code in `kernel/private/classes/cronjobs/mailpreferences.php`.

> Der Code von cronjobs/mailpreferences.php.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `infrequent`, dem Teil `mailpreferences`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php infrequent
./console exp:runcronjobs infrequent
# nur dieses Skript ausführen
php runcronjobs.php mailpreferences
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=mailpreferences.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/mailpreferences.php`, `kernel/private/classes/cronjobs/mailpreferences.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
