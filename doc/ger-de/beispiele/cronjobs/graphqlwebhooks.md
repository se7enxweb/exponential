---
title: Cronjob graphqlwebhooks.php
audience: developer, admin
category: examples
summary: "GraphQL-Webhooks: die Ereignisse des Journals an die Endpunkte senden, die sie haben wollen, mit Wiederholungen."
keywords: Teil, graphqlwebhooks.php, graphqlwebhooks, cronjob, runcronjobs, part
type: generated
generated: exp:help:examples
---
# Der Cronjob graphqlwebhooks.php

GraphQL-Webhooks: die Ereignisse des Journals an die Endpunkte senden, die sie haben wollen, mit Wiederholungen.

Einstiegspunkt `cronjobs/graphqlwebhooks.php`, Code in `kernel/private/classes/cronjobs/graphqlwebhooks.php`.

> Der Cronjob-Teil graphqlwebhooks (cronjobs/graphqlwebhooks.php; \[CronjobPart-graphqlwebhooks\] und die Gruppe frequent  
> in cronjob.ini; doc/ger-de/funktionen/6.0/graphql.md, „Webhooks“): liest das GraphQL-Ereignisjournal ab der Stelle, an  
> der der letzte Lauf aufhörte, legt für jeden Endpunkt, der ein Ereignis haben will, eine Zustellung in den Ausgang und  
> sendet die fälligen Zustellungen (expGraphQLWebhooks). Tut nichts, solange kein Endpunkt eingerichtet oder die API  
> ausgeschaltet ist.  

## Wann er läuft

In der ausgelieferten `settings/cronjob.ini` gehört er zu: dem Teil `frequent`, dem Teil `graphqlwebhooks`.

```bash
# seinen Teil ausführen (als der Benutzer, unter dem der Webserver läuft, im Wurzelverzeichnis der Installation)
php runcronjobs.php frequent
./console exp:runcronjobs frequent
# nur dieses Skript ausführen
php runcronjobs.php graphqlwebhooks
```

In einem Override zu einem Teil hinzufügen:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
Scripts[]=graphqlwebhooks.php
*/ ?>
```

## Siehe auch

- [Alle Cronjobs und Teile](README.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Ein eigener Cronjob](../kochbuch/cronjob.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `cronjobs/graphqlwebhooks.php`, `kernel/private/classes/cronjobs/graphqlwebhooks.php`, `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
