---
title: Cronjob contentjobs.php
audience: developer, admin
category: examples
summary: Setzt Inhaltsaufträge fort, deren Worker gestorben ist, und startet wartende Aufträge, die niemand gestartet hat.
keywords: Teil, contentjobs.php, contentjobs, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob contentjobs.php

Setzt Inhaltsaufträge fort, deren Worker gestorben ist, und startet wartende Aufträge, die niemand gestartet hat.

Einstiegspunkt `cronjobs/contentjobs.php`, Code in `kernel/private/classes/cronjobs/contentjobs.php`.

> Der Cronjob-Teil contentjobs (cronjobs/contentjobs.php, Gruppe frequent): startet die Inhaltsaufträge, die einen  
> Worker brauchen und keinen haben (ein laufender Auftrag, dessen Worker gestorben ist (kill -9, ein Neustart, eine Speichergrenze), und ein wartender Auftrag,  
> den niemand innerhalb von content.ini \[ContentJobSettings\] QueuedGrace Sekunden gestartet hat (der Start aus der Web-Anfrage  
> ist fehlgeschlagen)) und entfernt abgeschlossene Aufträge, die älter als KeepDays sind.  
>
> Der Worker wird abgekoppelt gestartet (bin/php/expcontentjob.php run <id>), wie aus der Web-Anfrage, sodass ein langer Auftrag  
> nie die anderen Cronjob-Teile aufhält; als root gestartet, wird er zum Benutzer der Website. Ein Auftrag, der immer wieder  
> fortgesetzt wird, ohne einen Stapel abzuschließen, wird vom Worker nach MaxAttempts Starts als fehlgeschlagen markiert, statt sich endlos zu wiederholen.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`, dem Teil `contentjobs`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php contentjobs
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=contentjobs.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/contentjobs.php`, `kernel/private/classes/cronjobs/contentjobs.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
