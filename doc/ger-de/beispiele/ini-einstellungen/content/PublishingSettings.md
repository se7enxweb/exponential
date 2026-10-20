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

> Enable/Disable the asynchronous publishing feature  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousPublishing=disabled
*/ ?>
```

## PublishingProcessSlots

> how many parallel publishing operations should be allowed  
> default: 10  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
PublishingProcessSlots=10
*/ ?>
```

## AsynchronousPublishingFilters

> Asynchronous Publishing filters  
> Each filter must implement the eZAsynchronousPublishinfFilter abstract class,  
> and the class name must be added to this array. Ex:  
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

> Queue hooks  
> You can have your own code executed before an item is added to the publishing queue  
> It isn't possible to do so after it is added as there is no way to ensure it isn't already being handled  
> Hooks can either be registered dynamically, using PHP code, or pre-defined in INI settings using the following variable  
> AsynchronousPublishingQueueHooks\[\]=MyHookClass::queue  
> The hook method must accept two parameters: the content object id, and its version (both integers)  

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

> Post handling hooks  
> These hooks will be executed right after an item has been processed by asynchronous publishing  
> It can for instance be used to remove custom data added in a Queue hook  
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

> Queue reader handler  
> Exponential comes with a default queue reader handler, but it can be overridden with a custom one  
> To define a new queue reader, override AsynchronousPublishingQueueReader with your class name:  
> AsynchronousPublishingQueueReader=myContentPublishingQueue  
> The class configured here must declare a method named next()  

Standard: `ezpContentPublishingQueue`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousPublishingQueueReader=ezpContentPublishingQueue
*/ ?>
```

## AsynchronousPollingInterval

> How frequent the daemon polls the queue (sleep in seconds)  
> default: 1  

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousPollingInterval=1
*/ ?>
```

## AsynchronousCleanupInterval

> How frequent the daemon cleans the queue (sleep in seconds)  
> default: 43200 (12 hours)  

Standard: `43200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousCleanupInterval=43200
*/ ?>
```

## AsynchronousCleanupAgeLimit

> How old processes need to be in order to be cleaned (in seconds)  
> default: 604800 (a week)  

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
