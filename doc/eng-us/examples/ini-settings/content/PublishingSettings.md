---
title: content.ini [PublishingSettings]
audience: developer, admin
category: examples
summary: "The settings of [PublishingSettings] in content.ini: AsynchronousPublishing, PublishingProcessSlots, AsynchronousPublishingFilters, AsynchronousPublishingPreQueueHooks, AsynchronousPublishingPostHandlingHooks, AsynchronousPublishingQueueReader, AsynchronousPollingInterval ..."
keywords: content.ini, PublishingSettings, INI, setting, AsynchronousPublishing, PublishingProcessSlots, AsynchronousPublishingFilters, AsynchronousPublishingPreQueueHooks, AsynchronousPublishingPostHandlingHooks, AsynchronousPublishingQueueReader, AsynchronousPollingInterval, AsynchronousCleanupInterval, AsynchronousCleanupAgeLimit
type: generated
generated: exp:help:examples
---
# content.ini [PublishingSettings]

The block `[PublishingSettings]` of `settings/content.ini`, with 9 setting(s).

Never edit `settings/content.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/content.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in an extension (read when the extension is active).

## AsynchronousPublishing

> Enable/Disable the asynchronous publishing feature  

Default: `disabled`.

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

Default: `10`.

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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
# an empty "AsynchronousPublishingFilters[]" first replaces the shipped list; without it the items are added
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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
# an empty "AsynchronousPublishingPreQueueHooks[]" first replaces the shipped list; without it the items are added
AsynchronousPublishingPreQueueHooks[]
*/ ?>
```

## AsynchronousPublishingPostHandlingHooks

> Post handling hooks  
> These hooks will be executed right after an item has been processed by asynchronous publishing  
> It can for instance be used to remove custom data added in a Queue hook  
> AsynchronousPublishingQueueHooks\[\]=MyHookClass::postHandle  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
# an empty "AsynchronousPublishingPostHandlingHooks[]" first replaces the shipped list; without it the items are added
AsynchronousPublishingPostHandlingHooks[]
*/ ?>
```

## AsynchronousPublishingQueueReader

> Queue reader handler  
> Exponential comes with a default queue reader handler, but it can be overridden with a custom one  
> To define a new queue reader, override AsynchronousPublishingQueueReader with your class name:  
> AsynchronousPublishingQueueReader=myContentPublishingQueue  
> The class configured here must declare a method named next()  

Default: `ezpContentPublishingQueue`.

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

Default: `1`.

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

Default: `43200`.

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

Default: `604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PublishingSettings]
AsynchronousCleanupAgeLimit=604800
*/ ?>
```

## Related

- [All blocks of content.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/content.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
