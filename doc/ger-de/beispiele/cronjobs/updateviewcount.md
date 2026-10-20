---
title: Cronjob updateviewcount.php
audience: developer, admin
category: examples
summary: Schreibt den Puffer der Aufrufzähler aus dem Speicher in die Datenbank.
keywords: Teil, updateviewcount.php, updateviewcount, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob updateviewcount.php

Schreibt den Puffer der Aufrufzähler aus dem Speicher in die Datenbank.

Einstiegspunkt `cronjobs/updateviewcount.php`, Code in `kernel/private/classes/cronjobs/updateviewcount.php`.

> Der Code von cronjobs/updateviewcount.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/updateviewcount.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

Er gehört zu keinem ausgelieferten Teil: Fügen Sie ihn in einem Override einem Teil hinzu (unten) oder führen Sie ihn einzeln aus.

```bash
# nur dieses Skript ausführen
php runcronjobs.php updateviewcount
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=updateviewcount.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/updateviewcount.php`, `kernel/private/classes/cronjobs/updateviewcount.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
