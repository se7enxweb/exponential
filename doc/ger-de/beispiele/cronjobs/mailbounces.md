---
title: Cronjob mailbounces.php
audience: developer, admin
category: examples
summary: "Das Postfach für Rückläufer: Hard Bounces und Spam-Beschwerden kommen auf die Sperrliste (mailpreferences.ini [BounceSettings])."
keywords: Teil, mailbounces.php, mailbounces, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob mailbounces.php

Das Postfach für Rückläufer: Hard Bounces und Spam-Beschwerden kommen auf die Sperrliste (mailpreferences.ini [BounceSettings]).

Einstiegspunkt `cronjobs/mailbounces.php`, Code in `kernel/private/classes/cronjobs/mailbounces.php`.

> Der Code von cronjobs/mailbounces.php.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`, dem Teil `mailbounces`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php mailbounces
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=mailbounces.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/mailbounces.php`, `kernel/private/classes/cronjobs/mailbounces.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
