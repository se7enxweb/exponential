---
title: Cronjob indexcontent.php
audience: developer, admin
category: examples
summary: Indiziert anstehende Inhaltsänderungen in der Suchmaschine.
keywords: Teil, indexcontent.php, indexcontent, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob indexcontent.php

Indiziert anstehende Inhaltsänderungen in der Suchmaschine.

Einstiegspunkt `cronjobs/indexcontent.php`, Code in `kernel/private/classes/cronjobs/indexcontent.php`.

> Der Code von cronjobs/indexcontent.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/indexcontent.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Standardteil (`[CronjobSettings]`, `runcronjobs.php` ohne Teil).

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php
./console exp:runcronjobs
# nur dieses Skript ausführen
php runcronjobs.php indexcontent
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=indexcontent.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/indexcontent.php`, `kernel/private/classes/cronjobs/indexcontent.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
