---
title: Event listeners for integrations
audience: integrator, developer
category: integration
order: 180
summary: Reacting to kernel events with ezpEvent listeners - which events are useful to an integration (caches cleared, object states, notifications, sessions), attaching a listener in settings, a CDN purge example, the difference between web requests and command line runs, and the rules.
keywords: ezpEvent, event listener, notify, filter, site.ini Event Listeners, content/cache, content/cache/all, content/state/assign, CDN purge, cache invalidation, RunnableSettings, hook, integration
type: help
---
# Event listeners for integrations

This page is for integration developers who want to react to something the kernel does without configuring a
workflow. The kernel announces events through `ezpEvent` (`kernel/private/classes/ezpevent.php`); a listener is a
function or static method named in the settings. Listeners are the lightest hook there is, and the right one for
reactions such as "purge these pages at the CDN" or "tell the archive that this object changed state". The full
chapter is [Events, listeners and filters](../extension-development/part-3-extension-developer/events-and-filters.md).

## Contents

1. [Events useful to an integration](#events-useful-to-an-integration)
2. [Attaching a listener](#attaching-a-listener)
3. [Example: purging a CDN when caches are cleared](#example-purging-a-cdn-when-caches-are-cleared)
4. [Web requests and command line runs](#web-requests-and-command-line-runs)
5. [Listeners or workflows](#listeners-or-workflows)
6. [Rules](#rules)

## Events useful to an integration

| Event | Kind | Parameters | Integration use |
|---|---|---|---|
| `content/cache` | notify | `( $nodeList, $contentObjectList )` | The view caches of these nodes were cleared: purge the same pages at a CDN or reverse proxy |
| `content/cache/all` | notify | none | Every view cache was cleared: purge everything downstream |
| `content/state/assign` | notify | `( $objectID, $selectedStateIDList )` | An object changed state (for example "approved", "archived"): tell the system that follows states |
| `content/notification/create` | filter | whether a notification is created | Suppress or allow notifications, for example during an import |
| `session/destroy`, `session/regenerate` | notify | session life cycle | Sign the user out of a connected application too |
| `response/output` | filter | the page | Add a tag to every page (prefer templates for this) |

There is no `content/publish` event in this list: the hook for publishing is the trigger `content / publish / after`
([Workflows and triggers](workflows-and-triggers-as-hooks.md)), and its visible result for caches is `content/cache`.

## Attaching a listener

In your extension's `settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[Event]
Listeners[]=content/cache@myIntegrationListeners::cacheCleared
Listeners[]=content/cache/all@myIntegrationListeners::allCacheCleared

*/ ?>
```

The form is `<event>@<callback>`. A malformed entry, an unknown callback or a duplicate is skipped or attached once,
with a note in the log.

## Example: purging a CDN when caches are cleared

The listener only notes the node ids; a frequent cronjob part sends the purge requests in batches, so a slow CDN API
never slows down publishing.

```php
<?php
/**
 * Event listeners of myintegration, attached in settings/site.ini.append.php [Event] Listeners[].
 */
class myIntegrationListeners
{
    /** notify: the view caches of these nodes were cleared */
    public static function cacheCleared( $nodeList, $contentObjectList )
    {
        $db = eZDB::instance();
        foreach ( (array)$nodeList as $nodeID )
        {
            $db->query( 'INSERT INTO myintegration_purge ( node_id, created ) VALUES ( '
                      . (int)$nodeID . ', ' . time() . ' )' );
        }
    }

    /** notify: every view cache was cleared */
    public static function allCacheCleared()
    {
        eZDB::instance()->query( 'INSERT INTO myintegration_purge ( node_id, created ) VALUES ( 0, ' . time() . ' )' );
    }
}
```

Node id `0` stands for "purge everything" in this example's own table. The cronjob part turns node ids into URLs
(`eZContentObjectTreeNode::fetch( $id )->urlAlias()`) and calls the CDN.

## Web requests and command line runs

`[Event] Listeners[]` is attached for **web requests** (`ezpKernelWeb`). A command line script, a cronjob part or
`ezexec.php` does not attach them unless it asks:

```php
<?php
ezpEvent::getInstance()->registerEventListeners();
```

Call it after `$script->initialize()` in an import script whose cache clearing your listeners must see. Listeners for
commands, cronjob parts and module views (`runnable/<kind>/before` and `after`, `cronjob/part/run`) are registered in
`site.ini [RunnableSettings] Listeners[]` and are attached everywhere.

## Listeners or workflows

| Listener | Workflow event |
|---|---|
| No configuration in the administration | Builders see and switch it at Setup > Triggers |
| Only reacts; cannot stop the operation | Can accept, reject or make the operation wait |
| Works for any announced event | Works for operations offered in `AvailableOperationList[]` |
| Attached for web requests (CLI on request) | Runs wherever the operation runs, web or CLI |

## Rules

- Keep listeners fast; record and defer slow work to a cronjob.
- A **filter** listener must return the value, on every path; forgetting destroys it.
- Do not throw from a listener; log with `eZDebug::writeError()` and continue.
- Under Velocity, never attach listeners in code that runs per request: they accumulate. Settings-based listeners are
  attached once.

## Related pages

- [Events, listeners and filters](../extension-development/part-3-extension-developer/events-and-filters.md)
- [Access and view cache filters](../../features/6.0/access-and-cache-filters.md)
- [Caching after imports](caching-after-imports.md)
- [Workflows and triggers as integration hooks](workflows-and-triggers-as-hooks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
