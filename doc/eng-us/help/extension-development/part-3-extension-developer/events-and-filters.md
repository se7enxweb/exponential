---
title: Events, listeners and filters
audience: developer, app-creator
category: extension-development
order: 280
summary: React to the kernel without changing it: ezpEvent, [Event] Listeners[], notify versus filter events, the events the kernel announces, and the rule that a filter returns its value.
keywords: event, listener, filter, ezpEvent, notify, attach, site.ini Event Listeners, response/output, content/cache, content/state/assign, module/functionlist, content/view/cachekeys, runnable events, cronjob/part/run
type: help
---
# Events, listeners and filters

Part 3, chapter 28. The lightest way to change system behavior is to **listen** to an event the kernel announces.
Nothing is replaced, nothing is overridden; your function is simply called. This chapter explains the event system,
`ezpEvent` (`kernel/private/classes/ezpevent.php`), and lists the events you can use.

## Two kinds

| Kind | Kernel calls | Your listener |
|---|---|---|
| **notify** | `ezpEvent::getInstance()->notify( $name, $params )` | is called with the parameters; its return value is ignored |
| **filter** | `ezpEvent::getInstance()->filter( $name, $value, ... )` | receives the value (and extra arguments), **must return it**, changed or not |

A filter listener that forgets to return the value destroys it: the page, the cache keys, the list of functions. This
is the one rule to remember.

## Attaching a listener

In your extension's `settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[Event]
Listeners[]=content/state/assign@mysiteHelloListeners::stateAssigned
Listeners[]=response/output@mysiteHelloListeners::output

*/ ?>
```

The form is `<event>@<callback>`, the callback a function or `Class::method`. A malformed entry, an unknown callback, or
the same entry twice is skipped or attached once, with a note in the log (`ezpEvent::registerEventListeners()`).
`[Event] Listeners[]` is attached for web requests; the runnables' events below are attached everywhere.

## The listener class

```php
<?php
/**
 * Event listeners of mysite_hello, attached in settings/site.ini.append.php:
 *   [Event]
 *   Listeners[]=content/state/assign@mysiteHelloListeners::stateAssigned
 *   Listeners[]=response/output@mysiteHelloListeners::output
 */
class mysiteHelloListeners
{
    /** notify: called with ( $objectID, $selectedStateIDList ) after object states were assigned */
    public static function stateAssigned( $objectID, $stateIDList )
    {
        eZLog::write( sprintf( 'Object %d got states %s', (int)$objectID, implode( ',', array_map( 'intval', (array)$stateIDList ) ) ),
                      'mysite_hello.log' );
    }

    /** filter: receives the page and MUST return it (changed or not) */
    public static function output( $html )
    {
        if ( !is_string( $html ) )
            return $html;
        return str_replace( '</body>', "<!-- served with mysite_hello -->\n</body>", $html );
    }
}
```

Attached to a fresh `ezpEvent` instance with the kernel loaded, `filter( 'response/output', '<html><body>x</body></html>' )`
returned the page with the comment inserted, and `notify()` of an event nobody listens to returned `false`.

## The kernel's events

Found in the kernel's code with `grep -rn "getInstance()->notify\|getInstance()->filter" kernel lib`:

| Event | Kind | Parameters / value |
|---|---|---|
| `request/preinput`, `request/input` | notify | the request, early |
| `response/preoutput`, `response/output` | filter | the output before it is sent |
| `content/view` | filter | the result of a content view |
| `content/view/cachekeys` | filter | the keys of the view cache (add a key, for example per user group) |
| `content/cache`, `content/cache/version`, `content/cache/all` | notify | caches of nodes and objects were cleared: `( $nodeList, $contentObjectList )` |
| `content/class/cache`, `content/section/cache`, `content/state/cache`, `content/translations/cache`, `user/cache/all` | notify | other caches cleared |
| `content/state/assign` | notify | `( $objectID, $selectedStateIDList )` |
| `content/download/access`, `content/edit/access`, `collaboration/item/access` | filter | a say in access decisions |
| `content/notification/create` | filter | whether a notification is created |
| `module/functionlist` | filter | the policy functions of a module (content limitations of extensions use it) |
| `image/alias` | notify | an image alias was created |
| `session/regenerate`, `session/destroy`, `session/gc`, `session/cleanup` | notify | session life cycle |
| `httpcache/store/headers` | filter | headers stored with an HTTP cache entry |

The access filters and view cache keys are documented in [Access and view cache filters](../../../features/6.0/access-and-cache-filters.md),
content limitations in [Content policy limitations of extensions](../../../features/6.0/content-limitation-handlers.md).

## Events of commands, cronjobs and views

Runnables announce `runnable/<kind>/before` (notify, with `$runnable, $class, $scope`) and `runnable/<kind>/after`
(filter of the result), `<kind>` being `command`, `cronjob` or `view`, and `runcronjobs.php` asks the filter
`cronjob/part/run` before a part runs ([Leaving out a cronjob part](../../../features/6.0/cronjob-part-filter.md)). They are
registered in `site.ini [RunnableSettings] Listeners[]`, which also allows replacing a runnable class with
`Implementation[<class>]=<subclass>`.

## Announcing your own events

Your extension can let others extend it the same way:

```php
<?php
$items = ezpEvent::getInstance()->filter( 'mysite_hello/greetings', $items );
ezpEvent::getInstance()->notify( 'mysite_hello/greeted', array( $name ) );
```

Name your events after your extension, and document their parameters.

## Rules

- Keep listeners fast: `response/output` runs on every page.
- Return the value from every filter, on every path.
- Do not throw from a listener; log and continue.
- Under a persistent server (Velocity), never attach listeners in code that runs per request without checking: they
  would accumulate. Settings-based listeners are attached once.

## Related pages

- Previous: [Database schema, .dba files and upgrades](database-schema-and-dba-files.md)
- Next: [Handlers named by settings](handlers-named-by-settings.md)
- [Workflow event types](workflow-event-types.md)
- [Extension points](../../../bc/6.0/rad-extension-points.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
