---
title: Observer / listener / event
audience: developer
category: standards
order: 327
summary: Code that reacts to something that happened elsewhere without the source knowing it: ezpEvent listeners, notification handlers, cache listeners. 8 instances in Exponential.
keywords: design pattern, observer, behavior, pattern catalogue
type: catalogue
---
# Observer / listener / event

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Code that reacts to something that happened elsewhere without the source knowing it: ezpEvent listeners, notification handlers, cache listeners.

| | |
|---|---|
| Group | Behavior |
| Instances | 8 |
| Recognized by | Class name contains Listener, Observer or Subscriber, or has attach()/notify()/addListener() methods; every ezpEvent notify()/filter() call (see the hooks page). |
| Extension point | event.ini [Event] Listeners[]=EVENT@CALLBACK; notification.ini [NotificationEventHandlerSettings] AvailableNotificationEventTypes[]. |

## Classes by area

### extension/cjw_newsletter (2)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterSubscriberExport` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettersubscriberexport.php:22` | The subscribers of one list as CSV, with filters (subscription status, a date range on a chosen date) and a choice of columns. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriberExport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscriber_export.php:15` | newsletter/subscriber_export/: the subscribers of a list as a CSV file, with filters (status, a date range) and a choice of columns. | – |

### extension/explayouts (2)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsSiteBundleEvent` | class | `extension/explayouts/classes/explayoutssitebundleevent.php:4` | – | – |
| `expLayoutsSiteBundleEventListener` | class | `extension/explayouts/classes/explayoutssitebundleeventlistener.php:4` | – | – |

### kernel/classes/requestclassifier (1)

[All patterns of kernel/classes/requestclassifier](area-kernel-classes-requestclassifier.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRequestVerdictListenerInterface` | interface | `kernel/classes/requestclassifier/exprequestverdictlistenerinterface.php:18` | Told about every verdict of the request classifier after its security.log line is written (settings/requestclassifier.ini [ClassifierSettings] Listen… | – |

### kernel/private (1)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpEvent` | class | `kernel/private/classes/ezpevent.php:20` | This class handles internal kernel events in Exponential, aka hooks. | – |

### kernel/private/classes/clusterfilehandlers (1)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZClusterEventListener` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlistener.php:14` | Interface to be implemented by any cluster event listener | – |

### kernel/private/classes/httpcache (1)

[All patterns of kernel/private/classes/httpcache](area-kernel-private-classes-httpcache.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpHttpCacheListener` | class | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | The kernel side of the role-aware HTTP cache: stores rendered content views for the early exit and Velocity's parent to serve, and turns the kernel's… | – |
