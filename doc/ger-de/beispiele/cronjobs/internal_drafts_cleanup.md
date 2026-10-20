---
title: Cronjob internal_drafts_cleanup.php
audience: developer, admin
category: examples
summary: Entfernt interne Entwürfe, die die konfigurierte Altersgrenze überschreiten.
keywords: Teil, internal_drafts_cleanup.php, internal_drafts_cleanup, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob internal_drafts_cleanup.php

Entfernt interne Entwürfe, die die konfigurierte Altersgrenze überschreiten.

Einstiegspunkt `cronjobs/internal_drafts_cleanup.php`, Code in `kernel/private/classes/cronjobs/internal_drafts_cleanup.php`.

> Der Code von cronjobs/internal\_drafts\_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/internal\_drafts\_cleanup.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Standardteil (`[CronjobSettings]`, `runcronjobs.php` ohne Teil).

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php
./console exp:runcronjobs
# nur dieses Skript ausführen
php runcronjobs.php internal_drafts_cleanup
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=internal_drafts_cleanup.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/internal_drafts_cleanup.php`, `kernel/private/classes/cronjobs/internal_drafts_cleanup.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
