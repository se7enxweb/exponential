---
title: Events, listeners and filters
audience: php-developer, developer
category: php-development
order: 300
summary: The kernel's event hub ezpEvent for PHP developers - notify and filter events, registering listeners in site.ini [Event] and [RunnableSettings] or with attach(), the events the kernel sends, writing a filter that returns its value, and the rules for listeners under persistent workers.
keywords: ezpEvent, events, listeners, filters, notify, filter, attach, detach, hasListeners, site.ini Event Listeners, RunnableSettings Listeners, request/input, response/output, content/cache, session/regenerate, httpcache/store/headers, observer
type: help
---
# Events, listeners and filters

This page is for PHP developers whose code must react to something the kernel does, or change a value on its way,
without editing the kernel. The kernel has one event hub, `ezpEvent` (`kernel/private/classes/ezpevent.php`). The
kernel sends events; your listener receives them.

## Contents

1. [Two kinds of events](#two-kinds-of-events)
2. [Registering a listener](#registering-a-listener)
3. [A notify listener](#a-notify-listener)
4. [A filter listener](#a-filter-listener)
5. [Events the kernel sends](#events-the-kernel-sends)
6. [Sending your own events](#sending-your-own-events)
7. [Rules](#rules)

## Two kinds of events

| Kind | Kernel call | Your listener |
|---|---|---|
| notify | `ezpEvent::getInstance()->notify( $name, array( ... ) )` | receives the parameters; its return value is ignored |
| filter | `ezpEvent::getInstance()->filter( $name, $value, ... )` | receives the value (and extra arguments) and **must return** the value, changed or not |

A filter listener that forgets to return destroys the value: the page goes blank, the header list is empty.

## Registering a listener

In settings, for web requests:

```ini
# extension/acmereport/settings/site.ini.append.php
<?php /* #?ini charset="utf-8"?
[Event]
Listeners[]=content/cache@acmeReportListener::contentCacheCleared
Listeners[]=response/output@acmeReportListener::output
*/ ?>
```

The form is `<event>@<callback>`, and the callback is a function name or `Class::method`. A wrong callback is skipped
with a line in the log. `[Event] Listeners[]` are attached to **web requests only**. For commands, cronjob parts and
views, `[RunnableSettings] Listeners[]` attaches listeners of the `runnable/<kind>/before` and `after` events in every
context ([Module views as classes](module-views-as-classes.md#events-around-run)).

In code, at run time:

```php
$id = ezpEvent::getInstance()->attach( 'content/cache', array( 'acmeReportListener', 'contentCacheCleared' ) );
ezpEvent::getInstance()->detach( 'content/cache', $id );
```

`ezformtoken` registers its three listeners this way in its `site.ini.append.php`:
`request/input@ezxFormToken::input`, `response/output@ezxFormToken::output`, `session/regenerate@ezxFormToken::reset`.

## A notify listener

```php
class acmeReportListener
{
    /**
     * content/cache: the view cache of these nodes and objects was cleared
     *
     * @param array $nodeList node ids
     * @param array $objectList content object ids
     */
    public static function contentCacheCleared( $nodeList, $objectList = array() )
    {
        acmeReportCache::forgetNodes( is_array( $nodeList ) ? $nodeList : array() );
    }
}
```

## A filter listener

```php
class acmeReportListener
{
    /**
     * response/output: the page before it is sent
     *
     * @param string $html
     * @return string
     */
    public static function output( $html )
    {
        if ( !is_string( $html ) )
            return $html;
        // change $html only when you must; this runs on every page
        return $html;
    }
}
```

## Events the kernel sends

A selection, found with `grep -rn "ezpEvent::getInstance()->" kernel lib`:

| Event | Kind | When |
|---|---|---|
| `request/preinput`, `request/input` | notify | the request arrives; input checks |
| `response/preoutput`, `response/output` | filter | the rendered page, before and after the pagelayout output |
| `module/functionlist` | filter | a module's list of views and functions |
| `content/view` | filter | the node id of a content view |
| `content/cache`, `content/cache/all`, `content/cache/version` | notify | view caches were cleared |
| `content/class/cache`, `content/section/cache`, `content/state/cache` ... | notify | other caches were cleared |
| `content/edit/access`, `content/download/access` | filter | access decisions you may tighten |
| `session/regenerate`, `session/destroy`, `session/gc`, `session/cleanup` | notify | the session life cycle |
| `httpcache/store/headers` | filter | the headers the HTTP cache keeps with a page |
| `image/alias` | notify | an image alias was made |
| `runnable/<kind>/before`, `runnable/<kind>/after` | notify, filter | around every command, cronjob part and view |

**Setup > RAD**, survey "events" (`/setup/radsurvey/(show)/events`), lists the events and their listeners on your
installation ([Extension points](../../bc/6.0/rad-extension-points.md#filter-event-listener)).

## Sending your own events

An extension can offer events to others the same way:

```php
ezpEvent::getInstance()->notify( 'acmereport/item/stored', array( $item->attribute( 'id' ) ) );
$rows = ezpEvent::getInstance()->filter( 'acmereport/export/rows', $rows, $month );
```

Name them under your extension's prefix, document the parameters, and check `hasListeners( $name )` first when
gathering the parameters is expensive.

## Rules

- **Fast and quiet.** Listeners run inside requests, some on every page. No slow calls, no output.
- **Return the value in a filter**, always, also on the paths that change nothing.
- **Never throw** from a listener; catch, log, and return.
- **Persistent workers.** Under Velocity a worker serves many requests. Register listeners in settings, or attach them
  in code once per request (guard with `eZExecution::requestKey()`), never with a `true` flag that a worker keeps from
  its warm-up, or the listener runs twice or not at all ([Static state traps](static-state-traps.md)).

## Related pages

- [Events and filters (extension developers)](../extension-development/part-3-extension-developer/events-and-filters.md)
- [Event hook pattern](../../standards/patterns/event-hook.md), [Observer pattern](../../standards/patterns/observer.md)
- [Extension points from PHP](extension-points-from-php.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
