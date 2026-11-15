---
title: Cronjob imagealiasbacklog.php
audience: developer, admin
category: examples
summary: Die Bild-Aliase im Rückstau erzeugen, die keine Anfrage erzeugt hat (settings/image.ini [AliasSettings] Backlog).
keywords: Teil, imagealiasbacklog.php, imagealiasbacklog, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob imagealiasbacklog.php

Die Bild-Aliase im Rückstau erzeugen, die keine Anfrage erzeugt hat (settings/image.ini [AliasSettings] Backlog).

Einstiegspunkt `cronjobs/imagealiasbacklog.php`, Code in `kernel/private/classes/cronjobs/imagealiasbacklog.php`.

> Der Cronjob-Teil imagealiasbacklog (cronjobs/imagealiasbacklog.php, Gruppe frequent): das Sicherheitsnetz des  
> Bild-Alias-Rückstaus (expImageAliasBacklog, settings/image.ini \[AliasSettings\] Backlog). Anfragen erzeugen den Rückstau  
> gleich nach ihren Antworten; was noch da ist, wenn dieser Teil läuft (der Server hat seitdem keine Anfrage bekommen,  
> oder ein Prozess ist gestorben, während er an einem Eintrag arbeitete), wird hier erzeugt, wobei wie bei einem Job auf  
> einen Umwandlungsplatz gewartet wird. Jeder Alias so, wie ihn der Siteaccess festlegt, in dem die Seite gezeigt wurde.  
> Nichts im Modus render oder bei leerem Rückstau.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php imagealiasbacklog
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=imagealiasbacklog.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/imagealiasbacklog.php`, `kernel/private/classes/cronjobs/imagealiasbacklog.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
