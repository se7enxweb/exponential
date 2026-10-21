---
title: Cronjob linkcheck.php
audience: developer, admin
category: examples
summary: Prüft alle internen und externen Links in veröffentlichten Inhalten auf defekte URLs.
keywords: Teil, linkcheck.php, linkcheck, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob linkcheck.php

Prüft alle internen und externen Links in veröffentlichten Inhalten auf defekte URLs.

Einstiegspunkt `cronjobs/linkcheck.php`, Code in `kernel/private/classes/cronjobs/linkcheck.php`.

> Der Code von cronjobs/linkcheck.php, in eine Klasse verschoben (#207 Stufe 1). Die Datei cronjobs/linkcheck.php ist ein Aufruf davon.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  
> Benutzeranleitung der Linkprüfung: doc/eng-us/guides/urls-and-aliases.md (Abschnitt "How links are checked")  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `infrequent`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php infrequent
./console exp:runcronjobs infrequent
# nur dieses Skript ausführen
php runcronjobs.php linkcheck
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=linkcheck.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/linkcheck.php`, `kernel/private/classes/cronjobs/linkcheck.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
