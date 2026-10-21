---
title: Cronjob trashpurge.php
audience: developer, admin
category: examples
summary: Löscht Objekte im Papierkorb endgültig (alle oder so alte, wie [TrashSettings] KeepItemsForDays sagt).
keywords: Teil, trashpurge.php, trashpurge, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob trashpurge.php

Löscht Objekte im Papierkorb endgültig (alle oder so alte, wie [TrashSettings] KeepItemsForDays sagt).

Einstiegspunkt `cronjobs/trashpurge.php`, Code in `kernel/private/classes/cronjobs/trashpurge.php`.

> Der Code von cronjobs/trashpurge.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/trashpurge.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

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
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/trashpurge.php`, `kernel/private/classes/cronjobs/trashpurge.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
