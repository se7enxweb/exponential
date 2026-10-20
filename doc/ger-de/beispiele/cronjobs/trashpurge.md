---
title: Cronjob trashpurge.php
audience: developer, admin
category: examples
summary: Permanently delete objects in the trash (all, or as old as [TrashSettings] KeepItemsForDays says).
keywords: Teil, trashpurge.php, trashpurge, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob trashpurge.php

Permanently delete objects in the trash (all, or as old as [TrashSettings] KeepItemsForDays says).

Einstiegspunkt `cronjobs/trashpurge.php`, Code in `kernel/private/classes/cronjobs/trashpurge.php`.

> The code of cronjobs/trashpurge.php, moved into a class (#207 stage 1). The file cronjobs/trashpurge.php is one call to it.  
> Guide: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

Er gehört zu keinem ausgelieferten Teil: Fügen Sie ihn in einem Override einem Teil hinzu (unten) oder führen Sie ihn einzeln aus.

```bash
# nur dieses Skript ausführen
php runcronjobs.php trashpurge
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=trashpurge.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/trashpurge.php`, `kernel/private/classes/cronjobs/trashpurge.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
