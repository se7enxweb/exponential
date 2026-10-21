---
title: "Events and listeners: ezpEvent"
audience: web-framework-developer, developer
category: web-frameworks
order: 220
summary: The in-process event bus of Exponential compared with Symfony's EventDispatcher, Laravel events, Django signals and Spring application events - notify and filter events, registering listeners in site.ini, the events the kernel emits, runnable events, the rules for listeners under persistent workers, and when to use workflows instead.
keywords: events, listeners, event dispatcher, signals, observers, ezpEvent, notify, filter, attach, Listeners, request/input, response/output, content/cache, runnable events, EventDispatcher, Django signals, Spring events, web framework developer
type: help
---
# Events and listeners: ezpEvent

This page is for developers who subscribe to framework events (Symfony's `EventDispatcher`, Laravel's `Event::listen`,
Django signals, Rails `ActiveSupport::Notifications`, Spring `@EventListener`). Exponential has a small in-process
event bus, `ezpEvent` (`kernel/private/classes/ezpevent.php`), with two kinds of events: **notify** events tell
listeners that something happened, **filter** events pass a value through listeners that may change it
([Events (ezpEvent)](../../standards/architecture/kernel-architecture.md#events-ezpevent)).

## Contents

1. [The API](#the-api)
2. [Registering listeners](#registering-listeners)
3. [Events the kernel emits](#events-the-kernel-emits)
4. [Rules for listeners](#rules-for-listeners)
5. [Events, workflows or notifications?](#events-workflows-or-notifications)
6. [Compared with other frameworks](#compared-with-other-frameworks)

## The API

```php
$events = ezpEvent::getInstance();
$events->attach( 'content/cache', array( 'expDemoListener', 'onContentCache' ) );  // or 'expDemoListener::onContentCache'
$events->notify( 'expdemo/item/stored', array( $itemID ) );                       // tell listeners
$html = $events->filter( 'expdemo/item/html', $html );                             // let listeners change a value
```

Your own extension can emit its own events with the same calls, so other extensions can hook into it.

## Registering listeners

```ini
# extension/expdemo/settings/site.ini.append.php
<?php /* #?ini charset="utf-8"?
[Event]
Listeners[]=content/cache@expDemoListener::onContentCache
Listeners[]=response/output@expDemoListener::filterOutput
*/ ?>
```

- `site.ini [Event] Listeners[]` entries are attached for web requests.
- `site.ini [RunnableSettings] Listeners[]` entries are attached for commands, cronjob parts and views in every context,
  the command line included, once per process ([Events around run()](../../bc/6.0/cli_cronjob_view_abstractions.md#events-around-run)).
- `site.ini` marks `[Event]` as private API that might change; the kernel's own extensions (`ezformtoken`) rely on it.

## Events the kernel emits

| Event | Kind | When |
|---|---|---|
| `request/preinput`, `request/input` | notify | early in a web request, with the URI |
| `response/preoutput`, `response/output` | filter | the final output of a web request |
| `content/view` | filter | the result of a content view |
| `content/view/cachekeys` | filter | the keys of the content view cache |
| `content/cache`, `content/cache/all`, `content/cache/version` | notify | content view caches cleared |
| `content/class/cache`, `content/section/cache`, `content/state/cache` | notify | caches of classes, sections, states cleared |
| `content/state/assign` | notify | an object state was assigned |
| `content/edit/access`, `content/download/access`, `collaboration/item/access` | filter | access decisions the kernel asks listeners about |
| `httpcache/store/headers` | filter | the headers the HTTP cache keeps with a page |
| `image/alias`, `image/removeAliases`, `image/purgeAliases` | notify | image variations created or removed |
| `session/regenerate`, `session/destroy`, `session/gc`, `session/cleanup` | notify | session life cycle |
| `runnable/<kind>/before`, `runnable/<kind>/after` | notify, filter | around every class-based command, cronjob part and view |

The list was taken from the calls of `ezpEvent::getInstance()->notify()` and `filter()` in `kernel/` and `lib/`; search
for them to see the parameters each passes. **Setup > RAD**'s survey lists the events too.

## Rules for listeners

- **A filter listener must return the value it was given**, changed or not; one that forgets destroys the value.
- **Registrations must be idempotent.** Under Velocity a listener attached once per request in a long-lived worker
  accumulates. Register in settings, or attach in code only once per process.
- **Keep listeners fast.** They run inside the request; send slow work to a queue ([Queues](queues-and-background-jobs.md)).
- **Name listeners as `Class::method` strings** so they can be configured.

## Events, workflows or notifications?

| Need | Use |
|---|---|
| React in code to a kernel event, always, synchronously | an `ezpEvent` listener |
| Run steps before or after publishing, checkout and other operations, possibly waiting (approval, payment, date), switchable by administrators | a workflow on a trigger ([Workflows and triggers](workflows-triggers-and-domain-events.md)) |
| Tell users by mail about new content or collaboration items | the notification system ([Notifications](../../guides/notifications-developer.md)) |
| Real-time messages to browsers | Velocity's WebSockets and Server-Sent Events ([Velocity WebSockets](../../features/6.0/velocity-websockets-and-events.md)) |

There is no built-in "model saved" event for your own `eZPersistentObject` classes; emit your own event from your
`store()` or service method.

## Compared with other frameworks

| Framework | Concept | Exponential |
|---|---|---|
| Symfony | event subscribers with priorities, stoppable events | listeners in `Listeners[]` order; no stopping, no priorities |
| Laravel | events and listeners, queued listeners | listeners; queue the work yourself with expqueue |
| Django | signals (`post_save`) | kernel events and workflows on content operations |
| Rails | callbacks, notifications | the same |
| Spring | application events, `@TransactionalEventListener` | listeners; no transaction phases |

## Related pages

- [Middleware: request rules, input and output listeners](middleware-and-request-rules.md)
- [Event hook pattern](../../standards/patterns/event-hook.md), [Observer pattern](../../standards/patterns/observer.md)
- [Extension points: kernel event listener](../../bc/6.0/rad-extension-points.md#kernel-event-listener)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
