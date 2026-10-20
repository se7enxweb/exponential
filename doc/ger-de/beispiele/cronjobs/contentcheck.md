---
title: Cronjob contentcheck.php
audience: developer, admin
category: examples
summary: Baut den Referenzindex der Inhaltsprüfungen (ungenutzte Assets) für Installationen, in denen eine Seite ihn nicht bauen darf (contentcheck.ini BuildOnRequest=false).
keywords: Teil, contentcheck.php, contentcheck, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob contentcheck.php

Baut den Referenzindex der Inhaltsprüfungen (ungenutzte Assets) für Installationen, in denen eine Seite ihn nicht bauen darf (contentcheck.ini BuildOnRequest=false).

Einstiegspunkt `cronjobs/contentcheck.php`, Code in `kernel/private/classes/cronjobs/contentcheck.php`.

> Der Code von cronjobs/contentcheck.php: baut den Referenzindex der Inhaltsprüfungen und behält ihn im Cache  
> (expContentCheckReferences). Führen Sie ihn als Cronjob-Teil contentcheck aus (php runcronjobs.php contentcheck), öfter  
> als contentcheck.ini \[UnreferencedSettings\] IndexTTL, wenn BuildOnRequest false ist. Mit \[MissingFileSettings\]  
> CronjobReport=enabled zählt er außerdem die fehlenden Dateien und Alias-Probleme des ganzen Baums und schreibt die Zahl  
> in die Cronjob-Ausgabe (eine Warnung, wenn es welche gibt); er behebt nie etwas.  
> Anleitung: doc/eng-us/features/6.0/content-checks.md  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `contentcheck`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php contentcheck
./console exp:runcronjobs contentcheck
# nur dieses Skript ausführen
php runcronjobs.php contentcheck
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-contentcheck]
Scripts[]=contentcheck.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../../../eng-us/examples/cookbook/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/contentcheck.php`, `kernel/private/classes/cronjobs/contentcheck.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
