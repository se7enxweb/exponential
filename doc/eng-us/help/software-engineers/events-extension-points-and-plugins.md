---
title: Events, extension points and plugins
audience: software-engineer, developer
category: software-engineering
order: 180
summary: How behavior is added to Exponential without forking it - the in-process event bus ezpEvent with notify and filter, handlers named in settings, datatypes, workflow event types, template operators, fetch functions, login and cluster handlers, replaceable views and modules, and why a kernel class override is the last resort.
keywords: extension points, plugins, ezpEvent, events, filters, listeners, hooks, handlers, settings, RunnableSettings, workflow, datatype, operator, kernel override, idempotent, software engineer
type: help
---
# Events, extension points and plugins

This page is for engineers who need to change what Exponential does without changing its code. Nearly every part of
the kernel is pluggable through a setting that names a class; the in-process event bus adds hooks at fixed points of a
request. The full list is [Extension points at a glance](../extension-development/part-8-reference/extension-points-at-a-glance.md)
and the registered points are documented in [RAD extension points](../../bc/6.0/rad-extension-points.md).

## Contents

1. [The order of preference](#the-order-of-preference)
2. [The event bus: ezpEvent](#the-event-bus-ezpevent)
3. [Handlers named in settings](#handlers-named-in-settings)
4. [Replacing a view or a module](#replacing-a-view-or-a-module)
5. [The last resort: a kernel override](#the-last-resort-a-kernel-override)
6. [Rules for listeners and plugins](#rules-for-listeners-and-plugins)

## The order of preference

1. **A setting.** Many behaviors are switches or lists already; check the shipped `settings/*.ini` first.
2. **A template override** for presentation ([The template engine and the design chain](template-engine-and-design-chain.md)).
3. **A registered extension point**: a datatype, workflow event, operator, fetch function, handler or event listener.
4. **A replaced view or module**, through `[RunnableSettings]` or an extension module override.
5. **A kernel class override**, only when nothing above fits.

## The event bus: ezpEvent

`ezpEvent::getInstance()` (`kernel/private/classes/ezpevent.php`) offers two calls:

| Call | Use |
|---|---|
| `notify( $name, $params )` | tell listeners that something happened |
| `filter( $name, $value )` | let listeners change a value and return it |

Global listeners are configured in `site.ini [Event] Listeners[]=<event>@<callback>`. Events the kernel emits include:

| Event | When |
|---|---|
| `request/preinput`, `request/input` | at the start of the dispatch loop |
| `response/preoutput`, `response/output` | output filters, before the response is sent |
| `content/cache`, `content/cache/all`, `content/class/cache` | content view caches are cleared |
| `image/removeAliases` | image variations are removed |
| `content/edit/access`, `content/download/access`, `collaboration/item/access` | access filters that can refuse |
| `runnable/<kind>/before`, `runnable/<kind>/after` | around class-based views, commands and cronjob parts |

The file marks `ezpEvent` as private API that may change; extensions rely on it nonetheless, so changes to it are
treated as compatibility-relevant ([Events and filters](../extension-development/part-3-extension-developer/events-and-filters.md)).

## Handlers named in settings

| Extension point | Setting | Guide |
|---|---|---|
| Datatypes | `content.ini [DataTypeSettings]` | [Custom datatypes](../creators/app-creators/custom-datatypes.md) |
| Workflow event types | `workflow.ini [EventSettings]` | [Writing a workflow event type](../creators/app-creators/writing-a-workflow-event-type.md) |
| Template operators and functions | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` | [Template operators](../creators/app-creators/template-operators.md) |
| Fetch functions | the module's `function_definition.php` | [Writing a fetch function](../creators/app-creators/writing-a-fetch-function.md) |
| Cronjob parts | `cronjob.ini` | [Writing a cronjob part](../creators/app-creators/writing-a-cronjob-part.md) |
| Notification handlers | `notification.ini` | [Notifications and mail](../creators/app-creators/notifications-and-mail.md) |
| Content limitation handlers | see the feature page | [Content limitation handlers](../../features/6.0/content-limitation-handlers.md) |
| Cluster file handler | `file.ini [ClusteringSettings] FileHandler` | [Scaling and clustering](scaling-and-clustering.md) |
| Any handler named by a setting | the pattern | [Handlers named by settings](../extension-development/part-3-extension-developer/handlers-named-by-settings.md) |

## Replacing a view or a module

- A class-based view can be replaced through `site.ini [RunnableSettings] Implementation[]`.
- A whole kernel module can be replaced by an extension's module of the same name
  ([Extension module override](../../features/6.0/extension-module-override.md)).

## The last resort: a kernel override

A kernel class override replaces a kernel class with a copy in an extension. Every update of the kernel can then break
your copy silently, so it needs a test and a note in your upgrade checklist
([Kernel overrides, the last resort](../extension-development/part-3-extension-developer/kernel-overrides-the-last-resort.md)).

## Rules for listeners and plugins

- **Registrations must be idempotent.** Under Velocity a listener attached once per request in a long-lived worker
  accumulates ([Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md)).
- Listener names should be `Class::method` strings so they can be configured.
- A new class named in a shipped setting should be guarded with `class_exists()` where the kernel loads it.
- Access decisions go through the access filters, never through a check of a user id.

## Related pages

- [Kernel, lib and extensions: the layering](kernel-lib-and-extensions.md)
- [Extending without forking](../extension-development/part-5-extend-fork-contribute/extending-without-forking.md)
- [Design pattern catalogue](../../standards/patterns/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
