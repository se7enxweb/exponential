---
title: content.ini [PublishingSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PublishingSettings] in content.ini: AsynchronousPublishing, PublishingProcessSlots, AsynchronousPublishingFilters, AsynchronousPublishingPreQueueHooks, AsynchronousPublishingPostHandlingHooks, AsynchronousPublishingQueueReader, AsynchronousPollingInterval ..."
keywords: Einstellung, content.ini, PublishingSettings, INI, setting, AsynchronousPublishing, PublishingProcessSlots, AsynchronousPublishingFilters, AsynchronousPublishingPreQueueHooks, AsynchronousPublishingPostHandlingHooks, AsynchronousPublishingQueueReader, AsynchronousPollingInterval, AsynchronousCleanupInterval, AsynchronousCleanupAgeLimit
type: generated
generated: exp:help:examples
---
# content.ini [PublishingSettings]

Der Block `[PublishingSettings]` von `settings/content.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AsynchronousPublishing

> Die Funktion des asynchronen Veröffentlichens aktivieren/deaktivieren  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousPublishing=disabled
*/ ?>
```

## PublishingProcessSlots

> wie viele parallele Veröffentlichungsvorgänge erlaubt sind  
> Standard: 10  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
PublishingProcessSlots=10
*/ ?>
```

## AsynchronousPublishingFilters

> Filter des asynchronen Veröffentlichens  
> Jeder Filter muss die abstrakte Klasse eZAsynchronousPublishinfFilter implementieren,  
> und der Klassenname muss diesem Array hinzugefügt werden. Bsp.:  
> AsynchronousPublishingFilters\[\]=MyAPFilterClass  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
# ein leeres "AsynchronousPublishingFilters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AsynchronousPublishingFilters[]
*/ ?>
```

## AsynchronousPublishingPreQueueHooks

> Queue-Hooks  
> Sie können eigenen Code ausführen lassen, bevor ein Eintrag in die Warteschlange zum Veröffentlichen kommt  
> Danach ist es nicht möglich, weil sich nicht sicherstellen lässt, dass er nicht schon verarbeitet wird  
> Hooks lassen sich entweder dynamisch mit PHP-Code registrieren oder in den INI-Einstellungen mit folgender Variable festlegen  
> AsynchronousPublishingQueueHooks\[\]=MyHookClass::queue  
> Die Hook-Methode muss zwei Parameter annehmen: die ID des Inhaltsobjekts und seine Version (beide Ganzzahlen)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
# ein leeres "AsynchronousPublishingPreQueueHooks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AsynchronousPublishingPreQueueHooks[]
*/ ?>
```

## AsynchronousPublishingPostHandlingHooks

> Hooks nach der Verarbeitung  
> Diese Hooks werden direkt ausgeführt, nachdem das asynchrone Veröffentlichen einen Eintrag verarbeitet hat  
> Sie lassen sich zum Beispiel verwenden, um eigene Daten zu entfernen, die ein Queue-Hook hinzugefügt hat  
> AsynchronousPublishingQueueHooks\[\]=MyHookClass::postHandle  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
# ein leeres "AsynchronousPublishingPostHandlingHooks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AsynchronousPublishingPostHandlingHooks[]
*/ ?>
```

## AsynchronousPublishingQueueReader

> Handler zum Lesen der Warteschlange  
> Exponential bringt einen Standard-Handler zum Lesen der Warteschlange mit, er kann aber durch einen eigenen ersetzt werden  
> Um einen neuen zu definieren, überschreiben Sie AsynchronousPublishingQueueReader mit Ihrem Klassennamen:  
> AsynchronousPublishingQueueReader=myContentPublishingQueue  
> Die hier konfigurierte Klasse muss eine Methode namens next() deklarieren  

Standard: `ezpContentPublishingQueue`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingQueueReader=ezpContentPublishingQueue
*/ ?>
```

## AsynchronousPollingInterval

> Wie oft der Daemon die Warteschlange abfragt (Pause in Sekunden)  
> Standard: 1  

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousPollingInterval=1
*/ ?>
```

## AsynchronousCleanupInterval

> Wie oft der Daemon die Warteschlange aufräumt (Pause in Sekunden)  
> Standard: 43200 (12 Stunden)  

Standard: `43200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousCleanupInterval=43200
*/ ?>
```

## AsynchronousCleanupAgeLimit

> Wie alt Prozesse sein müssen, um aufgeräumt zu werden (in Sekunden)  
> Standard: 604800 (eine Woche)  

Standard: `604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousCleanupAgeLimit=604800
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
