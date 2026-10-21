---
title: Cronjob audit.php
audience: developer, admin
category: examples
summary: "Audit: Spools der Ziele, Alarmregeln, Rotation nach Tagen, Archive, Aufbewahrung und Prüfpunkte."
keywords: Teil, audit.php, audit, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob audit.php

Audit: Spools der Ziele, Alarmregeln, Rotation nach Tagen, Archive, Aufbewahrung und Prüfpunkte.

Einstiegspunkt `cronjobs/audit.php`, Code in `kernel/private/classes/cronjobs/audit.php`.

> Der Cronjob-Teil audit (cronjobs/audit.php; \[CronjobPart-audit\] und die Gruppe frequent in cronjob.ini): die  
> geplante Arbeit des Audits, expAuditMaintenance::run(). Bei jedem Lauf: der inkrementelle Index, die Spools der Ziele (Webhook,  
> Mail, Netzwerk-Syslog) und der Cronjob-Durchgang der Alarmregeln; einmal am Tag nach \[AuditRotationSettings\] RotateAfter:  
> Rotation nach Tagen, Prüfung, Archivierung, Aufbewahrung, Pseudonymisierung des Index und der Prüfpunkt  
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
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/audit.php`, `kernel/private/classes/cronjobs/audit.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
