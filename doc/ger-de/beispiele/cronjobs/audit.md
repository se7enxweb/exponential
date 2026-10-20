---
title: Cronjob audit.php
audience: developer, admin
category: examples
summary: "Audit: sink spools, alert rules, rotation by day, archives, retention and checkpoints."
keywords: Teil, audit.php, audit, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob audit.php

Audit: sink spools, alert rules, rotation by day, archives, retention and checkpoints.

Einstiegspunkt `cronjobs/audit.php`, Code in `kernel/private/classes/cronjobs/audit.php`.

> The cronjob part audit (cronjobs/audit.php; \[CronjobPart-audit\] and the frequent group in cronjob.ini): the  
> audit's scheduled work, expAuditMaintenance::run(). Every run: the incremental index, the sink spools (webhook,  
> mail, network syslog) and the alert rules' cronjob pass; once a day after \[AuditRotationSettings\] RotateAfter:  
> rotation by day, verification, archiving, retention, pseudonymisation of the index and the checkpoint  
> (doc/eng-us/bc/6.0/audit.md, "Rotation, archives and retention (Q8)").  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`, dem Teil `audit`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php audit
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=audit.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/audit.php`, `kernel/private/classes/cronjobs/audit.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
