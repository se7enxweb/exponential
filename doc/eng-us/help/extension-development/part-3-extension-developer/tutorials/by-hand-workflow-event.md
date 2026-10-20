---
title: "By hand: a workflow event"
audience: developer, app-creator
category: extension-development
order: 160
summary: A workflow event type "Log publishing" by hand: the class, the registration, the view template, a workflow and a trigger, and the log line it writes.
keywords: tutorial, by hand, workflow event, eZWorkflowEventType, registerEventType, workflow.ini, AvailableEventTypes, trigger, content publish after, eZLog
type: help
---
# By hand: a workflow event

Tutorial 15. You add an event type that writes a line to `var/log/mysite_hello.log` for every published version. The
class passed `php -l`; with the kernel loaded, `eZWorkflowType::createType( 'event_mysitepublishlog' )` created it and
`isAllowed( 'content', 'publish', 'after' )` returned true (and false for `before`).

## 1. The class

`extension/mysite_hello/eventtypes/event/mysitepublishlog/mysitepublishlogtype.php`:

```php
<?php
/**
 * Workflow event "Log publishing": writes one line per published version to var/log/mysite_hello.log.
 * Use it on the trigger content / publish / after.
 */
class mysitePublishLogType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'mysitepublishlog';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING,
                             ezpI18n::tr( 'extension/mysite_hello', 'Log publishing' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        $version = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
        {
            // nothing to log; let the operation continue
            return eZWorkflowType::STATUS_ACCEPTED;
        }

        eZLog::write( sprintf( 'Published object %d version %d: %s', $objectID, $version, $object->attribute( 'name' ) ),
                      'mysite_hello.log' );

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( mysitePublishLogType::WORKFLOW_TYPE_STRING, 'mysitePublishLogType' );
```

## 2. The registration

`extension/mysite_hello/settings/workflow.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[EventSettings]
ExtensionDirectories[]=mysite_hello
AvailableEventTypes[]=event_mysitepublishlog

*/ ?>
```

## 3. The template the workflow editor shows

`extension/mysite_hello/design/standard/templates/workflow/eventtype/view/event_mysitepublishlog.tpl`:

```text
<div class="element">
    <p>{'Log publishing'|i18n( 'extension/mysite_hello' )}</p>
</div>
```

An event without settings needs no edit template; add `workflow/eventtype/edit/event_mysitepublishlog.tpl` when it gets
settings ([Workflow event types](../workflow-event-types.md#settings-of-an-event)).

## 4. Switch on and wire up

```bash
php -l extension/mysite_hello/eventtypes/event/mysitepublishlog/mysitepublishlogtype.php
php bin/php/ezcache.php --clear-all
```

1. **Setup > Workflows**: in a workflow group, create "Log publishing", add the event "Log publishing", store.
2. **Setup > Triggers**: for `content` / `publish` / after, choose the workflow, store.
3. Publish an article, then:

   ```bash
   tail -n 3 var/log/mysite_hello.log
   ```

   shows a line `Published object <id> version <n>: <name>`.

## 5. Remove the trigger when done

A trigger runs for every publication on the site. When you finish experimenting, set the trigger back to no workflow.

## Related pages

- Previous: [By hand: a datatype](by-hand-datatype.md)
- Next: [By hand: a cronjob part](by-hand-cronjob-part.md)
- [Workflow event types](../workflow-event-types.md), [With RAD: a workflow event](rad-workflow-event.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
