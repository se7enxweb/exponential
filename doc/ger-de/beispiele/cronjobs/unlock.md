---
title: Cronjob unlock.php
audience: developer, admin
category: examples
summary: Gibt Inhaltsobjekte frei, die in einem gesperrten Bearbeitungszustand hängen.
keywords: Teil, unlock.php, unlock, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob unlock.php

Gibt Inhaltsobjekte frei, die in einem gesperrten Bearbeitungszustand hängen.

Einstiegspunkt `cronjobs/unlock.php`, Code in `kernel/private/classes/cronjobs/unlock.php`.

> Der Code von cronjobs/unlock.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/unlock.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `unlock`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php unlock
./console exp:runcronjobs unlock
# nur dieses Skript ausführen
php runcronjobs.php unlock
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-unlock]
Scripts[]=unlock.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/unlock.php`, `kernel/private/classes/cronjobs/unlock.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
