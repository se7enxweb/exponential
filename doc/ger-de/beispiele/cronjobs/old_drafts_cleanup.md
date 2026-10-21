---
title: Cronjob old_drafts_cleanup.php
audience: developer, admin
category: examples
summary: Entfernt Entwürfe von Benutzern, die die konfigurierte Altersgrenze überschreiten.
keywords: Teil, old_drafts_cleanup.php, old_drafts_cleanup, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob old_drafts_cleanup.php

Entfernt Entwürfe von Benutzern, die die konfigurierte Altersgrenze überschreiten.

Einstiegspunkt `cronjobs/old_drafts_cleanup.php`, Code in `kernel/private/classes/cronjobs/old_drafts_cleanup.php`.

> Der Code von cronjobs/old\_drafts\_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/old\_drafts\_cleanup.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  

## Wann er läuft

Er gehört zu keinem ausgelieferten Teil: Fügen Sie ihn in einem Override einem Teil hinzu (unten) oder führen Sie ihn einzeln aus.

```bash
# nur dieses Skript ausführen
php runcronjobs.php old_drafts_cleanup
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=old_drafts_cleanup.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/old_drafts_cleanup.php`, `kernel/private/classes/cronjobs/old_drafts_cleanup.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
