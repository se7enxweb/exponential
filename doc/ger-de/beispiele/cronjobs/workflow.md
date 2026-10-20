---
title: Cronjob workflow.php
audience: developer, admin
category: examples
summary: Verarbeitet anstehende Workflow-Ereignisse und bringt hängende Workflows weiter.
keywords: Teil, workflow.php, workflow, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob workflow.php

Verarbeitet anstehende Workflow-Ereignisse und bringt hängende Workflows weiter.

Einstiegspunkt `cronjobs/workflow.php`, Code in `kernel/private/classes/cronjobs/workflow.php`.

> Der Code von cronjobs/workflow.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/workflow.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php workflow
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=workflow.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/workflow.php`, `kernel/private/classes/cronjobs/workflow.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
