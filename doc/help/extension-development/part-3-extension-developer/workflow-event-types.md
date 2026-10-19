---
title: Workflow event types
audience: developer, app-creator
category: extension-development
order: 180
summary: Add a step to workflows: an eZWorkflowEventType class, its registration and triggers, execute() and the statuses it can return, process parameters, a verified example.
keywords: workflow event, eZWorkflowEventType, eZWorkflowType, execute, STATUS_ACCEPTED, STATUS_REJECTED, STATUS_DEFERRED_TO_CRON, setTriggerTypes, registerEventType, AvailableEventTypes, parameter_list, eventtypes
type: help
---
# Workflow event types

Part 3, chapter 18. A **workflow event type** is a step builders can put into a workflow (see
[Workflows for builders](../part-2-builder/workflows-for-builders.md)): approve, wait until a date, and whatever your
extension adds. This chapter shows how to write one. The example passed `php -l` and was created and queried with the
kernel loaded; the reference classes are `eZWorkflowType` and `eZWorkflowEventType` (`kernel/classes/`).

## Registration

`extension/mysite_hello/settings/workflow.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[EventSettings]
ExtensionDirectories[]=mysite_hello
AvailableEventTypes[]=event_mysitepublishlog

*/ ?>
```

The type string is `event_<type>`; the kernel (`eZWorkflowType::loadAndRegisterType()`) splits it into the group
`event` and the type `mysitepublishlog` and includes
`extension/mysite_hello/eventtypes/event/mysitepublishlog/mysitepublishlogtype.php`. The `swark` extension registers
its two events (`event_autopriority`, `event_defertocron`) the same way.

## The class

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

With the kernel loaded, `eZWorkflowType::createType( 'event_mysitepublishlog' )` returned the class with the name
"Log publishing", and `isAllowed( 'content', 'publish', 'after' )` was true while `'before'` was false.

| Part | Meaning |
|---|---|
| `parent::__construct( $typeString, $name )` | `eZWorkflowEventType` puts the type in the group `event` |
| `setTriggerTypes()` | where builders may use it: `array( <module> => array( <operation> => array( 'before', 'after' ) ) )`, or `array( '*' => true )` for anywhere |
| `execute( $process, $event )` | the work; returns a status |
| `$process->attribute( 'parameter_list' )` | the operation's parameters; for `content/publish`: `object_id` and `version` |
| `registerEventType()` | links the string to the class |

## What execute() can return

From `eZWorkflowType` (`kernel/classes/ezworkflowtype.php`):

| Status | Effect |
|---|---|
| `STATUS_ACCEPTED` | done; the next event runs, then the operation continues |
| `STATUS_REJECTED` | stop; the operation does not happen |
| `STATUS_DEFERRED_TO_CRON` | wait: the workflow cronjob runs the event again later (the Approve event uses this) |
| `STATUS_DEFERRED_TO_CRON_REPEAT` | the same, repeated until another status is returned |
| `STATUS_WORKFLOW_CANCELLED` | cancel the whole workflow |
| `STATUS_FETCH_TEMPLATE`, `STATUS_FETCH_TEMPLATE_REPEAT` | show a template to the user (an interactive step) |
| `STATUS_REDIRECT`, `STATUS_REDIRECT_REPEAT` | send the user somewhere, then come back |
| `STATUS_RUN_SUB_EVENT` | run another workflow (the multiplexer) |

`swark`'s `defertocron` event is the smallest real example: its `execute()` only returns `STATUS_DEFERRED_TO_CRON`, so
publishing is finished by the cronjob instead of during the editor's request.

## Before or after?

| Trigger | Use it to |
|---|---|
| `content / publish / before` | decide whether publishing may happen (validate, approve, reject) |
| `content / publish / after` | react to content that is published (log, notify, index elsewhere) |

An event that only reacts and never waits or rejects is often simpler as an **event listener**
([Events, listeners and filters](events-and-filters.md)): no workflow to configure.

## Settings of an event

An event can have its own settings per workflow (like the approvers of Approve): implement `fetchHTTPInput()`,
`validateHTTPInput()`, `workflowEventContent()` and `storeEventData()`, and add the templates
`workflow/eventtype/edit/event_<type>.tpl` and `workflow/eventtype/view/event_<type>.tpl` to your design. The kernel's
approve event (`kernel/classes/workflowtypes/event/ezapprove/`) is the complete reference.

## Be careful in execute()

- It runs inside the publishing operation: keep it short, and never echo output.
- Write to the database inside `$db->begin()` / `$db->commit()` when you change several things.
- Cast every ID you put into SQL; `swark`'s `autopriority` reads node IDs from the object and uses them in an `UPDATE`.
- An exception aborts publishing for the editor. Catch what you expect, log it with `eZDebug::writeError()`, and return
  a status.

## RAD

**Workflow event extension** (`/setup/workflowevent`) writes the class, settings and templates, including event
settings and triggers; see [With RAD: a workflow event](tutorials/rad-workflow-event.md).

## Related pages

- Previous: [Datatype storage, validation and templates](datatype-storage-validation-and-templates.md)
- Next: [Triggers and workflows for developers](triggers-and-workflows-for-developers.md)
- [The eZWorkflowType API](../part-8-reference/ezworkflowtype-api.md)
- [Workflows](../../../guides/workflows.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
