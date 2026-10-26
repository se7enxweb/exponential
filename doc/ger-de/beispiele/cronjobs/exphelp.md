---
title: Cronjob exphelp.php
audience: developer, admin
category: examples
summary: "Help maintained as content: removes old answers to 'Was this helpful?' and records the DITA export of the help in git when exphelp.ini [ContentSettings] DitaHistoryDirectory is set."
keywords: Teil, exphelp.php, exphelp, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob exphelp.php

Help maintained as content: removes old answers to "Was this helpful?" and records the DITA export of the help in git when exphelp.ini [ContentSettings] DitaHistoryDirectory is set.

Einstiegspunkt `cronjobs/exphelp.php`, Code in `kernel/private/classes/cronjobs/exphelp.php`.

> The code of cronjobs/exphelp.php.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `infrequent`, dem Teil `exphelp`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php infrequent
./console exp:runcronjobs infrequent
# nur dieses Skript ausführen
php runcronjobs.php exphelp
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=exphelp.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/exphelp.php`, `kernel/private/classes/cronjobs/exphelp.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
